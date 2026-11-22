<?php

namespace Tests\Feature;

use App\Models\ArbaciaFertilization;
use App\Models\ArbaciaLarvalStage;
use App\Models\Equipment;
use App\Models\ImageAnalysis;
use App\Models\OpticalCalibration;
use App\Models\OpticalConfiguration;
use App\Models\SampleEntry;
use App\Models\Template;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as PsrRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use LogicException;
use Tests\TestCase;

/**
 * Análisis de imágenes de Arbacia con API_larvas.
 * La API se simula con Http::fake() y los fixtures de resources/fixtures/larvas_api.
 * Ningún test supone un número fijo de fotos por réplica.
 */
class ArbaciaImageAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private $microscope;
    private $camera;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config([
            'larvas_api.url' => 'http://larvas.test',
            'larvas_api.key' => 'clave-de-prueba',
        ]);

        $this->microscope = Equipment::create(['type' => 'microscope', 'code' => 'EQ-MIC-01', 'name' => 'Microscopio triocular']);
        $this->camera = Equipment::create(['type' => 'camera', 'code' => 'EQ-CAM-01', 'name' => 'Cámara Basler']);
    }

    // ===== Ayudas =====

    private function larval(): ArbaciaLarvalStage
    {
        return ArbaciaLarvalStage::create(['sample' => 'M-ARB-1', 'matrix' => 'Agua de mar']);
    }

    private function photo(string $name = 'foto.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 640, 512);
    }

    // Fixtures con el formato real de la API (resources/fixtures/larvas_api)
    private function fixtureRaw(string $name): string
    {
        return file_get_contents(resource_path("fixtures/larvas_api/{$name}.json"));
    }

    private function fixture(string $name): array
    {
        return json_decode($this->fixtureRaw($name), true);
    }

    // Respuesta exitosa de la API con el hash real de la foto enviada
    private function predictBody(UploadedFile $file, string $fixture = 'predict_mixed'): string
    {
        $data = $this->fixture($fixture);
        $data['image_metadata']['image_sha256'] = hash_file('sha256', $file->getRealPath());

        return json_encode($data);
    }

    private function fields(array $overrides = []): array
    {
        return array_merge([
            'row_key' => '3',
            'replica' => 2,
            'microscope_id' => $this->microscope->id,
            'camera_id' => $this->camera->id,
            'magnification' => '10x',
            'fixative' => 'lugol',
            'captured_at' => now()->subHour()->format('Y-m-d H:i'),
        ], $overrides);
    }

    private function upload($user, $bioassay, UploadedFile $file, array $overrides = [], string $section = 'arbacia-larval-stage')
    {
        return $this->actingAs($user)->post(
            route("$section.analyses.store", $bioassay),
            $this->fields($overrides) + ['image' => $file]
        );
    }

    // ===== Subir y analizar =====

    public function test_analist_uploads_a_photo_and_the_full_response_is_recorded_once()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        $body = $this->predictBody($file);
        Http::fake(['larvas.test/*' => Http::response($body, 200)]);

        $this->upload($analist, $bioassay, $file)
            ->assertRedirect(route('arbacia-larval-stage.edit', $bioassay) . '#analisis-imagenes')
            ->assertSessionHas('success');

        $analysis = ImageAnalysis::sole();
        $this->assertSame(ImageAnalysis::STATUS_COMPLETED, $analysis->status);
        $this->assertSame(ArbaciaLarvalStage::class, $analysis->analyzable_type);
        $this->assertSame($bioassay->id, (int) $analysis->analyzable_id);
        $this->assertSame(['3', 2, 1], [$analysis->row_key, (int) $analysis->replica, (int) $analysis->photo_number]);
        $this->assertSame($analist->id, (int) $analysis->performed_by);
        $this->assertSame(hash_file('sha256', $file->getRealPath()), $analysis->image_sha256);
        $this->assertSame($body, $analysis->raw_response);
        $this->assertSame(hash('sha256', $body), $analysis->response_sha256);
        $this->assertSame('larvas_v1_yolo11n_seg_640.onnx', $analysis->model_file);
        $this->assertSame('7e04090e5bfaba158bd9f5a2a01c0ae0981a029dc6c5fcae09306c8b4a3ac3c7', $analysis->model_sha256);
        $this->assertSame(29, $analysis->summary['total_instances']);
        $this->assertSame(['pluteus' => 13, 'otro' => 16], $analysis->summary['counts_by_class']);
        $this->assertCount(29, $analysis->instances);
        $this->assertSame(0.8, $analysis->conf);
        $this->assertSame(0.5, $analysis->iou);
        $this->assertSame(640, (int) $analysis->api_imgsz);
        $this->assertSame('lugol', $analysis->fixative);

        // La imagen original queda en el disco privado, idéntica
        Storage::disk('local')->assertExists($analysis->image_path);
        $this->assertStringStartsWith('arbacia/analyses/', $analysis->image_path);
        $this->assertSame($analysis->image_sha256, hash('sha256', Storage::disk('local')->get($analysis->image_path)));

        // Se llama con polígonos, sin imagen anotada y con la clave en el header
        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/v1/predict')
            && str_contains($request->url(), 'include_polygons=true')
            && str_contains($request->url(), 'return_image=false')
            && $request->hasHeader('X-API-Key', 'clave-de-prueba'));
    }

    public function test_a_replica_accepts_any_number_of_photos()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();

        foreach (range(1, 5) as $i) {
            $file = $this->photo("foto{$i}.jpg");
            Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
            $this->upload($analist, $bioassay, $file, ['row_key' => 'control', 'replica' => 1])->assertSessionHasNoErrors();
        }

        $this->assertSame([1, 2, 3, 4, 5], ImageAnalysis::orderBy('id')->pluck('photo_number')->map(fn ($n) => (int) $n)->all());
        $this->assertSame(5, ImageAnalysis::current()->where('row_key', 'control')->where('replica', 1)->count());
    }

    public function test_fertilization_sheet_is_disabled_until_there_is_a_model_but_keeps_its_rows_and_replicas()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = ArbaciaFertilization::create(['sample' => 'M-ARB-2']);
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);

        // Por defecto (modelo v1) no se ofrece: ni panel en la planilla ni rutas que respondan
        $this->assertSame(['arbacia-larval-stage'], config('larvas_api.enabled_sections'));
        $this->actingAs($analist)->get(route('arbacia-fertilization.edit', $bioassay))
            ->assertOk()
            ->assertDontSee('Análisis de imágenes (IA)')
            ->assertDontSee(route('arbacia-fertilization.analyses.store', $bioassay), false);
        $this->upload($analist, $bioassay, $file, ['row_key' => '1', 'replica' => 3], 'arbacia-fertilization')->assertNotFound();
        $this->assertSame(0, ImageAnalysis::count());
        Http::assertNothingSent();

        // Al reactivarla (cuando exista un modelo) funciona con sus propias filas y réplicas
        config(['larvas_api.enabled_sections' => ['arbacia-larval-stage', 'arbacia-fertilization']]);
        $this->actingAs($analist)->get(route('arbacia-fertilization.edit', $bioassay))
            ->assertOk()
            ->assertSee(route('arbacia-fertilization.analyses.store', $bioassay), false);

        $this->upload($analist, $bioassay, $file, ['row_key' => '1', 'replica' => 3], 'arbacia-fertilization')
            ->assertSessionHasNoErrors();
        $this->assertSame(ArbaciaFertilization::class, ImageAnalysis::sole()->analyzable_type);

        // Fecundación no tiene fila "control" separada ni réplica 4
        $this->upload($analist, $bioassay, $this->photo(), ['row_key' => 'control'], 'arbacia-fertilization')->assertSessionHasErrors('row_key');
        $this->upload($analist, $bioassay, $this->photo(), ['replica' => 4], 'arbacia-fertilization')->assertSessionHasErrors('replica');
    }

    public function errorResponses(): array
    {
        return [
            '400' => [400, 'error_400', 'api_bad_request'],
            '401' => [401, 'error_401', 'api_unauthorized'],
            '413' => [413, 'error_413', 'api_payload_too_large'],
            '422 texto' => [422, 'error_422', 'api_unprocessable'],
            '422 lista (validación FastAPI)' => [422, 'error_422list', 'api_unprocessable'],
            '500' => [500, 'error_500', 'api_internal_error'],
            '503' => [503, 'error_503', 'api_model_not_loaded'],
            '418 inesperado' => [418, 'error_500', 'api_unexpected_status'],
        ];
    }

    /**
     * @dataProvider errorResponses
     */
    public function test_api_errors_are_recorded_as_failed_with_their_code(int $status, string $fixture, string $code)
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $body = $this->fixtureRaw($fixture);
        Http::fake(['larvas.test/*' => Http::response($body, $status)]);

        $this->upload($analist, $bioassay, $this->photo())->assertSessionHas('error');

        $analysis = ImageAnalysis::sole();
        $this->assertSame(ImageAnalysis::STATUS_FAILED, $analysis->status);
        $this->assertSame($code, $analysis->error_code);
        $this->assertSame($status, (int) $analysis->http_status);
        $this->assertSame($body, $analysis->raw_response);
        $this->assertNull($analysis->summary);
        Storage::disk('local')->assertExists($analysis->image_path); // la foto se conserva igual
    }

    public function test_timeout_and_unreachable_are_distinguished()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();

        // Http::fake() acumula stubs en Laravel 8: uno solo que lanza el error de turno
        $error = 'cURL error 28: Operation timed out after 45001 milliseconds with 0 bytes received';
        Http::fake(function () use (&$error) {
            throw new ConnectException($error, new PsrRequest('POST', 'http://larvas.test'));
        });

        $this->upload($analist, $bioassay, $this->photo());
        $this->assertSame('api_timeout_possibly_completed', ImageAnalysis::latest('id')->first()->error_code);

        $error = 'cURL error 7: Failed to connect to larvas.test port 80';
        $this->upload($analist, $bioassay, $this->photo());
        $this->assertSame('api_unreachable', ImageAnalysis::latest('id')->first()->error_code);

        $this->assertSame(2, ImageAnalysis::where('status', ImageAnalysis::STATUS_FAILED)->count());
    }

    public function test_response_for_a_different_image_is_rejected_as_hash_mismatch()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        Http::fake(['larvas.test/*' => Http::response($this->fixtureRaw('predict_mixed'), 200)]); // hash de ceros

        $this->upload($analist, $bioassay, $this->photo());

        $analysis = ImageAnalysis::sole();
        $this->assertSame([ImageAnalysis::STATUS_FAILED, 'hash_mismatch'], [$analysis->status, $analysis->error_code]);
    }

    public function test_response_that_breaks_the_contract_is_rejected()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        $data = json_decode($this->predictBody($file), true);
        $data['summary']['counts_by_class']['pluteus'] += 1; // los conteos ya no cuadran con las instancias
        Http::fake(['larvas.test/*' => Http::response(json_encode($data), 200)]);

        $this->upload($analist, $bioassay, $file);

        $this->assertSame('contract_mismatch', ImageAnalysis::sole()->error_code);
    }

    // ===== Validaciones y permisos =====

    public function test_validated_bioassay_does_not_accept_photos()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $bioassay->update(['validated_by' => $this->userWithRole('Manager')->id, 'validated_at' => now()]);
        Http::fake();

        $this->upload($analist, $bioassay, $this->photo())->assertForbidden();

        $this->assertSame(0, ImageAnalysis::count());
        Http::assertNothingSent();
    }

    public function test_only_jpg_png_and_bmp_are_accepted_and_required_fields_are_checked()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        Http::fake();

        $tiff = UploadedFile::fake()->create('foto.tiff', 100, 'image/tiff');
        $this->upload($analist, $bioassay, $tiff)->assertSessionHasErrors('image');

        $this->upload($analist, $bioassay, $this->photo(), ['fixative' => '', 'magnification' => '', 'microscope_id' => $this->camera->id])
            ->assertSessionHasErrors(['fixative', 'magnification', 'microscope_id']);

        $this->assertSame(0, ImageAnalysis::count());
        Http::assertNothingSent();
    }

    public function test_um_measurements_require_a_valid_calibration_of_the_optical_configuration()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $optical = OpticalConfiguration::create([
            'code' => 'OPT-01', 'microscope_id' => $this->microscope->id, 'camera_id' => $this->camera->id, 'objective_magnification' => '10x',
        ]);
        OpticalCalibration::create([
            'optical_configuration_id' => $optical->id, 'um_per_px' => 0.5,
            'calibrated_at' => now()->subYears(2), 'valid_until' => now()->subYear(),
        ]);
        Http::fake();

        // Calibración vencida: no se puede medir en µm
        $this->upload($analist, $bioassay, $this->photo(), ['optical_configuration_id' => $optical->id, 'requested_um' => 1])
            ->assertSessionHasErrors('optical_configuration_id');
        Http::assertNothingSent();

        // Con calibración vigente se envía um_per_px
        OpticalCalibration::create([
            'optical_configuration_id' => $optical->id, 'um_per_px' => 0.645,
            'calibrated_at' => now()->subMonth(), 'valid_until' => now()->addYear(),
        ]);
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
        $this->upload($analist, $bioassay, $file, ['optical_configuration_id' => $optical->id, 'requested_um' => 1])->assertSessionHasNoErrors();

        $analysis = ImageAnalysis::sole();
        $this->assertSame(0.645, $analysis->um_per_px);
        $this->assertNotNull($analysis->optical_calibration_id);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'um_per_px=0.645'));
    }

    // ===== Reemplazar y anular =====

    public function test_replacing_a_photo_supersedes_it_only_when_the_new_one_succeeds()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        $replacement = $this->photo('nueva2.jpg');
        // Http::fake() acumula stubs en Laravel 8: uno solo cuya respuesta cambia en cada paso
        $next = Http::response($this->predictBody($file), 200);
        Http::fake(function () use (&$next) {
            return $next;
        });

        $this->upload($analist, $bioassay, $file);
        $original = ImageAnalysis::sole();

        // La de reemplazo falla: la original sigue vigente
        $next = Http::response($this->fixtureRaw('error_503'), 503);
        $this->actingAs($analist)->post(route('arbacia-larval-stage.analyses.supersede', $original), $this->fields() + [
            'image' => $this->photo('nueva.jpg'), 'reason' => 'Foto desenfocada',
        ])->assertSessionHas('error');
        $this->assertSame(ImageAnalysis::STATUS_COMPLETED, $original->fresh()->status);

        $this->assertNull($original->fresh()->voided_by);
        $this->assertNull($original->fresh()->voided_at);

        // La de reemplazo funciona (otra persona, más tarde): la original queda reemplazada con su motivo, autor y fecha
        $other = $this->userWithRole('Analist');
        $when = now()->addHours(2)->startOfMinute();
        Carbon::setTestNow($when);
        $next = Http::response($this->predictBody($replacement), 200);
        $this->actingAs($other)->post(route('arbacia-larval-stage.analyses.supersede', $original), $this->fields() + [
            'image' => $replacement, 'reason' => 'Foto desenfocada',
        ])->assertSessionHas('success');
        Carbon::setTestNow();

        $original->refresh();
        $new = ImageAnalysis::latest('id')->first();
        $this->assertSame(ImageAnalysis::STATUS_SUPERSEDED, $original->status);
        $this->assertSame($new->id, (int) $original->superseded_by_id);
        $this->assertSame('Foto desenfocada', $original->void_reason);
        $this->assertSame($other->id, (int) $original->voided_by);
        $this->assertTrue($when->equalTo($original->voided_at));
        $this->assertSame($analist->id, (int) $original->performed_by); // el autor de la foto no cambia
        $this->assertSame('Reemplazada', $original->statusLabel()[0]);

        $expected = "Reemplazada por {$other->name} el " . $when->format('d/m/Y H:i') . ' · Motivo: Foto desenfocada';
        $this->actingAs($analist)->get(route('arbacia-larval-stage.edit', $bioassay))->assertOk()->assertSee($expected);
        $this->actingAs($analist)->get(route('arbacia-larval-stage.analyses.show', $original))->assertOk()->assertSee($expected);
        $this->assertSame([$original->row_key, $original->replica], [$new->row_key, $new->replica]);
        $this->assertSame(3, ImageAnalysis::count()); // nada se borra: original, intento fallido y reemplazo
    }

    public function test_voiding_a_photo_requires_a_reason_and_keeps_the_record()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
        $this->upload($analist, $bioassay, $file);
        $analysis = ImageAnalysis::sole();

        $this->actingAs($analist)->patch(route('arbacia-larval-stage.analyses.void', $analysis), ['reason' => ''])
            ->assertSessionHasErrors('reason');

        $this->assertNull($analysis->fresh()->voided_by);

        $supervisor = $this->userWithRole('Supervisor');
        $when = now()->addHour()->startOfMinute();
        Carbon::setTestNow($when);
        $this->actingAs($supervisor)->patch(route('arbacia-larval-stage.analyses.void', $analysis), ['reason' => 'Réplica equivocada'])
            ->assertSessionHas('success');
        Carbon::setTestNow();

        $analysis->refresh();
        $this->assertSame([ImageAnalysis::STATUS_SUPERSEDED, 'Réplica equivocada', null],
            [$analysis->status, $analysis->void_reason, $analysis->superseded_by_id]);
        $this->assertSame($supervisor->id, (int) $analysis->voided_by);
        $this->assertTrue($when->equalTo($analysis->voided_at));
        $this->assertSame('Anulada', $analysis->statusLabel()[0]);
        $this->assertSame(0, ImageAnalysis::current()->count());

        $expected = "Anulada por {$supervisor->name} el " . $when->format('d/m/Y H:i') . ' · Motivo: Réplica equivocada';
        $this->actingAs($analist)->get(route('arbacia-larval-stage.edit', $bioassay))->assertOk()->assertSee($expected);
        $this->actingAs($analist)->get(route('arbacia-larval-stage.analyses.show', $analysis))->assertOk()->assertSee($expected);

        // Un segundo intento no cambia el autor ni la fecha
        $this->actingAs($analist)->patch(route('arbacia-larval-stage.analyses.void', $analysis), ['reason' => 'Otra vez'])
            ->assertSessionHas('error');
        $this->assertSame($supervisor->id, (int) $analysis->fresh()->voided_by);
    }

    public function test_an_analysis_cannot_be_modified_or_deleted_outside_the_allowed_columns()
    {
        $analist = $this->userWithRole('Analist');
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
        $this->upload($analist, $this->larval(), $file);
        $analysis = ImageAnalysis::sole();

        try {
            $analysis->update(['image_sha256' => str_repeat('f', 64)]);
            $this->fail('Se pudo modificar el hash de la imagen');
        } catch (LogicException $e) {
            $this->assertStringContainsString('image_sha256', $e->getMessage());
        }

        $this->expectException(LogicException::class);
        $analysis->fresh()->delete();
    }

    // ===== Detalle e imagen =====

    public function test_detail_page_shows_all_classes_traceability_and_polygons()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
        $this->upload($analist, $bioassay, $file);
        $analysis = ImageAnalysis::sole();

        $this->actingAs($analist)->get(route('arbacia-larval-stage.analyses.show', $analysis))
            ->assertOk()
            ->assertSee($analysis->uuid)
            ->assertSee($analysis->image_sha256)
            ->assertSee('pluteus')
            ->assertSee('otro')
            ->assertSee('analysisInstances', false)
            ->assertSee(route('arbacia-larval-stage.analyses.image', $analysis), false);

        // El análisis de Estado Larval no se abre desde la ruta de Fecundación
        $this->actingAs($analist)->get(route('arbacia-fertilization.analyses.show', $analysis))->assertNotFound();
    }

    public function test_detail_page_works_with_any_model_classes()
    {
        $analist = $this->userWithRole('Analist');
        $file = $this->photo();
        $data = json_decode($this->predictBody($file, 'predict_empty'), true);
        $data['model_info']['num_classes'] = 2;
        $data['model_info']['available_classes'] = (object) ['0' => 'clase_a', '1' => 'clase_b']; // la API lo envía como objeto
        $data['summary']['counts_by_class'] = ['clase_a' => 1, 'clase_b' => 0];
        $data['summary']['complete_counts_by_class'] = ['clase_a' => 1, 'clase_b' => 0];
        $data['summary']['total_instances'] = $data['summary']['complete_instances'] = 1;
        $data['instances'] = [[
            'instance_id' => 1, 'class_id' => 0, 'label' => 'clase_a', 'confidence' => 0.9, 'touching_border' => false,
            'bbox_xyxy' => [10, 10, 50, 80], 'bbox_xywhn' => [0.05, 0.08, 0.06, 0.14], 'polygon_xy' => null,
            'morphometry' => array_fill_keys(['area_px', 'perimeter_px', 'circularity'], null),
        ]];
        Http::fake(['larvas.test/*' => Http::response(json_encode($data), 200)]);
        $this->upload($analist, $this->larval(), $file);
        $analysis = ImageAnalysis::sole();

        $this->assertSame(ImageAnalysis::STATUS_COMPLETED, $analysis->status);
        $this->actingAs($analist)->get(route('arbacia-larval-stage.analyses.show', $analysis))
            ->assertOk()->assertSee('clase_a');
    }

    public function test_original_image_is_served_only_if_its_hash_still_matches()
    {
        $analist = $this->userWithRole('Analist');
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
        $this->upload($analist, $this->larval(), $file);
        $analysis = ImageAnalysis::sole();

        $this->actingAs($analist)->get(route('arbacia-larval-stage.analyses.image', $analysis))->assertOk();

        Storage::disk('local')->put($analysis->image_path, 'alterada');
        $this->actingAs($analist)->get(route('arbacia-larval-stage.analyses.image', $analysis))->assertStatus(500);
    }

    public function test_sheet_shows_the_panel_and_warns_when_there_is_no_equipment()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();

        $this->actingAs($analist)->get(route('arbacia-larval-stage.edit', $bioassay))
            ->assertOk()
            ->assertSee('Análisis de imágenes (IA)')
            ->assertSee(route('arbacia-larval-stage.analyses.store', $bioassay), false);

        Equipment::query()->update(['status' => 'out_of_service']);
        $this->actingAs($analist)->get(route('arbacia-larval-stage.edit', $bioassay))
            ->assertOk()
            ->assertSee('equipment:register')
            ->assertDontSee(route('arbacia-larval-stage.analyses.store', $bioassay), false);
    }

    public function test_other_species_have_no_image_analysis_routes()
    {
        foreach (['daphnia-magna', 'daphnia-magna-chronic', 'isochrysis-galbana', 'selenastrum-capricornutum',
                  'tisbe-longicornis-water', 'tisbe-longicornis-riles'] as $section) {
            $this->assertFalse(Route::has("$section.analyses.store"), $section);
        }
    }

    // ===== Bloqueo de eliminación =====

    private function sampleEntry(): SampleEntry
    {
        $template = Template::create(['title' => 'Ingreso de muestra', 'type' => 'sample_entry']);

        return SampleEntry::create(['template_id' => $template->id, 'internal_sample_code' => 'M-ARB-9']);
    }

    // Intenta eliminar el bioensayo y luego su ingreso: ambos quedan bloqueados, sin error 500, y nada se borra
    private function assertDeletionBlocked(string $section, $bioassay, SampleEntry $entry): void
    {
        $supervisor = $this->userWithRole('Supervisor');
        $analyses = ImageAnalysis::count();

        $this->actingAs($supervisor)->from(route("$section.edit", $bioassay))
            ->delete(route("$section.destroy", $bioassay))
            ->assertRedirect(route("$section.edit", $bioassay))
            ->assertSessionHas('error', ImageAnalysis::DELETE_BLOCKED_MESSAGE);

        $this->actingAs($supervisor)->from(route('sample_entries.show', $entry))
            ->delete(route('sample_entries.destroy', $entry))
            ->assertRedirect(route('sample_entries.show', $entry))
            ->assertSessionHas('error', ImageAnalysis::DELETE_BLOCKED_MESSAGE);

        $this->assertNotNull($bioassay->fresh());
        $this->assertNotNull($entry->fresh());
        $this->assertNotNull($entry->template->fresh());
        $this->assertSame($analyses, ImageAnalysis::count());
    }

    public function test_bioassay_and_sample_entry_with_analyses_cannot_be_deleted()
    {
        $entry = $this->sampleEntry();
        $bioassay = ArbaciaLarvalStage::create(['sample' => 'M-ARB-9', 'sample_entry_id' => $entry->id]);
        $file = $this->photo();
        Http::fake(['larvas.test/*' => Http::response($this->predictBody($file), 200)]);
        $this->upload($this->userWithRole('Analist'), $bioassay, $file);
        $this->assertSame($entry->id, (int) ImageAnalysis::sole()->sample_entry_id);

        $this->assertDeletionBlocked('arbacia-larval-stage', $bioassay, $entry);
    }

    public function test_deletion_stays_blocked_when_the_only_photo_is_voided_or_failed()
    {
        $analist = $this->userWithRole('Analist');

        // La única foto está anulada
        $entry = $this->sampleEntry();
        $bioassay = ArbaciaLarvalStage::create(['sample' => 'M-ARB-9', 'sample_entry_id' => $entry->id]);
        $file = $this->photo();
        // Http::fake() acumula stubs en Laravel 8: uno solo cuya respuesta cambia en cada paso
        $next = Http::response($this->predictBody($file), 200);
        Http::fake(function () use (&$next) {
            return $next;
        });
        $this->upload($analist, $bioassay, $file);
        $this->actingAs($analist)->patch(route('arbacia-larval-stage.analyses.void', ImageAnalysis::sole()), ['reason' => 'Réplica equivocada']);
        $this->assertSame(0, ImageAnalysis::current()->count());

        $this->assertDeletionBlocked('arbacia-larval-stage', $bioassay, $entry);

        // La única foto falló (en Fecundación, reactivada para la prueba)
        config(['larvas_api.enabled_sections' => ['arbacia-larval-stage', 'arbacia-fertilization']]);
        $other = SampleEntry::create(['template_id' => Template::create(['title' => 'Otro ingreso', 'type' => 'sample_entry'])->id]);
        $fertilization = ArbaciaFertilization::create(['sample' => 'M-ARB-10', 'sample_entry_id' => $other->id]);
        $next = Http::response($this->fixtureRaw('error_503'), 503);
        $this->upload($analist, $fertilization, $this->photo(), ['row_key' => '1', 'replica' => 1], 'arbacia-fertilization');
        $failed = ImageAnalysis::latest('id')->first();
        $this->assertSame([ImageAnalysis::STATUS_FAILED, 'api_model_not_loaded'], [$failed->status, $failed->error_code]);

        $this->assertDeletionBlocked('arbacia-fertilization', $fertilization, $other);
    }

    public function test_bioassay_without_analyses_can_still_be_deleted()
    {
        $entry = $this->sampleEntry();
        $bioassay = ArbaciaLarvalStage::create(['sample' => 'M-ARB-9', 'sample_entry_id' => $entry->id]);

        $this->actingAs($this->userWithRole('Supervisor'))->delete(route('arbacia-larval-stage.destroy', $bioassay))
            ->assertSessionHas('success');
        $this->assertNull($bioassay->fresh());
    }

    // ===== Comando de equipos =====

    public function test_equipment_register_command()
    {
        $this->artisan('equipment:register', ['type' => 'stage_micrometer', 'code' => 'EQ-MICRO-01', 'name' => 'Micrómetro de platina', '--brand' => 'Olympus'])
            ->assertExitCode(0);
        $this->assertDatabaseHas('equipment', ['code' => 'EQ-MICRO-01', 'type' => 'stage_micrometer', 'brand' => 'Olympus']);

        $this->artisan('equipment:register', ['type' => 'stage_micrometer', 'code' => 'EQ-MICRO-01', 'name' => 'Repetido'])->assertExitCode(1);
        $this->artisan('equipment:register', ['type' => 'telescopio', 'code' => 'EQ-X', 'name' => 'X'])->assertExitCode(1);
    }
}
