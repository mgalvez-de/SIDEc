{{-- resources/views/bioassays/daphnia_magna/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Logo -->
    <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        Análisis Bioensayo Agudo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #198754;">
        Daphnia magna
    </h2>
    <p class="text-center text-muted mb-4">RT-01.05 | Versión: 03 | Vigencia: 01.09.2023</p>

    <form id="bioassayForm" action="{{ route('daphnia-magna.store') }}" method="POST" novalidate>
        @csrf

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
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
                                <td><input type="text" class="form-control form-control-sm navigable" name="sample" required></td>
                                <td><input type="text" class="form-control form-control-sm navigable" name="matrix"></td>
                                <td><input type="text" class="form-control form-control-sm navigable datetimepicker" name="start_time" placeholder="Seleccione fecha y hora"></td>
                                <td><input type="text" class="form-control form-control-sm navigable datetimepicker" name="end_time" placeholder="Seleccione fecha y hora"></td>
                                <td><input type="text" class="form-control form-control-sm navigable" name="analyst"></td>
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
                <i class="fas fa-flask me-2"></i>Ensayo Preliminar
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
                                        <input type="number" step="0.01" class="form-control navigable" name="sample_temperature">
                                        <span class="input-group-text unit-badge">°C</span>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control form-control-sm navigable datepicker" name="reconstituted_water_date" placeholder="Seleccione fecha"></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm navigable" name="sample_ph"></td>
                            </tr>
                        </tbody>
                    </table>
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
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm navigable" name="pre_concentration_row{{ $i }}" placeholder="Conc. {{ $i }}"></td>
                                    <td><input type="number" min="0" class="form-control form-control-sm navigable" name="pre_24h_rep1_row{{ $i }}"></td>
                                    <td><input type="number" min="0" class="form-control form-control-sm navigable" name="pre_24h_rep2_row{{ $i }}"></td>
                                    <td><input type="number" min="0" class="form-control form-control-sm navigable sum-field" name="pre_24h_sum_row{{ $i }}" readonly></td>
                                    <td><input type="number" min="0" class="form-control form-control-sm navigable" name="pre_48h_rep1_row{{ $i }}"></td>
                                    <td><input type="number" min="0" class="form-control form-control-sm navigable" name="pre_48h_rep2_row{{ $i }}"></td>
                                    <td><input type="number" min="0" class="form-control form-control-sm navigable sum-field" name="pre_48h_sum_row{{ $i }}" readonly></td>
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
                                <th>Fecha de agua reconstituida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm navigable datetimepicker" name="def_start_time" placeholder="Seleccione fecha y hora"></td>
                                <td><input type="text" class="form-control form-control-sm navigable datetimepicker" name="def_end_time" placeholder="Seleccione fecha y hora"></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" class="form-control navigable" name="def_temperature">
                                        <span class="input-group-text unit-badge">°C</span>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control form-control-sm navigable datepicker" name="def_reconstituted_water_date" placeholder="Seleccione fecha"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @php
                    $hours = ['24', '48'];
                    $replicas = 4;
                    $concentrations = 5;
                @endphp

                @foreach ($hours as $hour)
                    <h4 class="mt-4 mb-3 text-center section-title">
                        <span>{{ $hour }} Horas</span>
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
                                                name="def_{{ $hour }}h_conc{{ $c }}_value" 
                                                placeholder="Conc. {{ $c }}">
                                        </td>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= $replicas; $i++)
                                    <tr>
                                        <td class="fw-semibold table-light">Réplica {{ $i }}</td>
                                        <td><input type="number" min="0" class="form-control form-control-sm navigable" name="def_{{ $hour }}h_control_rep{{ $i }}"></td>
                                        @for ($c = 1; $c <= $concentrations; $c++)
                                            <td><input type="number" min="0" class="form-control form-control-sm navigable" name="def_{{ $hour }}h_conc{{ $c }}_rep{{ $i }}"></td>
                                        @endfor
                                    </tr>
                                @endfor
                                <tr class="table-warning">
                                    <td class="fw-bold">∑ D. magna muertas</td>
                                    <td><input type="number" class="form-control form-control-sm navigable sum-field" name="def_{{ $hour }}h_control_sum" readonly></td>
                                    @for ($c = 1; $c <= $concentrations; $c++)
                                        <td><input type="number" class="form-control form-control-sm navigable sum-field" name="def_{{ $hour }}h_conc{{ $c }}_sum" readonly></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ================= RESULTADOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-chart-bar me-2"></i>Resultados de Análisis
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Inmovilidad del control</label>
                        <div class="input-group">
                            <input type="text" class="form-control navigable" name="control_immobility">
                            <span class="input-group-text unit-badge">%</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CL<sub>50</sub> 24h</label>
                        <input type="text" class="form-control navigable" name="cl50_24h">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CL<sub>50</sub> 48h</label>
                        <input type="text" class="form-control navigable" name="cl50_48h">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control navigable" name="observations" id="observations" rows="3" placeholder="Ingrese observaciones adicionales..."></textarea>
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
                        Rango de Aceptabilidad tóxico de referencia CrL<sub>50</sub> (24-48h): <strong>0,60 mg/l a 1,70 mg/l</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Rango de Aceptabilidad Control Inmovilidad: <strong>≤ 10%</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        pH de las muestras: <strong>6 a 9 unidades</strong>
                    </li>
                </ul>
                <div class="mt-3 pt-3 border-top">
                    <p class="mb-0 text-muted">V°B° _____________________</p>
                </div>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="fas fa-save me-2"></i>Guardar Bioensayo
            </button>
            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            <a href="{{ route('daphnia-magna.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
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
       CARD HEADER VERDE SUAVE
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
        
        .btn, button, nav, .navbar, .no-print { 
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
            title: '¿Confirmar guardado?',
            text: "Se registrará el nuevo bioensayo de Daphnia magna.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save me-1"></i> Sí, guardar',
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