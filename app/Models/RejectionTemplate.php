<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RejectionTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'template_id',
        'internal_sample_code',
        'sample_identifier',
        'client',
        'reason_for_rejection',
        'who_rejects',
        'who_informs_the_client',
        'customer_instructions',
        'observations',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
