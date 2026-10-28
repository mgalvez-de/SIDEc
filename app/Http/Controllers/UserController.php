<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create', ['roles' => User::ROLE_LABELS]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'role'     => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
            'password' => 'required|string|min:8|confirmed',
        ], $this->messages());

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user, 'roles' => User::ROLE_LABELS]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
            'password' => 'nullable|string|min:8|confirmed',
        ], $this->messages());

        // Si alguien se quitara a sí mismo el rol de administración, podría quedar el sistema sin quien gestione usuarios
        if ($user->is($request->user()) && ! in_array($validated['role'], User::ADMIN_ROLES)) {
            return back()->withErrors(['role' => 'No puedes quitarte tu propio rol de administración.'])->withInput();
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleActive(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['active' => ! $user->active]);

        return back()->with('success', $user->active ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    private function messages(): array
    {
        return [
            'required'           => 'Este campo es obligatorio.',
            'email.email'        => 'Ingresa un correo válido.',
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'role.in'            => 'Selecciona un rol válido.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
