<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'internal_sample_code',
        'sample_identifier',
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
