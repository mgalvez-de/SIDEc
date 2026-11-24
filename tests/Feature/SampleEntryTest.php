<?php

namespace Tests\Feature;

use App\Models\DaphniaMagnaTemplate;
use App\Models\IsochrysisGalbana;
use App\Models\ReceptionTemplate;
use App\Models\SampleEntry;
use App\Models\SelenastrumCapricornutum;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_entering_a_sample_creates_its_assigned_bioassays_linked_by_fk()
    {
        ReceptionTemplate::create([
            'template_id' => Template::create(['title' => 'Recepción', 'type' => 'reception'])->id,
            'internal_sample_code' => 'M-100',
            'assigned_bioassays' => ['Daphnia magna Agudo', 'Isochrysis galbana'],
        ]);

        $analist = $this->userWithRole('Analist');

        $this->actingAs($analist)
            ->post(route('sample_entries.store'), [
                'title' => 'Ingreso de muestra',
                'version' => '1',
                'internal_sample_code' => 'M-100',
            ])
            ->assertRedirect(route('sample_entries.index'));

        $entry = SampleEntry::where('internal_sample_code', 'M-100')->firstOrFail();

        $this->assertSame($entry->id, DaphniaMagnaTemplate::firstOrFail()->sample_entry_id);
        $this->assertSame($entry->id, IsochrysisGalbana::firstOrFail()->sample_entry_id);
        $this->assertSame($analist->id, DaphniaMagnaTemplate::firstOrFail()->created_by);
        $this->assertSame($analist->id, IsochrysisGalbana::firstOrFail()->created_by);
    }

    public function test_bioassays_are_created_with_the_official_sheet_code_version_and_validity()
    {
        ReceptionTemplate::create([
            'template_id' => Template::create(['title' => 'Recepción', 'type' => 'reception'])->id,
            'internal_sample_code' => 'M-200',
            'assigned_bioassays' => array_keys(config('bioensayos')),
        ]);

        $analist = $this->userWithRole('Analist');

        $this->actingAs($analist)->post(route('sample_entries.store'), [
            'title' => 'Ingreso de muestra',
            'version' => '1',
            'internal_sample_code' => 'M-200',
        ]);

        foreach (config('bioensayos') as $name => $sheet) {
            $template = $sheet['model']::firstOrFail()->template;

            $this->assertSame($sheet['code'], $template->code, $name);
            $this->assertEquals($sheet['version'], $template->version, $name);
            $this->assertSame($sheet['validity'], $template->validity, $name);
        }

        $this->actingAs($analist)
            ->get(route('selenastrum-capricornutum.edit', SelenastrumCapricornutum::firstOrFail()))
            ->assertOk()
            ->assertSee('RT-04.04 | Versión: 08 | Vigencia: 01.04.2024');
    }

    public function test_every_bioassay_has_its_official_sheet_configured()
    {
        // Mismos nombres y mismos modelos (sin importar el orden)
        $this->assertEquals(
            SampleEntry::BIOASSAY_MODELS,
            collect(config('bioensayos'))->map(fn ($sheet) => $sheet['model'])->all()
        );
    }

    public function test_the_database_rejects_a_repeated_internal_sample_code()
    {
        $nueva = fn () => ReceptionTemplate::create([
            'template_id' => Template::create(['title' => 'Recepción', 'type' => 'reception'])->id,
            'internal_sample_code' => 'M-300',
        ]);

        $nueva();

        $this->expectException(\Illuminate\Database\QueryException::class);
        $nueva();
    }

    public function test_the_same_sample_cannot_be_entered_twice()
    {
        ReceptionTemplate::create([
            'template_id' => Template::create(['title' => 'Recepción', 'type' => 'reception'])->id,
            'internal_sample_code' => 'M-100',
        ]);
        $analist = $this->userWithRole('Analist');
        $payload = ['title' => 'Ingreso', 'version' => '1', 'internal_sample_code' => 'M-100'];

        $this->actingAs($analist)->post(route('sample_entries.store'), $payload);
        $this->actingAs($analist)->post(route('sample_entries.store'), $payload)
            ->assertSessionHasErrors('internal_sample_code');

        $this->assertSame(1, SampleEntry::count());
    }
}
