{{-- resources/views/bioassays/daphnia_magna_chronic/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Logo -->
    <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        Editar Bioensayo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #198754;">
        Daphnia magna - Crónico (21 días)
    </h2>
    <p class="text-center text-muted mb-4">RT-02.01 | Versión: 03 | Vigencia: 01.10.2025</p>

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
        $maintenanceData = $daphnia_magna_chronic->maintenance_data ?? [];
        $controlData = $daphnia_magna_chronic->control_data ?? [];
        $concentrations = $daphnia_magna_chronic->concentrations_data ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('daphnia-magna-chronic.update', $daphnia_magna_chronic->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- Campo oculto para temporizador --}}
        <input type="hidden" name="timer_start" id="timer_start" 
               value="{{ old('timer_start', $daphnia_magna_chronic->timer_start) }}">

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-clipboard-list me-2"></i>Datos Generales
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-vial text-success me-1"></i>Muestra
                        </label>
                        <input type="text" class="form-control navigable bg-light" 
                               name="sample" 
                               value="{{ old('sample', $daphnia_magna_chronic->sample) }}" 
                               readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-water text-primary me-1"></i>Matriz
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="matrix" 
                               value="{{ old('matrix', $daphnia_magna_chronic->matrix) }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-alt text-success me-1"></i>Fecha y hora inicio Bioensayo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="bioassay_start" 
                               value="{{ old('bioassay_start', $daphnia_magna_chronic->bioassay_start) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-check text-danger me-1"></i>Fecha y hora término Bioensayo
                        </label>
                        <input type="text" class="form-control navigable datetimepicker" 
                               name="bioassay_end" 
                               value="{{ old('bioassay_end', $daphnia_magna_chronic->bioassay_end) }}" 
                               placeholder="Seleccione fecha y hora">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-user text-secondary me-1"></i>Analista
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
                <i class="fas fa-thermometer-half me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-temperature-low text-info me-1"></i>Temperatura de la muestra
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
                            <i class="fas fa-flask text-warning me-1"></i>pH
                        </label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph" 
                               value="{{ old('ph', $daphnia_magna_chronic->ph) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TEMPORIZADOR ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-clock me-2"></i>Temporizador del Ensayo (21 días)
            </div>
            <div class="card-body bg-light p-3">
                <div class="timer-container p-3" id="timerContainer">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-success btn-lg" id="btnStartTimer" onclick="startTimer()">
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
                                <span class="timer-elapsed" id="timerElapsed">00d 00:00:00</span>
                                <span class="timer-separator">/</span>
                                <span class="timer-limit">21d 00:00:00</span>
                            </div>
                            <div class="timer-status" id="timerStatus">
                                <span class="badge bg-secondary">Sin iniciar</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barra de progreso --}}
                    <div class="progress-container mt-3">
                        <div class="progress-labels d-flex justify-content-between mb-1">
                            <small class="text-muted">Día 0</small>
                            <small class="text-primary fw-bold">Día 21 (Límite)</small>
                            <small class="text-warning fw-bold">Día 22 (Gracia)</small>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress" style="height: 25px; border-radius: 12px; background: #e9ecef;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="progressBar" 
                                     role="progressbar" 
                                     style="width: 0%; border-radius: 12px;"
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="progress-text" id="progressText">Día 0</span>
                                </div>
                            </div>
                            {{-- Marcadores de tiempo --}}
                            <div class="progress-markers">
                                <div class="marker" style="left: 33.33%;" title="Día 7">
                                    <div class="marker-line marker-line-light"></div>
                                    <small class="marker-label">D7</small>
                                </div>
                                <div class="marker" style="left: 66.67%;" title="Día 14">
                                    <div class="marker-line marker-line-light"></div>
                                    <small class="marker-label">D14</small>
                                </div>
                                <div class="marker marker-limit" style="left: 95.45%;" title="Día 21 (límite)">
                                    <div class="marker-line"></div>
                                    <small class="marker-label">D21</small>
                                </div>
                            </div>
                        </div>
                        <div class="time-remaining mt-2 text-center" id="timeRemaining">
                            <small class="text-muted">Tiempo restante: <strong>21 días 00:00:00</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MANTENCIÓN DE ESPECIE ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-warning-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="fas fa-calendar-week me-2"></i>Mantención de Especie durante los 21 días
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table table-sm">
                        <thead>
                            <tr>
                                <th class="table-header-day" style="width: 80px;">Día de Cambio</th>
                                <th class="table-header-water">Fecha Agua Reconstituida</th>
                                <th class="table-header-food">Fecha Alimento</th>
                                <th class="table-header-amount">Cantidad Microalga (ml)</th>
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
                <i class="fas fa-check-double me-2"></i>Réplicas CONTROL (10 réplicas x 21 días)
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
                    <span><i class="fas fa-flask me-2"></i>Concentración {{ $conc }}</span>
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
                <i class="fas fa-chart-bar me-2"></i>Resultados
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-arrow-down text-success me-1"></i>NOEC (Máx. conc. sin efecto)
                        </label>
                        <input type="text" class="form-control navigable" 
                               name="noec" 
                               value="{{ old('noec', $daphnia_magna_chronic->noec) }}"
                               placeholder="Máxima concentración sin efecto observable">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-arrow-up text-danger me-1"></i>LOEC (Mín. conc. con efecto)
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
                <i class="fas fa-check-circle me-2"></i>Criterios de Aceptabilidad del Análisis
            </div>
            <div class="card-body bg-light p-3">
                <ul class="criteria-list mb-3">
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Mortalidad accidental por concentración: <strong>&lt; 20%</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Control: <strong>≥ 40 juveniles en 21 días</strong>
                    </li>
                    <li>
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        Sobrevivencia de adultos en 21 días: <strong>≥ 80%</strong>
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
                <i class="fas fa-save me-2"></i>Actualizar Bioensayo
            </button>
            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            @php
                $sampleEntry = \App\Models\SampleEntry::where('internal_sample_code', $daphnia_magna_chronic->sample)->first();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    .timer-container { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border: 2px solid #dee2e6; transition: all 0.3s ease; }
    .timer-container.timer-running { border-color: #198754; box-shadow: 0 0 15px rgba(25, 135, 84, 0.2); }
    .timer-container.timer-warning { border-color: #ffc107; background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%); }
    .timer-container.timer-grace { border-color: #fd7e14; background: linear-gradient(135deg, #fff5e6 0%, #ffe5cc 100%); animation: pulse-grace 2s infinite; }
    .timer-container.timer-expired { border-color: #dc3545; background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%); animation: pulse-expired 1s infinite; }
    @keyframes pulse-grace { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.005); } }
    @keyframes pulse-expired { 0%, 100% { opacity: 1; } 50% { opacity: 0.8; } }
    .timer-display { display: flex; align-items: center; gap: 15px; background: white; padding: 12px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .timer-icon { font-size: 1.8rem; color: #6c757d; }
    .timer-icon.running { color: #198754; animation: spin 2s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .timer-values { font-family: 'Courier New', monospace; font-size: 1.3rem; font-weight: bold; }
    .timer-elapsed { color: #198754; }
    .timer-elapsed.warning { color: #ffc107; }
    .timer-elapsed.grace { color: #fd7e14; }
    .timer-elapsed.expired { color: #dc3545; }
    .timer-separator { color: #6c757d; margin: 0 5px; }
    .timer-limit { color: #6c757d; }

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
        .btn, button, nav, .navbar, .no-print, .timer-container { display: none !important; }
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Temporizador: 21 días + 1 día de gracia = 22 días total
    const LIMIT_DAYS = 21;
    const GRACE_DAYS = 1;
    const TOTAL_DAYS = LIMIT_DAYS + GRACE_DAYS;
    const LIMIT_MS = LIMIT_DAYS * 24 * 60 * 60 * 1000;
    const TOTAL_MS = TOTAL_DAYS * 24 * 60 * 60 * 1000;

    let timerState = { startTime: null, interval: null };

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
    window.startTimer = function() {
        const now = Date.now();
        timerState.startTime = now;
        document.getElementById('timer_start').value = now;
        document.getElementById('btnStartTimer').style.display = 'none';
        document.getElementById('btnResetTimer').style.display = 'inline-block';
        updateTimerDisplay();
        timerState.interval = setInterval(updateTimerDisplay, 1000);
        Swal.fire({ icon: 'success', title: 'Temporizador Iniciado', text: `Ensayo de ${LIMIT_DAYS} días iniciado (+${GRACE_DAYS} día de gracia).`, timer: 3000, showConfirmButton: false });
    };

    window.resetTimer = function() {
        Swal.fire({
            title: '¿Reiniciar temporizador?', text: "Esta acción reiniciará el contador.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, reiniciar', cancelButtonText: 'Cancelar'
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
        const elapsedDays = Math.floor(elapsed / (24 * 60 * 60 * 1000));
        document.getElementById('timerElapsed').textContent = formatTimeDays(elapsed);
        document.getElementById('progressBar').style.width = `${percentage}%`;
        document.getElementById('progressText').textContent = `Día ${elapsedDays}`;
        const remaining = Math.max(LIMIT_MS - elapsed, 0);
        const container = document.getElementById('timerContainer');
        const elapsedSpan = document.getElementById('timerElapsed');
        const statusSpan = document.getElementById('timerStatus');
        const remainingContainer = document.getElementById('timeRemaining');
        const progressBar = document.getElementById('progressBar');
        container.classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        elapsedSpan.classList.remove('warning', 'grace', 'expired');
        progressBar.classList.remove('bg-success-gradient', 'bg-warning', 'bg-orange', 'bg-danger');
        container.querySelector('.timer-icon').classList.add('running');

        if (elapsed >= TOTAL_MS) {
            container.classList.add('timer-expired'); elapsedSpan.classList.add('expired'); progressBar.classList.add('bg-danger');
            statusSpan.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>¡Tiempo Expirado!</span>';
            remainingContainer.innerHTML = '<small class="text-danger fw-bold">Tiempo de gracia agotado</small>';
        } else if (elapsed >= LIMIT_MS) {
            container.classList.add('timer-grace'); elapsedSpan.classList.add('grace'); progressBar.classList.add('bg-orange');
            statusSpan.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Período de Gracia</span>';
            remainingContainer.innerHTML = `<small class="text-warning fw-bold">Gracia: ${formatTimeDays(TOTAL_MS - elapsed)}</small>`;
        } else if (elapsed >= LIMIT_MS * 0.9) {
            container.classList.add('timer-warning'); elapsedSpan.classList.add('warning'); progressBar.classList.add('bg-warning');
            statusSpan.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation me-1"></i>¡Últimos días!</span>';
            remainingContainer.innerHTML = `<small class="text-warning">Restante: ${formatTimeDays(remaining)}</small>`;
        } else {
            container.classList.add('timer-running'); progressBar.classList.add('bg-success-gradient');
            statusSpan.innerHTML = '<span class="badge bg-success"><i class="fas fa-play me-1"></i>En Progreso</span>';
            remainingContainer.innerHTML = `<small class="text-muted">Restante: ${formatTimeDays(remaining)}</small>`;
        }
    }

    function resetTimerUI() {
        document.getElementById('timerElapsed').textContent = '00d 00:00:00';
        document.getElementById('progressBar').style.width = '0%';
        document.getElementById('progressText').textContent = 'Día 0';
        document.getElementById('timerStatus').innerHTML = '<span class="badge bg-secondary">Sin iniciar</span>';
        document.getElementById('timeRemaining').innerHTML = '<small class="text-muted">Tiempo restante: <strong>21 días 00:00:00</strong></small>';
        document.getElementById('timerContainer').classList.remove('timer-running', 'timer-warning', 'timer-grace', 'timer-expired');
        document.getElementById('timerContainer').querySelector('.timer-icon').classList.remove('running');
    }

    function formatTimeDays(ms) {
        const totalSeconds = Math.floor(ms / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return `${String(days).padStart(2, '0')}d ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    // Restaurar timer
    const savedStart = document.getElementById('timer_start').value;
    if (savedStart && !isNaN(parseInt(savedStart))) {
        timerState.startTime = parseInt(savedStart);
        document.getElementById('btnStartTimer').style.display = 'none';
        document.getElementById('btnResetTimer').style.display = 'inline-block';
        updateTimerDisplay();
        timerState.interval = setInterval(updateTimerDisplay, 1000);
    }

    // Confirmación de envío
    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar actualización?', text: "Se guardarán los cambios del bioensayo Daphnia magna Crónico.",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#198754', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save me-1"></i> Sí, actualizar', cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar'
        }).then((result) => { if (result.isConfirmed) this.submit(); });
    });
});
</script>
@endpush