<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessByRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_area_manager_lands_on_dashboard_after_login()
    {
        $user = $this->userWithRole('Area Manager');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');
    }

    public function test_analist_lands_on_sample_entries_after_login()
    {
        $user = $this->userWithRole('Analist');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('sample_entries.index'));
    }

    public function test_analist_cannot_open_dashboard()
    {
        $this->actingAs($this->userWithRole('Analist'))
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_supervisor_cannot_open_bioassays()
    {
        $this->actingAs($this->userWithRole('Supervisor'))
            ->get('/daphnia-magna')
            ->assertForbidden();
    }

    // El menú solo debe mostrar las secciones que las rutas le permiten abrir a cada rol
    public function test_menu_only_links_to_sections_the_role_can_open()
    {
        $this->actingAs($this->userWithRole('Supervisor'))
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(url('/receptions'))
            ->assertSee(url('/rejections'));

        $this->actingAs($this->userWithRole('Manager'))
            ->get(route('sample_entries.index'))
            ->assertOk()
            ->assertDontSee(url('/dashboard'))
            ->assertDontSee(url('/receptions'))
            ->assertDontSee(url('/rejections'));
    }
}
