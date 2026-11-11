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

    // Nombre del campo con la fecha de inicio de la primera etapa
    public static function startField(): string
    {
        return array_values(static::DURATION_PHASES)[0][1];
    }

    // Fecha en que se inició el ensayo (null si todavía no empieza)
    public function startedAt()
    {
        return $this->getAttribute(static::startField());
    }

    // Iniciado: ya tiene la fecha de inicio de su primera etapa (separa "Nuevas" de "En proceso" en los listados)
    public function isStarted(): bool
    {
        return !empty($this->startedAt());
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
