<?php

namespace App\Models\Concerns;

use App\Support\TestDuration;

/**
 * Para los modelos de bioensayo. Cada modelo define DURATION_PHASES:
 *   'etapa' => ['Etiqueta', 'campo de inicio', 'campo de término', minutos exigidos]
 */
trait HasTestDuration
{
    public function durationChecks(): array
    {
        $checks = [];

        foreach (static::DURATION_PHASES as $phase => [$label, $startField, $endField, $minutes]) {
            $checks[$phase] = ['label' => $label]
                + TestDuration::check($this->getAttribute($startField), $this->getAttribute($endField), $minutes);
        }

        return $checks;
    }

    // Iniciado: ya tiene la fecha de inicio de su primera etapa (separa "Nuevas" de "En proceso" en los listados)
    public function isStarted(): bool
    {
        [, $startField] = array_values(static::DURATION_PHASES)[0];

        return !empty($this->getAttribute($startField));
    }

    // Alguna etapa ya tiene inicio y término, pero quedó fuera del margen permitido
    public function hasDurationProblem(): bool
    {
        foreach ($this->durationChecks() as $check) {
            if (in_array($check['status'], ['short', 'long'], true)) {
                return true;
            }
        }

        return false;
    }
}
