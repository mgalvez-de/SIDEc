@extends('layouts.app')

@section('content')
<div class="container">
    <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc"
        style="height: 80px; display: block; margin: 0 auto 20px auto;">
    <h2 class="mb-4 text-center text-black">ANÁLISIS BIOENSAYO <br> <small>Arbacia spatuligera (Estado Larval)</small></h2>
    <p class="text-center text-black">RT-01.05 | Versión: 01 | Vigencia: 06.10.2025</p>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('arbacia-larval-stage.store') }}" method="POST"
        data-confirm="¿Crear el bioensayo?" data-confirm-text="Después se abrirá el formulario completo para cargar los datos."
        data-confirm-icon="question" data-confirm-color="#198754" data-confirm-button="Sí, crear">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white text-black">Datos Generales</div>
            <div class="card-body p-2">
                <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                    <thead class="table-light">
                        <tr>
                            <th>Muestra</th>
                            <th>Matriz</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" name="sample" value="{{ old('sample') }}" required></td>
                            <td><input type="text" class="form-control form-control-sm" name="matrix" value="{{ old('matrix') }}"></td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-muted small mt-2 mb-0">Al crear el bioensayo se abrirá el formulario completo para cargar el resto de los datos.</p>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="bi bi-floppy me-2"></i>Crear Bioensayo
            </button>
            <a href="{{ route('arbacia-larval-stage.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="bi bi-x-lg me-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
