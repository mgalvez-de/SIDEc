<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TisbeLongicornisWater extends Model
{
    use HasFactory;

    protected $table = 'tisbe_longicornis_water';

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
        $limitMs = 48 * 60 * 60 * 1000;  // 48 horas
        $totalMs = 58 * 60 * 60 * 1000;  // 58 horas (48 + 10 gracia)

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
     * Calcular total de muertes a las 24H.
     */
    public function getTotalDeaths24hAttribute()
    {
        if (!$this->samples_data) return 0;
        
        $total = 0;
        foreach ($this->samples_data as $row) {
            $total += (int) ($row['sum_24h'] ?? 0);
        }
        return $total;
    }

    /**
     * Calcular total de muertes a las 48H.
     */
    public function getTotalDeaths48hAttribute()
    {
        if (!$this->samples_data) return 0;
        
        $total = 0;
        foreach ($this->samples_data as $row) {
            $total += (int) ($row['sum_48h'] ?? 0);
        }
        return $total;
    }
}