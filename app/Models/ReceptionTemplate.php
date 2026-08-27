<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionTemplate extends Model
{
    use HasFactory;

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

}
