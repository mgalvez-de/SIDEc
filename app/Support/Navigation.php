<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Secciones del sistema en un solo lugar: las usan el menú lateral, la barra de ubicación
 * ("Inicio › Bioensayos › Daphnia magna (agudo) › Editar") y el título de la pestaña del navegador.
 */
class Navigation
{
    // Prefijo del nombre de la ruta => nombre que se muestra
    public const SECTIONS = [
        'dashboard'      => 'Dashboard',
        'receptions'     => 'Recepciones',
        'sample_entries' => 'Ingreso de muestras',
        'rejections'     => 'Rechazos',
        'bioassays'      => 'Bioensayos',
        'account'        => 'Cambiar mi contraseña',
    ];

    // Prefijo de la ruta (y de la URL) => [nombre, ícono de Bootstrap Icons]
    public const BIOASSAYS = [
        'daphnia-magna'             => ['Daphnia magna (agudo)', 'bi-droplet'],
        'daphnia-magna-chronic'     => ['Daphnia magna (crónico)', 'bi-droplet-half'],
        'isochrysis-galbana'        => ['Isochrysis galbana (crónico)', 'bi-water'],
        'selenastrum-capricornutum' => ['Selenastrum capricornutum', 'bi-flower3'],
        'tisbe-longicornis-water'   => ['Tisbe longicornis aguas marinas', 'bi-bug'],
        'tisbe-longicornis-riles'   => ['Tisbe longicornis sustancias químicas / Riles', 'bi-bezier2'],
        'arbacia-larval-stage'      => ['Arbacia spatuligera estado larval', 'bi-egg'],
        'arbacia-fertilization'     => ['Arbacia spatuligera fecundación', 'bi-egg-fried'],
    ];

    private const ACTIONS = ['create' => 'Nuevo', 'edit' => 'Editar', 'show' => 'Detalle'];

    /**
     * Pasos de la barra de ubicación para la ruta actual: [['label' => ..., 'url' => ... o null], ...]
     * Ej.: daphnia-magna-chronic.edit => Bioensayos › Daphnia magna (crónico) › Editar
     */
    public static function breadcrumb(?string $routeName): array
    {
        if (!$routeName) {
            return [];
        }

        $section = Str::beforeLast($routeName, '.');   // "daphnia-magna-chronic"
        $action = Str::contains($routeName, '.') ? Str::afterLast($routeName, '.') : 'index';

        if (isset(self::BIOASSAYS[$section])) {
            $steps = [['label' => 'Bioensayos', 'url' => route('bioassays.index')]];
            $label = self::BIOASSAYS[$section][0];
        } elseif (isset(self::SECTIONS[$section])) {
            $steps = [];
            $label = self::SECTIONS[$section];
        } else {
            return [];
        }

        // La sección es un enlace a su listado, salvo cuando ya estás en el listado
        $hasList = $action !== 'index' && Route::has("$section.index");
        $steps[] = ['label' => $label, 'url' => $hasList ? route("$section.index") : null];

        if (isset(self::ACTIONS[$action])) {
            $steps[] = ['label' => self::ACTIONS[$action], 'url' => null];
        }

        return $steps;
    }

    // Título de la pestaña: "Daphnia magna (crónico) › Editar · SIDEc"
    public static function pageTitle(?string $routeName): string
    {
        $labels = collect(self::breadcrumb($routeName))->pluck('label');

        // En las páginas de un bioensayo no hace falta repetir "Bioensayos" (sí en el listado)
        if ($labels->count() > 1 && $labels->first() === 'Bioensayos') {
            $labels->shift();
        }

        return $labels->isEmpty() ? 'SIDEc' : $labels->implode(' › ') . ' · SIDEc';
    }

    // ¿La página actual es de algún bioensayo? (para marcar "Bioensayos" en el menú)
    public static function inBioassays(): bool
    {
        return request()->is('bioensayos')
            || collect(array_keys(self::BIOASSAYS))->contains(fn ($section) => request()->is($section, "$section/*"));
    }
}
