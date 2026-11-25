<?php

namespace App\Services;

use App\Models\ImageAnalysis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Conteos por réplica a partir de las fotos analizadas con API_larvas, y su copia a la planilla.
 *
 * Una réplica es la suma de sus fotos vigentes: larva = conteo de la clase larva_class de la planilla
 * (ImageAnalysis::SECTIONS) y total = todas las instancias, incluidas las que tocan el borde.
 * Todo se calcula desde los análisis vigentes; no hay contadores que mantener.
 * Solo aplica a las planillas que definen larva_class (hoy, Estado Larval).
 */
class ArbaciaReplicaCounts
{
    private const REPLICA_FIELDS = ['larva', 'total', 'percent'];

    public function target(): int
    {
        return (int) config('larvas_api.replica_target');
    }

    /**
     * Réplicas con fotos vigentes, por "fila|réplica".
     *
     * @return array<string, array{row_key: string, replica: int, larva: int, total: int, percent: float|null, photos: int, target: int, complete: bool}>
     */
    public function totals(Model $bioassay): array
    {
        $config = $this->sheetConfig($bioassay);
        if (! $config) {
            return [];
        }

        $totals = [];
        foreach (ImageAnalysis::forBioassay($bioassay)->current()->get(['row_key', 'replica', 'summary']) as $analysis) {
            $key = "{$analysis->row_key}|{$analysis->replica}";
            $totals[$key] = $totals[$key] ?? $this->emptyReplica((string) $analysis->row_key, (int) $analysis->replica);
            $totals[$key]['larva'] += (int) ($analysis->summary['counts_by_class'][$config['larva_class']] ?? 0);
            $totals[$key]['total'] += (int) ($analysis->summary['total_instances'] ?? 0);
            $totals[$key]['photos']++;
        }

        foreach ($totals as $key => $replica) {
            $totals[$key]['percent'] = $this->percent($replica['larva'], $replica['total']);
            $totals[$key]['complete'] = $replica['total'] >= $replica['target'];
        }

        return $totals;
    }

    // La réplica pedida, aunque no tenga fotos vigentes
    public function replica(array $totals, string $rowKey, int $replica): array
    {
        return $totals["{$rowKey}|{$replica}"] ?? $this->emptyReplica($rowKey, $replica);
    }

    /**
     * Escribe en la planilla los conteos de las réplicas con fotos vigentes, vacía las que tenían
     * resultados de la IA y ya no tienen fotos vigentes, y recalcula promedios e inhibición.
     *
     * @return array los totales con que quedó la planilla (ver totals())
     */
    public function applyToSheet(Model $bioassay): array
    {
        $config = $this->sheetConfig($bioassay);
        if (! $config) {
            return [];
        }

        return DB::transaction(function () use ($bioassay, $config) {
            $locked = $bioassay->newQuery()->whereKey($bioassay->getKey())->lockForUpdate()->firstOrFail();
            $totals = $this->totals($locked);
            $control = $locked->control_data ?? [];
            $rows = $locked->rows_data ?? [];

            // Réplicas que tuvieron algún resultado de la IA (cualquiera sea su estado actual)
            $analyzed = ImageAnalysis::forBioassay($locked)->whereNotNull('summary')
                ->select('row_key', 'replica')->distinct()->get();
            foreach ($analyzed as $analysis) {
                if (! isset($totals["{$analysis->row_key}|{$analysis->replica}"])) {
                    $this->put($control, $rows, (string) $analysis->row_key, (int) $analysis->replica, null, null, null);
                }
            }

            foreach ($totals as $replica) {
                $this->put($control, $rows, $replica['row_key'], $replica['replica'], $replica['larva'], $replica['total'], $replica['percent']);
            }

            [$control, $rows] = $this->recalculate($control, $rows, $config['replicas']);
            $locked->update(['control_data' => $control, 'rows_data' => $rows]); // update(): BioassayRecord registra updated_by

            return $totals;
        });
    }

