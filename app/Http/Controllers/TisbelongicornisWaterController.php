<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TisbeLongicornisWater;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class TisbeLongicornisWaterController extends Controller
{
    /**
     * Display a listing of the bioassays.
     */
    public function index()
    {
        $bioassays = TisbeLongicornisWater::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.tisbe_longicornis_water.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     */
    public function create()
    {
        return view('bioassays.tisbe_longicornis_water.create');
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

        // Crear template por defecto
        $template = Template::create([
            'title'    => 'Análisis Bioensayo - Tisbe longicornis (Agua)',
            'code'     => 'RT-01.05',
            'version'  => '01',
            'validity' => '06.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = TisbeLongicornisWater::create($validated);

        return redirect()->route('tisbe-longicornis-water.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay.
     */
    public function show(TisbeLongicornisWater $tisbe_longicornis_water)
    {
        return view('bioassays.tisbe_longicornis_water.show', compact('tisbe_longicornis_water'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(TisbeLongicornisWater $tisbe_longicornis_water)
    {
        return view('bioassays.tisbe_longicornis_water.edit', compact('tisbe_longicornis_water'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, TisbeLongicornisWater $tisbe_longicornis_water)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN
        // ==========================================
        $validated = $request->validate([
            // === DATOS GENERALES ===
            'sample'         => 'required|string|max:255',
            'matrix'         => 'nullable|string|max:255',
            'bioassay_start' => 'nullable|date',
            'bioassay_end'   => 'nullable|date',
            'analyst'        => 'nullable|string|max:255',

            // === TEMPORIZADOR ===
            'timer_start' => 'nullable|string',

            // === DATOS PRELIMINARES ===
            'initial_inoculum'   => 'nullable|numeric',
            'stock_culture_date' => 'nullable|date',

            // === DATOS DE MUESTRAS (JSON) ===
            'samples_data' => 'nullable|array',

            // === RESULTADOS ===
            'cl50_24h'     => 'nullable|string|max:255',
            'cl50_48h'     => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'vb'           => 'nullable|string|max:255',
        ]);

        // ==========================================
        // 2️⃣ PREPARAR DATOS
        // ==========================================
        $data = [
            // Datos generales
            'sample'         => $validated['sample'],
            'matrix'         => $validated['matrix'] ?? null,
            'bioassay_start' => $validated['bioassay_start'] ?? null,
            'bioassay_end'   => $validated['bioassay_end'] ?? null,
            'analyst'        => $validated['analyst'] ?? null,

            // Temporizador
            'timer_start' => $request->input('timer_start'),

            // Datos preliminares
            'initial_inoculum'   => $validated['initial_inoculum'] ?? null,
            'stock_culture_date' => $validated['stock_culture_date'] ?? null,

            // Datos de muestras (24 filas con réplicas 24H y 48H)
            'samples_data' => $validated['samples_data'] ?? [],

            // Resultados
            'cl50_24h'     => $validated['cl50_24h'] ?? null,
            'cl50_48h'     => $validated['cl50_48h'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'vb'           => $validated['vb'] ?? null,
        ];

        // ==========================================
        // 3️⃣ ACTUALIZAR MODELO
        // ==========================================
        $tisbe_longicornis_water->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $tisbe_longicornis_water->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Tisbe longicornis (Agua) actualizado correctamente.');
        }

        return redirect()->route('tisbe-longicornis-water.edit', $tisbe_longicornis_water->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(TisbeLongicornisWater $tisbe_longicornis_water)
    {
        $sampleEntry = SampleEntry::where('internal_sample_code', $tisbe_longicornis_water->sample)->first();

        $tisbe_longicornis_water->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}