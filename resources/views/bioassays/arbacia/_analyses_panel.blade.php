{{--
    Análisis de imágenes con API_larvas, común a las 2 planillas de Arbacia. Uso:
    @include('bioassays.arbacia._analyses_panel', ['bioassay' => $arbacia_larval_stage, 'section' => 'arbacia-larval-stage'])

    Subir varias fotos de una fila y réplica (el navegador las envía de a una), reemplazarlas o anularlas
    y ver el detalle. Cada réplica acepta fotos hasta llegar a la meta de individuos. En las planillas con
    larva_class, el servidor escribe los conteos de cada réplica en la planilla y aquí esas celdas quedan
    de solo lectura.
--}}
@php
    $panelConfig = \App\Models\ImageAnalysis::SECTIONS[$section];
    $sheetLinked = isset($panelConfig['larva_class']);
    $replicaCounts = app(\App\Services\ArbaciaReplicaCounts::class);
    $replicaTotals = $replicaCounts->totals($bioassay);
    $target = $replicaCounts->target();
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

    // Equipo, aumento, fijador y configuración óptica de la última foto, si no se vuelve de un error
    $prefill = session()->hasOldInput() ? null : $analyses->sortByDesc('id')->first();
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
                        @if ($sheetLinked)
                            <div class="form-text" id="an_replica_status" aria-live="polite"></div>
                        @endif
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
                        <label class="form-label small fw-bold mb-1" for="an_image">Fotografías (JPG, PNG o BMP; máx. 20 MB cada una)</label>
                        <input type="file" name="image" id="an_image" multiple accept=".jpg,.jpeg,.png,.bmp,image/jpeg,image/png,image/bmp"
                            class="form-control form-control-sm @error('image') is-invalid @enderror" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold mb-1" for="an_fixative">Fijador</label>
                        <select name="fixative" id="an_fixative" class="form-select form-select-sm @error('fixative') is-invalid @enderror" required>
                            <option value="">Elegir…</option>
                            @foreach ($fixatives as $value => $label)
                                <option value="{{ $value }}" {{ (old('fixative', optional($prefill)->fixative) === $value) ? 'selected' : '' }}>{{ $label }}</option>
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
                                <option value="{{ $equipment->id }}" {{ ((int) old('microscope_id', optional($prefill)->microscope_id) === $equipment->id) ? 'selected' : '' }}>{{ $equipment->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold mb-1" for="an_camera">Cámara</label>
                        <select name="camera_id" id="an_camera" class="form-select form-select-sm @error('camera_id') is-invalid @enderror" required>
                            @foreach ($cameras as $equipment)
                                <option value="{{ $equipment->id }}" {{ ((int) old('camera_id', optional($prefill)->camera_id) === $equipment->id) ? 'selected' : '' }}>{{ $equipment->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold mb-1" for="an_magnification">Aumento</label>
                        <input type="text" name="magnification" id="an_magnification" list="an_magnifications" maxlength="20"
                            value="{{ old('magnification', optional($prefill)->magnification) }}" placeholder="ej. 10x"
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
                                    <option value="{{ $optical->id }}" {{ ((int) old('optical_configuration_id', optional($prefill)->optical_configuration_id) === $optical->id) ? 'selected' : '' }}>{{ $optical->label() }}</option>
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
                    <button type="submit" class="btn btn-success px-4" id="an_submit">
                        <i class="bi bi-cpu me-2"></i>Analizar
                    </button>
                </div>

                {{-- Avance de la cola de fotos (lo llena el script del panel) --}}
                <div class="alert alert-danger py-2 small mt-3 mb-0 d-none" id="an_errors" role="alert"></div>
                <div class="mt-3 d-none" id="an_queue">
                    <div class="fw-bold small mb-1" id="an_queue_title"></div>
                    <ul class="list-group small" id="an_queue_list"></ul>
                    <div class="mt-2" id="an_queue_actions"></div>
                </div>
                <div class="alert alert-info py-2 small mt-3 mb-0 d-none" id="an_notice" role="status"></div>
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
                                            @if ($sheetLinked)
                                                @php $replicaTotal = $replicaCounts->replica($replicaTotals, $analysis->row_key, (int) $analysis->replica); @endphp
                                                <div>Larva/Total: {{ $replicaTotal['larva'] }} / {{ $replicaTotal['total'] }}</div>
                                                <div class="fw-normal">Avance: {{ $replicaTotal['total'] }} de {{ $target }} individuos</div>
                                            @endif
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

@push('styles')
<style>
    /* Celdas de la planilla calculadas desde las fotos (solo lectura) */
    #bioassayForm input.ia-cell { background-color: #e7f1ff; color: #084298; font-weight: 600; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sheetLinked = @json($sheetLinked);
    const target = @json($target);
    const totals = @json((object) $replicaTotals); // "fila|réplica" → conteos de las fotos vigentes

    const key = (row, replica) => `${row}|${replica}`;
    const photosText = (n) => n === 1 ? '1 foto' : `${n} fotos`;
    const replicaOf = (row, replica) => totals[key(row, replica)]
        || { row_key: row, replica: Number(replica), larva: 0, total: 0, photos: 0, complete: false };
    const replicaText = (t) => `Réplica: ${t.total} / ${target} individuos (${photosText(t.photos)})`;

    // ===== Celdas de la planilla =====
    // Las réplicas con fotos vigentes muestran los conteos de la IA y no se editan a mano.
    // notify: dispara 'input' para que el script de la planilla recalcule % y promedios.
    function syncCells(t, notify) {
        const prefix = t.row_key === 'control' ? 'control' : `rows[${t.row_key}]`;
        ['larva', 'total'].forEach((field) => {
            const input = document.querySelector(`#bioassayForm input[name="${prefix}[r${t.replica}_${field}]"]`);
            if (!input) return;

            if (t.photos > 0) {
                input.readOnly = true;
                input.classList.add('ia-cell');
                input.title = `Calculado por IA desde ${photosText(t.photos)}. Para cambiarlo, reemplaza o anula una foto.`;
                input.value = t[field];
                if (notify) input.dispatchEvent(new Event('input', { bubbles: true }));
            } else {
                input.readOnly = false;
                input.classList.remove('ia-cell');
                input.removeAttribute('title');
            }
        });
    }

    if (sheetLinked) {
        Object.values(totals).forEach((t) => syncCells(t, false));
    }

    // Cambios del usuario en la planilla sin guardar (los eventos que dispara este script no cuentan)
    let sheetDirty = false;
    const sheetForm = document.getElementById('bioassayForm');
    if (sheetForm) {
        sheetForm.addEventListener('input', (e) => { if (e.isTrusted) sheetDirty = true; });
    }

    // ===== Subida de varias fotos =====
    const form = document.getElementById('analysisUploadForm');
    if (!form) return;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const fileInput = document.getElementById('an_image');
    const rowSelect = document.getElementById('an_row');
    const replicaSelect = document.getElementById('an_replica');
    const submit = document.getElementById('an_submit');
    const selectionStatus = document.getElementById('an_replica_status');
    const errorsBox = document.getElementById('an_errors');
    const queueBox = document.getElementById('an_queue');
    const queueTitle = document.getElementById('an_queue_title');
    const queueList = document.getElementById('an_queue_list');
    const queueActions = document.getElementById('an_queue_actions');
    const notice = document.getElementById('an_notice');

    let queue = [];
    let queueRow = null;
    let queueReplica = null;
    let running = false;
    let recorded = false;  // el servidor registró al menos una foto (analizada o fallida)
    let warning = null;    // última advertencia del servidor (calibración)

    function showSelection() {
        let complete = false;
        if (sheetLinked) {
            const t = replicaOf(rowSelect.value, replicaSelect.value);
            complete = t.complete;
            selectionStatus.textContent = replicaText(t) + (complete ? ' · completa' : '');
            selectionStatus.classList.toggle('text-success', complete);
        }
        submit.disabled = running || complete;
    }

    function setBusy(busy) {
        running = busy;
        [rowSelect, replicaSelect, fileInput].forEach((el) => { el.disabled = busy; });
        showSelection();
    }

    function showQueueTitle() {
        const label = `${rowSelect.querySelector(`option[value="${queueRow}"]`).textContent} · R${queueReplica}`;
        queueTitle.textContent = sheetLinked ? `${label} — ${replicaText(replicaOf(queueRow, queueReplica))}` : label;
    }

    function showMessages(box, messages) {
        box.replaceChildren(...messages.map((text) => Object.assign(document.createElement('div'), { textContent: text })));
        box.classList.toggle('d-none', messages.length === 0);
    }

    const STATE_CLASSES = { pending: 'text-muted', running: 'text-primary', done: 'text-success', failed: 'text-danger', skipped: 'text-secondary' };

    function setState(item, state, text, url) {
        item.state = state;
        item.stateEl.className = STATE_CLASSES[state];
        item.stateEl.textContent = text;
        if (url) {
            const link = Object.assign(document.createElement('a'), { href: url, target: '_blank', textContent: 'Ver', className: 'ms-2' });
            item.stateEl.append(link);
        }
    }

    function newItem(file) {
        const li = Object.assign(document.createElement('li'), { className: 'list-group-item d-flex justify-content-between gap-2' });
        const name = Object.assign(document.createElement('span'), { className: 'text-break', textContent: file.name });
        const stateEl = document.createElement('span');
        li.append(name, stateEl);
        const item = { file, li, stateEl, state: null };
        setState(item, 'pending', 'En cola');

        return item;
    }

    const pending = () => queue.filter((item) => item.state === 'pending');

    // reason: 'complete' (réplica completa) o 'file' (foto repetida o archivo no válido)
    function skip(item, reason, text) {
        item.skipReason = reason;
        setState(item, 'skipped', `No enviada: ${text}`);
    }

    function skipPending() {
        pending().forEach((item) => skip(item, 'complete', 'réplica completa'));
    }

    // Envía una foto. Devuelve false si hay que detener la cola.
    async function send(item) {
        setState(item, 'running', 'Analizando…');

        const data = new FormData(form); // los selects deshabilitados no viajan: fila y réplica van aparte
        data.set('row_key', queueRow);
        data.set('replica', queueReplica);
        data.set('image', item.file, item.file.name);

        let response;
        let body = null;
        try {
            response = await fetch(form.action, {
                method: 'POST',
                body: data,
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            body = await response.json().catch(() => null);
        } catch (error) {
            setState(item, 'failed', 'Falló: no hubo respuesta del servidor.');
            return false;
        }

        if (response.status === 422) {
            const errors = (body && body.errors) || {};
            const fields = Object.keys(errors);

            // Error solo de este archivo (repetida, formato, tamaño): se salta y la cola sigue
            if (fields.length === 1 && fields[0] === 'image') {
                skip(item, 'file', errors.image.join(' '));
                return true;
            }

            showMessages(errorsBox, Object.values(errors).flat().concat(fields.length ? [] : [(body && body.message) || 'Datos no válidos.']));
            if (errors.replica) {
                skip(item, 'complete', 'réplica completa');
                skipPending();
            } else {
                setState(item, 'pending', 'En cola (revisa los datos del formulario)');
            }
            return false;
        }

        if (!response.ok || !body || !body.analysis) {
            const reasons = { 403: 'sin permiso para modificar este bioensayo', 419: 'la sesión expiró; recarga la página' };
            setState(item, 'failed', `Falló: ${reasons[response.status] || `error del servidor (HTTP ${response.status})`}.`);
            return false;
        }

        recorded = true;
        const analysis = body.analysis;
        if (body.replica) {
            totals[key(queueRow, queueReplica)] = body.replica;
            syncCells(body.replica, true);
            showQueueTitle();
            showSelection();
        }
        if (body.warning) {
            warning = body.warning;
            showMessages(notice, [warning]);
        }

        if (analysis.status !== 'completed') {
            setState(item, 'failed', `Falló: ${analysis.error_message} (${analysis.error_code}). Quedó registrada.`, analysis.url);
            return false;
        }

        const counts = analysis.larva !== null ? `${analysis.larva} / ${analysis.total}` : `${analysis.total} instancias`;
        setState(item, 'done', `Analizada: ${counts}`, analysis.url);

        return true;
    }

    async function run() {
        setBusy(true);
        queueActions.replaceChildren();

        for (const item of pending()) {
            if (sheetLinked && replicaOf(queueRow, queueReplica).complete) {
                skipPending();
                break;
            }
            if (!(await send(item))) break;
        }

        setBusy(false);

        if (pending().length > 0) {
            const resume = Object.assign(document.createElement('button'), {
                type: 'button', className: 'btn btn-sm btn-outline-primary', textContent: 'Continuar con las pendientes',
            });
            resume.addEventListener('click', () => { showMessages(errorsBox, []); run(); });
            queueActions.append(resume);
        } else {
            finish();
        }
    }

    function reloadToPanel() {
        location.hash = 'analisis-imagenes';
        location.reload();
    }

    // "Analizadas: X. No enviadas: Y (réplica completa: a; foto repetida o archivo no válido: b). Fallidas: z." sin las partes en cero
    function queueSummary() {
        const count = (test) => queue.filter(test).length;
        const done = count((item) => item.state === 'done');
        const failedCount = count((item) => item.state === 'failed');
        const complete = count((item) => item.state === 'skipped' && item.skipReason === 'complete');
        const invalid = count((item) => item.state === 'skipped' && item.skipReason === 'file');

        const reasons = [];
        if (complete) reasons.push(`réplica completa: ${complete}`);
        if (invalid) reasons.push(`foto repetida o archivo no válido: ${invalid}`);

        const parts = [];
        if (done) parts.push(`Analizadas: ${done}.`);
        if (complete + invalid) parts.push(`No enviadas: ${complete + invalid} (${reasons.join('; ')}).`);
        if (failedCount) parts.push(`Fallidas: ${failedCount}.`);

        return parts.join(' ');
    }

    // Se recarga solo si todas las fotos quedaron analizadas y la planilla no tiene cambios sin guardar.
    // Si no, la lista de la cola queda a la vista con un resumen.
    function finish() {
        if (queue.every((item) => item.state === 'done') && !sheetDirty) {
            reloadToPanel();
            return;
        }

        const messages = (warning ? [warning] : []).concat(queueSummary());
        if (recorded && sheetDirty) messages.push('Guarda la planilla para ver las fotos nuevas en la tabla.');
        showMessages(notice, messages);

        if (recorded && !sheetDirty) {
            const reload = Object.assign(document.createElement('button'), {
                type: 'button', className: 'btn btn-sm btn-outline-primary mt-2', textContent: 'Recargar para ver las fotos en la tabla',
            });
            reload.addEventListener('click', reloadToPanel);
            notice.append(reload);
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (running || fileInput.files.length === 0) return;

        queueRow = rowSelect.value;
        queueReplica = replicaSelect.value;
        queue = Array.from(fileInput.files).map(newItem);
        recorded = false;
        warning = null;
        fileInput.value = '';

        showMessages(errorsBox, []);
        showMessages(notice, []);
        queueList.replaceChildren(...queue.map((item) => item.li));
        queueBox.classList.remove('d-none');
        showQueueTitle();
        run();
    });

    rowSelect.addEventListener('change', showSelection);
    replicaSelect.addEventListener('change', showSelection);
    showSelection();
});
</script>
@endpush
