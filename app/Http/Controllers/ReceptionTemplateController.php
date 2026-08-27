<?php
namespace App\Http\Controllers;

use App\Models\ReceptionTemplate;
use App\Models\Template;
use Illuminate\Http\Request;

class ReceptionTemplateController extends Controller
{
    public function index()
    {
        // Sin paginación - DataTables se encarga del paginado en el frontend
        $receptions = ReceptionTemplate::with('template')
            ->orderBy('created_at', 'desc')
            ->get(); // Cambiado de paginate() a get()

        return view('receptions.index', compact('receptions'));
    }

    public function create()
    {
        return view('receptions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                          => 'required|string|max:255',
            'code'                           => 'nullable|string|max:50',
            'version'                        => 'nullable|string|max:50',
            'validity'                       => 'nullable|string|max:255',

            'thermometer_code'               => 'nullable|string|max:255',
            'correction_factor'              => 'nullable|string|max:255',
            'received_at'                    => 'nullable|date',
            'delivered_by'                   => 'nullable|string|max:255',
            'client'                         => 'nullable|string|max:255',
            'sampled_at'                     => 'nullable|date',
            'received_by'                    => 'nullable|string|max:255',
            'matrix'                         => 'nullable|string|max:255',
            'temperature_received'           => 'nullable|numeric',
            'temperature_corrected'          => 'nullable|numeric',
            'report_number'                  => 'nullable|integer',
            'assigned_bioassays'             => 'nullable|array',

            // Validación para múltiples muestras
            'samples'                        => 'required|array|min:1',
            'samples.*.sample_identifier'    => 'required|string|max:255',
            'samples.*.internal_sample_code' => 'nullable|string|max:255',
        ]);

        /* dd($validated); */

        // === Guardar la plantilla base ===
        $template = Template::create([
            'title'    => $validated['title'],
            'code'     => $validated['code'] ?? null,
            'version'  => $validated['version'] ?? null,
            'validity' => $validated['validity'] ?? null,
            'type'     => 'reception',
        ]);

        // === Datos comunes para todas las muestras ===
        $commonData = [
            'template_id'           => $template->id,
            'thermometer_code'      => $validated['thermometer_code'] ?? null,
            'correction_factor'     => $validated['correction_factor'] ?? null,
            'received_at'           => $validated['received_at'] ?? null,
            'delivered_by'          => $validated['delivered_by'] ?? null,
            'client'                => $validated['client'] ?? null,
            'sampled_at'            => $validated['sampled_at'] ?? null,
            'received_by'           => $validated['received_by'] ?? null,
            'matrix'                => $validated['matrix'] ?? null,
            'temperature_received'  => $validated['temperature_received'] ?? null,
            'temperature_corrected' => $validated['temperature_corrected'] ?? null,
            'report_number'         => $validated['report_number'] ?? null,
            'assigned_bioassays'    => $validated['assigned_bioassays'] ?? [],
        ];

        // === Crear una recepción por cada muestra ===
        $createdReceptions = [];
        foreach ($validated['samples'] as $sample) {
            $receptionData = array_merge($commonData, [
                'sample_identifier'    => $sample['sample_identifier'],
                'internal_sample_code' => $sample['internal_sample_code'] ?? null,
            ]);

            $createdReceptions[] = ReceptionTemplate::create($receptionData);
        }

        $sampleCount = count($createdReceptions);
        $message     = $sampleCount === 1
            ? 'Recepción registrada correctamente.'
            : "Se registraron {$sampleCount} muestras correctamente.";

        return redirect()->route('receptions.index')
            ->with('success', $message);
    }

    public function show(ReceptionTemplate $reception)
    {
        return view('receptions.show', compact('reception'));
    }

    public function edit(ReceptionTemplate $reception)
    {
        return view('receptions.edit', compact('reception'));
    }

    public function update(Request $request, ReceptionTemplate $reception)
    {
        $validated = $request->validate([
            'title'                 => 'required|string|max:255',
            'code'                  => 'nullable|string|max:50',
            'version'               => 'nullable|string|max:50',
            'validity'              => 'nullable|string|max:255',
            'thermometer_code'      => 'nullable|string|max:255',
            'correction_factor'     => 'nullable|string|max:255',
            'received_at'           => 'nullable|date',
            'delivered_by'          => 'nullable|string|max:255',
            'client'                => 'nullable|string|max:255',
            'sampled_at'            => 'nullable|date',
            'received_by'           => 'nullable|string|max:255',
            'sample_identifier'     => 'required|string|max:255',
            'matrix'                => 'nullable|string|max:255',
            'internal_sample_code'  => 'nullable|string|max:255',
            'temperature_received'  => 'nullable|numeric',
            'temperature_corrected' => 'nullable|numeric',
            'report_number'         => 'nullable|integer',
            'assigned_bioassays'    => 'nullable|array',
        ]);

        // Actualizar Template
        $reception->template->update([
            'title'    => $validated['title'],
            'code'     => $validated['code'] ?? null,
            'version'  => $validated['version'] ?? null,
            'validity' => $validated['validity'] ?? null,
        ]);

        // Actualizar ReceptionTemplate
        $reception->update([
            'thermometer_code'      => $validated['thermometer_code'] ?? null,
            'correction_factor'     => $validated['correction_factor'] ?? null,
            'received_at'           => $validated['received_at'] ?? null,
            'delivered_by'          => $validated['delivered_by'] ?? null,
            'client'                => $validated['client'] ?? null,
            'sampled_at'            => $validated['sampled_at'] ?? null,
            'received_by'           => $validated['received_by'] ?? null,
            'sample_identifier'     => $validated['sample_identifier'] ?? null,
            'matrix'                => $validated['matrix'] ?? null,
            'internal_sample_code'  => $validated['internal_sample_code'] ?? null,
            'temperature_received'  => $validated['temperature_received'] ?? null,
            'temperature_corrected' => $validated['temperature_corrected'] ?? null,
            'report_number'         => $validated['report_number'] ?? null,
            'assigned_bioassays'    => $validated['assigned_bioassays'] ?? [],
        ]);

        return redirect()->route('receptions.index')
            ->with('success', 'Recepción actualizada correctamente.');
    }

    public function destroy(ReceptionTemplate $reception)
    {
        $reception->template->delete(); // cascada elimina reception también
        return redirect()->route('receptions.index')->with('success', 'Plantilla eliminada correctamente.');
    }
}
