<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ArbaciaLarvalStage;
use Illuminate\Http\Request;

class ArbaciaLarvalStageController extends Controller
{
    /**
     * Display the specified bioassay.
     */
    public function show(ArbaciaLarvalStage $arbacia_larval_stage)
    {
        // No hay vista de detalle: el formulario del bioensayo hace de detalle
        return redirect()->route('arbacia-larval-stage.edit', $arbacia_larval_stage);
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
        $this->authorize('update', $arbacia_larval_stage);

        // ==========================================
        // 1️⃣ VALIDACIÓN
        // ==========================================
        $validated = $request->validate([
            // Datos generales
            'sample' => 'required|string|max:255',
            'matrix' => 'nullable|string|max:255',
            'bioassay_start' => 'nullable|date',
            'analyst' => 'nullable|string|max:255',


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
        $data = app(\App\Services\ArbaciaReplicaCounts::class)->protect($arbacia_larval_stage, $data); // los conteos de la IA no se pisan
        $arbacia_larval_stage->update($data);

        // ==========================================
        // 4️⃣ REDIRIGIR
        // ==========================================
        $sampleEntry = $arbacia_larval_stage->sampleEntry;

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
        $this->authorize('delete', $arbacia_larval_stage);
        if (\App\Models\ImageAnalysis::existsForBioassay($arbacia_larval_stage)) {
            return back()->with('error', \App\Models\ImageAnalysis::DELETE_BLOCKED_MESSAGE);
        }

        $sampleEntry = $arbacia_larval_stage->sampleEntry;

        $arbacia_larval_stage->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }

    public function validateBioassay(ArbaciaLarvalStage $arbacia_larval_stage)
    {
        $this->authorize('validateBioassay', $arbacia_larval_stage);

        $arbacia_larval_stage->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Bioensayo marcado como validado.');
    }
}