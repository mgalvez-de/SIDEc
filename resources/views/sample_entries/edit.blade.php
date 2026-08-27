@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc" style="height: 80px; display: block; margin: 0 auto 20px auto;">

        <!-- Título -->
        <h1 class="mb-4 text-secondary text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
            Editar Ingreso de Muestra
        </h1>

        <form id="entryForm" action="{{ route('sample_entries.update', $sampleEntry->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            
            {{-- Mostrar errores de validación si existen --}}
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

            {{-- ===================== INGRESO ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-success text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    Información de Ingreso
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha y Hora de Recepción</label>
                            <input type="text" name="received_at" class="form-control datetimepicker navigable"
                                value="{{ old('received_at', $sampleEntry->received_at) }}" placeholder="Seleccionar fecha y hora">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="internal_sample_code" class="form-label fw-bold">Código Interno</label>
                            <input type="text" name="internal_sample_code" id="internal_sample_code"
                                class="form-control navigable bg-white" 
                                value="{{ $sampleEntry->internal_sample_code }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Muestra (Matriz)</label>
                            @php
                                $matrixLabels = [
                                    'ASP' => 'ASP (Agua Superficial)',
                                    'AM' => 'AM (Agua de Mar)',
                                    'AR' => 'AR (Agua Residual)',
                                    'ASB' => 'ASB (Agua Subterránea)',
                                    'SM' => 'SM (Sedimento Marino)',
                                    'SL' => 'SL (Sedimento Lacustre)',
                                    'SA' => 'SA (Sedimento Acuático)',
                                    'SQ' => 'SQ (Sustancia Química)',
                                ];
                                $currentMatrix = $sampleEntry->sample_type ?? '';
                            @endphp
                            <input type="text" name="sample_type" id="sample_type" 
                                class="form-control navigable bg-white" 
                                value="{{ $matrixLabels[$currentMatrix] ?? $currentMatrix }}" 
                                readonly 
                                placeholder="Se completa automáticamente">
                            <input type="hidden" name="sample_type_code" id="sample_type_code" value="{{ $currentMatrix }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Concentración de la Muestra</label>
                            <div class="concentration-wrapper">
                                <div class="input-group">
                                    <input type="number" step="any" min="0" max="100" 
                                        name="sample_concentration"
                                        id="concentration_percent" 
                                        class="form-control navigable concentration-input" 
                                        placeholder="0 - 100"
                                        value="{{ old('sample_concentration', $sampleEntry->sample_concentration) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="concentration-alternatives mt-2">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="any" min="0" 
                                                    id="concentration_ppm" 
                                                    class="form-control concentration-input" 
                                                    placeholder="PPM">
                                                <span class="input-group-text">PPM</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="any" min="0" 
                                                    id="concentration_ppb" 
                                                    class="form-control concentration-input" 
                                                    placeholder="PPB">
                                                <span class="input-group-text">PPB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Ingrese en cualquier formato, los demás se calcularán automáticamente.
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de Lectura de Parámetros</label>
                            <input type="text" name="parameter_reading_date" class="form-control datetimepicker navigable"
                                value="{{ old('parameter_reading_date', $sampleEntry->parameter_reading_date) }}" placeholder="Seleccionar fecha">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Analista</label>
                            <input type="text" name="analyst" class="form-control navigable" 
                                value="{{ old('analyst', $sampleEntry->analyst) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">pH</label>
                            <input type="number" step="any" name="ph" class="form-control navigable"
                                value="{{ old('ph', $sampleEntry->ph) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Salinidad</label>
                            <div class="input-group">
                                <input type="number" step="any" name="salinity" class="form-control navigable"
                                    value="{{ old('salinity', $sampleEntry->salinity) }}">
                                <span class="input-group-text unit-badge">S‰</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Conductividad</label>
                            <div class="input-group">
                                <input type="number" step="any" name="conductivity" class="form-control navigable"
                                    value="{{ old('conductivity', $sampleEntry->conductivity) }}">
                                <span class="input-group-text unit-badge">μS/cm</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Oxígeno Disuelto</label>
                            <div class="input-group">
                                <input type="number" step="any" name="dissolved_oxygen" class="form-control navigable"
                                    value="{{ old('dissolved_oxygen', $sampleEntry->dissolved_oxygen) }}">
                                <span class="input-group-text unit-badge">mg O₂/L</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Temperatura</label>
                            <div class="input-group">
                                <input type="number" step="any" name="temperature" class="form-control navigable"
                                    value="{{ old('temperature', $sampleEntry->temperature) }}">
                                <span class="input-group-text unit-badge">°C</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea name="observations" class="form-control navigable" rows="3">{{ old('observations', $sampleEntry->observations) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===================== BIOENSAYOS ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-info text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    Bioensayos Asignados
                </div>
                <div class="card-body bg-light">
                    <div class="alert alert-info mb-3" role="alert">
                        <i class="fas fa-info-circle"></i> Los bioensayos fueron asignados en la recepción de muestra y no pueden modificarse aquí.
                    </div>
                    
                    <div class="row" id="bioassaysContainer">
                        @php
                            $allBioassays = [
                                'Daphnia magna Agudo',
                                'Daphnia magna Crónico',
                                'Isochrysis galbana',
                                'Selenastrum capricornutum',
                                'Tisbe biconicornis Agua',
                                'Tisbe biconicornis Sedimento',
                                'Arbacia spatuligera Estado Larval',
                                'Arbacia spatuligera Fecundación',
                            ];

                            $bioRoutes = [
                                'Daphnia magna Agudo' => 'daphnia-magna.index',
                                'Daphnia magna Crónico' => 'daphnia_magna_chronic.index',
                                'Isochrysis galbana' => 'isochrysis-galbana.index',
                                'Selenastrum capricornutum' => 'selenastum-capricornutum.index',
                                'Tisbe biconicornis Agua' => 'tisbe-longicornis-water.index',
                                'Tisbe biconicornis Sedimento' => 'tisbe-longicornis-riles.index',
                                'Arbacia spatuligera Fecundación' => 'arbacia_fertilization.index',
                                'Arbacia spatuligera Estado Larval' => 'arbacia_larval_stage.index',
                            ];
                        @endphp
                        @foreach ($allBioassays as $index => $bio)
                            <div class="col-md-6 mb-2">
                                <div class="bioassay-item d-flex align-items-center justify-content-between {{ in_array($bio, $assignedBioassays ?? []) ? 'bioassay-assigned' : 'bioassay-disabled' }}"
                                    data-bioassay="{{ $bio }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bioassay-checkbox-wrapper">
                                            <input class="form-check-input m-0" type="checkbox" id="bio{{ $index }}"
                                                {{ in_array($bio, $assignedBioassays ?? []) ? 'checked' : '' }} disabled>
                                        </div>
                                        <label class="form-check-label ms-2 mb-0" for="bio{{ $index }}">
                                            {{ $bio }}
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if (in_array($bio, $assignedBioassays ?? []))
                                            <span class="badge bg-success">Asignado</span>
                                            @php
                                                $route = $bioRoutes[$bio] ?? null;
                                                $daphnia = null;
                                                if ($route && str_contains($bio, 'Daphnia')) {
                                                    $daphnia = \App\Models\DaphniaMagnaTemplate::where('sample', $sampleEntry->internal_sample_code)->first();
                                                }
                                            @endphp
                                            @if ($daphnia)
                                                <a href="{{ route('daphnia-magna.edit', $daphnia->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Ir al ensayo
                                                </a>
                                            @elseif ($route)
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>No disponible</button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>No implementado</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===================== BASE TEMPLATE ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-primary text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    Información de la Plantilla
                </div>
                <div class="card-body bg-light">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold mb-1">Título</label>
                            <input type="text" name="title" class="form-control-plaintext text-muted"
                                value="INGRESO DE MUESTRAS" readonly>
                        </div>
                        <div style="min-width: 150px;">
                            <label class="form-label fw-bold mb-1">Código</label>
                            <input type="text" name="code" class="form-control-plaintext text-muted" value="IN-01.01"
                                readonly>
                        </div>
                        <div style="min-width: 100px;">
                            <label class="form-label fw-bold mb-1">Versión</label>
                            <input type="text" name="version" class="form-control-plaintext text-muted" 
                                value="{{ old('version', $sampleEntry->template->version ?? '01') }}" readonly>
                        </div>
                        <div style="min-width: 120px;">
                            <label class="form-label fw-bold mb-1">Vigencia</label>
                            <input type="text" name="validity" class="form-control-plaintext text-muted"
                                value="{{ old('validity', $sampleEntry->template->validity ?? '01.09.2023') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('sample_entries.show', $sampleEntry->id) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

@push('head')
    <!-- Flatpickr - Tema Dark -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================
           PERSONALIZACIÓN DE FLATPICKR - TEMA OSCURO
           ============================================ */
        
        /* Hacer el calendario más grande */
        .flatpickr-calendar {
            font-size: 15px !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4) !important;
        }

        /* Días más grandes */
        .flatpickr-day {
            width: 44px !important;
            height: 44px !important;
            line-height: 44px !important;
            font-size: 14px !important;
            border-radius: 8px !important;
        }

        /* Nombres de días de la semana */
        .flatpickr-weekday {
            font-size: 13px !important;
            font-weight: 600 !important;
        }

        /* Header del mes */
        .flatpickr-current-month {
            font-size: 16px !important;
        }

        /* ============================================
           SELECTOR DE HORA MEJORADO - MÁS GRANDE
           ============================================ */
        
        /* Contenedor del tiempo */
        .flatpickr-time {
            height: 60px !important;
            max-height: 60px !important;
            border-top: 1px solid #444 !important;
            margin-top: 10px !important;
            padding-top: 10px !important;
        }

        /* Inputs de hora y minutos más grandes */
        .flatpickr-time input {
            font-size: 24px !important;
            font-weight: 600 !important;
            height: 45px !important;
        }

        /* Separador : */
        .flatpickr-time .flatpickr-time-separator {
            font-size: 24px !important;
            font-weight: 600 !important;
            line-height: 45px !important;
        }

        /* Flechas de incremento/decremento */
        .flatpickr-time .numInputWrapper span {
            width: 20px !important;
            height: 20px !important;
        }

        .flatpickr-time .numInputWrapper span:after {
            font-size: 12px !important;
        }

        /* Hover en flechas */
        .flatpickr-time .numInputWrapper:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            border-radius: 6px !important;
        }

        /* AM/PM si se usa formato 12h (no aplica en 24h) */
        .flatpickr-am-pm {
            font-size: 16px !important;
            font-weight: 600 !important;
        }

        /* ============================================
           ESTILOS PARA LOS BIOENSAYOS
           ============================================ */
        .bioassay-item {
            padding: 12px 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            min-height: 48px;
        }

        .bioassay-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .bioassay-item.bioassay-assigned {
            background: #d1e7dd;
            border-color: #198754;
        }

        .bioassay-item.bioassay-disabled {
            background: #f8f9fa;
            opacity: 0.7;
        }

        .bioassay-checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .bioassay-checkbox-wrapper .form-check-input {
            width: 18px;
            height: 18px;
            cursor: default;
        }

        .bioassay-checkbox-wrapper .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        /* ============================================
           ESTILOS PARA CONCENTRACIÓN
           ============================================ */
        .concentration-wrapper {
            position: relative;
        }

        .concentration-alternatives {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #dee2e6;
            border-top: none;
        }

        .concentration-input:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        #concentration_percent {
            border-radius: 8px 0 0 8px;
            font-size: 1.1rem;
            font-weight: 500;
        }

        #concentration_percent + .input-group-text {
            border-radius: 0 8px 8px 0;
            background: #198754;
            color: white;
            font-weight: 600;
        }

        .concentration-alternatives .input-group-text {
            font-size: 0.75rem;
            min-width: 45px;
            justify-content: center;
        }

        /* Focus highlight para navegación */
        .navigable:focus {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Input de fecha con icono */
        .datetimepicker {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }

        /* Textarea con estilo consistente */
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Input readonly con fondo distintivo */
        input[readonly].form-control {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        .form-control.bg-white[readonly] {
            background-color: #fff !important;
        }

        /* ============================================
           ESTILOS PARA UNIDADES DE MEDIDA
           ============================================ */
        .unit-badge {
            background: #6c757d;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            min-width: 70px;
            justify-content: center;
        }
    </style>
@endpush

@push('scripts')
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ============= NAVEGACIÓN CON FLECHAS =============
            const navigableElements = document.querySelectorAll('.navigable');
            
            navigableElements.forEach((element, index) => {
                element.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const nextIndex = index + 1;
                        if (nextIndex < navigableElements.length) {
                            navigableElements[nextIndex].focus();
                        }
                    }
                    
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const prevIndex = index - 1;
                        if (prevIndex >= 0) {
                            navigableElements[prevIndex].focus();
                        }
                    }
                });
            });

            // ============= INICIALIZAR FLATPICKR - TEMA OSCURO =============
            flatpickr(".datetimepicker", {
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i",
                locale: "es",
                allowInput: true,
            });

            // ============= CONVERSIÓN DE CONCENTRACIONES =============
            const percentInput = document.getElementById('concentration_percent');
            const ppmInput = document.getElementById('concentration_ppm');
            const ppbInput = document.getElementById('concentration_ppb');

            let isUpdating = false;

            function updateFromPercent(value) {
                if (isUpdating) return;
                isUpdating = true;
                
                const percent = parseFloat(value) || 0;
                ppmInput.value = percent > 0 ? (percent * 10000).toFixed(4) : '';
                ppbInput.value = percent > 0 ? (percent * 10000000).toFixed(1) : '';
                
                isUpdating = false;
            }

            function updateFromPPM(value) {
                if (isUpdating) return;
                isUpdating = true;
                
                const ppm = parseFloat(value) || 0;
                const percent = ppm / 10000;
                percentInput.value = percent > 0 ? percent.toFixed(6) : '';
                ppbInput.value = ppm > 0 ? (ppm * 1000).toFixed(1) : '';
                
                isUpdating = false;
            }

            function updateFromPPB(value) {
                if (isUpdating) return;
                isUpdating = true;
                
                const ppb = parseFloat(value) || 0;
                const percent = ppb / 10000000;
                const ppm = ppb / 1000;
                percentInput.value = percent > 0 ? percent.toFixed(9) : '';
                ppmInput.value = ppm > 0 ? ppm.toFixed(4) : '';
                
                isUpdating = false;
            }

            percentInput.addEventListener('input', function() {
                updateFromPercent(this.value);
            });

            ppmInput.addEventListener('input', function() {
                updateFromPPM(this.value);
            });

            ppbInput.addEventListener('input', function() {
                updateFromPPB(this.value);
            });

            // Inicializar valores si hay valor existente
            if (percentInput.value) {
                updateFromPercent(percentInput.value);
            }

            // ============= SWEETALERT PARA CONFIRMACIÓN DEL ENVÍO =============
            $('#entryForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Confirmar actualización?',
                    text: "Se guardarán los cambios realizados.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush