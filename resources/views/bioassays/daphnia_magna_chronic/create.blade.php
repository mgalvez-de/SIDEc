{{-- resources/views/bioassays/daphnia_magna/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4 text-center">ANÁLISIS BIOENSAYO AGUDO <br> <small>Daphnia magna</small></h2>
        <p class="text-center text-muted">RT-01.05 | Versión: 03 | Vigencia: 01.09.2023</p>

        <form action="{{ route('daphnia-magna.store') }}" method="POST">
            @csrf

            {{-- ================= DATOS GENERALES ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Datos Generales</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sample">Muestra</label>
                            <input type="text" class="form-control" name="sample" id="sample" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="matrix">Matriz</label>
                            <input type="text" class="form-control" name="matrix" id="matrix">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time">Fecha y hora de inicio de Bioensayo</label>
                            <input type="datetime-local" class="form-control" name="start_time" id="start_time">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time">Fecha y hora de término de Bioensayo</label>
                            <input type="datetime-local" class="form-control" name="end_time" id="end_time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="analyst">Analista</label>
                        <input type="text" class="form-control" name="analyst" id="analyst">
                    </div>
                </div>
            </div>

            {{-- ================= ENSAYO PRELIMINAR ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">Ensayo Preliminar</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="sample_temperature">Temperatura de la muestra (°C)</label>
                            <input type="number" step="0.01" class="form-control" name="sample_temperature"
                                id="sample_temperature">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="reconstituted_water_date">Fecha de agua reconstituida</label>
                            <input type="date" class="form-control" name="reconstituted_water_date"
                                id="reconstituted_water_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sample_ph">pH de la muestra</label>
                            <input type="number" step="0.01" class="form-control" name="sample_ph" id="sample_ph">
                        </div>
                    </div>

                    <h5 class="mt-3">Tabla de Mortalidad (Preliminar)</h5>
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">Concentración</th>
                                <th colspan="3">24 horas</th>
                                <th colspan="3">48 horas</th>
                            </tr>
                            <tr>
                                <th>Replica 1</th>
                                <th>Replica 2</th>
                                <th>∑ muertas</th>
                                <th>Replica 1</th>
                                <th>Replica 2</th>
                                <th>∑ muertas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 8; $i++)
                                <tr>
                                    <td><input type="text" class="form-control"
                                            name="pre_concentration_row{{ $i }}"></td>
                                    <td><input type="number" class="form-control"
                                            name="pre_24h_rep1_row{{ $i }}"></td>
                                    <td><input type="number" class="form-control"
                                            name="pre_24h_rep2_row{{ $i }}"></td>
                                    <td><input type="number" class="form-control"
                                            name="pre_24h_sum_row{{ $i }}"></td>
                                    <td><input type="number" class="form-control"
                                            name="pre_48h_rep1_row{{ $i }}"></td>
                                    <td><input type="number" class="form-control"
                                            name="pre_48h_rep2_row{{ $i }}"></td>
                                    <td><input type="number" class="form-control"
                                            name="pre_48h_sum_row{{ $i }}"></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= ENSAYO DEFINITIVO ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">Ensayo Definitivo</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="def_start_time">Fecha y hora de inicio</label>
                            <input type="datetime-local" class="form-control" name="def_start_time" id="def_start_time">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="def_end_time">Fecha y hora de término</label>
                            <input type="datetime-local" class="form-control" name="def_end_time" id="def_end_time">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="def_temperature">Temperatura de la muestra (°C)</label>
                            <input type="number" step="0.01" class="form-control" name="def_temperature"
                                id="def_temperature">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="def_reconstituted_water_date">Fecha de agua reconstituida</label>
                            <input type="date" class="form-control" name="def_reconstituted_water_date"
                                id="def_reconstituted_water_date">
                        </div>
                    </div>

                    @php
                        $hours = ['24', '48'];
                        $replicas = 4;
                        $concentrations = 5;
                    @endphp

                    @foreach ($hours as $hour)
                        <h5 class="mt-4">{{ $hour }} horas</h5>
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Replicas</th>
                                    <th>Control</th>
                                    <th colspan="{{ $concentrations }}">Concentraciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= $replicas; $i++)
                                    <tr>
                                        <td>Replica {{ $i }}</td>
                                        <td><input type="number" class="form-control"
                                                name="def_{{ $hour }}h_control_rep{{ $i }}"></td>
                                        @for ($c = 1; $c <= $concentrations; $c++)
                                            <td><input type="number" class="form-control"
                                                    name="def_{{ $hour }}h_conc{{ $c }}_rep{{ $i }}">
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                                <tr class="fw-bold">
                                    <td>∑ D. magna muertas</td>
                                    <td><input type="number" class="form-control"
                                            name="def_{{ $hour }}h_control_sum"></td>
                                    @for ($c = 1; $c <= $concentrations; $c++)
                                        <td><input type="number" class="form-control"
                                                name="def_{{ $hour }}h_conc{{ $c }}_sum"></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>

            {{-- ================= RESULTADOS ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">Resultados de Análisis</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="control_immobility">Inmovilidad del control</label>
                        <input type="text" class="form-control" name="control_immobility" id="control_immobility">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cl50_24h">CL<sub>50</sub> 24h</label>
                            <input type="text" class="form-control" name="cl50_24h" id="cl50_24h">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cl50_48h">CL<sub>50</sub> 48h</label>
                            <input type="text" class="form-control" name="cl50_48h" id="cl50_48h">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="observations">Observaciones</label>
                        <textarea class="form-control" name="observations" id="observations" rows="3"></textarea>
                    </div>
                </div>
            </div>

            {{-- ================= CRITERIOS ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-warning">Criterios de Aceptabilidad</div>
                <div class="card-body">
                    <ul>
                        <li>Rango de Aceptabilidad tóxico de referencia CrL<sub>50</sub> (24-48h): 0,60 mg/l a 1,70 mg/l
                        </li>
                        <li>Rango de Aceptabilidad Control Inmovilidad ≤ 10%</li>
                        <li>pH de las muestras de 6 a 9 unidades</li>
                    </ul>
                    <p class="mt-4">V°B° _____________________</p>
                </div>
            </div>

            <div class="text-center d-flex justify-content-center gap-3">
                <button type="submit" class="btn btn-success btn-lg">Guardar Bioensayo</button>
                <button type="button" class="btn btn-outline-primary btn-lg" onclick="window.print()">Imprimir</button>
            </div>

        </form>
    </div>
@endsection
@push('styles')
<style>
/* ===== Estilo para impresión ===== */
@media print {
    /* Configurar a tamaño A4 y un solo bloque */
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-size: 11pt;
        line-height: 1.2;
        zoom: 0.85; /* 🔹 Escala para que quepa en una sola hoja */
    }

    /* Ocultar botones y navegación */
    button, .btn, nav, .navbar {
        display: none !important;
    }

    /* Limpiar cards */
    .card {
        border: none !important;
        box-shadow: none !important;
        margin-bottom: 8px !important;
    }
    .card-header {
        background: #eee !important;
        color: #000 !important;
        font-weight: bold;
        border: 1px solid #000 !important;
        padding: 4px 8px !important;
    }
    .card-body {
        padding: 4px 0 !important;
    }

    /* Tablas compactas */
    table {
        border-collapse: collapse !important;
        width: 100% !important;
        font-size: 10pt !important;
    }
    table th, table td {
        border: 1px solid #000 !important;
        padding: 3px !important;
    }
}
</style>
@endpush
