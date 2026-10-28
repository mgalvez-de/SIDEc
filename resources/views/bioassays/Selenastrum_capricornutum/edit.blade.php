{{-- resources/views/bioassays/selenastrum_capricornutum/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container px-4">
    <!-- Logo -->
    <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc"
         style="height: 80px; display: block; margin: 0 auto 20px auto;">
    
    <!-- Título Principal -->
    <h1 class="mb-2 text-secondary text-center"
        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 500;">
        <span class="d-print-none">Editar</span><span class="d-none d-print-inline">Análisis</span> Bioensayo
    </h1>
    <h2 class="mb-3 text-center" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; font-style: italic; color: #84cc16;">
        Selenastrum capricornutum
    </h2>
    <p class="text-center text-muted mb-4">RT-01.05 | Versión: 03 | Vigencia: 01.10.2025</p>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <strong><i class="bi bi-exclamation-triangle"></i> Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $measurements = $selenastrum_capricornutum->measurements ?? [];
    @endphp

    <form id="bioassayForm" action="{{ route('selenastrum-capricornutum.update', $selenastrum_capricornutum->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ================= DATOS GENERALES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-card-checklist me-2"></i>Datos Generales
            </div>
            <div class="card-body bg-light p-3">
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0 modern-table">
                        <thead>
                            <tr>
                                <th>Muestra</th>
                                <th>Matriz</th>
                                <th>Fecha y hora de inicio</th>
                                <th>Fecha y hora de término</th>
                                <th>Analista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable bg-light" 
                                           name="sample" 
                                           value="{{ old('sample', $selenastrum_capricornutum->sample) }}" 
                                           readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="matrix" 
                                           value="{{ old('matrix', $selenastrum_capricornutum->matrix) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_start" 
                                           value="{{ old('bioassay_start', $selenastrum_capricornutum->bioassay_start) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable datetimepicker" 
                                           name="bioassay_end" 
                                           value="{{ old('bioassay_end', $selenastrum_capricornutum->bioassay_end) }}" 
                                           placeholder="Seleccione fecha y hora">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm navigable" 
                                           name="analyst" 
                                           value="{{ old('analyst', $selenastrum_capricornutum->analyst) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= DATOS PRELIMINARES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-eyedropper me-2"></i>Datos Preliminares
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Volumen inóculo inicial</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="initial_inoculum" 
                                   value="{{ old('initial_inoculum', $selenastrum_capricornutum->initial_inoculum) }}">
                            <span class="input-group-text unit-badge">10⁴ cel/mL</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha de Cultivo (Stock)</label>
                        <input type="text" class="form-control navigable datepicker" 
                               name="stock_culture_date" 
                               value="{{ old('stock_culture_date', $selenastrum_capricornutum->stock_culture_date) }}" 
                               placeholder="Seleccione fecha">
                    </div>
                </div>

                @include('bioassays._duration', ['check' => $selenastrum_capricornutum->durationChecks()['bioassay'], 'start' => 'bioassay_start', 'end' => 'bioassay_end'])
            </div>
        </div>

        {{-- ================= CRECIMIENTO Y PH (CONTROL) ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-graph-up me-2"></i>Crecimiento y pH (Control)
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Réplicas de Control (RC)</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table">
                        <thead>
                            <tr>
                                <th class="table-header-time">24 h</th>
                                <th class="table-header-time">48 h</th>
                                <th class="table-header-time">72 h</th>
                                <th class="table-header-96h">RC1 (96 h)</th>
                                <th class="table-header-96h">RC2 (96 h)</th>
                                <th class="table-header-96h">RC3 (96 h)</th>
                                <th class="table-header-96h">RC4 (96 h)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc24h" 
                                           value="{{ old('rc24h', $selenastrum_capricornutum->rc24h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc48h" 
                                           value="{{ old('rc48h', $selenastrum_capricornutum->rc48h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc72h" 
                                           value="{{ old('rc72h', $selenastrum_capricornutum->rc72h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc196h" 
                                           value="{{ old('rc196h', $selenastrum_capricornutum->rc196h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc296h" 
                                           value="{{ old('rc296h', $selenastrum_capricornutum->rc296h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc396h" 
                                           value="{{ old('rc396h', $selenastrum_capricornutum->rc396h) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                           name="rc496h" 
                                           value="{{ old('rc496h', $selenastrum_capricornutum->rc496h) }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">pH Inicial</label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph_initial" 
                               value="{{ old('ph_initial', $selenastrum_capricornutum->ph_initial) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">pH Final</label>
                        <input type="number" step="0.01" class="form-control navigable" 
                               name="ph_final" 
                               value="{{ old('ph_final', $selenastrum_capricornutum->ph_final) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tasa de Crecimiento Control</label>
                        <input type="number" step="0.0001" class="form-control navigable" 
                               name="control_growth_rate" 
                               value="{{ old('control_growth_rate', $selenastrum_capricornutum->control_growth_rate) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MEDICIONES ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-table me-2"></i>Mediciones
            </div>
            <div class="card-body bg-light p-3">
                <h4 class="mb-3 text-center section-title">
                    <span>Tabla de Mediciones</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 modern-table compact-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 40px;">#</th>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Muestra / Conc.</th>
                                <th colspan="2">pH</th>
                                <th colspan="3" class="table-header-r1">R1</th>
                                <th colspan="3" class="table-header-r2">R2</th>
                                <th colspan="3" class="table-header-r3">R3</th>
                                <th colspan="3" class="table-header-r4">R4</th>
                                <th colspan="4" class="table-header-96h">96 h</th>
                                <th rowspan="2" class="align-middle">Tasa Crec.</th>
                                <th rowspan="2" class="align-middle">% Tasa Crec.</th>
                                <th rowspan="2" class="align-middle">% Inhib.</th>
                                <th rowspan="2" class="align-middle">CE<sub>50</sub></th>
                            </tr>
                            <tr>
                                <th>Ini</th>
                                <th>Fin</th>
                                <th>24 h</th><th>48 h</th><th>72 h</th>
                                <th>24 h</th><th>48 h</th><th>72 h</th>
                                <th>24 h</th><th>48 h</th><th>72 h</th>
                                <th>24 h</th><th>48 h</th><th>72 h</th>
                                <th>R1</th><th>R2</th><th>R3</th><th>R4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    $row = $measurements[$i] ?? [];
                                @endphp
                                <tr>
                                    <td class="fw-semibold table-light">{{ $i }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][sample_or_concentration]" 
                                               value="{{ old('measurements.'.$i.'.sample_or_concentration', $row['sample_or_concentration'] ?? '') }}"
                                               placeholder="Conc.">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ph_initial]" 
                                               value="{{ old('measurements.'.$i.'.ph_initial', $row['ph_initial'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ph_final]" 
                                               value="{{ old('measurements.'.$i.'.ph_final', $row['ph_final'] ?? '') }}">
                                    </td>
                                    {{-- R1: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r1_24h]" 
                                               value="{{ old('measurements.'.$i.'.r1_24h', $row['r1_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r1_48h]" 
                                               value="{{ old('measurements.'.$i.'.r1_48h', $row['r1_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r1_72h]" 
                                               value="{{ old('measurements.'.$i.'.r1_72h', $row['r1_72h'] ?? '') }}">
                                    </td>
                                    {{-- R2: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r2_24h]" 
                                               value="{{ old('measurements.'.$i.'.r2_24h', $row['r2_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r2_48h]" 
                                               value="{{ old('measurements.'.$i.'.r2_48h', $row['r2_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r2_72h]" 
                                               value="{{ old('measurements.'.$i.'.r2_72h', $row['r2_72h'] ?? '') }}">
                                    </td>
                                    {{-- R3: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r3_24h]" 
                                               value="{{ old('measurements.'.$i.'.r3_24h', $row['r3_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r3_48h]" 
                                               value="{{ old('measurements.'.$i.'.r3_48h', $row['r3_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r3_72h]" 
                                               value="{{ old('measurements.'.$i.'.r3_72h', $row['r3_72h'] ?? '') }}">
                                    </td>
                                    {{-- R4: 24h, 48h, 72h --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r4_24h]" 
                                               value="{{ old('measurements.'.$i.'.r4_24h', $row['r4_24h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r4_48h]" 
                                               value="{{ old('measurements.'.$i.'.r4_48h', $row['r4_48h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r4_72h]" 
                                               value="{{ old('measurements.'.$i.'.r4_72h', $row['r4_72h'] ?? '') }}">
                                    </td>
                                    {{-- 96h: R1, R2, R3, R4 --}}
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r196h]" 
                                               value="{{ old('measurements.'.$i.'.r196h', $row['r196h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r296h]" 
                                               value="{{ old('measurements.'.$i.'.r296h', $row['r296h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r396h]" 
                                               value="{{ old('measurements.'.$i.'.r396h', $row['r396h'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][r496h]" 
                                               value="{{ old('measurements.'.$i.'.r496h', $row['r496h'] ?? '') }}">
                                    </td>
                                    {{-- Resultados --}}
                                    <td>
                                        <input type="number" step="0.0001" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][growth_rate]" 
                                               value="{{ old('measurements.'.$i.'.growth_rate', $row['growth_rate'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][percent_growth_rate]" 
                                               value="{{ old('measurements.'.$i.'.percent_growth_rate', $row['percent_growth_rate'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][percent_inhibition]" 
                                               value="{{ old('measurements.'.$i.'.percent_inhibition', $row['percent_inhibition'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm navigable" 
                                               name="measurements[{{ $i }}][ce50]" 
                                               value="{{ old('measurements.'.$i.'.ce50', $row['ce50'] ?? '') }}">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= RESULTADOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-lime-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-bar-chart me-2"></i>Resultados y Observaciones
            </div>
            <div class="card-body bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">EC<sub>50</sub> Detalle</label>
                        <input type="text" class="form-control navigable" 
                               name="ce50_detail" 
                               value="{{ old('ce50_detail', $selenastrum_capricornutum->ce50_detail) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Coeficiente de Variación (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control navigable" 
                                   name="variation_coefficient" 
                                   value="{{ old('variation_coefficient', $selenastrum_capricornutum->variation_coefficient) }}">
                            <span class="input-group-text unit-badge">%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control navigable" name="observations" rows="3" 
                              placeholder="Ingrese observaciones adicionales...">{{ old('observations', $selenastrum_capricornutum->observations) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ================= CRITERIOS ================= --}}
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-secondary-subtle text-dark"
                style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <i class="bi bi-check-circle me-2"></i>Criterios de Aceptabilidad
            </div>
            <div class="card-body bg-light p-3">
                <ul class="criteria-list mb-3">
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Densidad celular inicial: <strong>10⁴ cel/mL</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Tasa de crecimiento del control: <strong>≥ 0.92 día⁻¹</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        Coeficiente de variación del control: <strong>≤ 7%</strong>
                    </li>
                    <li>
                        <i class="bi bi-arrow-right text-success me-2"></i>
                        pH de las muestras: <strong>7.5 a 8.5 unidades</strong>
                    </li>
                </ul>
                <div class="mt-3 pt-3 border-top">
                    <p class="mb-0 text-muted">V°B° _____________________</p>
                </div>
            </div>
        </div>

        {{-- ================= BOTONES ================= --}}
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="bi bi-floppy me-2"></i>Actualizar Bioensayo
            </button>

            @if(auth()->user()->can('validateBioassay', $selenastrum_capricornutum))
                @if($selenastrum_capricornutum->validated_at)
                    <span class="btn btn-outline-success btn-lg px-4 disabled">
                        <i class="bi bi-check-circle me-2"></i>Validado el
                        {{ $selenastrum_capricornutum->validated_at->format('d/m/Y H:i') }}
                    </span>
                @else
                    <button type="submit" form="validateBioassayForm" class="btn btn-warning btn-lg px-4">
                        <i class="bi bi-clipboard-check me-2"></i>Marcar como Validado
                    </button>
                @endif
            @endif

            <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir
            </button>
            @php
                $sampleEntry = $selenastrum_capricornutum->sampleEntry;
            @endphp
            @if($sampleEntry)
                <a href="{{ route('sample_entries.show', $sampleEntry->id) }}" class="btn btn-secondary btn-lg px-4">
                    <i class="bi bi-arrow-left me-2"></i>Volver a Muestra
                </a>
            @else
                <a href="{{ route('sample_entries.index') }}" class="btn btn-secondary btn-lg px-4">
                    <i class="bi bi-x-lg me-2"></i>Cancelar
                </a>
            @endif
        </div>
    </form>

    @if(auth()->user()->can('validateBioassay', $selenastrum_capricornutum) && !$selenastrum_capricornutum->validated_at)
        <form id="validateBioassayForm" data-confirm="¿Marcar como validado?" data-confirm-text="Quedará registrado quién lo validó y cuándo. Esta acción no se puede deshacer." data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, validar" action="{{ route('selenastrum-capricornutum.validate', $selenastrum_capricornutum->id) }}" method="POST" style="display:none">
            @csrf
            @method('PATCH')
        </form>
    @endif
</div>
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
@endpush

@push('styles')
<style>
    /* ============================================
       COLORES PERSONALIZADOS - VERDE LIMA
       ============================================ */
    .bg-lime-subtle {
        background-color: #ecfccb !important;
    }

    /* ============================================
       ESTILOS GENERALES DE TABLAS
       ============================================ */
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    .modern-table thead th {
        background: #e9ecef;
        color: #495057;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 8px 4px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody td {
        padding: 4px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .modern-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .modern-table input {
        border: 1px solid #ced4da;
        text-align: center;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0.75rem;
        padding: 2px 4px;
    }

    .modern-table input:focus {
        border-color: #84cc16;
        box-shadow: 0 0 0 0.15rem rgba(132, 204, 22, 0.25);
    }

    .compact-table {
        width: auto;
        min-width: 100%;
    }

    .compact-table input {
        height: 26px;
        min-width: 55px;
    }
    .modern-table input[step], .modern-table input[readonly] { min-width: 80px; }

    .compact-table td {
        padding: 2px !important;
    }

    /* ============================================
       TÍTULOS DE SECCIÓN
       ============================================ */
    .section-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1.3rem;
        font-weight: 500;
        color: #84cc16;
        position: relative;
        padding: 10px 0;
    }

    .section-title span {
        background: #f8f9fa;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    .section-title::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(to right, transparent, #84cc16, transparent);
    }

    /* ============================================
       HEADERS DE TABLA COLOREADOS
       ============================================ */
    .table-header-time {
        background: #dbeafe !important;
        color: #1e40af;
        font-size: 0.7rem !important;
    }

    .table-header-96h {
        background: #ecfccb !important;
        color: #3f6212;
        font-size: 0.7rem !important;
    }

    .table-header-r1 {
        background: #fef3c7 !important;
        color: #92400e;
        font-size: 0.7rem !important;
    }

    .table-header-r2 {
        background: #dbeafe !important;
        color: #1e40af;
        font-size: 0.7rem !important;
    }

    .table-header-r3 {
        background: #fce7f3 !important;
        color: #9d174d;
        font-size: 0.7rem !important;
    }

    .table-header-r4 {
        background: #e0e7ff !important;
        color: #3730a3;
        font-size: 0.7rem !important;
    }

    /* ============================================
       UNIDADES DE MEDIDA
       ============================================ */
    .unit-badge {
        background: #6c757d;
        color: white;
        font-weight: 500;
        font-size: 0.8rem;
        min-width: 80px;
        justify-content: center;
    }

    /* ============================================
       NAVEGACIÓN CON FLECHAS
       ============================================ */
    .navigable:focus {
        outline: 2px solid #0d6efd;
        outline-offset: 1px;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }

    /* ============================================
       LISTA DE CRITERIOS
       ============================================ */
    .criteria-list {
        list-style: none;
        padding-left: 0;
    }

    .criteria-list li {
        padding: 8px 0;
        font-size: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .criteria-list li:last-child {
        border-bottom: none;
    }

    /* ============================================
       TEMPORIZADOR
       ============================================ */

    @keyframes pulse-grace {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.005); }
    }

    @keyframes pulse-expired {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* ============================================
       BARRA DE PROGRESO
       ============================================ */
    .progress-wrapper { position: relative; }

    .progress-bar {
        transition: width 1s linear, background-color 0.5s ease;
    }

    .progress-bar.bg-lime {
        background: linear-gradient(90deg, #84cc16 0%, #bef264 100%) !important;
    }

    .progress-bar.bg-warning {
        background: linear-gradient(90deg, #ffc107 0%, #ffda6a 100%) !important;
    }

    .progress-bar.bg-orange {
        background: linear-gradient(90deg, #fd7e14 0%, #ffb380 100%) !important;
    }

    .progress-bar.bg-danger {
        background: linear-gradient(90deg, #dc3545 0%, #f17983 100%) !important;
    }

    .progress-text {
        font-size: 0.85rem;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .progress-markers {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
    }

    .marker {
        position: absolute;
        top: -8px; bottom: -8px;
        width: 2px;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .marker-line {
        width: 2px;
        height: 100%;
        border-radius: 2px;
        background: #0d6efd;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
    }

    .marker-line-light {
        background: #adb5bd;
        box-shadow: none;
    }

    .marker-label {
        position: absolute;
        bottom: -20px;
        font-size: 0.7rem;
        color: #6c757d;
    }

    .marker-grace .marker-line {
        background: #fd7e14;
        box-shadow: 0 0 5px rgba(253, 126, 20, 0.5);
    }

    /* ============================================
       IMPRESIÓN
       ============================================ */
    @media print {
        @page { size: A4 landscape; margin: 5mm; }
        body { font-size: 7pt; line-height: 1.1; }
        .btn, button, nav, .navbar, .no-print { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; margin-bottom: 5px !important; break-inside: avoid; }
        .card-header { background: #f0f0f0 !important; color: #000 !important; font-weight: bold; padding: 3px 6px !important; font-size: 9pt !important; }
        .card-body { padding: 4px !important; }
        .modern-table input { border: none !important; background: transparent !important; font-size: 6pt; height: 16px !important; }
        .modern-table th, .modern-table td { padding: 1px !important; font-size: 6pt; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendor/flatpickr/l10n/es.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    flatpickr(".datetimepicker", { enableTime: true, time_24hr: true, dateFormat: "Y-m-d H:i", locale: "es", allowInput: true });
    flatpickr(".datepicker", { dateFormat: "Y-m-d", locale: "es", allowInput: true });

    const navigableElements = document.querySelectorAll('.navigable');
    navigableElements.forEach((element, index) => {
        element.addEventListener('keydown', function(e) {
            let targetIndex = index;
            if (e.key === 'ArrowDown' || (e.key === 'Enter' && !e.shiftKey)) { e.preventDefault(); targetIndex = index + 1; }
            else if (e.key === 'ArrowUp' || (e.key === 'Enter' && e.shiftKey)) { e.preventDefault(); targetIndex = index - 1; }
            else if (e.key === 'ArrowRight' && this.selectionStart === this.value.length) { e.preventDefault(); targetIndex = index + 1; }
            else if (e.key === 'ArrowLeft' && this.selectionStart === 0) { e.preventDefault(); targetIndex = index - 1; }
            if (targetIndex >= 0 && targetIndex < navigableElements.length && targetIndex !== index) {
                navigableElements[targetIndex].focus();
                if (navigableElements[targetIndex].select) navigableElements[targetIndex].select();
            }
        });
    });

    document.getElementById('bioassayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Confirmar actualización?', text: "Se guardarán los cambios del bioensayo de Selenastrum capricornutum.",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#84cc16', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-floppy me-1"></i> Sí, actualizar', cancelButtonText: '<i class="bi bi-x-lg me-1"></i> Cancelar'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush