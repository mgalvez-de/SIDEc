@extends('layouts.app')

@php
    $bioassayBadges = [
        'validated'   => ['bg-success', 'Validado'],
        'pending'     => ['bg-warning text-dark', 'En curso'],
        'not_started' => ['bg-secondary', 'Sin ingresar'],
    ];
    $stateBadges = [
        'Completada'   => 'bg-success',
        'En proceso'   => 'bg-warning text-dark',
        'Sin ingresar' => 'bg-secondary',
        'Rechazada'    => 'bg-danger',
    ];

    // Tarjetas: [texto, valor, ícono, color]. Rechazadas solo para Supervisor y Jefe de Área.
    $cards = array_filter([
        ['Recibidas', $totals['received'], 'bi-inbox-fill', '#3498db'],
        ['Sin ingresar', $totals['not_entered'], 'bi-hourglass', '#7f8c8d'],
        ['En proceso', $totals['in_process'], 'bi-gear-wide-connected', '#f39c12'],
        ['Completadas', $totals['completed'], 'bi-check-circle-fill', '#27ae60'],
        $canSeeClients ? ['Rechazadas', $totals['rejected'], 'bi-x-circle-fill', '#c0392b'] : null,
    ]);

    // Datos de los gráficos
    $stateChart = array_filter([
        'Completadas'  => $totals['completed'],
        'En proceso'   => $totals['in_process'],
        'Sin ingresar' => $totals['not_entered'],
        'Rechazadas'   => $canSeeClients ? $totals['rejected'] : null,
    ], fn ($value) => $value !== null);

    // Texto de lo que se está mostrando según los filtros
    $period = request('from') || request('to')
        ? 'Recibidas ' . (request('from') ? 'desde el ' . \Carbon\Carbon::parse(request('from'))->format('d-m-Y') . ' ' : '')
            . (request('to') ? 'hasta el ' . \Carbon\Carbon::parse(request('to'))->format('d-m-Y') : '')
        : 'Todas las muestras';
    $showing = $period . ($canSeeClients && request('client') ? ' · Cliente: ' . request('client') : '');
@endphp

@section('content')
    <div class="container">
        <x-page-header title="Dashboard" icon="bi-bar-chart-line-fill"
            subtitle="Estado y avance de las muestras, con el plazo de entrega de 6 semanas" />

        {{-- Filtros: período y (solo Supervisor y Jefe de Área) cliente --}}
        <form method="GET" action="{{ route('dashboard') }}" class="card shadow-sm border-0 mb-2">
            <div class="card-body row g-3 align-items-end">
                <div class="col-sm-6 col-md-3">
                    <label for="from" class="form-label fw-bold">Recibidas desde</label>
                    <input type="date" id="from" name="from" value="{{ request('from') }}"
                        class="form-control @error('from') is-invalid @enderror">
                    @error('from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6 col-md-3">
                    <label for="to" class="form-label fw-bold">Hasta</label>
                    <input type="date" id="to" name="to" value="{{ request('to') }}"
                        class="form-control @error('to') is-invalid @enderror">
                    @error('to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @if ($canSeeClients)
                    <div class="col-md-4">
                        <label for="client" class="form-label fw-bold">Cliente</label>
                        <select id="client" name="client" class="form-select">
                            <option value="">Todos los clientes</option>
                            @foreach ($clients as $clientName)
                                <option value="{{ $clientName }}" {{ request('client') === $clientName ? 'selected' : '' }}>
                                    {{ $clientName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-ucsc flex-fill">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-light border" title="Quitar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
        <p class="small text-muted mb-4"><i class="bi bi-info-circle me-1"></i>Mostrando: {{ $showing }}</p>

        {{-- Tarjetas --}}
        <div class="row g-3 mb-4">
            @foreach ($cards as [$label, $value, $icon, $color])
                <div class="col-6 col-md-4 col-xl">
                    <div class="card h-100 border-0 shadow-sm text-center indicator-card" style="border-left: 4px solid {{ $color }} !important;">
                        <div class="card-body px-2">
                            <i class="bi {{ $icon }} fs-3" style="color: {{ $color }};"></i>
                            <div class="fs-3 fw-bold" style="color: {{ $color }};">{{ $value }}</div>
                            <div class="small text-muted">{{ $label }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Plazo de entrega (6 semanas): aviso solo si hay muestras atrasadas o por atrasarse --}}
        @if ($totals['warning'] + $totals['overdue'] > 0)
            <div class="alert alert-warning d-flex flex-wrap align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-alarm-fill fs-5"></i>
                <span>
                    Plazo de entrega:
                    @if ($totals['overdue'])
                        <strong class="text-danger">
                            {{ $totals['overdue'] }} {{ $totals['overdue'] === 1 ? 'muestra con plazo vencido' : 'muestras con plazo vencido' }}
                        </strong>
                    @endif
                    @if ($totals['overdue'] && $totals['warning'])
                        ·
                    @endif
                    @if ($totals['warning'])
                        <strong>
                            {{ $totals['warning'] }} {{ $totals['warning'] === 1 ? 'muestra por vencer' : 'muestras por vencer' }}
                        </strong>
                        (semana 5 o 6)
                    @endif
                </span>
                <button type="button" class="btn btn-sm btn-outline-dark ms-auto" id="showLateSamples">
                    <i class="bi bi-funnel me-1"></i> Ver en la tabla
                </button>
            </div>
        @else
            <p class="small text-success mb-4">
                <i class="bi bi-check-circle me-1"></i>Todas las muestras pendientes están dentro del plazo de entrega.
            </p>
        @endif

        {{-- Gráficos (responden a los filtros) --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-pie-chart-fill me-1 text-warning"></i> Estado de las muestras
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                        @if (array_sum($stateChart) > 0)
                            <div style="width: 100%; max-width: 300px;"><canvas id="stateChart"></canvas></div>
                        @else
                            <span class="text-muted">Sin muestras para mostrar.</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-eyedropper me-1 text-primary"></i> Bioensayos por tipo
                        <span class="small text-muted fw-normal">
                            ({{ $totals['validated'] }} validados · {{ $totals['pending'] }} pendientes)
                        </span>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                        @if ($byType)
                            <div style="position: relative; width: 100%; height: 300px;"><canvas id="bioassayChart"></canvas></div>
                        @else
                            <span class="text-muted">Sin bioensayos para mostrar.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Muestras --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-list-check me-1"></i> Muestras y plazo de entrega
                <span class="small text-muted fw-normal">({{ \App\Models\ReceptionTemplate::DELIVERY_WEEKS }} semanas desde la recepción)</span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle" id="samplesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            @if ($canSeeClients)
                                <th>Cliente</th>
                            @endif
                            <th>Recepción</th>
                            <th>Estado</th>
                            <th>Bioensayos</th>
                            <th>Plazo de entrega</th>
                        </tr>
                        {{-- Filtros por columna (igual que en Recepciones) --}}
                        <tr class="filters">
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Código"></th>
                            @if ($canSeeClients)
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cliente"></th>
                            @endif
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="dd-mm-aaaa"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Estado"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Bioensayo"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Plazo"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td class="text-nowrap">
                                    @if ($row['entry_id'])
                                        <a href="{{ route('sample_entries.show', $row['entry_id']) }}">{{ $row['code'] }}</a>
                                    @else
                                        {{ $row['code'] }}
                                    @endif
                                </td>
                                @if ($canSeeClients)
                                    <td>{{ $row['client'] ?? '-' }}</td>
                                @endif
                                <td class="text-nowrap" data-order="{{ optional($row['received_at'])->format('Y-m-d H:i') }}">
                                    {{ $row['received_at'] ? $row['received_at']->format('d-m-Y') : '-' }}
                                </td>
                                <td><span class="badge {{ $stateBadges[$row['state']] }}">{{ $row['state'] }}</span></td>
                                <td>
                                    @forelse ($row['bioassays'] as $name => $status)
                                        <div class="small text-nowrap">
                                            <span class="badge {{ $bioassayBadges[$status][0] }}">{{ $bioassayBadges[$status][1] }}</span>
                                            {{ $name }}
                                        </div>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td class="text-nowrap" data-order="{{ optional($row['deadline'])->format('Y-m-d') }}">
                                    @switch($row['deadline_status'])
                                        @case('completed')
                                            <span class="badge bg-success">Completada</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">Vencido</span>
                                            <div class="small text-danger">
                                                {{ $row['deadline']->format('d-m-Y') }} (hace {{ $row['deadline']->diffInDays(now()) }} días)
                                            </div>
                                            @break
                                        @case('warning')
                                            <span class="badge bg-warning text-dark">Por vencer</span>
                                            <div class="small">
                                                {{ $row['deadline']->format('d-m-Y') }} (quedan {{ now()->diffInDays($row['deadline']) }} días)
                                            </div>
                                            @break
                                        @case('ok')
                                            <span class="badge bg-light text-dark border">En plazo</span>
                                            <div class="small text-muted">{{ $row['deadline']->format('d-m-Y') }}</div>
                                            @break
                                        @case('unknown')
                                            <span class="text-muted small">Sin fecha de recepción</span>
                                            @break
                                        @default
                                            <span class="text-muted">-</span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .indicator-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .indicator-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
        }

        /* Fila de filtros por columna */
        .filters th {
            background-color: #f8f9fa !important;
            padding: 6px 4px !important;
        }

        .filters .column-search {
            font-size: 0.8rem;
            min-width: 80px;
        }

        @media print {
            .filters {
                display: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        $(function () {
            // ========== Gráfico de estados (dona) ==========
            const stateData = @json($stateChart);
            const stateColors = {
                'Completadas': '#27ae60', 'En proceso': '#f39c12', 'Sin ingresar': '#95a5a6', 'Rechazadas': '#c0392b'
            };
            const stateCanvas = document.getElementById('stateChart');
            if (stateCanvas) {
                new Chart(stateCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(stateData),
                        datasets: [{
                            data: Object.values(stateData),
                            backgroundColor: Object.keys(stateData).map((label) => stateColors[label]),
                        }]
                    },
                    options: { cutout: '65%', plugins: { legend: { position: 'bottom' } } }
                });
            }

            // ========== Bioensayos validados y pendientes por tipo (barras horizontales) ==========
            const byType = @json($byType);
            const bioassayCanvas = document.getElementById('bioassayChart');
            if (bioassayCanvas) {
                new Chart(bioassayCanvas, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(byType),
                        datasets: [
                            { label: 'Validados', data: Object.values(byType).map((t) => t.validated), backgroundColor: '#27ae60' },
                            { label: 'Pendientes', data: Object.values(byType).map((t) => t.pending), backgroundColor: '#f39c12' },
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false, // usa el alto del contenedor (300 px)
                        scales: { x: { stacked: true, ticks: { precision: 0 } }, y: { stacked: true } },
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }

            // ========== Tabla de muestras ==========
            const table = $('#samplesTable');
            const columns = table.find('thead tr:first th').length;
            const samplesTable = table.DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[columns - 4, 'desc']],                   // "Recepción" (se corre si está la columna Cliente)
                columnDefs: [{ orderable: false, targets: -2 }],  // la columna Bioensayos no se ordena
                orderCellsTop: true                               // al hacer clic en los filtros no se reordena
            });

            // Búsqueda individual por columna: cada input filtra la columna en la que está.
            // Una barra "|" significa "o": "Vencido|Por vencer" muestra las dos.
            function searchColumn(index, text) {
                const anyOf = text.includes('|');
                samplesTable.column(index).search(text, anyOf, !anyOf).draw();
            }

            table.find('.column-search').on('keyup change', function () {
                searchColumn($(this).parent().index(), this.value);
            });

            // Botón del aviso de plazo: deja en la tabla solo las muestras vencidas o por vencer
            $('#showLateSamples').on('click', function () {
                const deadlineColumn = columns - 1;
                table.find('.filters th').eq(deadlineColumn).find('input').val('Vencido|Por vencer');
                searchColumn(deadlineColumn, 'Vencido|Por vencer');
                table[0].scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
@endpush
