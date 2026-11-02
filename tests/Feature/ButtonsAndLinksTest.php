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

    // Antes las tres tablas (Nuevas, En proceso, Historial) mostraban los mismos bioensayos
    public function test_bioassay_list_separates_new_in_process_and_validated()
    {
        $entryId = \App\Models\SampleEntry::first()->id;
        $nueva = \App\Models\DaphniaMagnaTemplate::create(['sample' => 'N-1', 'sample_entry_id' => $entryId]);
        $enProceso = \App\Models\DaphniaMagnaTemplate::create(['sample' => 'P-1', 'sample_entry_id' => $entryId, 'preliminary_start_at' => now()]);
        $validada = \App\Models\DaphniaMagnaTemplate::create(['sample' => 'V-1', 'sample_entry_id' => $entryId, 'preliminary_start_at' => now()->subDays(5), 'validated_at' => now()]);

        $this->actingAs(User::where('email', 'analist@a')->first())
            ->get(route('daphnia-magna.index'))
            ->assertOk()
            ->assertViewHas('newBioassays', fn ($c) => $c->contains($nueva) && !$c->contains($enProceso) && !$c->contains($validada))
            ->assertViewHas('inProcessBioassays', fn ($c) => $c->contains($enProceso) && !$c->contains($nueva) && !$c->contains($validada))
            ->assertViewHas('validatedBioassays', fn ($c) => $c->contains($validada) && !$c->contains($nueva))
            // "Comenzar análisis" abre ESE bioensayo (antes abría uno nuevo en blanco)
            ->assertSee(route('daphnia-magna.edit', $nueva));
    }

    // Rechazar desde Recepciones: el formulario llega con los datos de la muestra
    public function test_reception_can_be_rejected_from_the_list()
    {
        $admin = User::where('email', 'areamanager@a')->first();
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', 'M-2026-013')->first(); // recibida, sin ingresar

        $this->actingAs($admin)->get(route('receptions.index'))
            ->assertSee(route('rejections.create', ['code' => 'M-2026-013']), false);

        $this->actingAs($admin)->get(route('rejections.create', ['code' => 'M-2026-013']))
            ->assertOk()
            ->assertSee('value="M-2026-013"', false)
            ->assertSee('value="' . e($reception->client) . '"', false);

        // Ya rechazada: en vez del botón aparece "Rechazada"
        $rejection = \App\Models\RejectionTemplate::create(['template_id' => $reception->template_id, 'internal_sample_code' => 'M-2026-013']);
        $this->actingAs($admin)->get(route('receptions.index'))
            ->assertDontSee(route('rejections.create', ['code' => 'M-2026-013']), false)
            ->assertSee(route('rejections.show', $rejection), false);
    }

    // El listado de rechazos mostraba solo los últimos 10
    public function test_rejection_list_shows_all_rejections()
    {
        $template = \App\Models\Template::create(['title' => 'RECHAZO', 'type' => 'rejection']);
        foreach (range(1, 12) as $i) {
            \App\Models\RejectionTemplate::create(['template_id' => $template->id, 'internal_sample_code' => "R-$i"]);
        }

        $this->actingAs(User::where('email', 'areamanager@a')->first())
            ->get(route('rejections.index'))
            ->assertViewHas('rejections', fn ($rejections) => $rejections->count() === 15); // 12 + 3 de demostración
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
