<?php

namespace Tests\Feature;

use App\Models\DaphniaMagnaTemplate;
use App\Models\ReceptionTemplate;
use App\Models\RejectionTemplate;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_counts_come_from_real_data()
    {
        $template = Template::create(['title' => 'Plantilla', 'type' => 'reception']);

        $reception = fn (string $code, array $bioassays = []) => ReceptionTemplate::create([
            'template_id' => $template->id,
            'internal_sample_code' => $code,
            'assigned_bioassays' => $bioassays,
        ]);
        $entry = fn (string $code) => SampleEntry::create([
            'template_id' => $template->id,
            'internal_sample_code' => $code,
        ]);

        // Recepcionada y nunca ingresada -> Sin Ingresar
        $reception('M-1');

        // Recepcionada y rechazada -> Rechazada (no cuenta como Sin Ingresar)
        $reception('M-2');
        RejectionTemplate::create(['template_id' => $template->id, 'internal_sample_code' => 'M-2']);

        // Ingresada con su bioensayo sin validar -> En Proceso
        $reception('M-3', ['Daphnia magna Agudo']);
        DaphniaMagnaTemplate::create(['sample' => 'M-3', 'sample_entry_id' => $entry('M-3')->id]);

        // Ingresada con su bioensayo validado -> Completada
        $reception('M-4', ['Daphnia magna Agudo']);
        DaphniaMagnaTemplate::create([
            'sample' => 'M-4',
            'sample_entry_id' => $entry('M-4')->id,
            'validated_at' => now(),
        ]);

        $this->actingAs($this->userWithRole('Area Manager'))
            ->get('/dashboard')
            ->assertOk()
            ->assertViewHas('totalRecepcionadas', 4)
            ->assertViewHas('totalRechazadas', 1)
            ->assertViewHas('totalSinIngresar', 1)
            ->assertViewHas('totalEnProceso', 1)
            ->assertViewHas('totalCompletadas', 1)
            ->assertViewHas('bioassayTotals', fn ($totals) => $totals['Daphnia magna Agudo'] === 2);
    }
}
