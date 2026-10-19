<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasTestDuration;
use Illuminate\Database\Eloquent\Model;

class ArbaciaLarvalStage extends Model
{
    use HasFactory, HasTestDuration;

    // Etapa => [etiqueta, campo de inicio, campo de término, minutos que debe durar según el protocolo]
    public const DURATION_PHASES = [
        'bioassay' => ['Duración del ensayo (inicio a término de fijación)', 'bioassay_start', 'fixation_time_end', 48 * 60],
    ];

    protected $table = 'arbacia_larval_stages';

    protected $fillable = [
        'template_id',
        'sample_entry_id',
        'created_by',
        'validated_by',
        'validated_at',

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
        'validated_at' => 'datetime',
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
        return $this->belongsTo(SampleEntry::class);
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