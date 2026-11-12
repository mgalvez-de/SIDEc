<?php

namespace App\Http\Controllers;

use App\Models\ReceptionTemplate;
use App\Models\RejectionTemplate;
use App\Models\SampleEntry;
use App\Support\Navigation;

/**
 * Papelera: lo que se elimina en el sistema no se borra de la base de datos, queda aquí
 * y el Supervisor o el Jefe de Área lo pueden restaurar.
 */
class TrashController extends Controller
{
    public function index()
    {
        $rows = collect($this->types())
            ->flatMap(fn ($type, $key) => $type['model']::onlyTrashed()->get()
                ->map(fn ($record) => (object) [
                    'type'       => $key,
                    'label'      => $type['label'],
                    'id'         => $record->id,
                    'sample'     => $record->{$type['sample']} ?? '—',
                    'deleted_at' => $record->deleted_at,
                ]))
            ->sortByDesc('deleted_at')
            ->values();

        return view('trash.index', compact('rows'));
    }

    public function restore(string $tipo, int $id)
    {
        $type = $this->types()[$tipo] ?? abort(404);
        $record = $type['model']::onlyTrashed()->findOrFail($id);

        // Un bioensayo no se restaura solo si su muestra sigue en la papelera: se restaura la muestra
        if (isset(BioassayController::MODELS[$tipo]) && $record->sample_entry_id
            && SampleEntry::onlyTrashed()->whereKey($record->sample_entry_id)->exists()) {
            return back()->with('error', 'Este bioensayo pertenece a un ingreso de muestra que está en la papelera. Restaura el ingreso y volverá con todos sus bioensayos.');
        }

        $record->restore();

        return back()->with('success', "{$type['label']} restaurado correctamente.");
    }

    // Clave en la URL => [modelo, nombre que se muestra, campo con el código de la muestra]
    private function types(): array
    {
        $types = [
            'receptions'     => ['model' => ReceptionTemplate::class, 'label' => 'Recepción', 'sample' => 'internal_sample_code'],
            'sample_entries' => ['model' => SampleEntry::class, 'label' => 'Ingreso de muestra', 'sample' => 'internal_sample_code'],
            'rejections'     => ['model' => RejectionTemplate::class, 'label' => 'Rechazo', 'sample' => 'internal_sample_code'],
        ];

        foreach (BioassayController::MODELS as $section => $model) {
            $types[$section] = ['model' => $model, 'label' => 'Bioensayo ' . Navigation::BIOASSAYS[$section][0], 'sample' => 'sample'];
        }

        return $types;
    }
}
