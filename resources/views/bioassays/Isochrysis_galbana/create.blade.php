{{-- resources/views/bioassays/isochrysis_galbana/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Logo -->
    <img src="{{ asset('storage/images/xd.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    <h2 class="mb-4 text-center text-black">ANÁLISIS BIOENSAYO <br> <small>Isochrysis galbana</small></h2>
    <p class="text-center text-black">RT-01.05 | Versión: 03 | Vigencia: 01.10.2025</p>

    <form action="{{ route('isochrysis-galbana.store') }}" method="POST">
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
                            <td><input type="text" class="form-control form-control-sm datetimepicker" name="bioassay_start" placeholder="Seleccione fecha y hora"></td>
                            <td><input type="text" class="form-control form-control-sm datetimepicker" name="bioassay_end" placeholder="Seleccione fecha y hora"></td>
                            <td><input type="text" class="form-control form-control-sm" name="analyst"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TABLA 1: DATOS PRELIMINARES ================= --}}
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
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="initial_inoculum_vol"></td>
                            <td><input type="text" class="form-control form-control-sm datepicker" name="stock_culture_date"></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TABLA 2: CRECIMIENTO Y PH ================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white text-black">Crecimiento y pH</div>
            <div class="card-body p-2">
                <table class="table table-bordered text-center align-center mb-0" style="table-layout: fixed;">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">Control</th>
                            <th colspan="3">RC</th>
                            <th>RC1</th>
                            <th>RC2</th>
                            <th>RC3</th>
                            <th>RC4</th>
                            <th>RC5</th>
                            <th>RC6</th>
                            <th colspan="2">pH </th>
                            <th rowspan="2">Tasa de crecimiento control</th>
                        </tr>
                        <tr>
                            <th>24h</th>
                            <th>48h</th>
                            <th>72h</th>
                            <th>96h</th>
                            <th>96h</th>
                            <th>96h</th>
                            <th>96h</th>
                            <th>96h</th>
                            <th>96h</th>
                            <th>Inicial</th>
                            <th>Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Conteo</td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc24h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc48h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc72h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc196h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc296h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc396h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc496h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc596h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="rc696h"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="ph_initial"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="ph_final"></td>
                            <td><input type="number" step="0.0001" class="form-control form-control-sm" name="growth_rate_control"></td>
                        </tr>
                        <tr>
                            <td colspan="12"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TABLA 3: MEDICIONES ================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white text-black">Mediciones</div>
            <div class="card-body p-2">
                <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="1">Conteo</th>
                            <th colspan="2">pH</th>
                            <th>R1</th>
                            <th>R2</th>
                            <th>R3</th>
                            <th rowspan="2">Tasa de Crecimiento</th>
                            <th rowspan="2">% de Tasa de Crecimiento</th>
                            <th rowspan="2">% de Tasa de inhibición</th>
                            <th rowspan="2">EC<sub>50</sub></th>

                        </tr>
                        <tr>
                            <th>Muestra o concentración</th>
                            <th>Inicial</th>
                            <th>Final</th>
                            <th>96h</th>
                            <th>96h</th>
                            <th>96h</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 10; $i++)
                            <tr>
                                <td>{{ $i }}</td>
                                @foreach (['sample_or_concentration','ph_initial','ph_final','r196h','r296h','r396h','growth_rate','growth_rate_percent','inhibition_percent','ec50'] as $field)
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="measurements[{{ $i }}][{{ $field }}]"></td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
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
    <!-- Flatpickr con tema corporativo -->
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
    vertical-align: middle; /* aseguro que queden centrados en rowspan/colspan */
}
.table td, .table th { padding: 0.25rem; }
.table { border-collapse: collapse; }
.table th, .table td { border: 1px solid #adb5bd !important; }
.table thead th {
    vertical-align: middle;
}



/* Impresión */
@media print {
    @page { size: A4 portrait; margin: 10mm; }
    body { font-size: 11pt; line-height: 1.2; zoom: 0.85; }
    button, .btn, nav, .navbar { display: none !important; }
    .card { border: none !important; box-shadow: none !important; margin-bottom: 8px !important; }
    .card-header { background: #fff !important; color: #000 !important; font-weight: bold; border: 1px solid #000 !important; padding: 4px 8px !important; }
    .card-body { padding: 4px 0 !important; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    flatpickr(".datetimepicker", { enableTime: true, time_24hr: true, dateFormat: "Y-m-d H:i", locale: "es" });
    flatpickr(".datepicker", { dateFormat: "Y-m-d", locale: "es" });

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
