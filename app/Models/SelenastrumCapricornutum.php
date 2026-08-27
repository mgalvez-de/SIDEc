<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelenastrumCapricornutum extends Model
{
    use HasFactory;

    protected $table = 'selenastrum_capricornutum';

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
        return $this->hasOne(SampleEntry::class, 'internal_sample_code', 'sample');
    }

    /**
     * Obtener el estado del temporizador.
     */
    public function getTimerStatusAttribute()
    {
        if (!$this->timer_start) {
            return 'not_started';
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->timer_start;
        $limitMs = 96 * 60 * 60 * 1000;  // 96 horas
        $totalMs = 106 * 60 * 60 * 1000; // 106 horas (96 + 10 gracia)

        if ($elapsed >= $totalMs) {
            return 'expired';
        } elseif ($elapsed >= $limitMs) {
            return 'grace';
        } elseif ($elapsed >= $limitMs * 0.9) {
            return 'warning';
        }

        return 'running';
    }
}