<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DaphniaMagnaChronic;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class DaphniaMagnaChronicController extends Controller
{
    /**
     * Display a listing of the bioassays.
     */
    public function index()
    {
        $bioassays = DaphniaMagnaChronic::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.daphnia_magna_chronic.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     */
    public function create()
    {
        return view('bioassays.daphnia_magna_chronic.create');
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
            'title'    => 'Análisis Bioensayo - Daphnia magna Crónico',
            'code'     => 'RT-02.01',
            'version'  => '03',
            'validity' => '01.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = DaphniaMagnaChronic::create($validated);

        return redirect()->route('daphnia-magna-chronic.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay.
     */
    public function show(DaphniaMagnaChronic $daphnia_magna_chronic)
    {
        return view('bioassays.daphnia_magna_chronic.show', compact('daphnia_magna_chronic'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(DaphniaMagnaChronic $daphnia_magna_chronic)
    {
        return view('bioassays.daphnia_magna_chronic.edit', compact('daphnia_magna_chronic'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, DaphniaMagnaChronic $daphnia_magna_chronic)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN
        // ==========================================
        $validated = $request->validate([
            // Datos generales
            'sample' => 'required|string|max:255',
            'matrix' => 'nullable|string|max:255',
            'bioassay_start' => 'nullable|date',
            'bioassay_end' => 'nullable|date',
            'analyst' => 'nullable|string|max:255',

            // Temporizador
            'timer_start' => 'nullable|string',

            // Datos preliminares
            'sample_temperature' => 'nullable|numeric',
            'ph' => 'nullable|numeric',

            // Mantención (JSON)
            'maintenance' => 'nullable|array',

            // Control (JSON)
            'control' => 'nullable|array',
            'control_total_reproduction' => 'nullable|numeric',

            // Concentraciones (JSON)
            'concentrations' => 'nullable|array',

            // Resultados
            'noec' => 'nullable|string|max:255',
            'loec' => 'nullable|string|max:255',
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
            'bioassay_end' => $validated['bioassay_end'] ?? null,
            'analyst' => $validated['analyst'] ?? null,

            // Temporizador
            'timer_start' => $request->input('timer_start'),

            // Datos preliminares
            'sample_temperature' => $validated['sample_temperature'] ?? null,
            'ph' => $validated['ph'] ?? null,

            // Mantención (JSON)
            'maintenance_data' => $validated['maintenance'] ?? [],

            // Control (JSON)
            'control_data' => $validated['control'] ?? [],
            'control_total_reproduction' => $validated['control_total_reproduction'] ?? null,

            // Concentraciones (JSON)
            'concentrations_data' => $validated['concentrations'] ?? [],

            // Resultados
            'noec' => $validated['noec'] ?? null,
            'loec' => $validated['loec'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ];

        // ==========================================
        // 3️⃣ ACTUALIZAR MODELO
        // ==========================================
        $daphnia_magna_chronic->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $daphnia_magna_chronic->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Daphnia magna Crónico actualizado correctamente.');
        }

        return redirect()->route('daphnia-magna-chronic.edit', $daphnia_magna_chronic->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(DaphniaMagnaChronic $daphnia_magna_chronic)
    {
        $sampleEntry = SampleEntry::where('internal_sample_code', $daphnia_magna_chronic->sample)->first();

        $daphnia_magna_chronic->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}