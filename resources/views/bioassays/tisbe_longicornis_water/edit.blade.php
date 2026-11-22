{{-- resources/views/bioassays/tisbelongicornis_water/edit.blade.php --}}
@extends('layouts.app')

@section('content')
@include('bioassays._status', ['bioassay' => $tisbe_longicornis_water, 'section' => 'tisbe-longicornis-water'])
<div class="container px-4">
    <!-- Logo -->
    <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        <span class="d-print-none">Editar</span><span class="d-none d-print-inline">Análisis</span> Bioensayo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #6f42c1;">
        Tisbe longicornis (Agua)
    </h2>
    @include('bioassays._sheet_code', ['bioassay' => $tisbe_longicornis_water])

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
        $samplesData = $tisbe_longicornis_water->samples_data ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('tisbe-longicornis-water.update', $tisbe_longicornis_water->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
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
                                           value="{{ old('sample', $tisbe_longicornis_water->sample) }}" 
                                           readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="matrix" 
                                           value="{{ old('matrix', $tisbe_longicornis_water->matrix) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_start" 
                                           value="{{ old('bioassay_start', $tisbe_longicornis_water->bioassay_start) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_end" 
                                           value="{{ old('bioassay_end', $tisbe_longicornis_water->bioassay_end) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="analyst" 
                                           value="{{ old('analyst', $tisbe_longicornis_water->analyst) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= DATOS PRELIMINARES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-eyedropper me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Volumen inóculo inicial</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="initial_inoculum" 
                                   value="{{ old('initial_inoculum', $tisbe_longicornis_water->initial_inoculum) }}">
                            <span class="input-group-text unit-badge">10⁴ cel/mL</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha de Cultivo (Stock)</label>
                        <input type="text" class="form-control navigable datepicker" 
                               name="stock_culture_date" 
                               value="{{ old('stock_culture_date', $tisbe_longicornis_water->stock_culture_date) }}" 
                               placeholder="Seleccione fecha">
                    </div>
                </div>

                @include('bioassays._duration', ['check' => $tisbe_longicornis_water->durationChecks()['bioassay'], 'start' => 'bioassay_start', 'end' => 'bioassay_end'])
            </div>
        </div>

        {{-- ================= TABLA PRINCIPAL DE MUESTRAS Y LECTURAS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-table me-2"></i>Muestras y Lecturas
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Tabla de Mortalidad (24H y 48H)</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table compact-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 40px;">#</th>
                                <th rowspan="2" class="align-middle" style="width: 120px;">Concentración / Muestra</th>
                                <th colspan="5" class="table-header-24h">24 Horas</th>
                                <th colspan="5" class="table-header-48h">48 Horas</th>
                                <th rowspan="2" class="align-middle" style="width: 150px;">Observaciones</th>
                            </tr>
                            <tr>
                                <th class="table-header-24h">R1</th>
                                <th class="table-header-24h">R2</th>
                                <th class="table-header-24h">R3</th>
                                <th class="table-header-24h">R4</th>
                                <th class="table-header-24h">∑ Muertos</th>
                                <th class="table-header-48h">R1</th>
                                <th class="table-header-48h">R2</th>
                                <th class="table-header-48h">R3</th>
                                <th class="table-header-48h">R4</th>
                                <th class="table-header-48h">∑ Muertos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 24; $i++)
                                @php
                                    $rowData = $samplesData[$i] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">{{ $i }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="samples_data[{{ $i }}][concentration]" 
                                               value="{{ old('samples_data.'.$i.'.concentration', $rowData['concentration'] ?? '') }}"
                                               placeholder="Conc. {{ $i }}">
                                    </td>
                                    {{-- 24H Réplicas --}}
                                    @for ($r = 1; $r <= 4; $r++)
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable replica-24h-{{ $i }}" 
                                                   name="samples_data[{{ $i }}][h24_r{{ $r }}]" 
                                                   value="{{ old('samples_data.'.$i.'.h24_r'.$r, $rowData['h24_r'.$r] ?? '') }}"
                                                   data-row="{{ $i }}">
                                        </td>
                                    @endfor
                                    <td>
                                        <input type="number" class="form-control form-control-sm navigable sum-field sum-24h-{{ $i }}" 
                                               name="samples_data[{{ $i }}][sum_24h]" 
                                               value="{{ old('samples_data.'.$i.'.sum_24h', $rowData['sum_24h'] ?? '') }}"
                                               readonly>
                                    </td>
                                    {{-- 48H Réplicas --}}
                                    @for ($r = 1; $r <= 4; $r++)
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable replica-48h-{{ $i }}" 
                                                   name="samples_data[{{ $i }}][h48_r{{ $r }}]" 
                                                   value="{{ old('samples_data.'.$i.'.h48_r'.$r, $rowData['h48_r'.$r] ?? '') }}"
                                                   data-row="{{ $i }}">
                                        </td>
                                    @endfor
                                    <td>
                                        <input type="number" class="form-control form-control-sm navigable sum-field sum-48h-{{ $i }}" 
                                               name="samples_data[{{ $i }}][sum_48h]" 
                                               value="{{ old('samples_data.'.$i.'.sum_48h', $rowData['sum_48h'] ?? '') }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="samples_data[{{ $i }}][observations]" 
                                               value="{{ old('samples_data.'.$i.'.observations', $rowData['observations'] ?? '') }}">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= RESULTADOS Y OBSERVACIONES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-bar-chart me-2"></i>Resultados y Observaciones
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CL<sub>50</sub> 24 h</label>
                        <input type="text" class="form-control navigable" 
                               name="cl50_24h" 
                               value="{{ old('cl50_24h', $tisbe_longicornis_water->cl50_24h) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CL<sub>50</sub> 48 h</label>
                        <input type="text" class="form-control navigable" 
                               name="cl50_48h" 
                               value="{{ old('cl50_48h', $tisbe_longicornis_water->cl50_48h) }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones Generales</label>
                    <textarea class="form-control navigable" name="observations" rows="3" 
                              placeholder="Ingrese observaciones adicionales...">{{ old('observations', $tisbe_longicornis_water->observations) }}</textarea>
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
                        Mortalidad del control: <strong>≤ 10%</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Temperatura del ensayo: <strong>20 ± 2 °C</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Salinidad: <strong>30 ± 5 ‰</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Oxígeno disuelto: <strong>≥ 60% saturación</strong>
                    </li>
                </ul>
                <div class="mt-3 pt-3 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">V°B°</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control navigable" 
                                   name="vb" 
                                   value="{{ old('vb', $tisbe_longicornis_water->vb) }}"
                                   placeholder="_____________________">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="bi bi-floppy me-2"></i>Actualizar Bioensayo
            </button>

            @if(auth()->user()->can('validateBioassay', $tisbe_longicornis_water))
                @if($tisbe_longicornis_water->validated_at)
                    <span class="btn btn-outline-success btn-lg px-4 disabled">
                        <i class="bi bi-check-circle me-2"></i>Validado el
                        {{ $tisbe_longicornis_water->validated_at->format('d/m/Y H:i') }}
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
                $sampleEntry = $tisbe_longicornis_water->sampleEntry;
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

    @if(auth()->user()->can('validateBioassay', $tisbe_longicornis_water) && !$tisbe_longicornis_water->validated_at)
        <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo, y la planilla pasará a solo lectura." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('tisbe-longicornis-water.validate', $tisbe_longicornis_water->id) }}" method="POST" style="display:none">
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
       COLORES PERSONALIZADOS - PÚRPURA
       ============================================ */
    .bg-purple-subtle {
        background-color: #e2d9f3 !important;
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
        font-size: 0.8rem;
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
        padding: 2px 4px;
    }

    .modern-table input:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 0.15rem rgba(111, 66, 193, 0.25);
    }

    /* Tabla compacta para muchas filas */
    .compact-table input {
        height: 28px;
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
        color: #6f42c1;
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
        background: linear-gradient(to right, transparent, #6f42c1, transparent);
    }

    /* ============================================
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-24h {
        background: #cfe2ff !important;
        color: #084298;
        font-size: 0.75rem !important;
    }

    .table-header-48h {
        background: #e2d9f3 !important;
        color: #5a32a3;
        font-size: 0.75rem !important;
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
       CARD HEADER COLORES
       ============================================ */
    .bg-secondary-subtle {
        background-color: #e2e3e5 !important;
    }

    /* ============================================
       FLATPICKR
       ============================================ */
    .flatpickr-calendar {
        font-size: 14px !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3) !important;
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
            margin: 5mm; 
        }
        
        body { 
            font-size: 8pt; 
            line-height: 1.1; 
        }
        
        .btn, button, nav, .navbar, .no-print {
            display: none !important;
        }

        .card { 
            border: 1px solid #000 !important; 
            box-shadow: none !important; 
            margin-bottom: 5px !important;
            break-inside: avoid;
        }
        
        .card-header { 
            background: #f0f0f0 !important; 
            color: #000 !important; 
            font-weight: bold; 
            padding: 3px 6px !important;
            font-size: 10pt !important;
        }
        
        .card-body { 
            padding: 4px !important; 
        }

        .modern-table input {
            border: none !important;
            background: transparent !important;
            font-size: 7pt;
            height: 18px !important;
        }

        .modern-table th,
        .modern-table td {
            padding: 1px !important;
            font-size: 7pt;
        }

        .container-fluid {
            padding: 0 !important;
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

    // ============= CÁLCULO AUTOMÁTICO DE SUMAS =============
    for (let row = 1; row <= 24; row++) {
        // 24H
        const inputs24h = document.querySelectorAll(`.replica-24h-${row}`);
        const sum24h = document.querySelector(`.sum-24h-${row}`);
        
        inputs24h.forEach(input => {
            input.addEventListener('input', () => {
                let total = 0;
                inputs24h.forEach(inp => {
                    total += parseFloat(inp.value) || 0;
                });
                sum24h.value = total;
            });
        });

        // 48H
        const inputs48h = document.querySelectorAll(`.replica-48h-${row}`);
        const sum48h = document.querySelector(`.sum-48h-${row}`);
        
        inputs48h.forEach(input => {
            input.addEventListener('input', () => {
                let total = 0;
                inputs48h.forEach(inp => {
                    total += parseFloat(inp.value) || 0;
                });
                sum48h.value = total;
            });
        });
    }

    // ============= SWEETALERT PARA CONFIRMACIÓN =============
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Confirmar actualización?',
            text: "Se guardarán los cambios del bioensayo de Tisbe longicornis (Agua).",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6f42c1',
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