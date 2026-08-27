<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TisbeLongicornisRiles;
use App\Models\SampleEntry;
use App\Models\Template;
use Illuminate\Http\Request;

class TisbeLongicornisRilesController extends Controller
{
    /**
     * Display a listing of the bioassays.
     */
    public function index()
    {
        $bioassays = TisbeLongicornisRiles::orderBy('created_at', 'desc')->paginate(10);
        return view('bioassays.tisbe_longicornis_riles.index', compact('bioassays'));
    }

    /**
     * Show the form for creating a new bioassay.
     */
    public function create()
    {
        return view('bioassays.tisbe_longicornis_riles.create');
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
            'title'    => 'Análisis Bioensayo Agudo - Tisbe longicornis (Sustancias Químicas/Riles)',
            'code'     => 'RT-01.05',
            'version'  => '01',
            'validity' => '06.10.2025',
            'type'     => 'bioassay',
        ]);

        $validated['template_id'] = $template->id;

        $bioassay = TisbeLongicornisRiles::create($validated);

        return redirect()->route('tisbe-longicornis-riles.edit', $bioassay->id)
            ->with('success', 'Bioensayo creado correctamente. Complete los datos.');
    }

    /**
     * Display the specified bioassay.
     */
    public function show(TisbeLongicornisRiles $tisbe_longicornis_riles)
    {
        return view('bioassays.tisbe_longicornis_riles.show', compact('tisbe_longicornis_riles'));
    }

    /**
     * Show the form for editing a bioassay.
     */
    public function edit(TisbeLongicornisRiles $tisbe_longicornis_riles)
    {
        return view('bioassays.tisbe_longicornis_riles.edit', compact('tisbe_longicornis_riles'));
    }

    /**
     * Update the specified bioassay.
     */
    public function update(Request $request, TisbeLongicornisRiles $tisbe_longicornis_riles)
    {
        // ==========================================
        // 1️⃣ VALIDACIÓN
        // ==========================================
        $validated = $request->validate([
            // Datos generales
            'sample'     => 'required|string|max:255',
            'matrix'     => 'nullable|string|max:255',
            'start_time' => 'nullable|date',
            'end_time'   => 'nullable|date',
            'analyst'    => 'nullable|string|max:255',

            // Temporizadores
            'preliminary_timer_start' => 'nullable|string',
            'definitive_timer_start'  => 'nullable|string',

            // Ensayo Preliminar (solo Temperatura y Fecha agua Control)
            'sample_temperature'  => 'nullable|numeric',
            'control_water_date'  => 'nullable|date',

            // Ensayo Definitivo
            'def_start_time'        => 'nullable|date',
            'def_end_time'          => 'nullable|date',
            'def_temperature'       => 'nullable|numeric',
            'def_control_water_date'=> 'nullable|date',

            // Resultados
            'control_immobility' => 'nullable|string|max:255',
            'cl50_24h'           => 'nullable|string|max:255',
            'cl50_48h'           => 'nullable|string|max:255',
            'observations'       => 'nullable|string',
        ]);

        // ==========================================
        // 2️⃣ PREPARAR DATOS GENERALES
        // ==========================================
        $data = [
            'sample'  => $validated['sample'],
            'matrix'  => $validated['matrix'] ?? null,
            'analyst' => $validated['analyst'] ?? null,

            // Temporizadores
            'preliminary_timer_start' => $request->input('preliminary_timer_start'),
            'definitive_timer_start'  => $request->input('definitive_timer_start'),

            // Ensayo Preliminar
            'preliminary_start_at'           => $validated['start_time'] ?? null,
            'preliminary_end_at'             => $validated['end_time'] ?? null,
            'preliminary_sample_temperature' => $validated['sample_temperature'] ?? null,
            'preliminary_control_water_date' => $validated['control_water_date'] ?? null,

            // Ensayo Definitivo
            'definitive_start_at'           => $validated['def_start_time'] ?? null,
            'definitive_end_at'             => $validated['def_end_time'] ?? null,
            'definitive_sample_temperature' => $validated['def_temperature'] ?? null,
            'definitive_control_water_date' => $validated['def_control_water_date'] ?? null,

            // Resultados
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
        // 4️⃣ CONSTRUIR TABLAS DEFINITIVAS 24H Y 48H
        // ==========================================
        $replicas = 4;
        $concentrations = 5;

        foreach (['24', '48'] as $hour) {
            $def_array = [];

            // Valores de concentración
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

            // Totales
            $totals = [
                'is_total'            => true,
                'control_sum'         => $request->input("def_{$hour}h_control_sum"),
                'concentrations_sum'  => [],
            ];
            for ($c = 1; $c <= $concentrations; $c++) {
                $totals['concentrations_sum'][] = $request->input("def_{$hour}h_conc{$c}_sum");
            }
            $def_array[] = $totals;

            $data["definitive_{$hour}h"] = [
                'concentration_values' => $concentration_values,
                'rows'                 => $def_array,
            ];
        }

        // ==========================================
        // 5️⃣ ACTUALIZAR MODELO
        // ==========================================
        $tisbe_longicornis_riles->update($data);

        // ==========================================
        // 6️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = SampleEntry::where('internal_sample_code', $tisbe_longicornis_riles->sample)->first();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo de Tisbe longicornis (Sustancias Químicas/Riles) actualizado correctamente.');
        }

        return redirect()->route('tisbe-longicornis-riles.edit', $tisbe_longicornis_riles->id)
            ->with('warning', 'Bioensayo actualizado, pero no se encontró la muestra asociada.');
    }

    /**
     * Remove the specified bioassay.
     */
    public function destroy(TisbeLongicornisRiles $tisbe_longicornis_riles)
    {
        $sampleEntry = SampleEntry::where('internal_sample_code', $tisbe_longicornis_riles->sample)->first();

        $tisbe_longicornis_riles->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }
}