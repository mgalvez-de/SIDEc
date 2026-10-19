<?php

namespace Tests\Feature;

use App\Support\Navigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_sends_each_role_to_its_start_page()
    {
        $this->actingAs($this->userWithRole('Supervisor'))->get(route('home'))->assertRedirect(route('dashboard'));
        $this->actingAs($this->userWithRole('Analist'))->get(route('home'))->assertRedirect(route('sample_entries.index'));
    }

    public function test_breadcrumb_and_tab_title_follow_the_current_page()
    {
        $this->assertSame([
            ['label' => 'Bioensayos', 'url' => null],
            ['label' => 'Daphnia magna (crónico)', 'url' => route('daphnia-magna-chronic.index')],
            ['label' => 'Nuevo', 'url' => null],
        ], Navigation::breadcrumb('daphnia-magna-chronic.create'));

        $this->assertSame('Recepciones · SIDEc', Navigation::pageTitle('receptions.index'));
        $this->assertSame('SIDEc', Navigation::pageTitle('login'));

        $this->actingAs($this->userWithRole('Analist'))
            ->get(route('daphnia-magna-chronic.create'))
            ->assertOk()
            ->assertSee('<title>Daphnia magna (crónico) › Nuevo · SIDEc</title>', false)
            ->assertSee('aria-label="Ubicación"', false);
    }

    public function test_top_bar_shows_the_user_role_in_spanish()
    {
        $this->actingAs($this->userWithRole('Manager'))
            ->get(route('sample_entries.index'))
            ->assertSee('Encargado de área')
            ->assertSee('Cerrar sesión');
    }

    // Antes, varias páginas (como este listado) no mostraban el mensaje de "guardado correctamente"
    public function test_server_messages_are_shown_on_every_page()
    {
        $this->actingAs($this->userWithRole('Analist'))
            ->withSession(['success' => 'Bioensayo guardado.', 'warning' => 'No se encontró la muestra asociada.'])
            ->get(route('daphnia-magna.index'))
            ->assertSee('Bioensayo guardado.')
            ->assertSee('No se encontró la muestra asociada.');
    }
}
