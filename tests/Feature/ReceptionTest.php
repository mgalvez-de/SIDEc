<?php

namespace Tests\Feature;

use App\Models\ReceptionTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionTest extends TestCase
{
    use RefreshDatabase;

    private function receptionPayload(array $samples): array
    {
        return ['title' => 'RECEPCIÓN DE MUESTRAS', 'version' => '03', 'samples' => $samples];
    }

    public function test_a_sample_code_cannot_be_received_twice()
    {
        $supervisor = $this->userWithRole('Supervisor');

        $this->actingAs($supervisor)->post(route('receptions.store'),
            $this->receptionPayload([['sample_identifier' => 'Agua 1', 'internal_sample_code' => 'M-99']]));

        $this->actingAs($supervisor)->post(route('receptions.store'),
            $this->receptionPayload([['sample_identifier' => 'Agua 2', 'internal_sample_code' => 'M-99']]))
            ->assertSessionHasErrors('samples.0.internal_sample_code');

        $this->assertSame(1, ReceptionTemplate::where('internal_sample_code', 'M-99')->count());
    }

    public function test_repeated_codes_in_the_same_form_are_rejected()
    {
        $this->actingAs($this->userWithRole('Supervisor'))
            ->post(route('receptions.store'), $this->receptionPayload([
                ['sample_identifier' => 'Agua 1', 'internal_sample_code' => 'M-50'],
                ['sample_identifier' => 'Agua 2', 'internal_sample_code' => 'M-50'],
            ]))
            ->assertSessionHasErrors('samples.0.internal_sample_code');

        $this->assertSame(0, ReceptionTemplate::count());
    }

    public function test_editing_a_reception_keeps_its_own_code()
    {
        $supervisor = $this->userWithRole('Supervisor');
        $this->actingAs($supervisor)->post(route('receptions.store'),
            $this->receptionPayload([['sample_identifier' => 'Agua 1', 'internal_sample_code' => 'M-10']]));
        $reception = ReceptionTemplate::firstOrFail();

        $this->actingAs($supervisor)
            ->put(route('receptions.update', $reception), [
                'title' => 'RECEPCIÓN DE MUESTRAS',
                'version' => '03',
                'sample_identifier' => 'Agua 1 (corregida)',
                'internal_sample_code' => 'M-10',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Agua 1 (corregida)', $reception->refresh()->sample_identifier);
    }
}
