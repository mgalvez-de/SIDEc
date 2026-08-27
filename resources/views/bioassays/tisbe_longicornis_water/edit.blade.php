{{-- resources/views/bioassays/tisbelongicornis_water/edit.blade.php --}}
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
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #6f42c1;">
        Tisbe longicornis (Agua)
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
        $samplesData = $tisbe_longicornis_water->samples_data ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('tisbe-longicornis-water.update', $tisbe_longicornis_water->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- Campo oculto para temporizador --}}
        <input type="hidden" name="timer_start" id="timer_start" 
               value="{{ old('timer_start', $tisbe_longicornis_water->timer_start) }}">

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
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
                <i class="fas fa-flask me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Volumen inóculo inicial</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="initial_inoculum" 
                                   value="{{ old('initial_inoculum', $tisbe_longicornis_water->initial_inoculum) }}">
                            <span class="input-group-text unit-badge">10⁴ cel/ml</span>
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

                {{-- TEMPORIZADOR --}}
                <div class="timer-container my-4 p-3" id="timerContainer">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-purple btn-lg" id="btnStartTimer" onclick="startTimer()">
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
                                <span class="timer-limit">48:00:00</span>
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
                            <small class="text-primary fw-bold">48h (Límite)</small>
                            <small class="text-warning fw-bold">58h (Gracia)</small>
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
                                <div class="marker marker-24h" style="left: 41.38%;" title="24 horas">
                                    <div class="marker-line marker-line-light"></div>
                                    <small class="marker-label">24h</small>
                                </div>
                                <div class="marker marker-limit" style="left: 82.76%;" title="48 horas">
                                    <div class="marker-line"></div>
                                    <small class="marker-label">48h</small>
                                </div>
                                <div class="marker marker-grace" style="left: 100%;" title="58 horas (fin gracia)">
                                    <div class="marker-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="time-remaining mt-2 text-center" id="timeRemaining">
                            <small class="text-muted">Tiempo restante: <strong>48:00:00</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TABLA PRINCIPAL DE MUESTRAS Y LECTURAS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-purple-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-table me-2"></i>Muestras y Lecturas
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
                <i class="fas fa-chart-bar me-2"></i>Resultados y Observaciones
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CL<sub>50</sub> 24h</label>
                        <input type="text" class="form-control navigable" 
                               name="cl50_24h" 
                               value="{{ old('cl50_24h', $tisbe_longicornis_water->cl50_24h) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CL<sub>50</sub> 48h</label>
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
                <i class="fas fa-check-circle me-2"></i>Criterios de Aceptabilidad
            </div>
            <div class="card-body bg-light p-3">
                <ul class="criteria-list mb-3">
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Mortalidad del control: <strong>≤ 10%</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Temperatura del ensayo: <strong>20 ± 2°C</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Salinidad: <strong>30 ± 5 ‰</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
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
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-purple btn-lg px-4">
                <i class="fas fa-save me-2"></i>Actualizar Bioensayo
            </button>
            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            @php
                $sampleEntry = \App\Models\SampleEntry::where('internal_sample_code', $tisbe_longicornis_water->sample)->first();
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
       COLORES PERSONALIZADOS - PÚRPURA
       ============================================ */
    .bg-purple-subtle {
        background-color: #e2d9f3 !important;
    }

    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }

    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
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
       TEMPORIZADOR
       ============================================ */
    .timer-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .timer-container.timer-running {
        border-color: #6f42c1;
        box-shadow: 0 0 15px rgba(111, 66, 193, 0.2);
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
        color: #6f42c1;
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
        color: #6f42c1;
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

    .progress-bar.bg-purple {
        background: linear-gradient(90deg, #6f42c1 0%, #a78bfa 100%) !important;
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
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
    }

    .marker {
        position: absolute;
        top: -8px;
        bottom: -8px;
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
        
        .btn, button, nav, .navbar, .no-print,
        .timer-container { 
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // ============= CONSTANTES DE TIEMPO (48h + 10h gracia) =============
    const LIMIT_HOURS = 48;
    const GRACE_HOURS = 10;
    const TOTAL_HOURS = LIMIT_HOURS + GRACE_HOURS;
    
    const LIMIT_MS = LIMIT_HOURS * 60 * 60 * 1000;
    const TOTAL_MS = TOTAL_HOURS * 60 * 60 * 1000;

    // ============= ESTADO DEL TEMPORIZADOR =============
    let timerState = {
        startTime: null,
        interval: null
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

    // ============= FUNCIONES DE TEMPORIZADOR =============
    window.startTimer = function() {
        const now = Date.now();
        
        timerState.startTime = now;
        document.getElementById('timer_start').value = now;
        
        document.getElementById('btnStartTimer').style.display = 'none';
        document.getElementById('btnResetTimer').style.display = 'inline-block';
        
        updateTimerDisplay();
        timerState.interval = setInterval(updateTimerDisplay, 1000);
        
        Swal.fire({
            icon: 'success',
            title: 'Temporizador Iniciado',
            text: `Tienes ${LIMIT_HOURS} horas para completar el ensayo (+ ${GRACE_HOURS}h de gracia).`,
            timer: 3000,
            showConfirmButton: false
        });
    };

    window.resetTimer = function() {
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
        
        const elapsedFormatted = formatTime(elapsed);
        document.getElementById('timerElapsed').textContent = elapsedFormatted;
        
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `${percentage.toFixed(1)}%`;
        
        const remaining = Math.max(LIMIT_MS - elapsed, 0);
        const remainingFormatted = formatTime(remaining);
        const remainingContainer = document.getElementById('timeRemaining');
        
        const container = document.getElementById('timerContainer');
        const elapsedSpan = document.getElementById('timerElapsed');
        const statusSpan = document.getElementById('timerStatus');
        const timerIcon = container.querySelector('.timer-icon');
        
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        elapsedSpan.classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-purple', 'bg-warning', 'bg-orange', 'bg-danger');
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
            progressBar.classList.add('bg-orange');
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
            progressBar.classList.add('bg-purple');
            statusSpan.innerHTML = '<span class="badge bg-success"><i class="fas fa-play me-1"></i>En Progreso</span>';
            remainingContainer.innerHTML = `<small class="text-muted"><i class="fas fa-clock me-1"></i>Tiempo restante: <strong>${remainingFormatted}</strong></small>`;
        }
    }

    function resetTimerUI() {
        const container = document.getElementById('timerContainer');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const elapsedSpan = document.getElementById('timerElapsed');
        const statusSpan = document.getElementById('timerStatus');
        const remainingContainer = document.getElementById('timeRemaining');
        const timerIcon = container.querySelector('.timer-icon');
        
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        elapsedSpan.classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-purple', 'bg-warning', 'bg-orange', 'bg-danger');
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

    // ============= RESTAURAR TEMPORIZADOR GUARDADO =============
    const savedStart = document.getElementById('timer_start').value;
    if (savedStart && !isNaN(parseInt(savedStart))) {
        timerState.startTime = parseInt(savedStart);
        document.getElementById('btnStartTimer').style.display = 'none';
        document.getElementById('btnResetTimer').style.display = 'inline-block';
        updateTimerDisplay();
        timerState.interval = setInterval(updateTimerDisplay, 1000);
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