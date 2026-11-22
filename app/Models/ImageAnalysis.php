<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;

/**
 * Fotografía de una réplica de Arbacia analizada con API_larvas.
 *
 * Se inserta una sola vez con la respuesta completa (o el error). Después solo pueden cambiar
 * MUTABLE_COLUMNS y nunca se borra (NCh-ISO/IEC 17025, 7.5 y 7.11). El modelo lo impone: lanza una
 * excepción si se intenta cambiar otra columna o borrar el registro.
 */
class ImageAnalysis extends Model
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUPERSEDED = 'superseded';

    // Fotos que cuentan para la réplica
    public const CURRENT_STATUSES = [self::STATUS_COMPLETED];

    public const STATUS_LABELS = [
        self::STATUS_COMPLETED  => ['Analizada', 'success'],
        self::STATUS_FAILED     => ['Falló', 'danger'],
        self::STATUS_SUPERSEDED => ['Reemplazada / anulada', 'secondary'],
    ];

    // Únicas columnas que pueden cambiar después del insert
    public const MUTABLE_COLUMNS = ['status', 'superseded_by_id', 'void_reason', 'voided_by', 'voided_at', 'updated_at'];

    /**
     * Planillas que admiten análisis de imágenes. Clave = prefijo de ruta (igual que Navigation::BIOASSAYS).
     * Solo se ofrecen las que están en config('larvas_api.enabled_sections'): ver sectionEnabled().
     * rows: filas de la planilla que se pueden fotografiar; replicas: réplicas por fila.
     */
    public const SECTIONS = [
        'arbacia-larval-stage' => [
            'model'    => ArbaciaLarvalStage::class,
            'rows'     => ['control', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'],
            'replicas' => 4,
            'control'  => 'control',
        ],
        'arbacia-fertilization' => [
            'model'    => ArbaciaFertilization::class,
            'rows'     => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'],
            'replicas' => 3,
            'control'  => '1',   // En Fecundación la fila 1 es el control
        ],
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'summary'        => 'array',
        'instances'      => 'array',
        'requested_um'   => 'boolean',
        'captured_at'    => 'datetime',
        'voided_at'      => 'datetime',
        'conf'           => 'float',
        'iou'            => 'float',
        'um_per_px'      => 'float',
    ];

    protected static function booted()
    {
        static::creating(function (ImageAnalysis $analysis) {
            $analysis->uuid = $analysis->uuid ?: (string) Str::uuid();
        });

        static::updating(function (ImageAnalysis $analysis) {
            $forbidden = array_diff(array_keys($analysis->getDirty()), self::MUTABLE_COLUMNS);
            if ($forbidden) {
                throw new LogicException('Un análisis de imagen no se puede modificar: ' . implode(', ', $forbidden));
            }
        });

        static::deleting(function () {
            throw new LogicException('Los análisis de imagen no se borran: se reemplazan o se anulan.');
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ===== Relaciones =====

    public function analyzable()
    {
        return $this->morphTo();
    }

    public function sampleEntry()
    {
        return $this->belongsTo(SampleEntry::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Quien anuló o reemplazó la foto
    public function voider()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function microscope()
    {
        return $this->belongsTo(Equipment::class, 'microscope_id')->withTrashed();
    }

    public function camera()
    {
        return $this->belongsTo(Equipment::class, 'camera_id')->withTrashed();
    }

    public function opticalConfiguration()
    {
        return $this->belongsTo(OpticalConfiguration::class);
    }

    public function opticalCalibration()
    {
        return $this->belongsTo(OpticalCalibration::class);
    }

    public function supersededBy()
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }

    public function supersedes()
    {
        return $this->hasOne(self::class, 'superseded_by_id');
    }

    // ===== Consultas =====

    public function scopeForBioassay($query, Model $bioassay)
    {
        return $query->where('analyzable_type', get_class($bioassay))->where('analyzable_id', $bioassay->getKey());
    }

    public function scopeCurrent($query)
    {
        return $query->whereIn('status', self::CURRENT_STATUSES);
    }

    public static function nextPhotoNumber(Model $bioassay, string $rowKey, int $replica): int
    {
        return (int) self::forBioassay($bioassay)->where('row_key', $rowKey)->where('replica', $replica)->max('photo_number') + 1;
    }

    // ===== Bloqueo de eliminación =====
    // Un bioensayo o un ingreso de muestra con análisis no se elimina. Cuentan todos los registros
    // (vigentes, fallidos, reemplazados y anulados), porque ninguno se borra nunca.

    public const DELETE_BLOCKED_MESSAGE = 'No se puede eliminar: tiene fotografías analizadas con IA, que se conservan siempre (NCh-ISO/IEC 17025).';

    public static function existsForBioassay(Model $bioassay): bool
    {
        return self::forBioassay($bioassay)->exists();
    }

    public static function existsForSampleEntry(SampleEntry $sampleEntry): bool
    {
        return self::where(function ($query) use ($sampleEntry) {
            $query->where('sample_entry_id', $sampleEntry->getKey());

            // También los análisis de sus bioensayos de Arbacia, aunque no tengan sample_entry_id
            foreach (self::SECTIONS as $config) {
                $ids = $config['model']::where('sample_entry_id', $sampleEntry->getKey())->pluck('id');
                if ($ids->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('analyzable_type', $config['model'])->whereIn('analyzable_id', $ids));
                }
            }
        })->exists();
    }

    // ===== Estado =====

    public function isCurrent(): bool
    {
        return in_array($this->status, self::CURRENT_STATUSES, true);
    }

    // Se puede reemplazar o anular una foto vigente o una que falló (la fallida sale de la lista)
    public function canBeReplaced(): bool
    {
        return $this->status !== self::STATUS_SUPERSEDED;
    }

    public function statusLabel(): array
    {
        if ($this->status === self::STATUS_SUPERSEDED) {
            return [$this->superseded_by_id ? 'Reemplazada' : 'Anulada', 'secondary'];
        }

        return self::STATUS_LABELS[$this->status] ?? [$this->status, 'secondary'];
    }

    // "Anulada por X el dd/mm/aaaa hh:mm" / "Reemplazada por X el …" (sin el motivo)
    public function voidedText(): ?string
    {
        if ($this->status !== self::STATUS_SUPERSEDED) {
            return null;
        }

        return $this->statusLabel()[0]
            . ($this->voider ? ' por ' . $this->voider->name : '')
            . ($this->voided_at ? ' el ' . $this->voided_at->format('d/m/Y H:i') : '');
    }

    // Planilla habilitada para el análisis de imágenes (Fecundación queda fuera mientras no haya modelo)
    public static function sectionEnabled(string $section): bool
    {
        return isset(self::SECTIONS[$section]) && in_array($section, (array) config('larvas_api.enabled_sections', []), true);
    }

    public function section(): ?string
    {
        foreach (self::SECTIONS as $section => $config) {
            if ($config['model'] === $this->analyzable_type) {
                return $section;
            }
        }

        return null;
    }

    // "Control" / "Fila 3" / "Fila 1 (control)", con la concentración si la planilla la tiene
    public static function rowLabel(string $section, string $rowKey, ?Model $bioassay = null): string
    {
        $config = self::SECTIONS[$section];
        $label = $rowKey === 'control' ? 'Control' : "Fila {$rowKey}";

        if ($rowKey !== 'control' && $config['control'] === $rowKey) {
            $label .= ' (control)';
        }

        $concentration = $bioassay && $rowKey !== 'control' ? ($bioassay->rows_data[$rowKey]['concentration'] ?? null) : null;

        return $concentration ? "{$label} · {$concentration}" : $label;
    }

    /** Clases con conteo > 0, de mayor a menor (para listados). */
    public function detectedClasses(string $source = 'counts_by_class'): array
    {
        $counts = array_filter($this->summary[$source] ?? [], fn ($n) => $n > 0);
        arsort($counts);

        return $counts;
    }
}
