<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasTestDuration;
use Illuminate\Database\Eloquent\Model;

class DaphniaMagnaChronic extends Model
{
    use HasFactory, HasTestDuration;

    // Etapa => [etiqueta, campo de inicio, campo de término, minutos que debe durar según el protocolo]
    public const DURATION_PHASES = [
        'bioassay' => ['Duración del ensayo', 'bioassay_start', 'bioassay_end', 21 * 24 * 60],
    ];

    protected $table = 'daphnia_magna_chronic';

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
        'validated_at' => 'datetime',
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
        return $this->belongsTo(SampleEntry::class);
    }

    /**
     * Verificar si el control cumple el criterio (≥ 40 juveniles).
     */
    public function getControlMeetsCriteriaAttribute()
    {
        return ($this->control_total_reproduction ?? 0) >= 40;
    }
}