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

        // El listado de cada organismo es la página Bioensayos filtrada
        $list = route('bioassays.index', ['tipo' => $slug]);
        $this->actingAs($analist)->get(route("$slug.index"))->assertRedirect($list);
        $this->actingAs($analist)->get($list)->assertOk();

        // Los bioensayos no se crean a mano: los crea el ingreso de la muestra
        $this->assertFalse(\Illuminate\Support\Facades\Route::has("$slug.create"));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has("$slug.store"));
        $bioassay = $model::create(['sample' => 'QA-1', 'created_by' => $analist->id]);

        $supervisor = $this->userWithRole('Supervisor');
        $deleteForm = 'action="' . route("$slug.destroy", $bioassay) . '"';

        // "Eliminar" solo lo ve quien puede eliminar (Supervisor / Jefe de Área)
        $this->actingAs($analist)->get($list)
            ->assertSee(route("$slug.edit", $bioassay), false)
            ->assertDontSee($deleteForm, false);
        $this->actingAs($supervisor)->get($list)->assertSee($deleteForm, false);

        $this->actingAs($analist)->get(route("$slug.edit", $bioassay))
            ->assertOk()
            ->assertSee('QA-1');

        $this->actingAs($analist)
            ->put(route("$slug.update", $bioassay), ['sample' => 'QA-1', 'observations' => 'Revisado en QA'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Revisado en QA', $bioassay->refresh()->observations);
        $this->assertSame($analist->id, $bioassay->updated_by); // queda registrado quién lo modificó

        $this->actingAs($this->userWithRole('Manager'))
            ->patch(route("$slug.validate", $bioassay))
            ->assertRedirect();
        $this->assertNotNull($bioassay->refresh()->validated_at);
        $this->assertSame($analist->id, $bioassay->updated_by); // validar no cuenta como modificar

        // Validado = bloqueado: no se modifica ni se elimina, y la planilla lo avisa
        $this->actingAs($analist)
            ->put(route("$slug.update", $bioassay), ['sample' => 'QA-1', 'observations' => 'Cambio tras validar'])
            ->assertForbidden();
        $this->actingAs($supervisor)->delete(route("$slug.destroy", $bioassay))->assertForbidden();
        $this->assertSame('Revisado en QA', $bioassay->refresh()->observations);
        $this->actingAs($analist)->get(route("$slug.edit", $bioassay))
            ->assertOk()
            ->assertSee('Bioensayo validado: solo lectura.')
            ->assertDontSee(route('bioassays.reopen', ['tipo' => $slug, 'id' => $bioassay->id]), false);

        // Solo Supervisor o Jefe de Área lo reabren
        $reopen = route('bioassays.reopen', ['tipo' => $slug, 'id' => $bioassay->id]);
        $this->actingAs($analist)->patch($reopen)->assertForbidden();
        $this->actingAs($supervisor)->get(route("$slug.edit", $bioassay))->assertSee($reopen, false);
        $this->actingAs($supervisor)->patch($reopen)->assertRedirect();
        $this->assertNull($bioassay->refresh()->validated_at);

        // Eliminar: solo Supervisor / Jefe de Área
        $this->actingAs($analist)->delete(route("$slug.destroy", $bioassay))->assertForbidden();
        $this->actingAs($supervisor)->delete(route("$slug.destroy", $bioassay))->assertRedirect();
        $this->assertNull($model::find($bioassay->id));
    }
}
