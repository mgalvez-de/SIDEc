<?php

namespace Tests\Feature;

use App\Models\ArbaciaLarvalStage;
use App\Models\DaphniaMagnaTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BioassayDurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_shows_the_duration_instead_of_a_timer()
    {
        $bioassay = DaphniaMagnaTemplate::create([
            'sample' => 'M-001',
            'preliminary_start_at' => '2026-10-05 12:00',
            'preliminary_end_at' => '2026-10-07 12:10',
            'definitive_start_at' => '2026-10-08 09:00',
            'definitive_end_at' => '2026-10-10 11:00',
        ]);

        $this->actingAs($this->userWithRole('Analist'))
            ->get(route('daphnia-magna.edit', $bioassay))
            ->assertOk()
            ->assertSee('48 h 10 min: dentro del margen')
            ->assertSee('50 h 0 min: duró más de lo exigido')
            ->assertDontSee('Iniciar Preliminar');
    }

    public function test_sample_detail_marks_bioassays_out_of_time()
    {
        $larval = new ArbaciaLarvalStage([
            'bioassay_start' => '2026-10-05 09:00',
            'fixation_time_end' => '2026-10-07 12:00',
        ]);

        $this->assertTrue($larval->hasDurationProblem());

        $larval->fixation_time_end = '2026-10-07 09:20';
        $this->assertFalse($larval->hasDurationProblem());
    }
}
