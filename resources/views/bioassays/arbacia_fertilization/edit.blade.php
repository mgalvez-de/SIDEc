{{-- resources/views/bioassays/arbacia_fertilization/edit.blade.php --}}
@extends('layouts.app')

@section('content')
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
        Arbacia spatuligera - Fecundación
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
        $rows = $arbacia_fertilization->rows_data ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('arbacia-fertilization.update', $arbacia_fertilization->id) }}" method="POST" novalidate>
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
                               value="{{ old('sample', $arbacia_fertilization->sample) }}" 
                               readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-water text-primary me-1"></i>Matriz
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="matrix" 
                               value="{{ old('matrix', $arbacia_fertilization->matrix) }}">
                    </div>
                </div>

                <div class="row g-3 align-items-end mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar3 text-success me-1"></i>Fecha y hora inicio Bioensayo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="bioassay_start" 
                               value="{{ old('bioassay_start', $arbacia_fertilization->bioassay_start) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-person text-secondary me-1"></i>Analista
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="analyst" 
                               value="{{ old('analyst', $arbacia_fertilization->analyst) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-percent text-info me-1"></i>% Fecundación Control
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="control_fertilization_percentage" 
                                   value="{{ old('control_fertilization_percentage', $arbacia_fertilization->control_fertilization_percentage) }}">
                            <span class="input-group-text unit-badge">%</span>
                        </div>
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
                            <i class="bi bi-hourglass-top text-warning me-1"></i>Hora adición de espermios
                        </label>
                        <input type="text" class="form-control navigable timepicker" 
                               name="sperm_addition_time" 
                               value="{{ old('sperm_addition_time', $arbacia_fertilization->sperm_addition_time) }}" 
                               placeholder="HH:MM">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-egg text-danger me-1"></i>Hora adición de ovocitos
                        </label>
                        <input type="text" class="form-control navigable timepicker" 
                               name="egg_addition_time" 
                               value="{{ old('egg_addition_time', $arbacia_fertilization->egg_addition_time) }}" 
                               placeholder="HH:MM">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-hourglass-bottom text-info me-1"></i>Hora término fijación
                        </label>
                        <input type="text" class="form-control navigable timepicker" 
                               name="fixation_time_end" 
                               value="{{ old('fixation_time_end', $arbacia_fertilization->fixation_time_end) }}" 
                               placeholder="HH:MM">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calculator text-success me-1"></i>Fecha/hora término conteo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="count_end_datetime" 
                               value="{{ old('count_end_datetime', $arbacia_fertilization->count_end_datetime) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                </div>

                @include('bioassays._duration', ['check' => $arbacia_fertilization->durationChecks()['bioassay'], 'start' => 'bioassay_start', 'end' => 'fixation_time_end'])
            </div>
        </div>

        {{-- ================= TABLA DE DATOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-pink-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-table me-2"></i>Datos de Fecundación
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 50px;">#</th>
                                <th rowspan="2" class="align-middle" style="width: 120px;">Concentración</th>
                                <th colspan="2" class="table-header-replica">Réplica 1</th>
                                <th colspan="2" class="table-header-replica">Réplica 2</th>
                                <th colspan="2" class="table-header-replica">Réplica 3</th>
                                <th rowspan="2" class="align-middle table-header-result">% Fecundación</th>
                                <th rowspan="2" class="align-middle table-header-result">% Inhibición</th>
                                <th rowspan="2" class="align-middle table-header-ci">CI</th>
                            </tr>
                            <tr>
                                <th class="table-header-nf">NF/Total</th>
                                <th class="table-header-fert">% Fec.</th>
                                <th class="table-header-nf">NF/Total</th>
                                <th class="table-header-fert">% Fec.</th>
                                <th class="table-header-nf">NF/Total</th>
                                <th class="table-header-fert">% Fec.</th>
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
                                               placeholder="{{ $i == 1 ? 'Control' : 'Conc. '.$i }}">
                                    </td>
                                    {{-- Réplica 1 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable nf-input" 
                                                   name="rows[{{ $i }}][r1_nf]" 
                                                   value="{{ old('rows.'.$i.'.r1_nf', $row['r1_nf'] ?? '') }}"
                                                   placeholder="NF"
                                                   data-row="{{ $i }}" data-replica="1">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r1_total]" 
                                                   value="{{ old('rows.'.$i.'.r1_total', $row['r1_total'] ?? '') }}"
                                                   placeholder="Total"
                                                   data-row="{{ $i }}" data-replica="1">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-fert bg-light" 
                                               name="rows[{{ $i }}][r1_fert]" 
                                               value="{{ old('rows.'.$i.'.r1_fert', $row['r1_fert'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="1" readonly>
                                    </td>
                                    {{-- Réplica 2 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable nf-input" 
                                                   name="rows[{{ $i }}][r2_nf]" 
                                                   value="{{ old('rows.'.$i.'.r2_nf', $row['r2_nf'] ?? '') }}"
                                                   placeholder="NF"
                                                   data-row="{{ $i }}" data-replica="2">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r2_total]" 
                                                   value="{{ old('rows.'.$i.'.r2_total', $row['r2_total'] ?? '') }}"
                                                   placeholder="Total"
                                                   data-row="{{ $i }}" data-replica="2">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-fert bg-light" 
                                               name="rows[{{ $i }}][r2_fert]" 
                                               value="{{ old('rows.'.$i.'.r2_fert', $row['r2_fert'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="2" readonly>
                                    </td>
                                    {{-- Réplica 3 --}}
                                    <td>
                                        <div class="input-group input-group-sm fraction-input">
                                            <input type="number" class="form-control navigable nf-input" 
                                                   name="rows[{{ $i }}][r3_nf]" 
                                                   value="{{ old('rows.'.$i.'.r3_nf', $row['r3_nf'] ?? '') }}"
                                                   placeholder="NF"
                                                   data-row="{{ $i }}" data-replica="3">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control navigable total-input" 
                                                   name="rows[{{ $i }}][r3_total]" 
                                                   value="{{ old('rows.'.$i.'.r3_total', $row['r3_total'] ?? '') }}"
                                                   placeholder="Total"
                                                   data-row="{{ $i }}" data-replica="3">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable replica-fert bg-light" 
                                               name="rows[{{ $i }}][r3_fert]" 
                                               value="{{ old('rows.'.$i.'.r3_fert', $row['r3_fert'] ?? '') }}"
                                               data-row="{{ $i }}" data-replica="3" readonly>
                                    </td>
                                    {{-- Resultados --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable avg-fert bg-success-subtle" 
                                               name="rows[{{ $i }}][avg_fertilization]" 
                                               value="{{ old('rows.'.$i.'.avg_fertilization', $row['avg_fertilization'] ?? '') }}"
                                               data-row="{{ $i }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable inhibition bg-warning-subtle" 
                                               name="rows[{{ $i }}][inhibition]" 
                                               value="{{ old('rows.'.$i.'.inhibition', $row['inhibition'] ?? '') }}"
                                               data-row="{{ $i }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="rows[{{ $i }}][ci]" 
                                               value="{{ old('rows.'.$i.'.ci', $row['ci'] ?? '') }}">
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
                        <label class="form-label fw-bold">CI<sub>50</sub></label>
                        <input type="text" class="form-control navigable" 
                               name="ci50" 
                               value="{{ old('ci50', $arbacia_fertilization->ci50) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea class="form-control navigable" name="observations" rows="2" 
                                  placeholder="Ingrese observaciones adicionales...">{{ old('observations', $arbacia_fertilization->observations) }}</textarea>
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
                        Fecundación en control: <strong>≥ 70%</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-pink me-2"></i>
                        Tiempo de exposición: <strong>60 minutos</strong>
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

            @if(auth()->user()->can('validateBioassay', $arbacia_fertilization))
                @if($arbacia_fertilization->validated_at)
                    <span class="btn btn-outline-success btn-lg px-4 disabled">
                        <i class="bi bi-check-circle me-2"></i>Validado el
                        {{ $arbacia_fertilization->validated_at->format('d/m/Y H:i') }}
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
                $sampleEntry = $arbacia_fertilization->sampleEntry;
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

    @if(auth()->user()->can('validateBioassay', $arbacia_fertilization) && !$arbacia_fertilization->validated_at)
        <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo. Esta acción no se puede deshacer." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('arbacia-fertilization.validate', $arbacia_fertilization->id) }}" method="POST" style="display:none">
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
    .bg-pink-subtle {
        background-color: #fce7f3 !important;
    }

    .text-pink {
        color: #d63384 !important;
    }

    .bg-success-subtle { background-color: #d1e7dd !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; }

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
        font-size: 0.8rem;
    }

    .modern-table input:focus {
        border-color: #d63384;
        box-shadow: 0 0 0 0.15rem rgba(214, 51, 132, 0.25);
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
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-replica {
        background: #fce7f3 !important;
        color: #9d174d;
        font-size: 0.75rem !important;
    }

    .table-header-nf {
        background: #fef3c7 !important;
        color: #92400e;
        font-size: 0.7rem !important;
    }

    .table-header-fert {
        background: #dbeafe !important;
        color: #1e40af;
        font-size: 0.7rem !important;
    }

    .table-header-result {
        background: #d1e7dd !important;
        color: #0f5132;
        font-size: 0.7rem !important;
    }

    .table-header-ci {
        background: #e2d9f3 !important;
        color: #432874;
        font-size: 0.7rem !important;
    }

    /* ============================================
       INPUT DE FRACCIÓN
       ============================================ */
    .fraction-input {
        flex-wrap: nowrap;
    }

    .fraction-input > .form-control {
        min-width: 55px;
    }

    .fraction-input input {
        text-align: center;
        padding: 2px 4px;
    }

    .fraction-input .input-group-text {
        padding: 2px 6px;
        font-weight: bold;
        background: #f8f9fa;
    }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge {
        background: #6c757d;
        color: white;
        font-weight: 500;
        font-size: 0.8rem;
        min-width: 40px;
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

    @keyframes pulse-grace {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.005); }
    }

    @keyframes pulse-expired {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* ============================================
       BARRA DE PROGRESO
       ============================================ */
    .progress-wrapper { position: relative; }

    .progress-bar {
        transition: width 1s linear, background-color 0.5s ease;
    }

    .progress-bar.bg-pink {
        background: linear-gradient(90deg, #d63384 0%, #f472b6 100%) !important;
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

    // Cálculo automático de porcentaje de fecundación
    document.querySelectorAll('.nf-input, .total-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.dataset.row;
            const replica = this.dataset.replica;
            calculateReplicaFertilization(row, replica);
            calculateRowAverages(row);
        });
    });

    function calculateReplicaFertilization(row, replica) {
        const nf = parseFloat(document.querySelector(`input[name="rows[${row}][r${replica}_nf]"]`).value) || 0;
        const total = parseFloat(document.querySelector(`input[name="rows[${row}][r${replica}_total]"]`).value) || 0;
        const fertField = document.querySelector(`input[name="rows[${row}][r${replica}_fert]"]`);
        
        if (total > 0) {
            const fert = ((total - nf) / total) * 100;
            fertField.value = fert.toFixed(2);
        } else {
            fertField.value = '';
        }
    }

    function calculateRowAverages(row) {
        const r1 = parseFloat(document.querySelector(`input[name="rows[${row}][r1_fert]"]`).value) || null;
        const r2 = parseFloat(document.querySelector(`input[name="rows[${row}][r2_fert]"]`).value) || null;
        const r3 = parseFloat(document.querySelector(`input[name="rows[${row}][r3_fert]"]`).value) || null;
        
        const values = [r1, r2, r3].filter(v => v !== null);
        const avgField = document.querySelector(`input[name="rows[${row}][avg_fertilization]"]`);
        const inhibField = document.querySelector(`input[name="rows[${row}][inhibition]"]`);
        
        if (values.length > 0) {
            const avg = values.reduce((a, b) => a + b, 0) / values.length;
            avgField.value = avg.toFixed(2);
            
            // Calcular inhibición basada en el control (fila 1)
            const controlAvg = parseFloat(document.querySelector(`input[name="rows[1][avg_fertilization]"]`).value) || 0;
            if (controlAvg > 0 && row > 1) {
                const inhibition = ((controlAvg - avg) / controlAvg) * 100;
                inhibField.value = inhibition.toFixed(2);
            } else {
                inhibField.value = row == 1 ? '0.00' : '';
            }
        } else {
            avgField.value = '';
            inhibField.value = '';
        }
    }

    // Recalcular todas las filas al cargar
    for (let i = 1; i <= 15; i++) {
        for (let r = 1; r <= 3; r++) {
            calculateReplicaFertilization(i, r);
        }
        calculateRowAverages(i);
    }

    // Timer functions


    // Confirmación de envío
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Confirmar actualización?', text: "Se guardarán los cambios del bioensayo de Arbacia spatuligera (Fecundación).",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#d63384', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-floppy me-1"></i> Sí, actualizar', cancelButtonText: '<i class="bi bi-x-lg me-1"></i> Cancelar'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush