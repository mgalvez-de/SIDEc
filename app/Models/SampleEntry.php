<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'received_at',
        'internal_sample_code',
        'sample_type',
        'sample_concentration',
        'parameter_reading_date',
        'analyst',
        'ph',
        'salinity',
        'conductivity',
        'dissolved_oxygen',
        'temperature',
        'observations',
        'state',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function reception()
    {
        return $this->belongsTo(ReceptionTemplate::class, 'internal_sample_code', 'internal_sample_code');
    }

    public function daphniaMagna()
    {
        return $this->hasOne(DaphniaMagnaTemplate::class, 'sample', 'internal_sample_code');
    }

    public function isochrysisGalbana()
    {
        return $this->hasOne(IsochrysisGalbana::class, 'sample', 'internal_sample_code');
    }

    public function selenastrumCapricornutum()
    {
        return $this->hasOne(SelenastrumCapricornutum::class, 'sample', 'internal_sample_code');
    }

    public function tisbeLongicornisWater()
    {
        return $this->hasOne(TisbelongicornisWater::class, 'sample', 'internal_sample_code');
    }

}
