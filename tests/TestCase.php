<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // RefreshDatabase borra y recrea todas las tablas: si phpunit.xml dejara de apuntar a
    // SQLite en memoria, los tests vaciarían la base MySQL real del laboratorio.
    protected function setUpTraits()
    {
        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException(
                'Los tests deben correr en SQLite en memoria (ver phpunit.xml), nunca en la base real.'
            );
        }

        return parent::setUpTraits();
    }

    protected function userWithRole(string $role): \App\Models\User
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);

        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
