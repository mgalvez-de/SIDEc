<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TisbeLongicornisRiles extends Model
{
    use HasFactory;

    protected $table = 'tisbe_longicornis_riles';

    protected $fillable = [
        'template_id',

        // Temporizadores
        'preliminary_timer_start',
        'definitive_timer_start',

        // Datos generales
        'sample',
        'matrix',
        'analyst',

        // Ensayo Preliminar
        'preliminary_start_at',
        'preliminary_end_at',
        'preliminary_sample_temperature',
        'preliminary_control_water_date',
        'preliminary_table',

        // Ensayo Definitivo
        'definitive_start_at',
        'definitive_end_at',
        'definitive_sample_temperature',
        'definitive_control_water_date',
        'definitive_24h',
        'definitive_48h',

        // Resultados
        'control_immobility',
        'cl50_24h',
        'cl50_48h',
        'observations',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'preliminary_start_at'           => 'datetime',
        'preliminary_end_at'             => 'datetime',
        'preliminary_control_water_date' => 'date',
        'preliminary_sample_temperature' => 'decimal:2',
        'preliminary_table'              => 'array',

        'definitive_start_at'           => 'datetime',
        'definitive_end_at'             => 'datetime',
        'definitive_control_water_date' => 'date',
        'definitive_sample_temperature' => 'decimal:2',
        'definitive_24h'                => 'array',
        'definitive_48h'                => 'array',
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
     * Obtener el estado del temporizador preliminar.
     */
    public function getPreliminaryTimerStatusAttribute()
    {
        if (!$this->preliminary_timer_start) {
            return 'not_started';
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->preliminary_timer_start;
        $limitMs = 48 * 60 * 60 * 1000;
        $totalMs = 58 * 60 * 60 * 1000;

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
     * Obtener el estado del temporizador definitivo.
     */
    public function getDefinitiveTimerStatusAttribute()
    {
        if (!$this->definitive_timer_start) {
            return 'not_started';
        }

        $elapsed = now()->timestamp * 1000 - (int) $this->definitive_timer_start;
        $limitMs = 48 * 60 * 60 * 1000;
        $totalMs = 58 * 60 * 60 * 1000;

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