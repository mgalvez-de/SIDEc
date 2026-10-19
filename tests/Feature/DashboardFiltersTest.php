<?php

namespace Tests\Feature;

use App\Models\DaphniaMagnaTemplate;
use App\Models\ReceptionTemplate;
use App\Models\RejectionTemplate;
use App\Models\SampleEntry;
use App\Models\SelenastrumCapricornutum;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Dashboard: filtros por período y cliente, permisos por rol y plazo de entrega
class DashboardFiltersTest extends TestCase
{
    use RefreshDatabase;

    private $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->template = Template::create(['title' => 'Plantilla', 'type' => 'reception']);
    }

    private function reception(string $code, string $client, $receivedAt, array $bioassays = []): ReceptionTemplate
    {
        return ReceptionTemplate::create([
            'template_id' => $this->template->id,
            'internal_sample_code' => $code,
            'client' => $client,
            'received_at' => $receivedAt,
            'assigned_bioassays' => $bioassays,
        ]);
    }

    private function entry(string $code): SampleEntry
    {
        return SampleEntry::create(['template_id' => $this->template->id, 'internal_sample_code' => $code]);
    }

    public function test_only_manager_supervisor_and_area_manager_can_open_the_dashboard()
    {
        $this->actingAs($this->userWithRole('Analist'))->get(route('dashboard'))->assertForbidden();

        foreach (['Manager', 'Supervisor', 'Area Manager'] as $role) {
            $this->actingAs($this->userWithRole($role))->get(route('dashboard'))->assertOk();
        }
    }

    // Seguimiento era una página aparte; ahora es parte del Dashboard y el enlace antiguo sigue funcionando
    public function test_old_tracking_address_goes_to_the_dashboard()
    {
        $this->actingAs($this->userWithRole('Manager'))->get('/seguimiento')->assertRedirect('/dashboard');
    }

    public function test_manager_sees_the_progress_but_not_the_clients()
    {
        $this->reception('M-1', 'Pesquera Costa Sur S.A.', now()->subDays(3), ['Daphnia magna Agudo']);
        RejectionTemplate::create([
            'template_id' => $this->template->id, 'internal_sample_code' => 'M-2', 'client' => 'Minera Los Robles SpA',
        ]);

        $this->actingAs($this->userWithRole('Manager'))
            ->get(route('dashboard', ['client' => 'Minera Los Robles SpA']))
            ->assertOk()
            ->assertSee('M-1')
            ->assertDontSee('Pesquera Costa Sur')
            ->assertDontSee('Minera Los Robles')
            ->assertDontSee('M-2');

        $this->actingAs($this->userWithRole('Supervisor'))
            ->get(route('dashboard'))
            ->assertSee('Pesquera Costa Sur')
            ->assertSee('M-2');
    }

    public function test_client_filter_includes_rejected_samples()
    {
        $this->reception('M-1', 'Acuícola Mar Austral SpA', now()->subDays(3));
        $this->reception('M-2', 'Otro cliente', now()->subDays(3));
        RejectionTemplate::create([
            'template_id' => $this->template->id, 'internal_sample_code' => 'M-3', 'client' => 'Acuícola Mar Austral SpA',
        ]);

        $this->actingAs($this->userWithRole('Area Manager'))
            ->get(route('dashboard', ['client' => 'Acuícola Mar Austral SpA']))
            ->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->pluck('code')->sort()->values()->all() === ['M-1', 'M-3'])
            ->assertViewHas('totals', fn ($totals) => $totals['rejected'] === 1);
    }

    public function test_period_filter_counts_validated_and_pending_bioassays_by_type()
    {
        $this->reception('M-1', 'Cliente', '2026-09-10 10:00', ['Daphnia magna Agudo', 'Selenastrum capricornutum']);
        $entry = $this->entry('M-1');
        DaphniaMagnaTemplate::create(['sample' => 'M-1', 'sample_entry_id' => $entry->id, 'validated_at' => now()]);
        SelenastrumCapricornutum::create(['sample' => 'M-1', 'sample_entry_id' => $entry->id]);

        // Recibida pero aún sin ingresar: su bioensayo también está pendiente
        $this->reception('M-2', 'Cliente', '2026-09-20 10:00', ['Daphnia magna Agudo']);

        // Fuera del período
        $this->reception('M-3', 'Cliente', '2026-08-01 10:00', ['Daphnia magna Agudo']);

        $this->actingAs($this->userWithRole('Manager'))
            ->get(route('dashboard', ['from' => '2026-09-01', 'to' => '2026-09-30']))
            ->assertOk()
            ->assertViewHas('totals', fn ($totals) => $totals['received'] === 2
                && $totals['validated'] === 1 && $totals['pending'] === 2)
            ->assertViewHas('byType', fn ($byType) => $byType === [
                'Daphnia magna Agudo' => ['validated' => 1, 'pending' => 1],
                'Selenastrum capricornutum' => ['validated' => 0, 'pending' => 1],
            ]);
    }

    public function test_end_date_before_start_date_shows_an_error()
    {
        $this->actingAs($this->userWithRole('Supervisor'))
            ->from(route('dashboard'))
            ->get(route('dashboard', ['from' => '2026-09-30', 'to' => '2026-09-01']))
            ->assertSessionHasErrors(['to' => 'La fecha "hasta" debe ser igual o posterior a la fecha "desde".']);
    }

    public function test_six_week_deadline_turns_yellow_from_week_five_and_red_after_week_six()
    {
        $this->assertSame('ok', $this->reception('M-1', 'C', now()->subDays(20))->deadlineStatus());
        $this->assertSame('warning', $this->reception('M-2', 'C', now()->subDays(29))->deadlineStatus());
        $this->assertSame('overdue', $this->reception('M-3', 'C', now()->subDays(43))->deadlineStatus());

        // Completada (todos sus bioensayos validados): sin alerta aunque hayan pasado las 6 semanas
        $completed = $this->reception('M-4', 'C', now()->subDays(60), ['Daphnia magna Agudo']);
        DaphniaMagnaTemplate::create([
            'sample' => 'M-4', 'sample_entry_id' => $this->entry('M-4')->id, 'validated_at' => now(),
        ]);
        $this->assertSame('completed', $completed->deadlineStatus());
    }

    public function test_menu_shows_the_dashboard_to_manager_but_not_to_analyst()
    {
        $this->actingAs($this->userWithRole('Manager'))->get(route('sample_entries.index'))
            ->assertSee(route('dashboard'));

        $this->actingAs($this->userWithRole('Analist'))->get(route('sample_entries.index'))
            ->assertDontSee(route('dashboard'));
    }

    public function test_rejection_saves_the_client()
    {
        $this->actingAs($this->userWithRole('Supervisor'))
            ->post(route('rejections.store'), [
                'title' => 'RECHAZO DE MUESTRAS',
                'version' => 1,
                'internal_sample_code' => 'M-9',
                'client' => 'Salmonera Fiordo Sur SpA',
            ])
            ->assertRedirect(route('rejections.index'));

        $this->assertDatabaseHas('rejection_templates', ['internal_sample_code' => 'M-9', 'client' => 'Salmonera Fiordo Sur SpA']);
    }
}
