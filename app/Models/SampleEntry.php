<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class SampleEntry extends Model
{
    use HasFactory, SoftDeletes;

    // Nombre del bioensayo tal como se guarda en ReceptionTemplate::assigned_bioassays => modelo
    public const BIOASSAY_MODELS = [
        'Daphnia magna Agudo'               => DaphniaMagnaTemplate::class,
        'Daphnia magna Crónico'             => DaphniaMagnaChronic::class,
        'Isochrysis galbana'                => IsochrysisGalbana::class,
        'Tisbe biconicornis Agua'           => TisbeLongicornisWater::class,
        'Tisbe biconicornis Sedimento'      => TisbeLongicornisRiles::class,
        'Selenastrum capricornutum'         => SelenastrumCapricornutum::class,
        'Arbacia spatuligera Fecundación'   => ArbaciaFertilization::class,
        'Arbacia spatuligera Estado Larval' => ArbaciaLarvalStage::class,
    ];

    private $assignedBioassays;

    protected $fillable = [
        'template_id',
        'received_at',
        'internal_sample_code',
        'sample_type',
        'sample_concentration',
        'parameter_reading_date',
        'analyst',
        'ph',
        'salinity',
        'conductivity',
        'dissolved_oxygen',
        'temperature',
        'observations',
    ];

    // Papelera: al eliminar o restaurar una muestra, sus bioensayos van con ella
    protected static function booted()
    {
        static::deleted(function (SampleEntry $entry) {
            foreach (self::BIOASSAY_MODELS as $model) {
                $model::where('sample_entry_id', $entry->id)->get()->each->delete();
            }
        });

        static::restoring(function (SampleEntry $entry) {
            foreach (self::BIOASSAY_MODELS as $model) {
                // Solo los que se fueron a la papelera junto con la muestra (no los eliminados antes, uno a uno)
                $model::onlyTrashed()->where('sample_entry_id', $entry->id)
                    ->where('deleted_at', '>=', $entry->deleted_at)->get()->each->restore();
            }
        });
    }

    public function hasValidatedBioassays(): bool
    {
        return $this->validatedBioassaysCount() > 0;
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function reception()
    {
        return $this->belongsTo(ReceptionTemplate::class, 'internal_sample_code', 'internal_sample_code');
    }

    /**
     * Bioensayos asignados en la recepción => su registro (null si no existe).
     */
    public function assignedBioassays(): Collection
    {
        return $this->assignedBioassays ??= collect($this->reception->assigned_bioassays ?? [])
            ->filter(fn ($name) => isset(self::BIOASSAY_MODELS[$name]))
            ->mapWithKeys(fn ($name) => [
                $name => self::BIOASSAY_MODELS[$name]::where('sample_entry_id', $this->id)->first(),
            ]);
    }

    public function validatedBioassaysCount(): int
    {
        return $this->assignedBioassays()->filter(fn ($bioassay) => $bioassay && $bioassay->validated_at)->count();
    }

    // Una muestra está completada cuando todos sus bioensayos asignados fueron validados por un Manager
    public function isCompleted(): bool
    {
        $total = $this->assignedBioassays()->count();

        return $total > 0 && $this->validatedBioassaysCount() === $total;
    }

}
