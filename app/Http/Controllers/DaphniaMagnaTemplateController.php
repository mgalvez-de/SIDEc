<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DaphniaMagnaTemplate;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class DaphniaMagnaTemplateController extends Controller
{
    /**
     * Display a listing of the bioassays.
     * Nota: No se usa realmente, se accede desde sample_entries.show
     */
    public function index()
    {
        $bioassays = DaphniaMagnaTemplate::latest()->paginate(10);
        return view('bioassays.daphnia_magna.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     * Nota: Normalmente los bioensayos se crean automáticamente desde SampleEntryController@store
     */
    public function create()
    {
        return view('bioassays.daphnia_magna.create');
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
            'title'    => 'Análisis Bioensayo Agudo - Daphnia magna',
            'code'     => 'RT-01.05',
            'version'  => '03',
            'validity' => '01.09.2023',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = DaphniaMagnaTemplate::create($validated);

        return redirect()->route('daphnia-magna.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(DaphniaMagnaTemplate $daphnia_magna)
    {
        return view('bioassays.daphnia_magna.edit', compact('daphnia_magna'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, DaphniaMagnaTemplate $daphnia_magna)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN DE TODOS LOS CAMPOS
        // ==========================================
        $validated = $request->validate([
            // === DATOS GENERALES ===
            'sample'     => 'required|string|max:255',
            'matrix'     => 'nullable|string|max:255',
            'start_time' => 'nullable|date',
            'end_time'   => 'nullable|date',
            'analyst'    => 'nullable|string|max:255',

            // === TEMPORIZADORES ===
            'preliminary_timer_start' => 'nullable|string',
            'definitive_timer_start'  => 'nullable|string',

            // === ENSAYO PRELIMINAR ===
            'sample_temperature'       => 'nullable|numeric',
            'reconstituted_water_date' => 'nullable|date',
            'sample_ph'                => 'nullable|numeric',

            // === ENSAYO DEFINITIVO (datos generales) ===
            'def_start_time'               => 'nullable|date',
            'def_end_time'                 => 'nullable|date',
            'def_temperature'              => 'nullable|numeric',
            'def_reconstituted_water_date' => 'nullable|date',

            // === RESULTADOS DE ANÁLISIS ===
            'control_immobility' => 'nullable|string|max:255',
            'cl50_24h'           => 'nullable|string|max:255',
            'cl50_48h'           => 'nullable|string|max:255',
            'observations'       => 'nullable|string',
        ]);

        // ==========================================
        // 2️⃣ MAPEAR DATOS GENERALES
        // ==========================================
        $data = [
            'sample'  => $validated['sample'],
            'matrix'  => $validated['matrix'] ?? null,
            'analyst' => $validated['analyst'] ?? null,

            // Temporizadores
            'preliminary_timer_start' => $request->input('preliminary_timer_start'),
            'definitive_timer_start'  => $request->input('definitive_timer_start'),

            // Ensayo Preliminar - datos generales
            'preliminary_start_at'                 => $validated['start_time'] ?? null,
            'preliminary_end_at'                   => $validated['end_time'] ?? null,
            'preliminary_sample_temperature'       => $validated['sample_temperature'] ?? null,
            'preliminary_reconstituted_water_date' => $validated['reconstituted_water_date'] ?? null,
            'preliminary_sample_ph'                => $validated['sample_ph'] ?? null,

            // Ensayo Definitivo - datos generales
            'definitive_start_at'                 => $validated['def_start_time'] ?? null,
            'definitive_end_at'                   => $validated['def_end_time'] ?? null,
            'definitive_sample_temperature'       => $validated['def_temperature'] ?? null,
            'definitive_reconstituted_water_date' => $validated['def_reconstituted_water_date'] ?? null,

            // Resultados de análisis
            'control_immobility' => $validated['control_immobility'] ?? null,
            'cl50_24h'           => $validated['cl50_24h'] ?? null,
            'cl50_48h'           => $validated['cl50_48h'] ?? null,
            'observations'       => $validated['observations'] ?? null,
        ];

        // ==========================================
        // 3️⃣ CONSTRUIR TABLA PRELIMINAR (JSON)
        // ==========================================
        $preliminary_table = [];
        for ($i = 1; $i <= 8; $i++) {
            $preliminary_table[] = [
                'concentration' => $request->input("pre_concentration_row{$i}"),
                '24h_rep1'      => $request->input("pre_24h_rep1_row{$i}"),
                '24h_rep2'      => $request->input("pre_24h_rep2_row{$i}"),
                '24h_sum'       => $request->input("pre_24h_sum_row{$i}"),
                '48h_rep1'      => $request->input("pre_48h_rep1_row{$i}"),
                '48h_rep2'      => $request->input("pre_48h_rep2_row{$i}"),
                '48h_sum'       => $request->input("pre_48h_sum_row{$i}"),
            ];
        }
        $data['preliminary_table'] = $preliminary_table;

        // ==========================================
        // 4️⃣ CONSTRUIR TABLAS DEFINITIVAS 24H Y 48H (JSON)
        // ==========================================
        $replicas = 4;
        $concentrations = 5;

        foreach (['24', '48'] as $hour) {
            $def_array = [];

            // Valores de concentración (headers editables)
            $concentration_values = [];
            for ($c = 1; $c <= $concentrations; $c++) {
                $concentration_values[] = $request->input("def_{$hour}h_conc{$c}_value");
            }

            // Réplicas
            for ($r = 1; $r <= $replicas; $r++) {
                $row = [
                    'replica' => $r,
                    'control' => $request->input("def_{$hour}h_control_rep{$r}"),
                    'concentrations' => [],
                ];
                for ($c = 1; $c <= $concentrations; $c++) {
                    $row['concentrations'][] = $request->input("def_{$hour}h_conc{$c}_rep{$r}");
                }
                $def_array[] = $row;
            }

            // Totales (sumas)
            $totals = [
                'is_total'            => true,
                'control_sum'         => $request->input("def_{$hour}h_control_sum"),
                'concentrations_sum'  => [],
            ];
            for ($c = 1; $c <= $concentrations; $c++) {
                $totals['concentrations_sum'][] = $request->input("def_{$hour}h_conc{$c}_sum");
            }
            $def_array[] = $totals;

            // Guardar valores de concentración junto con los datos
            $data["definitive_{$hour}h"] = [
                'concentration_values' => $concentration_values,
                'rows'                 => $def_array,
            ];
        }

        // ==========================================
        // 5️⃣ ACTUALIZAR EL MODELO
        // ==========================================
        $daphnia_magna->update($data);

        // ==========================================
        // 6️⃣ REDIRIGIR A SAMPLE_ENTRIES.SHOW
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $daphnia_magna->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Daphnia magna actualizado correctamente.');
        }

        // Fallback: si no se encuentra la muestra asociada
        return redirect()->route('daphnia-magna.edit', $daphnia_magna->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Display the specified bioassay (read-only view).
     */
    public function show(DaphniaMagnaTemplate $daphnia_magna)
    {
        return view('bioassays.daphnia_magna.show', compact('daphnia_magna'));
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(DaphniaMagnaTemplate $daphnia_magna)
    {
        // Obtener sample_entry antes de eliminar
        $sampleEntry = SampleEntry::where('internal_sample_code', $daphnia_magna->sample)->first();

        $daphnia_magna->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}