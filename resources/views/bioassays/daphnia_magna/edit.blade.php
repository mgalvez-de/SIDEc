{{-- resources/views/bioassays/daphnia_magna/edit.blade.php --}}
@extends('layouts.app')

@section('content')
@include('bioassays._status', ['bioassay' => $daphnia_magna, 'section' => 'daphnia-magna'])
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc"
            style="height: 80px; display: block; margin: 0 auto 20px auto;">

        <!-- Título Principal -->
        <h1 class="mb-2 text-secondary text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
            <span class="d-print-none">Editar</span><span class="d-none d-print-inline">Análisis</span> Bioensayo Agudo
        </h1>
        <h2 class="mb-3 text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #198754;">
            Daphnia magna
        </h2>
        <p class="text-center text-muted mb-4">RT-01.05 | Versión: 03 | Vigencia: 01.09.2023</p>

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
            // Extraer datos de las estructuras JSON
            $preliminary_table = $daphnia_magna->preliminary_table ?? [];

            $def_24h_data = $daphnia_magna->definitive_24h ?? [];
            $def_48h_data = $daphnia_magna->definitive_48h ?? [];

            // Extraer valores de concentración
            $def_24h_conc_values = $def_24h_data['concentration_values'] ?? [];
            $def_48h_conc_values = $def_48h_data['concentration_values'] ?? [];

            // Extraer filas
            $def_24h_rows = $def_24h_data['rows'] ?? [];
            $def_48h_rows = $def_48h_data['rows'] ?? [];
        @endphp

        <form id="bioassayForm" action="{{ route('daphnia-magna.update', $daphnia_magna->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- ================= DATOS GENERALES ================= --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-success-subtle text-dark"
                    style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-card-checklist me-2"></i>Datos Generales
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
                                            name="sample" value="{{ old('sample', $daphnia_magna->sample) }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" name="matrix"
                                            value="{{ old('matrix', $daphnia_magna->matrix) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datetimepicker"
                                            name="start_time"
                                            value="{{ old('start_time', $daphnia_magna->preliminary_start_at) }}"
                                            placeholder="Seleccione fecha y hora">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datetimepicker"
                                            name="end_time"
                                            value="{{ old('end_time', $daphnia_magna->preliminary_end_at) }}"
                                            placeholder="Seleccione fecha y hora">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" name="analyst"
                                            value="{{ old('analyst', $daphnia_magna->analyst) }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================= ENSAYO PRELIMINAR ================= --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-success-subtle text-dark"
                    style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-eyedropper me-2"></i>Ensayo Preliminar
                </div>
                <div class="card-body bg-light p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>Temperatura de la muestra (°C)</th>
                                    <th>Fecha de agua reconstituida</th>
                                    <th>pH de la muestra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" class="form-control navigable"
                                                name="sample_temperature"
                                                value="{{ old('sample_temperature', $daphnia_magna->preliminary_sample_temperature) }}">
                                            <span class="input-group-text unit-badge">°C</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datepicker"
                                            name="reconstituted_water_date"
                                            value="{{ old('reconstituted_water_date', $daphnia_magna->preliminary_reconstituted_water_date) }}"
                                            placeholder="Seleccione fecha">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable"
                                            name="sample_ph"
                                            value="{{ old('sample_ph', $daphnia_magna->preliminary_sample_ph) }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @include('bioassays._duration', ['check' => $daphnia_magna->durationChecks()['preliminary'], 'start' => 'start_time', 'end' => 'end_time'])

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
                                                value="{{ old('pre_concentration_row' . $i, $row['concentration'] ?? '') }}"
                                                placeholder="Conc. {{ $i }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable"
                                                name="pre_24h_rep1_row{{ $i }}"
                                                value="{{ old('pre_24h_rep1_row' . $i, $row['24h_rep1'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable"
                                                name="pre_24h_rep2_row{{ $i }}"
                                                value="{{ old('pre_24h_rep2_row' . $i, $row['24h_rep2'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0"
                                                class="form-control form-control-sm navigable sum-field"
                                                name="pre_24h_sum_row{{ $i }}"
                                                value="{{ old('pre_24h_sum_row' . $i, $row['24h_sum'] ?? '') }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable"
                                                name="pre_48h_rep1_row{{ $i }}"
                                                value="{{ old('pre_48h_rep1_row' . $i, $row['48h_rep1'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable"
                                                name="pre_48h_rep2_row{{ $i }}"
                                                value="{{ old('pre_48h_rep2_row' . $i, $row['48h_rep2'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0"
                                                class="form-control form-control-sm navigable sum-field"
                                                name="pre_48h_sum_row{{ $i }}"
                                                value="{{ old('pre_48h_sum_row' . $i, $row['48h_sum'] ?? '') }}" readonly>
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
                <div class="card-header bg-success-subtle text-dark"
                    style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-eyedropper me-2"></i>Ensayo Definitivo
                </div>
                <div class="card-body bg-light p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>Fecha y hora de inicio</th>
                                    <th>Fecha y hora de término</th>
                                    <th>Temperatura (°C)</th>
                                    <th>Fecha de agua reconstituida</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datetimepicker"
                                            name="def_start_time"
                                            value="{{ old('def_start_time', $daphnia_magna->definitive_start_at) }}"
                                            placeholder="Seleccione fecha y hora">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datetimepicker"
                                            name="def_end_time"
                                            value="{{ old('def_end_time', $daphnia_magna->definitive_end_at) }}"
                                            placeholder="Seleccione fecha y hora">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" class="form-control navigable"
                                                name="def_temperature"
                                                value="{{ old('def_temperature', $daphnia_magna->definitive_sample_temperature) }}">
                                            <span class="input-group-text unit-badge">°C</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datepicker"
                                            name="def_reconstituted_water_date"
                                            value="{{ old('def_reconstituted_water_date', $daphnia_magna->definitive_reconstituted_water_date) }}"
                                            placeholder="Seleccione fecha">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @include('bioassays._duration', ['check' => $daphnia_magna->durationChecks()['definitive'], 'start' => 'def_start_time', 'end' => 'def_end_time'])

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
                                            <input type="text"
                                                class="form-control form-control-sm navigable concentration-header"
                                                name="def_24h_conc{{ $c }}_value"
                                                value="{{ old('def_24h_conc' . $c . '_value', $def_24h_conc_values[$c - 1] ?? '') }}"
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
                                                value="{{ old('def_24h_control_rep' . $r, $row['control'] ?? '') }}">
                                        </td>
                                        @for ($c = 1; $c <= $concentrations; $c++)
                                            <td>
                                                <input type="number" min="0" class="form-control form-control-sm navigable"
                                                    name="def_24h_conc{{ $c }}_rep{{ $r }}"
                                                    value="{{ old('def_24h_conc' . $c . '_rep' . $r, $row_concs[$c - 1] ?? '') }}">
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                                @php
                                    $totals_24h = $def_24h_rows[$replicas] ?? [];
                                    $totals_24h_concs = $totals_24h['concentrations_sum'] ?? [];
                                @endphp
                                <tr class="table-warning">
                                    <td class="fw-bold">∑ D. magna muertas</td>
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
                                                value="{{ old('def_24h_conc' . $c . '_sum', $totals_24h_concs[$c - 1] ?? '') }}"
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
                                            <input type="text"
                                                class="form-control form-control-sm navigable concentration-header"
                                                name="def_48h_conc{{ $c }}_value"
                                                value="{{ old('def_48h_conc' . $c . '_value', $def_48h_conc_values[$c - 1] ?? '') }}"
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
                                                value="{{ old('def_48h_control_rep' . $r, $row['control'] ?? '') }}">
                                        </td>
                                        @for ($c = 1; $c <= $concentrations; $c++)
                                            <td>
                                                <input type="number" min="0" class="form-control form-control-sm navigable"
                                                    name="def_48h_conc{{ $c }}_rep{{ $r }}"
                                                    value="{{ old('def_48h_conc' . $c . '_rep' . $r, $row_concs[$c - 1] ?? '') }}">
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                                @php
                                    $totals_48h = $def_48h_rows[$replicas] ?? [];
                                    $totals_48h_concs = $totals_48h['concentrations_sum'] ?? [];
                                @endphp
                                <tr class="table-warning">
                                    <td class="fw-bold">∑ D. magna muertas</td>
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
                                                value="{{ old('def_48h_conc' . $c . '_sum', $totals_48h_concs[$c - 1] ?? '') }}"
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
                <div class="card-header bg-success-subtle text-dark"
                    style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-bar-chart me-2"></i>Resultados de Análisis
                </div>
                <div class="card-body bg-light p-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Inmovilidad del control</label>
                            <div class="input-group">
                                <input type="text" class="form-control navigable" name="control_immobility"
                                    value="{{ old('control_immobility', $daphnia_magna->control_immobility) }}">
                                <span class="input-group-text unit-badge">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">CL<sub>50</sub> 24 h</label>
                            <input type="text" class="form-control navigable" name="cl50_24h"
                                value="{{ old('cl50_24h', $daphnia_magna->cl50_24h) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">CL<sub>50</sub> 48 h</label>
                            <input type="text" class="form-control navigable" name="cl50_48h"
                                value="{{ old('cl50_48h', $daphnia_magna->cl50_48h) }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea class="form-control navigable" name="observations" id="observations" rows="3"
                            placeholder="Ingrese observaciones adicionales...">{{ old('observations', $daphnia_magna->observations) }}</textarea>
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
                            <i class="bi bi-arrow-right text-success me-2"></i>
                            Rango de Aceptabilidad tóxico de referencia CrL<sub>50</sub> (24-48 h): <strong>0,60 mg/L a 1,70
                                mg/L</strong>
                        </li>
                        <li>
                            <i class="bi bi-arrow-right text-success me-2"></i>
                            Rango de Aceptabilidad Control Inmovilidad: <strong>≤ 10%</strong>
                        </li>
                        <li>
                            <i class="bi bi-arrow-right text-success me-2"></i>
                            pH de las muestras: <strong>6 a 9 unidades</strong>
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

                @if(auth()->user()->can('validateBioassay', $daphnia_magna))
                    @if($daphnia_magna->validated_at)
                        <span class="btn btn-outline-success btn-lg px-4 disabled">
                            <i class="bi bi-check-circle me-2"></i>Validado el
                            {{ $daphnia_magna->validated_at->format('d/m/Y H:i') }}
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
                    $sampleEntry = $daphnia_magna->sampleEntry;
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

        @if(auth()->user()->can('validateBioassay', $daphnia_magna) && !$daphnia_magna->validated_at)
            <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo, y la planilla pasará a solo lectura." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('daphnia-magna.validate', $daphnia_magna->id) }}" method="POST" style="display:none">
                @csrf
                @method('PATCH')
            </form>
        @endif
    </div>
@endsection

@push('head')
    <!-- Flatpickr - Tema Dark -->
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
@endpush

@push('styles')
    <style>
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
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        /* ============================================
           TÍTULOS DE SECCIÓN
           ============================================ */
        .section-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: #198754;
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
            background: linear-gradient(to right, transparent, #198754, transparent);
        }

        /* ============================================
           HEADERS DE TABLA COLOREADOS
           ============================================ */
        .table-header-24h {
            background: #cfe2ff !important;
            color: #084298;
        }

        .table-header-48h {
            background: #d1e7dd !important;
            color: #0f5132;
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
            border: 2px dashed #198754 !important;
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
        .bg-success-subtle {
            background-color: #d1e7dd !important;
        }

        .bg-secondary-subtle {
            background-color: #e2e3e5 !important;
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

            .btn,
            button,
            nav,
            .navbar,
            .no-print {
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
    <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendor/flatpickr/l10n/es.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

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
                element.addEventListener('keydown', function (e) {
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
            document.getElementById('bioassayForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Confirmar actualización?',
                    text: "Se guardarán los cambios del bioensayo de Daphnia magna.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-floppy me-1"></i> Sí, actualizar',
                    cancelButtonText: '<i class="bi bi-x-lg me-1"></i> Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush