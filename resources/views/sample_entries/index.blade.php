@extends('layouts.app')

@section('content')
    <div class="container-fluid px-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ingreso de Muestras</li>
            </ol>
        </nav>

        <!-- Encabezado con título -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="text-secondary"
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 700;">
                Ingreso de Muestras
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

        <!-- Primera fila: Nuevas / En proceso -->
        <div class="row mb-5">
            <!-- Muestras Nuevas -->
            <div class="col-md-6 mb-5">
                <div class="table-card">
                    <div class="card-header-custom mb-3">
                        <h3 class="text-secondary mb-0">Muestras Nuevas (Sin ingresar)</h3>
                    </div>

                    <!-- Barra de controles -->
                    <div class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div id="nuevasLengthContainer"></div>
                            <div id="nuevasSearchContainer" class="flex-grow-1" style="max-width: 300px;"></div>
                        </div>
                        <a href="{{ route('sample_entries.create') }}"
                            class="btn btn-sm btn-light text-dark border d-inline-flex align-items-center gap-1 btn-add">
                            <i class="bi bi-plus-lg"></i> Nuevo Ingreso
                        </a>
                    </div>

                    <div class="table-responsive table-box-scroll">
                        <table id="nuevasTable" class="table table-bordered table-striped table-hover align-middle text-center">
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
            <div class="col-md-6 mb-5">
                <div class="table-card">
                    <div class="card-header-custom mb-3">
                        <h3 class="text-secondary mb-0">Muestras en Proceso</h3>
                    </div>

                    <!-- Barra de controles -->
                    <div class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
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
                                    <th style="width: 150px;">Tiempo Restante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sampleEntries as $index => $sampleEntry)
                                    @php
                                        // Datos de prueba para temporizadores (10 segundos para testing)
                                        $tiempoRestante = 10;
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" class="row-select-proceso" value="{{ $sampleEntry->id }}">
                                        </td>
                                        <td>{{ $sampleEntry->id }}</td>
                                        <td>{{ $sampleEntry->internal_sample_code ?? 'Nulo' }}</td>
                                        <td><span class="badge bg-warning text-dark">Bioensayos Pendientes</span></td>
                                        <td>
                                            <div class="timer-container" data-tiempo="{{ $tiempoRestante }}">
                                                <!-- Timer de Barra con borde -->
                                                <div class="timer-view timer-bar-wrapper">
                                                    <div class="timer-bar-container">
                                                        <div class="timer-bar-fill"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Timer Circular con texto -->
                                                <div class="timer-view timer-circle-wrapper">
                                                    <canvas class="timer-canvas" width="20" height="20"></canvas>
                                                    <div class="timer-text"></div>
                                                </div>
                                            </div>
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
                    <div class="table-controls-wrapper d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
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
                                    <th>Título</th>
                                    <th>Código</th>
                                    <th>Versión</th>
                                    <th>Vigencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sampleEntries as $sampleEntry)
                                    <tr>
                                        <td><input type="checkbox" class="row-select-historial"
                                                value="{{ $sampleEntry->id }}">
                                        </td>
                                        <td>{{ $sampleEntry->id }}</td>
                                        <td>{{ $sampleEntry->template->title ?? 'Nulo' }}</td>
                                        <td>{{ $sampleEntry->template->code ?? 'Nulo' }}</td>
                                        <td>{{ $sampleEntry->template->version ?? 'Nulo' }}</td>
                                        <td>{{ $sampleEntry->template->validity ?? 'Nulo' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('sample_entries.show', $sampleEntry) }}"
                                                    class="btn btn-sm btn-light text-dark border btn-view">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                                <form action="{{ route('sample_entries.destroy', $sampleEntry) }}" method="POST"
                                                    class="d-inline form-delete">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-light text-dark border btn-delete">
                                                        <i class="bi bi-trash3"></i> Eliminar
                                                    </button>
                                                </form>
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
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

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

        /* Estilos para Toastr */
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

        /* Badge personalizado */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 600;
        }

        /* Estilos para temporizadores */
        .timer-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 40px;
            position: relative;
        }

        .timer-view {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timer-view.active {
            opacity: 1;
        }

        /* Timer Circular - 20x20px */
        .timer-circle-wrapper {
            width: auto;
            height: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .timer-canvas {
            flex-shrink: 0;
        }

        .timer-text {
            font-size: 14px;
            font-weight: 700;
            color: #000000;
            white-space: nowrap;
            min-width: 45px;
            text-align: left;
        }

        /* Timer de Barra - 20x160px con borde */
        .timer-bar-wrapper {
            width: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timer-bar-container {
            width: 160px;
            height: 20px;
            background-color: #ffffff;
            border: 2px solid #000000;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .timer-bar-fill {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            transition: width 1s linear;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .timer-text-bar {
            font-size: 14px;
            font-weight: 700;
            color: #000000;
            white-space: nowrap;
        }

        /* Animación de pulsación */
        @keyframes pulse {
            0%, 100% {
                transform: translate(-50%, -50%) scale(1);
            }
            50% {
                transform: translate(-50%, -50%) scale(1.05);
            }
        }

        .timer-warning .timer-view.active {
            animation: pulse 1s ease-in-out infinite;
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

            .timer-text,
            .timer-text-bar,
            .timer-digital-text {
                font-size: 11px;
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

            // Mostrar notificaciones de sesión
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
                initComplete: function() {
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
                initComplete: function() {
                    $('.dataTables_length').eq(1).appendTo('#procesoLengthContainer');
                    $('.dataTables_filter').eq(1).appendTo('#procesoSearchContainer');
                    $('.dataTables_info').eq(1).appendTo('#procesoInfo');
                    $('.dataTables_paginate').eq(1).appendTo('#procesoPagination');
                    
                    // Inicializar temporizadores después de que la tabla esté lista
                    initTimers();
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
                initComplete: function() {
                    $('.dataTables_length').eq(2).appendTo('#historialLengthContainer');
                    $('.dataTables_filter').eq(2).appendTo('#historialSearchContainer');
                    $('.dataTables_info').eq(2).appendTo('#historialInfo');
                    $('.dataTables_paginate').eq(2).appendTo('#historialPagination');
                }
            });

            // Sistema de temporizadores
            const timers = [];
            const viewChangeIntervals = [];

            function formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            function getColorByTime(percentage) {
                if (percentage > 50) return '#10b981'; // Verde
                if (percentage > 25) return '#f59e0b'; // Amarillo
                return '#ef4444'; // Rojo
            }

            function drawCircularTimer(canvas, percentage, color) {
                const ctx = canvas.getContext('2d');
                const centerX = canvas.width / 2;
                const centerY = canvas.height / 2;
                const radius = 7;
                
                // Limpiar canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Círculo de fondo (gris claro - siempre completo)
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                ctx.strokeStyle = '#e5e7eb';
                ctx.lineWidth = 4;
                ctx.stroke();
                
                // Círculo de progreso (se desllena de 100% a 0%)
                // Comienza en la parte superior (-90 grados) y va en sentido horario
                const startAngle = -Math.PI / 2;
                const endAngle = startAngle + (2 * Math.PI * percentage / 100);
                
                if (percentage > 0) {
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                    ctx.strokeStyle = color;
                    ctx.lineWidth = 4;
                    ctx.lineCap = 'round';
                    ctx.stroke();
                }
            }

            function updateTimer(container, remainingTime, totalTime) {
                const percentage = (remainingTime / totalTime) * 100;
                const color = getColorByTime(percentage);
                const timeText = formatTime(remainingTime);
                
                // 1. ACTUALIZAR TEXTO DEL TEMPORIZADOR (sin animaciones, solo cambio directo)
                const circleText = container.find('.timer-circle-wrapper .timer-text');
                circleText.text(timeText);
                
                // 2. ACTUALIZAR CÍRCULO ANIMADO (se desllena según el porcentaje)
                const canvas = container.find('.timer-canvas')[0];
                if (canvas) {
                    drawCircularTimer(canvas, percentage, color);
                }
                
                // 3. ACTUALIZAR BARRA DE PROGRESO (se desllena de 100% a 0%)
                const barFill = container.find('.timer-bar-fill');
                
                // Si el timer se acaba de reiniciar, hacer reset instantáneo
                if (remainingTime === totalTime) {
                    barFill.css({
                        'transition': 'none',
                        'width': '100%',
                        'background': `linear-gradient(90deg, ${color}, #3b82f6)`
                    });
                    
                    // Restaurar transición después de 50ms
                    setTimeout(function() {
                        barFill.css('transition', 'width 1s linear');
                    }, 50);
                } else {
                    // Actualizar normalmente
                    barFill.css({
                        'width': percentage + '%',
                        'background': `linear-gradient(90deg, ${color}, #3b82f6)`
                    });
                }
                
                // 4. ANIMACIÓN DE ADVERTENCIA (últimos 5 segundos)
                if (remainingTime <= 5 && remainingTime > 0) {
                    container.addClass('timer-warning');
                } else {
                    container.removeClass('timer-warning');
                }
            }

            function cycleTimerViews(container) {
                const views = container.find('.timer-view');
                let currentIndex = 0;
                
                // Asegurarse de que solo hay 2 vistas
                if (views.length !== 2) {
                    console.error('Se esperan exactamente 2 vistas de timer');
                    return;
                }
                
                // Mostrar la primera vista inmediatamente
                views.eq(currentIndex).addClass('active');
                
                // Cambiar entre las 2 vistas cada 3 segundos
                const cycleInterval = setInterval(function() {
                    // Remover active de la vista actual
                    views.eq(currentIndex).removeClass('active');
                    
                    // Cambiar al siguiente índice (0 o 1)
                    currentIndex = (currentIndex + 1) % 2;
                    
                    // Agregar active a la nueva vista
                    views.eq(currentIndex).addClass('active');
                }, 3000);
                
                viewChangeIntervals.push(cycleInterval);
            }

            function initTimers() {
                $('.timer-container').each(function() {
                    const container = $(this);
                    const totalTime = parseInt(container.data('tiempo'));
                    let remainingTime = totalTime;
                    
                    console.log('Inicializando timer con totalTime:', totalTime);
                    
                    // Iniciar ciclo de vistas
                    cycleTimerViews(container);
                    
                    // Actualizar inmediatamente con el tiempo inicial
                    updateTimer(container, remainingTime, totalTime);
                    
                    // Crear intervalo para actualizar cada segundo (1000ms)
                    const interval = setInterval(function() {
                        remainingTime--;
                        
                        console.log('Tiempo restante:', remainingTime);
                        
                        if (remainingTime < 0) {
                            // Modo debug: reiniciar el timer al llegar a 0
                            console.log('Timer reiniciado');
                            toastr.warning('¡Tiempo agotado! Reiniciando timer...', 'Debug');
                            remainingTime = totalTime;
                        }
                        
                        updateTimer(container, remainingTime, totalTime);
                    }, 1000);
                    
                    timers.push(interval);
                });
            }

            // Limpiar intervalos al salir
            $(window).on('beforeunload', function() {
                timers.forEach(timer => clearInterval(timer));
                viewChangeIntervals.forEach(interval => clearInterval(interval));
            });

            // Botones de exportación
            $('#exportExcel').on('click', function(e) {
                e.preventDefault();
                historialTable.button(0).trigger();
                toastr.success('Exportando a Excel...', 'Éxito');
            });

            $('#exportPrint').on('click', function(e) {
                e.preventDefault();
                historialTable.button(1).trigger();
                toastr.info('Preparando impresión...', 'Información');
            });

            // Checkboxes
            $('#selectAllProceso').on('click', function() {
                $('.row-select-proceso').prop('checked', this.checked);
            });

            $('#selectAllHistorial').on('click', function() {
                $('.row-select-historial').prop('checked', this.checked);
            });

            // Confirmación de eliminación
            $('.form-delete').on('submit', function(e) {
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
            $('.btn-action').on('click', function() {
                toastr.info('Procesando ingreso de muestra...', 'Información');
            });

            $('.btn-view').on('click', function() {
                toastr.info('Cargando detalles de la muestra...', 'Información');
            });

            $('.btn-edit').on('click', function() {
                toastr.info('Abriendo formulario de edición...', 'Información');
            });

            $('.btn-add').on('click', function() {
                toastr.info('Abriendo formulario para nuevo ingreso...', 'Información');
            });
        });
    </script>
@endpush
