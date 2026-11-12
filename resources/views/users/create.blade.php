@extends('layouts.app')

@section('content')
    <div class="container" style="max-width: 720px;">
        <h1 class="text-secondary mb-4" style="font-weight: 500;">
            <i class="bi bi-person-plus me-2"></i>Nuevo usuario
        </h1>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST"
                    data-confirm="¿Crear el usuario?" data-confirm-icon="question"
                    data-confirm-color="#198754" data-confirm-button="Sí, crear">
                    @csrf
                    @include('users._form', ['user' => null])

                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <a href="{{ route('users.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Crear usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
