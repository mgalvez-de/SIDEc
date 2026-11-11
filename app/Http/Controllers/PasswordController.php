<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * "Cambiar mi contraseña": cada persona cambia la suya, sin depender del Supervisor.
 */
class PasswordController extends Controller
{
    public function edit()
    {
        return view('auth.password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Escribe tu contraseña actual.',
            'password.required'         => 'Escribe la contraseña nueva.',
            'password.min'              => 'La contraseña nueva debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'Las dos contraseñas nuevas no coinciden.',
            'password.different'        => 'La contraseña nueva debe ser distinta de la actual.',
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Tu contraseña se cambió correctamente.');
    }
}
