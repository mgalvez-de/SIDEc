<?php
namespace App\Http\Controllers;

use App\Models\RejectionTemplate;
use Illuminate\Http\Request;
use App\Models\Template;

class RejectionTemplateController extends Controller
{
    public function index()
    {
        $rejections = RejectionTemplate::with('template')->latest()->paginate(10);
        return view('rejections.index', compact('rejections'));
    }

    public function create()
    {
        return view('rejections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'code'     => 'nullable|string|max:50',
            'version'  => 'required|integer|min:1',
            'validity' => 'nullable|string|max:255',

            'internal_sample_code'  => 'nullable|string|max:255',
            'sample_identifier'     => 'nullable|string|max:255',
            'reason_for_rejection'  => 'nullable|string|max:255',
            'who_rejects'           => 'nullable|string|max:255',
            'who_informs_the_client'=> 'nullable|string|max:255',
            'customer_instructions' => 'nullable|string|max:255',
            'observations'          => 'nullable|string|max:300'
        ]);

        // Crear plantilla base
        $template = Template::create([
            'title'    => $validated['title'],
            'code'     => $validated['code'] ?? null,
            'version'  => $validated['version'],
            'validity' => $validated['validity'] ?? null,
            'type'     => 'rejection',
        ]);

        // Agregar relación
        $validated['template_id'] = $template->id;
        RejectionTemplate::create($validated);

        return redirect()->route('rejections.index')->with('success', 'Plantilla creada correctamente.');
    }

    public function show(RejectionTemplate $rejection)
    {
        return view('rejections.show', compact('rejection'));
    }

    public function edit(RejectionTemplate $rejection)
    {
        return view('rejections.edit', compact('rejection'));
    }

    public function update(Request $request, RejectionTemplate $rejection)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'code'     => 'nullable|string|max:50',
            'version'  => 'required|string|min:1',
            'validity' => 'nullable|string|max:255',
        ]);

        // Actualizar Template
        $rejection->template->update([
            'title'    => $validated['title'],
            'code'     => $validated['code'] ?? null,
            'version'  => $validated['version'],
            'validity' => $validated['validity'] ?? null,
        ]);

        // Actualizar RejectionTemplate
        $rejection->update($request->except(['title', 'code', 'version', 'validity']));

        return redirect()->route('rejections.index')->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(RejectionTemplate $rejection)
    {
        $rejection->template->delete(); // cascada elimina rejection
        return redirect()->route('rejections.index')->with('success', 'Plantilla eliminada correctamente.');
    }
}
