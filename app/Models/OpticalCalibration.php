<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * µm por píxel de una configuración óptica, medido con un patrón (micrómetro de platina).
 */
class OpticalCalibration extends Model
{
    protected $fillable = [
        'optical_configuration_id', 'um_per_px', 'calibrated_at', 'valid_until',
        'reference_equipment_id', 'performed_by', 'notes',
    ];

    protected $casts = [
        'um_per_px'     => 'float',
        'calibrated_at' => 'date',
        'valid_until'   => 'date',
    ];

    public function opticalConfiguration()
    {
        return $this->belongsTo(OpticalConfiguration::class);
    }

    public function referenceEquipment()
    {
        return $this->belongsTo(Equipment::class, 'reference_equipment_id')->withTrashed();
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
