{{-- resources/views/bioassays/arbacia_larval_stage/edit.blade.php --}}
@extends('layouts.app')

@section('content')
@include('bioassays._status', ['bioassay' => $arbacia_larval_stage, 'section' => 'arbacia-larval-stage'])
<div class="container-fluid px-4">
    <!-- Logo -->
    <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        <span class="d-print-none">Editar</span><span class="d-none d-print-inline">Análisis</span> Bioensayo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #d63384;">
        Arbacia spatuligera - Estado Larval
    </h2>
    <p class="text-center text-muted mb-4">RT-01.05 | Versión: 01 | Vigencia: 06.10.2025</p>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <strong><i class="bi bi-exclamation-triangle"></i> Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $rows = $arbacia_larval_stage->rows_data ?? [];
        $controlData = $arbacia_larval_stage->control_data ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('arbacia-larval-stage.update', $arbacia_larval_stage->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-pink-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-card-checklist me-2"></i>Datos Generales
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-eyedropper text-pink me-1"></i>Muestra
                        </label>
                        <input type="text" class="form-control navigable bg-light" 
                               name="sample" 
                               value="{{ old('sample', $arbacia_larval_stage->sample) }}" 
                               readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-water text-primary me-1"></i>Matriz
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="matrix" 
                               value="{{ old('matrix', $arbacia_larval_stage->matrix) }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar3 text-success me-1"></i>Fecha y hora inicio Bioensayo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="bioassay_start" 
                               value="{{ old('bioassay_start', $arbacia_larval_stage->bioassay_start) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-person text-secondary me-1"></i>Analista
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="analyst" 
                               value="{{ old('analyst', $arbacia_larval_stage->analyst) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TIEMPOS DEL ENSAYO ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-pink-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-clock me-2"></i>Tiempos del Ensayo
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-egg text-warning me-1"></i>Hora de fecundación
                        </label>
                        <input type="text" class="form-control navigable timepicker" 
                               name="fertilization_time" 
                               value="{{ old('fertilization_time', $arbacia_larval_stage->fertilization_time) }}" 
                               placeholder="HH:MM">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-plus-circle text-info me-1"></i>Hora adición óvulos fecundados
                        </label>
                        <input type="text" class="form-control navigable timepicker" 
                               name="fertilized_eggs_added_at" 
                               value="{{ old('fertilized_eggs_added_at', $arbacia_larval_stage->fertilized_eggs_added_at) }}" 
                               placeholder="HH:MM">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-hourglass-bottom text-danger me-1"></i>Fecha y hora término fijación
                        </label>
                        {{-- Con fecha, porque el ensayo dura 48 h y la fijación termina otro día --}}
                        <input type="text" class="form-control navigable datetimepicker"
                               name="fixation_time_end"
                               value="{{ old('fixation_time_end', $arbacia_larval_stage->fixation_time_end) }}"
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calculator text-success me-1"></i>Fecha/hora término conteo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="count_end_datetime" 
                               value="{{ old('count_end_datetime', $arbacia_larval_stage->count_end_datetime) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                </div>

                @include('bioassays._duration', ['check' => $arbacia_larval_stage->durationChecks()['bioassay'], 'start' => 'bioassay_start', 'end' => 'fixation_time_end'])
            </div>
        </div>

        {{-- ================= CONTROL ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-check-circle me-2"></i>Control
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th colspan="2" class="table-header-replica-1">Réplica 1</th>
                                <th colspan="2" class="table-header-replica-2">Réplica 2</th>
                                <th colspan="2" class="table-header-replica-3">Réplica 3</th>
                                <th colspan="2" class="table-header-replica-4">Réplica 4</th>
                                <th rowspan="2" class="align-middle table-header-result">% Larva Pluteus</th>
                                <th rowspan="2" class="align-middle table-header-result">% Total Larvas</th>
                            </tr>
                            <tr>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- Réplica 1 --}}
                                <td>
                                    <div class="input-group input-group-sm fraction-input">
                                        <input type="number" class="form-control navigable control-larva" 
                                               name="control[r1_larva]" 
                                               value="{{ old('control.r1_larva', $controlData['r1_larva'] ?? '') }}"
                                               placeholder="L"
                                               data-replica="1">
                                        <span class="input-group-text">/</span>
                                        <input type="number" class="form-control navigable control-total" 
                                               name="control[r1_total]" 
                                               value="{{ old('control.r1_total', $controlData['r1_total'] ?? '') }}"
                                               placeholder="T"
                                               data-replica="1">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable control-percent bg-light" 
                                           name="control[r1_percent]" 
                                           value="{{ old('control.r1_percent', $controlData['r1_percent'] ?? '') }}"
                                           data-replica="1" readonly>
                                </td>
                                {{-- Réplica 2 --}}
                                <td>
                                    <div class="input-group input-group-sm fraction-input">
                                        <input type="number" class="form-control navigable control-larva" 
                                               name="control[r2_larva]" 
                                               value="{{ old('control.r2_larva', $controlData['r2_larva'] ?? '') }}"
                                               placeholder="L"
                                               data-replica="2">
                                        <span class="input-group-text">/</span>
                                        <input type="number" class="form-control navigable control-total" 
                                               name="control[r2_total]" 
                                               value="{{ old('control.r2_total', $controlData['r2_total'] ?? '') }}"
                                               placeholder="T"
                                               data-replica="2">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable control-percent bg-light" 
                                           name="control[r2_percent]" 
                                           value="{{ old('control.r2_percent', $controlData['r2_percent'] ?? '') }}"
                                           data-replica="2" readonly>
                                </td>
                                {{-- Réplica 3 --}}
                                <td>
                                    <div class="input-group input-group-sm fraction-input">
                                        <input type="number" class="form-control navigable control-larva" 
                                               name="control[r3_larva]" 
                                               value="{{ old('control.r3_larva', $controlData['r3_larva'] ?? '') }}"
                                               placeholder="L"
                                               data-replica="3">
                                        <span class="input-group-text">/</span>
                                        <input type="number" class="form-control navigable control-total" 
                                               name="control[r3_total]" 
                                               value="{{ old('control.r3_total', $controlData['r3_total'] ?? '') }}"
                                               placeholder="T"
                                               data-replica="3">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable control-percent bg-light" 
                                           name="control[r3_percent]" 
                                           value="{{ old('control.r3_percent', $controlData['r3_percent'] ?? '') }}"
                                           data-replica="3" readonly>
                                </td>
                                {{-- Réplica 4 --}}
                                <td>
                                    <div class="input-group input-group-sm fraction-input">
                                        <input type="number" class="form-control navigable control-larva" 
                                               name="control[r4_larva]" 
                                               value="{{ old('control.r4_larva', $controlData['r4_larva'] ?? '') }}"
                                               placeholder="L"
                                               data-replica="4">
                                        <span class="input-group-text">/</span>
                                        <input type="number" class="form-control navigable control-total" 
                                               name="control[r4_total]" 
                                               value="{{ old('control.r4_total', $controlData['r4_total'] ?? '') }}"
                                               placeholder="T"
                                               data-replica="4">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable control-percent bg-light" 
                                           name="control[r4_percent]" 
                                           value="{{ old('control.r4_percent', $controlData['r4_percent'] ?? '') }}"
                                           data-replica="4" readonly>
                                </td>
                                {{-- Resultados Control --}}
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable bg-success-subtle" 
                                           name="control[larval_pluteus_avg]" 
                                           value="{{ old('control.larval_pluteus_avg', $controlData['larval_pluteus_avg'] ?? '') }}"
                                           id="controlLarvalAvg" readonly>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable bg-info-subtle" 
                                           name="control[total_larva_percent]" 
                                           value="{{ old('control.total_larva_percent', $controlData['total_larva_percent'] ?? '') }}"
                                           id="controlTotalPercent" readonly>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= TABLA DE DATOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-pink-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-table me-2"></i>Datos de Estado Larval
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 50px;">#</th>
                                <th rowspan="2" class="align-middle" style="width: 120px;">Concentración</th>
                                <th colspan="2" class="table-header-replica-1">Réplica 1</th>
                                <th colspan="2" class="table-header-replica-2">Réplica 2</th>
                                <th colspan="2" class="table-header-replica-3">Réplica 3</th>
                                <th colspan="2" class="table-header-replica-4">Réplica 4</th>
                                <th rowspan="2" class="align-middle table-header-result">% Larva Pluteus</th>
                                <th rowspan="2" class="align-middle table-header-inhibition">% Inhibición</th>
                                <th rowspan="2" class="align-middle table-header-ce">CE</th>
                            </tr>
                            <tr>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                                <th class="table-header-larva">Larva/Total</th>
                                <th class="table-header-percent">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 15; $i++)
                                @php
                                    $row = $rows[$i] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">{{ $i }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="rows[{{ $i }}][concentration]" 
                                               value="{{ old('rows.'.$i.'.concentration', $row['concentration'] ?? '') }}"
                                               placeholder="Conc. {{ $i }}">
                                    </td>
                                    {{-- Réplica 1 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable larva-input" 
                                                   name="rows[{{ $i }}][r1_larva]" 
                                                   value="{{ old('rows.'.$i.'.r1_larva', $row['r1_larva'] ?? '') }}"
                                                   placeholder="L"
                                                   data-row="{{ $i }}" data-replica="1">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r1_total]" 
                                                   value="{{ old('rows.'.$i.'.r1_total', $row['r1_total'] ?? '') }}"
                                                   placeholder="T"
                                                   data-row="{{ $i }}" data-replica="1">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-percent bg-light" 
                                               name="rows[{{ $i }}][r1_percent]" 
                                               value="{{ old('rows.'.$i.'.r1_percent', $row['r1_percent'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="1" readonly>
                                    </td>
                                    {{-- Réplica 2 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable larva-input" 
                                                   name="rows[{{ $i }}][r2_larva]" 
                                                   value="{{ old('rows.'.$i.'.r2_larva', $row['r2_larva'] ?? '') }}"
                                                   placeholder="L"
                                                   data-row="{{ $i }}" data-replica="2">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r2_total]" 
                                                   value="{{ old('rows.'.$i.'.r2_total', $row['r2_total'] ?? '') }}"
                                                   placeholder="T"
                                                   data-row="{{ $i }}" data-replica="2">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-percent bg-light" 
                                               name="rows[{{ $i }}][r2_percent]" 
                                               value="{{ old('rows.'.$i.'.r2_percent', $row['r2_percent'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="2" readonly>
                                    </td>
                                    {{-- Réplica 3 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable larva-input" 
                                                   name="rows[{{ $i }}][r3_larva]" 
                                                   value="{{ old('rows.'.$i.'.r3_larva', $row['r3_larva'] ?? '') }}"
                                                   placeholder="L"
                                                   data-row="{{ $i }}" data-replica="3">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r3_total]" 
                                                   value="{{ old('rows.'.$i.'.r3_total', $row['r3_total'] ?? '') }}"
                                                   placeholder="T"
                                                   data-row="{{ $i }}" data-replica="3">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-percent bg-light" 
                                               name="rows[{{ $i }}][r3_percent]" 
                                               value="{{ old('rows.'.$i.'.r3_percent', $row['r3_percent'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="3" readonly>
                                    </td>
                                    {{-- Réplica 4 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable larva-input" 
                                                   name="rows[{{ $i }}][r4_larva]" 
                                                   value="{{ old('rows.'.$i.'.r4_larva', $row['r4_larva'] ?? '') }}"
                                                   placeholder="L"
                                                   data-row="{{ $i }}" data-replica="4">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r4_total]" 
                                                   value="{{ old('rows.'.$i.'.r4_total', $row['r4_total'] ?? '') }}"
                                                   placeholder="T"
                                                   data-row="{{ $i }}" data-replica="4">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-percent bg-light" 
                                               name="rows[{{ $i }}][r4_percent]" 
                                               value="{{ old('rows.'.$i.'.r4_percent', $row['r4_percent'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="4" readonly>
                                    </td>
                                    {{-- Resultados --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable avg-larval bg-success-subtle" 
                                               name="rows[{{ $i }}][larval_pluteus_percent]" 
                                               value="{{ old('rows.'.$i.'.larval_pluteus_percent', $row['larval_pluteus_percent'] ?? '') }}"
                                               data-row="{{ $i }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable inhibition bg-warning-subtle" 
                                               name="rows[{{ $i }}][inhibition_percent]" 
                                               value="{{ old('rows.'.$i.'.inhibition_percent', $row['inhibition_percent'] ?? '') }}"
                                               data-row="{{ $i }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="rows[{{ $i }}][ce]" 
                                               value="{{ old('rows.'.$i.'.ce', $row['ce'] ?? '') }}">
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
            <div class="card-header bg-pink-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-bar-chart me-2"></i>Resultados
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CE<sub>50</sub></label>
                        <input type="text" class="form-control navigable" 
                               name="ce50" 
                               value="{{ old('ce50', $arbacia_larval_stage->ce50) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea class="form-control navigable" name="observations" rows="2" 
                                  placeholder="Ingrese observaciones adicionales...">{{ old('observations', $arbacia_larval_stage->observations) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= CRITERIOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-secondary-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-check-circle me-2"></i>Criterios de Aceptabilidad
            </div>
            <div class="card-body bg-light p-3">
                <ul class="criteria-list mb-3">
                    <li>
                        <i class="bi bi-arrow-right text-pink me-2"></i>
                        Desarrollo larval en control: <strong>≥ 80%</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-pink me-2"></i>
                        Tiempo de exposición: <strong>48 horas</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-pink me-2"></i>
                        Temperatura: <strong>15 ± 2 °C</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-pink me-2"></i>
                        Salinidad: <strong>34 ± 2‰</strong>
                    </li>
                </ul>
                <div class="mt-3 pt-3 border-top">
                    <p class="mb-0 text-muted">V°B° _____________________</p>
                </div>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="bi bi-floppy me-2"></i>Actualizar Bioensayo
            </button>

            @if(auth()->user()->can('validateBioassay', $arbacia_larval_stage))
                @if($arbacia_larval_stage->validated_at)
                    <span class="btn btn-outline-success btn-lg px-4 disabled">
                        <i class="bi bi-check-circle me-2"></i>Validado el
                        {{ $arbacia_larval_stage->validated_at->format('d/m/Y H:i') }}
                    </span>
                @else
                    <button type="submit" form="validateBioassayForm" class="btn btn-warning btn-lg px-4">
                        <i class="bi bi-clipboard-check me-2"></i>Marcar como Validado
                    </button>
                @endif
            @endif

            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir
            </button>
            @php
                $sampleEntry = $arbacia_larval_stage->sampleEntry;
            @endphp
            @if($sampleEntry)
                <a href="{{ route('sample_entries.show', $sampleEntry->id) }}" class="btn btn-secondary btn-lg px-4">
                    <i class="bi bi-arrow-left me-2"></i>Volver a Muestra
                </a>
            @else
                <a href="{{ route('sample_entries.index') }}" class="btn btn-secondary btn-lg px-4">
                    <i class="bi bi-x-lg me-2"></i>Cancelar
                </a>
            @endif
        </div>
    </form>

    @if(auth()->user()->can('validateBioassay', $arbacia_larval_stage) && !$arbacia_larval_stage->validated_at)
        <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo, y la planilla pasará a solo lectura." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('arbacia-larval-stage.validate', $arbacia_larval_stage->id) }}" method="POST" style="display:none">
            @csrf
            @method('PATCH')
        </form>
    @endif
</div>
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
@endpush

@push('styles')
<style>
    /* ============================================
       COLORES PERSONALIZADOS - ROSA/PINK
       ============================================ */
    .bg-pink-subtle { background-color: #fce7f3 !important; }
    .text-pink { color: #d63384 !important; }
    .bg-success-subtle { background-color: #d1e7dd !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; }
    .bg-info-subtle { background-color: #cff4fc !important; }

    /* ============================================
       ESTILOS GENERALES DE TABLAS
       ============================================ */
    .modern-table { border-collapse: separate; border-spacing: 0; border-radius: 8px; overflow: hidden; background: white; }
    .modern-table thead th { background: #e9ecef; color: #495057; font-weight: 600; font-size: 0.75rem; padding: 8px 4px; border: 1px solid #dee2e6; vertical-align: middle; }
    .modern-table tbody td { padding: 4px; border: 1px solid #dee2e6; vertical-align: middle; }
    .modern-table tbody tr:hover { background-color: #f8f9fa; }
    .modern-table input { border: 1px solid #ced4da; text-align: center; border-radius: 4px; transition: all 0.2s ease; font-size: 0.8rem; }
    .modern-table input:focus { border-color: #d63384; box-shadow: 0 0 0 0.15rem rgba(214, 51, 132, 0.25); }
    .modern-table { width: auto; min-width: 100%; }
    .modern-table input { min-width: 55px; }
    .modern-table input[step], .modern-table input[readonly] { min-width: 80px; }

    /* ============================================
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-replica-1 { background: #fce7f3 !important; color: #9d174d; font-size: 0.75rem !important; }
    .table-header-replica-2 { background: #dbeafe !important; color: #1e40af; font-size: 0.75rem !important; }
    .table-header-replica-3 { background: #fef3c7 !important; color: #92400e; font-size: 0.75rem !important; }
    .table-header-replica-4 { background: #e0e7ff !important; color: #3730a3; font-size: 0.75rem !important; }
    .table-header-larva { background: #f3e8ff !important; color: #6b21a8; font-size: 0.7rem !important; }
    .table-header-percent { background: #ecfccb !important; color: #3f6212; font-size: 0.7rem !important; }
    .table-header-result { background: #d1e7dd !important; color: #0f5132; font-size: 0.7rem !important; }
    .table-header-inhibition { background: #fff3cd !important; color: #664d03; font-size: 0.7rem !important; }
    .table-header-ce { background: #e2d9f3 !important; color: #432874; font-size: 0.7rem !important; }

    /* ============================================
       INPUT DE FRACCIÓN
       ============================================ */
    .fraction-input { flex-wrap: nowrap; }
    .fraction-input > .form-control { min-width: 55px; }
    .fraction-input input { text-align: center; padding: 2px 4px; }
    .fraction-input .input-group-text { padding: 2px 6px; font-weight: bold; background: #f8f9fa; }

    /* ============================================
       NAVEGACIÓN CON FLECHAS
       ============================================ */
    .navigable:focus { outline: 2px solid #0d6efd; outline-offset: 1px; box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25); }

    /* ============================================
       LISTA DE CRITERIOS
       ============================================ */
    .criteria-list { list-style: none; padding-left: 0; }
    .criteria-list li { padding: 8px 0; font-size: 1rem; border-bottom: 1px solid #e9ecef; }
    .criteria-list li:last-child { border-bottom: none; }

    /* ============================================
       IMPRESIÓN
       ============================================ */
    @media print {
        @page { size: A4 landscape; margin: 5mm; }
        body { font-size: 8pt; line-height: 1.1; }
        .btn, button, nav, .navbar, .no-print { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; margin-bottom: 5px !important; break-inside: avoid; }
        .card-header { background: #f0f0f0 !important; color: #000 !important; font-weight: bold; padding: 3px 6px !important; font-size: 9pt !important; }
        .card-body { padding: 4px !important; }
        .modern-table input { border: none !important; background: transparent !important; font-size: 7pt; }
        .modern-table th, .modern-table td { padding: 2px !important; font-size: 7pt; }
        .container-fluid { padding: 0 !important; }
        .fraction-input .input-group-text { background: transparent !important; border: none !important; }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendor/flatpickr/l10n/es.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // Flatpickr
    flatpickr(".datetimepicker", { enableTime: true, time_24hr: true, dateFormat: "Y-m-d H:i", locale: "es", allowInput: true });
    flatpickr(".timepicker", { enableTime: true, noCalendar: true, time_24hr: true, dateFormat: "H:i", locale: "es", allowInput: true });

    // Navegación con flechas
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

    // Cálculo automático para CONTROL
    document.querySelectorAll('.control-larva, .control-total').forEach(input => {
        input.addEventListener('input', function() {
            const replica = this.dataset.replica;
            calculateControlPercent(replica);
            calculateControlAverages();
        });
    });

    function calculateControlPercent(replica) {
        const larva = parseFloat(document.querySelector(`input[name="control[r${replica}_larva]"]`).value) || 0;
        const total = parseFloat(document.querySelector(`input[name="control[r${replica}_total]"]`).value) || 0;
        const percentField = document.querySelector(`input[name="control[r${replica}_percent]"]`);
        if (total > 0) {
            percentField.value = ((larva / total) * 100).toFixed(2);
        } else {
            percentField.value = '';
        }
    }

    function calculateControlAverages() {
        const percents = [];
        for (let r = 1; r <= 4; r++) {
            const val = parseFloat(document.querySelector(`input[name="control[r${r}_percent]"]`).value);
            if (!isNaN(val)) percents.push(val);
        }
        if (percents.length > 0) {
            const avg = percents.reduce((a, b) => a + b, 0) / percents.length;
            document.getElementById('controlLarvalAvg').value = avg.toFixed(2);
            // Total larvas = promedio (en este caso es el mismo)
            document.getElementById('controlTotalPercent').value = avg.toFixed(2);
        } else {
            document.getElementById('controlLarvalAvg').value = '';
            document.getElementById('controlTotalPercent').value = '';
        }
        // Recalcular inhibiciones
        for (let i = 1; i <= 15; i++) {
            calculateRowAverages(i);
        }
    }

    // Cálculo automático para filas de datos
    document.querySelectorAll('.larva-input, .total-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.dataset.row;
            const replica = this.dataset.replica;
            calculateReplicaPercent(row, replica);
            calculateRowAverages(row);
        });
    });

    function calculateReplicaPercent(row, replica) {
        const larva = parseFloat(document.querySelector(`input[name="rows[${row}][r${replica}_larva]"]`).value) || 0;
        const total = parseFloat(document.querySelector(`input[name="rows[${row}][r${replica}_total]"]`).value) || 0;
        const percentField = document.querySelector(`input[name="rows[${row}][r${replica}_percent]"]`);
        if (total > 0) {
            percentField.value = ((larva / total) * 100).toFixed(2);
        } else {
            percentField.value = '';
        }
    }

    function calculateRowAverages(row) {
        const percents = [];
        for (let r = 1; r <= 4; r++) {
            const val = parseFloat(document.querySelector(`input[name="rows[${row}][r${r}_percent]"]`).value);
            if (!isNaN(val)) percents.push(val);
        }
        const avgField = document.querySelector(`input[name="rows[${row}][larval_pluteus_percent]"]`);
        const inhibField = document.querySelector(`input[name="rows[${row}][inhibition_percent]"]`);
        
        if (percents.length > 0) {
            const avg = percents.reduce((a, b) => a + b, 0) / percents.length;
            avgField.value = avg.toFixed(2);
            
            // Calcular inhibición basada en el control
            const controlAvg = parseFloat(document.getElementById('controlLarvalAvg').value) || 0;
            if (controlAvg > 0) {
                const inhibition = ((controlAvg - avg) / controlAvg) * 100;
                inhibField.value = inhibition.toFixed(2);
            } else {
                inhibField.value = '';
            }
        } else {
            avgField.value = '';
            inhibField.value = '';
        }
    }

    // Recalcular todo al cargar
    for (let r = 1; r <= 4; r++) { calculateControlPercent(r); }
    calculateControlAverages();
    for (let i = 1; i <= 15; i++) {
        for (let r = 1; r <= 4; r++) { calculateReplicaPercent(i, r); }
        calculateRowAverages(i);
    }

    // Timer functions


    // Confirmación de envío
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Confirmar actualización?', text: "Se guardarán los cambios del bioensayo de Arbacia spatuligera (Estado Larval).",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#d63384', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-floppy me-1"></i> Sí, actualizar', cancelButtonText: '<i class="bi bi-x-lg me-1"></i> Cancelar'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush