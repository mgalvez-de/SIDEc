@extends('layouts.app')

{{--
    Detalle de un análisis de imagen (API_larvas). No depende del mapeo de clases:
    muestra todas las clases que devolvió el modelo, sean cuales sean.
--}}
@php
    $sectionLabel = \App\Support\Navigation::BIOASSAYS[$section][0] ?? $section;
    $rowLabel = \App\Models\ImageAnalysis::rowLabel($section, $analysis->row_key, $record);
    [$statusText, $statusColor] = $analysis->statusLabel();
    $summary = $analysis->summary ?? [];
    $counts = $summary['counts_by_class'] ?? [];
    $complete = $summary['complete_counts_by_class'] ?? [];
    $instances = $analysis->instances ?? [];
    $hasViewer = $analysis->summary !== null && $analysis->image_width && $analysis->image_height;
    $fixatives = config('larvas_api.fixatives');
    $num = fn ($value, $decimals = 2) => $value === null ? '—' : \App\Support\NumberFormat::format(round((float) $value, $decimals), $decimals);
    $rawPretty = null;
    if ($analysis->raw_response !== null) {
        $decoded = json_decode($analysis->raw_response, true);
        $rawPretty = $decoded !== null
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION)
            : $analysis->raw_response;
    }
    arsort($counts);
@endphp

