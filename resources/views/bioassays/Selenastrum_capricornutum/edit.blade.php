{{-- resources/views/bioassays/selenastrum_capricornutum/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container px-4">
    <!-- Logo -->
    <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        Editar Bioensayo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #84cc16;">
        Selenastrum capricornutum
    </h2>
    <p class="text-center text-muted mb-4">RT-01.05 | Versión: 03 | Vigencia: 01.10.2025</p>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <strong><i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $measurements = $selenastrum_capricornutum->measurements ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('selenastrum-capricornutum.update', $selenastrum_capricornutum->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- Campo oculto para temporizador --}}
        <input type="hidden" name="timer_start" id="timer_start" 
               value="{{ old('timer_start', $selenastrum_capricornutum->timer_start) }}">

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-clipboard-list me-2"></i>Datos Generales
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0 modern-table">
                        <thead>
                            <tr>
                                <th>Muestra</th>
                                <th>Matriz</th>
                                <th>Fecha y hora de inicio</th>
                                <th>Fecha y hora de término</th>
                                <th>Analista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable bg-light" 
                                           name="sample" 
                                           value="{{ old('sample', $selenastrum_capricornutum->sample) }}" 
                                           readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="matrix" 
                                           value="{{ old('matrix', $selenastrum_capricornutum->matrix) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_start" 
                                           value="{{ old('bioassay_start', $selenastrum_capricornutum->bioassay_start) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_end" 
                                           value="{{ old('bioassay_end', $selenastrum_capricornutum->bioassay_end) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="analyst" 
                                           value="{{ old('analyst', $selenastrum_capricornutum->analyst) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= DATOS PRELIMINARES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-flask me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Volumen inóculo inicial</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="initial_inoculum" 
                                   value="{{ old('initial_inoculum', $selenastrum_capricornutum->initial_inoculum) }}">
                            <span class="input-group-text unit-badge">10⁴ cel/ml</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha de Cultivo (Stock)</label>
                        <input type="text" class="form-control navigable datepicker" 
                               name="stock_culture_date" 
                               value="{{ old('stock_culture_date', $selenastrum_capricornutum->stock_culture_date) }}" 
                               placeholder="Seleccione fecha">
                    </div>
                </div>

                {{-- TEMPORIZADOR --}}
                <div class="timer-container my-4 p-3" id="timerContainer">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-lime btn-lg" id="btnStartTimer" onclick="startTimer()">
                                <i class="fas fa-play me-2"></i>Iniciar Ensayo
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="btnResetTimer" onclick="resetTimer()" style="display: none;">
                                <i class="fas fa-redo me-1"></i>Reiniciar
                            </button>
                        </div>
                        <div class="timer-display" id="timerDisplay">
                            <div class="timer-icon">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div class="timer-values">
                                <span class="timer-elapsed" id="timerElapsed">00:00:00</span>
                                <span class="timer-separator">/</span>
                                <span class="timer-limit">96:00:00</span>
                            </div>
                            <div class="timer-status" id="timerStatus">
                                <span class="badge bg-secondary">Sin iniciar</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barra de progreso --}}
                    <div class="progress-container mt-3">
                        <div class="progress-labels d-flex justify-content-between mb-1">
                            <small class="text-muted">Inicio</small>
                            <small class="text-primary fw-bold">96h (Límite)</small>
                            <small class="text-warning fw-bold">106h (Gracia)</small>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress" style="height: 25px; border-radius: 12px; background: #e9ecef;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="progressBar" 
                                     role="progressbar" 
                                     style="width: 0%; border-radius: 12px;"
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="progress-text" id="progressText">0%</span>
                                </div>
                            </div>
                            {{-- Marcadores de tiempo --}}
                            <div class="progress-markers">
                                <div class="marker marker-24h" style="left: 22.64%;" title="24 horas">
                                    <div class="marker-line marker-line-light"></div>
                                    <small class="marker-label">24h</small>
                                </div>
                                <div class="marker marker-48h" style="left: 45.28%;" title="48 horas">
                                    <div class="marker-line marker-line-light"></div>
                                    <small class="marker-label">48h</small>
                                </div>
                                <div class="marker marker-72h" style="left: 67.92%;" title="72 horas">
                                    <div class="marker-line marker-line-light"></div>
                                    <small class="marker-label">72h</small>
                                </div>
                                <div class="marker marker-limit" style="left: 90.57%;" title="96 horas">
                                    <div class="marker-line"></div>
                                    <small class="marker-label">96h</small>
                                </div>
                                <div class="marker marker-grace" style="left: 100%;" title="106 horas (fin gracia)">
                                    <div class="marker-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="time-remaining mt-2 text-center" id="timeRemaining">
                            <small class="text-muted">Tiempo restante: <strong>96:00:00</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= CRECIMIENTO Y PH (CONTROL) ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-chart-line me-2"></i>Crecimiento y pH (Control)
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Réplicas de Control (RC)</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th class="table-header-time">24h</th>
                                <th class="table-header-time">48h</th>
                                <th class="table-header-time">72h</th>
                                <th class="table-header-96h">RC1 (96h)</th>
                                <th class="table-header-96h">RC2 (96h)</th>
                                <th class="table-header-96h">RC3 (96h)</th>
                                <th class="table-header-96h">RC4 (96h)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc24h" 
                                           value="{{ old('rc24h', $selenastrum_capricornutum->rc24h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc48h" 
                                           value="{{ old('rc48h', $selenastrum_capricornutum->rc48h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc72h" 
                                           value="{{ old('rc72h', $selenastrum_capricornutum->rc72h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc196h" 
                                           value="{{ old('rc196h', $selenastrum_capricornutum->rc196h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc296h" 
                                           value="{{ old('rc296h', $selenastrum_capricornutum->rc296h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc396h" 
                                           value="{{ old('rc396h', $selenastrum_capricornutum->rc396h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc496h" 
                                           value="{{ old('rc496h', $selenastrum_capricornutum->rc496h) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">pH Inicial</label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph_initial" 
                               value="{{ old('ph_initial', $selenastrum_capricornutum->ph_initial) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">pH Final</label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph_final" 
                               value="{{ old('ph_final', $selenastrum_capricornutum->ph_final) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tasa de Crecimiento Control</label>
                        <input type="number" step="0.0001" class="form-control navigable" 
                               name="control_growth_rate" 
                               value="{{ old('control_growth_rate', $selenastrum_capricornutum->control_growth_rate) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MEDICIONES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-table me-2"></i>Mediciones
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Tabla de Mediciones</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table compact-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 40px;">#</th>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Muestra / Conc.</th>
                                <th colspan="2">pH</th>
                                <th colspan="3" class="table-header-r1">R1</th>
                                <th colspan="3" class="table-header-r2">R2</th>
                                <th colspan="3" class="table-header-r3">R3</th>
                                <th colspan="3" class="table-header-r4">R4</th>
                                <th colspan="4" class="table-header-96h">96h</th>
                                <th rowspan="2" class="align-middle">Tasa Crec.</th>
                                <th rowspan="2" class="align-middle">% Tasa Crec.</th>
                                <th rowspan="2" class="align-middle">% Inhib.</th>
                                <th rowspan="2" class="align-middle">CE<sub>50</sub></th>
                            </tr>
                            <tr>
                                <th>Ini</th>
                                <th>Fin</th>
                                <th>24h</th><th>48h</th><th>72h</th>
                                <th>24h</th><th>48h</th><th>72h</th>
                                <th>24h</th><th>48h</th><th>72h</th>
                                <th>24h</th><th>48h</th><th>72h</th>
                                <th>R1</th><th>R2</th><th>R3</th><th>R4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    $row = $measurements[$i] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">{{ $i }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][sample_or_concentration]" 
                                               value="{{ old('measurements.'.$i.'.sample_or_concentration', $row['sample_or_concentration'] ?? '') }}"
                                               placeholder="Conc.">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ph_initial]" 
                                               value="{{ old('measurements.'.$i.'.ph_initial', $row['ph_initial'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ph_final]" 
                                               value="{{ old('measurements.'.$i.'.ph_final', $row['ph_final'] ?? '') }}">
                                    </td>
                                    {{-- R1: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r1_24h]" 
                                               value="{{ old('measurements.'.$i.'.r1_24h', $row['r1_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r1_48h]" 
                                               value="{{ old('measurements.'.$i.'.r1_48h', $row['r1_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r1_72h]" 
                                               value="{{ old('measurements.'.$i.'.r1_72h', $row['r1_72h'] ?? '') }}">
                                    </td>
                                    {{-- R2: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r2_24h]" 
                                               value="{{ old('measurements.'.$i.'.r2_24h', $row['r2_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r2_48h]" 
                                               value="{{ old('measurements.'.$i.'.r2_48h', $row['r2_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r2_72h]" 
                                               value="{{ old('measurements.'.$i.'.r2_72h', $row['r2_72h'] ?? '') }}">
                                    </td>
                                    {{-- R3: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r3_24h]" 
                                               value="{{ old('measurements.'.$i.'.r3_24h', $row['r3_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r3_48h]" 
                                               value="{{ old('measurements.'.$i.'.r3_48h', $row['r3_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r3_72h]" 
                                               value="{{ old('measurements.'.$i.'.r3_72h', $row['r3_72h'] ?? '') }}">
                                    </td>
                                    {{-- R4: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r4_24h]" 
                                               value="{{ old('measurements.'.$i.'.r4_24h', $row['r4_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r4_48h]" 
                                               value="{{ old('measurements.'.$i.'.r4_48h', $row['r4_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r4_72h]" 
                                               value="{{ old('measurements.'.$i.'.r4_72h', $row['r4_72h'] ?? '') }}">
                                    </td>
                                    {{-- 96h: R1, R2, R3, R4 --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r196h]" 
                                               value="{{ old('measurements.'.$i.'.r196h', $row['r196h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r296h]" 
                                               value="{{ old('measurements.'.$i.'.r296h', $row['r296h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r396h]" 
                                               value="{{ old('measurements.'.$i.'.r396h', $row['r396h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r496h]" 
                                               value="{{ old('measurements.'.$i.'.r496h', $row['r496h'] ?? '') }}">
                                    </td>
                                    {{-- Resultados --}}
                                    <td>
                                        <input type="number" step="0.0001" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][growth_rate]" 
                                               value="{{ old('measurements.'.$i.'.growth_rate', $row['growth_rate'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][percent_growth_rate]" 
                                               value="{{ old('measurements.'.$i.'.percent_growth_rate', $row['percent_growth_rate'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][percent_inhibition]" 
                                               value="{{ old('measurements.'.$i.'.percent_inhibition', $row['percent_inhibition'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ce50]" 
                                               value="{{ old('measurements.'.$i.'.ce50', $row['ce50'] ?? '') }}">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= RESULTADOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-chart-bar me-2"></i>Resultados y Observaciones
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">EC<sub>50</sub> Detalle</label>
                        <input type="text" class="form-control navigable" 
                               name="ce50_detail" 
                               value="{{ old('ce50_detail', $selenastrum_capricornutum->ce50_detail) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Coeficiente de Variación (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="variation_coefficient" 
                                   value="{{ old('variation_coefficient', $selenastrum_capricornutum->variation_coefficient) }}">
                            <span class="input-group-text unit-badge">%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control navigable" name="observations" rows="3" 
                              placeholder="Ingrese observaciones adicionales...">{{ old('observations', $selenastrum_capricornutum->observations) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ================= CRITERIOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-secondary-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-check-circle me-2"></i>Criterios de Aceptabilidad
            </div>
            <div class="card-body bg-light p-3">
                <ul class="criteria-list mb-3">
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Densidad celular inicial: <strong>10⁴ cel/ml</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Tasa de crecimiento del control: <strong>≥ 0.92 día⁻¹</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Coeficiente de variación del control: <strong>≤ 7%</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        pH de las muestras: <strong>7.5 a 8.5 unidades</strong>
                    </li>
                </ul>
                <div class="mt-3 pt-3 border-top">
                    <p class="mb-0 text-muted">V°B° _____________________</p>
                </div>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-lime btn-lg px-4">
                <i class="fas fa-save me-2"></i>Actualizar Bioensayo
            </button>
            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            @php
                $sampleEntry = \App\Models\SampleEntry::where('internal_sample_code', $selenastrum_capricornutum->sample)->first();
            @endphp
            @if($sampleEntry)
                <a href="{{ route('sample_entries.show', $sampleEntry->id) }}" class="btn btn-secondary btn-lg px-4">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Muestra
                </a>
            @else
                <a href="{{ route('sample_entries.index') }}" class="btn btn-secondary btn-lg px-4">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            @endif
        </div>
    </form>
</div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('styles')
<style>
    /* ============================================
       COLORES PERSONALIZADOS - VERDE LIMA
       ============================================ */
    .bg-lime-subtle {
        background-color: #ecfccb !important;
    }

    .btn-lime {
        background-color: #84cc16;
        border-color: #84cc16;
        color: white;
    }

    .btn-lime:hover {
        background-color: #65a30d;
        border-color: #65a30d;
        color: white;
    }

    /* ============================================
       ESTILOS GENERALES DE TABLAS
       ============================================ */
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    .modern-table thead th {
        background: #e9ecef;
        color: #495057;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 8px 4px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody td {
        padding: 4px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .modern-table input {
        border: 1px solid #ced4da;
        text-align: center;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0.75rem;
        padding: 2px 4px;
    }

    .modern-table input:focus {
        border-color: #84cc16;
        box-shadow: 0 0 0 0.15rem rgba(132, 204, 22, 0.25);
    }

    .compact-table input {
        height: 26px;
    }

    .compact-table td {
        padding: 2px !important;
    }

    /* ============================================
       TÍTULOS DE SECCIÓN
       ============================================ */
    .section-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1.3rem;
        font-weight: 500;
        color: #84cc16;
        position: relative;
        padding: 10px 0;
    }

    .section-title span {
        background: #f8f9fa;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    .section-title::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(to right, transparent, #84cc16, transparent);
    }

    /* ============================================
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-time {
        background: #dbeafe !important;
        color: #1e40af;
        font-size: 0.7rem !important;
    }

    .table-header-96h {
        background: #ecfccb !important;
        color: #3f6212;
        font-size: 0.7rem !important;
    }

    .table-header-r1 {
        background: #fef3c7 !important;
        color: #92400e;
        font-size: 0.7rem !important;
    }

    .table-header-r2 {
        background: #dbeafe !important;
        color: #1e40af;
        font-size: 0.7rem !important;
    }

    .table-header-r3 {
        background: #fce7f3 !important;
        color: #9d174d;
        font-size: 0.7rem !important;
    }

    .table-header-r4 {
        background: #e0e7ff !important;
        color: #3730a3;
        font-size: 0.7rem !important;
    }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge {
        background: #6c757d;
        color: white;
        font-weight: 500;
        font-size: 0.8rem;
        min-width: 80px;
        justify-content: center;
    }

    /* ============================================
       NAVEGACIÓN CON FLECHAS
       ============================================ */
    .navigable:focus {
        outline: 2px solid #0d6efd;
        outline-offset: 1px;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }

    /* ============================================
       LISTA DE CRITERIOS
       ============================================ */
    .criteria-list {
        list-style: none;
        padding-left: 0;
    }

    .criteria-list li {
        padding: 8px 0;
        font-size: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .criteria-list li:last-child {
        border-bottom: none;
    }

    /* ============================================
       TEMPORIZADOR
       ============================================ */
    .timer-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .timer-container.timer-running {
        border-color: #84cc16;
        box-shadow: 0 0 15px rgba(132, 204, 22, 0.2);
    }

    .timer-container.timer-warning {
        border-color: #ffc107;
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.3);
        background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    }

    .timer-container.timer-grace {
        border-color: #fd7e14;
        box-shadow: 0 0 15px rgba(253, 126, 20, 0.3);
        background: linear-gradient(135deg, #fff5e6 0%, #ffe5cc 100%);
        animation: pulse-grace 2s infinite;
    }

    .timer-container.timer-expired {
        border-color: #dc3545;
        box-shadow: 0 0 20px rgba(220, 53, 69, 0.4);
        background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%);
        animation: pulse-expired 1s infinite;
    }

    @keyframes pulse-grace {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.005); }
    }

    @keyframes pulse-expired {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .timer-display {
        display: flex;
        align-items: center;
        gap: 15px;
        background: white;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .timer-icon {
        font-size: 1.8rem;
        color: #6c757d;
    }

    .timer-icon.running {
        color: #84cc16;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .timer-values {
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .timer-elapsed {
        color: #84cc16;
    }

    .timer-elapsed.warning { color: #ffc107; }
    .timer-elapsed.grace { color: #fd7e14; }
    .timer-elapsed.expired { color: #dc3545; }

    .timer-separator { color: #6c757d; margin: 0 5px; }
    .timer-limit { color: #6c757d; }

    /* ============================================
       BARRA DE PROGRESO
       ============================================ */
    .progress-wrapper { position: relative; }

    .progress-bar {
        transition: width 1s linear, background-color 0.5s ease;
    }

    .progress-bar.bg-lime {
        background: linear-gradient(90deg, #84cc16 0%, #bef264 100%) !important;
    }

    .progress-bar.bg-warning {
        background: linear-gradient(90deg, #ffc107 0%, #ffda6a 100%) !important;
    }

    .progress-bar.bg-orange {
        background: linear-gradient(90deg, #fd7e14 0%, #ffb380 100%) !important;
    }

    .progress-bar.bg-danger {
        background: linear-gradient(90deg, #dc3545 0%, #f17983 100%) !important;
    }

    .progress-text {
        font-size: 0.85rem;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .progress-markers {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
    }

    .marker {
        position: absolute;
        top: -8px; bottom: -8px;
        width: 2px;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .marker-line {
        width: 2px;
        height: 100%;
        border-radius: 2px;
        background: #0d6efd;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
    }

    .marker-line-light {
        background: #adb5bd;
        box-shadow: none;
    }

    .marker-label {
        position: absolute;
        bottom: -20px;
        font-size: 0.7rem;
        color: #6c757d;
    }

    .marker-grace .marker-line {
        background: #fd7e14;
        box-shadow: 0 0 5px rgba(253, 126, 20, 0.5);
    }

    /* ============================================
       IMPRESIÓN
       ============================================ */
    @media print {
        @page { size: A4 landscape; margin: 5mm; }
        body { font-size: 7pt; line-height: 1.1; }
        .btn, button, nav, .navbar, .no-print, .timer-container { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; margin-bottom: 5px !important; break-inside: avoid; }
        .card-header { background: #f0f0f0 !important; color: #000 !important; font-weight: bold; padding: 3px 6px !important; font-size: 9pt !important; }
        .card-body { padding: 4px !important; }
        .modern-table input { border: none !important; background: transparent !important; font-size: 6pt; height: 16px !important; }
        .modern-table th, .modern-table td { padding: 1px !important; font-size: 6pt; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const LIMIT_HOURS = 96;
    const GRACE_HOURS = 10;
    const TOTAL_HOURS = LIMIT_HOURS + GRACE_HOURS;
    const LIMIT_MS = LIMIT_HOURS * 60 * 60 * 1000;
    const TOTAL_MS = TOTAL_HOURS * 60 * 60 * 1000;

    let timerState = { startTime: null, interval: null };

    flatpickr(".datetimepicker", { enableTime: true, time_24hr: true, dateFormat: "Y-m-d H:i", locale: "es", allowInput: true });
    flatpickr(".datepicker", { dateFormat: "Y-m-d", locale: "es", allowInput: true });

    const navigableElements = document.querySelectorAll('.navigable');
    navigableElements.forEach((element, index) => {
        element.addEventListener('keydown', function(e) {
            let targetIndex = index;
            if (e.key === 'ArrowDown' || (e.key === 'Enter' && !e.shiftKey)) { e.preventDefault(); targetIndex = index + 1; }
            else if (e.key === 'ArrowUp' || (e.key === 'Enter' && e.shiftKey)) { e.preventDefault(); targetIndex = index - 1; }
            else if (e.key === 'ArrowRight' && this.selectionStart === this.value.length) { e.preventDefault(); targetIndex = index + 1; }
            else if (e.key === 'ArrowLeft' && this.selectionStart === 0) { e.preventDefault(); targetIndex = index - 1; }
            if (targetIndex >= 0 && targetIndex < navigableElements.length && targetIndex !== index) {
                navigableElements[targetIndex].focus();
                if (navigableElements[targetIndex].select) navigableElements[targetIndex].select();
            }
        });
    });

    window.startTimer = function() {
        const now = Date.now();
        timerState.startTime = now;
        document.getElementById('timer_start').value = now;
        document.getElementById('btnStartTimer').style.display = 'none';
        document.getElementById('btnResetTimer').style.display = 'inline-block';
        updateTimerDisplay();
        timerState.interval = setInterval(updateTimerDisplay, 1000);
        Swal.fire({ icon: 'success', title: 'Temporizador Iniciado', text: `Tienes ${LIMIT_HOURS} horas para completar el ensayo (+ ${GRACE_HOURS}h de gracia).`, timer: 3000, showConfirmButton: false });
    };

    window.resetTimer = function() {
        Swal.fire({
            title: '¿Reiniciar temporizador?', text: "Esta acción reiniciará el contador a cero.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, reiniciar', cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                if (timerState.interval) clearInterval(timerState.interval);
                timerState.startTime = null;
                document.getElementById('timer_start').value = '';
                document.getElementById('btnStartTimer').style.display = 'inline-block';
                document.getElementById('btnResetTimer').style.display = 'none';
                resetTimerUI();
            }
        });
    };

    function updateTimerDisplay() {
        if (!timerState.startTime) return;
        const elapsed = Date.now() - timerState.startTime;
        const percentage = Math.min((elapsed / TOTAL_MS) * 100, 100);
        document.getElementById('timerElapsed').textContent = formatTime(elapsed);
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = `${percentage}%`;
        document.getElementById('progressText').textContent = `${percentage.toFixed(1)}%`;
        const remaining = Math.max(LIMIT_MS - elapsed, 0);
        const container = document.getElementById('timerContainer');
        const elapsedSpan = document.getElementById('timerElapsed');
        const statusSpan = document.getElementById('timerStatus');
        const remainingContainer = document.getElementById('timeRemaining');
        const timerIcon = container.querySelector('.timer-icon');
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        elapsedSpan.classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-lime', 'bg-warning', 'bg-orange', 'bg-danger');
        timerIcon.classList.add('running');
        if (elapsed >= TOTAL_MS) {
            container.classList.add('timer-expired'); elapsedSpan.classList.add('expired'); progressBar.classList.add('bg-danger');
            statusSpan.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>¡Tiempo Expirado!</span>';
            remainingContainer.innerHTML = '<small class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Tiempo de gracia agotado</small>';
        } else if (elapsed >= LIMIT_MS) {
            container.classList.add('timer-grace'); elapsedSpan.classList.add('grace'); progressBar.classList.add('bg-orange');
            statusSpan.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Período de Gracia</span>';
            remainingContainer.innerHTML = `<small class="text-warning fw-bold"><i class="fas fa-clock me-1"></i>Gracia restante: <strong>${formatTime(TOTAL_MS - elapsed)}</strong></small>`;
        } else if (elapsed >= LIMIT_MS * 0.9) {
            container.classList.add('timer-warning'); elapsedSpan.classList.add('warning'); progressBar.classList.add('bg-warning');
            statusSpan.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>¡Poco Tiempo!</span>';
            remainingContainer.innerHTML = `<small class="text-warning"><i class="fas fa-clock me-1"></i>Tiempo restante: <strong>${formatTime(remaining)}</strong></small>`;
        } else {
            container.classList.add('timer-running'); progressBar.classList.add('bg-lime');
            statusSpan.innerHTML = '<span class="badge bg-success"><i class="fas fa-play me-1"></i>En Progreso</span>';
            remainingContainer.innerHTML = `<small class="text-muted"><i class="fas fa-clock me-1"></i>Tiempo restante: <strong>${formatTime(remaining)}</strong></small>`;
        }
    }

    function resetTimerUI() {
        const container = document.getElementById('timerContainer');
        const progressBar = document.getElementById('progressBar');
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        document.getElementById('timerElapsed').classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-lime', 'bg-warning', 'bg-orange', 'bg-danger');
        container.querySelector('.timer-icon').classList.remove('running');
        document.getElementById('timerElapsed').textContent = '00:00:00';
        progressBar.style.width = '0%';
        document.getElementById('progressText').textContent = '0%';
        document.getElementById('timerStatus').innerHTML = '<span class="badge bg-secondary">Sin iniciar</span>';
        document.getElementById('timeRemaining').innerHTML = '<small class="text-muted">Tiempo restante: <strong>96:00:00</strong></small>';
    }

    function formatTime(ms) {
        const totalSeconds = Math.floor(ms / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    const savedStart = document.getElementById('timer_start').value;
    if (savedStart && !isNaN(parseInt(savedStart))) {
        timerState.startTime = parseInt(savedStart);
        document.getElementById('btnStartTimer').style.display = 'none';
        document.getElementById('btnResetTimer').style.display = 'inline-block';
        updateTimerDisplay();
        timerState.interval = setInterval(updateTimerDisplay, 1000);
    }

    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Confirmar actualización?', text: "Se guardarán los cambios del bioensayo de Selenastrum capricornutum.",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#84cc16', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save me-1"></i> Sí, actualizar', cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush