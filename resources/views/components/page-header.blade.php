{{--
    Encabezado común de las páginas. Uso:
    <x-page-header title="Recepciones" icon="bi-box-arrow-in-down" subtitle="Texto opcional">
        (botones opcionales, ej.: "Nuevo")
    </x-page-header>
--}}
@props(['title', 'icon' => null, 'subtitle' => null])

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title">
            @if ($icon)
                <i class="bi {{ $icon }}"></i>
            @endif
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if (trim($slot) !== '')
        <div class="d-flex flex-wrap gap-2">{{ $slot }}</div>
    @endif
</div>
