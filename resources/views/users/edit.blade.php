@extends('layouts.app')

@section('content')
    <div class="container" style="max-width: 720px;">
        <h1 class="text-secondary mb-4" style="font-weight: 500;">
            <i class="bi bi-pencil me-2"></i>Editar usuario
        </h1>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST"
                    data-confirm="¿Guardar los cambios?" data-confirm-icon="question"
                    data-confirm-color="#198754" data-confirm-button="Sí, guardar">
                    @csrf
                    @method('PUT')
                    @include('users._form')

                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <a href="{{ route('users.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
