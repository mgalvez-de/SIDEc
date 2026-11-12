<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // En la base real los roles los crea RolesAndUsersSeeder; aquí hay que crearlos
    protected function setUp(): void
    {
        parent::setUp();

        foreach (array_keys(User::ROLE_LABELS) as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function newUserPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ana Pérez',
            'email' => 'ana@crea.cl',
            'role' => 'Analist',
            'password' => 'clave-segura',
            'password_confirmation' => 'clave-segura',
        ], $overrides);
    }

    public function test_area_manager_can_create_a_user_with_a_role()
    {
        $this->actingAs($this->userWithRole('Area Manager'))
            ->post(route('users.store'), $this->newUserPayload())
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'ana@crea.cl')->firstOrFail();
        $this->assertTrue($user->hasRole('Analist'));
        $this->assertTrue($user->active);
        $this->assertTrue(Hash::check('clave-segura', $user->password));
    }

    public function test_only_supervisor_and_area_manager_can_manage_users()
    {
        $this->actingAs($this->userWithRole('Supervisor'))->get(route('users.index'))->assertOk();

        foreach (['Analist', 'Manager'] as $role) {
            $this->actingAs($this->userWithRole($role))->get(route('users.index'))->assertForbidden();
        }
    }

    public function test_user_screens_open_with_spanish_role_names()
    {
        $admin = $this->userWithRole('Supervisor');
        $manager = $this->userWithRole('Manager');

        $this->actingAs($admin)->get(route('users.index'))->assertOk()->assertSee('Encargado de área');
        $this->actingAs($admin)->get(route('users.create'))->assertOk()->assertSee('Jefe de Área');
        $this->actingAs($admin)->get(route('users.edit', $manager))->assertOk()->assertSee($manager->email);
    }

    public function test_email_cannot_be_repeated()
    {
        $admin = $this->userWithRole('Area Manager');

        $this->actingAs($admin)->post(route('users.store'), $this->newUserPayload());
        $this->actingAs($admin)
            ->post(route('users.store'), $this->newUserPayload(['name' => 'Otra Ana']))
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'ana@crea.cl')->count());
    }

    public function test_editing_without_password_keeps_the_current_one()
    {
        $user = $this->userWithRole('Analist');

        $this->actingAs($this->userWithRole('Area Manager'))
            ->put(route('users.update', $user), [
                'name' => 'Nombre corregido',
                'email' => $user->email,
                'role' => 'Manager',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertSame('Nombre corregido', $user->name);
        $this->assertTrue($user->hasRole('Manager'));
        $this->assertFalse($user->hasRole('Analist'));
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_admin_cannot_remove_their_own_admin_role()
    {
        $admin = $this->userWithRole('Area Manager');

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'Analist',
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->refresh()->hasRole('Area Manager'));
    }

    public function test_deactivated_user_cannot_log_in()
    {
        $user = $this->userWithRole('Analist');

        $this->actingAs($this->userWithRole('Area Manager'))
            ->patch(route('users.toggle-active', $user));
        $this->assertFalse($user->refresh()->active);

        auth()->logout();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivated_user_is_logged_out_on_next_request()
    {
        $user = $this->userWithRole('Analist');
        $user->update(['active' => false]);

        $this->actingAs($user)
            ->get(route('sample_entries.index'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_cannot_deactivate_their_own_account()
    {
        $admin = $this->userWithRole('Supervisor');

        $this->actingAs($admin)->patch(route('users.toggle-active', $admin));

        $this->assertTrue($admin->refresh()->active);
    }
}
