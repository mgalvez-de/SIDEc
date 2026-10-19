<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasTestDuration;
use Illuminate\Database\Eloquent\Model;

class SelenastrumCapricornutum extends Model
{
    use HasFactory, HasTestDuration;

    // Etapa => [etiqueta, campo de inicio, campo de término, minutos que debe durar según el protocolo]
    public const DURATION_PHASES = [
        'bioassay' => ['Duración del ensayo', 'bioassay_start', 'bioassay_end', 96 * 60],
    ];

    protected $table = 'selenastrum_capricornutum';

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
        'initial_inoculum',
        'stock_culture_date',

        // Crecimiento y pH (Control)
        'rc24h',
        'rc48h',
        'rc72h',
        'rc196h',
        'rc296h',
        'rc396h',
        'rc496h',
        'ph_initial',
        'ph_final',
        'control_growth_rate',

        // Mediciones (JSON)
        'measurements',

        // Resultados
        'ce50_detail',
        'variation_coefficient',
        'observations',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'validated_at' => 'datetime',
        'bioassay_start'        => 'datetime',
        'bioassay_end'          => 'datetime',
        'stock_culture_date'    => 'date',
        'initial_inoculum'      => 'decimal:2',
        'rc24h'                 => 'decimal:2',
        'rc48h'                 => 'decimal:2',
        'rc72h'                 => 'decimal:2',
        'rc196h'                => 'decimal:2',
        'rc296h'                => 'decimal:2',
        'rc396h'                => 'decimal:2',
        'rc496h'                => 'decimal:2',
        'ph_initial'            => 'decimal:2',
        'ph_final'              => 'decimal:2',
        'control_growth_rate'   => 'decimal:4',
        'variation_coefficient' => 'decimal:2',
        'measurements'          => 'array',
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