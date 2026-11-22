<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ImageAnalysis;
use App\Models\OpticalConfiguration;
use App\Services\LarvasApi\LarvasApiClient;
use App\Services\LarvasApi\LarvasApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Fotografías de las réplicas de Arbacia analizadas con API_larvas.
 *
 * Cada foto se guarda en el disco privado, se envía a la API y se registra UNA sola vez en
 * image_analyses, con la respuesta completa o con el error. Reemplazar o anular una foto no
 * borra nada: la anterior queda "superseded" con su motivo. Los resultados no se copian a la
 * planilla: los conteos de la planilla se siguen ingresando a mano.
 */
class ArbaciaImageAnalysisController extends Controller
{
    /** @var LarvasApiClient */
    private $client;

    public function __construct(LarvasApiClient $client)
    {
        $this->client = $client;
    }

    // Subir y analizar una foto de una réplica
    public function store(Request $request, $bioassay)
    {
        $section = $this->section($request);
        $record = $this->bioassay($section, $bioassay);
        $this->authorize('update', $record);

        $data = $this->validatePhoto($request, $section, true);
        [$analysis, $warning] = $this->analyze($request, $record, $data, $data['row_key'], (int) $data['replica']);

        return $this->backToSheet($section, $record, $analysis, $warning, 'Foto analizada');
    }

    // Detalle del análisis: conteos, trazabilidad, polígonos y respuesta cruda
    public function show(Request $request, ImageAnalysis $analysis)
    {
        $section = $this->section($request);
        $this->ensureBelongs($analysis, $section);

        $record = $analysis->analyzable;
        $chain = $this->replacementChain($analysis);

        return view('bioassays.arbacia.analyses.show', compact('analysis', 'section', 'record', 'chain'));
    }

    // La fotografía original, desde el disco privado, verificando que no haya cambiado
    public function image(Request $request, ImageAnalysis $analysis)
    {
        $this->ensureBelongs($analysis, $this->section($request));

        $disk = Storage::disk(config('larvas_api.disk'));
        abort_unless($disk->exists($analysis->image_path), 404, 'No se encontró el archivo de la imagen.');

        $path = $disk->path($analysis->image_path);
        if (! hash_equals($analysis->image_sha256, hash_file('sha256', $path))) {
            Log::critical('Imagen alterada: el SHA-256 del archivo no coincide con el registrado', [
                'analysis' => $analysis->uuid, 'path' => $analysis->image_path,
            ]);
            abort(500, 'La imagen guardada no coincide con su registro (SHA-256). Avise al Supervisor.');
        }

        return response()->file($path, [
            'Content-Type' => $analysis->image_mime,
            'Cache-Control' => 'private, no-store',
        ]);
    }

    // Reemplazar una foto por otra (misma fila y réplica). La anterior queda reemplazada solo si la nueva se analiza bien.
    public function supersede(Request $request, ImageAnalysis $analysis)
    {
        $section = $this->section($request);
        $this->ensureBelongs($analysis, $section);
        $record = $this->bioassay($section, $analysis->analyzable_id);
        $this->authorize('update', $record);

        if (! $analysis->canBeReplaced()) {
            return back()->with('error', 'Esta foto ya fue reemplazada o anulada.');
        }

        $data = $this->validatePhoto($request, $section, false);
        $reason = $data['reason'];

        [$new, $warning] = $this->analyze($request, $record, $data, $analysis->row_key, (int) $analysis->replica);

        if ($new->status !== ImageAnalysis::STATUS_COMPLETED) {
            return $this->backToSheet($section, $record, $new, $warning, 'Foto de reemplazo', 'La foto anterior sigue vigente.');
        }

        $replaced = DB::transaction(function () use ($analysis, $new, $reason) {
            $current = ImageAnalysis::whereKey($analysis->id)->lockForUpdate()->first();
            if (! $current->canBeReplaced()) {
                return false; // otra persona la reemplazó en paralelo: la nueva queda como foto adicional
            }

            $current->update([
                'status' => ImageAnalysis::STATUS_SUPERSEDED,
                'superseded_by_id' => $new->id,
                'void_reason' => $reason,
                'voided_by' => auth()->id(),
                'voided_at' => now(),
            ]);

            return true;
        });

        $extra = $replaced ? null : 'La foto anterior ya había sido reemplazada por otra persona: la nueva quedó como una foto adicional de la réplica.';

        return $this->backToSheet($section, $record, $new, $warning, 'Foto reemplazada', $extra);
    }

