@extends('layouts.app')

@section('content')
<div class="container">
    <x-page-header title="Arbacia spatuligera fecundación" icon="bi-egg-fried" subtitle="Bioensayos">
        <a href="{{ route('arbacia-fertilization.create') }}" class="btn btn-ucsc">
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
                            <th scope="col">Fecha Inicio</th>
                            <th scope="col">Analista</th>
                            <th scope="col">Muestra Principal</th>
                            <th scope="col">Porcentaje Fecundación</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bioassays as $bioassay)
                            <tr>
                                <td>{{ $bioassay->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($bioassay->bioassay_start)->format('d-m-Y H:i') }}</td>
                                <td>{{ $bioassay->analyst }}</td>
                                <td>{{ $bioassay->main_sample }}</td>
                                <td>{{ \App\Support\NumberFormat::format($bioassay->fertilization_percentage) ?? 'N/A' }}%</td>
                                <td>
                                    <a href="{{ route('arbacia-fertilization.show', $bioassay->id) }}" class="btn btn-sm btn-light text-dark border btn-action" title="Ver">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('arbacia-fertilization.edit', $bioassay->id) }}" class="btn btn-sm btn-light text-dark border btn-edit me-1" title="Editar">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="{{ route('arbacia-fertilization.destroy', $bioassay->id) }}" method="POST" class="d-inline form-delete">
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
                                        <span>No hay bioensayos de Arbacia spatuligera registrados aún.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- Enlaces de paginación --}}
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
            // Selecciona todos los formularios de eliminación
            const deleteForms = document.querySelectorAll('.form-delete');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Previene el envío inmediato del formulario

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
                            this.submit(); // Envía el formulario si el usuario confirma
                        }
                    });
                });
            });
        });
    </script>
@endpush

