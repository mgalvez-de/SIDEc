@extends('layouts.app')

@section('content')
<div class="container">
    <x-page-header title="Daphnia magna (crónico)" icon="bi-droplet-half" subtitle="Bioensayos">
        <a href="{{ route('daphnia-magna-chronic.create') }}" class="btn btn-ucsc">
            <i class="bi bi-plus-circle me-1"></i> Nuevo bioensayo
        </a>
    </x-page-header>

    <div class="row">
        <div class="col-12">
            <h3 class="text-secondary mb-2">Historial de Bioensayos</h3>
            <div class="table-responsive table-box">
                <table id="historialTable" class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Muestra</th>
                            <th scope="col">Analista</th>
                            <th scope="col">Fecha Creación</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bioassays as $bioassay)
                            <tr>
                                <td>{{ $bioassay->id }}</td>
                                <td>{{ $bioassay->sample ?? 'N/A' }}</td>
                                <td>{{ $bioassay->analyst ?? 'N/A' }}</td>
                                <td>{{ $bioassay->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('daphnia-magna-chronic.edit', $bioassay) }}" class="btn btn-sm btn-light text-dark border btn-edit me-1" title="Editar">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="{{ route('daphnia-magna-chronic.destroy', $bioassay) }}" method="POST" class="d-inline form-delete">
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
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        <span>No hay bioensayos de Daphnia magna (Crónico) registrados aún.</span>
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

