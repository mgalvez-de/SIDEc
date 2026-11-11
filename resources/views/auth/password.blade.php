@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 560px;">

    <x-page-header title="Cambiar mi contraseña" icon="bi-key"
        subtitle="Usa al menos 8 caracteres y no la compartas con nadie." />

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('account.password.update') }}" autocomplete="off">
                @csrf @method('PUT')

                @foreach ([
                    'current_password'      => ['Contraseña actual', 'current-password'],
                    'password'              => ['Contraseña nueva', 'new-password'],
                    'password_confirmation' => ['Repite la contraseña nueva', 'new-password'],
                ] as $field => [$label, $autocomplete])
                    <div class="mb-3">
                        <label for="{{ $field }}" class="form-label fw-semibold">{{ $label }}</label>
                        <input type="password" id="{{ $field }}" name="{{ $field }}" required
                            autocomplete="{{ $autocomplete }}"
                            class="form-control @error($field) is-invalid @enderror">
                        @error($field)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-ucsc">
                        <i class="bi bi-check-lg me-1"></i> Guardar contraseña
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
