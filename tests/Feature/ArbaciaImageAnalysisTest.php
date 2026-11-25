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
use App\Services\LarvasApi\LarvasApiException;
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
 * Con predict_mixed cada foto tiene 13 pluteus + 16 otro = 29 individuos.
 */
class ArbaciaImageAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private $microscope;
    private $camera;
    private $photos = 0;

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

    // Cada foto es distinta (otro ancho, otro SHA-256): las fotos vigentes repetidas se rechazan
    private function photo(string $name = 'foto.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 640 + $this->photos++, 512);
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

    // ===== Conteos por réplica y planilla =====

    // Respuesta exitosa (predict_mixed) con el hash de la foto que llega en cada petición
    private function fakeApi(): void
    {
        Http::fake(fn ($request) => $this->predictResponse($request));
    }

    private function predictResponse($request)
    {
        $contents = collect($request->data())->firstWhere('name', 'file')['contents'];
        $data = $this->fixture('predict_mixed');
        $data['image_metadata']['image_sha256'] = hash('sha256', is_resource($contents) ? stream_get_contents($contents, -1, 0) : (string) $contents);

        return Http::response(json_encode($data), 200);
    }

    // La API responde bien hasta que $fail pasa a true (503)
    private function fakeApiThatCanFail(bool &$fail): void
    {
        Http::fake(function ($request) use (&$fail) {
            return $fail ? Http::response($this->fixtureRaw('error_503'), 503) : $this->predictResponse($request);
        });
    }

    private function uploadJson($user, $bioassay, array $overrides = [], string $section = 'arbacia-larval-stage', ?UploadedFile $file = null)
    {
        return $this->actingAs($user)->post(
            route("$section.analyses.store", $bioassay),
            $this->fields($overrides) + ['image' => $file ?: $this->photo()],
            ['Accept' => 'application/json']
        );
    }

    public function test_json_upload_returns_photo_and_replica_counts_and_fills_the_sheet()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $this->fakeApi();

        $response = $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1])->assertCreated();

        $analysis = ImageAnalysis::sole();
        $response->assertJson([
            'status' => 'completed',
            'warning' => null,
            'analysis' => [
                'uuid' => $analysis->uuid, 'photo_number' => 1, 'status' => 'completed', 'error_code' => null, 'error_message' => null,
                'larva' => 13, 'total' => 29, 'url' => route('arbacia-larval-stage.analyses.show', $analysis),
            ],
            'replica' => [
                'row_key' => 'control', 'replica' => 1, 'larva' => 13, 'total' => 29, 'percent' => 44.83,
                'target' => 100, 'complete' => false, 'photos' => 1,
            ],
        ]);

        $bioassay->refresh();
        $this->assertSame([13, 29, 44.83], [$bioassay->control_data['r1_larva'], $bioassay->control_data['r1_total'], $bioassay->control_data['r1_percent']]);
        $this->assertSame($analist->id, (int) $bioassay->updated_by);

        // El panel embebe los totales y muestra el avance de la réplica
        $this->actingAs($analist)->get(route('arbacia-larval-stage.edit', $bioassay))
            ->assertOk()
            ->assertSee('Larva/Total: 13 / 29')
            ->assertSee('Avance: 29 de 100 individuos')
            ->assertSee('"control|1":', false);
    }

    public function test_photos_add_up_and_fill_control_and_numeric_rows_with_average_and_inhibition()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $bioassay->update([
            'control_data' => ['r2_larva' => 4517, 'r2_total' => 10000, 'r2_percent' => 45.17],
            'rows_data' => ['1' => ['concentration' => '6.25%', 'ce' => 'x']],
        ]);
        $this->fakeApi();

        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1]);
        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1])
            ->assertJson(['replica' => ['larva' => 26, 'total' => 58, 'percent' => 44.83, 'photos' => 2]]);
        $this->uploadJson($analist, $bioassay, ['row_key' => '1', 'replica' => 1]);

        $bioassay->refresh();
        $control = $bioassay->control_data;
        $this->assertSame([26, 58, 44.83], [$control['r1_larva'], $control['r1_total'], $control['r1_percent']]);
        $this->assertSame([4517, 10000, 45.17], [$control['r2_larva'], $control['r2_total'], $control['r2_percent']]); // manual, intacta
        $this->assertEquals(45.0, $control['larval_pluteus_avg']);
        $this->assertEquals(45.0, $control['total_larva_percent']);

        $row = $bioassay->rows_data['1'];
        $this->assertSame([13, 29, 44.83], [$row['r1_larva'], $row['r1_total'], $row['r1_percent']]);
        $this->assertEquals(44.83, $row['larval_pluteus_percent']);
        $this->assertEquals(0.38, $row['inhibition_percent']); // (45 − 44.83) / 45 × 100
        $this->assertSame(['6.25%', 'x'], [$row['concentration'], $row['ce']]);
        $this->assertSame([1], array_keys($bioassay->rows_data)); // no crea filas vacías
    }

    public function test_a_replica_accepts_photos_until_it_reaches_the_target()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $this->fakeApi();

        foreach (range(1, 4) as $i) {
            $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1])->assertCreated();
        }

        // La 4ª foto pasa de 100: se conserva el total real
        $this->assertSame([52, 116], [$bioassay->fresh()->control_data['r1_larva'], $bioassay->fresh()->control_data['r1_total']]);

        // La 5ª se rechaza sin guardar la imagen ni llamar a la API
        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1])
            ->assertStatus(422)
            ->assertJsonPath('errors.replica.0', 'La réplica ya llegó a 100 individuos (116). Para cambiar una foto, reemplázala o anúlala.');
        $this->assertSame(4, ImageAnalysis::count());
        $this->assertCount(4, Storage::disk('local')->allFiles('arbacia/analyses'));
        Http::assertSentCount(4);

        // Sin JS (formulario normal) también se rechaza; otra réplica sigue aceptando fotos
        $this->upload($analist, $bioassay, $this->photo(), ['row_key' => 'control', 'replica' => 1])->assertSessionHasErrors('replica');
        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 2])->assertCreated();
        Http::assertSentCount(5);
    }

    public function test_a_failed_photo_does_not_count()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $fail = false;
        $this->fakeApiThatCanFail($fail);

        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1]);
        $fail = true;

        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1])
            ->assertCreated()
            ->assertJson([
                'status' => 'failed',
                'analysis' => [
                    'status' => 'failed', 'error_code' => 'api_model_not_loaded', 'larva' => null, 'total' => null,
                    'error_message' => LarvasApiException::MESSAGES['api_model_not_loaded'],
                ],
                'replica' => ['larva' => 13, 'total' => 29, 'photos' => 1],
            ]);

        $this->assertSame([13, 29], [$bioassay->fresh()->control_data['r1_larva'], $bioassay->fresh()->control_data['r1_total']]);
    }

    public function test_void_and_supersede_recalculate_and_voiding_all_photos_empties_only_that_replica()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $bioassay->update(['control_data' => ['r2_larva' => 40, 'r2_total' => 80, 'r2_percent' => 50]]);
        $this->fakeApi();

        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1]);
        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1]);
        [$first, $second] = ImageAnalysis::orderBy('id')->get();

        // Reemplazar: la réplica sigue con 2 fotos vigentes
        $this->actingAs($analist)->post(route('arbacia-larval-stage.analyses.supersede', $first), $this->fields() + [
            'image' => $this->photo(), 'reason' => 'Foto desenfocada',
        ])->assertSessionHas('success');
        $this->assertSame([26, 58], [$bioassay->fresh()->control_data['r1_larva'], $bioassay->fresh()->control_data['r1_total']]);

        // Anular: queda 1
        $this->actingAs($analist)->patch(route('arbacia-larval-stage.analyses.void', $second), ['reason' => 'Réplica equivocada']);
        $this->assertSame([13, 29], [$bioassay->fresh()->control_data['r1_larva'], $bioassay->fresh()->control_data['r1_total']]);

        // Anular la última: la réplica queda vacía y la manual no cambia
        $this->actingAs($analist)->patch(route('arbacia-larval-stage.analyses.void', ImageAnalysis::current()->sole()), ['reason' => 'Réplica equivocada']);
        $control = $bioassay->fresh()->control_data;
        $this->assertSame([null, null, null], [$control['r1_larva'], $control['r1_total'], $control['r1_percent']]);
        $this->assertSame([40, 80, 50], [$control['r2_larva'], $control['r2_total'], $control['r2_percent']]);
        $this->assertEquals(50, $control['larval_pluteus_avg']);
    }

    public function test_saving_the_sheet_keeps_the_ai_counts_and_manual_replicas_as_sent()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $this->fakeApi();
        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1]);
        $this->uploadJson($analist, $bioassay, ['row_key' => '2', 'replica' => 3]);

        $this->actingAs($analist)->put(route('arbacia-larval-stage.update', $bioassay), [
            'sample' => 'M-ARB-1',
            'control' => ['r1_larva' => '99', 'r1_total' => '100', 'r1_percent' => '99.00', 'r2_larva' => '4517', 'r2_total' => '10000', 'r2_percent' => '45.17'],
            'rows' => ['2' => ['concentration' => '12.5%', 'r3_larva' => '1', 'r3_total' => '1', 'r4_larva' => '5517', 'r4_total' => '10000']],
        ])->assertSessionHasNoErrors();

        $bioassay->refresh();
        $control = $bioassay->control_data;
        $this->assertSame([13, 29, 44.83], [$control['r1_larva'], $control['r1_total'], $control['r1_percent']]);
        $this->assertSame(['4517', '10000', '45.17'], [$control['r2_larva'], $control['r2_total'], $control['r2_percent']]);
        $this->assertEquals(45.0, $control['larval_pluteus_avg']); // (44.83 + 45.17) / 2

        $row = $bioassay->rows_data['2'];
        $this->assertSame([13, 29, 44.83], [$row['r3_larva'], $row['r3_total'], $row['r3_percent']]);
        $this->assertSame(['12.5%', '5517', '10000'], [$row['concentration'], $row['r4_larva'], $row['r4_total']]);
        $this->assertEquals(50.0, $row['larval_pluteus_percent']); // (44.83 + 55.17) / 2
        $this->assertEquals(-11.11, $row['inhibition_percent']); // (45 − 50) / 45 × 100
    }

    public function test_validated_bioassay_rejects_json_uploads_without_touching_the_sheet()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $bioassay->update(['validated_by' => $this->userWithRole('Manager')->id, 'validated_at' => now()]);
        Http::fake();

        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1])->assertForbidden();

        $this->assertSame(0, ImageAnalysis::count());
        $this->assertNull($bioassay->fresh()->control_data);
        Http::assertNothingSent();
    }

    public function test_fertilization_json_upload_is_still_not_found()
    {
        $bioassay = ArbaciaFertilization::create(['sample' => 'M-ARB-2']);
        Http::fake();

        $this->uploadJson($this->userWithRole('Analist'), $bioassay, ['row_key' => '1', 'replica' => 1], 'arbacia-fertilization')->assertNotFound();

        $this->assertSame(0, ImageAnalysis::count());
        Http::assertNothingSent();
    }

    // ===== Fotos repetidas =====

    public function test_a_current_photo_cannot_be_uploaded_again_in_the_same_bioassay()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        $this->fakeApi();

        $this->uploadJson($analist, $bioassay, ['row_key' => '3', 'replica' => 2], 'arbacia-larval-stage', $file)->assertCreated();

        // Misma foto en otra réplica (con JSON y sin JS)
        $this->uploadJson($analist, $bioassay, ['row_key' => 'control', 'replica' => 1], 'arbacia-larval-stage', $file)
            ->assertStatus(422)
            ->assertJsonPath('errors.image.0', 'Esta foto ya está analizada en Fila 3, réplica 2, foto 1.');
        $this->upload($analist, $bioassay, $file, ['row_key' => 'control', 'replica' => 1])->assertSessionHasErrors('image');

        // Tampoco sirve como reemplazo
        $this->actingAs($analist)->post(route('arbacia-larval-stage.analyses.supersede', ImageAnalysis::sole()), $this->fields() + [
            'image' => $file, 'reason' => 'Foto desenfocada',
        ])->assertSessionHasErrors('image');

        $this->assertSame(1, ImageAnalysis::count());
        $this->assertCount(1, Storage::disk('local')->allFiles('arbacia/analyses'));
        Http::assertSentCount(1);
    }

    public function test_a_failed_photo_can_be_uploaded_again()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        $fail = true;
        $this->fakeApiThatCanFail($fail);

        $this->uploadJson($analist, $bioassay, [], 'arbacia-larval-stage', $file)->assertJsonPath('status', 'failed');
        $fail = false;
        $this->uploadJson($analist, $bioassay, [], 'arbacia-larval-stage', $file)->assertJsonPath('status', 'completed');

        $this->assertSame(2, ImageAnalysis::where('image_sha256', hash_file('sha256', $file->getRealPath()))->count());
        $this->assertSame(1, ImageAnalysis::current()->count());
    }

    public function test_a_voided_photo_can_be_uploaded_again()
    {
        $analist = $this->userWithRole('Analist');
        $bioassay = $this->larval();
        $file = $this->photo();
        $this->fakeApi();

        $this->uploadJson($analist, $bioassay, ['row_key' => '3', 'replica' => 2], 'arbacia-larval-stage', $file);
        $this->actingAs($analist)->patch(route('arbacia-larval-stage.analyses.void', ImageAnalysis::sole()), ['reason' => 'Réplica equivocada']);

        $this->uploadJson($analist, $bioassay, ['row_key' => '3', 'replica' => 1], 'arbacia-larval-stage', $file)->assertCreated();

        $current = ImageAnalysis::current()->sole();
        $this->assertSame(['3', 1], [$current->row_key, (int) $current->replica]);
    }

    public function test_the_same_photo_is_accepted_in_another_bioassay()
    {
        $analist = $this->userWithRole('Analist');
        $file = $this->photo();
        $this->fakeApi();

        $this->uploadJson($analist, $this->larval(), [], 'arbacia-larval-stage', $file)->assertCreated();
        $this->uploadJson($analist, $this->larval(), [], 'arbacia-larval-stage', $file)->assertCreated();

        $this->assertSame(2, ImageAnalysis::current()->count());
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
