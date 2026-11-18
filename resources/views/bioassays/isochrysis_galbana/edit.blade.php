{{-- resources/views/bioassays/isochrysis_galbana/edit.blade.php --}}
@extends('layouts.app')

@section('content')
@include('bioassays._status', ['bioassay' => $isochrysis_galbana, 'section' => 'isochrysis-galbana'])
<div class="container">
    <!-- Logo -->
    <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        <span class="d-print-none">Editar</span><span class="d-none d-print-inline">Análisis</span> Bioensayo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #20c997;">
        Isochrysis galbana
    </h2>
    @include('bioassays._sheet_code', ['bioassay' => $isochrysis_galbana])

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
        $measurements = $isochrysis_galbana->measurements ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('isochrysis-galbana.update', $isochrysis_galbana->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-teal-subtle text-dark"
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
                                           name="sample" 
                                           value="{{ old('sample', $isochrysis_galbana->sample) }}" 
                                           readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="matrix" 
                                           value="{{ old('matrix', $isochrysis_galbana->matrix) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_start" 
                                           value="{{ old('bioassay_start', $isochrysis_galbana->bioassay_start) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_end" 
                                           value="{{ old('bioassay_end', $isochrysis_galbana->bioassay_end) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="analyst" 
                                           value="{{ old('analyst', $isochrysis_galbana->analyst) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= DATOS PRELIMINARES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-teal-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-eyedropper me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0 modern-table">
                        <thead>
                            <tr>
                                <th>Volumen inóculo inicial (10⁴ cel/mL)</th>
                                <th>Fecha de Cultivo (Stock)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" class="form-control navigable" 
                                               name="initial_inoculum_vol" 
                                               value="{{ old('initial_inoculum_vol', $isochrysis_galbana->initial_inoculum_vol) }}">
                                        <span class="input-group-text unit-badge">10⁴ cel/mL</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datepicker" 
                                           name="stock_culture_date" 
                                           value="{{ old('stock_culture_date', $isochrysis_galbana->stock_culture_date) }}" 
                                           placeholder="Seleccione fecha">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @include('bioassays._duration', ['check' => $isochrysis_galbana->durationChecks()['bioassay'], 'start' => 'bioassay_start', 'end' => 'bioassay_end'])
            </div>
        </div>

        {{-- ================= CRECIMIENTO Y PH (CONTROL) ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-teal-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-graph-up me-2"></i>Crecimiento y pH (Control)
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Réplicas de Control (RC)</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th class="table-header-time">24 h</th>
                                <th class="table-header-time">48 h</th>
                                <th class="table-header-time">72 h</th>
                                <th class="table-header-96h">RC1 (96 h)</th>
                                <th class="table-header-96h">RC2 (96 h)</th>
                                <th class="table-header-96h">RC3 (96 h)</th>
                                <th class="table-header-96h">RC4 (96 h)</th>
                                <th class="table-header-96h">RC5 (96 h)</th>
                                <th class="table-header-96h">RC6 (96 h)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc24h" 
                                           value="{{ old('rc24h', $isochrysis_galbana->rc24h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc48h" 
                                           value="{{ old('rc48h', $isochrysis_galbana->rc48h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc72h" 
                                           value="{{ old('rc72h', $isochrysis_galbana->rc72h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc196h" 
                                           value="{{ old('rc196h', $isochrysis_galbana->rc196h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc296h" 
                                           value="{{ old('rc296h', $isochrysis_galbana->rc296h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc396h" 
                                           value="{{ old('rc396h', $isochrysis_galbana->rc396h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc496h" 
                                           value="{{ old('rc496h', $isochrysis_galbana->rc496h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc596h" 
                                           value="{{ old('rc596h', $isochrysis_galbana->rc596h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc696h" 
                                           value="{{ old('rc696h', $isochrysis_galbana->rc696h) }}">
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
                               value="{{ old('ph_initial', $isochrysis_galbana->ph_initial) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">pH Final</label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph_final" 
                               value="{{ old('ph_final', $isochrysis_galbana->ph_final) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tasa de Crecimiento Control</label>
                        <input type="number" step="0.0001" class="form-control navigable" 
                               name="growth_rate_control" 
                               value="{{ old('growth_rate_control', $isochrysis_galbana->growth_rate_control) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MEDICIONES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-teal-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-table me-2"></i>Mediciones
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Tabla de Mediciones (96 Horas)</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 50px;">#</th>
                                <th rowspan="2" class="align-middle">Muestra o Concentración</th>
                                <th colspan="2">pH</th>
                                <th colspan="3" class="table-header-96h">Réplicas (96 h)</th>
                                <th rowspan="2" class="align-middle">Tasa Crecimiento</th>
                                <th rowspan="2" class="align-middle">% Tasa Crecimiento</th>
                                <th rowspan="2" class="align-middle">% Inhibición</th>
                                <th rowspan="2" class="align-middle">*EC<sub>50</sub></th>
                            </tr>
                            <tr>
                                <th>Inicial</th>
                                <th>Final</th>
                                <th>R1</th>
                                <th>R2</th>
                                <th>R3</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 10; $i++)
                                @php
                                    $row = $measurements[$i] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">{{ $i }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][sample_or_concentration]" 
                                               value="{{ old('measurements.'.$i.'.sample_or_concentration', $row['sample_or_concentration'] ?? '') }}"
                                               placeholder="Conc. {{ $i }}">
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
                                        <input type="number" step="0.0001" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][growth_rate]" 
                                               value="{{ old('measurements.'.$i.'.growth_rate', $row['growth_rate'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][growth_rate_percent]" 
                                               value="{{ old('measurements.'.$i.'.growth_rate_percent', $row['growth_rate_percent'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][inhibition_percent]" 
                                               value="{{ old('measurements.'.$i.'.inhibition_percent', $row['inhibition_percent'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ec50]" 
                                               value="{{ old('measurements.'.$i.'.ec50', $row['ec50'] ?? '') }}">
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
            <div class="card-header bg-teal-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-bar-chart me-2"></i>Resultados de Análisis
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">EC<sub>50</sub> (96 h)</label>
                        <input type="text" class="form-control navigable" 
                               name="ec50_detail" 
                               value="{{ old('ec50_detail', $isochrysis_galbana->ec50_detail) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Coeficiente de Variación (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="variation_coefficient" 
                                   value="{{ old('variation_coefficient', $isochrysis_galbana->variation_coefficient) }}">
                            <span class="input-group-text unit-badge">%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control navigable" name="observations" id="observations" rows="3" 
                              placeholder="Ingrese observaciones adicionales...">{{ old('observations', $isochrysis_galbana->observations) }}</textarea>
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
                        Densidad celular inicial: <strong>10⁴ cel/mL</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Tasa de crecimiento del control: <strong>≥ 0.92 día⁻¹</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Coeficiente de variación del control: <strong>≤ 7%</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        pH de las muestras: <strong>7.5 a 8.5 unidades</strong>
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

            @if(auth()->user()->can('validateBioassay', $isochrysis_galbana))
                @if($isochrysis_galbana->validated_at)
                    <span class="btn btn-outline-success btn-lg px-4 disabled">
                        <i class="bi bi-check-circle me-2"></i>Validado el
                        {{ $isochrysis_galbana->validated_at->format('d/m/Y H:i') }}
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
                $sampleEntry = $isochrysis_galbana->sampleEntry;
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

    @if(auth()->user()->can('validateBioassay', $isochrysis_galbana) && !$isochrysis_galbana->validated_at)
        <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo, y la planilla pasará a solo lectura." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('isochrysis-galbana.validate', $isochrysis_galbana->id) }}" method="POST" style="display:none">
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
       COLORES PERSONALIZADOS - TEAL/TURQUESA
       ============================================ */
    .bg-teal-subtle {
        background-color: #d2f4ea !important;
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
        font-size: 0.85rem;
        padding: 10px 6px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody td {
        padding: 6px;
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
        font-size: 0.85rem;
    }

    .modern-table input:focus {
        border-color: #20c997;
        box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25);
    }

    .modern-table {
        width: auto;
        min-width: 100%;
    }

    .modern-table input {
        min-width: 55px;
    }
    .modern-table input[step], .modern-table input[readonly] { min-width: 80px; }

    /* ============================================
       TÍTULOS DE SECCIÓN
       ============================================ */
    .section-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1.3rem;
        font-weight: 500;
        color: #20c997;
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
        background: linear-gradient(to right, transparent, #20c997, transparent);
    }

    /* ============================================
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-time {
        background: #cfe2ff !important;
        color: #084298;
    }

    .table-header-96h {
        background: #d2f4ea !important;
        color: #0f5132;
    }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge {
        background: #6c757d;
        color: white;
        font-weight: 500;
        font-size: 0.8rem;
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
            size: A4 landscape; 
            margin: 8mm; 
        }
        
        body { 
            font-size: 9pt; 
            line-height: 1.2; 
        }
        
        .btn, button, nav, .navbar, .no-print {
            display: none !important;
        }

        .card { 
            border: 1px solid #000 !important; 
            box-shadow: none !important; 
            margin-bottom: 8px !important;
            break-inside: avoid;
        }
        
        .card-header { 
            background: #f0f0f0 !important; 
            color: #000 !important; 
            font-weight: bold; 
            padding: 4px 8px !important;
            font-size: 11pt !important;
        }
        
        .card-body { 
            padding: 6px !important; 
        }

        .section-title {
            font-size: 10pt !important;
            margin: 8px 0 !important;
        }

        .modern-table input {
            border: none !important;
            background: transparent !important;
            font-size: 8pt;
        }

        .modern-table th,
        .modern-table td {
            padding: 3px !important;
            font-size: 8pt;
        }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendor/flatpickr/l10n/es.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

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

    // ============= SWEETALERT PARA CONFIRMACIÓN =============
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Confirmar actualización?',
            text: "Se guardarán los cambios del bioensayo de Isochrysis galbana.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#20c997',
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