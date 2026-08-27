<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArbaciaLarvalStage extends Model
{
    use HasFactory;

    protected $table = 'arbacia_larval_stages';

    protected $fillable = [
        'template_id',

        // Temporizador
        'timer_start',

        // Datos generales
        'sample',
        'matrix',
        'bioassay_start',
        'analyst',

        // Tiempos del ensayo
        'fertilization_time',
        'fertilized_eggs_added_at',
        'fixation_time_end',
        'count_end_datetime',

        // Control (JSON)
        // Estructura: {
        //   "r1_larva": 90, "r1_total": 100, "r1_percent": 90.00,
        //   "r2_larva": 88, "r2_total": 100, "r2_percent": 88.00,
        //   "r3_larva": 92, "r3_total": 100, "r3_percent": 92.00,
        //   "r4_larva": 91, "r4_total": 100, "r4_percent": 91.00,
        //   "larval_pluteus_avg": 90.25,
        //   "total_larva_percent": 90.25
        // }
        'control_data',

        // Filas de datos (JSON)
        // Estructura: {
        //   "1": {
        //     "concentration": "6.25%",
        //     "r1_larva": 85, "r1_total": 100, "r1_percent": 85.00,
        //     "r2_larva": 82, "r2_total": 100, "r2_percent": 82.00,
        //     "r3_larva": 88, "r3_total": 100, "r3_percent": 88.00,
        //     "r4_larva": 84, "r4_total": 100, "r4_percent": 84.00,
        //     "larval_pluteus_percent": 84.75,
        //     "inhibition_percent": 6.09,
        //     "ce": ""
        //   },
        //   "2": { ... },
        //   ...
        //   "15": { ... }
        // }
        'rows_data',

        // Resultados
        'ce50',
        'observations',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'bioassay_start'      => 'datetime',
        'count_end_datetime'  => 'datetime',
        'control_data'        => 'array',
        'rows_data'           => 'array',
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
     * Este bioensayo tiene un tiempo límite de 48 horas + 4 horas de gracia.
     */
    public function getTimerStatusAttribute()
    {
        if (!$this->timer_start) {
            return 'not_started';
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->timer_start;
        $limitMs = 48 * 60 * 60 * 1000;  // 48 horas
        $totalMs = 52 * 60 * 60 * 1000;  // 52 horas (48 + 4 gracia)

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
     * Obtener el porcentaje promedio larval del control.
     */
    public function getControlLarvalAvgAttribute()
    {
        $control = $this->control_data ?? [];
        return $control['larval_pluteus_avg'] ?? null;
    }
}