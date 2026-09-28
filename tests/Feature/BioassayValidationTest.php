<?php

namespace Tests\Feature;

use App\Models\DaphniaMagnaTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BioassayValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_validate_a_bioassay()
    {
        $manager = $this->userWithRole('Manager');
        $bioassay = DaphniaMagnaTemplate::create(['sample' => 'M-001']);

        $this->actingAs($manager)
            ->patch(route('daphnia-magna.validate', $bioassay))
            ->assertRedirect();

        $bioassay->refresh();
        $this->assertSame($manager->id, $bioassay->validated_by);
        $this->assertNotNull($bioassay->validated_at);
    }

    public function test_analist_cannot_validate_a_bioassay()
    {
        $bioassay = DaphniaMagnaTemplate::create(['sample' => 'M-001']);

        $this->actingAs($this->userWithRole('Analist'))
            ->patch(route('daphnia-magna.validate', $bioassay))
            ->assertForbidden();

        $this->assertNull($bioassay->refresh()->validated_at);
    }
}
