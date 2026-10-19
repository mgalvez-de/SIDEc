<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // Nombre del rol en el sistema => nombre del cargo en el laboratorio (se muestra en la barra superior)
    public const ROLE_LABELS = [
        'Analist'      => 'Analista',
        'Manager'      => 'Encargado de área',
        'Supervisor'   => 'Supervisor',
        'Area Manager' => 'Jefe de Área',
    ];

    // Roles que ven datos de clientes (Supervisor y Jefe de Área)
    public const ADMIN_ROLES = ['Supervisor', 'Area Manager'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->getRoleNames()->first()] ?? 'Sin rol';
    }
}
