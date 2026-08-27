{{-- resources/views/bioassays/tisbe_longicornis_riles/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Logo -->
    <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        Editar Bioensayo Agudo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #fd7e14;">
        Tisbe longicornis (Sustancias Químicas/Riles)
    </h2>
    <p class="text-center text-muted mb-4">RT-01.05 | Versión: 01 | Vigencia: 06.10.2025</p>

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
        // Extraer datos de las estructuras JSON
        $preliminary_table = $tisbe_longicornis_riles->preliminary_table ?? [];
        
        $def_24h_data = $tisbe_longicornis_riles->definitive_24h ?? [];
        $def_48h_data = $tisbe_longicornis_riles->definitive_48h ?? [];
        
        // Extraer valores de concentración
        $def_24h_conc_values = $def_24h_data['concentration_values'] ?? [];
        $def_48h_conc_values = $def_48h_data['concentration_values'] ?? [];
        
        // Extraer filas
        $def_24h_rows = $def_24h_data['rows'] ?? [];
        $def_48h_rows = $def_48h_data['rows'] ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('tisbe-longicornis-riles.update', $tisbe_longicornis_riles->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- Campos ocultos para guardar tiempos de inicio de temporizadores --}}
        <input type="hidden" name="preliminary_timer_start" id="preliminary_timer_start" 
               value="{{ old('preliminary_timer_start', $tisbe_longicornis_riles->preliminary_timer_start) }}">
        <input type="hidden" name="definitive_timer_start" id="definitive_timer_start" 
               value="{{ old('definitive_timer_start', $tisbe_longicornis_riles->definitive_timer_start) }}">

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-orange-subtle text-dark"
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
                                           value="{{ old('sample', $tisbe_longicornis_riles->sample) }}" 
                                           readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="matrix" 
                                           value="{{ old('matrix', $tisbe_longicornis_riles->matrix) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="start_time" 
                                           value="{{ old('start_time', $tisbe_longicornis_riles->preliminary_start_at) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="end_time" 
                                           value="{{ old('end_time', $tisbe_longicornis_riles->preliminary_end_at) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="analyst" 
                                           value="{{ old('analyst', $tisbe_longicornis_riles->analyst) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= ENSAYO PRELIMINAR ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-orange-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-flask me-2"></i>Ensayo Preliminar
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0 modern-table">
                        <thead>
                            <tr>
                                <th>Temperatura de la muestra (°C)</th>
                                <th>Fecha de agua Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" class="form-control navigable" 
                                               name="sample_temperature" 
                                               value="{{ old('sample_temperature', $tisbe_longicornis_riles->preliminary_sample_temperature) }}">
                                        <span class="input-group-text unit-badge">°C</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datepicker" 
                                           name="control_water_date" 
                                           value="{{ old('control_water_date', $tisbe_longicornis_riles->preliminary_control_water_date) }}" 
                                           placeholder="Seleccione fecha">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TEMPORIZADOR PRELIMINAR --}}
                <div class="timer-container my-4 p-3" id="preliminaryTimerContainer">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-orange btn-lg" id="btnStartPreliminary" onclick="startTimer('preliminary')">
                                <i class="fas fa-play me-2"></i>Iniciar Preliminar
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="btnResetPreliminary" onclick="resetTimer('preliminary')" style="display: none;">
                                <i class="fas fa-redo me-1"></i>Reiniciar
                            </button>
                        </div>
                        <div class="timer-display" id="preliminaryTimerDisplay">
                            <div class="timer-icon">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div class="timer-values">
                                <span class="timer-elapsed" id="preliminaryElapsed">00:00:00</span>
                                <span class="timer-separator">/</span>
                                <span class="timer-limit">48:00:00</span>
                            </div>
                            <div class="timer-status" id="preliminaryStatus">
                                <span class="badge bg-secondary">Sin iniciar</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barra de progreso --}}
                    <div class="progress-container mt-3">
                        <div class="progress-labels d-flex justify-content-between mb-1">
                            <small class="text-muted">Inicio</small>
                            <small class="text-primary fw-bold">48h (Límite)</small>
                            <small class="text-warning fw-bold">58h (Gracia)</small>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress" style="height: 25px; border-radius: 12px; background: #e9ecef;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="preliminaryProgressBar" 
                                     role="progressbar" 
                                     style="width: 0%; border-radius: 12px;"
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="progress-text" id="preliminaryProgressText">0%</span>
                                </div>
                            </div>
                            {{-- Marcadores de tiempo --}}
                            <div class="progress-markers">
                                <div class="marker marker-limit" style="left: 82.76%;" title="48 horas">
                                    <div class="marker-line"></div>
                                </div>
                                <div class="marker marker-grace" style="left: 100%;" title="58 horas (fin gracia)">
                                    <div class="marker-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="time-remaining mt-2 text-center" id="preliminaryTimeRemaining">
                            <small class="text-muted">Tiempo restante: <strong>48:00:00</strong></small>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4 mb-3 text-center section-title">
                    <span>Tabla de Mortalidad (Preliminar)</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">Concentración</th>
                                <th colspan="3" class="table-header-24h">24 horas</th>
                                <th colspan="3" class="table-header-48h">48 horas</th>
                            </tr>
                            <tr>
                                <th>Réplica 1</th>
                                <th>Réplica 2</th>
                                <th>∑ Muertas</th>
                                <th>Réplica 1</th>
                                <th>Réplica 2</th>
                                <th>∑ Muertas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 8; $i++)
                                @php
                                    $row = $preliminary_table[$i - 1] ?? [];
                                @endphp
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="pre_concentration_row{{ $i }}" 
                                               value="{{ old('pre_concentration_row'.$i, $row['concentration'] ?? '') }}" 
                                               placeholder="Conc. {{ $i }}">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable" 
                                               name="pre_24h_rep1_row{{ $i }}" 
                                               value="{{ old('pre_24h_rep1_row'.$i, $row['24h_rep1'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable" 
                                               name="pre_24h_rep2_row{{ $i }}" 
                                               value="{{ old('pre_24h_rep2_row'.$i, $row['24h_rep2'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable sum-field" 
                                               name="pre_24h_sum_row{{ $i }}" 
                                               value="{{ old('pre_24h_sum_row'.$i, $row['24h_sum'] ?? '') }}" 
                                               readonly>
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable" 
                                               name="pre_48h_rep1_row{{ $i }}" 
                                               value="{{ old('pre_48h_rep1_row'.$i, $row['48h_rep1'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable" 
                                               name="pre_48h_rep2_row{{ $i }}" 
                                               value="{{ old('pre_48h_rep2_row'.$i, $row['48h_rep2'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable sum-field" 
                                               name="pre_48h_sum_row{{ $i }}" 
                                               value="{{ old('pre_48h_sum_row'.$i, $row['48h_sum'] ?? '') }}" 
                                               readonly>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= ENSAYO DEFINITIVO ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-orange-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-vial me-2"></i>Ensayo Definitivo
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0 modern-table">
                        <thead>
                            <tr>
                                <th>Fecha y hora de inicio</th>
                                <th>Fecha y hora de término</th>
                                <th>Temperatura (°C)</th>
                                <th>Fecha de agua Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="def_start_time" 
                                           value="{{ old('def_start_time', $tisbe_longicornis_riles->definitive_start_at) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="def_end_time" 
                                           value="{{ old('def_end_time', $tisbe_longicornis_riles->definitive_end_at) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" class="form-control navigable" 
                                               name="def_temperature" 
                                               value="{{ old('def_temperature', $tisbe_longicornis_riles->definitive_sample_temperature) }}">
                                        <span class="input-group-text unit-badge">°C</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datepicker" 
                                           name="def_control_water_date" 
                                           value="{{ old('def_control_water_date', $tisbe_longicornis_riles->definitive_control_water_date) }}" 
                                           placeholder="Seleccione fecha">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TEMPORIZADOR DEFINITIVO --}}
                <div class="timer-container my-4 p-3" id="definitiveTimerContainer">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-warning btn-lg" id="btnStartDefinitive" onclick="startTimer('definitive')">
                                <i class="fas fa-play me-2"></i>Iniciar Definitivo
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="btnResetDefinitive" onclick="resetTimer('definitive')" style="display: none;">
                                <i class="fas fa-redo me-1"></i>Reiniciar
                            </button>
                        </div>
                        <div class="timer-display" id="definitiveTimerDisplay">
                            <div class="timer-icon">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div class="timer-values">
                                <span class="timer-elapsed" id="definitiveElapsed">00:00:00</span>
                                <span class="timer-separator">/</span>
                                <span class="timer-limit">48:00:00</span>
                            </div>
                            <div class="timer-status" id="definitiveStatus">
                                <span class="badge bg-secondary">Sin iniciar</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barra de progreso --}}
                    <div class="progress-container mt-3">
                        <div class="progress-labels d-flex justify-content-between mb-1">
                            <small class="text-muted">Inicio</small>
                            <small class="text-primary fw-bold">48h (Límite)</small>
                            <small class="text-warning fw-bold">58h (Gracia)</small>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress" style="height: 25px; border-radius: 12px; background: #e9ecef;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="definitiveProgressBar" 
                                     role="progressbar" 
                                     style="width: 0%; border-radius: 12px;"
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="progress-text" id="definitiveProgressText">0%</span>
                                </div>
                            </div>
                            {{-- Marcadores de tiempo --}}
                            <div class="progress-markers">
                                <div class="marker marker-limit" style="left: 82.76%;" title="48 horas">
                                    <div class="marker-line"></div>
                                </div>
                                <div class="marker marker-grace" style="left: 100%;" title="58 horas (fin gracia)">
                                    <div class="marker-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="time-remaining mt-2 text-center" id="definitiveTimeRemaining">
                            <small class="text-muted">Tiempo restante: <strong>48:00:00</strong></small>
                        </div>
                    </div>
                </div>

                @php
                    $replicas = 4;
                    $concentrations = 5;
                @endphp

                {{-- TABLA 24 HORAS --}}
                <h4 class="mt-4 mb-3 text-center section-title">
                    <span>24 Horas</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Réplica</th>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Control</th>
                                <th colspan="{{ $concentrations }}">Concentraciones</th>
                            </tr>
                            <tr>
                                @for ($c = 1; $c <= $concentrations; $c++)
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable concentration-header" 
                                               name="def_24h_conc{{ $c }}_value" 
                                               value="{{ old('def_24h_conc'.$c.'_value', $def_24h_conc_values[$c - 1] ?? '') }}"
                                               placeholder="Conc. {{ $c }}">
                                    </td>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for ($r = 1; $r <= $replicas; $r++)
                                @php
                                    $row = $def_24h_rows[$r - 1] ?? [];
                                    $row_concs = $row['concentrations'] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">Réplica {{ $r }}</td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable" 
                                               name="def_24h_control_rep{{ $r }}" 
                                               value="{{ old('def_24h_control_rep'.$r, $row['control'] ?? '') }}">
                                    </td>
                                    @for ($c = 1; $c <= $concentrations; $c++)
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable" 
                                                   name="def_24h_conc{{ $c }}_rep{{ $r }}" 
                                                   value="{{ old('def_24h_conc'.$c.'_rep'.$r, $row_concs[$c - 1] ?? '') }}">
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                            @php
                                $totals_24h = $def_24h_rows[$replicas] ?? [];
                                $totals_24h_concs = $totals_24h['concentrations_sum'] ?? [];
                            @endphp
                            <tr class="table-warning">
                                <td class="fw-bold">∑ Tisbe muertas</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm navigable sum-field" 
                                           name="def_24h_control_sum" 
                                           value="{{ old('def_24h_control_sum', $totals_24h['control_sum'] ?? '') }}" 
                                           readonly>
                                </td>
                                @for ($c = 1; $c <= $concentrations; $c++)
                                    <td>
                                        <input type="number" class="form-control form-control-sm navigable sum-field" 
                                               name="def_24h_conc{{ $c }}_sum" 
                                               value="{{ old('def_24h_conc'.$c.'_sum', $totals_24h_concs[$c - 1] ?? '') }}" 
                                               readonly>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLA 48 HORAS --}}
                <h4 class="mt-4 mb-3 text-center section-title">
                    <span>48 Horas</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Réplica</th>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Control</th>
                                <th colspan="{{ $concentrations }}">Concentraciones</th>
                            </tr>
                            <tr>
                                @for ($c = 1; $c <= $concentrations; $c++)
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable concentration-header" 
                                               name="def_48h_conc{{ $c }}_value" 
                                               value="{{ old('def_48h_conc'.$c.'_value', $def_48h_conc_values[$c - 1] ?? '') }}"
                                               placeholder="Conc. {{ $c }}">
                                    </td>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for ($r = 1; $r <= $replicas; $r++)
                                @php
                                    $row = $def_48h_rows[$r - 1] ?? [];
                                    $row_concs = $row['concentrations'] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">Réplica {{ $r }}</td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm navigable" 
                                               name="def_48h_control_rep{{ $r }}" 
                                               value="{{ old('def_48h_control_rep'.$r, $row['control'] ?? '') }}">
                                    </td>
                                    @for ($c = 1; $c <= $concentrations; $c++)
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable" 
                                                   name="def_48h_conc{{ $c }}_rep{{ $r }}" 
                                                   value="{{ old('def_48h_conc'.$c.'_rep'.$r, $row_concs[$c - 1] ?? '') }}">
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                            @php
                                $totals_48h = $def_48h_rows[$replicas] ?? [];
                                $totals_48h_concs = $totals_48h['concentrations_sum'] ?? [];
                            @endphp
                            <tr class="table-warning">
                                <td class="fw-bold">∑ Tisbe muertas</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm navigable sum-field" 
                                           name="def_48h_control_sum" 
                                           value="{{ old('def_48h_control_sum', $totals_48h['control_sum'] ?? '') }}" 
                                           readonly>
                                </td>
                                @for ($c = 1; $c <= $concentrations; $c++)
                                    <td>
                                        <input type="number" class="form-control form-control-sm navigable sum-field" 
                                               name="def_48h_conc{{ $c }}_sum" 
                                               value="{{ old('def_48h_conc'.$c.'_sum', $totals_48h_concs[$c - 1] ?? '') }}" 
                                               readonly>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= RESULTADOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-orange-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-chart-bar me-2"></i>Resultados de Análisis
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Inmovilidad del control</label>
                        <div class="input-group">
                            <input type="text" class="form-control navigable" 
                                   name="control_immobility" 
                                   value="{{ old('control_immobility', $tisbe_longicornis_riles->control_immobility) }}">
                            <span class="input-group-text unit-badge">%</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CL<sub>50</sub> 24h</label>
                        <input type="text" class="form-control navigable" 
                               name="cl50_24h" 
                               value="{{ old('cl50_24h', $tisbe_longicornis_riles->cl50_24h) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CL<sub>50</sub> 48h</label>
                        <input type="text" class="form-control navigable" 
                               name="cl50_48h" 
                               value="{{ old('cl50_48h', $tisbe_longicornis_riles->cl50_48h) }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control navigable" name="observations" id="observations" rows="3" 
                              placeholder="Ingrese observaciones adicionales...">{{ old('observations', $tisbe_longicornis_riles->observations) }}</textarea>
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
                        Rango de aceptabilidad Control Negativo: <strong> Inmobilidad ≤ 10%</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        pH de las muestras <strong>de 6 a 9 unidades</strong>
                    </li>
                </ul>
                <div class="mt-3 pt-3 border-top">
                    <p class="mb-0 text-muted">V°B° _____________________</p>
                </div>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-orange btn-lg px-4">
                <i class="fas fa-save me-2"></i>Actualizar Bioensayo
            </button>
            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            @php
                $sampleEntry = \App\Models\SampleEntry::where('internal_sample_code', $tisbe_longicornis_riles->sample)->first();
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
    <!-- Flatpickr - Tema Dark -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('styles')
