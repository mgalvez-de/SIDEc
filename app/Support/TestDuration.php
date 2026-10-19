<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Revisa si un ensayo duró lo que exige su protocolo, a partir de la fecha y hora de inicio y de término
 * (reemplaza a los temporizadores, que no se podían corregir).
 */
class TestDuration
{
    // Margen acordado con el supervisor: hasta 30 minutos por arriba o por abajo
    public const TOLERANCE_MINUTES = 30;

    /**
     * @return array{status: string, elapsed: int|null, expected: int}
     *         status: 'ok', 'short' (duró menos), 'long' (duró más) o 'incomplete' (falta un dato)
     */
    public static function check($start, $end, int $expectedMinutes): array
    {
        $start = self::parse($start);
        $end = self::parseEnd($end, $start);

        if (! $start || ! $end) {
            return ['status' => 'incomplete', 'elapsed' => null, 'expected' => $expectedMinutes];
        }

        $elapsed = (int) $start->diffInMinutes($end, false);
        $difference = $elapsed - $expectedMinutes;

        if (abs($difference) <= self::TOLERANCE_MINUTES) {
            $status = 'ok';
        } else {
            $status = $difference < 0 ? 'short' : 'long';
        }

        return ['status' => $status, 'elapsed' => $elapsed, 'expected' => $expectedMinutes];
    }

    public static function describe(array $check): string
    {
        $expected = self::format($check['expected']) . ' ± ' . self::TOLERANCE_MINUTES . ' min';

        switch ($check['status']) {
            case 'ok':
                return self::format($check['elapsed']) . ': dentro del margen (' . $expected . ').';
            case 'short':
                return self::format($check['elapsed']) . ': duró menos de lo exigido (' . $expected . ').';
            case 'long':
                return self::format($check['elapsed']) . ': duró más de lo exigido (' . $expected . ').';
            default:
                return 'Ingresa la fecha y hora de inicio y de término para calcular la duración (debe ser ' . $expected . ').';
        }
    }

    public static function format(?int $minutes): string
    {
        if ($minutes === null) {
            return '';
        }

        $sign = $minutes < 0 ? '-' : '';
        $minutes = abs($minutes);
        $days = intdiv($minutes, 1440);
        $hours = intdiv($minutes % 1440, 60);
        $mins = $minutes % 60;

        // Ensayos largos (Daphnia crónico, 21 días) se leen mejor en días
        if ($days >= 7) {
            return $sign . $days . ' d ' . $hours . ' h';
        }

        $totalHours = intdiv($minutes, 60);

        return $sign . ($totalHours > 0 ? $totalHours . ' h ' : '') . $mins . ' min';
    }

    private static function parse($value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // El término puede venir solo como hora (HH:MM), como en Arbacia: se asume el mismo día del inicio,
    // o el día siguiente si la hora es anterior a la de inicio.
    private static function parseEnd($value, ?Carbon $start): ?Carbon
    {
        if ($start && is_string($value) && preg_match('/^\s*(\d{1,2}):(\d{2})\s*$/', $value, $m)) {
            $end = $start->copy()->setTime((int) $m[1], (int) $m[2]);

            return $end->lessThan($start) ? $end->addDay() : $end;
        }

        return self::parse($value);
    }
}
