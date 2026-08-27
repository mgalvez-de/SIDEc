@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Logo -->
    <img src="{{ asset('storage/images/xd.webp') }}" alt="Logo SIDEc"
        style="height: 80px; display: block; margin: 0 auto 20px auto;">
    <h2 class="mb-4 text-center text-black">ANÁLISIS BIOENSAYO <br> <small>Arbacia spatuligera (Fecundación)</small></h2>
    <p class="text-center text-black">RT-XX.XX | Versión: 01 | Vigencia: XX.XX.XXXX</p>

    <form action="{{ route('arbacia_fertilization.store') }}" method="POST">
        @csrf

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white text-black">Datos Generales</div>
            <div class="card-body p-2">
                <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y hora de inicio de Bioensayo</th>
                            <th>Analista</th>
                            <th>Hora adición esperma</th>
                            <th>Hora adición óvulos</th>
                            <th>Hora término fijación</th>
                            <th>Fecha y hora término conteo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control form-control-sm datetimepicker" name="bioassay_start" placeholder="Seleccione fecha y hora" required></td>
                            <td><input type="text" class="form-control form-control-sm" name="analyst" required></td>
                            <td><input type="text" class="form-control form-control-sm timepicker" name="sperm_addition_time" placeholder="Seleccione hora"></td>
                            <td><input type="text" class="form-control form-control-sm timepicker" name="egg_addition_time" placeholder="Seleccione hora"></td>
                            <td><input type="text" class="form-control form-control-sm timepicker" name="fixation_time_end" placeholder="Seleccione hora"></td>
                            <td><input type="text" class="form-control form-control-sm datetimepicker" name="count_end_datetime" placeholder="Seleccione fecha y hora"></td>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TABLA PRINCIPAL DE MUESTRAS Y RESULTADOS ================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white text-black">Muestras, Concentraciones y Resultados</div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0" style="table-layout: fixed; min-width: 1200px;">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="3">#</th>
                                <th rowspan="3">Muestra</th>
                                <th rowspan="3">Matriz</th>
                                <th rowspan="3">Concentración</th>
                                <th colspan="8">Réplicas</th>
                                <th colspan="2" rowspan="2">Resultados por Réplica</th>
                                <th rowspan="3">CI</th>
                            </tr>
                            <tr>
                                <th colspan="2">Réplica 1</th>
                                <th colspan="2">Réplica 2</th>
                                <th colspan="2">Réplica 3</th>
                                <th colspan="2">Réplica 4</th>
                            </tr>
                            <tr>
                                <th>NF</th><th>Total</th>
                                <th>NF</th><th>Total</th>
                                <th>NF</th><th>Total</th>
                                <th>NF</th><th>Total</th>
                                <th>% Fecundación</th>
                                <th>% Inhibición</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 15; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td><input type="text" class="form-control form-control-sm" name="sample{{ $i }}"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="matrix{{ $i }}"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="concentration{{ $i }}"></td>

                                    {{-- Replicas (NF y Total) --}}
                                    @for ($replica = 1; $replica <= 4; $replica++)
                                        <td><input type="number" class="form-control form-control-sm" name="replica{{ $i }}_{{ $replica }}_nf"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="replica{{ $i }}_{{ $replica }}_total"></td>
                                    @endfor

                                    {{-- Porcentajes --}}
                                    <td><input type="text" class="form-control form-control-sm" name="replica{{ $i }}_fertilization_percentage"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="replica{{ $i }}_inhibition_percentage"></td>

                                    {{-- CI --}}
                                    <td><input type="text" class="form-control form-control-sm" name="ci{{ $i }}"></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= RESULTADO FINAL ================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white text-black">Resultado Final</div>
            <div class="card-body p-2">
                <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                    <thead class="table-light">
                        <tr>
                            <th>Porcentaje de Fecundación General (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="fertilization_percentage"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="text-center d-flex justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg">Guardar Bioensayo</button>
            <a href="{{ route('arbacia_fertilization.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
            <button type="button" class="btn btn-outline-primary btn-lg" onclick="window.print()">Imprimir</button>
        </div>
    </form>
</div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
@endpush

@push('styles')
    {{-- Estilos consistentes con create.blade.php de Tisbe --}}
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
                size: A4 landscape; /* Cambiado a landscape para la tabla ancha */
                margin: 10mm;
            }

            body {
                font-size: 10pt;
                line-height: 1.2;
                zoom: 0.80;
            }

            .btn, nav, .navbar, .btn-secondary {
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

            flatpickr(".timepicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "es"
            });

            $('form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Confirmar guardado?',
                    text: "Se registrará el nuevo bioensayo de Arbacia.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754', // Verde de éxito
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
