{{--
    Estado del bioensayo, común a las 8 planillas. Uso:
    @include('bioassays._status', ['bioassay' => $daphnia_magna, 'section' => 'daphnia-magna'])

    - Muestra quién lo creó, quién lo modificó por última vez y quién lo validó.
    - Si está validado: la planilla queda en solo lectura y aparece "Reabrir" (Supervisor / Jefe de Área).
    - Si no: avisa antes de salir de la página con cambios sin guardar.
--}}
@php $locked = $bioassay->isLocked(); @endphp

<div class="container d-print-none mb-3">
    <p class="small text-muted mb-2">
        <i class="bi bi-clock-history me-1"></i>
        Creado por {{ optional($bioassay->creator)->name ?? 'el sistema' }} el {{ $bioassay->created_at->format('d/m/Y H:i') }}
        @if ($bioassay->editor)
            · Última modificación: {{ $bioassay->editor->name }}, {{ $bioassay->updated_at->format('d/m/Y H:i') }}
        @endif
        @if ($locked)
            · Validado por {{ optional($bioassay->validator)->name ?? '—' }} el {{ $bioassay->validated_at->format('d/m/Y H:i') }}
        @endif
    </p>

    @if ($locked)
        <div class="alert alert-secondary d-flex flex-wrap align-items-center justify-content-between gap-3 mb-0" role="status">
            <span>
                <i class="bi bi-lock-fill me-1"></i>
                <strong>Bioensayo validado: solo lectura.</strong>
                Para corregir un dato, un Supervisor o Jefe de Área debe reabrirlo.
            </span>
            @can('reopen', $bioassay)
                <form action="{{ route('bioassays.reopen', ['tipo' => $section, 'id' => $bioassay->id]) }}" method="POST"
                    data-confirm="¿Reabrir este bioensayo?"
                    data-confirm-text="Se quitará la validación y se podrá modificar. Habrá que validarlo de nuevo."
                    data-confirm-button="Sí, reabrir">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-unlock me-1"></i> Reabrir
                    </button>
                </form>
            @endcan
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // El formulario de la planilla es el que guarda con PUT
        const method = document.querySelector('form input[name="_method"][value="PUT"]');
        if (!method) return;
        const form = method.form;

        @if ($locked)
            form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(function (field) {
                field.disabled = true;
            });
            form.querySelectorAll('button[type="submit"]:not([form])').forEach(function (button) {
                button.classList.add('d-none');
            });
        @else
            // Aviso del navegador al salir con cambios sin guardar
            let unsaved = false;
            form.addEventListener('input', function () { unsaved = true; });
            form.addEventListener('submit', function () { unsaved = false; });
            window.addEventListener('beforeunload', function (event) {
                if (unsaved) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
        @endif
    });
</script>
