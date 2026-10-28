@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h1 class="text-secondary m-0" style="font-weight: 500;">
                <i class="bi bi-people me-2"></i>Usuarios
            </h1>
            <a href="{{ route('users.create') }}" class="btn btn-danger">
                <i class="bi bi-person-plus me-1"></i> Nuevo usuario
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="{{ $user->active ? '' : 'text-muted' }}">
                                <td>
                                    {{ $user->name }}
                                    @if ($user->is(auth()->user()))
                                        <span class="badge bg-secondary ms-1">Tú</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roleLabel() }}</td>
                                <td>
                                    @if ($user->active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Desactivado</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        @unless ($user->is(auth()->user()))
                                            <form action="{{ route('users.toggle-active', $user) }}" method="POST"
                                                @if ($user->active)
                                                    data-confirm="¿Desactivar a {{ $user->name }}?"
                                                    data-confirm-text="Ya no podrá iniciar sesión. Sus registros se conservan."
                                                    data-confirm-button="Sí, desactivar"
                                                @else
                                                    data-confirm="¿Activar a {{ $user->name }}?"
                                                    data-confirm-text="Podrá volver a iniciar sesión."
                                                    data-confirm-icon="question" data-confirm-color="#198754"
                                                    data-confirm-button="Sí, activar"
                                                @endif
                                                >
                                                @csrf
                                                @method('PATCH')
                                                @if ($user->active)
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-person-x"></i> Desactivar
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-person-check"></i> Activar
                                                    </button>
                                                @endif
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
