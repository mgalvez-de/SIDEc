@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc" style="height: 80px; display: block; margin: 0 auto 20px auto;">

        <!-- Título -->
        <h1 class="mb-4 text-secondary text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
            Nuevo Rechazo de Muestra
        </h1>

        <!-- Formulario -->
        <form id="rejectionForm" action="{{ route('rejections.store') }}" method="POST">
            @csrf

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
                                value="{{ old('internal_sample_code') }}" placeholder="Ej: COD-2024-001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Identificación de la Muestra</label>
                            <input type="text" name="sample_identifier" class="form-control navigable"
                                value="{{ old('sample_identifier') }}" placeholder="Ej: Muestra-001">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Motivo del Rechazo <span class="text-danger">*</span></label>
                            <select name="reason_for_rejection" class="form-select navigable" required>
                                <option value="" disabled selected>Seleccione un motivo</option>
                                <option value="Muestra en mal estado" {{ old('reason_for_rejection') == 'Muestra en mal estado' ? 'selected' : '' }}>
                                    Muestra en mal estado
                                </option>
                                <option value="Contenedor inadecuado" {{ old('reason_for_rejection') == 'Contenedor inadecuado' ? 'selected' : '' }}>
                                    Contenedor inadecuado
                                </option>
                                <option value="Volumen insuficiente" {{ old('reason_for_rejection') == 'Volumen insuficiente' ? 'selected' : '' }}>
                                    Volumen insuficiente
                                </option>
                                <option value="Temperatura fuera de rango" {{ old('reason_for_rejection') == 'Temperatura fuera de rango' ? 'selected' : '' }}>
                                    Temperatura fuera de rango
                                </option>
                                <option value="Etiquetado incorrecto" {{ old('reason_for_rejection') == 'Etiquetado incorrecto' ? 'selected' : '' }}>
                                    Etiquetado incorrecto
                                </option>
                                <option value="Muestra contaminada" {{ old('reason_for_rejection') == 'Muestra contaminada' ? 'selected' : '' }}>
                                    Muestra contaminada
                                </option>
                                <option value="Tiempo de custodia excedido" {{ old('reason_for_rejection') == 'Tiempo de custodia excedido' ? 'selected' : '' }}>
                                    Tiempo de custodia excedido
                                </option>
                                <option value="Documentación incompleta" {{ old('reason_for_rejection') == 'Documentación incompleta' ? 'selected' : '' }}>
                                    Documentación incompleta
                                </option>
                                <option value="Otro" {{ old('reason_for_rejection') == 'Otro' ? 'selected' : '' }}>
                                    Otro (especificar en observaciones)
                                </option>
                            </select>
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
                                value="{{ old('who_rejects') }}" placeholder="Nombre del responsable">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Quién Informa al Cliente</label>
                            <input type="text" name="who_informs_the_client" class="form-control navigable"
                                value="{{ old('who_informs_the_client') }}" placeholder="Nombre de quien informa">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Instrucciones del Cliente</label>
                            <textarea name="customer_instructions" class="form-control navigable" rows="3"
                                placeholder="Instrucciones proporcionadas por el cliente...">{{ old('customer_instructions') }}</textarea>
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
                                placeholder="Máximo 300 caracteres...">{{ old('observations') }}</textarea>
                            <small class="text-muted">
                                <span id="charCount">0</span>/300 caracteres
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== BASE TEMPLATE (AL FONDO) ===================== --}}
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-primary text-white"
                    style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <i class="bi bi-file-earmark-text me-2"></i>Información de la Plantilla
                </div>
                <div class="card-body bg-light">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold mb-1">Título</label>
                            <input type="text" name="title" class="form-control-plaintext text-muted"
                                value="RECHAZO DE MUESTRAS" readonly>
                        </div>
                        <div style="min-width: 150px;">
                            <label class="form-label fw-bold mb-1">Código</label>
                            <input type="text" name="code" class="form-control-plaintext text-muted" value="RO-02.02"
                                readonly>
                        </div>
                        <div style="min-width: 100px;">
                            <label class="form-label fw-bold mb-1">Versión</label>
                            <input type="hidden" name="version" value="1">
                            <input type="text" class="form-control-plaintext text-muted" value="01" readonly>
                        </div>
                        <div style="min-width: 120px;">
                            <label class="form-label fw-bold mb-1">Vigencia</label>
                            <input type="text" name="validity" class="form-control-plaintext text-muted"
                                value="{{ date('d.m.Y') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-danger me-2">
                    <i class="bi bi-x-circle me-1"></i>Registrar Rechazo
                </button>
                <a href="{{ route('rejections.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Cancelar
                </a>
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
            outline: 2px solid #dc3545;
            outline-offset: 2px;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Botón de submit */
        .btn-danger {
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        /* Textarea contador de caracteres */
        textarea {
            resize: vertical;
            min-height: 100px;
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
                // Actualizar al cargar
                charCount.textContent = observationsField.value.length;

                // Actualizar al escribir
                observationsField.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                    
                    // Cambiar color si está cerca del límite
                    if (this.value.length >= 280) {
                        charCount.classList.add('text-danger');
                        charCount.classList.remove('text-muted');
                    } else {
                        charCount.classList.remove('text-danger');
                        charCount.classList.add('text-muted');
                    }
                });
            }

            // ============= SWEETALERT PARA CONFIRMACIÓN DEL ENVÍO =============
            document.getElementById('rejectionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Confirmar rechazo?',
                    text: "Se registrará el rechazo de la muestra.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, registrar rechazo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush