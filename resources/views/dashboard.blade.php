@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="container">
            <h1 class="mb-4 text-center fw-bold" style="color: #2c3e50;">
                <i class="bi bi-bar-chart-line-fill me-2"></i>Dashboard de Muestras
            </h1>

            <!-- Tarjetas de Indicadores -->
            <div class="row mb-4 g-3">
                <div class="col-md-4 col-lg">
                    <div class="card indicator-card h-100 border-0 shadow-sm"
                        style="border-left: 4px solid #3498db !important;">
                        <div class="card-body text-center">
                            <div class="indicator-icon mb-2" style="color: #3498db;">
                                <i class="bi bi-inbox-fill fs-1"></i>
                            </div>
                            <h6 class="card-subtitle text-muted">Recepcionadas</h6>
                            <p class="card-text fs-2 fw-bold counter" data-target="{{ $totalRecepcionadas }}"
                                style="color: #3498db;">0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg">
                    <div class="card indicator-card h-100 border-0 shadow-sm"
                        style="border-left: 4px solid #e74c3c !important;">
                        <div class="card-body text-center">
                            <div class="indicator-icon mb-2" style="color: #e74c3c;">
                                <i class="bi bi-x-circle-fill fs-1"></i>
                            </div>
                            <h6 class="card-subtitle text-muted">Rechazadas</h6>
                            <p class="card-text fs-2 fw-bold counter" data-target="{{ $totalRechazadas }}"
                                style="color: #e74c3c;">0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg">
                    <div class="card indicator-card h-100 border-0 shadow-sm"
                        style="border-left: 4px solid #95a5a6 !important;">
                        <div class="card-body text-center">
                            <div class="indicator-icon mb-2" style="color: #95a5a6;">
                                <i class="bi bi-hourglass-split fs-1"></i>
                            </div>
                            <h6 class="card-subtitle text-muted">Sin Ingresar</h6>
                            <p class="card-text fs-2 fw-bold counter" data-target="{{ $totalSinIngresar }}"
                                style="color: #95a5a6;">0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg">
                    <div class="card indicator-card h-100 border-0 shadow-sm"
                        style="border-left: 4px solid #f39c12 !important;">
                        <div class="card-body text-center">
                            <div class="indicator-icon mb-2" style="color: #f39c12;">
                                <i class="bi bi-gear-wide-connected fs-1"></i>
                            </div>
                            <h6 class="card-subtitle text-muted">En Proceso</h6>
                            <p class="card-text fs-2 fw-bold counter" data-target="{{ $totalEnProceso }}"
                                style="color: #f39c12;">0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg">
                    <div class="card indicator-card h-100 border-0 shadow-sm"
                        style="border-left: 4px solid #27ae60 !important;">
                        <div class="card-body text-center">
                            <div class="indicator-icon mb-2" style="color: #27ae60;">
                                <i class="bi bi-check-circle-fill fs-1"></i>
                            </div>
                            <h6 class="card-subtitle text-muted">Completadas</h6>
                            <p class="card-text fs-2 fw-bold counter" data-target="{{ $totalCompletadas }}"
                                style="color: #27ae60;">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos principales -->
            <div class="row mb-4 g-4">
                <!-- Gráfico de Bioensayos (Barras) -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Muestras por Bioensayo
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="bioassayBarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Bioensayos (Torta) -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Distribución por
                                Bioensayo</h5>
                        </div>
                        <div class="card-body">
                            <div id="bioassayPieChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-4 justify-content-center">
                <!-- Estado de muestras (Donut) -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0"><i class="bi bi-diagram-3-fill me-2 text-warning"></i>Estado de Muestras</h5>
                        </div>
                        <div class="card-body">
                            <div id="statusDonutChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de datos -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="mb-0"><i class="bi bi-table me-2 text-secondary"></i>Últimas Muestras Registradas
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover" id="samplesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Bioensayo</th>
                                        <th>Cliente</th>
                                        <th>Estado</th>
                                        <th>Fecha Recepción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sampleRows as $row)
                                        <tr>
                                            <td><span class="badge bg-secondary">#{{ $row['code'] ?? $row['id'] }}</span></td>
                                            <td>{{ $row['bioassays'] }}</td>
                                            <td>{{ $row['client'] }}</td>
                                            <td>
                                                @if ($row['state'] === 'Completada')
                                                    <span class="badge bg-success">Completada</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">En Proceso</span>
                                                @endif
                                            </td>
                                            <td>{{ $row['received_at'] ? \Carbon\Carbon::parse($row['received_at'])->format('Y-m-d') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No hay muestras ingresadas
                                                todavía.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .indicator-card {
            transition: all 0.3s ease;
            border-left-width: 4px !important;
            border-left-style: solid !important;
        }

        .indicator-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .indicator-icon {
            transition: transform 0.3s ease;
        }

        .indicator-card:hover .indicator-icon {
            transform: scale(1.2);
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            font-weight: 600;
        }

        .text-purple {
            color: #9b59b6 !important;
        }

        /* Animación de entrada para las tarjetas */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .indicator-card,
        .card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .row .col-md-4:nth-child(1) .indicator-card {
            animation-delay: 0.1s;
        }

        .row .col-md-4:nth-child(2) .indicator-card {
            animation-delay: 0.2s;
        }

        .row .col-md-4:nth-child(3) .indicator-card {
            animation-delay: 0.3s;
        }

        .row .col-md-6:nth-child(4) .indicator-card {
            animation-delay: 0.4s;
        }

        .row .col-md-6:nth-child(5) .indicator-card {
            animation-delay: 0.5s;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1) !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        window.addEventListener('load', function () {

            // ========== ANIMACIÓN DE CONTADORES ==========
            const counters = document.querySelectorAll('.counter');
            const speed = 50;

            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                const increment = target / speed;

                const updateCount = () => {
                    const count = +counter.innerText;
                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(updateCount, 30);
                    } else {
                        counter.innerText = target;
                    }
                };

                updateCount();
            });

            // ========== DATOS REALES (calculados en SampleEntryController@dashboard) ==========
            const bioassayTotals = @json($bioassayTotals);
            const bioassays = Object.keys(bioassayTotals);
            const bioassayData = Object.values(bioassayTotals);

            const gradientColors = [
                '#3498db', '#2ecc71', '#e74c3c', '#f39c12',
                '#9b59b6', '#1abc9c', '#e67e22', '#34495e'
            ];

            // ========== GRÁFICO DE BARRAS - BIOENSAYOS ==========
            const barCtx = document.getElementById('bioassayBarChart');
            if (barCtx) {
                new Chart(barCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: bioassays,
                        datasets: [{
                            label: 'Cantidad de Muestras',
                            data: bioassayData,
                            backgroundColor: gradientColors,
                            borderColor: gradientColors.map(c => c),
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            duration: 2000,
                            easing: 'easeOutBounce'
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            }

            // ========== GRÁFICO DE TORTA - BIOENSAYOS (ApexCharts) ==========
            const pieEl = document.querySelector('#bioassayPieChart');
            if (pieEl) {
                new ApexCharts(pieEl, {
                    chart: {
                        type: 'pie',
                        height: 350,
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 1500,
                            animateGradually: { enabled: true, delay: 150 }
                        }
                    },
                    series: bioassayData,
                    labels: bioassays,
                    colors: gradientColors,
                    legend: {
                        position: 'bottom',
                        fontSize: '11px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => val.toFixed(1) + '%'
                    },
                    tooltip: {
                        y: { formatter: (val) => val + ' muestras' }
                    }
                }).render();
            }

            // ========== GRÁFICO DONUT - ESTADOS ==========
            const donutEl = document.querySelector('#statusDonutChart');
            if (donutEl) {
                new ApexCharts(donutEl, {
                    chart: {
                        type: 'donut',
                        height: 350,
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 1500
                        }
                    },
                    series: [
                        {{ $totalCompletadas }},
                        {{ $totalEnProceso }},
                        {{ $totalSinIngresar }},
                        {{ $totalRechazadas }}
                    ],
                    labels: ['Completadas', 'En Proceso', 'Sin Ingresar', 'Rechazadas'],
                    colors: ['#27ae60', '#f39c12', '#95a5a6', '#e74c3c'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: { show: true },
                                    value: { show: true, fontSize: '22px', fontWeight: 600 },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        fontSize: '14px',
                                        formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        }
                    },
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: false }
                }).render();
            }

            // ========== DATATABLES ==========
            $('#samplesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                order: [[4, 'desc']]
            });
        });
    </script>
@endpush