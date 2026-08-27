<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\IsochrysisGalbana;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class IsochrysisGalbanaController extends Controller
{
    /**
     * Display a listing of the bioassays.
     * Nota: No se usa normalmente, se accede desde sample_entries.show
     */
    public function index()
    {
        $bioassays = IsochrysisGalbana::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.isochrysis_galbana.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     * Nota: Los bioensayos se crean automáticamente desde SampleEntryController@store
     */
    public function create()
    {
        return view('bioassays.isochrysis_galbana.create');
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
            'title'    => 'Análisis Bioensayo - Isochrysis galbana',
            'code'     => 'RT-01.05',
            'version'  => '03',
            'validity' => '01.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = IsochrysisGalbana::create($validated);

        return redirect()->route('isochrysis-galbana.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay (read-only view).
     */
    public function show(IsochrysisGalbana $isochrysis_galbana)
    {
        return view('bioassays.isochrysis_galbana.show', compact('isochrysis_galbana'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(IsochrysisGalbana $isochrysis_galbana)
    {
        return view('bioassays.isochrysis_galbana.edit', compact('isochrysis_galbana'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, IsochrysisGalbana $isochrysis_galbana)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN DE TODOS LOS CAMPOS
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
            'initial_inoculum_vol' => 'nullable|numeric',
            'stock_culture_date'   => 'nullable|date',

            // === CRECIMIENTO Y PH (CONTROL) ===
            'rc24h'               => 'nullable|numeric',
            'rc48h'               => 'nullable|numeric',
            'rc72h'               => 'nullable|numeric',
            'rc196h'              => 'nullable|numeric',
            'rc296h'              => 'nullable|numeric',
            'rc396h'              => 'nullable|numeric',
            'rc496h'              => 'nullable|numeric',
            'rc596h'              => 'nullable|numeric',
            'rc696h'              => 'nullable|numeric',
            'ph_initial'          => 'nullable|numeric',
            'ph_final'            => 'nullable|numeric',
            'growth_rate_control' => 'nullable|numeric',

            // === MEDICIONES ===
            'measurements' => 'nullable|array',

            // === RESULTADOS ===
            'ec50_detail'           => 'nullable|string|max:255',
            'variation_coefficient' => 'nullable|numeric',
            'observations'          => 'nullable|string',
        ]);

        // ==========================================
        // 2️⃣ PREPARAR DATOS PARA GUARDAR
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
            'initial_inoculum_vol' => $validated['initial_inoculum_vol'] ?? null,
            'stock_culture_date'   => $validated['stock_culture_date'] ?? null,

            // Crecimiento y pH (Control)
            'rc24h'               => $validated['rc24h'] ?? null,
            'rc48h'               => $validated['rc48h'] ?? null,
            'rc72h'               => $validated['rc72h'] ?? null,
            'rc196h'              => $validated['rc196h'] ?? null,
            'rc296h'              => $validated['rc296h'] ?? null,
            'rc396h'              => $validated['rc396h'] ?? null,
            'rc496h'              => $validated['rc496h'] ?? null,
            'rc596h'              => $validated['rc596h'] ?? null,
            'rc696h'              => $validated['rc696h'] ?? null,
            'ph_initial'          => $validated['ph_initial'] ?? null,
            'ph_final'            => $validated['ph_final'] ?? null,
            'growth_rate_control' => $validated['growth_rate_control'] ?? null,

            // Mediciones (se guarda como JSON)
            'measurements' => $validated['measurements'] ?? [],

            // Resultados
            'ec50_detail'           => $validated['ec50_detail'] ?? null,
            'variation_coefficient' => $validated['variation_coefficient'] ?? null,
            'observations'          => $validated['observations'] ?? null,
        ];

        // ==========================================
        // 3️⃣ ACTUALIZAR EL MODELO
        // ==========================================
        $isochrysis_galbana->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR A SAMPLE_ENTRIES.SHOW
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $isochrysis_galbana->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Isochrysis galbana actualizado correctamente.');
        }

        // Fallback: si no se encuentra la muestra asociada
        return redirect()->route('isochrysis-galbana.edit', $isochrysis_galbana->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(IsochrysisGalbana $isochrysis_galbana)
    {
        // Obtener sample_entry antes de eliminar
        $sampleEntry = SampleEntry::where('internal_sample_code', $isochrysis_galbana->sample)->first();

        $isochrysis_galbana->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}