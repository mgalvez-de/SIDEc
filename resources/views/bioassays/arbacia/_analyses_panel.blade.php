{{--
    Análisis de imágenes con API_larvas, común a las 2 planillas de Arbacia. Uso:
    @include('bioassays.arbacia._analyses_panel', ['bioassay' => $arbacia_larval_stage, 'section' => 'arbacia-larval-stage'])

    Subir fotos por fila y réplica (cualquier cantidad), reemplazarlas o anularlas y ver el detalle.
    Los resultados no se copian a la planilla: los conteos se siguen ingresando a mano.
--}}
@php
    $panelConfig = \App\Models\ImageAnalysis::SECTIONS[$section];
    $rowOrder = array_flip($panelConfig['rows']);
    $analyses = \App\Models\ImageAnalysis::forBioassay($bioassay)
        ->with(['performer', 'voider', 'microscope', 'camera', 'supersededBy'])
        ->get()
        ->sortBy(fn ($a) => sprintf('%03d-%02d-%05d', $rowOrder[$a->row_key] ?? 999, $a->replica, $a->photo_number))
        ->values();
    $byReplica = $analyses->groupBy(fn ($a) => $a->row_key . '|' . $a->replica);

    $canAnalyze = auth()->user()->can('update', $bioassay);
    $microscopes = \App\Models\Equipment::active()->ofType('microscope')->orderBy('code')->get();
    $cameras = \App\Models\Equipment::active()->ofType('camera')->orderBy('code')->get();
    $opticals = \App\Models\OpticalConfiguration::active()->orderBy('code')->get();
    $fixatives = config('larvas_api.fixatives');
    $now = now()->format('Y-m-d H:i');
@endphp

