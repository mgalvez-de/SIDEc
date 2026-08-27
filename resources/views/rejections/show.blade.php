@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('img/xd.webp') }}" alt="Logo SIDEc" style="height: 80px; display: block; margin: 0 auto 20px auto;">

        <!-- Título -->
        <h1 class="mb-4 text-secondary text-center"
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
            Detalle del Rechazo #{{ $rejection->id }}
        </h1>

        {{-- ===================== INFORMACIÓN DE LA MUESTRA ===================== --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-danger text-white"
                style="font-size: 1.5rem; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-exclamation-triangle me-2"></i>Información de la Muestra Rechazada
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Código Interno de Muestra</label>
                        <div class="form-control-plaintext bg-white rounded p-2 border">
                            {{ $rejection->internal_sample_code ?? 'No especificado' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Identificación de la Muestra</label>
                        <div class="form-control-plaintext bg-white rounded p-2 border">
                            {{ $rejection->sample_identifier ?? 'No especificado' }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold text-muted">Motivo del Rechazo</label>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>{{ $rejection->reason_for_rejection ?? 'No especificado' }}</strong>
                        </div>
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
                        <label class="form-label fw-bold text-muted">Quién Rechaza</label>
                        <div class="form-control-plaintext bg-white rounded p-2 border">
                            <i class="bi bi-person-badge me-1 text-warning"></i>
                            {{ $rejection->who_rejects ?? 'No especificado' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Quién Informa al Cliente</label>
                        <div class="form-control-plaintext bg-white rounded p-2 border">
                            <i class="bi bi-telephone me-1 text-info"></i>
                            {{ $rejection->who_informs_the_client ?? 'No especificado' }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold text-muted">Instrucciones del Cliente</label>
                        <div class="form-control-plaintext bg-white rounded p-2 border" style="min-height: 80px;">
                            {{ $rejection->customer_instructions ?? 'Sin instrucciones' }}
                        </div>
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
                    <div class="col-md-12">
                        <div class="form-control-plaintext bg-white rounded p-3 border" style="min-height: 100px;">
                            @if($rejection->observations)
                                {{ $rejection->observations }}
                            @else
                                <span class="text-muted fst-italic">Sin observaciones adicionales</span>
                            @endif
                        </div>
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
                        <label class="form-label fw-bold mb-1 text-muted">Título</label>
                        <div class="form-control-plaintext">
                            {{ $rejection->template->title ?? 'RECHAZO DE MUESTRAS' }}
                        </div>
                    </div>
                    <div style="min-width: 150px;">
                        <label class="form-label fw-bold mb-1 text-muted">Código</label>
                        <div class="form-control-plaintext">
                            <span class="badge bg-primary">{{ $rejection->template->code ?? 'RO-02.02' }}</span>
                        </div>
                    </div>
                    <div style="min-width: 100px;">
                        <label class="form-label fw-bold mb-1 text-muted">Versión</label>
                        <div class="form-control-plaintext">
                            {{ str_pad($rejection->template->version ?? '1', 2, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    <div style="min-width: 120px;">
                        <label class="form-label fw-bold mb-1 text-muted">Vigencia</label>
                        <div class="form-control-plaintext">
                            {{ $rejection->template->validity ?? '-' }}
                        </div>
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
            <a href="{{ route('rejections.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver al listado
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('rejections.edit', $rejection) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                <form action="{{ route('rejections.destroy', $rejection) }}" method="POST" class="d-inline form-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Efecto hover en cards */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        /* Estilo para campos de solo lectura */
        .form-control-plaintext {
            transition: all 0.2s ease;
        }

        .form-control-plaintext:hover {
            background-color: #f8f9fa !important;
        }

        /* Botones */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: scale(1.02);
        }

        .btn-warning:hover {
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        }

        .btn-danger:hover {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary:hover {
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
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

        /* Delays para animación escalonada */
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // SweetAlert para confirmación de eliminación
            document.querySelector('.form-delete').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Eliminar rechazo?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
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