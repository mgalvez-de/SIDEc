<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Cierra la sesión de un usuario que fue desactivado mientras estaba conectado.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al Supervisor o al Jefe de Área.']);
        }

        return $next($request);
    }
}