    // Anular una foto (ej. quedó en la réplica equivocada). Queda registrada con su motivo.
    public function void(Request $request, ImageAnalysis $analysis)
    {
        $section = $this->section($request);
        $this->ensureBelongs($analysis, $section);
        $record = $this->bioassay($section, $analysis->analyzable_id);
        $this->authorize('update', $record);

        $data = $request->validate(
            ['reason' => 'required|string|min:5|max:1000'],
            [],
            ['reason' => 'motivo']
        );

        if (! $analysis->canBeReplaced()) {
            return back()->with('error', 'Esta foto ya fue reemplazada o anulada.');
        }

        $analysis->update([
            'status' => ImageAnalysis::STATUS_SUPERSEDED,
            'void_reason' => $data['reason'],
            'voided_by' => auth()->id(),
            'voided_at' => now(),
        ]);

        return redirect()->to(route("$section.edit", $record) . '#analisis-imagenes')
            ->with('success', 'Foto anulada: ' . $this->photoName($section, $analysis, $record) . '. Queda registrada con su motivo.');
    }

    // ===================================================================

    private function section(Request $request): string
    {
        $section = $request->route('tipo');
        abort_unless(ImageAnalysis::sectionEnabled($section), 404); // ver larvas_api.enabled_sections

        return $section;
    }

    private function bioassay(string $section, $id): Model
    {
        return ImageAnalysis::SECTIONS[$section]['model']::findOrFail($id);
    }

    private function ensureBelongs(ImageAnalysis $analysis, string $section): void
    {
        abort_unless($analysis->analyzable_type === ImageAnalysis::SECTIONS[$section]['model'], 404);
    }

    private function validatePhoto(Request $request, string $section, bool $isNew): array
    {
        $config = ImageAnalysis::SECTIONS[$section];

        $rules = [
            'image' => ['required', 'file', 'mimes:' . implode(',', config('larvas_api.mimes')), 'max:' . config('larvas_api.max_kb')],
            'microscope_id' => ['required', Rule::exists('equipment', 'id')->where('type', 'microscope')->where('status', 'active')->whereNull('deleted_at')],
            'camera_id' => ['required', Rule::exists('equipment', 'id')->where('type', 'camera')->where('status', 'active')->whereNull('deleted_at')],
            'magnification' => ['required', 'string', 'max:20'],
            'fixative' => ['required', Rule::in(array_keys(config('larvas_api.fixatives')))],
            'captured_at' => ['nullable', 'date', 'before_or_equal:now'],
            'requested_um' => ['nullable', 'boolean'],
            'optical_configuration_id' => ['nullable', 'required_if:requested_um,1', Rule::exists('optical_configurations', 'id')->where('status', 'active')],
        ];

        if ($isNew) {
            $rules['row_key'] = ['required', Rule::in($config['rows'])];
            $rules['replica'] = ['required', 'integer', 'between:1,' . $config['replicas']];
        } else {
            $rules['reason'] = ['required', 'string', 'min:5', 'max:1000'];
        }

        return $request->validate($rules, [
            'image.mimes' => 'La imagen debe ser JPG, PNG o BMP.',
            'image.max' => 'La imagen no puede superar los 20 MB.',
            'optical_configuration_id.required_if' => 'Para medir en µm hay que elegir la configuración óptica calibrada.',
        ], [
            'image' => 'imagen',
            'microscope_id' => 'microscopio',
            'camera_id' => 'cámara',
            'magnification' => 'aumento',
            'fixative' => 'fijador',
            'captured_at' => 'fecha de captura',
            'optical_configuration_id' => 'configuración óptica',
            'row_key' => 'fila',
            'replica' => 'réplica',
            'reason' => 'motivo',
        ]);
    }

    /**
     * Guarda la imagen, la analiza y registra el resultado (una sola inserción).
     *
     * @return array{0: ImageAnalysis, 1: string|null} [análisis, advertencia]
     */
    private function analyze(Request $request, Model $record, array $data, string $rowKey, int $replica): array
    {
        [$optical, $calibration, $warning] = $this->opticalSetup($data);

        $file = $request->file('image');
        $sha256 = hash_file('sha256', $file->getRealPath());
        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension() ?: ($file->extension() ?: 'img'));

        // 1) La imagen original al disco privado, verificando que quedó idéntica
        $disk = Storage::disk(config('larvas_api.disk'));
        $path = $disk->putFileAs(config('larvas_api.directory') . '/' . now()->format('Y/m'), $file, "{$uuid}.{$extension}");
        if (! $path || ! hash_equals($sha256, (string) hash_file('sha256', $disk->path($path)))) {
            Log::error('No se pudo guardar la imagen del análisis', ['uuid' => $uuid, 'path' => $path]);
            abort(500, 'No se pudo guardar la imagen en el servidor.');
        }
        [$width, $height] = @getimagesize($disk->path($path)) ?: [null, null];

