<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionTemplate extends Model
{
    use HasFactory;

    // Plazo de entrega al cliente: 6 semanas desde la recepción. Desde la semana 5 se avisa en amarillo.
    public const DELIVERY_WEEKS = 6;
    public const WARNING_WEEKS = 4;

    protected $fillable = [
        'template_id',
        'thermometer_code',
        'correction_factor',
        'received_at',
        'delivered_by',
        'client',
        'sampled_at',
        'received_by',
        'sample_identifier',
        'matrix',
        'internal_sample_code',
        'temperature_received',
        'temperature_corrected',
        'report_number',
        'assigned_bioassays',
    ];

    protected $casts = [
        'assigned_bioassays' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function sampleEntry()
    {
        return $this->hasOne(SampleEntry::class, 'internal_sample_code', 'internal_sample_code');
    }

    public function deliveryDeadline(): ?Carbon
    {
        return $this->received_at ? Carbon::parse($this->received_at)->addWeeks(self::DELIVERY_WEEKS) : null;
    }

    /**
     * 'completed' (todos sus bioensayos validados), 'overdue' (pasó las 6 semanas), 'warning' (semana 5 o 6),
     * 'ok' (en plazo) o 'unknown' (sin fecha de recepción).
     */
    public function deadlineStatus(): string
    {
        if ($this->sampleEntry && $this->sampleEntry->isCompleted()) {
            return 'completed';
        }
        if (!$this->received_at) {
            return 'unknown';
        }

        $received = Carbon::parse($this->received_at);
        if (now()->gte($received->copy()->addWeeks(self::DELIVERY_WEEKS))) {
            return 'overdue';
        }

        return now()->gte($received->copy()->addWeeks(self::WARNING_WEEKS)) ? 'warning' : 'ok';
    }

}
