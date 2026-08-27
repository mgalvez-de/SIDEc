@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('storage/images/xd.webp') }}" alt="Logo SIDEc"
            style="height: 80px; display: block; margin: 0 auto 20px auto;">
        <h2 class="mb-4 text-center text-black">ANÁLISIS BIOENSAYO <br> <small>Tisbe longicornis Sus</small></h2>
        <p class="text-center text-black">RT-01.05 | Versión: 01 | Vigencia: 06.10.2025</p>

        <form action="{{ route('tisbe-longicornis-substance.store') }}" method="POST">
            @csrf

            {{-- ================= FORMULARIO PRINCIPAL ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white text-black">Datos Generales</div>
                <div class="card-body p-2">
                    <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th>Muestra</th>
                                <th>Matriz</th>
                                <th>Fecha y hora de inicio de Bioensayo</th>
                                <th>Fecha y hora de término de Bioensayo</th>
                                <th>Analista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" name="sample" required></td>
                                <td><input type="text" class="form-control form-control-sm" name="matrix"></td>
                                <td><input type="text" class="form-control form-control-sm datetimepicker"
                                        name="bioassay_start" placeholder="Seleccione fecha y hora"></td>
                                <td><input type="text" class="form-control form-control-sm datetimepicker"
                                        name="bioassay_end" placeholder="Seleccione fecha y hora"></td>
                                <td><input type="text" class="form-control form-control-sm" name="analyst"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= DATOS PRELIMINARES ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white text-black">Datos Preliminares</div>
                <div class="card-body p-2">
                    <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th>Volumen inóculo inicial (10⁴ cel/ml)</th>
                                <th>Fecha de Cultivo (Stock)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" step="0.01" class="form-control form-control-sm"
                                        name="initial_inoculum"></td>
                                <td><input type="text" class="form-control form-control-sm datepicker"
                                        name="stock_culture_date"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= TABLA PRINCIPAL ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white text-black">Muestras y Lecturas</div>
                <div class="card-body p-2">
                    <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2" style="width: 110px">Concentración / Muestra</th>

                                <th colspan="5">24H</th>
                                <th colspan="5">48H</th>

                                <th rowspan="2" style="width: 110px">Observaciones</th>

                            </tr>
                            <tr>
                                @for ($replica = 1; $replica <= 4; $replica++)
                                    <th>R{{ $replica }}</th>
                                @endfor
                                <th>∑ Tisbe Muerto 24H</th>
                                @for ($replica = 1; $replica <= 4; $replica++)
                                    <th>R{{ $replica }}</th>
                                @endfor
                                <th>∑ Tisbe Muerto 48H</th>

                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 24; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="sample{{ $i }}"></td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="observations{{ $i }}"></td>
                                    @for ($replica = 1; $replica <= 4; $replica++)
                                        <td><input type="text" class="form-control form-control-sm"
                                                name="24H{{ $i }}R{{ $replica }}"></td>
                                    @endfor
                                    @for ($replica = 1; $replica <= 4; $replica++)
                                        <td><input type="text" class="form-control form-control-sm"
                                                name="48H{{ $i }}R{{ $replica }}"></td>
                                    @endfor
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="sum_dead_tisbe_24_{{ $i }}"></td>
                                    <td><input type="text" class="form-control form-control-sm"
                                            name="sum_dead_tisbe_48_{{ $i }}"></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- ================= OBSERVACIONES ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white text-black">Observaciones</div>
                <div class="mb-3">
                    <textarea class="form-control form-control-sm" name="observations" rows="3"></textarea>
                </div>
            </div>

            {{-- ================= V°B° ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white text-black">V°B°</div>
                <div class="card-body p-2">
                    <input type="text" class="form-control form-control-sm" name="VB">
                </div>
            </div>

            {{-- ================= BOTONES ================= --}}
            <div class="text-center d-flex justify-content-center gap-3 mb-5">
                <button type="submit" class="btn btn-success btn-lg">Guardar Bioensayo</button>
                <button type="button" class="btn btn-outline-primary btn-lg" onclick="window.print()">Imprimir</button>
            </div>
        </form>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
@endpush

@push('styles')
    <style>
        table input {
            border: 1px solid #ced4da;
            text-align: center;
            padding: 3px;
            height: 30px;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .table td,
        .table th {
            padding: 0.25rem;
        }

        .table {
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #adb5bd !important;
        }

        .table thead th {
            vertical-align: middle;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body {
                font-size: 11pt;
                line-height: 1.2;
                zoom: 0.85;
            }

            button,
            .btn,
            nav,
            .navbar {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 8px !important;
            }

            .card-header {
                background: #fff !important;
                color: #000 !important;
                font-weight: bold;
                border: 1px solid #000 !important;
                padding: 4px 8px !important;
            }

            .card-body {
                padding: 4px 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".datetimepicker", {
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i",
                locale: "es"
            });
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                locale: "es"
            });

            $('form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Confirmar guardado?',
                    text: "Se registrará el nuevo bioensayo.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) this.submit();
                });
            });
        });
    </script>
@endpush
