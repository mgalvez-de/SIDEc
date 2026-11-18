<?php
namespace App\Http\Controllers;

use App\Models\SampleEntry;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SampleEntryController extends Controller
{
    public function index()
    {
        [$completedEntries, $inProcessEntries] = SampleEntry::with('reception')->latest()->get()
            ->partition(fn ($entry) => $entry->isCompleted());
        $newReceptions = \App\Models\ReceptionTemplate::whereDoesntHave('sampleEntry')
            ->with('template')
            ->get();

        return view('sample_entries.index', compact('inProcessEntries', 'completedEntries', 'newReceptions'));
    }

    public function create(Request $request)
    {
        $availableReceptions = \App\Models\ReceptionTemplate::whereDoesntHave('sampleEntry')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'internal_sample_code', 'client', 'matrix', 'assigned_bioassays']);

        $selectedCode = $request->query('code'); // Capturamos ?code=XYZ123

        // Rescatar los bioensayos de la recepción seleccionada
        $assignedBioassays = [];
        if ($selectedCode) {
            $reception = $availableReceptions->firstWhere('internal_sample_code', $selectedCode);
            if ($reception) {
                $assignedBioassays = $reception->assigned_bioassays ?? [];
            }
        }

        return view('sample_entries.create', compact('availableReceptions', 'selectedCode', 'assignedBioassays'));
    }

    /**
     * Store a newly created sample entry.
     * Este método también crea automáticamente los bioensayos asignados.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules() + [
            'internal_sample_code' => [
                'required',
                'string',
                'max:255',
                'exists:reception_templates,internal_sample_code',
            ],
        ]);

        // Evitar duplicados
        if (SampleEntry::where('internal_sample_code', $validated['internal_sample_code'])->exists()) {
            return back()->withErrors(['internal_sample_code' => 'Esta muestra ya fue ingresada.'])->withInput();
        }

        // Crear plantilla base
        $template = Template::create([
            'title' => $validated['title'],
            'code' => $validated['code'] ?? null,
            'version' => $validated['version'] ?? null,
            'validity' => $validated['validity'] ?? null,
            'type' => 'sample_entry',
        ]);

        $validated['template_id'] = $template->id;

        // Crear registro de ingreso
        $sampleEntry = SampleEntry::create($validated);

        // Buscar la recepción asociada para obtener los bioensayos asignados
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        if ($reception && is_array($reception->assigned_bioassays)) {
            // Crear los bioensayos asignados
            $this->createAssignedBioassays($reception->assigned_bioassays, $sampleEntry);
        }

        return redirect()->route('sample_entries.index')
            ->with('success', 'Ingreso de muestra y bioensayos creados correctamente.');
    }

    /**
     * Crear los bioensayos asignados para una muestra.
     *
     * @param array $bioassays Lista de bioensayos asignados
     * @param SampleEntry $sampleEntry Registro de ingreso de muestra
     * @return void
     */
    private function createAssignedBioassays(array $bioassays, SampleEntry $sampleEntry): void
    {
        // Código, versión y vigencia de cada hoja oficial: config/bioensayos.php
        $bioassayConfig = config('bioensayos');

        foreach ($bioassays as $bioassay) {
            // Un nombre que no está en config/bioensayos.php no tiene formulario: se avisa en el log
            if (!isset($bioassayConfig[$bioassay])) {
                \Log::warning("Bioensayo no implementado: {$bioassay}");
                continue;
            }

            $config = $bioassayConfig[$bioassay];

            // No duplicar si esta muestra ya tiene ese bioensayo
            if ($config['model']::where('sample', $sampleEntry->internal_sample_code)->exists()) {
                continue;
            }

            // Crear el template del bioensayo
            $template = \App\Models\Template::create([
                'title' => $config['title'],
                'code' => $config['code'],
                'version' => $config['version'],
                'validity' => $config['validity'],
                'type' => 'bioassay',
            ]);

            // Crear el registro del bioensayo
            $config['model']::create([
                'template_id' => $template->id,
                'sample' => $sampleEntry->internal_sample_code,
                'sample_entry_id' => $sampleEntry->id,
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function show(SampleEntry $sampleEntry)
    {
        // Obtener los bioensayos asignados desde la recepción
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        $assignedBioassays = [];
        if ($reception && is_array($reception->assigned_bioassays)) {
            $assignedBioassays = $reception->assigned_bioassays;
        }

        return view('sample_entries.show', compact('sampleEntry', 'assignedBioassays'));
    }

    public function edit(SampleEntry $sampleEntry)
    {
        // Buscar la recepción asociada a esta muestra por su código interno
        $reception = \App\Models\ReceptionTemplate::where('internal_sample_code', $sampleEntry->internal_sample_code)->first();

        // Cargar bioensayos asociados (si existen)
        $assignedBioassays = $reception ? ($reception->assigned_bioassays ?? []) : [];

        return view('sample_entries.edit', compact('sampleEntry', 'assignedBioassays'));
    }

    public function update(Request $request, SampleEntry $sampleEntry)
    {
        // Mismas reglas que al crear. El código interno no se puede cambiar: es lo que une
        // el ingreso con su recepción y con sus bioensayos.
        $validated = $request->validate($this->rules());

        $templateFields = ['title', 'code', 'version', 'validity'];
        $sampleEntry->template->update(Arr::only($validated, $templateFields));
        $sampleEntry->update(Arr::except($validated, $templateFields));

        return redirect()->route('sample_entries.index')->with('success', 'Ingreso de muestra actualizado correctamente.');
    }

    public function destroy(SampleEntry $sampleEntry)
    {
        abort_unless(auth()->user()->hasAnyRole(User::ADMIN_ROLES), 403,
            'Solo el Supervisor o el Jefe de Área pueden eliminar un ingreso de muestra.');

        if ($sampleEntry->hasValidatedBioassays()) {
            return back()->with('error', 'No se puede eliminar: la muestra tiene bioensayos validados. Primero hay que reabrirlos.');
        }

        // Primero sus bioensayos (no se deja a la cascada de la base de datos, que no todas aplican)
        // y después su plantilla, que borra en cascada el ingreso
        foreach (SampleEntry::BIOASSAY_MODELS as $model) {
            $model::where('sample_entry_id', $sampleEntry->id)->delete();
        }
        $sampleEntry->template->delete();

        return redirect()->route('sample_entries.index')
            ->with('success', 'Ingreso de muestra eliminado correctamente.');
    }

    // Campos del formulario de ingreso (crear y corregir)
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'version' => 'required|string|max:50',
            'validity' => 'nullable|string|max:255',
            'received_at' => 'nullable|date',
            'sample_type' => 'nullable|string|max:255',
            'sample_concentration' => 'nullable|numeric',
            'parameter_reading_date' => 'nullable|date',
            'analyst' => 'nullable|string|max:255',
            'ph' => 'nullable|numeric',
            'salinity' => 'nullable|numeric',
            'conductivity' => 'nullable|numeric',
            'dissolved_oxygen' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'observations' => 'nullable|string|max:255',
        ];
    }
}
