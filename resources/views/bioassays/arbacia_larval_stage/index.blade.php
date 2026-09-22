@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-secondary">Bioensayos de Arbacia spatuligera (Estado Larval)</h2>
        <a href="{{ route('arbacia-larval-stage.create') }}" class="btn btn-light text-dark border btn-action">
            <i class="bi bi-plus-circle me-2"></i> Nuevo Bioensayo
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <h3 class="text-secondary mb-2">Historial de Bioensayos</h3>
            <div class="table-responsive table-box">
                <table id="historialTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Fecha Inicio</th>
                            <th scope="col">Analista</th>
                            <th scope="col">Muestra</th>
                            <th scope="col">CE<sub>50</sub></th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bioassays as $bioassay)
                            <tr>
                                <td>{{ $bioassay->id }}</td>
                                <td>{{ $bioassay->bioassay_start ? $bioassay->bioassay_start->format('d-m-Y H:i') : 'N/A' }}</td>
                                <td>{{ $bioassay->analyst ?? 'N/A' }}</td>
                                <td>{{ $bioassay->sample ?? 'N/A' }}</td>
                                <td>{{ $bioassay->ce50 ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('arbacia-larval-stage.edit', $bioassay->id) }}" class="btn btn-sm btn-light text-dark border btn-edit me-1" title="Editar">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="{{ route('arbacia-larval-stage.destroy', $bioassay->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-dark border btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay bioensayos de Arbacia spatuligera (Estado Larval) registrados aún.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $bioassays->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, ¡eliminar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/tisbe.css') }}">
@endpush