<style>
    /* ============================================
       COLORES PERSONALIZADOS - NARANJA
       ============================================ */
    .bg-orange-subtle {
        background-color: #ffe5cc !important;
    }

    .btn-orange {
        background-color: #fd7e14;
        border-color: #fd7e14;
        color: white;
    }

    .btn-orange:hover {
        background-color: #e96b02;
        border-color: #e96b02;
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
        font-size: 0.9rem;
        padding: 12px 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody td {
        padding: 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .modern-table input {
        border: 1px solid #ced4da;
        text-align: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .modern-table input:focus {
        border-color: #fd7e14;
        box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
    }

    /* ============================================
       TÍTULOS DE SECCIÓN
       ============================================ */
    .section-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1.4rem;
        font-weight: 500;
        color: #fd7e14;
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
        background: linear-gradient(to right, transparent, #fd7e14, transparent);
    }

    /* ============================================
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-24h {
        background: #cfe2ff !important;
        color: #084298;
    }

    .table-header-48h {
        background: #ffe5cc !important;
        color: #975200;
    }

    /* ============================================
       CAMPOS DE SUMA (READONLY)
       ============================================ */
    .sum-field {
        background-color: #fff3cd !important;
        font-weight: 600;
        cursor: not-allowed;
    }

    /* ============================================
       INPUTS DE CONCENTRACIÓN EN HEADER
       ============================================ */
    .concentration-header {
        background: #f8f9fa;
        font-weight: 500;
        border: 2px dashed #fd7e14 !important;
    }

    .concentration-header:focus {
        border-style: solid !important;
    }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge {
        background: #6c757d;
        color: white;
        font-weight: 500;
        font-size: 0.85rem;
        min-width: 50px;
        justify-content: center;
    }

    /* ============================================
       NAVEGACIÓN CON FLECHAS
       ============================================ */
    .navigable:focus {
        outline: 2px solid #0d6efd;
        outline-offset: 2px;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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
       CARD HEADER COLORES
       ============================================ */
    .bg-secondary-subtle {
        background-color: #e2e3e5 !important;
    }

    /* ============================================
       TEMPORIZADORES
       ============================================ */
    .timer-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .timer-container.timer-running {
        border-color: #fd7e14;
        box-shadow: 0 0 15px rgba(253, 126, 20, 0.2);
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
        color: #fd7e14;
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
        color: #fd7e14;
    }

    .timer-elapsed.warning {
        color: #ffc107;
    }

    .timer-elapsed.grace {
        color: #fd7e14;
    }

    .timer-elapsed.expired {
        color: #dc3545;
    }

    .timer-separator {
        color: #6c757d;
        margin: 0 5px;
    }

    .timer-limit {
        color: #6c757d;
    }

    /* ============================================
       BARRA DE PROGRESO
       ============================================ */
    .progress-wrapper {
        position: relative;
    }

    .progress-bar {
        transition: width 1s linear, background-color 0.5s ease;
    }

    .progress-bar.bg-orange {
        background: linear-gradient(90deg, #fd7e14 0%, #ffb380 100%) !important;
    }

    .progress-bar.bg-warning {
        background: linear-gradient(90deg, #ffc107 0%, #ffda6a 100%) !important;
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
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
    }

    .marker {
        position: absolute;
        top: -5px;
        bottom: -5px;
        width: 3px;
        transform: translateX(-50%);
    }

    .marker-line {
        width: 100%;
        height: 100%;
        border-radius: 2px;
    }

    .marker-limit .marker-line {
        background: #0d6efd;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
    }

    .marker-grace .marker-line {
        background: #fd7e14;
        box-shadow: 0 0 5px rgba(253, 126, 20, 0.5);
    }

    /* ============================================
       FLATPICKR PERSONALIZACIÓN
       ============================================ */
    .flatpickr-calendar {
        font-size: 14px !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .flatpickr-day {
        border-radius: 6px !important;
    }

    .flatpickr-time input {
        font-size: 18px !important;
        font-weight: 500 !important;
    }

    /* ============================================
       BOTONES
       ============================================ */
    .btn-lg {
        border-radius: 8px;
        font-weight: 500;
    }

    /* ============================================
       IMPRESIÓN
       ============================================ */
    @media print {
        @page { 
            size: A4 portrait; 
            margin: 10mm; 
        }
        
        body { 
            font-size: 10pt; 
            line-height: 1.2; 
        }
        
        .btn, button, nav, .navbar, .no-print,
        .timer-container { 
            display: none !important; 
        }
        
        .card { 
            border: 1px solid #000 !important; 
            box-shadow: none !important; 
            margin-bottom: 10px !important;
            break-inside: avoid;
        }
        
        .card-header { 
            background: #f0f0f0 !important; 
            color: #000 !important; 
            font-weight: bold; 
            padding: 6px 10px !important;
            font-size: 12pt !important;
        }
        
        .card-body { 
            padding: 8px !important; 
        }

        .section-title {
            font-size: 11pt !important;
            margin: 10px 0 !important;
        }

        .modern-table input {
            border: none !important;
            background: transparent !important;
            font-size: 9pt;
        }

        .sum-field {
            background: #f0f0f0 !important;
        }

        .concentration-header {
            border: 1px solid #000 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // ============= CONSTANTES DE TIEMPO =============
    const LIMIT_HOURS = 48;
    const GRACE_HOURS = 10;
    const TOTAL_HOURS = LIMIT_HOURS + GRACE_HOURS;
    
    const LIMIT_MS = LIMIT_HOURS * 60 * 60 * 1000;
    const TOTAL_MS = TOTAL_HOURS * 60 * 60 * 1000;

    // ============= ESTADO DE TEMPORIZADORES =============
    const timers = {
        preliminary: {
            startTime: null,
            interval: null,
            inputId: 'preliminary_timer_start'
        },
        definitive: {
            startTime: null,
            interval: null,
            inputId: 'definitive_timer_start'
        }
    };

    // ============= INICIALIZAR FLATPICKR =============
    flatpickr(".datetimepicker", { 
        enableTime: true, 
        time_24hr: true, 
        dateFormat: "Y-m-d H:i", 
        locale: "es",
        allowInput: true
    });
    
    flatpickr(".datepicker", { 
        dateFormat: "Y-m-d", 
        locale: "es",
        allowInput: true
    });

    // ============= NAVEGACIÓN CON FLECHAS =============
    const navigableElements = document.querySelectorAll('.navigable');
    
    navigableElements.forEach((element, index) => {
        element.addEventListener('keydown', function(e) {
            let targetIndex = index;
            
            if (e.key === 'ArrowDown' || (e.key === 'Enter' && !e.shiftKey)) {
                e.preventDefault();
                targetIndex = index + 1;
            } else if (e.key === 'ArrowUp' || (e.key === 'Enter' && e.shiftKey)) {
                e.preventDefault();
                targetIndex = index - 1;
            } else if (e.key === 'ArrowRight' && this.selectionStart === this.value.length) {
                e.preventDefault();
                targetIndex = index + 1;
            } else if (e.key === 'ArrowLeft' && this.selectionStart === 0) {
                e.preventDefault();
                targetIndex = index - 1;
            }
            
            if (targetIndex >= 0 && targetIndex < navigableElements.length && targetIndex !== index) {
                navigableElements[targetIndex].focus();
                if (navigableElements[targetIndex].select) {
                    navigableElements[targetIndex].select();
                }
            }
        });
    });

    // ============= FUNCIONES DE TEMPORIZADOR =============
    window.startTimer = function(type) {
        const timer = timers[type];
        const now = Date.now();
        
        timer.startTime = now;
        document.getElementById(timer.inputId).value = now;
        
        document.getElementById(`btnStart${capitalizeFirst(type)}`).style.display = 'none';
        document.getElementById(`btnReset${capitalizeFirst(type)}`).style.display = 'inline-block';
        
        updateTimerDisplay(type);
        timer.interval = setInterval(() => updateTimerDisplay(type), 1000);
        
        Swal.fire({
            icon: 'success',
            title: `Temporizador ${type === 'preliminary' ? 'Preliminar' : 'Definitivo'} Iniciado`,
            text: `Tienes ${LIMIT_HOURS} horas para completar el ensayo (+ ${GRACE_HOURS}h de gracia).`,
            timer: 3000,
            showConfirmButton: false
        });
    };

    window.resetTimer = function(type) {
        Swal.fire({
            title: '¿Reiniciar temporizador?',
            text: "Esta acción reiniciará el contador a cero.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, reiniciar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const timer = timers[type];
                
                if (timer.interval) clearInterval(timer.interval);
                timer.startTime = null;
                document.getElementById(timer.inputId).value = '';
                
                document.getElementById(`btnStart${capitalizeFirst(type)}`).style.display = 'inline-block';
                document.getElementById(`btnReset${capitalizeFirst(type)}`).style.display = 'none';
                
                resetTimerUI(type);
            }
        });
    };

    function updateTimerDisplay(type) {
        const timer = timers[type];
        if (!timer.startTime) return;
        
        const elapsed = Date.now() - timer.startTime;
        const percentage = Math.min((elapsed / TOTAL_MS) * 100, 100);
        
        const elapsedFormatted = formatTime(elapsed);
        document.getElementById(`${type}Elapsed`).textContent = elapsedFormatted;
        
        const progressBar = document.getElementById(`${type}ProgressBar`);
        const progressText = document.getElementById(`${type}ProgressText`);
        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `${percentage.toFixed(1)}%`;
        
        const remaining = Math.max(LIMIT_MS - elapsed, 0);
        const remainingFormatted = formatTime(remaining);
        const remainingContainer = document.getElementById(`${type}TimeRemaining`);
        
        const container = document.getElementById(`${type}TimerContainer`);
        const elapsedSpan = document.getElementById(`${type}Elapsed`);
        const statusSpan = document.getElementById(`${type}Status`);
        const timerIcon = container.querySelector('.timer-icon');
        
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        elapsedSpan.classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-orange', 'bg-warning', 'bg-danger');
        timerIcon.classList.add('running');
        
        if (elapsed >= TOTAL_MS) {
            container.classList.add('timer-expired');
            elapsedSpan.classList.add('expired');
            progressBar.classList.add('bg-danger');
            statusSpan.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>¡Tiempo Expirado!</span>';
            remainingContainer.innerHTML = '<small class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Tiempo de gracia agotado</small>';
        } else if (elapsed >= LIMIT_MS) {
            const graceRemaining = TOTAL_MS - elapsed;
            container.classList.add('timer-grace');
            elapsedSpan.classList.add('grace');
            progressBar.classList.add('bg-warning');
            statusSpan.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Período de Gracia</span>';
            remainingContainer.innerHTML = `<small class="text-warning fw-bold"><i class="fas fa-clock me-1"></i>Gracia restante: <strong>${formatTime(graceRemaining)}</strong></small>`;
        } else if (elapsed >= LIMIT_MS * 0.9) {
            container.classList.add('timer-warning');
            elapsedSpan.classList.add('warning');
            progressBar.classList.add('bg-warning');
            statusSpan.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>¡Poco Tiempo!</span>';
            remainingContainer.innerHTML = `<small class="text-warning"><i class="fas fa-clock me-1"></i>Tiempo restante: <strong>${remainingFormatted}</strong></small>`;
        } else {
            container.classList.add('timer-running');
            progressBar.classList.add('bg-orange');
            statusSpan.innerHTML = '<span class="badge bg-success"><i class="fas fa-play me-1"></i>En Progreso</span>';
            remainingContainer.innerHTML = `<small class="text-muted"><i class="fas fa-clock me-1"></i>Tiempo restante: <strong>${remainingFormatted}</strong></small>`;
        }
    }

    function resetTimerUI(type) {
        const container = document.getElementById(`${type}TimerContainer`);
        const progressBar = document.getElementById(`${type}ProgressBar`);
        const progressText = document.getElementById(`${type}ProgressText`);
        const elapsedSpan = document.getElementById(`${type}Elapsed`);
        const statusSpan = document.getElementById(`${type}Status`);
        const remainingContainer = document.getElementById(`${type}TimeRemaining`);
        const timerIcon = container.querySelector('.timer-icon');
        
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        elapsedSpan.classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-orange', 'bg-warning', 'bg-danger');
        timerIcon.classList.remove('running');
        
        elapsedSpan.textContent = '00:00:00';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        statusSpan.innerHTML = '<span class="badge bg-secondary">Sin iniciar</span>';
        remainingContainer.innerHTML = '<small class="text-muted">Tiempo restante: <strong>48:00:00</strong></small>';
    }

    function formatTime(ms) {
        const totalSeconds = Math.floor(ms / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ============= RESTAURAR TEMPORIZADORES GUARDADOS =============
    ['preliminary', 'definitive'].forEach(type => {
        const savedStart = document.getElementById(timers[type].inputId).value;
        if (savedStart && !isNaN(parseInt(savedStart))) {
            timers[type].startTime = parseInt(savedStart);
            document.getElementById(`btnStart${capitalizeFirst(type)}`).style.display = 'none';
            document.getElementById(`btnReset${capitalizeFirst(type)}`).style.display = 'inline-block';
            updateTimerDisplay(type);
            timers[type].interval = setInterval(() => updateTimerDisplay(type), 1000);
        }
    });

    // ============= CÁLCULO AUTOMÁTICO DE SUMAS (PRELIMINAR) =============
    for (let i = 1; i <= 8; i++) {
        const rep1_24h = document.querySelector(`[name="pre_24h_rep1_row${i}"]`);
        const rep2_24h = document.querySelector(`[name="pre_24h_rep2_row${i}"]`);
        const sum_24h = document.querySelector(`[name="pre_24h_sum_row${i}"]`);
        
        const rep1_48h = document.querySelector(`[name="pre_48h_rep1_row${i}"]`);
        const rep2_48h = document.querySelector(`[name="pre_48h_rep2_row${i}"]`);
        const sum_48h = document.querySelector(`[name="pre_48h_sum_row${i}"]`);

        [rep1_24h, rep2_24h].forEach(input => {
            if (input) {
                input.addEventListener('input', () => {
                    const val1 = parseFloat(rep1_24h.value) || 0;
                    const val2 = parseFloat(rep2_24h.value) || 0;
                    sum_24h.value = val1 + val2;
                });
            }
        });

        [rep1_48h, rep2_48h].forEach(input => {
            if (input) {
                input.addEventListener('input', () => {
                    const val1 = parseFloat(rep1_48h.value) || 0;
                    const val2 = parseFloat(rep2_48h.value) || 0;
                    sum_48h.value = val1 + val2;
                });
            }
        });
    }

    // ============= CÁLCULO AUTOMÁTICO DE SUMAS (DEFINITIVO) =============
    const hours = ['24', '48'];
    const replicas = 4;
    const concentrations = 5;

    hours.forEach(hour => {
        // Control
        const controlInputs = [];
        for (let r = 1; r <= replicas; r++) {
            controlInputs.push(document.querySelector(`[name="def_${hour}h_control_rep${r}"]`));
        }
        const controlSum = document.querySelector(`[name="def_${hour}h_control_sum"]`);

        controlInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', () => {
                    let total = 0;
                    controlInputs.forEach(inp => {
                        total += parseFloat(inp.value) || 0;
                    });
                    controlSum.value = total;
                });
            }
        });

        // Concentraciones
        for (let c = 1; c <= concentrations; c++) {
            const concInputs = [];
            for (let r = 1; r <= replicas; r++) {
                concInputs.push(document.querySelector(`[name="def_${hour}h_conc${c}_rep${r}"]`));
            }
            const concSum = document.querySelector(`[name="def_${hour}h_conc${c}_sum"]`);

            concInputs.forEach(input => {
                if (input) {
                    input.addEventListener('input', () => {
                        let total = 0;
                        concInputs.forEach(inp => {
                            total += parseFloat(inp.value) || 0;
                        });
                        concSum.value = total;
                    });
                }
            });
        }
    });

    // ============= SWEETALERT PARA CONFIRMACIÓN =============
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Confirmar actualización?',
            text: "Se guardarán los cambios del bioensayo de Tisbe longicornis (Sustancias Químicas/Riles).",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#fd7e14',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save me-1"></i> Sí, actualizar',
            cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush