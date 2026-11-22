<?php

/*
|--------------------------------------------------------------------------
| Hojas de registro oficiales de cada bioensayo
|--------------------------------------------------------------------------
|
| Datos del encabezado de cada hoja de registro del laboratorio (código,
| versión y vigencia), según las copias controladas entregadas por el
| supervisor en noviembre de 2026.
|
| La clave es el nombre del bioensayo tal como se guarda en la recepción
| (ReceptionTemplate::assigned_bioassays). No cambiar las claves sin migrar
| los datos ya guardados.
|
| Si el laboratorio emite una nueva versión de una hoja, basta con
| actualizarla aquí: los bioensayos nuevos tomarán los datos nuevos y los
| ya creados conservan los de la hoja con que se registraron.
|
*/

return [

    'Daphnia magna Agudo' => [
        'model'    => App\Models\DaphniaMagnaTemplate::class,
        'title'    => 'Análisis Bioensayo Agudo - Daphnia magna',
        'code'     => 'RT-01.05',
        'version'  => 3,
        'validity' => '01.09.2023',
    ],

    'Daphnia magna Crónico' => [
        'model'    => App\Models\DaphniaMagnaChronic::class,
        'title'    => 'Análisis Bioensayo Crónico - Daphnia magna',
        'code'     => 'RT-02.01',
        'version'  => 3,
        'validity' => '01.09.2023',
    ],

    'Isochrysis galbana' => [
        'model'    => App\Models\IsochrysisGalbana::class,
        'title'    => 'Análisis Bioensayo Crónico - Isochrysis galbana',
        'code'     => 'RT-03.04',
        'version'  => 5,
        'validity' => '01.10.2023',
    ],

    'Selenastrum capricornutum' => [
        'model'    => App\Models\SelenastrumCapricornutum::class,
        'title'    => 'Análisis Bioensayo Crónico - Selenastrum capricornutum',
        'code'     => 'RT-04.04',
        'version'  => 8,
        'validity' => '01.04.2024',
    ],

    'Tisbe longicornis Aguas marinas' => [
        'model'    => App\Models\TisbeLongicornisWater::class,
        'title'    => 'Análisis Bioensayo Agudo - Tisbe longicornis (Aguas marinas)',
        'code'     => 'RT-05.02',
        'version'  => 6,
        'validity' => '01.10.2026',
    ],

    'Tisbe longicornis Sustancias químicas/Riles' => [
        'model'    => App\Models\TisbeLongicornisRiles::class,
        'title'    => 'Análisis Bioensayo Agudo - Tisbe longicornis (Sustancias químicas/Riles)',
        'code'     => 'RT-05.04',
        'version'  => 2,
        'validity' => '01.10.2023',
    ],

    'Arbacia spatuligera Fecundación' => [
        'model'    => App\Models\ArbaciaFertilization::class,
        'title'    => 'Bioensayo Crónico - Arbacia spatuligera (Fecundación)',
        'code'     => 'RT-06.03',
        'version'  => 6,
        'validity' => '01.10.2023',
    ],

    'Arbacia spatuligera Estado Larval' => [
        'model'    => App\Models\ArbaciaLarvalStage::class,
        'title'    => 'Bioensayo Crónico - Arbacia spatuligera (Estado Larval)',
        'code'     => 'RT-06.05',
        'version'  => 4,
        'validity' => '01.10.2023',
    ],

];