        $attributes = [
            'uuid' => $uuid,
            'analyzable_type' => get_class($record),
            'analyzable_id' => $record->getKey(),
            'sample_entry_id' => $record->sample_entry_id,
            'row_key' => $rowKey,
            'replica' => $replica,
            'photo_number' => ImageAnalysis::nextPhotoNumber($record, $rowKey, $replica),
            'requested_um' => (bool) ($data['requested_um'] ?? false),
            'microscope_id' => $data['microscope_id'],
            'camera_id' => $data['camera_id'],
            'magnification' => $data['magnification'],
            'optical_configuration_id' => optional($optical)->id,
            'optical_calibration_id' => optional($calibration)->id,
            'um_per_px' => optional($calibration)->um_per_px,
            'fixative' => $data['fixative'],
            'captured_at' => $data['captured_at'] ?? null,
            'image_path' => $path,
            'image_sha256' => $sha256,
            'image_original_name' => Str::limit($file->getClientOriginalName(), 250, ''),
            'image_mime' => $file->getMimeType() ?: 'application/octet-stream',
            'image_size_bytes' => $file->getSize(),
            'image_width' => $width,
            'image_height' => $height,
            'performed_by' => auth()->id(),
        ];

        // 2) Análisis en API_larvas
        try {
            $result = $this->client->predict($disk->path($path), $file->getClientOriginalName(), $sha256, $calibration ? (float) $calibration->um_per_px : null);
            $attributes += $result->toAttributes() + ['status' => ImageAnalysis::STATUS_COMPLETED];
        } catch (LarvasApiException $e) {
            $attributes += [
                'status' => ImageAnalysis::STATUS_FAILED,
                'http_status' => $e->httpStatus,
                'error_code' => $e->errorCode,
                'error_message' => Str::limit($e->getMessage(), 2000),
                'raw_response' => $e->rawBody,
                'response_sha256' => $e->rawBody !== null ? hash('sha256', $e->rawBody) : null,
                'round_trip_ms' => $e->roundTripMs !== null ? round($e->roundTripMs, 1) : null,
            ];
        }

        // 3) Registro único
        return [ImageAnalysis::create($attributes), $warning];
    }

    /**
     * Configuración óptica y calibración. La calibración vigente se exige solo si se piden medidas en µm.
     *
     * @return array{0: OpticalConfiguration|null, 1: \App\Models\OpticalCalibration|null, 2: string|null}
     */
    private function opticalSetup(array $data): array
    {
        $optical = ! empty($data['optical_configuration_id']) ? OpticalConfiguration::find($data['optical_configuration_id']) : null;
        if (! $optical) {
            return [null, null, null];
        }

        if ((int) $optical->microscope_id !== (int) $data['microscope_id'] || (int) $optical->camera_id !== (int) $data['camera_id']) {
            throw ValidationException::withMessages([
                'optical_configuration_id' => 'La configuración óptica no corresponde al microscopio y la cámara elegidos.',
            ]);
        }

        $calibration = $optical->calibrationValidAt($data['captured_at'] ?? now());

        if (! empty($data['requested_um'])) {
            if (! $calibration) {
                throw ValidationException::withMessages([
                    'optical_configuration_id' => 'La configuración óptica no tiene una calibración vigente para la fecha de captura: no se puede medir en µm.',
                ]);
            }

            return [$optical, $calibration, null];
        }

        $warning = $calibration ? null : 'La configuración óptica no tiene una calibración vigente; las medidas quedaron solo en píxeles.';

        return [$optical, null, $warning];
    }

    private function backToSheet(string $section, Model $record, ImageAnalysis $analysis, ?string $warning, string $verb, ?string $extra = null)
    {
        $redirect = redirect()->to(route("$section.edit", $record) . '#analisis-imagenes');
        $name = $this->photoName($section, $analysis, $record);

        if ($analysis->status === ImageAnalysis::STATUS_COMPLETED) {
            $total = $analysis->summary['total_instances'] ?? 0;
            $redirect->with('success', "{$verb}: {$name}. Instancias detectadas: {$total}." . ($extra ? " {$extra}" : ''));
        } else {
            $message = LarvasApiException::MESSAGES[$analysis->error_code] ?? 'El análisis falló.';
            $redirect->with('error', "{$verb} — {$name}: {$message} (código {$analysis->error_code}). El intento quedó registrado." . ($extra ? " {$extra}" : ''));
        }

        if ($warning) {
            $redirect->with('warning', $warning);
        }

        return $redirect;
    }

    private function photoName(string $section, ImageAnalysis $analysis, Model $record): string
    {
        return ImageAnalysis::rowLabel($section, $analysis->row_key) . ", réplica {$analysis->replica}, foto {$analysis->photo_number}";
    }

    // Fotos anteriores que esta reemplazó (de la más reciente a la más antigua)
    private function replacementChain(ImageAnalysis $analysis): array
    {
        $chain = [];
        $current = $analysis->supersedes;

        while ($current && count($chain) < 50) {
            $chain[] = $current;
            $current = $current->supersedes;
        }

        return $chain;
    }
}
