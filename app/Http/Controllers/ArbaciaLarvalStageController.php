<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ArbaciaLarvalStage;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class ArbaciaLarvalStageController extends Controller
{
    /**
     * Display a listing of the bioassays.
     */
    public function index()
    {
        $bioassays = ArbaciaLarvalStage::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.arbacia_larval_stage.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     */
    public function create()
    {
        return view('bioassays.arbacia_larval_stage.create');
    }

    /**
     * Store a newly created bioassay.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sample' => 'required|string|max:255',
            'matrix' => 'nullable|string|max:255',
        ]);

        $template = Template::create([
            'title'    => 'Análisis Bioensayo - Arbacia spatuligera (Estado Larval)',
            'code'     => 'RT-01.05',
            'version'  => '01',
            'validity' => '06.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = ArbaciaLarvalStage::create($validated);

        return redirect()->route('arbacia-larval-stage.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay.
     */
    public function show(ArbaciaLarvalStage $arbacia_larval_stage)
    {
        return view('bioassays.arbacia_larval_stage.show', compact('arbacia_larval_stage'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(ArbaciaLarvalStage $arbacia_larval_stage)
    {
        return view('bioassays.arbacia_larval_stage.edit', compact('arbacia_larval_stage'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, ArbaciaLarvalStage $arbacia_larval_stage)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN
        // ==========================================
        $validated = $request->validate([
            // Datos generales
            'sample' => 'required|string|max:255',
            'matrix' => 'nullable|string|max:255',
            'bioassay_start' => 'nullable|date',
            'analyst' => 'nullable|string|max:255',

            // Temporizador
            'timer_start' => 'nullable|string',

            // Tiempos del ensayo
            'fertilization_time' => 'nullable|string',
            'fertilized_eggs_added_at' => 'nullable|string',
            'fixation_time_end' => 'nullable|string',
            'count_end_datetime' => 'nullable|date',

            // Control (JSON)
            'control' => 'nullable|array',

            // Filas de datos (JSON)
            'rows' => 'nullable|array',

            // Resultados
            'ce50' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);

        // ==========================================
        // 2️⃣ PREPARAR DATOS
        // ==========================================
        $data = [
            // Datos generales
            'sample' => $validated['sample'],
            'matrix' => $validated['matrix'] ?? null,
            'bioassay_start' => $validated['bioassay_start'] ?? null,
            'analyst' => $validated['analyst'] ?? null,

            // Temporizador
            'timer_start' => $request->input('timer_start'),

            // Tiempos del ensayo
            'fertilization_time' => $validated['fertilization_time'] ?? null,
            'fertilized_eggs_added_at' => $validated['fertilized_eggs_added_at'] ?? null,
            'fixation_time_end' => $validated['fixation_time_end'] ?? null,
            'count_end_datetime' => $validated['count_end_datetime'] ?? null,

            // Control (JSON)
            'control_data' => $validated['control'] ?? [],

            // Filas de datos (JSON)
            'rows_data' => $validated['rows'] ?? [],

            // Resultados
            'ce50' => $validated['ce50'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ];

        // ==========================================
        // 3️⃣ ACTUALIZAR MODELO
        // ==========================================
        $arbacia_larval_stage->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $arbacia_larval_stage->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Arbacia spatuligera (Estado Larval) actualizado correctamente.');
        }

        return redirect()->route('arbacia-larval-stage.edit', $arbacia_larval_stage->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(ArbaciaLarvalStage $arbacia_larval_stage)
    {
        $sampleEntry = SampleEntry::where('internal_sample_code', $arbacia_larval_stage->sample)->first();

        $arbacia_larval_stage->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}