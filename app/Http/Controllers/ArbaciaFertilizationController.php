<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ArbaciaFertilization;
use Illuminate\Http\Request;

class ArbaciaFertilizationController extends Controller
{
    /**
     * Display the specified bioassay.
     */
    public function show(ArbaciaFertilization $arbacia_fertilization)
    {
        // No hay vista de detalle: el formulario del bioensayo hace de detalle
        return redirect()->route('arbacia-fertilization.edit', $arbacia_fertilization);
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
        $this->authorize('update', $arbacia_fertilization);

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
        $sampleEntry = $arbacia_fertilization->sampleEntry;

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
        $this->authorize('delete', $arbacia_fertilization);
        if (\App\Models\ImageAnalysis::existsForBioassay($arbacia_fertilization)) {
            return back()->with('error', \App\Models\ImageAnalysis::DELETE_BLOCKED_MESSAGE);
        }

        $sampleEntry = $arbacia_fertilization->sampleEntry;

        $arbacia_fertilization->delete();

        if ($sampleEntry) {
            return redirect()->route('sample_entries.show', $sampleEntry->id)
                ->with('success', 'Bioensayo eliminado correctamente.');
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Bioensayo eliminado correctamente.');
    }

    public function validateBioassay(ArbaciaFertilization $arbacia_fertilization)
    {
        $this->authorize('validateBioassay', $arbacia_fertilization);

        $arbacia_fertilization->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Bioensayo marcado como validado.');
    }
}