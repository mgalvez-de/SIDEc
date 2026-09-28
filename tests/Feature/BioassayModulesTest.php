<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BioassayModulesTest extends TestCase
{
    use RefreshDatabase;

    public function bioassayModules(): array
    {
        return [
            'Daphnia magna agudo'       => ['daphnia-magna', \App\Models\DaphniaMagnaTemplate::class],
            'Daphnia magna crónico'     => ['daphnia-magna-chronic', \App\Models\DaphniaMagnaChronic::class],
            'Isochrysis galbana'        => ['isochrysis-galbana', \App\Models\IsochrysisGalbana::class],
            'Selenastrum capricornutum' => ['selenastrum-capricornutum', \App\Models\SelenastrumCapricornutum::class],
            'Tisbe agua'                => ['tisbe-longicornis-water', \App\Models\TisbeLongicornisWater::class],
            'Tisbe RILes'               => ['tisbe-longicornis-riles', \App\Models\TisbeLongicornisRiles::class],
            'Arbacia fecundación'       => ['arbacia-fertilization', \App\Models\ArbaciaFertilization::class],
            'Arbacia estado larval'     => ['arbacia-larval-stage', \App\Models\ArbaciaLarvalStage::class],
        ];
    }

    /**
     * @dataProvider bioassayModules
     */
    public function test_full_bioassay_lifecycle(string $slug, string $model)
    {
        $analist = $this->userWithRole('Analist');

        $this->actingAs($analist)->get(route("$slug.index"))
            ->assertOk()
            ->assertSee(route("$slug.create"), false);
        $this->actingAs($analist)->get(route("$slug.create"))
            ->assertOk()
            ->assertSee(route("$slug.store"), false);

        $this->actingAs($analist)
            ->post(route("$slug.store"), ['sample' => 'QA-1', 'matrix' => 'Agua de mar'])
            ->assertRedirect();
        $bioassay = $model::where('sample', 'QA-1')->firstOrFail();
        $this->assertSame($analist->id, $bioassay->created_by);

        $this->actingAs($analist)->get(route("$slug.index"))
            ->assertSee(route("$slug.edit", $bioassay), false)
            ->assertSee('action="' . route("$slug.destroy", $bioassay) . '"', false);

        $this->actingAs($analist)->get(route("$slug.edit", $bioassay))
            ->assertOk()
            ->assertSee('QA-1');

        $this->actingAs($analist)
            ->put(route("$slug.update", $bioassay), ['sample' => 'QA-1', 'observations' => 'Revisado en QA'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Revisado en QA', $bioassay->refresh()->observations);

        $this->actingAs($this->userWithRole('Manager'))
            ->patch(route("$slug.validate", $bioassay))
            ->assertRedirect();
        $this->assertNotNull($bioassay->refresh()->validated_at);

        $this->actingAs($analist)->delete(route("$slug.destroy", $bioassay))->assertRedirect();
        $this->assertNull($model::find($bioassay->id));
    }
}
