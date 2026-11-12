<?php

namespace Database\Seeders;

use App\Models\ArbaciaFertilization;
use App\Models\ArbaciaLarvalStage;
use App\Models\DaphniaMagnaChronic;
use App\Models\DaphniaMagnaTemplate;
use App\Models\IsochrysisGalbana;
use App\Models\ReceptionTemplate;
use App\Models\RejectionTemplate;
use App\Models\SampleEntry;
use App\Models\SelenastrumCapricornutum;
use App\Models\Template;
use App\Models\TisbeLongicornisRiles;
use App\Models\TisbeLongicornisWater;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Borra TODOS los datos del laboratorio (recepciones, rechazos, ingresos y bioensayos) y carga un set de demostración.
 * Uso: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    // Nombre en assigned_bioassays => [modelo, título, código, versión, vigencia, método que genera los datos]
    private const BIOASSAYS = [
        'Daphnia magna Agudo'               => [DaphniaMagnaTemplate::class, 'Análisis Bioensayo Agudo - Daphnia magna', 'RT-01.05', 3, '01.09.2023', 'daphniaAgudo'],
        'Daphnia magna Crónico'             => [DaphniaMagnaChronic::class, 'Análisis Bioensayo - Daphnia magna Crónico', 'RT-02.01', 3, '01.10.2025', 'daphniaCronico'],
        'Isochrysis galbana'                => [IsochrysisGalbana::class, 'Análisis Bioensayo - Isochrysis galbana', 'RT-01.05', 3, '01.10.2025', 'isochrysis'],
        'Selenastrum capricornutum'         => [SelenastrumCapricornutum::class, 'Análisis Bioensayo - Selenastrum capricornutum', 'RT-01.XX', 1, '01.01.2025', 'selenastrum'],
        'Tisbe biconicornis Agua'           => [TisbeLongicornisWater::class, 'Análisis Bioensayo - Tisbe longicornis (Agua)', 'RT-01.05', 1, '06.10.2025', 'tisbeAgua'],
        'Tisbe biconicornis Sedimento'      => [TisbeLongicornisRiles::class, 'Análisis Bioensayo - Tisbe biconicornis (Sedimento)', 'RT-01.XX', 1, '01.01.2025', 'tisbeRiles'],
        'Arbacia spatuligera Fecundación'   => [ArbaciaFertilization::class, 'Análisis Bioensayo - Arbacia spatuligera (Fecundación)', 'RT-01.XX', 1, '01.01.2025', 'arbaciaFecundacion'],
        'Arbacia spatuligera Estado Larval' => [ArbaciaLarvalStage::class, 'Análisis Bioensayo - Arbacia spatuligera (Estado Larval)', 'RT-01.XX', 1, '01.01.2025', 'arbaciaLarval'],
    ];

    private const MATRICES = [
        'ASP' => 'ASP (Agua Superficial)',
        'AM'  => 'AM (Agua de Mar)',
        'AR'  => 'AR (Agua Residual)',
        'SM'  => 'SM (Sedimento Marino)',
    ];

    // Concentraciones (% v/v) de los ensayos definitivos
    private const SERIE = [6.25, 12.5, 25, 50, 100];

    private $analistId;
    private $managerId;
    private $now;
    private $reportNumber = 1200;

    public function run()
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('DemoDataSeeder borra los datos del laboratorio: no se ejecuta en producción.');
        }

        mt_srand(2026);
        $this->call(RolesAndUsersSeeder::class);
        $this->analistId = User::where('email', 'analist@a')->value('id');
        $this->managerId = User::where('email', 'manager@a')->value('id');
        $this->now = Carbon::now()->second(0);

        $this->clearLabData();

        foreach ($this->samples() as $sample) {
            isset($sample['rejection']) ? $this->createRejection($sample) : $this->createSample($sample);
        }
    }

    private function samples(): array
    {
        return [
            [
                'code' => 'M-2026-001', 'received' => $this->ago(84, '09:40'), 'client' => 'Pesquera Costa Sur S.A.',
                'delivered_by' => 'Luis Herrera', 'received_by' => 'Andrea Muñoz', 'matrix' => 'AR', 'temp' => 3.8,
                'identifier' => 'RIL planta de proceso, descarga final',
                'entry' => ['at' => $this->ago(83, '10:15'), 'analyst' => 'Camila Rojas', 'observations' => 'Muestra homogénea, sin sólidos en suspensión visibles.'],
                'bioassays' => [
                    'Daphnia magna Agudo'       => ['stage' => 'validated', 'ec50' => 42, 'ec50_24' => 78, 'control_dead' => 1],
                    'Selenastrum capricornutum' => ['stage' => 'validated', 'ec50' => 31],
                ],
            ],
            [
                'code' => 'M-2026-002', 'received' => $this->ago(71, '10:05'), 'client' => 'Consultora Ambiental Marea Ltda.',
                'delivered_by' => 'Marcela Vidal', 'received_by' => 'Felipe Carrasco', 'matrix' => 'AM', 'temp' => 4.4,
                'identifier' => 'Agua de mar, estación E-3 de la bahía',
                'entry' => ['at' => $this->ago(71, '15:30'), 'analyst' => 'Diego Fuentes', 'observations' => 'Salinidad adecuada para especies marinas, no requiere ajuste.'],
                'bioassays' => [
                    'Tisbe biconicornis Agua'         => ['stage' => 'validated', 'ec50' => null, 'ec50_24' => null],
                    'Arbacia spatuligera Fecundación' => ['stage' => 'validated', 'ec50' => null],
                ],
            ],
            [
                'code' => 'M-2026-003', 'received' => $this->ago(64, '11:20'), 'client' => 'Química Industrial Andes S.A.', 'identifier' => 'Efluente planta de tratamiento, salida clorada',
                'rejection' => [
                    'reason' => 'Temperatura fuera de rango', 'by' => 'Andrea Muñoz',
                    'instructions' => 'Tomar una nueva muestra y enviarla refrigerada (≤ 6 °C) dentro de las 24 h siguientes al muestreo.',
                    'observations' => 'Temperatura medida al recibir: 14,6 °C. La muestra llegó sin unidades refrigerantes.',
                ],
            ],
            [
                'code' => 'M-2026-004', 'received' => $this->ago(57, '09:15'), 'client' => 'Acuícola Mar Austral SpA',
                'delivered_by' => 'Jorge Castillo', 'received_by' => 'Andrea Muñoz', 'matrix' => 'AM', 'temp' => 3.1,
                'identifier' => 'Agua de mar, centro de cultivo, jaula 4',
                'entry' => ['at' => $this->ago(57, '12:40'), 'analyst' => 'Valentina Soto', 'observations' => 'Leve turbidez; se dejó decantar 30 min antes de preparar diluciones.'],
                'bioassays' => [
                    'Isochrysis galbana'                => ['stage' => 'validated', 'ec50' => 65],
                    'Arbacia spatuligera Estado Larval' => ['stage' => 'complete', 'ec50' => 48], // sin validar: plazo vencido (rojo)
                ],
            ],
            [
                'code' => 'M-2026-005', 'received' => $this->ago(46, '10:50'), 'client' => 'Minera Los Robles SpA',
                'delivered_by' => 'Rodrigo Pino', 'received_by' => 'Felipe Carrasco', 'matrix' => 'ASP', 'temp' => 4.9,
                'identifier' => 'Agua superficial, estero aguas abajo del tranque de relaves',
                'entry' => ['at' => $this->ago(46, '14:10'), 'analyst' => 'Camila Rojas', 'observations' => 'Muestra de color levemente anaranjado; sin olor.'],
                'bioassays' => [
                    'Daphnia magna Crónico' => ['stage' => 'validated', 'factors' => [1.0, 0.96, 0.76, 0.48, 0.15]],
                ],
            ],
            [
                'code' => 'M-2026-006', 'received' => $this->ago(38, '16:10'), 'client' => 'Consultora Ambiental Marea Ltda.', 'identifier' => 'Agua de mar, muelle de descarga',
                'rejection' => [
                    'reason' => 'Volumen insuficiente', 'by' => 'Felipe Carrasco',
                    'instructions' => 'Enviar al menos 2 L de muestra para completar los bioensayos solicitados.',
                    'observations' => 'Se recibieron aproximadamente 500 mL; los ensayos solicitados requieren 2 L.',
                ],
            ],
            [
                'code' => 'M-2026-007', 'received' => $this->ago(31, '09:30'), 'client' => 'Química Industrial Andes S.A.',
                'delivered_by' => 'Paula Contreras', 'received_by' => 'Andrea Muñoz', 'matrix' => 'SM', 'temp' => 2.7,
                'identifier' => 'Sedimento marino, punto de descarga del emisario',
                'entry' => ['at' => $this->ago(31, '13:00'), 'analyst' => 'Diego Fuentes', 'observations' => 'Ensayo sobre elutriado 1:4 (sedimento:agua de mar).'],
                'bioassays' => [
                    'Tisbe biconicornis Sedimento' => ['stage' => 'complete', 'ec50' => 18, 'ec50_24' => 34, 'control_dead' => 0], // sin validar: semana 5 (amarillo)
                ],
            ],
            [
                'code' => 'M-2026-008', 'received' => $this->ago(19, '10:00'), 'client' => 'Consultora Ambiental Marea Ltda.',
                'delivered_by' => 'Marcela Vidal', 'received_by' => 'Felipe Carrasco', 'matrix' => 'ASP', 'temp' => 4.2,
                'identifier' => 'Agua superficial, río estación R-2',
                'entry' => ['at' => $this->ago(18, '09:45'), 'analyst' => 'Valentina Soto', 'observations' => 'Muestra transparente, sin sólidos visibles.'],
                'bioassays' => [
                    'Daphnia magna Agudo'   => ['stage' => 'validated', 'ec50' => null, 'ec50_24' => null, 'control_dead' => 0],
                    'Daphnia magna Crónico' => ['stage' => 'running', 'day' => 15, 'factors' => [1.0, 1.0, 0.98, 0.96, 0.92]],
                ],
            ],
            [
                'code' => 'M-2026-009', 'received' => $this->ago(11, '11:25'), 'client' => 'Acuícola Mar Austral SpA',
                'delivered_by' => 'Jorge Castillo', 'received_by' => 'Andrea Muñoz', 'matrix' => 'AM', 'temp' => 3.6,
                'identifier' => 'Agua de mar, toma de agua del hatchery',
                'entry' => ['at' => $this->ago(10, '09:20'), 'analyst' => 'Diego Fuentes', 'observations' => 'Sin observaciones.'],
                'bioassays' => [
                    'Tisbe biconicornis Agua' => ['stage' => 'validated', 'ec50' => 72, 'ec50_24' => null, 'control_dead' => 1],
                    'Isochrysis galbana'      => ['stage' => 'complete', 'ec50' => 55],
                ],
            ],
            [
                'code' => 'M-2026-010', 'received' => $this->ago(6, '15:40'), 'client' => 'Acuícola Mar Austral SpA', 'identifier' => 'Agua de mar, centro de cultivo canal norte',
                'rejection' => [
                    'reason' => 'Contenedor inadecuado', 'by' => 'Andrea Muñoz',
                    'instructions' => 'Usar envases de vidrio ámbar o polietileno de alta densidad, llenos y sin cámara de aire.',
                    'observations' => 'Muestra enviada en botella plástica reutilizada, con restos de etiqueta de un producto de limpieza.',
                ],
            ],
            [
                'code' => 'M-2026-011', 'received' => $this->ago(4, '10:30'), 'client' => 'Minera Los Robles SpA',
                'delivered_by' => 'Rodrigo Pino', 'received_by' => 'Felipe Carrasco', 'matrix' => 'AR', 'temp' => 5.1,
                'identifier' => 'Efluente planta de tratamiento, cámara de salida',
                'entry' => ['at' => $this->ago(4, '15:00'), 'analyst' => 'Camila Rojas', 'observations' => 'pH ácido; no se ajustó para no alterar la toxicidad de la muestra.'],
                'bioassays' => [
                    'Daphnia magna Agudo'       => ['stage' => 'running', 'hours' => 20, 'ec50' => 28, 'ec50_24' => 55],
                    'Selenastrum capricornutum' => ['stage' => 'running', 'hours' => 40, 'ec50' => 22],
                ],
            ],
            [
                'code' => 'M-2026-012', 'received' => $this->ago(2, '09:15'), 'client' => 'Química Industrial Andes S.A.',
                'delivered_by' => 'Paula Contreras', 'received_by' => 'Andrea Muñoz', 'matrix' => 'AM', 'temp' => 3.9,
                'identifier' => 'Agua de mar, zona de mezcla del emisario submarino',
                'entry' => ['at' => $this->ago(2, '11:00'), 'analyst' => 'Valentina Soto', 'observations' => 'Sin observaciones.'],
                'bioassays' => [
                    'Arbacia spatuligera Fecundación'   => ['stage' => 'complete', 'ec50' => 35],
                    'Arbacia spatuligera Estado Larval' => ['stage' => 'running', 'hours' => 18],
                ],
            ],
            [
                'code' => 'M-2026-013', 'received' => $this->ago(1, '12:10'), 'client' => 'Planta de Tratamiento Aguas del Valle',
                'delivered_by' => 'Sergio Muñoz', 'received_by' => 'Felipe Carrasco', 'matrix' => 'AR', 'temp' => 4.6,
                'identifier' => 'Efluente tratado, salida del sedimentador secundario',
                'assigned' => ['Daphnia magna Agudo', 'Selenastrum capricornutum'],
            ],
            [
                'code' => 'M-2026-014', 'received' => $this->now->copy()->subHours(3)->minute(15), 'client' => 'Salmonera Fiordo Sur SpA',
                'delivered_by' => 'Carolina Díaz', 'received_by' => 'Andrea Muñoz', 'matrix' => 'AM', 'temp' => 3.3,
                'identifier' => 'Agua de mar, centro de engorda, 5 m de profundidad',
                'assigned' => ['Tisbe biconicornis Agua', 'Isochrysis galbana'],
            ],
        ];
    }

    private function createSample(array $s): void
    {
        $receivedAt = $s['received'];
        $assigned = isset($s['bioassays']) ? array_keys($s['bioassays']) : $s['assigned'];

        $this->make(ReceptionTemplate::class, [
            'template_id'           => $this->template('RECEPCIÓN DE MUESTRAS', 'RO-02.01', 3, '01.09.2023', 'reception', $receivedAt),
            'thermometer_code'      => 'TER-02',
            'correction_factor'     => '+0,2',
            'received_at'           => $receivedAt,
            'delivered_by'          => $s['delivered_by'],
            'client'                => $s['client'],
            'sampled_at'            => $receivedAt->copy()->subHours(mt_rand(4, 20)),
            'received_by'           => $s['received_by'],
            'sample_identifier'     => $s['identifier'],
            'matrix'                => $s['matrix'],
            'internal_sample_code'  => $s['code'],
            'temperature_received'  => $s['temp'],
            'temperature_corrected' => round($s['temp'] + 0.2, 1),
            'report_number'         => ++$this->reportNumber,
            'assigned_bioassays'    => $assigned,
        ], $receivedAt);

        if (!isset($s['entry'])) {
            return;
        }

        $enteredAt = $s['entry']['at'];
        $chem = $this->chemistry($s['matrix']);

        $entry = $this->make(SampleEntry::class, [
            'template_id'            => $this->template('INGRESO DE MUESTRAS', 'IN-01.01', 1, '01.09.2023', 'sample_entry', $enteredAt),
            'received_at'            => $receivedAt,
            'internal_sample_code'   => $s['code'],
            'sample_type'            => self::MATRICES[$s['matrix']],
            'sample_concentration'   => 100,
            'parameter_reading_date' => $enteredAt->toDateString(),
            'analyst'                => $s['entry']['analyst'],
            'observations'           => $s['entry']['observations'],
        ] + $chem, $enteredAt);

        foreach ($s['bioassays'] as $name => $spec) {
            [$model, $title, $code, $version, $validity, $builder] = self::BIOASSAYS[$name];

            $start = $spec['stage'] === 'running'
                ? $this->now->copy()->subHours($spec['hours'] ?? 0)->minute(0)
                : $enteredAt->copy()->addHours(2)->minute(0);

            [$data, $finishedAt] = $this->$builder($spec, $start, $chem);

            $attributes = $data + [
                'template_id'     => $this->template($title, $code, $version, $validity, 'bioassay', $enteredAt),
                'sample'          => $s['code'],
                'matrix'          => self::MATRICES[$s['matrix']],
                'analyst'         => $s['entry']['analyst'],
                'sample_entry_id' => $entry->id,
                'created_by'      => $this->analistId,
            ];

            $updatedAt = $finishedAt ?? $this->now->copy()->subHour();
            if ($spec['stage'] === 'validated') {
                $attributes['validated_by'] = $this->managerId;
                $attributes['validated_at'] = $updatedAt = $finishedAt->copy()->addDay()->setTime(11, 30);
            }

            $this->make($model, $attributes, $enteredAt, $updatedAt);
        }
    }

    private function createRejection(array $s): void
    {
        $at = $s['received'];
        $r = $s['rejection'];

        $this->make(RejectionTemplate::class, [
            'template_id'            => $this->template('RECHAZO DE MUESTRAS', 'RO-02.02', 1, $at->format('d.m.Y'), 'rejection', $at),
            'internal_sample_code'   => $s['code'],
            'sample_identifier'      => $s['identifier'],
            'client'                 => $s['client'],
            'reason_for_rejection'   => $r['reason'],
            'who_rejects'            => $r['by'],
            'who_informs_the_client' => 'Paula Reyes',
            'customer_instructions'  => $r['instructions'],
            'observations'           => $r['observations'],
        ], $at);
    }

    // ================= BIOENSAYOS AGUDOS (Daphnia magna y Tisbe RILes) =================

    private function daphniaAgudo(array $spec, Carbon $start, array $chem): array
    {
        [$data, $end] = $this->acuteBioassay($spec, $start, 'reconstituted_water_date', 'Daphnia magna');
        $data['preliminary_sample_ph'] = $chem['ph'];

        return [$data, $end];
    }

    private function tisbeRiles(array $spec, Carbon $start, array $chem): array
    {
        return $this->acuteBioassay($spec, $start, 'control_water_date', 'Tisbe');
    }

    private function acuteBioassay(array $spec, Carbon $start, string $waterField, string $organism): array
    {
        $running = $spec['stage'] === 'running';

        // En curso: el preliminar ya terminó y el definitivo partió hace $spec['hours'] horas
        $prelimStart = $running ? $this->now->copy()->subDays(4)->setTime(17, 0) : $start;
        $prelimEnd = $prelimStart->copy()->addHours(48);
        $defStart = $running ? $start : $prelimEnd->copy()->addDay()->setTime(9, 30);
        $defEnd = $running ? null : $defStart->copy()->addHours(48);

        $tables = $this->definitiveTables($spec, !$running);
        $data = [
            'preliminary_start_at'             => $prelimStart,
            'preliminary_end_at'               => $prelimEnd,
            'preliminary_sample_temperature'   => $this->between(19.6, 20.6, 1),
            "preliminary_$waterField"          => $prelimStart->copy()->subDay()->toDateString(),
            'preliminary_table'                => $this->preliminaryTable($spec),
            'definitive_start_at'              => $defStart,
            'definitive_end_at'                => $defEnd,
            'definitive_sample_temperature'    => $this->between(19.6, 20.6, 1),
            "definitive_$waterField"           => $defStart->copy()->subDay()->toDateString(),
            'definitive_24h'                   => $tables['24'],
            'definitive_48h'                   => $tables['48'],
        ];

        if ($running) {
            return [$data + ['observations' => 'Ensayo definitivo en curso; primera lectura a las 24 h.'], null];
        }

        $totals48 = end($tables['48']['rows']);
        $totals24 = end($tables['24']['rows']);
        $controlPct = round($totals48['control_sum'] / 20 * 100);

        return [$data + [
            'control_immobility' => (string) $controlPct,
            'cl50_24h'           => $this->pct($this->ec50From(self::SERIE, array_map(fn ($n) => $n / 20, $totals24['concentrations_sum']))),
            'cl50_48h'           => $this->pct($this->ec50From(self::SERIE, array_map(fn ($n) => $n / 20, $totals48['concentrations_sum']))),
            'observations'       => "Ensayo válido: inmovilidad del control de $controlPct % (≤ 10 %). pH y oxígeno disuelto dentro de rango durante el ensayo con $organism.",
        ], $defEnd];
    }

    private function preliminaryTable(array $spec): array
    {
        $rows = [];
        foreach (array_merge([null], [1.56, 3.13, 6.25, 12.5, 25, 50, 100]) as $c) {
            $row = ['concentration' => $c === null ? 'Control' : $this->label($c)];
            $reps = [];
            foreach ([1, 2] as $r) {
                $m48 = $c === null ? 0 : $this->count(5, $this->affected($c, $spec['ec50'] ?? null));
                $m24 = $c === null ? 0 : min($m48, $this->count(5, $this->affected($c, $spec['ec50_24'] ?? null)));
                $reps[$r] = [$m24, $m48];
            }
            $rows[] = $row + [
                '24h_rep1' => $reps[1][0], '24h_rep2' => $reps[2][0], '24h_sum' => $reps[1][0] + $reps[2][0],
                '48h_rep1' => $reps[1][1], '48h_rep2' => $reps[2][1], '48h_sum' => $reps[1][1] + $reps[2][1],
            ];
        }

        return $rows;
    }

    private function definitiveTables(array $spec, bool $withCounts): array
    {
        $counts = ['24' => [], '48' => []];
        $control = ['24' => [], '48' => []];
        for ($r = 1; $r <= 4; $r++) {
            $control['48'][$r] = $r <= ($spec['control_dead'] ?? 0) ? 1 : 0;
            $control['24'][$r] = 0;
            foreach (self::SERIE as $i => $c) {
                $m48 = $this->count(5, $this->affected($c, $spec['ec50'] ?? null));
                $counts['48'][$r][$i] = $m48;
                $counts['24'][$r][$i] = min($m48, $this->count(5, $this->affected($c, $spec['ec50_24'] ?? null)));
            }
        }

        $tables = [];
        foreach (['24', '48'] as $h) {
            $rows = [];
            for ($r = 1; $r <= 4; $r++) {
                $rows[] = [
                    'replica'        => $r,
                    'control'        => $withCounts ? $control[$h][$r] : null,
                    'concentrations' => $withCounts ? $counts[$h][$r] : array_fill(0, 5, null),
                ];
            }
            $rows[] = [
                'is_total'           => true,
                'control_sum'        => $withCounts ? array_sum($control[$h]) : null,
                'concentrations_sum' => $withCounts
                    ? array_map(fn ($i) => array_sum(array_column($counts[$h], $i)), range(0, 4))
                    : array_fill(0, 5, null),
            ];
            $tables[$h] = ['concentration_values' => array_map([$this, 'label'], self::SERIE), 'rows' => $rows];
        }

        return $tables;
    }

    // ================= DAPHNIA MAGNA CRÓNICO (reproducción 21 días) =================

    private function daphniaCronico(array $spec, Carbon $start, array $chem): array
    {
        $running = $spec['stage'] === 'running';
        $start = $running ? $this->now->copy()->subDays($spec['day'])->setTime(9, 0) : $start->copy()->addDay()->setTime(10, 0);
        $days = $running ? $spec['day'] - 1 : 21;

        $firstBrood = [];
        for ($r = 1; $r <= 10; $r++) {
            $firstBrood[$r] = mt_rand(8, 10);
        }

        [$control, $controlTotal] = $this->reproductionGrid($days, $firstBrood, 1.0);

        $concentrations = [];
        foreach (self::SERIE as $i => $c) {
            [$grid, $total] = $this->reproductionGrid($days, $firstBrood, $spec['factors'][$i]);
            $sum = $grid['sum'];
            unset($grid['sum']);
            $concentrations[$i + 1] = ['value' => $this->label($c), 'days' => $grid, 'sum' => $sum, 'total_reproduction' => $total];
        }

        $maintenance = [];
        for ($d = 0; $d <= min($days, 20); $d += 3) {
            $date = $start->copy()->addDays($d)->toDateString();
            $maintenance[$d] = ['water_date' => $date, 'food_date' => $date, 'microalgae_ml' => '1.5'];
        }

        $data = [
            'bioassay_start'             => $start,
            'bioassay_end'               => $running ? null : $start->copy()->addDays(21),
            'sample_temperature'         => $this->between(19.8, 20.4, 1),
            'ph'                         => $chem['ph'],
            'maintenance_data'           => $maintenance,
            'control_data'               => $control,
            'control_total_reproduction' => $controlTotal,
            'concentrations_data'        => $concentrations,
        ];

        if ($running) {
            return [$data + ['observations' => "Ensayo en curso (día {$spec['day']} de 21). Sin mortalidad de adultos a la fecha."], null];
        }

        $loec = null;
        foreach ($spec['factors'] as $i => $factor) {
            if ($factor < 0.85) {
                $loec = $i;
                break;
            }
        }
        $perFemale = round($controlTotal / 10, 1);

        return [$data + [
            'noec'         => $loec === null ? '100 %' : ($loec === 0 ? '< 6,25 %' : $this->label(self::SERIE[$loec - 1])),
            'loec'         => $loec === null ? '> 100 %' : $this->label(self::SERIE[$loec]),
            'observations' => 'Ensayo válido: reproducción media del control de ' . number_format($perFemale, 1, ',', '') . ' crías por hembra (≥ 40) y sobrevivencia de adultos del 100 %.',
        ], $start->copy()->addDays(21)];
    }

    private function reproductionGrid(int $days, array $firstBrood, float $factor): array
    {
        $grid = [];
        for ($d = 1; $d <= $days; $d++) {
            for ($r = 1; $r <= 10; $r++) {
                $size = $d === $firstBrood[$r] ? mt_rand(5, 8) : mt_rand(10, 16);
                $isBrood = $d >= $firstBrood[$r] && ($d - $firstBrood[$r]) % 3 === 0;
                $grid[$d]["r$r"] = $isBrood ? (int) round($size * $factor) : 0;
            }
        }

        $sum = [];
        for ($r = 1; $r <= 10; $r++) {
            $sum["r$r"] = array_sum(array_column($grid, "r$r"));
        }
        $grid['sum'] = $sum;

        return [$grid, array_sum($sum)];
    }

    // ================= MICROALGAS (inhibición del crecimiento 96 h) =================

    private function isochrysis(array $spec, Carbon $start, array $chem): array
    {
        $mu = $this->between(0.97, 1.03, 3);
        $controlReps = $this->replicatesAt($mu, 4, 6);
        $rates = array_map(fn ($n) => log($n) / 4, $controlReps);
        $muControl = array_sum($rates) / count($rates);

        $concs = [3.125, 6.25, 12.5, 25, 50, 100];
        $rows = [];
        $inhibitions = [];
        foreach ($concs as $i => $c) {
            $reps = $this->replicatesAt($muControl * (1 - $this->affected($c, $spec['ec50'] ?? null, 2.5)), 4, 3);
            [$rate, $percent, $inhibition] = $this->growth($reps, $muControl);
            $inhibitions[] = $inhibition / 100;
            $phInitial = round($chem['ph'] + $this->jitter(0.05), 2);
            $rows[$i + 1] = [
                'sample_or_concentration' => $this->label($c),
                'ph_initial'              => $phInitial,
                'ph_final'                => round(min(8.45, $phInitial + 0.25 * (1 - $inhibition / 100) + 0.05), 2),
                'r196h'                   => $reps[0],
                'r296h'                   => $reps[1],
                'r396h'                   => $reps[2],
                'growth_rate'             => number_format($rate, 4, '.', ''),
                'growth_rate_percent'     => number_format($percent, 2, '.', ''),
                'inhibition_percent'      => number_format($inhibition, 2, '.', ''),
                'ec50'                    => null,
            ];
        }
        $ec50 = $this->pct($this->ec50From($concs, $inhibitions));
        $rows[1]['ec50'] = $ec50;
        $cv = $this->cv($rates);
        $end = $start->copy()->addHours(96);

        return [[
            'bioassay_start'        => $start,
            'bioassay_end'          => $end,
            'initial_inoculum_vol'  => 1.00,
            'stock_culture_date'    => $start->copy()->subDays(5)->toDateString(),
            'rc24h'                 => round(exp($mu), 2),
            'rc48h'                 => round(exp($mu * 2), 2),
            'rc72h'                 => round(exp($mu * 3), 2),
            'rc196h'                => $controlReps[0],
            'rc296h'                => $controlReps[1],
            'rc396h'                => $controlReps[2],
            'rc496h'                => $controlReps[3],
            'rc596h'                => $controlReps[4],
            'rc696h'                => $controlReps[5],
            'ph_initial'            => 8.10,
            'ph_final'              => 8.38,
            'growth_rate_control'   => round($muControl, 4),
            'measurements'          => $rows,
            'ec50_detail'           => "CE50-96h: $ec50 (inhibición de la tasa de crecimiento)",
            'variation_coefficient' => round($cv, 2),
            'observations'          => $this->algaeObservation($muControl, $cv),
        ], $end];
    }

    private function selenastrum(array $spec, Carbon $start, array $chem): array
    {
        $running = $spec['stage'] === 'running';
        $mu = $this->between(1.18, 1.26, 3);
        $controlRates = [];
        for ($r = 0; $r < 4; $r++) {
            $controlRates[] = $mu + $this->jitter(0.02);
        }
        $muControl = array_sum($controlRates) / 4;
        $at = fn (float $rate, int $days) => round(exp($rate * $days), 2);

        $rows = [];
        $inhibitions = [];
        foreach (self::SERIE as $i => $c) {
            $muC = $muControl * (1 - $this->affected($c, $spec['ec50'] ?? null, 2.5));
            $phInitial = round($chem['ph'] + $this->jitter(0.05), 2);
            $row = ['sample_or_concentration' => $this->label($c), 'ph_initial' => $phInitial];
            $reps96 = [];
            for ($r = 1; $r <= 4; $r++) {
                $rate = $muC + $this->jitter(0.03);
                $row["r{$r}_24h"] = $at($rate, 1);
                $row["r{$r}_48h"] = $running ? null : $at($rate, 2);
                $row["r{$r}_72h"] = $running ? null : $at($rate, 3);
                $reps96[] = $at($rate, 4);
            }
            if ($running) {
                $rows[$i + 1] = $row;
                continue;
            }
            [$rate, $percent, $inhibition] = $this->growth($reps96, $muControl);
            $inhibitions[] = $inhibition / 100;
            $rows[$i + 1] = $row + [
                'ph_final'            => round($phInitial + 0.45 * (1 - $inhibition / 100) + 0.05, 2),
                'r196h'               => $reps96[0],
                'r296h'               => $reps96[1],
                'r396h'               => $reps96[2],
                'r496h'               => $reps96[3],
                'growth_rate'         => number_format($rate, 4, '.', ''),
                'percent_growth_rate' => number_format($percent, 2, '.', ''),
                'percent_inhibition'  => number_format($inhibition, 2, '.', ''),
                'ce50'                => null,
            ];
        }

        $data = [
            'bioassay_start'     => $start,
            'initial_inoculum'   => 1.00,
            'stock_culture_date' => $start->copy()->subDays(4)->toDateString(),
            'rc24h'              => $at($muControl, 1),
            'ph_initial'         => 7.52,
            'measurements'       => $rows,
        ];

        if ($running) {
            return [$data + ['observations' => 'Ensayo en curso; lecturas de 24 h registradas.'], null];
        }

        $controlReps = array_map(fn ($rate) => $at($rate, 4), $controlRates);
        $realRates = array_map(fn ($n) => log($n) / 4, $controlReps);
        $cv = $this->cv($realRates);
        $ec50Value = $this->ec50From(self::SERIE, $inhibitions);
        $rows[1]['ce50'] = $ec50Value !== null && $ec50Value > 0 ? round($ec50Value, 2) : null;
        $end = $start->copy()->addHours(96);

        return [array_merge($data, [
            'bioassay_end'          => $end,
            'rc48h'                 => $at($muControl, 2),
            'rc72h'                 => $at($muControl, 3),
            'rc196h'                => $controlReps[0],
            'rc296h'                => $controlReps[1],
            'rc396h'                => $controlReps[2],
            'rc496h'                => $controlReps[3],
            'ph_final'              => 8.21,
            'control_growth_rate'   => round(array_sum($realRates) / 4, 4),
            'measurements'          => $rows,
            'ce50_detail'           => 'CE50-96h: ' . $this->pct($ec50Value) . ' (inhibición de la tasa de crecimiento)',
            'variation_coefficient' => round($cv, 2),
            'observations'          => $this->algaeObservation(array_sum($realRates) / 4, $cv),
        ]), $end];
    }

    private function replicatesAt(float $rate, int $days, int $n): array
    {
        $reps = [];
        for ($i = 0; $i < $n; $i++) {
            $reps[] = round(exp(($rate + $this->jitter(0.02)) * $days), 2);
        }

        return $reps;
    }

    private function growth(array $reps96, float $muControl): array
    {
        $rate = log(array_sum($reps96) / count($reps96)) / 4;
        $percent = $rate / $muControl * 100;

        return [$rate, $percent, max(0, 100 - $percent)];
    }

    private function algaeObservation(float $mu, float $cv): string
    {
        return 'Ensayo válido: tasa de crecimiento del control de ' . number_format($mu, 2, ',', '')
            . ' d⁻¹ (≥ 0,92) y coeficiente de variación del control de ' . number_format($cv, 1, ',', '') . ' % (≤ 7 %).';
    }

    // ================= TISBE (agua) =================

    private function tisbeAgua(array $spec, Carbon $start, array $chem): array
    {
        $rows = [];
        $props24 = [];
        $props48 = [];
        foreach (array_merge([null], self::SERIE) as $i => $c) {
            $row = ['concentration' => $c === null ? 'Control' : $this->label($c)];
            $sum24 = $sum48 = 0;
            for ($r = 1; $r <= 4; $r++) {
                $m48 = $c === null ? ($r <= ($spec['control_dead'] ?? 0) ? 1 : 0) : $this->count(5, $this->affected($c, $spec['ec50']));
                $m24 = $c === null ? 0 : min($m48, $this->count(5, $this->affected($c, $spec['ec50_24'])));
                $row["h24_r$r"] = $m24;
                $row["h48_r$r"] = $m48;
                $sum24 += $m24;
                $sum48 += $m48;
            }
            $row['sum_24h'] = $sum24;
            $row['sum_48h'] = $sum48;
            $row['observations'] = $sum48 === 0 ? 'Sin mortalidad' : ($sum48 <= 2 ? 'Mortalidad baja' : ($sum48 < 10 ? 'Mortalidad parcial' : 'Mortalidad alta'));
            $rows[$i + 1] = $row;
            if ($c !== null) {
                $props24[] = $sum24 / 20;
                $props48[] = $sum48 / 20;
            }
        }
        $end = $start->copy()->addHours(48);
        $controlPct = round($rows[1]['sum_48h'] / 20 * 100);

        return [[
            'bioassay_start'     => $start,
            'bioassay_end'       => $end,
            'initial_inoculum'   => 1.00,
            'stock_culture_date' => $start->copy()->subDays(7)->toDateString(),
            'samples_data'       => $rows,
            'cl50_24h'           => $this->pct($this->ec50From(self::SERIE, $props24)),
            'cl50_48h'           => $this->pct($this->ec50From(self::SERIE, $props48)),
            'observations'       => "Ensayo válido: mortalidad del control de $controlPct % (≤ 10 %). Temperatura 20 ± 1 °C y salinidad 32 ‰ durante el ensayo.",
            'vb'                 => $spec['stage'] === 'validated' ? 'Paula Reyes' : null,
        ], $end];
    }

    // ================= ARBACIA SPATULIGERA =================

    private function arbaciaFecundacion(array $spec, Carbon $start, array $chem): array
    {
        $controlFert = $this->between(87, 92, 1);
        $rows = [];
        $inhibitions = [];
        $controlAvg = null;
        foreach (array_merge([null], self::SERIE) as $i => $c) {
            $expected = $c === null ? $controlFert : $controlFert * (1 - $this->affected($c, $spec['ec50'] ?? null, 2.5));
            $row = ['concentration' => $c === null ? 'Control' : $this->label($c)];
            $ferts = [];
            for ($r = 1; $r <= 3; $r++) {
                $nf = max(0, min(100, (int) round(100 - $expected + $this->jitter(3))));
                $row["r{$r}_nf"] = $nf;
                $row["r{$r}_total"] = 100;
                $row["r{$r}_fert"] = number_format(100 - $nf, 2, '.', '');
                $ferts[] = 100 - $nf;
            }
            $avg = array_sum($ferts) / 3;
            $controlAvg = $controlAvg ?? $avg;
            $inhibition = $c === null ? 0 : max(0, ($controlAvg - $avg) / $controlAvg * 100);
            $row['avg_fertilization'] = number_format($avg, 2, '.', '');
            $row['inhibition'] = number_format($inhibition, 2, '.', '');
            $row['ci'] = null;
            if ($c !== null) {
                $inhibitions[] = $inhibition / 100;
            }
            $rows[$i + 1] = $row;
        }
        $ci50 = $this->pct($this->ec50From(self::SERIE, $inhibitions));
        $rows[2]['ci'] = $ci50;
        $countEnd = $start->copy()->addHours(5);

        return [[
            'bioassay_start'                   => $start,
            'control_fertilization_percentage' => round($controlAvg, 2),
            'sperm_addition_time'              => $start->format('H:i'),
            'egg_addition_time'                => $start->copy()->addMinutes(20)->format('H:i'),
            'fixation_time_end'                => $start->copy()->addMinutes(40)->format('H:i'),
            'count_end_datetime'               => $countEnd,
            'rows_data'                        => $rows,
            'ci50'                             => $ci50,
            'observations'                     => 'Ensayo válido: fecundación del control de ' . number_format($controlAvg, 1, ',', '') . ' % (≥ 70 %). Exposición de espermatozoides de 20 min.',
        ], $countEnd];
    }

    private function arbaciaLarval(array $spec, Carbon $start, array $chem): array
    {
        $running = $spec['stage'] === 'running';
        $data = [
            'bioassay_start'           => $start,
            'fertilization_time'       => $start->format('H:i'),
            'fertilized_eggs_added_at' => $start->copy()->addMinutes(30)->format('H:i'),
        ];

        if ($running) {
            $rows = [];
            foreach (self::SERIE as $i => $c) {
                $rows[$i + 1] = ['concentration' => $this->label($c)];
            }

            return [$data + [
                'control_data' => [],
                'rows_data'    => $rows,
                'observations' => 'Ensayo en curso; fijación de larvas a las 48 h.',
            ], null];
        }

        $controlExpected = $this->between(86, 91, 1);
        $control = [];
        $percents = [];
        for ($r = 1; $r <= 4; $r++) {
            $larva = (int) round($controlExpected + $this->jitter(3));
            $control["r{$r}_larva"] = $larva;
            $control["r{$r}_total"] = 100;
            $control["r{$r}_percent"] = number_format($larva, 2, '.', '');
            $percents[] = $larva;
        }
        $controlAvg = array_sum($percents) / 4;
        $control['larval_pluteus_avg'] = number_format($controlAvg, 2, '.', '');
        $control['total_larva_percent'] = number_format($controlAvg, 2, '.', '');

        $rows = [];
        $inhibitions = [];
        foreach (self::SERIE as $i => $c) {
            $expected = $controlAvg * (1 - $this->affected($c, $spec['ec50'] ?? null, 2.5));
            $row = ['concentration' => $this->label($c)];
            $values = [];
            for ($r = 1; $r <= 4; $r++) {
                $larva = max(0, min(100, (int) round($expected + $this->jitter(3))));
                $row["r{$r}_larva"] = $larva;
                $row["r{$r}_total"] = 100;
                $row["r{$r}_percent"] = number_format($larva, 2, '.', '');
                $values[] = $larva;
            }
            $avg = array_sum($values) / 4;
            $inhibition = max(0, ($controlAvg - $avg) / $controlAvg * 100);
            $inhibitions[] = $inhibition / 100;
            $rows[$i + 1] = $row + [
                'larval_pluteus_percent' => number_format($avg, 2, '.', ''),
                'inhibition_percent'     => number_format($inhibition, 2, '.', ''),
                'ce'                     => null,
            ];
        }
        $ce50 = $this->pct($this->ec50From(self::SERIE, $inhibitions));
        $rows[1]['ce'] = $ce50;
        $countEnd = $start->copy()->addHours(50);

        return [$data + [
            'fixation_time_end'  => $start->copy()->addHours(48)->format('Y-m-d H:i'),
            'count_end_datetime' => $countEnd,
            'control_data'       => $control,
            'rows_data'          => $rows,
            'ce50'               => $ce50,
            'observations'       => 'Ensayo válido: desarrollo larval normal (pluteus) del control de ' . number_format($controlAvg, 1, ',', '') . ' % (≥ 80 %).',
        ], $countEnd];
    }

    // ================= APOYO =================

    private function clearLabData(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (self::BIOASSAYS as [$model]) {
            $model::truncate();
        }
        SampleEntry::truncate();
        RejectionTemplate::truncate();
        ReceptionTemplate::truncate();
        Template::truncate();
        Schema::enableForeignKeyConstraints();
    }

    private function make(string $model, array $attributes, Carbon $createdAt, ?Carbon $updatedAt = null): Model
    {
        $record = new $model();
        $record->forceFill($attributes + ['created_at' => $createdAt, 'updated_at' => $updatedAt ?? $createdAt]);
        $record->save();

        return $record;
    }

    private function template(string $title, string $code, int $version, string $validity, string $type, Carbon $at): int
    {
        return $this->make(Template::class, compact('title', 'code', 'version', 'validity', 'type'), $at)->id;
    }

    private function chemistry(string $matrix): array
    {
        $ranges = [
            'AM'  => ['ph' => [7.9, 8.2], 'salinity' => [32.0, 34.0], 'conductivity' => [50200, 52800], 'dissolved_oxygen' => [6.6, 7.6]],
            'ASP' => ['ph' => [7.0, 7.8], 'salinity' => [0.1, 0.2], 'conductivity' => [160, 380], 'dissolved_oxygen' => [7.8, 9.0]],
            'AR'  => ['ph' => [6.6, 7.4], 'salinity' => [0.8, 2.2], 'conductivity' => [1450, 3400], 'dissolved_oxygen' => [4.0, 5.8]],
            'SM'  => ['ph' => [7.5, 7.9], 'salinity' => [29.0, 31.0], 'conductivity' => [45500, 48000], 'dissolved_oxygen' => [5.2, 6.2]],
        ][$matrix];

        $values = ['temperature' => $this->between(18.5, 20.5, 1)];
        foreach ($ranges as $field => [$min, $max]) {
            $values[$field] = $this->between($min, $max, $field === 'conductivity' ? 0 : ($field === 'ph' ? 2 : 1));
        }

        return $values;
    }

    private function ago(int $days, string $time): Carbon
    {
        return Carbon::now()->subDays($days)->setTimeFromTimeString($time);
    }

    // Fracción de organismos afectados según una curva dosis-respuesta log-logística
    private function affected(float $conc, ?float $ec50, float $slope = 3.0): float
    {
        return $ec50 === null ? 0.0 : 1 / (1 + pow($ec50 / $conc, $slope));
    }

    private function count(int $n, float $p): int
    {
        return max(0, min($n, (int) round($n * $p + $this->jitter(0.6))));
    }

    private function jitter(float $amplitude): float
    {
        return (mt_rand(-1000, 1000) / 1000) * $amplitude;
    }

    private function between(float $min, float $max, int $decimals): float
    {
        return round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), $decimals);
    }

    // Interpolación log-lineal entre las dos concentraciones que cruzan el 50 % de efecto.
    // null = no se alcanza el 50 % (mayor a la concentración más alta); -1 = ya se supera en la más baja.
    private function ec50From(array $concs, array $proportions): ?float
    {
        if ($proportions[0] >= 0.5) {
            return -1.0;
        }
        for ($i = 1; $i < count($concs); $i++) {
            if ($proportions[$i] >= 0.5) {
                $step = $proportions[$i] - $proportions[$i - 1];
                $f = $step > 0 ? (0.5 - $proportions[$i - 1]) / $step : 0;

                return pow(10, log10($concs[$i - 1]) + $f * (log10($concs[$i]) - log10($concs[$i - 1])));
            }
        }

        return null;
    }

    private function pct(?float $value): string
    {
        if ($value === null) {
            return '> 100 %';
        }
        if ($value < 0) {
            return '< ' . $this->label(self::SERIE[0]);
        }

        return number_format($value, 1, ',', '') . ' %';
    }

    private function label(float $conc): string
    {
        return rtrim(rtrim(number_format($conc, 2, ',', ''), '0'), ',') . ' %';
    }

    private function cv(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn ($v) => ($v - $mean) ** 2, $values)) / (count($values) - 1);

        return sqrt($variance) / $mean * 100;
    }
}
