@extends('layouts.app')

@php
    // Estado => [texto de la insignia, color]
    $badges = [
        'nuevas'     => ['Nueva', 'bg-secondary'],
        'en-proceso' => ['En proceso', 'bg-warning text-dark'],
        'validadas'  => ['Validada', 'bg-success'],
    ];
    // Botón principal de cada fila según su estado
    $actions = ['nuevas' => 'Comenzar análisis', 'en-proceso' => 'Continuar análisis', 'validadas' => 'Ver'];
@endphp

@section('content')
<div class="container-fluid px-4">

    <x-page-header title="Bioensayos" icon="bi-eyedropper"
        subtitle="Ensayos de todos los organismos. Filtra por organismo o por estado.">
        <div class="dropdown">
            <button class="btn btn-ucsc dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus-circle me-1"></i> Nuevo bioensayo
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach ($types as $section => [$label, $icon])
                    <li>
                        <a class="dropdown-item" href="{{ route("$section.create") }}">
                            <i class="bi {{ $icon }} me-2"></i>{{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </x-page-header>

    {{-- Filtros: organismo (lista) y estado (botones con la cantidad de cada uno) --}}
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
        <form method="GET" action="{{ route('bioassays.index') }}" id="bioassayFilters" class="filter-type">
            <label for="tipo" class="form-label mb-1">Organismo</label>
            <select id="tipo" name="tipo" class="form-select">
                <option value="">Todos los organismos</option>
                @foreach ($types as $section => [$label])
                    <option value="{{ $section }}" {{ $type === $section ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @if ($status)
                <input type="hidden" name="estado" value="{{ $status }}">
            @endif
            <noscript><button type="submit" class="btn btn-ucsc mt-2">Filtrar</button></noscript>
        </form>

        <ul class="nav nav-pills gap-1" aria-label="Estado">
            <li class="nav-item">
                <a class="nav-link {{ $status ? '' : 'active' }}"
                    href="{{ route('bioassays.index', array_filter(['tipo' => $type])) }}">
                    Todos <span class="count">{{ $counts->sum() }}</span>
                </a>
            </li>
            @foreach ($statuses as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                        href="{{ route('bioassays.index', array_filter(['tipo' => $type, 'estado' => $key])) }}">
                        {{ $label }} <span class="count">{{ $counts[$key] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="table-responsive table-box">
        <table id="bioassaysTable" class="table table-bordered table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Muestra</th>
                    <th>Bioensayo</th>
                    <th>Analista</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row->bioassay->sample ?? 'N/A' }}</td>
                        <td><i class="bi {{ $row->icon }} me-1 text-danger"></i>{{ $row->label }}</td>
                        <td>{{ $row->bioassay->analyst ?? '—' }}</td>
                        <td><span class="badge {{ $badges[$row->status][1] }}">{{ $badges[$row->status][0] }}</span></td>
                        <td data-order="{{ $row->started_at ? $row->started_at->timestamp : 0 }}">
                            {{ $row->started_at ? $row->started_at->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route("{$row->section}.edit", $row->bioassay) }}"
                                class="btn btn-sm btn-light text-dark border btn-action">
                                {{ $actions[$row->status] }}
                            </a>
                            @can('delete', $row->bioassay)
                            <form action="{{ route("{$row->section}.destroy", $row->bioassay) }}" method="POST"
                                class="d-inline" data-confirm="¿Eliminar el bioensayo de la muestra {{ $row->bioassay->sample }}?"
                                data-confirm-button="Sí, eliminar">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-dark border btn-delete">Eliminar</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}">
<style>
    .filter-type { min-width: 280px; }

    .nav-pills .nav-link {
        color: #4b5563;
        border: 1px solid #dee2e6;
        background: #fff;
    }

    .nav-pills .count {
        display: inline-block;
        min-width: 1.6em;
        margin-left: .25rem;
        padding: 0 .4em;
        border-radius: 1em;
        background: rgba(0, 0, 0, .08);
        font-size: .8em;
        font-variant-numeric: tabular-nums;
    }

    .nav-pills .nav-link.active .count { background: rgba(255, 255, 255, .25); }

    .table-box {
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        padding: .5rem;
        background-color: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script>
    // Al cambiar el organismo se aplica el filtro sin apretar ningún botón
    document.getElementById('tipo').addEventListener('change', function () {
        this.form.submit();
    });

    $(function () {
        $('#bioassaysTable').DataTable({
            language: { url: '{{ asset('vendor/datatables/es-ES.json') }}' },
            order: [],                 // se respeta el orden del servidor: en proceso, nuevas, validadas
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    });
</script>
@endpush
