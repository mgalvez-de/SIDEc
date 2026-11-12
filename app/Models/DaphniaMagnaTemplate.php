<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BioassayRecord;
use App\Models\Concerns\HasTestDuration;
use Illuminate\Database\Eloquent\Model;

class DaphniaMagnaTemplate extends Model
{
    use HasFactory, HasTestDuration, BioassayRecord;

    // Etapa => [etiqueta, campo de inicio, campo de término, minutos que debe durar según el protocolo]
    public const DURATION_PHASES = [
        'preliminary' => ['Duración del ensayo preliminar', 'preliminary_start_at', 'preliminary_end_at', 48 * 60],
        'definitive' => ['Duración del ensayo definitivo', 'definitive_start_at', 'definitive_end_at', 48 * 60],
    ];

    protected $fillable = [
        'template_id',
         'sample_entry_id',
         'created_by',
        'validated_by',
        'validated_at',

        // Datos generales
        'sample',
        'matrix',
        'analyst',

        // Ensayo Preliminar
        'preliminary_start_at',
        'preliminary_end_at',
        'preliminary_sample_temperature',
        'preliminary_reconstituted_water_date',
        'preliminary_sample_ph',
        'preliminary_table',

        // Ensayo Definitivo
        'definitive_start_at',
        'definitive_end_at',
        'definitive_sample_temperature',
        'definitive_reconstituted_water_date',
        'definitive_24h',
        'definitive_48h',

        // Resultados de análisis
        'control_immobility',
        'cl50_24h',
        'cl50_48h',
        'observations',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'validated_at' => 'datetime',
        'preliminary_start_at' => 'datetime',
        'preliminary_end_at' => 'datetime',
        'preliminary_reconstituted_water_date' => 'date',
        'preliminary_sample_temperature' => 'decimal:2',
        'preliminary_sample_ph' => 'decimal:2',
        'preliminary_table' => 'array',

        'definitive_start_at' => 'datetime',
        'definitive_end_at' => 'datetime',
        'definitive_reconstituted_water_date' => 'date',
        'definitive_sample_temperature' => 'decimal:2',
        'definitive_24h' => 'array',
        'definitive_48h' => 'array',
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