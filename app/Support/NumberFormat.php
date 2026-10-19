<?php

namespace App\Support;

/**
 * Muestra números con el formato chileno: coma decimal y punto de miles (1234.5 -> "1.234,5").
 * Es la versión para las pantallas de solo lectura de public/js/decimal-comma.js, que hace lo mismo en los campos.
 */
class NumberFormat
{
    /**
     * @param  mixed     $value     número o texto numérico; cualquier otra cosa se devuelve tal cual
     * @param  int|null  $decimals  cantidad de decimales; null = los mismos que trae el valor
     */
    public static function format($value, ?int $decimals = null)
    {
        if (!is_numeric($value)) {
            return $value; // null, '' o un texto como "No especificado"
        }

        if ($decimals === null) {
            $text = (string) $value;
            if (stripos($text, 'e') !== false) {
                $text = rtrim(rtrim(sprintf('%.10F', (float) $value), '0'), '.'); // "1.0E-7" -> "0.0000001"
            }
            $dot = strpos($text, '.');
            $decimals = $dot === false ? 0 : strlen($text) - $dot - 1;
        }

        return number_format((float) $value, $decimals, ',', '.');
    }
}
