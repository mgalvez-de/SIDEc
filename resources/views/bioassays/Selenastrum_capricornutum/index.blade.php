@extends('layouts.app')

@section('content')
<div class="container">

    <x-page-header title="Selenastrum capricornutum" icon="bi-flower3" subtitle="Bioensayos">
        <a href="{{ route('selenastrum-capricornutum.create') }}" class="btn btn-ucsc">
            <i class="bi bi-plus-circle me-1"></i> Nuevo bioensayo
        </a>
    </x-page-header>

    <div class="row mb-4">
        <!-- Tabla Muestras Nuevas -->
        <div class="col-md-6 mb-4">
            <h3 class="text-secondary mb-2">Muestras Nuevas</h3>
            <div class="table-responsive table-box-scroll">
                <table id="nuevasTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Muestra</th>
                            <th>Analista</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bioassays as $sample)
                            <tr>
                                <td>{{ $sample->id }}</td>
                                <td>{{ $sample->sample ?? 'N/A' }}</td>
                                <td>{{ $sample->analyst ?? 'N/A' }}</td>
                                <td>{{ $sample->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('selenastrum-capricornutum.create') }}"
                                       class="btn btn-sm btn-light text-dark border btn-action">
                                       Comenzar Análisis
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay nuevas muestras.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabla Muestras en Proceso -->
        <div class="col-md-6">
            <h3 class="text-secondary mb-2">Muestras en Proceso</h3>
            <div class="table-responsive table-box-scroll">
                <table id="procesoTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Muestra</th>
                            <th>Analista</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bioassays as $sample)
                            <tr>
                                <td>{{ $sample->id }}</td>
                                <td>{{ $sample->sample ?? 'N/A' }}</td>
                                <td>{{ $sample->analyst ?? 'N/A' }}</td>
                                <td>{{ $sample->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('selenastrum-capricornutum.edit', $sample) }}"
                                       class="btn btn-sm btn-light text-dark border btn-action">
                                       Continuar Análisis
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay muestras en proceso.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla Historial de Muestras Enviadas -->
    <div class="row mt-10">
        <div class="col-12">
            <h3 class="text-secondary mb-2">Historial de Muestras Enviadas</h3>
            <div class="table-responsive table-box">
                <table id="historialTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>ID</th>
                            <th>Muestra</th>
                            <th>Analista</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bioassays as $sample)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-select" value="{{ $sample->id }}">
                                </td>
                                <td>{{ $sample->id }}</td>
                                <td>{{ $sample->sample ?? 'N/A' }}</td>
                                <td>{{ $sample->analyst ?? 'N/A' }}</td>
                                <td>{{ $sample->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('selenastrum-capricornutum.edit', $sample) }}"
                                       class="btn btn-sm btn-light text-dark border btn-edit me-1">
                                       Editar
                                    </a>
                                    <form action="{{ route('selenastrum-capricornutum.destroy', $sample) }}" method="POST" class="d-inline form-delete" data-confirm="¿Eliminar este bioensayo?" data-confirm-text="Esta acción no se puede deshacer." data-confirm-button="Sí, eliminar">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-dark border btn-delete">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay historial de muestras.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
@endpush