@section('content')
<div class="container-fluid px-4">
    <x-page-header title="Análisis de imagen" icon="bi-camera"
        subtitle="{{ $sectionLabel }} · Muestra {{ optional($record)->sample }} · {{ $rowLabel }}, réplica {{ $analysis->replica }}, foto {{ $analysis->photo_number }}">
        @if ($record)
            <a href="{{ route("$section.edit", $record) }}#analisis-imagenes" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver a la planilla
            </a>
        @endif
    </x-page-header>

    {{-- ===== Estado ===== --}}
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <span class="badge bg-{{ $statusColor }} fs-6">{{ $statusText }}</span>
        <span class="text-muted small">Registrado por {{ optional($analysis->performer)->name }} el {{ $analysis->created_at->format('d/m/Y H:i:s') }}</span>
    </div>

    @if ($analysis->status === \App\Models\ImageAnalysis::STATUS_FAILED)
        <div class="alert alert-danger">
            <strong><i class="bi bi-x-octagon me-1"></i>El análisis falló.</strong>
            {{ \App\Services\LarvasApi\LarvasApiException::MESSAGES[$analysis->error_code] ?? '' }}
            <div class="small mt-1">
                Código: <code>{{ $analysis->error_code }}</code>
                @if ($analysis->http_status) · HTTP {{ $analysis->http_status }} @endif
                @if ($analysis->round_trip_ms !== null) · {{ $num($analysis->round_trip_ms, 1) }} ms @endif
            </div>
            <div class="small">Detalle técnico: {{ $analysis->error_message }}</div>
        </div>
    @endif

    @if ($analysis->status === \App\Models\ImageAnalysis::STATUS_SUPERSEDED)
        <div class="alert alert-secondary">
            <i class="bi bi-archive me-1"></i>
            {{ $analysis->voidedText() }} · Motivo: {{ $analysis->void_reason }}
            @if ($analysis->supersededBy)
                <div class="small mt-1">
                    La reemplaza la <a href="{{ route("$section.analyses.show", $analysis->supersededBy) }}">foto {{ $analysis->supersededBy->photo_number }}</a>.
                </div>
            @endif
        </div>
    @endif

    @if ($chain)
        <div class="alert alert-light border small">
            <i class="bi bi-clock-history me-1"></i>Esta foto reemplazó a:
            @foreach ($chain as $previous)
                <a href="{{ route("$section.analyses.show", $previous) }}">foto {{ $previous->photo_number }}</a>
                ({{ $previous->void_reason }})@if (! $loop->last), @endif
            @endforeach
        </div>
    @endif

    <div class="row g-4">
        {{-- ===== Imagen con los polígonos ===== --}}
        <div class="col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold"><i class="bi bi-bounding-box me-1"></i>Segmentación</div>
                <div class="card-body">
                    @if ($hasViewer)
                        <div class="d-flex flex-wrap gap-3 align-items-center mb-2 small" id="analysisControls">
                            @foreach ($counts as $label => $count)
                                @if ($count > 0)
                                    <label class="form-check-label d-inline-flex align-items-center gap-1">
                                        <input type="checkbox" class="form-check-input mt-0 js-class-filter" value="{{ $label }}" checked>
                                        <span class="analysis-swatch" data-label="{{ $label }}"></span>
                                        {{ $label }} ({{ $count }})
                                    </label>
                                @endif
                            @endforeach
                            <label class="form-check form-switch mb-0 ms-auto">
                                <input type="checkbox" class="form-check-input" id="highlightBorder">
                                <span class="form-check-label">Resaltar los que tocan el borde</span>
                            </label>
                        </div>

                        <div class="analysis-viewer position-relative border rounded overflow-hidden" id="analysisViewer"
                            data-width="{{ $analysis->image_width }}" data-height="{{ $analysis->image_height }}">
                            <img src="{{ route("$section.analyses.image", $analysis) }}" alt="Fotografía original" class="d-block w-100">
                            <svg class="position-absolute top-0 start-0 w-100 h-100" viewBox="0 0 {{ $analysis->image_width }} {{ $analysis->image_height }}"
                                preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"></svg>
                            <div class="analysis-tooltip position-absolute d-none" role="tooltip"></div>
                        </div>
                        {{-- <p class="small text-muted mt-2 mb-0">
                            Pasa el mouse sobre un individuo para ver su clase, confianza y morfometría.
                            Los que no tienen polígono se dibujan con su caja (línea punteada).
                        </p> --}}
                        <script type="application/json" id="analysisInstances">@json($instances)</script>
                    @elseif ($analysis->image_path)
                        <img src="{{ route("$section.analyses.image", $analysis) }}" alt="Fotografía original" class="d-block w-100 border rounded">
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Conteos y trazabilidad ===== --}}
        <div class="col-xl-4">
            @if ($analysis->summary)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bi bi-list-ol me-1"></i>Conteos por clase</div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Clase</th><th class="text-end">Total</th><th class="text-end" title="Sin contar los que tocan el borde">Completos</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($counts as $label => $count)
                                    <tr class="{{ $count ? '' : 'text-muted' }}">
                                        <td>{{ $label }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                        <td class="text-end">{{ $complete[$label] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td>Total</td>
                                    <td class="text-end">{{ $summary['total_instances'] }}</td>
                                    <td class="text-end">{{ $summary['complete_instances'] }}</td>
                                </tr>
                            </tfoot>
                        </table>
                        <p class="small text-muted px-2 py-2 mb-0">
                            {{ $summary['border_touching_instances'] }} instancias tocan el borde de la imagen.
                        </p>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold"><i class="bi bi-fingerprint me-1"></i>Trazabilidad</div>
                <div class="card-body small">
                    <dl class="row mb-0 analysis-dl">
                        <dt class="col-5">ID del análisis</dt><dd class="col-7"><code>{{ $analysis->uuid }}</code></dd>
                        <dt class="col-5">Imagen original</dt><dd class="col-7">{{ $analysis->image_original_name }}<br>
                            {{ $analysis->image_width ?? '?' }} × {{ $analysis->image_height ?? '?' }} px · {{ $num($analysis->image_size_bytes / 1048576, 2) }} MB · {{ $analysis->image_mime }}</dd>
                        <dt class="col-5">SHA-256 imagen</dt><dd class="col-7"><code class="text-break">{{ $analysis->image_sha256 }}</code></dd>
                        <dt class="col-5">Captura</dt><dd class="col-7">{{ optional($analysis->captured_at)->format('d/m/Y H:i') ?? '—' }} · {{ $fixatives[$analysis->fixative] ?? $analysis->fixative }}</dd>
                        <dt class="col-5">Microscopio</dt><dd class="col-7">{{ optional($analysis->microscope)->label() }}</dd>
                        <dt class="col-5">Cámara</dt><dd class="col-7">{{ optional($analysis->camera)->label() }}</dd>
                        <dt class="col-5">Aumento</dt><dd class="col-7">{{ $analysis->magnification }}</dd>
                        <dt class="col-5">Config. óptica</dt><dd class="col-7">{{ optional($analysis->opticalConfiguration)->label() ?? '—' }}
                            @if ($analysis->um_per_px) · {{ $num($analysis->um_per_px, 4) }} µm/px @endif</dd>
                        @if ($analysis->summary)
                            <dt class="col-5">Modelo</dt><dd class="col-7">{{ $analysis->model_file }}</dd>
                            <dt class="col-5">SHA-256 modelo</dt><dd class="col-7"><code class="text-break">{{ $analysis->model_sha256 }}</code></dd>
                            <dt class="col-5">API</dt><dd class="col-7">v{{ $analysis->api_version }} · request <code>{{ $analysis->api_request_id }}</code></dd>
                            <dt class="col-5">Hora de la API (UTC)</dt><dd class="col-7">{{ $analysis->api_timestamp_utc }}</dd>
                            <dt class="col-5">Parámetros</dt><dd class="col-7">conf {{ $num($analysis->conf, 2) }} · iou {{ $num($analysis->iou, 2) }} · imgsz {{ $analysis->api_imgsz }}</dd>
                            <dt class="col-5">Tiempos</dt><dd class="col-7">inferencia {{ $num($analysis->inference_time_ms, 1) }} ms · total {{ $num($analysis->round_trip_ms, 1) }} ms</dd>
                        @endif
                        @if ($analysis->response_sha256)
                            <dt class="col-5">SHA-256 respuesta</dt><dd class="col-7"><code class="text-break">{{ $analysis->response_sha256 }}</code></dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Instancias y respuesta cruda ===== --}}
    @if ($instances)
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white">
                <a class="fw-bold text-decoration-none" data-bs-toggle="collapse" href="#analysisInstancesTable" role="button" aria-expanded="false">
                    <i class="bi bi-table me-1"></i>Instancias detectadas ({{ count($instances) }})
                </a>
            </div>
            <div class="collapse" id="analysisInstancesTable">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>#</th><th>Clase</th><th class="text-end">Confianza</th><th>Borde</th>
                                <th class="text-end">Área (px)</th><th class="text-end">Perímetro (px)</th><th class="text-end">Circularidad</th>
                                <th class="text-end">Solidez</th><th class="text-end">Razón de aspecto</th><th class="text-end">Eje mayor (px)</th>
                                <th class="text-end">Área (µm²)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instances as $instance)
                                @php $m = $instance['morphometry'] ?? []; @endphp
                                <tr>
                                    <td>{{ $instance['instance_id'] }}</td>
                                    <td>{{ $instance['label'] }}</td>
                                    <td class="text-end">{{ $num($instance['confidence'], 3) }}</td>
                                    <td>{{ $instance['touching_border'] ? 'Sí' : 'No' }}</td>
                                    <td class="text-end">{{ $num($m['area_px'] ?? null, 1) }}</td>
                                    <td class="text-end">{{ $num($m['perimeter_px'] ?? null, 1) }}</td>
                                    <td class="text-end">{{ $num($m['circularity'] ?? null, 3) }}</td>
                                    <td class="text-end">{{ $num($m['solidity'] ?? null, 3) }}</td>
                                    <td class="text-end">{{ $num($m['aspect_ratio'] ?? null, 2) }}</td>
                                    <td class="text-end">{{ $num($m['major_axis_px'] ?? null, 1) }}</td>
                                    <td class="text-end">{{ $num($m['area_um2'] ?? null, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if ($rawPretty !== null)
        <div class="card shadow-sm border-0 mt-4 mb-5">
            <div class="card-header bg-white">
                <a class="fw-bold text-decoration-none" data-bs-toggle="collapse" href="#analysisRaw" role="button" aria-expanded="false">
                    <i class="bi bi-braces me-1"></i>Respuesta API (JSON)
                </a>
                {{-- <span class="small text-muted ms-2">(formateada para lectura; el SHA-256 corresponde a los bytes originales guardados)</span> --}}
            </div>
            <div class="collapse" id="analysisRaw">
                <pre class="small mb-0 p-3 bg-light" style="max-height: 480px; overflow: auto;">{{ $rawPretty }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .analysis-viewer svg polygon, .analysis-viewer svg rect { vector-effect: non-scaling-stroke; stroke-width: 2; fill-opacity: .18; cursor: pointer; }
    .analysis-viewer svg g:hover polygon, .analysis-viewer svg g:hover rect { fill-opacity: .4; stroke-width: 3; }
    .analysis-viewer svg rect { stroke-dasharray: 4 3; }
    .analysis-viewer.highlight-border svg g[data-border="0"] { opacity: .35; }
    .analysis-viewer.highlight-border svg g[data-border="1"] polygon,
    .analysis-viewer.highlight-border svg g[data-border="1"] rect { stroke: #dc3545 !important; stroke-dasharray: 6 4; stroke-width: 3; }
    .analysis-tooltip { background: rgba(33, 37, 41, .92); color: #fff; font-size: .78rem; padding: .4rem .6rem; border-radius: .35rem;
        pointer-events: none; z-index: 5; max-width: 260px; line-height: 1.35; }
    .analysis-swatch { display: inline-block; width: .85rem; height: .85rem; border-radius: .2rem; }
    .analysis-dl dt { font-weight: 600; }
    .analysis-dl dd { margin-bottom: .35rem; }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('js/arbacia-analysis-viewer.js') }}"></script>
@endpush
