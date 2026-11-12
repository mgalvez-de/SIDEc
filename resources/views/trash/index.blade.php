@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <x-page-header title="Papelera" icon="bi-trash3"
        subtitle="Lo que se elimina en el sistema queda aquí y se puede restaurar." />

    <div class="table-responsive table-box">
        <table id="trashTable" class="table table-bordered table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Muestra</th>
                    <th>Tipo</th>
                    <th>Eliminado el</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row->sample }}</td>
                        <td>{{ $row->label }}</td>
                        <td data-order="{{ $row->deleted_at->timestamp }}">{{ $row->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('trash.restore', ['tipo' => $row->type, 'id' => $row->id]) }}" method="POST"
                                class="d-inline" data-confirm="¿Restaurar {{ $row->label }} de la muestra {{ $row->sample }}?"
                                data-confirm-icon="question" data-confirm-button="Sí, restaurar">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light text-dark border btn-action">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                </button>
                            </form>
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
    $(function () {
        $('#trashTable').DataTable({
            language: { url: '{{ asset('vendor/datatables/es-ES.json') }}', emptyTable: 'La papelera está vacía' },
            order: [],
            pageLength: 25,
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    });
</script>
@endpush
