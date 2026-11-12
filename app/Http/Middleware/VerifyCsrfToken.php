<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Si la página llevaba mucho rato abierta, el token vence y "Cerrar sesión" mostraba "419 Page Expired"
        // sin cerrar la sesión. Sin token, lo peor que puede pasar es que alguien te cierre la sesión.
        'logout',
    ];
}
