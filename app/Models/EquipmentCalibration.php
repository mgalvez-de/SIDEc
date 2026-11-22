<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Certificado de calibración de un equipo patrón (ej. micrómetro de platina).
 */
class EquipmentCalibration extends Model
{
    protected $fillable = [
        'equipment_id', 'calibrated_at', 'valid_until', 'certificate_number', 'provider',
        'certificate_path', 'notes', 'created_by',
    ];

    protected $casts = [
        'calibrated_at' => 'date',
        'valid_until'   => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class)->withTrashed();
    }
}
