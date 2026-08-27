@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc" style="height: 80px; display: block; margin: 0 auto 20px auto;">

        <!-- Título -->
        <h1 class="mb-4 text-secondary text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
            Detalle de Ingreso de Muestra
        </h1>

        {{-- ===================== INGRESO ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white"
                style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                Información de Ingreso
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Fecha y Hora de Recepción</label>
                        <input type="text" class="form-control bg-white" value="{{ $sampleEntry->received_at }}"
                            readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Código Interno</label>
                        <input type="text" class="form-control bg-white" value="{{ $sampleEntry->internal_sample_code }}"
                            readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tipo de Muestra (Matriz)</label>
                        @php
                            $matrixLabels = [
                                'ASP' => 'ASP (Agua Superficial)',
                                'AM' => 'AM (Agua de Mar)',
                                'AR' => 'AR (Agua Residual)',
                                'ASB' => 'ASB (Agua Subterránea)',
                                'SM' => 'SM (Sedimento Marino)',
                                'SL' => 'SL (Sedimento Lacustre)',
                                'SA' => 'SA (Sedimento Acuático)',
                                'SQ' => 'SQ (Sustancia Química)',
                            ];
                        @endphp
                        <input type="text" class="form-control bg-white"
                            value="{{ $matrixLabels[$sampleEntry->sample_type] ?? $sampleEntry->sample_type }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Concentración de la Muestra</label>
                        <div class="concentration-wrapper">
                            <div class="input-group">
                                <input type="text" class="form-control bg-white"
                                    value="{{ $sampleEntry->sample_concentration }}" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                            @if ($sampleEntry->sample_concentration)
                                <div class="concentration-alternatives mt-2">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control bg-white"
                                                    value="{{ number_format($sampleEntry->sample_concentration * 10000, 4) }}"
                                                    readonly>
                                                <span class="input-group-text">PPM</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control bg-white"
                                                    value="{{ number_format($sampleEntry->sample_concentration * 10000000, 1) }}"
                                                    readonly>
                                                <span class="input-group-text">PPB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Fecha de Lectura de Parámetros</label>
                        <input type="text" class="form-control bg-white"
                            value="{{ $sampleEntry->parameter_reading_date }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Analista</label>
                        <input type="text" class="form-control bg-white" value="{{ $sampleEntry->analyst }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">pH</label>
                        <input type="text" class="form-control bg-white" value="{{ $sampleEntry->ph }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Salinidad</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-white" value="{{ $sampleEntry->salinity }}"
                                readonly>
                            <span class="input-group-text unit-badge">S‰</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Conductividad</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-white" value="{{ $sampleEntry->conductivity }}"
                                readonly>
                            <span class="input-group-text unit-badge">μS/cm</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Oxígeno Disuelto</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-white"
                                value="{{ $sampleEntry->dissolved_oxygen }}" readonly>
                            <span class="input-group-text unit-badge">mg O₂/L</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Temperatura</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-white" value="{{ $sampleEntry->temperature }}"
                                readonly>
                            <span class="input-group-text unit-badge">°C</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control bg-white" rows="3" readonly>{{ $sampleEntry->observations }}</textarea>
                </div>
            </div>
        </div>

        {{-- ===================== BIOENSAYOS ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-info text-white"
                style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-flask me-2"></i>Bioensayos Asignados
            </div>
            <div class="card-body bg-light">
                <div class="row" id="bioassaysContainer">
                    @php
                        $allBioassays = [
                            'Daphnia magna Agudo',
                            'Daphnia magna Crónico',
                            'Isochrysis galbana',
                            'Selenastrum capricornutum',
                            'Tisbe biconicornis Agua',
                            'Tisbe biconicornis Sedimento',
                            'Arbacia spatuligera Estado Larval',
                            'Arbacia spatuligera Fecundación',
                        ];

                        // Mapeo de bioensayos a sus modelos y rutas
                        $bioConfig = [
                            'Daphnia magna Agudo' => [
                                'model' => \App\Models\DaphniaMagnaTemplate::class,
                                'route' => 'daphnia-magna.edit',
                                'color' => 'success',
                            ],
                            'Daphnia magna Crónico' => [
                                'model' => \App\Models\DaphniaMagnaChronic::class,
                                'route' => 'daphnia-magna-chronic.edit',
                                'color' => 'success',
                            ],
                            'Isochrysis galbana' => [
                                'model' => \App\Models\IsochrysisGalbana::class,
                                'route' => 'isochrysis-galbana.edit',
                                'color' => 'info',
                            ],
                            'Selenastrum capricornutum' => [
                                'model' => \App\Models\SelenastrumCapricornutum::class, // No implementado aún
                                'route' => 'selenastrum-capricornutum.edit',
                                'color' => 'secondary',
                            ],
                            'Tisbe biconicornis Agua' => [
                                'model' => \App\Models\TisbeLongicornisWater::class,
                                'route' => 'tisbe-longicornis-water.edit',
                                'color' => 'purple', // Púrpura (necesita CSS: .bg-purple { background-color: #6f42c1 !important; })
                            ],
                            'Tisbe biconicornis Sedimento' => [
                                'model' => \App\Models\TisbeLongicornisRiles::class,
                                'route' => 'tisbe-longicornis-riles.edit',
                                'color' => 'warning', // Naranja
                            ],
                            'Arbacia spatuligera Estado Larval' => [
                                'model' => \App\Models\ArbaciaLarvalStage::class,
                                'route' => 'arbacia-larval-stage.edit',
                                'color' => 'secondary',
                            ],
                            'Arbacia spatuligera Fecundación' => [
                                'model' => \App\Models\ArbaciaFertilization::class,
                                'route' => 'arbacia-fertilization.edit',
                                'color' => 'pink',
                            ],
                        ];
                    @endphp

                    @foreach ($allBioassays as $index => $bio)
                        @php
                            $isAssigned = in_array($bio, $assignedBioassays ?? []);
                            $config = $bioConfig[$bio] ?? null;
                            $bioassayRecord = null;
                            $timerStatus = null;

                            // Buscar el registro del bioensayo si está asignado y tiene modelo
                            if ($isAssigned && $config && $config['model']) {
                                $bioassayRecord = $config['model']
                                    ::where('sample', $sampleEntry->internal_sample_code)
                                    ->first();

                                // Obtener estado del temporizador si existe
                                if ($bioassayRecord && isset($bioassayRecord->timer_status)) {
                                    $timerStatus = $bioassayRecord->timer_status;
                                } elseif ($bioassayRecord) {
                                    // Para Daphnia que tiene dos temporizadores
                                    $timerStatus =
                                        $bioassayRecord->preliminary_timer_status ??
                                        ($bioassayRecord->definitive_timer_status ?? null);
                                }
                            }
                        @endphp

                        <div class="col-md-6 mb-2">
                            <div
                                class="bioassay-item d-flex align-items-center justify-content-between {{ $isAssigned ? 'bioassay-assigned' : 'bioassay-disabled' }}">
                                <div class="d-flex align-items-center">
                                    <div class="bioassay-checkbox-wrapper">
                                        <input class="form-check-input m-0" type="checkbox" id="bio{{ $index }}"
                                            {{ $isAssigned ? 'checked' : '' }} disabled>
                                    </div>
                                    <label class="form-check-label ms-2 mb-0" for="bio{{ $index }}">
                                        {{ $bio }}
                                    </label>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    @if ($isAssigned)
                                        {{-- Badge de estado --}}
                                        <span class="badge bg-{{ $config['color'] ?? 'success' }}">Asignado</span>

                                        {{-- Indicador de temporizador si existe --}}
                                        @if ($timerStatus)
                                            @switch($timerStatus)
                                                @case('running')
                                                    <span class="badge bg-success" title="Temporizador en progreso">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                @break

                                                @case('warning')
                                                    <span class="badge bg-warning text-dark" title="¡Poco tiempo restante!">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </span>
                                                @break

                                                @case('grace')
                                                    <span class="badge bg-orange text-white" title="Período de gracia">
                                                        <i class="fas fa-hourglass-half"></i>
                                                    </span>
                                                @break

                                                @case('expired')
                                                    <span class="badge bg-danger" title="Tiempo expirado">
                                                        <i class="fas fa-times-circle"></i>
                                                    </span>
                                                @break
                                            @endswitch
                                        @endif

                                        {{-- Botón de acción --}}
                                        @if ($bioassayRecord && $config['route'])
                                            <a href="{{ route($config['route'], $bioassayRecord->id) }}"
                                                class="btn btn-sm btn-{{ $config['color'] ?? 'primary' }}">
                                                <i class="fas fa-edit"></i> Ir al ensayo
                                            </a>
                                        @elseif ($config && $config['model'] === null)
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-clock"></i> No implementado
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-warning" disabled>
                                                <i class="fas fa-exclamation-triangle"></i> Sin registro
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Leyenda de estados --}}
                @if (count($assignedBioassays ?? []) > 0)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <strong>Estados del temporizador:</strong>
                            <span class="badge bg-success ms-2"><i class="fas fa-clock"></i></span> En progreso
                            <span class="badge bg-warning text-dark ms-2"><i
                                    class="fas fa-exclamation-triangle"></i></span> Poco tiempo
                            <span class="badge bg-orange text-white ms-2"><i class="fas fa-hourglass-half"></i></span>
                            Período de gracia
                            <span class="badge bg-danger ms-2"><i class="fas fa-times-circle"></i></span> Expirado
                        </small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Estilos adicionales para el badge naranja --}}
        <style>
            .bg-orange {
                background-color: #fd7e14 !important;
            }
        </style>

        {{-- ===================== BASE TEMPLATE ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-primary text-white"
                style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                Información de la Plantilla
            </div>
            <div class="card-body bg-light">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="flex-grow-1">
                        <label class="form-label fw-bold mb-1">Título</label>
                        <input type="text" class="form-control-plaintext text-muted" value="INGRESO DE MUESTRAS"
                            readonly>
                    </div>
                    <div style="min-width: 150px;">
                        <label class="form-label fw-bold mb-1">Código</label>
                        <input type="text" class="form-control-plaintext text-muted" value="IN-01.01" readonly>
                    </div>
                    <div style="min-width: 100px;">
                        <label class="form-label fw-bold mb-1">Versión</label>
                        <input type="text" class="form-control-plaintext text-muted"
                            value="{{ $sampleEntry->template->version ?? '01' }}" readonly>
                    </div>
                    <div style="min-width: 120px;">
                        <label class="form-label fw-bold mb-1">Vigencia</label>
                        <input type="text" class="form-control-plaintext text-muted"
                            value="{{ $sampleEntry->template->validity ?? '01.09.2023' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('sample_entries.edit', $sampleEntry->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('sample_entries.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
@endsection

@push('head')
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================
                                       ESTILOS PARA LOS BIOENSAYOS
                                       ============================================ */
        .bioassay-item {
            padding: 12px 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            min-height: 48px;
        }

        .bioassay-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .bioassay-item.bioassay-assigned {
            background: #d1e7dd;
            border-color: #198754;
        }

        .bioassay-item.bioassay-disabled {
            background: #f8f9fa;
            opacity: 0.7;
        }

        .bioassay-checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .bioassay-checkbox-wrapper .form-check-input {
            width: 18px;
            height: 18px;
            cursor: default;
        }

        .bioassay-checkbox-wrapper .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        /* ============================================
                                       ESTILOS PARA CONCENTRACIÓN
                                       ============================================ */
        .concentration-wrapper {
            position: relative;
        }

        .concentration-alternatives {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #dee2e6;
            border-top: none;
        }

        .concentration-alternatives .input-group-text {
            font-size: 0.75rem;
            min-width: 45px;
            justify-content: center;
        }

        /* ============================================
                                       ESTILOS PARA UNIDADES DE MEDIDA
                                       ============================================ */
        .unit-badge {
            background: #6c757d;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            min-width: 70px;
            justify-content: center;
        }

        /* Input readonly con fondo distintivo */
        input[readonly].form-control,
        textarea[readonly].form-control {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        .form-control.bg-white[readonly] {
            background-color: #fff !important;
        }

        /* Textarea con estilo consistente */
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .bg-purple {
            background-color: #6f42c1 !important;
        }

        /* Botón purple */
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }

        .btn-purple:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
            color: white;
        }

        .btn-purple:focus,
        .btn-purple.focus {
            background-color: #5a32a3;
            border-color: #5a32a3;
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }

        .btn-purple:active,
        .btn-purple.active {
            background-color: #4a2789;
            border-color: #4a2789;
            color: white;
        }

        /* Fondo rosa */
        .bg-pink {
            background-color: #d63384 !important;
        }

        /* Fondo rosa claro */
        .bg-pink-light {
            background-color: #fce7f3 !important;
        }

        /* Botón pink */
        .btn-pink {
            background-color: #d63384;
            border-color: #d63384;
            color: white;
        }

        .btn-pink:hover {
            background-color: #b52b6f;
            /* rosa más oscuro para hover */
            border-color: #b52b6f;
            color: white;
        }

        .btn-pink:focus,
        .btn-pink.focus {
            background-color: #b52b6f;
            border-color: #b52b6f;
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(214, 51, 132, 0.25);
        }

        .btn-pink:active,
        .btn-pink.active {
            background-color: #9a245e;
            /* aún más oscuro para active */
            border-color: #9a245e;
            color: white;
        }
    </style>
@endpush
