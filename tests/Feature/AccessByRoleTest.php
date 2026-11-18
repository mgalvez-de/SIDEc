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

    // Acordado con el supervisor del laboratorio: el Supervisor tiene acceso a todo el sistema
    public function test_supervisor_can_open_sample_entries_and_bioassays()
    {
        $supervisor = $this->userWithRole('Supervisor');

        $this->actingAs($supervisor)->get(route('sample_entries.index'))->assertOk();
        $this->actingAs($supervisor)->get('/bioensayos')->assertOk();
    }

    public function test_manager_cannot_open_receptions()
    {
        $this->actingAs($this->userWithRole('Manager'))
            ->get('/receptions')
            ->assertForbidden();
    }

    // El menú solo debe mostrar las secciones que las rutas le permiten abrir a cada rol
    public function test_menu_only_links_to_sections_the_role_can_open()
    {
        $this->actingAs($this->userWithRole('Supervisor'))
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(url('/receptions'))
            ->assertSee(url('/rejections'))
            ->assertSee(url('/sample_entries'))
            ->assertSee(route('users.index'));

        $this->actingAs($this->userWithRole('Manager'))
            ->get(route('sample_entries.index'))
            ->assertOk()
            ->assertSee(url('/dashboard'))   // el Encargado de área ve el Dashboard (sin clientes)
            ->assertDontSee(url('/receptions'))
            ->assertDontSee(url('/rejections'))
            ->assertDontSee(route('users.index'));
    }

    // El menú marca la sección actual: dentro de cualquier bioensayo queda marcado "Bioensayos"
    public function test_menu_marks_the_current_section()
    {
        $analist = $this->userWithRole('Analist');

        $bioassay = \App\Models\DaphniaMagnaChronic::create(['sample' => 'MENU-1']);
        $html = $this->actingAs($analist)->get(route('daphnia-magna-chronic.edit', $bioassay))->assertOk()->getContent();
        $this->assertTrue($this->linkIsActive($html, route('bioassays.index')));
        $this->assertFalse($this->linkIsActive($html, route('sample_entries.index')));

        $html = $this->actingAs($analist)->get(route('sample_entries.index'))->assertOk()->getContent();
        $this->assertTrue($this->linkIsActive($html, route('sample_entries.index')));
        $this->assertFalse($this->linkIsActive($html, route('bioassays.index')));
    }

    // Clases del enlace del menú que apunta a $url
    private function linkIsActive(string $html, string $url): bool
    {
        preg_match('/<a href="' . preg_quote($url, '/') . '"\s+class="([^"]*)"/', $html, $match);

        return in_array('active', explode(' ', $match[1] ?? ''));
    }
}
