<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $roles = [
            'Analist',
            'Supervisor',
            'Manager',
            'Area Manager',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // En producción solo se crean los roles: las cuentas de prueba tienen una contraseña conocida.
        // La primera cuenta real se crea con:  php artisan sidec:crear-admin
        if (app()->environment('production')) {
            return;
        }

        // Cuentas de prueba (solo desarrollo y demostraciones)
        $users = [
            [
                'name' => 'Usuario Analista',
                'email' => 'analist@a',
                'password' => Hash::make('12345678'),
                'role' => 'Analist',
            ],
            [
                'name' => 'Usuario Supervisor',
                'email' => 'supervisor@a',
                'password' => Hash::make('12345678'),
                'role' => 'Supervisor',
            ],
            [
                'name' => 'Usuario Encargado de área',
                'email' => 'manager@a',
                'password' => Hash::make('12345678'),
                'role' => 'Manager',
            ],
            [
                'name' => 'Usuario Jefe de Área',
                'email' => 'areamanager@a',
                'password' => Hash::make('12345678'),
                'role' => 'Area Manager',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            $user->assignRole($userData['role']);
        }
    }
}
