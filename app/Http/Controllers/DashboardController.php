<?php
namespace App\Http\Controllers;

use App\Models\ReceptionTemplate;
use App\Models\RejectionTemplate;
use App\Models\SampleEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Dashboard: estado y avance de las muestras, filtrable por período y por cliente, con el plazo de entrega
 * de 6 semanas. (Une el antiguo Dashboard con la página de Seguimiento.)
 * El Encargado de área lo ve sin datos de clientes ni rechazos.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from'   => 'nullable|date',
            'to'     => 'nullable|date' . ($request->filled('from') ? '|after_or_equal:from' : ''),
            'client' => 'nullable|string|max:255',
        ], [
            'from.date'         => 'La fecha "desde" no es válida.',
            'to.date'           => 'La fecha "hasta" no es válida.',
            'to.after_or_equal' => 'La fecha "hasta" debe ser igual o posterior a la fecha "desde".',
        ]);

        $canSeeClients = $request->user()->hasAnyRole(User::ADMIN_ROLES);
        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : null;
        $client = $canSeeClients ? $request->input('client') : null;

        // -------- Muestras recepcionadas en el período --------
        $receptions = ReceptionTemplate::with('sampleEntry.reception')
            ->when($from, fn ($query) => $query->where('received_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('received_at', '<=', $to))
            ->when($client, fn ($query) => $query->where('client', $client))
            ->orderByDesc('received_at')
            ->get();

        $rejections = RejectionTemplate::all();
        $rejectedCodes = $rejections->pluck('internal_sample_code')->filter();

        $rows = $receptions->map(fn ($reception) => $this->receptionRow($reception, $rejectedCodes));

        // -------- Rechazos que no tienen recepción (solo Supervisor y Jefe de Área) --------
        if ($canSeeClients) {
            $receivedCodes = ReceptionTemplate::pluck('internal_sample_code')->filter();

            $rows = $rows->concat($rejections
                ->reject(fn ($rejection) => $receivedCodes->contains($rejection->internal_sample_code))
                ->filter(fn ($rejection) => (!$from || $rejection->created_at->gte($from))
                    && (!$to || $rejection->created_at->lte($to))
                    && (!$client || $rejection->client === $client))
                ->map(fn ($rejection) => $this->rejectionRow($rejection)));
        }

        // -------- Resumen: bioensayos validados y pendientes, en total y por tipo --------
        $byType = array_fill_keys(array_keys(SampleEntry::BIOASSAY_MODELS), ['validated' => 0, 'pending' => 0]);
        foreach ($rows as $row) {
            foreach ($row['bioassays'] as $name => $status) {
                // "Pendiente" es todo lo que aún no está validado (en curso o sin ingresar)
                $byType[$name][$status === 'validated' ? 'validated' : 'pending']++;
            }
        }
        $byType = array_filter($byType, fn ($counts) => $counts['validated'] + $counts['pending'] > 0);

        $totals = [
            'received'    => $receptions->count(),
            'not_entered' => $rows->where('state', 'Sin ingresar')->count(),
            'in_process'  => $rows->where('state', 'En proceso')->count(),
            'completed'   => $rows->where('state', 'Completada')->count(),
            'rejected'    => $rows->where('state', 'Rechazada')->count(),
            'validated'   => array_sum(array_column($byType, 'validated')),
            'pending'     => array_sum(array_column($byType, 'pending')),
            'warning'     => $rows->where('deadline_status', 'warning')->count(),
            'overdue'     => $rows->where('deadline_status', 'overdue')->count(),
        ];

        // Lista de clientes para el filtro (de las recepciones y de los rechazos)
        $clients = $canSeeClients
            ? ReceptionTemplate::pluck('client')->merge(RejectionTemplate::pluck('client'))->filter()->unique()->sort()->values()
            : collect();

        return view('dashboard', compact('rows', 'byType', 'totals', 'clients', 'canSeeClients'));
    }

    private function receptionRow(ReceptionTemplate $reception, $rejectedCodes): array
    {
        $entry = $reception->sampleEntry;
        $rejected = $rejectedCodes->contains($reception->internal_sample_code);

        if ($rejected) {
            $state = 'Rechazada';
            $bioassays = [];
        } elseif ($entry) {
            $state = $entry->isCompleted() ? 'Completada' : 'En proceso';
            // Al ingresar la muestra se crean sus bioensayos; quedan pendientes hasta que se validan
            $bioassays = $entry->assignedBioassays()
                ->map(fn ($bioassay) => $bioassay && $bioassay->validated_at ? 'validated' : 'pending')
                ->all();
        } else {
            $state = 'Sin ingresar';
            $bioassays = collect($reception->assigned_bioassays ?? [])
                ->filter(fn ($name) => isset(SampleEntry::BIOASSAY_MODELS[$name]))
                ->mapWithKeys(fn ($name) => [$name => 'not_started'])
                ->all();
        }

        return [
            'code'            => $reception->internal_sample_code,
            'client'          => $reception->client,
            'received_at'     => $reception->received_at ? Carbon::parse($reception->received_at) : null,
            'state'           => $state,
            'bioassays'       => $bioassays,
            'entry_id'        => $entry->id ?? null,
            'deadline'        => $rejected ? null : $reception->deliveryDeadline(),
            'deadline_status' => $rejected ? 'rejected' : $reception->deadlineStatus(),
        ];
    }

    private function rejectionRow(RejectionTemplate $rejection): array
    {
        return [
            'code'            => $rejection->internal_sample_code,
            'client'          => $rejection->client,
            'received_at'     => $rejection->created_at,
            'state'           => 'Rechazada',
            'bioassays'       => [],
            'entry_id'        => null,
            'deadline'        => null,
            'deadline_status' => 'rejected',
        ];
    }
}