<div class="card mb-4 shadow-sm border-0 d-print-none" id="analisis-imagenes" style="border-radius: 10px;">
    <div class="card-header bg-pink-subtle text-dark d-flex flex-wrap justify-content-between align-items-center gap-2"
        style="font-size: 1.3rem; font-weight: 500; border-top-left-radius: 10px; border-top-right-radius: 10px;">
        <span><i class="bi bi-camera me-2"></i>Análisis de imágenes (IA)</span>
    </div>
    <div class="card-body bg-light p-3">
        <p class="small text-muted mb-3">
            Cada foto que se analiza con API_larvas y queda registrada con su huella SHA-256, el equipo usado y la versión del modelo.
        </p>

        {{-- ===== Subir una foto ===== --}}
        @if (! $canAnalyze)
            <div class="alert alert-secondary py-2 mb-3">
                <i class="bi bi-lock-fill me-1"></i>Bioensayo validado: no se pueden agregar, reemplazar ni anular fotos.
            </div>
        @elseif ($microscopes->isEmpty() || $cameras->isEmpty())
            <div class="alert alert-warning py-2 mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Para analizar fotos hay que registrar al menos un microscopio y una cámara.
                Pídeselo al Supervisor o al Jefe de Área (comando <code>php artisan equipment:register</code>).
            </div>
        @else
            <form method="POST" enctype="multipart/form-data" action="{{ route("$section.analyses.store", $bioassay) }}"
                class="border rounded bg-white p-3 mb-3" id="analysisUploadForm">
                @csrf
                <div class="row g-2">
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold mb-1" for="an_row">Fila</label>
                        <select name="row_key" id="an_row" class="form-select form-select-sm @error('row_key') is-invalid @enderror" required>
                            @foreach ($panelConfig['rows'] as $rowKey)
                                <option value="{{ $rowKey }}" {{ (old('row_key') === $rowKey) ? 'selected' : '' }}>{{ \App\Models\ImageAnalysis::rowLabel($section, $rowKey, $bioassay) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-1">
                        <label class="form-label small fw-bold mb-1" for="an_replica">Réplica</label>
                        <select name="replica" id="an_replica" class="form-select form-select-sm @error('replica') is-invalid @enderror" required>
                            @for ($r = 1; $r <= $panelConfig['replicas']; $r++)
                                <option value="{{ $r }}" {{ ((int) old('replica', 1) === $r) ? 'selected' : '' }}>R{{ $r }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label small fw-bold mb-1" for="an_image">Fotografía (JPG, PNG o BMP; máx. 20 MB)</label>
                        <input type="file" name="image" id="an_image" accept=".jpg,.jpeg,.png,.bmp,image/jpeg,image/png,image/bmp"
                            class="form-control form-control-sm @error('image') is-invalid @enderror" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold mb-1" for="an_fixative">Fijador</label>
                        <select name="fixative" id="an_fixative" class="form-select form-select-sm @error('fixative') is-invalid @enderror" required>
                            <option value="">Elegir…</option>
                            @foreach ($fixatives as $value => $label)
                                <option value="{{ $value }}" {{ (old('fixative') === $value) ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold mb-1" for="an_captured">Fecha de captura</label>
                        <input type="text" name="captured_at" id="an_captured" value="{{ old('captured_at', $now) }}"
                            class="form-control form-control-sm datetimepicker @error('captured_at') is-invalid @enderror">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold mb-1" for="an_microscope">Microscopio</label>
                        <select name="microscope_id" id="an_microscope" class="form-select form-select-sm @error('microscope_id') is-invalid @enderror" required>
                            @foreach ($microscopes as $equipment)
                                <option value="{{ $equipment->id }}" {{ ((int) old('microscope_id') === $equipment->id) ? 'selected' : '' }}>{{ $equipment->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold mb-1" for="an_camera">Cámara</label>
                        <select name="camera_id" id="an_camera" class="form-select form-select-sm @error('camera_id') is-invalid @enderror" required>
                            @foreach ($cameras as $equipment)
                                <option value="{{ $equipment->id }}" {{ ((int) old('camera_id') === $equipment->id) ? 'selected' : '' }}>{{ $equipment->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold mb-1" for="an_magnification">Aumento</label>
                        <input type="text" name="magnification" id="an_magnification" list="an_magnifications" maxlength="20"
                            value="{{ old('magnification') }}" placeholder="ej. 10x"
                            class="form-control form-control-sm @error('magnification') is-invalid @enderror" required>
                        <datalist id="an_magnifications">
                            <option value="4x"><option value="10x"><option value="20x"><option value="40x">
                        </datalist>
                    </div>
                    @if ($opticals->isNotEmpty())
                        <div class="col-6 col-md-2">
                            <label class="form-label small fw-bold mb-1" for="an_optical">Config. óptica</label>
                            <select name="optical_configuration_id" id="an_optical" class="form-select form-select-sm @error('optical_configuration_id') is-invalid @enderror">
                                <option value="">Sin calibración</option>
                                @foreach ($opticals as $optical)
                                    <option value="{{ $optical->id }}" {{ ((int) old('optical_configuration_id') === $optical->id) ? 'selected' : '' }}>{{ $optical->label() }}</option>
                                @endforeach
                            </select>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="requested_um" value="1" id="an_um" {{ old('requested_um') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="an_um">Medir en µm</label>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-cpu me-2"></i>Analizar foto
                    </button>
                </div>
            </form>
        @endif

        {{-- ===== Fotos registradas ===== --}}
        @if ($analyses->isEmpty())
            <p class="text-muted mb-0"><i class="bi bi-images me-1"></i>Aún no hay fotos analizadas en este bioensayo.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Fila · réplica</th>
                            <th class="text-center">Foto</th>
                            <th>Estado</th>
                            <th>Detecciones (todas las clases)</th>
                            <th>Equipo y fijador</th>
                            <th>Analista</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($byReplica as $group)
                            @php $current = $group->filter->isCurrent()->count(); @endphp
                            @foreach ($group as $analysis)
                                @php [$statusText, $statusColor] = $analysis->statusLabel(); @endphp
                                <tr class="{{ $analysis->isCurrent() ? '' : 'text-muted' }}">
                                    @if ($loop->first)
                                        <td rowspan="{{ $group->count() }}" class="fw-bold">
                                            {{ \App\Models\ImageAnalysis::rowLabel($section, $analysis->row_key, $bioassay) }} · R{{ $analysis->replica }}
                                            <div class="fw-normal text-muted">{{ $current }} {{ $current === 1 ? 'foto vigente' : 'fotos vigentes' }}</div>
                                        </td>
                                    @endif
                                    <td class="text-center">{{ $analysis->photo_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
                                        @if ($analysis->status === \App\Models\ImageAnalysis::STATUS_FAILED)
                                            <div class="text-danger mt-1"><code>{{ $analysis->error_code }}</code></div>
                                        @endif
                                        @if ($analysis->status === \App\Models\ImageAnalysis::STATUS_SUPERSEDED)
                                            <div class="mt-1">
                                                <i class="bi bi-chat-left-text me-1"></i>{{ $analysis->voidedText() }} · Motivo: {{ $analysis->void_reason }}
                                                @if ($analysis->supersededBy)
                                                    <span class="d-block">→ reemplazada por la foto {{ $analysis->supersededBy->photo_number }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($analysis->summary)
                                            <strong>{{ $analysis->summary['total_instances'] }}</strong> instancias
                                            ({{ $analysis->summary['border_touching_instances'] }} en el borde)
                                            <div class="text-muted">
                                                @forelse (array_slice($analysis->detectedClasses(), 0, 5, true) as $label => $count)
                                                    {{ $label }}: {{ $count }}@if (! $loop->last) · @endif
                                                @empty
                                                    Sin detecciones
                                                @endforelse
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        {{ optional($analysis->microscope)->code }} / {{ optional($analysis->camera)->code }} · {{ $analysis->magnification }}
                                        <div class="text-muted">{{ $fixatives[$analysis->fixative] ?? $analysis->fixative }}</div>
                                    </td>
                                    <td>
                                        {{ optional($analysis->performer)->name }}
                                        <div class="text-muted">{{ $analysis->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route("$section.analyses.show", $analysis) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Ver
                                        </a>
                                        @if ($canAnalyze && $analysis->canBeReplaced())
                                            <details class="d-inline-block text-start align-top ms-1">
                                                <summary class="btn btn-sm btn-outline-secondary">Reemplazar</summary>
                                                <form method="POST" enctype="multipart/form-data" class="mt-2 p-2 border rounded bg-light" style="min-width: 260px;"
                                                    action="{{ route("$section.analyses.supersede", $analysis) }}">
                                                    @csrf
                                                    {{-- Mismo equipo y fijador que la foto original --}}
                                                    <input type="hidden" name="microscope_id" value="{{ $analysis->microscope_id }}">
                                                    <input type="hidden" name="camera_id" value="{{ $analysis->camera_id }}">
                                                    <input type="hidden" name="magnification" value="{{ $analysis->magnification }}">
                                                    <input type="hidden" name="fixative" value="{{ $analysis->fixative }}">
                                                    <input type="hidden" name="captured_at" value="{{ $now }}">
                                                    @if ($analysis->optical_configuration_id)
                                                        <input type="hidden" name="optical_configuration_id" value="{{ $analysis->optical_configuration_id }}">
                                                        <input type="hidden" name="requested_um" value="{{ $analysis->requested_um ? 1 : 0 }}">
                                                    @endif
                                                    <input type="file" name="image" required class="form-control form-control-sm mb-2"
                                                        accept=".jpg,.jpeg,.png,.bmp,image/jpeg,image/png,image/bmp">
                                                    <input type="text" name="reason" required minlength="5" maxlength="1000"
                                                        class="form-control form-control-sm mb-2" placeholder="Motivo (obligatorio)">
                                                    <button type="submit" class="btn btn-sm btn-warning w-100">Analizar y reemplazar</button>
                                                </form>
                                            </details>
                                            <details class="d-inline-block text-start align-top ms-1">
                                                <summary class="btn btn-sm btn-outline-danger">Anular</summary>
                                                <form method="POST" class="mt-2 p-2 border rounded bg-light" style="min-width: 240px;"
                                                    action="{{ route("$section.analyses.void", $analysis) }}"
                                                    data-confirm="¿Anular esta foto?"
                                                    data-confirm-text="Dejará de contar para la réplica. No se borra: queda registrada con su motivo."
                                                    data-confirm-button="Sí, anular">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="reason" required minlength="5" maxlength="1000"
                                                        class="form-control form-control-sm mb-2" placeholder="Motivo (obligatorio)">
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">Anular foto</button>
                                                </form>
                                            </details>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
