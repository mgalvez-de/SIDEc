<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// Puesta en marcha en el servidor: sin cuentas de prueba y con una cuenta real creada por comando
class ProductionSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_only_roles_in_production()
    {
        $this->app['env'] = 'production';

        // Se llama directo: en producción "db:seed" pide confirmación por teclado
        $this->app->make(RolesAndUsersSeeder::class)->run();

        $this->assertSame(4, Role::count());
        $this->assertSame(0, User::count());
    }

    public function test_seeder_creates_test_accounts_outside_production()
    {
        $this->seed(RolesAndUsersSeeder::class);

        $this->assertSame(4, User::count());
    }

    public function test_command_creates_the_first_area_manager()
    {
        $this->artisan('sidec:crear-admin')
            ->expectsQuestion('Nombre completo', 'Jefa de Prueba')
            ->expectsQuestion('Correo', 'jefa@crea.test')
            ->expectsQuestion('Contraseña (mínimo 8 caracteres; no se muestra al escribir)', 'clave-segura-1')
            ->expectsQuestion('Repite la contraseña', 'clave-segura-1')
            ->assertExitCode(0);

        $user = User::where('email', 'jefa@crea.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Area Manager'));
        $this->assertTrue($user->active);
    }

    public function test_command_rejects_a_short_password()
    {
        $this->artisan('sidec:crear-admin')
            ->expectsQuestion('Nombre completo', 'Jefa de Prueba')
            ->expectsQuestion('Correo', 'jefa@crea.test')
            ->expectsQuestion('Contraseña (mínimo 8 caracteres; no se muestra al escribir)', 'corta')
            ->expectsQuestion('Repite la contraseña', 'corta')
            ->assertExitCode(1);

        $this->assertSame(0, User::count());
    }
}
