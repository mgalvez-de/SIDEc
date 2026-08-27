{{-- resources/views/receptions/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Inicio</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('receptions.index') }}"><i class="fas fa-inbox me-1"></i>Recepciones</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('receptions.show', $reception) }}"><i class="fas fa-eye me-1"></i>{{ $reception->internal_sample_code }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-edit me-1"></i>Editar
            </li>
        </ol>
    </nav>

    <!-- Logo -->
    <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">

    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        Editar Recepción de Muestra
    </h1>
    <p class="text-center text-muted mb-4">
        <span class="badge bg-info fs-6">{{ $reception->internal_sample_code }}</span>
    </p>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4 border-0 shadow-sm" style="border-radius: 10px;">
            <strong><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="editReceptionForm" action="{{ route('receptions.update', $reception) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ===================== N° DE INFORME ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-warning-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-file-alt me-2"></i>N° de Informe
            </div>
            <div class="card-body bg-light p-3">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-warning-subtle border-0">
                                <i class="fas fa-hashtag text-warning"></i>
                            </span>
                            <input type="number" name="report_number" class="form-control form-control-lg navigable text-center fw-bold"
                                   value="{{ old('report_number', $reception->report_number) }}" placeholder="N° de Informe" required
                                   style="font-size: 1.5rem; letter-spacing: 2px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== IDENTIFICACIÓN DE MUESTRA ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-info-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-vial me-2"></i>Identificación de Muestra
            </div>
            <div class="card-body bg-light p-3">
                <div class="sample-card-edit">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag text-info me-1"></i>Identificación de la muestra
                            </label>
                            <input type="text" name="sample_identifier" class="form-control navigable"
                                   value="{{ old('sample_identifier', $reception->sample_identifier) }}" 
                                   placeholder="Ej: Muestra-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-barcode text-info me-1"></i>Código Interno de Muestra
                            </label>
                            <input type="text" name="internal_sample_code" class="form-control navigable bg-light"
                                   value="{{ old('internal_sample_code', $reception->internal_sample_code) }}" 
                                   placeholder="Código interno" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== INFORMACIÓN DE RECEPCIÓN ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-clipboard-check me-2"></i>Información de Recepción
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-thermometer-half text-danger me-1"></i>Código del Termómetro
                        </label>
                        <input type="text" name="thermometer_code" class="form-control navigable"
                               value="{{ old('thermometer_code', $reception->thermometer_code) }}" placeholder="Ej: T-001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-sliders-h text-primary me-1"></i>Factor de Corrección
                        </label>
                        <input type="text" name="correction_factor" class="form-control navigable"
                               value="{{ old('correction_factor', $reception->correction_factor) }}" placeholder="Ej: 1.02">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-check text-success me-1"></i>Fecha y Hora de Recepción
                        </label>
                        <input type="text" name="received_at" class="form-control datetimepicker navigable"
                               value="{{ old('received_at', $reception->received_at) }}" placeholder="Seleccionar fecha y hora">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-user text-secondary me-1"></i>Nombre de quien entrega
                        </label>
                        <input type="text" name="delivered_by" class="form-control navigable"
                               value="{{ old('delivered_by', $reception->delivered_by) }}" placeholder="Ej: Juan Pérez">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-building text-info me-1"></i>Cliente
                        </label>
                        <input type="text" name="client" class="form-control navigable" 
                               value="{{ old('client', $reception->client) }}" placeholder="Nombre del cliente">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clock text-warning me-1"></i>Fecha y Hora de Muestreo
                        </label>
                        <input type="text" name="sampled_at" class="form-control datetimepicker navigable"
                               value="{{ old('sampled_at', $reception->sampled_at) }}" placeholder="Seleccionar fecha y hora">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-user-check text-success me-1"></i>Nombre de quien recibe
                        </label>
                        <input type="text" name="received_by" class="form-control navigable"
                               value="{{ old('received_by', $reception->received_by) }}" placeholder="Nombre del receptor">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-water text-primary me-1"></i>Matriz
                        </label>
                        <select name="matrix" class="form-select navigable" required>
                            <option value="" disabled>Seleccione una matriz</option>
                            @php
                                $matrices = [
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
                            @foreach ($matrices as $code => $label)
                                <option value="{{ $code }}" {{ old('matrix', $reception->matrix) == $code ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-temperature-low text-info me-1"></i>Temperatura de Recepción
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="temperature_received" class="form-control navigable"
                                   value="{{ old('temperature_received', $reception->temperature_received) }}" placeholder="Ej: 20.5">
                            <span class="input-group-text unit-badge">°C</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-temperature-high text-danger me-1"></i>Temperatura Corregida
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="temperature_corrected" class="form-control navigable"
                                   value="{{ old('temperature_corrected', $reception->temperature_corrected) }}" placeholder="Ej: 21.0">
                            <span class="input-group-text unit-badge">°C</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== BIOENSAYOS ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-flask me-2"></i>Bioensayos Asignados
            </div>
            <div class="card-body bg-light p-3">
                <p class="text-muted mb-3">
                    <i class="fas fa-hand-pointer me-1"></i>Modifique los bioensayos asignados a esta muestra:
                </p>

                @php
                    $availableBioassays = [
                        ['name' => 'Daphnia magna Agudo', 'icon' => 'fa-bug', 'color' => 'success'],
                        ['name' => 'Daphnia magna Crónico', 'icon' => 'fa-bug', 'color' => 'success'],
                        ['name' => 'Isochrysis galbana', 'icon' => 'fa-seedling', 'color' => 'info'],
                        ['name' => 'Selenastrum capricornutum', 'icon' => 'fa-leaf', 'color' => 'lime'],
                        ['name' => 'Tisbe biconicornis Agua', 'icon' => 'fa-fish', 'color' => 'purple'],
                        ['name' => 'Tisbe biconicornis Sedimento', 'icon' => 'fa-fish', 'color' => 'orange'],
                        ['name' => 'Arbacia spatuligera Estado Larval', 'icon' => 'fa-star', 'color' => 'pink'],
                        ['name' => 'Arbacia spatuligera Fecundación', 'icon' => 'fa-star', 'color' => 'pink'],
                    ];
                    $selected = old('assigned_bioassays', $reception->assigned_bioassays ?? []);
                @endphp

                <div class="bioassay-grid">
                    @foreach ($availableBioassays as $index => $assay)
                        <div class="bioassay-item" data-color="{{ $assay['color'] }}">
                            <input class="bioassay-checkbox" type="checkbox" name="assigned_bioassays[]"
                                   value="{{ $assay['name'] }}" id="assay_{{ $index }}"
                                   {{ in_array($assay['name'], $selected) ? 'checked' : '' }}>
                            <label class="bioassay-label" for="assay_{{ $index }}">
                                <div class="bioassay-icon">
                                    <i class="fas {{ $assay['icon'] }}"></i>
                                </div>
                                <div class="bioassay-info">
                                    <span class="bioassay-name">{{ $assay['name'] }}</span>
                                </div>
                                <div class="bioassay-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="selected-count mt-3 text-center">
                    <span class="badge bg-secondary px-3 py-2" id="selectedCount">
                        <i class="fas fa-clipboard-list me-1"></i>
                        <span id="countNumber">0</span> bioensayo(s) seleccionado(s)
                    </span>
                </div>
            </div>
        </div>

        {{-- ===================== INFORMACIÓN DE LA PLANTILLA ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-secondary-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-file-contract me-2"></i>Información de la Plantilla
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="template-info-item">
                            <span class="template-label">Título</span>
                            <span class="template-value">{{ $reception->template->title ?? 'RECEPCIÓN DE MUESTRAS' }}</span>
                            <input type="hidden" name="title" value="{{ $reception->template->title ?? 'RECEPCIÓN DE MUESTRAS' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="template-info-item">
                            <span class="template-label">Código</span>
                            <span class="template-value">{{ $reception->template->code ?? 'RO-02.01' }}</span>
                            <input type="hidden" name="code" value="{{ $reception->template->code ?? 'RO-02.01' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="template-info-item">
                            <span class="template-label">Versión</span>
                            <span class="template-value">{{ $reception->template->version ?? '03' }}</span>
                            <input type="hidden" name="version" value="{{ $reception->template->version ?? '03' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="template-info-item">
                            <span class="template-label">Vigencia</span>
                            <span class="template-value">{{ $reception->template->validity ?? '01.09.2023' }}</span>
                            <input type="hidden" name="validity" value="{{ $reception->template->validity ?? '01.09.2023' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== BOTONES ===================== --}}
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg px-5">
                <i class="fas fa-save me-2"></i>Actualizar Recepción
            </button>
            <a href="{{ route('receptions.show', $reception) }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="fas fa-eye me-2"></i>Ver Detalle
            </a>
            <a href="{{ route('receptions.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
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
       BREADCRUMB PERSONALIZADO
       ============================================ */
    .breadcrumb-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .breadcrumb-custom .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .breadcrumb-custom .breadcrumb-item a:hover {
        color: #0d6efd;
    }

    .breadcrumb-custom .breadcrumb-item.active {
        color: #495057;
        font-weight: 500;
    }

    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-size: 1.2rem;
        color: #adb5bd;
    }

    /* ============================================
       COLORES PERSONALIZADOS
       ============================================ */
    .bg-warning-subtle { background-color: #fff3cd !important; }
    .bg-info-subtle { background-color: #cff4fc !important; }
    .bg-success-subtle { background-color: #d1e7dd !important; }
    .bg-purple-subtle { background-color: #e2d9f3 !important; }
    .bg-secondary-subtle { background-color: #e2e3e5 !important; }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge {
        background: #6c757d;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
        min-width: 45px;
        justify-content: center;
    }

    /* ============================================
       CARD DE MUESTRA
       ============================================ */
    .sample-card-edit {
        background: white;
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #17a2b8;
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.15);
    }

    /* ============================================
       GRID DE BIOENSAYOS
       ============================================ */
    .bioassay-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
    }

    .bioassay-item {
        position: relative;
    }

    .bioassay-checkbox {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .bioassay-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }

    .bioassay-label:hover {
        border-color: #adb5bd;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .bioassay-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .bioassay-info {
        flex: 1;
    }

    .bioassay-name {
        font-weight: 500;
        color: #495057;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .bioassay-check {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bioassay-check i {
        font-size: 0.7rem;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Estados checked por color */
    .bioassay-checkbox:checked + .bioassay-label {
        border-color: var(--check-color, #198754);
        background: var(--check-bg, #d1e7dd);
    }

    .bioassay-checkbox:checked + .bioassay-label .bioassay-icon {
        background: var(--check-color, #198754);
        color: white;
        transform: rotate(360deg);
    }

    .bioassay-checkbox:checked + .bioassay-label .bioassay-name {
        color: var(--check-color, #198754);
        font-weight: 600;
    }

    .bioassay-checkbox:checked + .bioassay-label .bioassay-check {
        background: var(--check-color, #198754);
        border-color: var(--check-color, #198754);
        animation: checkBounce 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bioassay-checkbox:checked + .bioassay-label .bioassay-check i {
        opacity: 1;
        transform: scale(1);
    }

    @keyframes checkBounce {
        0% { transform: scale(1); }
        30% { transform: scale(1.3); }
        50% { transform: scale(0.9); }
        70% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Colores por tipo de bioensayo */
    .bioassay-item[data-color="success"] { --check-color: #198754; --check-bg: #d1e7dd; }
    .bioassay-item[data-color="info"] { --check-color: #0dcaf0; --check-bg: #cff4fc; }
    .bioassay-item[data-color="lime"] { --check-color: #84cc16; --check-bg: #ecfccb; }
    .bioassay-item[data-color="purple"] { --check-color: #6f42c1; --check-bg: #e2d9f3; }
    .bioassay-item[data-color="orange"] { --check-color: #fd7e14; --check-bg: #ffe5cc; }
    .bioassay-item[data-color="pink"] { --check-color: #d63384; --check-bg: #f8d7da; }

    /* ============================================
       INFO DE PLANTILLA
       ============================================ */
    .template-info-item {
        background: white;
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        text-align: center;
    }

    .template-label {
        display: block;
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }

    .template-value {
        display: block;
        font-weight: 600;
        color: #495057;
        font-size: 0.95rem;
    }

    /* ============================================
       CONTADOR DE SELECCIONADOS
       ============================================ */
    #selectedCount {
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    #selectedCount.has-selection {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%) !important;
        transform: scale(1.05);
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
       FLATPICKR PERSONALIZADO
       ============================================ */
    .flatpickr-calendar {
        font-size: 15px !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3) !important;
    }

    .flatpickr-day {
        width: 42px !important;
        height: 42px !important;
        line-height: 42px !important;
        font-size: 14px !important;
        border-radius: 8px !important;
    }

    .flatpickr-weekday {
        font-size: 13px !important;
        font-weight: 600 !important;
    }

    .flatpickr-current-month {
        font-size: 16px !important;
    }

    .flatpickr-time {
        height: 55px !important;
        max-height: 55px !important;
        border-top: 1px solid #444 !important;
        margin-top: 8px !important;
        padding-top: 8px !important;
    }

    .flatpickr-time input {
        font-size: 22px !important;
        font-weight: 600 !important;
        height: 42px !important;
    }

    .flatpickr-time .flatpickr-time-separator {
        font-size: 22px !important;
        font-weight: 600 !important;
        line-height: 42px !important;
    }

    .datetimepicker {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 40px;
    }

    /* ============================================
       BOTONES
       ============================================ */
    .btn-lg {
        border-radius: 10px;
        font-weight: 500;
    }

    /* ============================================
       IMPRESIÓN
       ============================================ */
    @media print {
        .breadcrumb-custom, .btn, button { display: none !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    // ============= NAVEGACIÓN CON FLECHAS =============
    const navigableElements = document.querySelectorAll('.navigable');
    navigableElements.forEach((element, index) => {
        element.addEventListener('keydown', function(e) {
            let targetIndex = index;
            if (e.key === 'ArrowDown' || (e.key === 'Enter' && !e.shiftKey)) {
                e.preventDefault();
                targetIndex = index + 1;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                targetIndex = index - 1;
            }
            if (targetIndex >= 0 && targetIndex < navigableElements.length && targetIndex !== index) {
                navigableElements[targetIndex].focus();
            }
        });
    });

    // ============= CONTADOR DE BIOENSAYOS =============
    function updateBioassayCount() {
        const count = document.querySelectorAll('.bioassay-checkbox:checked').length;
        const countElement = document.getElementById('countNumber');
        const badgeElement = document.getElementById('selectedCount');
        
        countElement.textContent = count;
        
        if (count > 0) {
            badgeElement.classList.add('has-selection');
        } else {
            badgeElement.classList.remove('has-selection');
        }
    }

    document.querySelectorAll('.bioassay-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBioassayCount);
    });
    
    // Inicializar contador
    updateBioassayCount();

    // ============= CONFIRMACIÓN DE ENVÍO =============
    document.getElementById('editReceptionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;

        const bioassayCount = document.querySelectorAll('.bioassay-checkbox:checked').length;
        
        let message = 'Se actualizará la información de la recepción';
        if (bioassayCount > 0) {
            message += ` con <strong>${bioassayCount}</strong> bioensayo(s) asignado(s)`;
        }

        Swal.fire({
            title: '¿Guardar cambios?',
            html: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save me-1"></i>Sí, actualizar',
            cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush