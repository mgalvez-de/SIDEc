<?php

namespace App\Http\Controllers;

use App\Models\ArbaciaFertilization;
use App\Models\ArbaciaLarvalStage;
use App\Models\DaphniaMagnaChronic;
use App\Models\DaphniaMagnaTemplate;
use App\Models\IsochrysisGalbana;
use App\Models\SelenastrumCapricornutum;
use App\Models\TisbeLongicornisRiles;
use App\Models\TisbeLongicornisWater;
use App\Support\Navigation;
use Illuminate\Http\Request;

/**
 * Página única "Bioensayos": los ensayos de los 8 organismos en una sola tabla,
 * con filtro por organismo y por estado (Nuevas, En proceso, Validadas).
 */
class BioassayController extends Controller
{
    // Prefijo de la ruta (el mismo de Navigation::BIOASSAYS) => modelo
    public const MODELS = [
        'daphnia-magna'             => DaphniaMagnaTemplate::class,
        'daphnia-magna-chronic'     => DaphniaMagnaChronic::class,
        'isochrysis-galbana'        => IsochrysisGalbana::class,
        'selenastrum-capricornutum' => SelenastrumCapricornutum::class,
        'tisbe-longicornis-water'   => TisbeLongicornisWater::class,
        'tisbe-longicornis-riles'   => TisbeLongicornisRiles::class,
        'arbacia-larval-stage'      => ArbaciaLarvalStage::class,
        'arbacia-fertilization'     => ArbaciaFertilization::class,
    ];

    // Estado (valor en la URL) => nombre que se muestra. El orden es el de la tabla.
    public const STATUSES = [
        'en-proceso' => 'En proceso',
        'nuevas'     => 'Nuevas',
        'validadas'  => 'Validadas',
    ];

    public function index(Request $request)
    {
        // Un valor desconocido en la URL se ignora y se muestra todo
        $type = array_key_exists($request->query('tipo'), self::MODELS) ? $request->query('tipo') : null;
        $status = array_key_exists($request->query('estado'), self::STATUSES) ? $request->query('estado') : null;

        $rows = collect(self::MODELS)
            ->when($type, fn ($models) => $models->only($type))
            ->flatMap(fn ($model, $section) => $model::query()
                ->get(['id', 'sample', 'analyst', 'created_at', 'validated_at', $model::startField()])
                ->map(fn ($bioassay) => (object) [
                    'section'    => $section,
                    'label'      => Navigation::BIOASSAYS[$section][0],
                    'icon'       => Navigation::BIOASSAYS[$section][1],
                    'bioassay'   => $bioassay,
                    'status'     => $this->status($bioassay),
                    'started_at' => $bioassay->startedAt(),
                ]));

        // Cuántos hay en cada estado (para los botones de filtro), antes de filtrar por estado
        $counts = collect(self::STATUSES)->map(fn ($label, $key) => $rows->where('status', $key)->count());

        $statusOrder = array_flip(array_keys(self::STATUSES));
        $rows = $rows
            ->when($status, fn ($rows) => $rows->where('status', $status))
            ->sortBy([
                fn ($a, $b) => $statusOrder[$a->status] <=> $statusOrder[$b->status],
                fn ($a, $b) => $b->bioassay->created_at <=> $a->bioassay->created_at,
            ])
            ->values();

        $types = Navigation::BIOASSAYS;
        $statuses = self::STATUSES;

        return view('bioassays.index', compact('rows', 'counts', 'type', 'status', 'types', 'statuses'));
    }

    // Los listados antiguos (/daphnia-magna, /arbacia-fertilization...) llevan a esta página ya filtrada
    public function byType(string $tipo)
    {
        session()->reflash(); // que no se pierda el mensaje de "guardado correctamente" en el camino

        return redirect()->route('bioassays.index', ['tipo' => $tipo]);
    }

    // Reabrir un bioensayo validado: se quita la validación y vuelve a poder modificarse
    public function reopen(string $tipo, int $id)
    {
        abort_unless(isset(self::MODELS[$tipo]), 404);

        $bioassay = self::MODELS[$tipo]::findOrFail($id);
        $this->authorize('reopen', $bioassay);

        $bioassay->update(['validated_by' => null, 'validated_at' => null]);
        \Log::info("Bioensayo reabierto: {$tipo} #{$id} (muestra {$bioassay->sample}) por el usuario " . auth()->id());

        return back()->with('success', 'Bioensayo reabierto: ya se puede modificar. Recuerda validarlo de nuevo al terminar.');
    }

    private function status($bioassay): string
    {
        if ($bioassay->validated_at) {
            return 'validadas';
        }

        return $bioassay->isStarted() ? 'en-proceso' : 'nuevas';
    }
}
