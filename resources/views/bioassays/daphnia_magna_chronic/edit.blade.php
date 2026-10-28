{{-- resources/views/bioassays/daphnia_magna_chronic/edit.blade.php --}}
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
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #198754;">
        Daphnia magna - Crónico (21 días)
    </h2>
    <p class="text-center text-muted mb-4">RT-02.01 | Versión: 03 | Vigencia: 01.10.2025</p>

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
        $maintenanceData = $daphnia_magna_chronic->maintenance_data ?? [];
        $controlData = $daphnia_magna_chronic->control_data ?? [];
        $concentrations = $daphnia_magna_chronic->concentrations_data ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('daphnia-magna-chronic.update', $daphnia_magna_chronic->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-card-checklist me-2"></i>Datos Generales
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-eyedropper text-success me-1"></i>Muestra
                        </label>
                        <input type="text" class="form-control navigable bg-light" 
                               name="sample" 
                               value="{{ old('sample', $daphnia_magna_chronic->sample) }}" 
                               readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-water text-primary me-1"></i>Matriz
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="matrix" 
                               value="{{ old('matrix', $daphnia_magna_chronic->matrix) }}">
                    </div>
                </div>

                <div class="row g-3 align-items-end mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar3 text-success me-1"></i>Fecha y hora inicio Bioensayo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="bioassay_start" 
                               value="{{ old('bioassay_start', $daphnia_magna_chronic->bioassay_start) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-check text-danger me-1"></i>Fecha y hora término Bioensayo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="bioassay_end" 
                               value="{{ old('bioassay_end', $daphnia_magna_chronic->bioassay_end) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-person text-secondary me-1"></i>Analista
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="analyst" 
                               value="{{ old('analyst', $daphnia_magna_chronic->analyst) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= DATOS PRELIMINARES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-info-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-thermometer-half me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-thermometer-low text-info me-1"></i>Temperatura de la muestra
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.1" class="form-control navigable" 
                                   name="sample_temperature" 
                                   value="{{ old('sample_temperature', $daphnia_magna_chronic->sample_temperature) }}">
                            <span class="input-group-text unit-badge">°C</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-eyedropper text-warning me-1"></i>pH
                        </label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph" 
                               value="{{ old('ph', $daphnia_magna_chronic->ph) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-clock me-2"></i>Temporizador del Ensayo (21 días)
            </div>
            <div class="card-body bg-light p-3">
                @include('bioassays._duration', ['check' => $daphnia_magna_chronic->durationChecks()['bioassay'], 'start' => 'bioassay_start', 'end' => 'bioassay_end'])
            </div>
        </div>

        {{-- ================= MANTENCIÓN DE ESPECIE ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-warning-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-calendar-week me-2"></i>Mantención de Especie durante los 21 días
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table table-sm">
                        <thead>
                            <tr>
                                <th class="table-header-day" style="width: 80px;">Día de Cambio</th>
                                <th class="table-header-water">Fecha Agua Reconstituida</th>
                                <th class="table-header-food">Fecha Alimento</th>
                                <th class="table-header-amount">Cantidad Microalga (mL)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($day = 0; $day <= 20; $day += 3)
                                <tr>
                                    <td class="fw-semibold table-light">{{ $day }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datepicker" 
                                               name="maintenance[{{ $day }}][water_date]" 
                                               value="{{ old('maintenance.'.$day.'.water_date', $maintenanceData[$day]['water_date'] ?? '') }}"
                                               placeholder="DD/MM/YYYY">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable datepicker" 
                                               name="maintenance[{{ $day }}][food_date]" 
                                               value="{{ old('maintenance.'.$day.'.food_date', $maintenanceData[$day]['food_date'] ?? '') }}"
                                               placeholder="DD/MM/YYYY">
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" class="form-control form-control-sm navigable" 
                                               name="maintenance[{{ $day }}][microalgae_ml]" 
                                               value="{{ old('maintenance.'.$day.'.microalgae_ml', $maintenanceData[$day]['microalgae_ml'] ?? '') }}">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= TABLA CONTROL ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-primary-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-check2-all me-2"></i>Réplicas CONTROL (10 réplicas x 21 días)
            </div>
            <div class="card-body bg-light p-2">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table table-sm">
                        <thead>
                            <tr>
                                <th class="table-header-day" style="width: 60px;">Día</th>
                                @for ($r = 1; $r <= 10; $r++)
                                    <th class="table-header-replica">N°{{ $r }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for ($day = 1; $day <= 21; $day++)
                                <tr>
                                    <td class="fw-semibold table-light">{{ $day }}</td>
                                    @for ($r = 1; $r <= 10; $r++)
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm navigable control-input" 
                                                   name="control[{{ $day }}][r{{ $r }}]" 
                                                   value="{{ old('control.'.$day.'.r'.$r, $controlData[$day]['r'.$r] ?? '') }}"
                                                   data-day="{{ $day }}" data-replica="{{ $r }}"
                                                   style="width: 50px; padding: 2px;">
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                            {{-- Fila de suma --}}
                            <tr class="table-success">
                                <td class="fw-bold">∑</td>
                                @for ($r = 1; $r <= 10; $r++)
                                    <td>
                                        <input type="number" class="form-control form-control-sm bg-success-subtle fw-bold" 
                                               name="control[sum][r{{ $r }}]" 
                                               value="{{ old('control.sum.r'.$r, $controlData['sum']['r'.$r] ?? '') }}"
                                               id="controlSum{{ $r }}" readonly
                                               style="width: 50px; padding: 2px;">
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Total reproducción Control:</label>
                        <input type="number" class="form-control bg-success-subtle fw-bold" 
                               name="control_total_reproduction" 
                               value="{{ old('control_total_reproduction', $daphnia_magna_chronic->control_total_reproduction) }}"
                               id="controlTotalReproduction" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TABLAS DE CONCENTRACIONES ================= --}}
        @for ($conc = 1; $conc <= 5; $conc++)
            @php
                $concData = $concentrations[$conc] ?? [];
                $colors = ['danger', 'warning', 'info', 'secondary', 'dark'];
                $bgColors = ['danger-subtle', 'warning-subtle', 'info-subtle', 'secondary-subtle', 'dark-subtle'];
            @endphp
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-{{ $bgColors[$conc-1] }} text-dark d-flex justify-content-between align-items-center"
                    style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <span><i class="bi bi-eyedropper me-2"></i>Concentración {{ $conc }}</span>
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" 
                           name="concentrations[{{ $conc }}][value]" 
                           value="{{ old('concentrations.'.$conc.'.value', $concData['value'] ?? '') }}"
                           placeholder="Ej: 100%, 50%, 25%...">
                </div>
                <div class="card-body bg-light p-2">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0 modern-table table-sm">
                            <thead>
                                <tr>
                                    <th class="table-header-day" style="width: 60px;">Día</th>
                                    @for ($r = 1; $r <= 10; $r++)
                                        <th class="table-header-replica-{{ $conc }}">N°{{ $r }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for ($day = 1; $day <= 21; $day++)
                                    <tr>
                                        <td class="fw-semibold table-light">{{ $day }}</td>
                                        @for ($r = 1; $r <= 10; $r++)
                                            <td>
                                                <input type="number" min="0" class="form-control form-control-sm navigable conc-input" 
                                                       name="concentrations[{{ $conc }}][days][{{ $day }}][r{{ $r }}]" 
                                                       value="{{ old('concentrations.'.$conc.'.days.'.$day.'.r'.$r, $concData['days'][$day]['r'.$r] ?? '') }}"
                                                       data-conc="{{ $conc }}" data-day="{{ $day }}" data-replica="{{ $r }}"
                                                       style="width: 50px; padding: 2px;">
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                                {{-- Fila de suma --}}
                                <tr class="table-{{ $colors[$conc-1] }}">
                                    <td class="fw-bold">∑</td>
                                    @for ($r = 1; $r <= 10; $r++)
                                        <td>
                                            <input type="number" class="form-control form-control-sm bg-{{ $bgColors[$conc-1] }} fw-bold" 
                                                   name="concentrations[{{ $conc }}][sum][r{{ $r }}]" 
                                                   value="{{ old('concentrations.'.$conc.'.sum.r'.$r, $concData['sum']['r'.$r] ?? '') }}"
                                                   id="conc{{ $conc }}Sum{{ $r }}" readonly
                                                   style="width: 50px; padding: 2px;">
                                        </td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Total reproducción Conc. {{ $conc }}:</label>
                            <input type="number" class="form-control form-control-sm bg-{{ $bgColors[$conc-1] }} fw-bold" 
                                   name="concentrations[{{ $conc }}][total_reproduction]" 
                                   value="{{ old('concentrations.'.$conc.'.total_reproduction', $concData['total_reproduction'] ?? '') }}"
                                   id="conc{{ $conc }}TotalReproduction" readonly>
                        </div>
                    </div>
                </div>
            </div>
        @endfor

        {{-- ================= RESULTADOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-bar-chart me-2"></i>Resultados
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-arrow-down text-success me-1"></i>NOEC (Máx. conc. sin efecto)
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="noec" 
                               value="{{ old('noec', $daphnia_magna_chronic->noec) }}"
                               placeholder="Máxima concentración sin efecto observable">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-arrow-up text-danger me-1"></i>LOEC (Mín. conc. con efecto)
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="loec" 
                               value="{{ old('loec', $daphnia_magna_chronic->loec) }}"
                               placeholder="Mínima concentración con efecto observable">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea class="form-control navigable" name="observations" rows="3" 
                                  placeholder="Ingrese observaciones adicionales...">{{ old('observations', $daphnia_magna_chronic->observations) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= CRITERIOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-secondary-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-check-circle me-2"></i>Criterios de Aceptabilidad del Análisis
            </div>
            <div class="card-body bg-light p-3">
                <ul class="criteria-list mb-3">
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Mortalidad accidental por concentración: <strong>&lt; 20%</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Control: <strong>≥ 40 juveniles en 21 días</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Sobrevivencia de adultos en 21 días: <strong>≥ 80%</strong>
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

            @if(auth()->user()->can('validateBioassay', $daphnia_magna_chronic))
                @if($daphnia_magna_chronic->validated_at)
                    <span class="btn btn-outline-success btn-lg px-4 disabled">
                        <i class="bi bi-check-circle me-2"></i>Validado el
                        {{ $daphnia_magna_chronic->validated_at->format('d/m/Y H:i') }}
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
                $sampleEntry = $daphnia_magna_chronic->sampleEntry;
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

    @if(auth()->user()->can('validateBioassay', $daphnia_magna_chronic) && !$daphnia_magna_chronic->validated_at)
        <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo. Esta acción no se puede deshacer." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('daphnia-magna-chronic.validate', $daphnia_magna_chronic->id) }}" method="POST" style="display:none">
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
       COLORES PERSONALIZADOS
       ============================================ */
    .bg-success-subtle { background-color: #d1e7dd !important; }
    .bg-info-subtle { background-color: #cff4fc !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; }
    .bg-primary-subtle { background-color: #cfe2ff !important; }
    .bg-secondary-subtle { background-color: #e2e3e5 !important; }
    .bg-dark-subtle { background-color: #d3d3d4 !important; }

    /* ============================================
       ESTILOS DE TABLAS
       ============================================ */
    .modern-table { border-collapse: separate; border-spacing: 0; border-radius: 8px; overflow: hidden; background: white; }
    .modern-table thead th { background: #e9ecef; color: #495057; font-weight: 600; font-size: 0.7rem; padding: 4px 2px; border: 1px solid #dee2e6; }
    .modern-table tbody td { padding: 2px; border: 1px solid #dee2e6; }
    .modern-table tbody tr:hover { background-color: #f8f9fa; }
    .modern-table input { border: 1px solid #ced4da; text-align: center; border-radius: 3px; font-size: 0.75rem; }
    .modern-table input:focus { border-color: #198754; box-shadow: 0 0 0 0.1rem rgba(25, 135, 84, 0.25); }
    .modern-table { width: auto; min-width: 100%; }
    .modern-table input { min-width: 55px; }
    .modern-table input[step], .modern-table input[readonly] { min-width: 80px; }

    /* Headers de tabla */
    .table-header-day { background: #6c757d !important; color: white !important; }
    .table-header-replica { background: #cfe2ff !important; color: #084298; }
    .table-header-replica-1 { background: #f8d7da !important; color: #842029; }
    .table-header-replica-2 { background: #fff3cd !important; color: #664d03; }
    .table-header-replica-3 { background: #cff4fc !important; color: #055160; }
    .table-header-replica-4 { background: #e2e3e5 !important; color: #41464b; }
    .table-header-replica-5 { background: #d3d3d4 !important; color: #1a1a1a; }
    .table-header-water { background: #cff4fc !important; color: #055160; }
    .table-header-food { background: #fff3cd !important; color: #664d03; }
    .table-header-amount { background: #d1e7dd !important; color: #0f5132; }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge { background: #6c757d; color: white; font-weight: 500; font-size: 0.9rem; min-width: 45px; justify-content: center; }

    /* ============================================
       NAVEGACIÓN
       ============================================ */
    .navigable:focus { outline: 2px solid #198754; outline-offset: 1px; }

    /* ============================================
       LISTA DE CRITERIOS
       ============================================ */
    .criteria-list { list-style: none; padding-left: 0; }
    .criteria-list li { padding: 8px 0; font-size: 1rem; border-bottom: 1px solid #e9ecef; }
    .criteria-list li:last-child { border-bottom: none; }

    /* ============================================
       TEMPORIZADOR
       ============================================ */
    @keyframes pulse-grace { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.005); } }
    @keyframes pulse-expired { 0%, 100% { opacity: 1; } 50% { opacity: 0.8; } }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* ============================================
       BARRA DE PROGRESO
       ============================================ */
    .progress-wrapper { position: relative; }
    .progress-bar { transition: width 1s linear, background-color 0.5s ease; }
    .progress-bar.bg-success-gradient { background: linear-gradient(90deg, #198754 0%, #20c997 100%) !important; }
    .progress-bar.bg-warning { background: linear-gradient(90deg, #ffc107 0%, #ffda6a 100%) !important; }
    .progress-bar.bg-orange { background: linear-gradient(90deg, #fd7e14 0%, #ffb380 100%) !important; }
    .progress-bar.bg-danger { background: linear-gradient(90deg, #dc3545 0%, #f17983 100%) !important; }
    .progress-text { font-size: 0.8rem; font-weight: 600; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); }
    .progress-markers { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; }
    .marker { position: absolute; top: -8px; bottom: -8px; width: 2px; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; }
    .marker-line { width: 2px; height: 100%; border-radius: 2px; background: #0d6efd; box-shadow: 0 0 5px rgba(13, 110, 253, 0.5); }
    .marker-line-light { background: #adb5bd; box-shadow: none; }
    .marker-label { position: absolute; bottom: -20px; font-size: 0.65rem; color: #6c757d; }

    /* ============================================
       IMPRESIÓN
       ============================================ */
    @media print {
        @page { size: A4 landscape; margin: 3mm; }
        body { font-size: 6pt; line-height: 1; }
        .btn, button, nav, .navbar, .no-print { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; margin-bottom: 3px !important; break-inside: avoid; }
        .card-header { background: #f0f0f0 !important; color: #000 !important; padding: 2px 4px !important; font-size: 8pt !important; }
        .card-body { padding: 2px !important; }
        .modern-table input { border: none !important; background: transparent !important; font-size: 6pt; width: 30px !important; }
        .modern-table th, .modern-table td { padding: 1px !important; font-size: 6pt; }
        .container-fluid { padding: 0 !important; }
        h1, h2 { font-size: 10pt !important; margin: 2px 0 !important; }
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
    flatpickr(".datepicker", { dateFormat: "Y-m-d", locale: "es", allowInput: true });

    // Cálculo automático de sumas - Control
    document.querySelectorAll('.control-input').forEach(input => {
        input.addEventListener('input', calculateControlSums);
    });

    function calculateControlSums() {
        for (let r = 1; r <= 10; r++) {
            let sum = 0;
            for (let day = 1; day <= 21; day++) {
                const val = parseFloat(document.querySelector(`input[name="control[${day}][r${r}]"]`)?.value) || 0;
                sum += val;
            }
            const sumField = document.getElementById(`controlSum${r}`);
            if (sumField) sumField.value = sum;
        }
        // Total
        let total = 0;
        for (let r = 1; r <= 10; r++) {
            total += parseFloat(document.getElementById(`controlSum${r}`)?.value) || 0;
        }
        const totalField = document.getElementById('controlTotalReproduction');
        if (totalField) totalField.value = total;
    }

    // Cálculo automático de sumas - Concentraciones
    document.querySelectorAll('.conc-input').forEach(input => {
        input.addEventListener('input', function() {
            const conc = this.dataset.conc;
            calculateConcSums(conc);
        });
    });

    function calculateConcSums(conc) {
        for (let r = 1; r <= 10; r++) {
            let sum = 0;
            for (let day = 1; day <= 21; day++) {
                const val = parseFloat(document.querySelector(`input[name="concentrations[${conc}][days][${day}][r${r}]"]`)?.value) || 0;
                sum += val;
            }
            const sumField = document.getElementById(`conc${conc}Sum${r}`);
            if (sumField) sumField.value = sum;
        }
        // Total
        let total = 0;
        for (let r = 1; r <= 10; r++) {
            total += parseFloat(document.getElementById(`conc${conc}Sum${r}`)?.value) || 0;
        }
        const totalField = document.getElementById(`conc${conc}TotalReproduction`);
        if (totalField) totalField.value = total;
    }

    // Inicializar cálculos
    calculateControlSums();
    for (let c = 1; c <= 5; c++) calculateConcSums(c);

    // Timer functions


    // Confirmación de envío
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar actualización?', text: "Se guardarán los cambios del bioensayo Daphnia magna Crónico.",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#198754', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-floppy me-1"></i> Sí, actualizar', cancelButtonText: '<i class="bi bi-x-lg me-1"></i> Cancelar'
        }).then((result) => { if (result.isConfirmed) this.submit(); });
    });
});
</script>
@endpush