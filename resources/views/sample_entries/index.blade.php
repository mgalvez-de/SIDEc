@extends('layouts.app')

@section('content')
    <div class="container-fluid px-5">

<x-page-header title="Ingreso de muestras" icon="bi-journal-plus" />

        <!-- Primera fila: Nuevas / En proceso -->
        <div class="row mb-5">
            <!-- Muestras Nuevas -->
            <div class="col-xxl-6 mb-5">
                <div class="table-card">
                    <div class="card-header-custom mb-3">
                        <h3 class="text-secondary mb-0">Muestras Nuevas (Sin ingresar)</h3>
                    </div>

                    <!-- Barra de controles -->
                    <div
                        class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div id="nuevasLengthContainer"></div>
                            <div id="nuevasSearchContainer" class="flex-grow-1" style="max-width: 300px;"></div>
                        </div>

                    </div>

                    <div class="table-responsive table-box-scroll">
                        <table id="nuevasTable"
                            class="table table-bordered table-striped table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th>Fecha de Recepción</th>
                                    <th>Código Interno</th>
                                    <th>Tipo de Muestra</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($newReceptions as $reception)
                                    <tr>
                                        <td>{{ $reception->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $reception->internal_sample_code }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $reception->matrix }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('sample_entries.create', ['code' => $reception->internal_sample_code]) }}"
                                                class="btn btn-sm btn-light text-dark border btn-action">
                                                <i class="bi bi-box-arrow-in-right"></i> Ingresar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Info y paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div id="nuevasInfo"></div>
                        <div id="nuevasPagination"></div>
                    </div>
                </div>
            </div>

            <!-- Muestras en Proceso -->
            <div class="col-xxl-6 mb-5">
                <div class="table-card">
                    <div class="card-header-custom mb-3">
                        <h3 class="text-secondary mb-0">Muestras en Proceso</h3>
                    </div>

                    <!-- Barra de controles -->
                    <div
                        class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div id="procesoLengthContainer"></div>
                            <div id="procesoSearchContainer" class="flex-grow-1" style="max-width: 300px;"></div>
                        </div>
                    </div>

                    <div class="table-responsive table-box-scroll">
                        <table id="procesoTable"
                            class="table table-bordered table-striped table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"><input type="checkbox" id="selectAllProceso"></th>
                                    <th>ID</th>
                                    <th>Código Interno</th>
                                    <th>Estado</th>
                                    <th style="width: 150px;">Avance</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inProcessEntries as $sampleEntry)
                                    @php
                                        $totalBioassays = $sampleEntry->assignedBioassays()->count();
                                        $validatedBioassays = $sampleEntry->validatedBioassaysCount();
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" class="row-select-proceso" value="{{ $sampleEntry->id }}">
                                        </td>
                                        <td>{{ $sampleEntry->id }}</td>
                                        <td>{{ $sampleEntry->internal_sample_code ?? 'Nulo' }}</td>
                                        <td><span class="badge bg-warning text-dark">Bioensayos Pendientes</span></td>
                                        <td>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $totalBioassays ? $validatedBioassays / $totalBioassays * 100 : 0 }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $validatedBioassays }} de {{ $totalBioassays }}
                                                validados</small>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('sample_entries.show', $sampleEntry) }}"
                                                    class="btn btn-sm btn-light text-dark border btn-view">
                                                    <i class="bi bi-play-circle"></i> Continuar
                                                </a>
                                                <a href="{{ route('sample_entries.edit', $sampleEntry) }}"
                                                    class="btn btn-sm btn-light text-dark border btn-edit">
                                                    <i class="bi bi-pencil-square"></i> Corregir
                                                </a>
                                                @role('Supervisor|Area Manager')
                                                    @unless ($sampleEntry->hasValidatedBioassays())
                                                <form action="{{ route('sample_entries.destroy', $sampleEntry) }}" method="POST"
                                                    class="d-inline form-delete">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-light text-dark border btn-delete">
                                                        <i class="bi bi-trash3"></i> Eliminar
                                                    </button>
                                                </form>
                                                    @endunless
                                                @endrole
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Info y paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div id="procesoInfo"></div>
                        <div id="procesoPagination"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda fila: Historial -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="table-card">
                    <div class="card-header-custom mb-3">
                        <h3 class="text-secondary mb-0">Historial de Muestras Completadas</h3>
                    </div>

                    <!-- Barra de controles -->
                    <div
                        class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div id="historialLengthContainer"></div>
                            <div id="historialSearchContainer" class="flex-grow-1" style="max-width: 400px;"></div>
                        </div>
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
                    </div>

                    <div class="table-responsive table-box">
                        <table id="historialTable"
                            class="table table-bordered table-striped table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"><input type="checkbox" id="selectAllHistorial"></th>
                                    <th>ID</th>
                                    <th>Código Interno</th>
                                    <th>Tipo de Muestra</th>
                                    <th>Analista</th>
                                    <th>Fecha de Recepción</th>
                                    <th>Bioensayos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($completedEntries as $sampleEntry)
                                    <tr>
                                        <td><input type="checkbox" class="row-select-historial" value="{{ $sampleEntry->id }}">
                                        </td>
                                        <td>{{ $sampleEntry->id }}</td>
                                        <td>{{ $sampleEntry->internal_sample_code ?? 'Nulo' }}</td>
                                        <td>{{ $sampleEntry->sample_type ?? 'Nulo' }}</td>
                                        <td>{{ $sampleEntry->analyst ?? 'Nulo' }}</td>
                                        <td>{{ $sampleEntry->received_at ? \Carbon\Carbon::parse($sampleEntry->received_at)->format('d/m/Y H:i') : 'Nulo' }}
                                        </td>
                                        <td>{{ $sampleEntry->assignedBioassays()->keys()->implode(', ') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('sample_entries.show', $sampleEntry) }}"
                                                    class="btn btn-sm btn-light text-dark border btn-view">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Info y paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div id="historialInfo"></div>
                        <div id="historialPagination"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/buttons/buttons.bootstrap5.min.css') }}" rel="stylesheet">

    <style>
        /* Centrar contenido de celdas y encabezados */
        table th,
        table td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        table td {
            background-color: #ffffff !important;
        }

        /* Checkbox */
        input[type="checkbox"] {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        /* Botones hover */
        .btn-action:hover,
        .btn-view:hover,
        .btn-export:hover {
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

        /* Hover en las opciones del dropdown */
        .dropdown-menu .dropdown-item:hover {
            background-color: rgba(29, 78, 216, 0.1);
            color: #1d4ed8 !important;
            transition: all 0.2s ease;
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

        /* Header de tarjetas */
        .card-header-custom {
            padding: 0.75rem 0;
            background: transparent;
            border: none;
            animation: slideDown 0.4s ease-out;
        }

        .card-header-custom h3 {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Contenedor de card completo */
        .table-card {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #ffffff;
            animation: fadeIn 0.5s ease-out;
        }

        /* Cajas de tabla sin borde adicional */
        .table-box-scroll,
        .table-box {
            border: none;
            border-radius: 0;
            padding: 0;
            background-color: transparent;
            animation: fadeIn 0.5s ease-out;
        }

        .table-box-scroll {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Tablas sin colores */
        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ffffff !important;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff !important;
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
        table tbody tr {
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

        table tbody tr:hover {
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

        /* Badge personalizado */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-controls-wrapper {
                flex-direction: column;
                align-items: stretch !important;
            }

            #nuevasSearchContainer,
            #procesoSearchContainer,
            #historialSearchContainer {
                max-width: 100% !important;
            }

        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/jszip/jszip.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Mostrar notificaciones de sesión
                // Configuración común para DataTables
                const commonConfig = {
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Buscar...",
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
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
            };

            // Tabla Nuevas
            const nuevasTable = $('#nuevasTable').DataTable({
                ...commonConfig,
                dom: 'lfrtip',
                initComplete: function () {
                    $('.dataTables_length').eq(0).appendTo('#nuevasLengthContainer');
                    $('.dataTables_filter').eq(0).appendTo('#nuevasSearchContainer');
                    $('.dataTables_info').eq(0).appendTo('#nuevasInfo');
                    $('.dataTables_paginate').eq(0).appendTo('#nuevasPagination');
                }
            });

            // Tabla Proceso
            const procesoTable = $('#procesoTable').DataTable({
                ...commonConfig,
                dom: 'lfrtip',
                columnDefs: [{
                    orderable: false,
                    targets: [0, -1]
                }],
                initComplete: function () {
                    $('.dataTables_length').eq(1).appendTo('#procesoLengthContainer');
                    $('.dataTables_filter').eq(1).appendTo('#procesoSearchContainer');
                    $('.dataTables_info').eq(1).appendTo('#procesoInfo');
                    $('.dataTables_paginate').eq(1).appendTo('#procesoPagination');

                }
            });

            // Tabla Historial
            const historialTable = $('#historialTable').DataTable({
                ...commonConfig,
                dom: "<'row'<'col-sm-12'B>>lfrtip",
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Historial_Muestras_' + new Date().toISOString().slice(0, 10),
                    className: 'd-none',
                    exportOptions: {
                        columns: ':visible:not(:first-child):not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    title: 'Historial de Muestras Completadas',
                    className: 'd-none',
                    exportOptions: {
                        columns: ':visible:not(:first-child):not(:last-child)'
                    }
                }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0, -1]
                }],
                initComplete: function () {
                    $('.dataTables_length').eq(2).appendTo('#historialLengthContainer');
                    $('.dataTables_filter').eq(2).appendTo('#historialSearchContainer');
                    $('.dataTables_info').eq(2).appendTo('#historialInfo');
                    $('.dataTables_paginate').eq(2).appendTo('#historialPagination');
                }
            });

            // Botones de exportación
            $('#exportExcel').on('click', function (e) {
                e.preventDefault();
                historialTable.button(0).trigger();
            });

            $('#exportPrint').on('click', function (e) {
                e.preventDefault();
                historialTable.button(1).trigger();
            });

            // Checkboxes
            $('#selectAllProceso').on('click', function () {
                $('.row-select-proceso').prop('checked', this.checked);
            });

            $('#selectAllHistorial').on('click', function () {
                $('.row-select-historial').prop('checked', this.checked);
            });

            // Confirmación de eliminación
            $('.form-delete').on('submit', function (e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Eliminar muestra?',
                    text: "Irá a la papelera. El Supervisor o el Jefe de Área puede restaurarlo.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b91c1c',
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
                    }
                });
            });

        });
    </script>
@endpush