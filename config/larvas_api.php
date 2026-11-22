<?php

/*
|--------------------------------------------------------------------------
| Integración con API_larvas (análisis de imágenes de Arbacia spatuligera)
|--------------------------------------------------------------------------
| Microservicio FastAPI que segmenta y cuenta larvas en fotografías de microscopio.
*/

return [

    // URL base del servicio (sin "/" final). Ej.: http://host.docker.internal:8000
    'url' => rtrim((string) env('LARVAS_API_URL', 'http://host.docker.internal:8000'), '/'),

    // Clave que se envía en el header X-API-Key (la misma API_KEY de API_larvas). Nunca llega al navegador.
    'key' => env('LARVAS_API_KEY'),

    // Segundos. El timeout total debe ser menor que el proxy_read_timeout del proxy (nginx: 60 s)
    // y que max_execution_time de PHP.
    'timeout' => (int) env('LARVAS_API_TIMEOUT', 45),
    'connect_timeout' => (int) env('LARVAS_API_CONNECT_TIMEOUT', 5),

    // Planillas donde se ofrece el análisis de imágenes (claves de ImageAnalysis::SECTIONS).
    // Fecundación queda fuera: el modelo v1 no detecta huevos fecundados ni no fecundados.
    // Para reactivarla cuando exista un modelo, agregar 'arbacia-fertilization'.
    'enabled_sections' => ['arbacia-larval-stage'],

    // Dónde se guardan las fotografías originales (disco privado, nunca se borran)
    'disk' => 'local',
    'directory' => 'arbacia/analyses',

    // Formatos aceptados al subir (la API también acepta TIFF, pero SIDEc no lo ofrece)
    'mimes' => ['jpg', 'jpeg', 'png', 'bmp'],
    'max_kb' => 20480,

    // Fijadores con que se preparan las muestras antes de fotografiarlas
    'fixatives' => [
        'lugol'     => 'Lugol',
        'formalina' => 'Formalina',
    ],
];
