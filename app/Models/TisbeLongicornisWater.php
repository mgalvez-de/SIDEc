<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BioassayRecord;
use App\Models\Concerns\HasTestDuration;
use Illuminate\Database\Eloquent\Model;

class TisbeLongicornisWater extends Model
{
    use HasFactory, HasTestDuration, BioassayRecord;

    // Etapa => [etiqueta, campo de inicio, campo de término, minutos que debe durar según el protocolo]
    public const DURATION_PHASES = [
        'bioassay' => ['Duración del ensayo', 'bioassay_start', 'bioassay_end', 48 * 60],
    ];

    protected $table = 'tisbe_longicornis_water';

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

        // Datos de muestras (JSON con 24 filas)
        'samples_data',

        // Resultados
        'cl50_24h',
        'cl50_48h',
        'observations',
        'vb',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'validated_at' => 'datetime',
        'bioassay_start'     => 'datetime',
        'bioassay_end'       => 'datetime',
        'stock_culture_date' => 'date',
        'initial_inoculum'   => 'decimal:2',
        'samples_data'       => 'array',
    ];

    /**
     * Relación con Template base.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Relación con SampleEntry a través del código de muestra.
     */
    public function sampleEntry()
    {
        return $this->belongsTo(SampleEntry::class);
    }
}