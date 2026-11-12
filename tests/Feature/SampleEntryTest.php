<?php

namespace Tests\Feature;

use App\Models\DaphniaMagnaTemplate;
use App\Models\IsochrysisGalbana;
use App\Models\ReceptionTemplate;
use App\Models\SampleEntry;
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
