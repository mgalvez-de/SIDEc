<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

/**
 * Crea la primera cuenta real del sistema (Jefe de Área) desde la terminal del servidor.
 * Las demás cuentas se crean después desde la pantalla Usuarios.
 *
 *   php artisan sidec:crear-admin
 */
class CreateAdmin extends Command
{
    protected $signature = 'sidec:crear-admin';

    protected $description = 'Crea una cuenta de Jefe de Área (pide nombre, correo y contraseña)';

    public function handle()
    {
        $name = $this->ask('Nombre completo');
        $email = $this->ask('Correo');
        $password = $this->secret('Contraseña (mínimo 8 caracteres; no se muestra al escribir)');
        $confirmation = $this->secret('Repite la contraseña');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation],
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        Role::firstOrCreate(['name' => 'Area Manager']);

        $user = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        $user->assignRole('Area Manager');

        $this->info("Cuenta creada: {$email} (Jefe de Área). Ya puedes iniciar sesión.");

        return 0;
    }
}
