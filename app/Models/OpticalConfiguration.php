<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Microscopio + objetivo + cámara. Es la unidad que se calibra en µm/px (NCh-ISO/IEC 17025, 6.5).
 */
class OpticalConfiguration extends Model
{
    protected $fillable = [
        'code', 'microscope_id', 'camera_id', 'objective_magnification', 'adapter', 'status', 'notes',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function microscope()
    {
        return $this->belongsTo(Equipment::class, 'microscope_id')->withTrashed();
    }

    public function camera()
    {
        return $this->belongsTo(Equipment::class, 'camera_id')->withTrashed();
    }

    public function calibrations()
    {
        return $this->hasMany(OpticalCalibration::class);
    }

    // Calibración vigente en una fecha (la más reciente que la cubre), o null
    public function calibrationValidAt($date): ?OpticalCalibration
    {
        $day = \Illuminate\Support\Carbon::parse($date)->toDateString();

        return $this->calibrations()
            ->whereDate('calibrated_at', '<=', $day)
            ->whereDate('valid_until', '>=', $day)
            ->orderByDesc('calibrated_at')
            ->first();
    }

    public function label(): string
    {
        return "{$this->code} · {$this->objective_magnification}";
    }
}
