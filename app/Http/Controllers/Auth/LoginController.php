<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            return redirect()->route('sample_entries.index');
        }

        if ($user->hasRole('Area Manager')) {
            // Redirige a /dashboard
            return redirect('/dashboard');
        }

        if ($user->hasRole('Supervisor')) {
            // Por ahora no hacemos nada, lo dejamos en /home o donde quieras
            return redirect('/dashboard');
        }

        // Por seguridad, redirección por defecto si no tiene rol definido
        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Solo pueden iniciar sesión los usuarios activos
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['active' => true]);
    }

    /**
     * Si la contraseña es correcta pero la cuenta está desactivada, se avisa en vez de decir "credenciales incorrectas"
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where($this->username(), $request->input($this->username()))->first();

        if ($user && ! $user->active && Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                $this->username() => ['Tu cuenta está desactivada. Contacta al Supervisor o al Jefe de Área.'],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
