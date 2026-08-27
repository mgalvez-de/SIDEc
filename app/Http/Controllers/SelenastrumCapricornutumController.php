<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SelenastrumCapricornutum;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class SelenastrumCapricornutumController extends Controller
{
    /**
     * Display a listing of the bioassays.
     */
    public function index()
    {
        $bioassays = SelenastrumCapricornutum::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.selenastrum_capricornutum.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     */
    public function create()
    {
        return view('bioassays.selenastrum_capricornutum.create');
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
            'title'    => 'Análisis Bioensayo - Selenastrum capricornutum',
            'code'     => 'RT-01.05',
            'version'  => '03',
            'validity' => '01.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = SelenastrumCapricornutum::create($validated);

        return redirect()->route('selenastrum-capricornutum.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay.
     */
    public function show(SelenastrumCapricornutum $selenastrum_capricornutum)
    {
        return view('bioassays.selenastrum_capricornutum.show', compact('selenastrum_capricornutum'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(SelenastrumCapricornutum $selenastrum_capricornutum)
    {
        return view('bioassays.selenastrum_capricornutum.edit', compact('selenastrum_capricornutum'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, SelenastrumCapricornutum $selenastrum_capricornutum)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN
        // ==========================================
        $validated = $request->validate([
            // Datos generales
            'sample'         => 'required|string|max:255',
            'matrix'         => 'nullable|string|max:255',
            'bioassay_start' => 'nullable|date',
            'bioassay_end'   => 'nullable|date',
            'analyst'        => 'nullable|string|max:255',

            // Temporizador
            'timer_start' => 'nullable|string',

            // Datos preliminares
            'initial_inoculum'   => 'nullable|numeric',
            'stock_culture_date' => 'nullable|date',

            // Crecimiento y pH (Control)
            'rc24h'               => 'nullable|numeric',
            'rc48h'               => 'nullable|numeric',
            'rc72h'               => 'nullable|numeric',
            'rc196h'              => 'nullable|numeric',
            'rc296h'              => 'nullable|numeric',
            'rc396h'              => 'nullable|numeric',
            'rc496h'              => 'nullable|numeric',
            'ph_initial'          => 'nullable|numeric',
            'ph_final'            => 'nullable|numeric',
            'control_growth_rate' => 'nullable|numeric',

            // Mediciones (JSON)
            'measurements' => 'nullable|array',

            // Resultados
            'ce50_detail'           => 'nullable|string|max:255',
            'variation_coefficient' => 'nullable|numeric',
            'observations'          => 'nullable|string',
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

            // Crecimiento y pH (Control)
            'rc24h'               => $validated['rc24h'] ?? null,
            'rc48h'               => $validated['rc48h'] ?? null,
            'rc72h'               => $validated['rc72h'] ?? null,
            'rc196h'              => $validated['rc196h'] ?? null,
            'rc296h'              => $validated['rc296h'] ?? null,
            'rc396h'              => $validated['rc396h'] ?? null,
            'rc496h'              => $validated['rc496h'] ?? null,
            'ph_initial'          => $validated['ph_initial'] ?? null,
            'ph_final'            => $validated['ph_final'] ?? null,
            'control_growth_rate' => $validated['control_growth_rate'] ?? null,

            // Mediciones (JSON)
            'measurements' => $validated['measurements'] ?? [],

            // Resultados
            'ce50_detail'           => $validated['ce50_detail'] ?? null,
            'variation_coefficient' => $validated['variation_coefficient'] ?? null,
            'observations'          => $validated['observations'] ?? null,
        ];

        // ==========================================
        // 3️⃣ ACTUALIZAR MODELO
        // ==========================================
        $selenastrum_capricornutum->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $selenastrum_capricornutum->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Selenastrum capricornutum actualizado correctamente.');
        }

        return redirect()->route('selenastrum-capricornutum.edit', $selenastrum_capricornutum->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(SelenastrumCapricornutum $selenastrum_capricornutum)
    {
        $sampleEntry = SampleEntry::where('internal_sample_code', $selenastrum_capricornutum->sample)->first();

        $selenastrum_capricornutum->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}