<?php
namespace App\Http\Controllers;

use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class SampleEntryController extends Controller
{

    public function dashboard()
    {
        // -------- SampleEntry base --------
        $totalSamples = SampleEntry::count();

        $byState = SampleEntry::select('state', \DB::raw('COUNT(*) as total'))
            ->groupBy('state')
            ->pluck('total', 'state');

        $byType = SampleEntry::select('sample_type', \DB::raw('COUNT(*) as total'))
            ->groupBy('sample_type')
            ->pluck('total', 'sample_type');

        $monthlySamples = SampleEntry::selectRaw('DATE_FORMAT(received_at,"%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Estadísticas ambientales
        $avgPh           = SampleEntry::avg('ph');
        $avgTemp         = SampleEntry::avg('temperature');
        $avgSalinity     = SampleEntry::avg('salinity');
        $avgConductivity = SampleEntry::avg('conductivity');
        $avgOxygen       = SampleEntry::avg('dissolved_oxygen');

        // ---------------- BIOENSAYOS ----------------

        $arbacia     = \App\Models\ArbaciaFertilization::count();
        $daphnia     = \App\Models\DaphniaMagnaTemplate::count();
        $isochrysis  = \App\Models\IsochrysisGalbana::count();
        $selenastrum = \App\Models\SelenastrumCapricornutum::count();
        $tisbeWater  = \App\Models\TisbeLongicornisWater::count();
        $tisbeRiles  = \App\Models\TisbeLongicornisRiles::count();

        $bioassayTotals = [
            'Arbacia fertilization'     => $arbacia,
            'Daphnia magna'             => $daphnia,
            'Isochrysis galbana'        => $isochrysis,
            'Selenastrum capricornutum' => $selenastrum,
            'Tisbe water'               => $tisbeWater,
            'Tisbe riles'               => $tisbeRiles,
        ];

        // bioensayos asociados a muestras existentes
        $matchedBioassays = [
            'arbacia' => \App\Models\ArbaciaFertilization::whereIn('sample',
                SampleEntry::pluck('internal_sample_code')
            )->count(),

            'daphnia' => \App\Models\DaphniaMagnaTemplate::whereIn('sample',
                SampleEntry::pluck('internal_sample_code')
            )->count(),
        ];

        return view('dashboard', compact(
            'totalSamples',
            'byState',
            'byType',
            'monthlySamples',
            'avgPh',
            'avgTemp',
            'avgSalinity',
            'avgConductivity',
            'avgOxygen',
            'bioassayTotals',
            'matchedBioassays'
        ));
    }

    public function index()
    {
        $sampleEntries = SampleEntry::with('template')->latest()->get();
        $newReceptions = \App\Models\ReceptionTemplate::whereDoesntHave('sampleEntry')
            ->with('template')
            ->get();

        return view('sample_entries.index', compact('sampleEntries', 'newReceptions'));
    }

    public function create(Request $request)
    {
        $availableReceptions = \App\Models\ReceptionTemplate::whereDoesntHave('sampleEntry')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'internal_sample_code', 'client', 'matrix', 'assigned_bioassays']);

        $selectedCode = $request->query('code'); // Capturamos ?code=XYZ123

        // Rescatar los bioensayos de la recepción seleccionada
        $assignedBioassays = [];
        if ($selectedCode) {
            $reception = $availableReceptions->firstWhere('internal_sample_code', $selectedCode);
            if ($reception) {
                $assignedBioassays = $reception->assigned_bioassays ?? [];
            }
        }

        return view('sample_entries.create', compact('availableReceptions', 'selectedCode', 'assignedBioassays'));
    }

    /**
     * Store a newly created sample entry.
     * Este método también crea automáticamente los bioensayos asignados.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'code'                   => 'nullable|string|max:50',
            'version'                => 'required|string|min:1',
            'validity'               => 'nullable|string|max:255',
            'received_at'            => 'nullable|date',
            'internal_sample_code'   => [
                'required',
                'string',
                'max:255',
                'exists:reception_templates,internal_sample_code',
            ],
            'sample_type'            => 'nullable|string|max:255',
            'sample_concentration'   => 'nullable|numeric',
            'parameter_reading_date' => 'nullable|date',
            'analyst'                => 'nullable|string|max:255',
            'ph'                     => 'nullable|numeric',
            'salinity'               => 'nullable|numeric',
            'conductivity'           => 'nullable|numeric',
            'dissolved_oxygen'       => 'nullable|numeric',
            'temperature'            => 'nullable|numeric',
            'observations'           => 'nullable|string|max:255',
        ]);

        // Evitar duplicados
        if (SampleEntry::where('internal_sample_code', $validated['internal_sample_code'])->exists()) {
            return back()->withErrors(['internal_sample_code' => 'Esta muestra ya fue ingresada.'])->withInput();
        }

        // Crear plantilla base
        $template = Template::create([
            'title'    => $validated['title'],
            'code'     => $validated['code'] ?? null,
            'version'  => $validated['version'] ?? null,
            'validity' => $validated['validity'] ?? null,
            'type'     => 'sample_entry',
        ]);

        $validated['template_id'] = $template->id;

        // Crear registro de ingreso
        $sampleEntry = SampleEntry::create($validated);

        // Buscar la recepción asociada para obtener los bioensayos asignados
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        if ($reception && is_array($reception->assigned_bioassays)) {
            // Crear los bioensayos asignados
            $this->createAssignedBioassays($reception->assigned_bioassays, $sampleEntry);
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Ingreso de muestra y bioensayos creados correctamente.');
    }

/**
 * Crear los bioensayos asignados para una muestra.
 *
 * @param array $bioassays Lista de bioensayos asignados
 * @param SampleEntry $sampleEntry Registro de ingreso de muestra
 * @return void
 */
    private function createAssignedBioassays(array $bioassays, SampleEntry $sampleEntry): void
    {
        $bioassayConfig = [
            'Daphnia magna Agudo'               => [
                'model'    => \App\Models\DaphniaMagnaTemplate::class,
                'title'    => 'Análisis Bioensayo Agudo - Daphnia magna',
                'code'     => 'RT-01.05',
                'version'  => '03',
                'validity' => '01.09.2023',
            ],
            'Daphnia magna Crónico'             => [
                'model'    => \App\Models\DaphniaMagnaChronic::class,
                'title'    => 'Análisis Bioensayo - Daphnia magna Crónico',
                'code'     => 'RT-02.01',
                'version'  => '03',
                'validity' => '01.10.2025',
            ],
            'Isochrysis galbana'                => [
                'model'    => \App\Models\IsochrysisGalbana::class,
                'title'    => 'Análisis Bioensayo - Isochrysis galbana',
                'code'     => 'RT-01.05',
                'version'  => '03',
                'validity' => '01.10.2025',
            ],
            'Tisbe biconicornis Agua'           => [
                'model'    => \App\Models\TisbeLongicornisWater::class,
                'title'    => 'Análisis Bioensayo - Tisbe longicornis (Agua)',
                'code'     => 'RT-01.05',
                'version'  => '01',
                'validity' => '06.10.2025',
            ],
            'Tisbe biconicornis Sedimento'      => [
                'model'    => \App\Models\TisbeLongicornisRiles::class,
                'title'    => 'Análisis Bioensayo - Tisbe biconicornis (Sedimento)',
                'code'     => 'RT-01.XX',
                'version'  => '01',
                'validity' => '01.01.2025',
            ],
            'Selenastrum capricornutum'         => [
                'model'    => \App\Models\SelenastrumCapricornutum::class,
                'title'    => 'Análisis Bioensayo - Selenastrum capricornutum',
                'code'     => 'RT-01.XX',
                'version'  => '01',
                'validity' => '01.01.2025',
            ],
            'Arbacia spatuligera Fecundación'   => [
                'model'    => \App\Models\ArbaciaFertilization::class,
                'title'    => 'Análisis Bioensayo - Arbacia spatuligera (Fecundación)',
                'code'     => 'RT-01.XX',
                'version'  => '01',
                'validity' => '01.01.2025',
            ],
            'Arbacia spatuligera Estado Larval' => [
                'model'    => \App\Models\ArbaciaLarvalStage::class,
                'title'    => 'Análisis Bioensayo - Arbacia spatuligera (Estado Larval)',
                'code'     => 'RT-01.XX',
                'version'  => '01',
                'validity' => '01.01.2025',
            ],
        ];

        foreach ($bioassays as $bioassay) {
            // Verificar si el bioensayo está configurado
            if (! isset($bioassayConfig[$bioassay])) {
                \Log::info("Bioensayo no implementado: {$bioassay}");
                continue;
            }

            $config = $bioassayConfig[$bioassay];

            // Verificar que no exista ya un registro para esta muestra
            $existingRecord = $config['model']::where('sample', $sampleEntry->internal_sample_code)->first();
            if ($existingRecord) {
                \Log::info("Bioensayo ya existe para muestra {$sampleEntry->internal_sample_code}: {$bioassay}");
                continue;
            }

            // Crear el template del bioensayo
            $template = \App\Models\Template::create([
                'title'    => $config['title'],
                'code'     => $config['code'],
                'version'  => $config['version'],
                'validity' => $config['validity'],
                'type'     => 'bioassay',
            ]);

            // Crear el registro del bioensayo
            $config['model']::create([
                'template_id' => $template->id,
                'sample'      => $sampleEntry->internal_sample_code,
            ]);

            \Log::info("Bioensayo creado: {$bioassay} para muestra {$sampleEntry->internal_sample_code}");
        }
    }

    /* public function show(SampleEntry $sampleEntry)
    {
        // Buscar la recepción asociada a esta muestra por su código interno
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        // Cargar bioensayos asociados (si existen)
        $assignedBioassays = $reception ? ($reception->assigned_bioassays ?? []) : [];

        return view('sample_entries.show', compact('sampleEntry', 'assignedBioassays'));
    } */

    /**
     * Display the specified sample entry.
     */
    public function show(SampleEntry $sampleEntry)
    {
        // Obtener los bioensayos asignados desde la recepción
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        $assignedBioassays = [];
        if ($reception && is_array($reception->assigned_bioassays)) {
            $assignedBioassays = $reception->assigned_bioassays;
        }

        return view('sample_entries.show', compact('sampleEntry', 'assignedBioassays'));
    }

    public function edit(SampleEntry $sampleEntry)
    {
        // Buscar la recepción asociada a esta muestra por su código interno
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        // Cargar bioensayos asociados (si existen)
        $assignedBioassays = $reception ? ($reception->assigned_bioassays ?? []) : [];

        return view('sample_entries.edit', compact('sampleEntry', 'assignedBioassays'));
    }

    public function update(Request $request, SampleEntry $sampleEntry)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'code'     => 'nullable|string|max:50',
            'version'  => 'required|integer|min:1',
            'validity' => 'nullable|string|max:255',
        ]);

        // Actualizar Template
        $sampleEntry->template->update([
            'title'    => $validated['title'],
            'code'     => $validated['code'] ?? null,
            'version'  => $validated['version'],
            'validity' => $validated['validity'] ?? null,
        ]);

        // Actualizar
        $sampleEntry->update($request->except(['title', 'code', 'version', 'validity']));

        return redirect()->route('sample_entries.index')->with('success', 'Plantilla actualizada correctamente.');

    }

    public function destroy(SampleEntry $sampleEntry)
    {
        $sampleEntry->template->delete(); // cascada elimina ingreso de muestra
        return redirect()->route('sample_entries.index')->with('success', 'Ingreso de muestra eliminado correctamente.');
    }
}
