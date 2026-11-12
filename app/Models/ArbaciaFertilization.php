<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BioassayRecord;
use App\Models\Concerns\HasTestDuration;
use Illuminate\Database\Eloquent\Model;

class ArbaciaFertilization extends Model
{
    use HasFactory, HasTestDuration, BioassayRecord;

    // Etapa => [etiqueta, campo de inicio, campo de término, minutos que debe durar según el protocolo]
    public const DURATION_PHASES = [
        'bioassay' => ['Duración del ensayo (inicio a término de fijación)', 'bioassay_start', 'fixation_time_end', 60],
    ];

    protected $table = 'arbacia_fertilization';

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
        'validated_at' => 'datetime',
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
        return $this->belongsTo(SampleEntry::class);
    }
}