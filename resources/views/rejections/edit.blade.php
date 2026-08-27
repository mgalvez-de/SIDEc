@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc" style="height: 80px; display: block; margin: 0 auto 20px auto;">

        <!-- Título -->
        <h1 class="mb-4 text-secondary text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
            Editar Rechazo #{{ $rejection->id }}
        </h1>

        <!-- Formulario -->
        <form id="rejectionForm" action="{{ route('rejections.update', $rejection) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ===================== INFORMACIÓN DE LA MUESTRA ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-danger text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-exclamation-triangle me-2"></i>Información de la Muestra Rechazada
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Código Interno de Muestra</label>
                            <input type="text" name="internal_sample_code" class="form-control navigable"
                                value="{{ old('internal_sample_code', $rejection->internal_sample_code) }}" 
                                placeholder="Ej: COD-2024-001">
                            @error('internal_sample_code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Identificación de la Muestra</label>
                            <input type="text" name="sample_identifier" class="form-control navigable"
                                value="{{ old('sample_identifier', $rejection->sample_identifier) }}" 
                                placeholder="Ej: Muestra-001">
                            @error('sample_identifier')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Motivo del Rechazo <span class="text-danger">*</span></label>
                            <select name="reason_for_rejection" class="form-select navigable" required>
                                <option value="" disabled>Seleccione un motivo</option>
                                @php
                                    $reasons = [
                                        'Muestra en mal estado',
                                        'Contenedor inadecuado',
                                        'Volumen insuficiente',
                                        'Temperatura fuera de rango',
                                        'Etiquetado incorrecto',
                                        'Muestra contaminada',
                                        'Tiempo de custodia excedido',
                                        'Documentación incompleta',
                                        'Otro'
                                    ];
                                    $currentReason = old('reason_for_rejection', $rejection->reason_for_rejection);
                                @endphp
                                @foreach($reasons as $reason)
                                    <option value="{{ $reason }}" {{ $currentReason == $reason ? 'selected' : '' }}>
                                        {{ $reason }}{{ $reason == 'Otro' ? ' (especificar en observaciones)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reason_for_rejection')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== RESPONSABLES ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-warning text-dark"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-people me-2"></i>Responsables
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Quién Rechaza</label>
                            <input type="text" name="who_rejects" class="form-control navigable"
                                value="{{ old('who_rejects', $rejection->who_rejects) }}" 
                                placeholder="Nombre del responsable">
                            @error('who_rejects')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Quién Informa al Cliente</label>
                            <input type="text" name="who_informs_the_client" class="form-control navigable"
                                value="{{ old('who_informs_the_client', $rejection->who_informs_the_client) }}" 
                                placeholder="Nombre de quien informa">
                            @error('who_informs_the_client')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Instrucciones del Cliente</label>
                            <textarea name="customer_instructions" class="form-control navigable" rows="3"
                                placeholder="Instrucciones proporcionadas por el cliente...">{{ old('customer_instructions', $rejection->customer_instructions) }}</textarea>
                            @error('customer_instructions')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== OBSERVACIONES ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-secondary text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Observaciones Adicionales</label>
                            <textarea name="observations" class="form-control navigable" rows="4" maxlength="300"
                                placeholder="Máximo 300 caracteres...">{{ old('observations', $rejection->observations) }}</textarea>
                            <small class="text-muted">
                                <span id="charCount">{{ strlen(old('observations', $rejection->observations ?? '')) }}</span>/300 caracteres
                            </small>
                            @error('observations')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== BASE TEMPLATE ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-primary text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-file-earmark-text me-2"></i>Información de la Plantilla
                </div>
                <div class="card-body bg-light">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold mb-1">Título</label>
                            <input type="text" name="title" class="form-control navigable"
                                value="{{ old('title', $rejection->template->title ?? 'RECHAZO DE MUESTRAS') }}" required>
                            @error('title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div style="min-width: 150px;">
                            <label class="form-label fw-bold mb-1">Código</label>
                            <input type="text" name="code" class="form-control navigable"
                                value="{{ old('code', $rejection->template->code ?? 'RO-02.02') }}">
                            @error('code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div style="min-width: 100px;">
                            <label class="form-label fw-bold mb-1">Versión</label>
                            <input type="number" name="version" class="form-control navigable" min="1"
                                value="{{ old('version', $rejection->template->version ?? 1) }}" required>
                            @error('version')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div style="min-width: 150px;">
                            <label class="form-label fw-bold mb-1">Vigencia</label>
                            <input type="text" name="validity" class="form-control navigable"
                                value="{{ old('validity', $rejection->template->validity ?? '') }}" 
                                placeholder="dd.mm.yyyy">
                            @error('validity')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== METADATOS ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-dark text-white"
                    style="font-size: 1.2rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-clock-history me-2"></i>Información del Registro
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus me-1"></i>
                                <strong>Creado:</strong> 
                                {{ $rejection->created_at ? $rejection->created_at->format('d/m/Y H:i') : '-' }}
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                <i class="bi bi-calendar-check me-1"></i>
                                <strong>Última actualización:</strong> 
                                {{ $rejection->updated_at ? $rejection->updated_at->format('d/m/Y H:i') : '-' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== BOTONES DE ACCIÓN ===================== --}}
            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('rejections.show', $rejection) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Cancelar
                </a>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btnReset">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Restablecer
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('head')
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Focus highlight para navegación */
        .navigable:focus {
            outline: 2px solid #198754;
            outline-offset: 2px;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        /* Efecto hover en cards */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        /* Estilo para select */
        .form-select:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        /* Botones */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: scale(1.02);
        }

        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }

        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.4);
        }

        /* Textarea */
        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Campo modificado */
        .field-modified {
            border-color: #ffc107 !important;
            background-color: #fffbeb !important;
        }

        /* Animación de entrada */
        .card {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('scripts')
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

            // ============= CONTADOR DE CARACTERES =============
            const observationsField = document.querySelector('textarea[name="observations"]');
            const charCount = document.getElementById('charCount');

            if (observationsField && charCount) {
                observationsField.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                    
                    if (this.value.length >= 280) {
                        charCount.classList.add('text-danger');
                        charCount.classList.remove('text-muted');
                    } else {
                        charCount.classList.remove('text-danger');
                        charCount.classList.add('text-muted');
                    }
                });
            }

            // ============= DETECTAR CAMBIOS EN CAMPOS =============
            const form = document.getElementById('rejectionForm');
            const originalValues = {};

            // Guardar valores originales
            navigableElements.forEach((element) => {
                originalValues[element.name] = element.value;
                
                element.addEventListener('input', function() {
                    if (this.value !== originalValues[this.name]) {
                        this.classList.add('field-modified');
                    } else {
                        this.classList.remove('field-modified');
                    }
                });
            });

            // ============= BOTÓN RESTABLECER =============
            document.getElementById('btnReset').addEventListener('click', function() {
                Swal.fire({
                    title: '¿Restablecer cambios?',
                    text: "Se perderán todos los cambios no guardados.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#198754',
                    confirmButtonText: 'Sí, restablecer',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        navigableElements.forEach((element) => {
                            element.value = originalValues[element.name];
                            element.classList.remove('field-modified');
                        });
                        // Actualizar contador de caracteres
                        if (charCount && observationsField) {
                            charCount.textContent = observationsField.value.length;
                        }
                        Swal.fire({
                            title: 'Restablecido',
                            text: 'Los campos han sido restablecidos.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // ============= SWEETALERT PARA CONFIRMACIÓN DEL ENVÍO =============
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formEl = this;

                // Verificar si hay cambios
                let hasChanges = false;
                navigableElements.forEach((element) => {
                    if (element.value !== originalValues[element.name]) {
                        hasChanges = true;
                    }
                });

                if (!hasChanges) {
                    Swal.fire({
                        title: 'Sin cambios',
                        text: 'No se han realizado modificaciones.',
                        icon: 'info',
                        confirmButtonColor: '#198754'
                    });
                    return;
                }

                Swal.fire({
                    title: '¿Guardar cambios?',
                    text: "Se actualizará la información del rechazo.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formEl.submit();
                    }
                });
            });

            // ============= ADVERTENCIA AL SALIR CON CAMBIOS =============
            window.addEventListener('beforeunload', function(e) {
                let hasChanges = false;
                navigableElements.forEach((element) => {
                    if (element.value !== originalValues[element.name]) {
                        hasChanges = true;
                    }
                });

                if (hasChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>
@endpush