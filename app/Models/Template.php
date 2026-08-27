<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'version',
        'validity',
        'type',
    ];

    public function reception()
    {
        return $this->hasOne(ReceptionTemplate::class);
    }

    public function daphniaMagnaTemplates()
    {
        return $this->hasMany(DaphniaMagnaTemplate::class);
    }

}
