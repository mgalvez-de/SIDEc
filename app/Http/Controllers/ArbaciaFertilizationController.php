<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ArbaciaFertilization;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class ArbaciaFertilizationController extends Controller
{
    /**
     * Display a listing of the bioassays.
     */
    public function index()
    {
        $bioassays = ArbaciaFertilization::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.arbacia_fertilization.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     */
    public function create()
    {
        return view('bioassays.arbacia_fertilization.create');
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
            'title'    => 'Análisis Bioensayo - Arbacia spatuligera (Fecundación)',
            'code'     => 'RT-01.05',
            'version'  => '01',
            'validity' => '06.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = ArbaciaFertilization::create($validated);

        return redirect()->route('arbacia-fertilization.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay.
     */
    public function show(ArbaciaFertilization $arbacia_fertilization)
    {
        return view('bioassays.arbacia_fertilization.show', compact('arbacia_fertilization'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(ArbaciaFertilization $arbacia_fertilization)
    {
        return view('bioassays.arbacia_fertilization.edit', compact('arbacia_fertilization'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, ArbaciaFertilization $arbacia_fertilization)
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
            'control_fertilization_percentage' => 'nullable|numeric',

            // Temporizador
            'timer_start' => 'nullable|string',

            // Tiempos del ensayo
            'sperm_addition_time' => 'nullable|string',
            'egg_addition_time' => 'nullable|string',
            'fixation_time_end' => 'nullable|string',
            'count_end_datetime' => 'nullable|date',

            // Filas de datos (JSON)
            'rows' => 'nullable|array',

            // Resultados
            'ci50' => 'nullable|string|max:255',
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
            'control_fertilization_percentage' => $validated['control_fertilization_percentage'] ?? null,

            // Temporizador
            'timer_start' => $request->input('timer_start'),

            // Tiempos del ensayo
            'sperm_addition_time' => $validated['sperm_addition_time'] ?? null,
            'egg_addition_time' => $validated['egg_addition_time'] ?? null,
            'fixation_time_end' => $validated['fixation_time_end'] ?? null,
            'count_end_datetime' => $validated['count_end_datetime'] ?? null,

            // Filas de datos (JSON)
            'rows_data' => $validated['rows'] ?? [],

            // Resultados
            'ci50' => $validated['ci50'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ];

        // ==========================================
        // 3️⃣ ACTUALIZAR MODELO
        // ==========================================
        $arbacia_fertilization->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $arbacia_fertilization->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Arbacia spatuligera (Fecundación) actualizado correctamente.');
        }

        return redirect()->route('arbacia-fertilization.edit', $arbacia_fertilization->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(ArbaciaFertilization $arbacia_fertilization)
    {
        $sampleEntry = SampleEntry::where('internal_sample_code', $arbacia_fertilization->sample)->first();

        $arbacia_fertilization->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}