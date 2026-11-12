<?php

namespace Tests\Feature;

use App\Http\Controllers\BioassayController;
use App\Models\ArbaciaFertilization;
use App\Models\DaphniaMagnaTemplate;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Página única "Bioensayos": antes había 8 listados, uno por organismo y cada uno distinto
class BioassayListTest extends TestCase
{
    use RefreshDatabase;

    private User $analist;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
        $this->analist = User::where('email', 'analist@a')->firstOrFail();
    }

    public function test_shows_the_bioassays_of_every_organism()
    {
        $total = collect(BioassayController::MODELS)->sum(fn ($model) => $model::count());

        $this->actingAs($this->analist)
            ->get(route('bioassays.index'))
            ->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->count() === $total
                && $rows->pluck('section')->unique()->count() === count(BioassayController::MODELS));
    }

    public function test_filters_by_organism_and_by_status()
    {
        $this->actingAs($this->analist)
            ->get(route('bioassays.index', ['tipo' => 'daphnia-magna', 'estado' => 'en-proceso']))
            ->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->isNotEmpty()
                && $rows->every(fn ($row) => $row->section === 'daphnia-magna' && $row->status === 'en-proceso'))
            // los botones de estado muestran cuántos hay de ese organismo, sin importar el estado elegido
            ->assertViewHas('counts', fn ($counts) => $counts->sum() === DaphniaMagnaTemplate::count());
    }

    // Arbacia y Daphnia crónico antes mostraban todo junto bajo "Historial"
    public function test_bioassays_that_had_a_single_list_are_now_separated_by_status()
    {
        $entryId = \App\Models\SampleEntry::first()->id;
        ArbaciaFertilization::create(['sample' => 'ARB-NUEVA', 'sample_entry_id' => $entryId]);
        ArbaciaFertilization::create(['sample' => 'ARB-VALIDADA', 'sample_entry_id' => $entryId,
            'bioassay_start' => now()->subHour(), 'validated_at' => now()]);

        $this->actingAs($this->analist)
            ->get(route('bioassays.index', ['tipo' => 'arbacia-fertilization', 'estado' => 'nuevas']))
            ->assertSee('ARB-NUEVA')
            ->assertDontSee('ARB-VALIDADA');
    }

    public function test_unknown_filters_are_ignored()
    {
        $this->actingAs($this->analist)
            ->get(route('bioassays.index', ['tipo' => 'no-existe', 'estado' => 'cualquiera']))
            ->assertOk()
            ->assertViewHas('type', null)
            ->assertViewHas('status', null);
    }

    // Los enlaces antiguos (/daphnia-magna...) siguen funcionando y no pierden el mensaje de "guardado"
    public function test_old_organism_lists_redirect_to_the_filtered_page()
    {
        $this->actingAs($this->analist)
            ->withSession(['success' => 'Bioensayo guardado.'])
            ->get(route('isochrysis-galbana.index'))
            ->assertRedirect(route('bioassays.index', ['tipo' => 'isochrysis-galbana']));

        $this->followRedirects($this->actingAs($this->analist)
            ->withSession(['success' => 'Bioensayo guardado.'])
            ->get(route('isochrysis-galbana.index')))
            ->assertSee('Bioensayo guardado.');
    }

    public function test_menu_has_a_single_bioassays_link()
    {
        $this->actingAs($this->analist)
            ->get(route('sample_entries.index'))
            ->assertSee('href="' . route('bioassays.index') . '"', false)
            ->assertDontSee('bioassaysMenu', false);
    }
}
