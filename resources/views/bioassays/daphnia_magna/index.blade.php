@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-secondary">Gestión de Muestras Daphnia Magna</h1>
        <a href="{{ route('daphnia-magna.create') }}"
           class="btn btn-light text-dark border btn-action">
           <i class="bi bi-plus-circle me-1"></i> Crear Entrada
        </a>
    </div>

    <div class="row mb-4">
        <!-- Tabla Muestras Nuevas -->
        <div class="col-md-6 mb-4">
            <h3 class="text-secondary mb-2">Muestras Nuevas</h3>
            <div class="table-responsive table-box-scroll">
                <table id="nuevasTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Muestra</th>
                            <th>Analista</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bioassays as $bioassay)
                            <tr>
                                <td>{{ $bioassay->id }}</td>
                                <td>{{ $bioassay->sample ?? 'N/A' }}</td>
                                <td>{{ $bioassay->analyst ?? 'N/A' }}</td>
                                <td>{{ $bioassay->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('daphnia-magna.create') }}"
                                       class="btn btn-sm btn-light text-dark border btn-action">
                                       Comenzar Análisis
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay nuevas muestras</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabla Muestras en Proceso -->
        <div class="col-md-6">
            <h3 class="text-secondary mb-2">Muestras en Proceso</h3>
            <div class="table-responsive table-box-scroll">
                <table id="procesoTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Muestra</th>
                            <th>Analista</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bioassays as $bioassay)
                            <tr>
                                <td>{{ $bioassay->id }}</td>
                                <td>{{ $bioassay->sample ?? 'N/A' }}</td>
                                <td>{{ $bioassay->analyst ?? 'N/A' }}</td>
                                <td>{{ $bioassay->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('daphnia-magna.create') }}"
                                       class="btn btn-sm btn-light text-dark border btn-action">
                                       Continuar Análisis
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay muestras en proceso.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla Historial de Muestras Enviadas -->
    <div class="row mt-10">
        <div class="col-12">
            <h3 class="text-secondary mb-2">Historial de Muestras Enviadas</h3>
            <div class="table-responsive table-box">
                <table id="historialTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>ID</th>
                            <th>Muestra</th>
                            <th>Analista</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bioassays as $bioassay)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-select" value="{{ $bioassay->id }}">
                                </td>
                                <td>{{ $bioassay->id }}</td>
                                <td>{{ $bioassay->sample ?? 'N/A' }}</td>
                                <td>{{ $bioassay->analyst ?? 'N/A' }}</td>
                                <td>{{ $bioassay->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('daphnia-magna.edit', $bioassay) }}"
                                       class="btn btn-sm btn-light text-dark border btn-edit me-1">
                                       Editar
                                    </a>
                                    <form action="{{ route('daphnia-magna.destroy', $bioassay) }}" method="POST" class="d-inline form-delete">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-dark border btn-delete">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay historial de muestras.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    table th, table td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    table td {
        background-color: #ffffff !important;
    }

    table input[type="checkbox"] {
        cursor: pointer;
        width: 16px;
        height: 16px;
    }

    .btn-action:hover {
        background-color: #1d4ed8 !important;
        color: white !important;
        border-color: #1d4ed8 !important;
    }

    .btn-edit:hover {
        background-color: #15803d !important;
        color: white !important;
        border-color: #15803d !important;
    }
    .btn-delete:hover {
        background-color: #b91c1c !important;
        color: white !important;
        border-color: #b91c1c !important;
    }

    .table-box-scroll {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        max-height: 400px; /* espacio para ~10 filas */
        overflow-y: auto;
        padding: 0.5rem;
        background-color: #f9f9f9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .table-box {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 0.5rem;
        background-color: #f9f9f9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    h3.text-secondary {
        font-weight: 700;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#nuevasTable, #procesoTable, #historialTable').DataTable({
            paging: true,
            ordering: true,
            info: true,
            searching: true,
            lengthMenu: [10,25,50],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ entradas",
                info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                infoEmpty: "No hay entradas",
                paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" },
                zeroRecords: "No se encontraron coincidencias"
            },
            columnDefs: [{ orderable: false, targets: [-1] }]
        });

        // Selección en historial
        $('#selectAll').on('click', function() {
            $('.row-select').prop('checked', this.checked);
        });

        $('#historialTable').on('change', '.row-select', function() {
            $('#selectAll').prop('checked', $('.row-select:checked').length === $('.row-select').length);
        });

        $('.form-delete').on('submit', function(e){
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: '¿Eliminar muestra?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => { if(result.isConfirmed) form.submit(); });
        });
    });
</script>
@endpush
