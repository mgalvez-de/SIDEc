{{-- resources/views/receptions/show.blade.php --}}
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
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-eye me-1"></i>{{ $reception->internal_sample_code }}
            </li>
        </ol>
    </nav>

    <!-- Logo -->
    <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">

    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        Detalle de Recepción
    </h1>
    <p class="text-center text-muted mb-4">
        <span class="badge bg-info fs-6">{{ $reception->internal_sample_code }}</span>
    </p>

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
                        <div class="form-control form-control-lg text-center fw-bold bg-white"
                             style="font-size: 1.5rem; letter-spacing: 2px;">
                            {{ $reception->report_number ?? 'Sin asignar' }}
                        </div>
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
                        <div class="form-control bg-white">{{ $reception->sample_identifier ?? 'No especificado' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-barcode text-info me-1"></i>Código Interno de Muestra
                        </label>
                        <div class="form-control bg-light fw-bold">{{ $reception->internal_sample_code ?? 'No especificado' }}</div>
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
                    <div class="form-control bg-white">{{ $reception->thermometer_code ?? 'No especificado' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-sliders-h text-primary me-1"></i>Factor de Corrección
                    </label>
                    <div class="form-control bg-white">{{ $reception->correction_factor ?? 'No especificado' }}</div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-check text-success me-1"></i>Fecha y Hora de Recepción
                    </label>
                    <div class="form-control bg-white">
                        @if($reception->received_at)
                            {{ \Carbon\Carbon::parse($reception->received_at)->format('d/m/Y H:i') }}
                        @else
                            No especificado
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user text-secondary me-1"></i>Nombre de quien entrega
                    </label>
                    <div class="form-control bg-white">{{ $reception->delivered_by ?? 'No especificado' }}</div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-building text-info me-1"></i>Cliente
                    </label>
                    <div class="form-control bg-white">{{ $reception->client ?? 'No especificado' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-clock text-warning me-1"></i>Fecha y Hora de Muestreo
                    </label>
                    <div class="form-control bg-white">
                        @if($reception->sampled_at)
                            {{ \Carbon\Carbon::parse($reception->sampled_at)->format('d/m/Y H:i') }}
                        @else
                            No especificado
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user-check text-success me-1"></i>Nombre de quien recibe
                    </label>
                    <div class="form-control bg-white">{{ $reception->received_by ?? 'No especificado' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-water text-primary me-1"></i>Matriz
                    </label>
                    <div class="form-control bg-white">
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
                        {{ $matrices[$reception->matrix] ?? $reception->matrix ?? 'No especificado' }}
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-temperature-low text-info me-1"></i>Temperatura de Recepción
                    </label>
                    <div class="input-group">
                        <div class="form-control bg-white">{{ $reception->temperature_received ?? 'No especificado' }}</div>
                        <span class="input-group-text unit-badge">°C</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-temperature-high text-danger me-1"></i>Temperatura Corregida
                    </label>
                    <div class="input-group">
                        <div class="form-control bg-white">{{ $reception->temperature_corrected ?? 'No especificado' }}</div>
                        <span class="input-group-text unit-badge">°C</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== BIOENSAYOS ===================== --}}
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-header bg-purple-subtle text-dark d-flex justify-content-between align-items-center"
            style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <span><i class="fas fa-flask me-2"></i>Bioensayos Asignados</span>
            <span class="badge bg-secondary">{{ count($reception->assigned_bioassays ?? []) }} asignado(s)</span>
        </div>
        <div class="card-body bg-light p-3">
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
                $assigned = $reception->assigned_bioassays ?? [];
            @endphp

            @if(count($assigned) > 0)
                <div class="bioassay-grid">
                    @foreach ($availableBioassays as $index => $assay)
                        @php
                            $isAssigned = in_array($assay['name'], $assigned);
                        @endphp
                        <div class="bioassay-item {{ $isAssigned ? 'assigned' : 'not-assigned' }}" data-color="{{ $assay['color'] }}">
                            <div class="bioassay-label">
                                <div class="bioassay-icon">
                                    <i class="fas {{ $assay['icon'] }}"></i>
                                </div>
                                <div class="bioassay-info">
                                    <span class="bioassay-name">{{ $assay['name'] }}</span>
                                </div>
                                <div class="bioassay-check">
                                    @if($isAssigned)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning border-0 d-flex align-items-center mb-0" style="border-radius: 8px;">
                    <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                    <div>No hay bioensayos asignados a esta muestra.</div>
                </div>
            @endif
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
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="template-info-item">
                        <span class="template-label">Código</span>
                        <span class="template-value">{{ $reception->template->code ?? 'RO-02.01' }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="template-info-item">
                        <span class="template-label">Versión</span>
                        <span class="template-value">{{ $reception->template->version ?? '03' }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="template-info-item">
                        <span class="template-label">Vigencia</span>
                        <span class="template-value">{{ $reception->template->validity ?? '01.09.2023' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== INFORMACIÓN DEL SISTEMA ===================== --}}
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-header bg-dark text-white"
            style="font-size: 1.1rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <i class="fas fa-history me-2"></i>Información del Sistema
        </div>
        <div class="card-body bg-light p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-plus-circle text-success"></i>
                        <span class="text-muted">Creado:</span>
                        <span class="fw-bold">{{ $reception->created_at ? $reception->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-edit text-primary"></i>
                        <span class="text-muted">Última actualización:</span>
                        <span class="fw-bold">{{ $reception->updated_at ? $reception->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== BOTONES ===================== --}}
    <div class="d-flex justify-content-center gap-3 mb-5">
        <a href="{{ route('receptions.edit', $reception) }}" class="btn btn-success btn-lg px-5">
            <i class="fas fa-edit me-2"></i>Editar Recepción
        </a>
        <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
        <a href="{{ route('receptions.index') }}" class="btn btn-secondary btn-lg px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
        </a>
        <form action="{{ route('receptions.destroy', $reception) }}" method="POST" class="d-inline" id="deleteForm">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-lg px-4">
                <i class="fas fa-trash-alt me-2"></i>Eliminar
            </button>
        </form>
    </div>
</div>
@endsection

@push('head')
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
       FORM-CONTROL COMO DISPLAY
       ============================================ */
    .form-control {
        display: flex;
        align-items: center;
        min-height: calc(1.5em + 0.75rem + 2px);
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

    .bioassay-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        color: #6c757d;
    }

    /* Estado asignado */
    .bioassay-item.assigned .bioassay-label {
        border-color: var(--check-color, #198754);
        background: var(--check-bg, #d1e7dd);
    }

    .bioassay-item.assigned .bioassay-icon {
        background: var(--check-color, #198754);
        color: white;
    }

    .bioassay-item.assigned .bioassay-name {
        color: var(--check-color, #198754);
        font-weight: 600;
    }

    .bioassay-item.assigned .bioassay-check {
        background: var(--check-color, #198754);
        border-color: var(--check-color, #198754);
    }

    .bioassay-item.assigned .bioassay-check i {
        color: white;
    }

    /* Estado no asignado */
    .bioassay-item.not-assigned .bioassay-label {
        opacity: 0.5;
        background: #f8f9fa;
    }

    .bioassay-item.not-assigned .bioassay-check {
        background: #dc3545;
        border-color: #dc3545;
    }

    .bioassay-item.not-assigned .bioassay-check i {
        color: white;
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
        .breadcrumb-custom, .btn, button, form, #deleteForm { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .bioassay-item.not-assigned { display: none; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Confirmación de eliminación
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Eliminar esta recepción?',
            html: `<p>Se eliminará permanentemente la recepción <strong>{{ $reception->internal_sample_code }}</strong></p>
                   <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt me-1"></i>Sí, eliminar',
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