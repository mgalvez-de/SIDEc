<?php

namespace Tests\Feature;

use App\Models\ReceptionTemplate;
use App\Models\RejectionTemplate;
use App\Models\SampleEntry;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    private const MODULES = [
        'daphnia-magna'             => \App\Models\DaphniaMagnaTemplate::class,
        'daphnia-magna-chronic'     => \App\Models\DaphniaMagnaChronic::class,
        'isochrysis-galbana'        => \App\Models\IsochrysisGalbana::class,
        'selenastrum-capricornutum' => \App\Models\SelenastrumCapricornutum::class,
        'tisbe-longicornis-water'   => \App\Models\TisbeLongicornisWater::class,
        'tisbe-longicornis-riles'   => \App\Models\TisbeLongicornisRiles::class,
        'arbacia-fertilization'     => \App\Models\ArbaciaFertilization::class,
        'arbacia-larval-stage'      => \App\Models\ArbaciaLarvalStage::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
    }

    public function test_dashboard_shows_every_sample_state()
    {
        $this->actingAs(User::where('email', 'areamanager@a')->firstOrFail())
            ->get('/dashboard')
            ->assertOk()
            ->assertViewHas('totals', fn ($totals) => $totals['received'] === 11
                && $totals['rejected'] === 3
                && $totals['not_entered'] === 2
                && $totals['in_process'] === 6
                && $totals['completed'] === 3
                && $totals['warning'] === 1
                && $totals['overdue'] === 1)
            ->assertSee('1 muestra con plazo vencido')
            ->assertSee('1 muestra por vencer');
    }

    public function test_sample_entries_list_separates_in_process_from_completed()
    {
        $codes = fn ($entries) => $entries->pluck('internal_sample_code')->sort()->values()->all();

        $this->actingAs(User::where('email', 'analist@a')->firstOrFail())
            ->get(route('sample_entries.index'))
            ->assertOk()
            ->assertViewHas('inProcessEntries', fn ($entries) => $codes($entries) === ['M-2026-004', 'M-2026-007', 'M-2026-008', 'M-2026-009', 'M-2026-011', 'M-2026-012'])
            ->assertViewHas('completedEntries', fn ($entries) => $codes($entries) === ['M-2026-001', 'M-2026-002', 'M-2026-005'])
            ->assertViewHas('newReceptions', fn ($receptions) => $codes($receptions) === ['M-2026-013', 'M-2026-014'])
            ->assertDontSee('Reiniciando timer');
    }

    // Si una duración del modelo no coincide con el protocolo, los ensayos de demostración saldrían "Fuera de tiempo"
    public function test_finished_demo_bioassays_are_within_their_duration()
    {
        foreach (self::MODULES as $slug => $model) {
            foreach ($model::all() as $bioassay) {
                foreach ($bioassay->durationChecks() as $phase => $check) {
                    $this->assertNotContains($check['status'], ['short', 'long'], "$slug {$bioassay->sample} ($phase)");
                }
            }
        }
    }

    public function test_every_demo_record_opens_without_errors()
    {
        $analist = User::where('email', 'analist@a')->firstOrFail();
        $supervisor = User::where('email', 'supervisor@a')->firstOrFail();

        foreach (self::MODULES as $slug => $model) {
            $this->assertGreaterThan(0, $model::count(), "$slug sin registros de demostración");
            $this->actingAs($analist)->get(route("$slug.index"))->assertOk();
            foreach ($model::all() as $bioassay) {
                $this->actingAs($analist)->get(route("$slug.edit", $bioassay))->assertOk()->assertSee($bioassay->sample);
            }
        }

        foreach (SampleEntry::all() as $entry) {
            $this->actingAs($analist)->get(route('sample_entries.show', $entry))->assertOk();
        }
        foreach (ReceptionTemplate::all() as $reception) {
            $this->actingAs($supervisor)->get(route('receptions.show', $reception))->assertOk();
        }
        foreach (RejectionTemplate::all() as $rejection) {
            $this->actingAs($supervisor)->get(route('rejections.show', $rejection))->assertOk();
        }
    }
}
