<?php

namespace Tests\Feature;

use App\Models\ArbaciaFertilization;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Botones y enlaces que estaban rotos y se arreglaron
class ButtonsAndLinksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
    }

    // Antes daba error 500: no existe vista de detalle, el formulario hace de detalle
    public function test_bioassay_detail_link_opens_its_form()
    {
        $bioassay = ArbaciaFertilization::first();

        $this->actingAs(User::where('email', 'analist@a')->first())
            ->get(route('arbacia-fertilization.show', $bioassay))
            ->assertRedirect(route('arbacia-fertilization.edit', $bioassay));
    }

    // Antes solo Daphnia agudo tenía "Ir al ensayo"; los demás decían "No disponible"
    public function test_sample_entry_form_links_to_every_assigned_bioassay()
    {
        $entry = \App\Models\SampleEntry::where('internal_sample_code', 'M-2026-012')->first(); // Arbacia fecundación y larval
        $bioassays = $entry->assignedBioassays();

        $this->actingAs(User::where('email', 'analist@a')->first())
            ->get(route('sample_entries.edit', $entry))
            ->assertOk()
            ->assertDontSee('No disponible')
            ->assertSee(route('arbacia-fertilization.edit', $bioassays['Arbacia spatuligera Fecundación']))
            ->assertSee(route('arbacia-larval-stage.edit', $bioassays['Arbacia spatuligera Estado Larval']));
    }

    // Filtrar por empresa en la tabla: lista con los clientes (escribiendo había que acertar con las tildes)
    public function test_dashboard_table_has_a_client_list()
    {
        $this->actingAs(User::where('email', 'areamanager@a')->first())
            ->get(route('dashboard'))
            ->assertSee('column-select', false)
            ->assertSee('<option value="Acuícola Mar Austral SpA">', false);
    }

    // El calendario, las confirmaciones y la traducción de las tablas ya no dependen de internet
    public function test_libraries_are_served_from_the_project()
    {
        foreach (['vendor/sweetalert2/sweetalert2.all.min.js', 'vendor/flatpickr/flatpickr.min.js',
                     'vendor/flatpickr/l10n/es.js', 'vendor/datatables/es-ES.json'] as $file) {
            $this->assertFileExists(public_path($file));
        }

        $this->actingAs(User::where('email', 'analist@a')->first())
            ->get(route('daphnia-magna.edit', \App\Models\DaphniaMagnaTemplate::first()))
            ->assertSee(asset('vendor/flatpickr/flatpickr.min.js'))
            ->assertSee(asset('vendor/sweetalert2/sweetalert2.all.min.js'))
            ->assertDontSee('npmcdn.com');

        // Íconos, tablas, exportar a Excel y gráficos también van dentro del proyecto
        $this->actingAs(User::where('email', 'areamanager@a')->first())
            ->get(route('dashboard'))
            ->assertSee(asset('vendor/bootstrap-icons/bootstrap-icons.min.css'))
            ->assertSee(asset('vendor/datatables/jquery.dataTables.min.js'))
            ->assertSee(asset('vendor/chartjs/chart.umd.min.js'))
            ->assertDontSee('cdn.datatables.net')
            ->assertDontSee('cdn.jsdelivr.net')
            ->assertDontSee('code.jquery.com');
    }
}
