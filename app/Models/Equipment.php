<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Equipo del laboratorio (microscopio, cámara, micrómetro de platina...). NCh-ISO/IEC 17025, 6.4.
 */
class Equipment extends Model
{
    use SoftDeletes;

    // "equipment" es incontable: se declara la tabla para no depender del pluralizador
    protected $table = 'equipment';

    public const TYPES = [
        'microscope'       => 'Microscopio',
        'camera'           => 'Cámara',
        'stage_micrometer' => 'Micrómetro de platina',
        'other'            => 'Otro',
    ];

    public const STATUSES = [
        'active'         => 'Operativo',
        'out_of_service' => 'Fuera de servicio',
    ];

    protected $fillable = [
        'code', 'name', 'type', 'brand', 'model', 'serial_number', 'location', 'status', 'notes', 'created_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function calibrations()
    {
        return $this->hasMany(EquipmentCalibration::class);
    }

    // "EQ-MIC-01 · Microscopio Olympus CX23"
    public function label(): string
    {
        return trim("{$this->code} · {$this->name}");
    }
}
