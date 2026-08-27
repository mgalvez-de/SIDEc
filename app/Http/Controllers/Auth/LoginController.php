<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirección por defecto (no se usará porque usamos authenticated)
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirección después de login según rol
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('Analist')) {
            // Redirige a la ruta de resource de sample_entries
            return redirect()->route('sample_entries.index');
        }

        if ($user->hasRole('Manager')) {
            // Redirige a la ruta de resource de receptions
            return redirect()->route('receptions.index');
        }

        if ($user->hasRole('Area Manager')) {
            // Redirige a /dashboard
            return redirect('/dashboard');
        }

        if ($user->hasRole('Supervisor')) {
            // Por ahora no hacemos nada, lo dejamos en /home o donde quieras
            return redirect(RouteServiceProvider::HOME);
        }

        // Por seguridad, redirección por defecto si no tiene rol definido
        return redirect(RouteServiceProvider::HOME);
    }
}
