<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaphniaMagnaChronic extends Model
{
    use HasFactory;

    protected $table = 'daphnia_magna_chronic';

    protected $fillable = [
        'template_id',

        // Temporizador
        'timer_start',

        // Datos generales
        'sample',
        'matrix',
        'bioassay_start',
        'bioassay_end',
        'analyst',

        // Datos preliminares
        'sample_temperature',
        'ph',

        // Mantención de especie (JSON)
        // Estructura: {
        //   "0": { "water_date": "2025-01-01", "food_date": "2025-01-01", "microalgae_ml": 1.5 },
        //   "3": { ... },
        //   "6": { ... },
        //   ...
        //   "18": { ... }
        // }
        'maintenance_data',

        // Control (JSON) - 21 días x 10 réplicas
        // Estructura: {
        //   "1": { "r1": 5, "r2": 3, "r3": 4, ..., "r10": 6 },
        //   "2": { "r1": 0, "r2": 2, ... },
        //   ...
        //   "21": { ... },
        //   "sum": { "r1": 45, "r2": 38, ... }
        // }
        'control_data',
        'control_total_reproduction',

        // Concentraciones (JSON) - hasta 5 concentraciones
        // Estructura: {
        //   "1": {
        //     "value": "100%",
        //     "days": {
        //       "1": { "r1": 2, "r2": 1, ... },
        //       "2": { ... },
        //       ...
        //       "21": { ... }
        //     },
        //     "sum": { "r1": 30, "r2": 25, ... },
        //     "total_reproduction": 280
        //   },
        //   "2": { ... },
        //   ...
        //   "5": { ... }
        // }
        'concentrations_data',

        // Resultados
        'noec',
        'loec',
        'observations',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'bioassay_start'            => 'datetime',
        'bioassay_end'              => 'datetime',
        'sample_temperature'        => 'decimal:1',
        'ph'                        => 'decimal:2',
        'control_total_reproduction' => 'integer',
        'maintenance_data'          => 'array',
        'control_data'              => 'array',
        'concentrations_data'       => 'array',
    ];

    /**
     * Relación con Template base.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Relación con SampleEntry.
     */
    public function sampleEntry()
    {
        return $this->hasOne(SampleEntry::class, 'internal_sample_code', 'sample');
    }

    /**
     * Obtener el estado del temporizador.
     * Este bioensayo tiene un tiempo límite de 21 días + 1 día de gracia.
     */
    public function getTimerStatusAttribute()
    {
        if (!$this->timer_start) {
            return 'not_started';
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->timer_start;
        $limitMs = 21 * 24 * 60 * 60 * 1000;  // 21 días
        $totalMs = 22 * 24 * 60 * 60 * 1000;  // 22 días (21 + 1 gracia)

        if ($elapsed >= $totalMs) {
            return 'expired';
        } elseif ($elapsed >= $limitMs) {
            return 'grace';
        } elseif ($elapsed >= $limitMs * 0.9) {
            return 'warning';
        }

        return 'running';
    }

    /**
     * Obtener el día actual del ensayo.
     */
    public function getCurrentDayAttribute()
    {
        if (!$this->timer_start) {
            return 0;
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->timer_start;
        return min(floor($elapsed / (24 * 60 * 60 * 1000)), 22);
    }

    /**
     * Verificar si el control cumple el criterio (≥ 40 juveniles).
     */
    public function getControlMeetsCriteriaAttribute()
    {
        return ($this->control_total_reproduction ?? 0) >= 40;
    }
}