@extends('layouts.app')

@section('content')
    <div class="container-fluid px-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Recepción de Muestras</li>
            </ol>
        </nav>

        <!-- Encabezado con título -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="text-secondary"
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 700;">
                Recepción de Muestras
            </h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="successAlert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="errorAlert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <!-- Barra de controles sobre la tabla -->
        <div class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
            <!-- Lado izquierdo: Selector de entradas y búsqueda -->
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div id="entriesLengthContainer"></div>
                <div id="searchContainer" class="flex-grow-1" style="max-width: 400px;"></div>
            </div>

            <!-- Lado derecho: Exportar e Ingresar -->
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

                <a href="{{ route('receptions.create') }}"
                    class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-add">
                    <i class="bi bi-plus-lg"></i> Ingresar Nueva Muestra
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table id="receptionsTable"
                class="table table-bordered table-striped table-hover align-middle text-center container">
                <thead class="text-center">
                    <tr>
                        <th style="width: 30px;">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Número de Informe</th>
                        <th>ID</th>
                        <th>Identificación de Muestra</th>
                        <th>Código Interno</th>
                        <th>Fecha de Recepción</th>
                        <th>Fecha de Muestreo</th>
                        <th>Entregado Por</th>
                        <th>Cliente</th>
                        <th>Recibido Por</th>
                        <th>Matriz</th>
                        <th>Temperatura Recibida</th>
                        <th>Temperatura Corregida</th>
                        <th>Acciones</th>
                    </tr>
                    <tr class="filters">
                        <th></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="N° Informe"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="ID"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="ID Muestra"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Código Interno"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Fecha Recepción"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Fecha Muestreo"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Entregado por"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cliente">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Recibido por"></th>
                        <th><input type="text" class="form-control form-control-sm column-search" placeholder="Matriz">
                        </th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Temp. Recibida"></th>
                        <th><input type="text" class="form-control form-control-sm column-search"
                                placeholder="Temp. Corregida"></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receptions as $reception)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-select" value="{{ $reception->id }}">
                            </td>
                            <td>{{ $reception->report_number ?? 'Nulo' }}</td>
                            <td>{{ $reception->template_id }}</td>
                            <td>{{ $reception->sample_identifier ?? 'Nulo' }}</td>
                            <td>{{ $reception->internal_sample_code ?? 'Nulo' }}</td>
                            <td>{{ $reception->received_at ?? 'Nulo' }}</td>
                            <td>{{ $reception->sampled_at ?? 'Nulo' }}</td>
                            <td>{{ $reception->delivered_by ?? 'Nulo' }}</td>
                            <td>{{ $reception->client ?? 'Nulo' }}</td>
                            <td>{{ $reception->received_by ?? 'Nulo' }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $reception->matrix ?? 'Sin matriz' }}
                                </span>
                            </td>
                            <td>{{ $reception->temperature_received ?? 'Nulo' }}</td>
                            <td>{{ $reception->temperature_corrected ?? 'Nulo' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('receptions.show', $reception) }}"
                                        class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-view">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('receptions.edit', $reception) }}"
                                        class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-edit">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <form action="{{ route('receptions.destroy', $reception) }}" method="POST"
                                        class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-delete">
                                            <i class="bi bi-trash3"></i> Eliminar
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
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        /* Centrar contenido de celdas y encabezados */
        #receptionsTable th,
        #receptionsTable td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* Fondo blanco */
        #receptionsTable td {
            background-color: #ffffff !important;
        }

        /* Checkbox */
        #receptionsTable input[type="checkbox"] {
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
        #receptionsTable td:last-child {
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
        #receptionsTable tbody tr {
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

        #receptionsTable tbody tr:hover {
            background-color: #f0f9ff !important;
            transform: scale(1.005);
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.1);
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

        /* Animación para el alert */
        .alert {
            animation: slideDown 0.4s ease-out;
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

        #toast-container>div {
            opacity: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        #toast-container>.toast {
            background-image: none !important;
            padding: 15px 20px 15px 50px;
        }

        #toast-container>.toast:before {
            position: absolute;
            font-family: 'Bootstrap Icons';
            font-size: 24px;
            line-height: 24px;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        #toast-container>.toast-success:before {
            content: "\f26b";
        }

        #toast-container>.toast-error:before {
            content: "\f659";
        }

        #toast-container>.toast-info:before {
            content: "\f431";
        }

        #toast-container>.toast-warning:before {
            content: "\f33a";
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastr -->
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
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif

            // Inicializar DataTable
            const table = $('#receptionsTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 10,
                columnDefs: [{
                    orderable: false,
                    targets: [0, 13] // Checkbox y Acciones no ordenables
                }],
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
                    zeroRecords: "No se encontraron coincidencias"
                },
                dom: "<'row'<'col-sm-12'B>>lfrtip",
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Recepciones_' + new Date().toISOString().slice(0, 10),
                        className: 'd-none',
                        exportOptions: {
                            columns: ':visible:not(:first-child):not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Recepciones de Muestras',
                        className: 'd-none',
                        exportOptions: {
                            columns: ':visible:not(:first-child):not(:last-child)'
                        }
                    }
                ],
                orderCellsTop: true,
                fixedHeader: true,
                initComplete: function() {
                    // Mover el selector de entradas
                    $('.dataTables_length').appendTo('#entriesLengthContainer');

                    // Mover la búsqueda
                    $('.dataTables_filter').appendTo('#searchContainer');

                    // Mover la información
                    $('.dataTables_info').appendTo('#tableInfo');

                    // Mover la paginación
                    $('.dataTables_paginate').appendTo('#tablePagination');
                }
            });

            // Aplicar búsqueda individual por columna
            $('.column-search').on('keyup change', function() {
                const columnIndex = $(this).parent().index();
                table.column(columnIndex).search(this.value).draw();
            });

            // Botones externos del dropdown Exportar
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

            // Seleccionar/Deseleccionar todos los checkboxes
            $('#selectAll').on('click', function() {
                $('.row-select').prop('checked', this.checked);
            });

            $('#receptionsTable').on('change', '.row-select', function() {
                $('#selectAll').prop('checked', $('.row-select:checked').length === $('.row-select')
                    .length);
            });

            // SweetAlert2 para confirmación de eliminación
            $('.form-delete').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Eliminar muestra?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#23C552',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                        toastr.success('Muestra eliminada correctamente', 'Éxito');
                    } else if (result.isDismissed) {
                        toastr.info('Eliminación cancelada', 'Información');
                    }
                });
            });

            // Notificaciones para botones de acción
            $('.btn-view').on('click', function() {
                toastr.info('Cargando detalles de la muestra...', 'Información');
            });

            $('.btn-edit').on('click', function() {
                toastr.info('Abriendo formulario de edición...', 'Información');
            });

            $('.btn-add').on('click', function() {
                toastr.info('Abriendo formulario para nueva muestra...', 'Información');
            });
        });
    </script>
@endpush