    /**
     * Impone los conteos de la IA en el $data del formulario de la planilla (claves control_data y
     * rows_data), para que guardar la planilla no los pise, y recalcula promedios e inhibición.
     */
    public function protect(Model $bioassay, array $data): array
    {
        $totals = $this->totals($bioassay);
        if (! $totals) {
            return $data;
        }

        $control = $data['control_data'] ?? [];
        $rows = $data['rows_data'] ?? [];
        foreach ($totals as $replica) {
            $this->put($control, $rows, $replica['row_key'], $replica['replica'], $replica['larva'], $replica['total'], $replica['percent']);
        }

        [$data['control_data'], $data['rows_data']] = $this->recalculate($control, $rows, $this->sheetConfig($bioassay)['replicas']);

        return $data;
    }

    // ===================================================================

    // Configuración de la planilla (ImageAnalysis::SECTIONS), o null si no recibe conteos de la IA
    private function sheetConfig(Model $bioassay): ?array
    {
        foreach (ImageAnalysis::SECTIONS as $config) {
            if ($config['model'] === get_class($bioassay)) {
                return isset($config['larva_class']) ? $config : null;
            }
        }

        return null;
    }

    private function emptyReplica(string $rowKey, int $replica): array
    {
        return [
            'row_key' => $rowKey, 'replica' => $replica, 'larva' => 0, 'total' => 0, 'percent' => null,
            'photos' => 0, 'target' => $this->target(), 'complete' => false,
        ];
    }

    private function percent($larva, $total): ?float
    {
        $total = is_numeric($total) ? (float) $total : 0;

        return $total > 0 ? round((is_numeric($larva) ? (float) $larva : 0) / $total * 100, 2) : null;
    }

    // Fila "control" → control_data; filas numéricas → rows_data[fila]
    private function put(array &$control, array &$rows, string $rowKey, int $replica, $larva, $total, $percent): void
    {
        $values = array_combine(
            array_map(fn ($field) => "r{$replica}_{$field}", self::REPLICA_FIELDS),
            [$larva, $total, $percent]
        );

        if ($rowKey === 'control') {
            $control = array_merge($control, $values);
        } else {
            $rows[$rowKey] = array_merge(is_array($rows[$rowKey] ?? null) ? $rows[$rowKey] : [], $values);
        }
    }

    /**
     * Mismas fórmulas que el JS de la planilla (arbacia_larval_stage/edit.blade.php): el % de cada réplica
     * sale de larva/total; promedio = media de los % disponibles; inhibición = (promControl − promFila) /
     * promControl × 100, vacía si promControl es 0 o la fila no tiene datos. Todo con 2 decimales.
     */
    private function recalculate(array $control, array $rows, int $replicas): array
    {
        $controlAvg = $this->average($control, $replicas);
        $controlAvg = $controlAvg === null ? null : round($controlAvg, 2);
        $control['larval_pluteus_avg'] = $controlAvg;
        $control['total_larva_percent'] = $controlAvg;

        foreach ($rows as $key => $row) {
            if (! is_array($row)) {
                continue;
            }

            $avg = $this->average($row, $replicas);
            $rows[$key]['larval_pluteus_percent'] = $avg === null ? null : round($avg, 2);
            $rows[$key]['inhibition_percent'] = $avg !== null && $controlAvg > 0
                ? round(($controlAvg - $avg) / $controlAvg * 100, 2)
                : null;
        }

        return [$control, $rows];
    }

    private function average(array $values, int $replicas): ?float
    {
        $percents = [];
        for ($r = 1; $r <= $replicas; $r++) {
            $percent = $this->percent($values["r{$r}_larva"] ?? null, $values["r{$r}_total"] ?? null);
            if ($percent !== null) {
                $percents[] = $percent;
            }
        }

        return $percents ? array_sum($percents) / count($percents) : null;
    }
}
