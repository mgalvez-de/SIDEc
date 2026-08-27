@extends('layouts.app')

@section('content')
    <div class="container-fluid px-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rechazos de Muestras</li>
            </ol>
        </nav>

        <!-- Encabezado con título -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="text-secondary"
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 700;">
                Rechazos de Muestras
            </h1>
        </div>

        <!-- Barra de controles sobre la tabla -->
        <div class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
            <!-- Lado izquierdo: Selector de entradas y búsqueda -->
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div id="entriesLengthContainer"></div>
                <div id="searchContainer" class="flex-grow-1" style="max-width: 400px;"></div>
            </div>

            <!-- Lado derecho: Exportar y Nuevo Rechazo -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="dropdown">
                    <button
                        class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-export dropdown-toggle"
                        type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download"></i> Exportar
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li>
                            <a class="dropdown-item" href="#" id="exportExcel">
                                <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="exportPrint">
                                <i class="bi bi-printer"></i> Imprimir
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('rejections.create') }}"
                    class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-add">
                    <i class="bi bi-plus-lg"></i> Nuevo Rechazo
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table id="rejectionsTable" class="table table-bordered table-striped table-hover align-middle text-center">
                <thead class="text-center">
                    <tr>
                        <th style="width: 30px;">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>ID</th>
                        <th>Código Muestra</th>
                        <th>Identificador</th>
                        <th>Motivo Rechazo</th>
                        <th>Quién Rechaza</th>
                        <th>Código Doc.</th>
                        <th>Acciones</th>
                    </tr>
                    <tr class="filters">
                        <th></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="ID"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Código"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Identificador"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Motivo"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Quién"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Doc."></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Solo iterar si hay datos, sin @empty para evitar conflicto con DataTables --}}
                    @foreach($rejections as $rejection)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-select" value="{{ $rejection->id }}">
                            </td>
                            <td>{{ $rejection->id }}</td>
                            <td>{{ $rejection->internal_sample_code ?? '-' }}</td>
                            <td>{{ $rejection->sample_identifier ?? '-' }}</td>
                            <td>
                                <span class="badge bg-danger text-wrap" style="max-width: 200px;">
                                    {{ Str::limit($rejection->reason_for_rejection, 30) ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $rejection->who_rejects ?? '-' }}</td>
                            <td>
                                <small class="text-muted">{{ $rejection->template->code ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('rejections.show', $rejection) }}"
                                        class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-view"
                                        title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('rejections.edit', $rejection) }}"
                                        class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-edit"
                                        title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('rejections.destroy', $rejection) }}" method="POST"
                                        class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-delete"
                                            title="Eliminar">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Información y paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div id="tableInfo"></div>
                <div id="tablePagination"></div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        /* Centrar contenido de celdas y encabezados */
        #rejectionsTable th,
        #rejectionsTable td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* Fondo blanco */
        #rejectionsTable td {
            background-color: #ffffff !important;
        }

        /* Checkbox */
        #rejectionsTable input[type="checkbox"] {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        /* Estilo para la fila de filtros */
        .filters th {
            background-color: #f8f9fa !important;
            padding: 8px 5px !important;
        }

        .filters .column-search {
            font-size: 0.85rem;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .filters .column-search:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
        }

        /* Botones hover */
        .btn-view:hover {
            background-color: #1d4ed8 !important;
            color: white !important;
            border-color: #1d4ed8 !important;
        }

        .btn-edit:hover,
        .btn-add:hover {
            background-color: #15803d !important;
            color: white !important;
            border-color: #15803d !important;
        }

        .btn-delete:hover {
            background-color: #b91c1c !important;
            color: white !important;
            border-color: #b91c1c !important;
        }

        .btn-export:hover {
            background-color: #1d4ed8 !important;
            color: white !important;
            border-color: #1d4ed8 !important;
        }

        /* Hover en las opciones del dropdown Exportar */
        .dropdown-menu .dropdown-item:hover {
            background-color: rgba(29, 78, 216, 0.1);
            color: #1d4ed8 !important;
            transition: all 0.2s ease;
        }

        /* Ajustar columna de acciones para botones en línea */
        #rejectionsTable td:last-child {
            white-space: nowrap;
        }

        /* Estilos para breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
            color: #6b7280;
        }

        .breadcrumb-item a {
            color: #1d4ed8;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #1e40af;
        }

        .breadcrumb-item.active {
            color: #6b7280;
        }

        /* Estilos para la barra de controles */
        .table-controls-wrapper {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animación para la tabla */
        .table-responsive {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Estilos para los controles de DataTables */
        .dataTables_length label,
        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px 35px 5px 10px;
            transition: all 0.3s ease;
            background-color: white;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
            min-width: 70px;
        }

        .dataTables_length select:hover {
            border-color: #1d4ed8;
            background-color: #f8f9fa;
        }

        .dataTables_length select:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
            outline: none;
        }

        .dataTables_filter {
            width: 100%;
        }

        .dataTables_filter label {
            width: 100%;
        }

        .dataTables_filter input {
            width: 100% !important;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }

        .dataTables_filter input:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
            outline: none;
            transform: scale(1.01);
        }

        /* Animación para los botones */
        .btn-light {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-light::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-light:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-light:active {
            transform: scale(0.95);
        }

        /* Animación para filas de la tabla */
        #rejectionsTable tbody tr {
            animation: fadeInRow 0.3s ease-out;
            transition: all 0.2s ease;
        }

        @keyframes fadeInRow {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        #rejectionsTable tbody tr:hover {
            background-color: #fef2f2 !important;
            transform: scale(1.005);
            box-shadow: 0 2px 8px rgba(185, 28, 28, 0.1);
        }

        /* Animación para la paginación */
        .dataTables_paginate {
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dataTables_paginate .paginate_button {
            transition: all 0.2s ease;
        }

        .dataTables_paginate .paginate_button:hover {
            transform: translateY(-2px);
        }

        /* Estilos personalizados para Toastr */
        .toast-success {
            background-color: #15803d !important;
        }

        .toast-error {
            background-color: #b91c1c !important;
        }

        .toast-info {
            background-color: #1d4ed8 !important;
        }

        .toast-warning {
            background-color: #f59e0b !important;
        }

        #toast-container > div {
            opacity: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        #toast-container > .toast {
            background-image: none !important;
            padding: 15px 20px 15px 50px;
        }

        #toast-container > .toast:before {
            position: absolute;
            font-family: 'Bootstrap Icons';
            font-size: 24px;
            line-height: 24px;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        #toast-container > .toast-success:before {
            content: "\f26b";
        }

        #toast-container > .toast-error:before {
            content: "\f659";
        }

        #toast-container > .toast-info:before {
            content: "\f431";
        }

        #toast-container > .toast-warning:before {
            content: "\f33a";
        }

        /* Badge de motivo de rechazo */
        .badge.bg-danger {
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-controls-wrapper {
                flex-direction: column;
                align-items: stretch !important;
            }

            #searchContainer {
                max-width: 100% !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configuración de Toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Mostrar notificación de sesión si existe
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if(session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if(session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif

            // Inicializar DataTable
            const table = $('#rejectionsTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 10,
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0, 7]
                    },
                    {
                        searchable: false,
                        targets: [0, 7]
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Buscar en todas las columnas...",
                    lengthMenu: "Mostrar _MENU_ entradas",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    infoEmpty: "No hay entradas",
                    infoFiltered: "(filtrado de _MAX_ entradas totales)",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    zeroRecords: "No se encontraron coincidencias",
                    emptyTable: '<div class="text-muted d-flex flex-column align-items-center py-4"><i class="bi bi-inbox fs-2 mb-2"></i><span>No hay rechazos registrados.</span></div>'
                },
                dom: "<'row'<'col-sm-12'B>>lfrtip",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Rechazos_' + new Date().toISOString().slice(0, 10),
                        className: 'd-none',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Rechazos de Muestras',
                        className: 'd-none',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6]
                        }
                    }
                ],
                orderCellsTop: true,
                fixedHeader: true,
                initComplete: function() {
                    $('.dataTables_length').appendTo('#entriesLengthContainer');
                    $('.dataTables_filter').appendTo('#searchContainer');
                    $('.dataTables_info').appendTo('#tableInfo');
                    $('.dataTables_paginate').appendTo('#tablePagination');
                }
            });

            // Búsqueda por columna
            $('.column-search').on('keyup change', function() {
                const columnIndex = $(this).parent().index();
                table.column(columnIndex).search(this.value).draw();
            });

            // Exportar
            $('#exportExcel').on('click', function(e) {
                e.preventDefault();
                table.button(0).trigger();
                toastr.success('Exportando a Excel...', 'Éxito');
            });

            $('#exportPrint').on('click', function(e) {
                e.preventDefault();
                table.button(1).trigger();
                toastr.info('Preparando impresión...', 'Información');
            });

            // Checkboxes
            $('#selectAll').on('click', function() {
                $('.row-select').prop('checked', this.checked);
            });

            $('#rejectionsTable').on('change', '.row-select', function() {
                $('#selectAll').prop('checked', $('.row-select:checked').length === $('.row-select').length);
            });

            // Confirmación de eliminación
            $(document).on('submit', '.form-delete', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Eliminar rechazo?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b91c1c',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Notificaciones
            $(document).on('click', '.btn-view', function() {
                toastr.info('Cargando detalles del rechazo...', 'Información');
            });

            $(document).on('click', '.btn-edit', function() {
                toastr.info('Abriendo formulario de edición...', 'Información');
            });

            $('.btn-add').on('click', function() {
                toastr.info('Abriendo formulario para nuevo rechazo...', 'Información');
            });
        });
    </script>
@endpush