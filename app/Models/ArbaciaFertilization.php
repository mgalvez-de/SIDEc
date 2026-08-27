<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArbaciaFertilization extends Model
{
    use HasFactory;

    protected $table = 'arbacia_fertilization';

    protected $fillable = [
        'template_id',

        // Temporizador
        'timer_start',

        // Datos generales
        'sample',
        'matrix',
        'bioassay_start',
        'analyst',
        'control_fertilization_percentage',

        // Tiempos del ensayo
        'sperm_addition_time',
        'egg_addition_time',
        'fixation_time_end',
        'count_end_datetime',

        // Filas de datos (JSON)
        // Estructura: {
        //   "1": { 
        //     "concentration": "Control",
        //     "r1_nf": 10, "r1_total": 100, "r1_fert": 90.00,
        //     "r2_nf": 12, "r2_total": 100, "r2_fert": 88.00,
        //     "r3_nf": 8, "r3_total": 100, "r3_fert": 92.00,
        //     "avg_fertilization": 90.00,
        //     "inhibition": 0.00,
        //     "ci": ""
        //   },
        //   "2": { ... },
        //   ...
        //   "15": { ... }
        // }
        'rows_data',

        // Resultados
        'ci50',
        'observations',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'bioassay_start'                   => 'datetime',
        'count_end_datetime'               => 'datetime',
        'control_fertilization_percentage' => 'decimal:2',
        'rows_data'                        => 'array',
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
     * Este bioensayo tiene un tiempo límite de 60 minutos + 10 minutos de gracia.
     */
    public function getTimerStatusAttribute()
    {
        if (!$this->timer_start) {
            return 'not_started';
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->timer_start;
        $limitMs = 60 * 60 * 1000;  // 60 minutos (1 hora)
        $totalMs = 70 * 60 * 1000;  // 70 minutos (60 + 10 gracia)

        if ($elapsed >= $totalMs) {
            return 'expired';
        } elseif ($elapsed >= $limitMs) {
            return 'grace';
        } elseif ($elapsed >= $limitMs * 0.8) {
            return 'warning';
        }

        return 'running';
    }

    /**
     * Obtener el porcentaje promedio de fecundación del control (fila 1).
     */
    public function getControlFertilizationAttribute()
    {
        $rows = $this->rows_data ?? [];
        return $rows[1]['avg_fertilization'] ?? $this->control_fertilization_percentage ?? null;
    }
}