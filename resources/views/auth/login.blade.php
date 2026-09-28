@extends('layouts.app')

@section('content')
    <style>
        /* Contenedor cuadrado invisible (define el borde) */
        .outer-box {
            position: relative;
            width: 400px;
            /* 10 pasos × 40px */
            height: 400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
        }

        /* Imagen centrada dentro del borde */
        .outer-box img {
            width: 80%;
            height: auto;
            border-radius: 10px;
            display: block;
        }

        /* Cuadrado principal */
        .moving-square,
        .trail-square {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 6px;
            will-change: transform, opacity;
        }

        .moving-square {
            background-color: red;
            z-index: 40;
            transition: none;
        }

        /* Estelas (precreadas y reusadas) */
        .trail-square {
            z-index: 20;
            pointer-events: none;
            transition: opacity 0.18s linear;
        }

        .logo-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            font-size: 1.3rem;
            color: #333;
            margin-top: 20px;
            line-height: 1.3;
        }

        /* 🔥 NUEVO: centra todo el contenido verticalmente 🔥 */
        .login-wrapper {
            /* 3rem = padding py-4 del <main> del layout; con 100vh sobraba y aparecía la barra de scroll */
            min-height: calc(100vh - 3rem);
            /* ocupa toda la pantalla */
            display: flex;
            align-items: center;
            /* centra verticalmente */
            justify-content: center;
            /* centra horizontalmente */

        }
    </style>

    <div class="container login-wrapper">
        <div class="row justify-content-center align-items-center w-100 g-4">
            <div class="col-12 col-lg-6 mb-3 mb-lg-0 text-center">
                <div class="outer-box row" id="animatedBox">
                    <img src="{{ asset('img/logo.crea.webp') }}" alt="Logo SIDEc">
                    <h3 class="logo-title mt-1">
                        Sistema de Información<br>Departamento de Ecotoxicología
                    </h3>

                    <div class="moving-square" aria-hidden="true"></div>
                </div>
            </div>

            <div class="col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4">
                <div class="card shadow">
                    <div class="card-header">Iniciar sesión</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Credencial</label>
                                <input id="email" type="email"
                                    class="rounded-pill form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input id="password" type="password"
                                    class="rounded-pill form-control @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>

                            <div class="row mb-0">
                                <div class="d-flex flex-wrap justify-content-center gap-3 align-items-center">
                                    <button type="submit" class="btn btn-danger">Entrar</button>
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link p-0" href="{{ route('password.request') }}">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const box = document.getElementById('animatedBox');
            const square = box.querySelector('.moving-square');

            const boxSize = 400;
            const squareSize = 40;
            const stepsPerSide = 10;
            const step = squareSize;
            const delay = 30;
            const trailLength = 20;
            const color = 'red';
            const offset = -200;

            const positions = [];
            for (let i = 0; i < stepsPerSide; i++) positions.push({
                x: offset + i * step,
                y: offset
            });
            for (let i = 0; i < stepsPerSide; i++) positions.push({
                x: offset + boxSize,
                y: offset + i * step
            });
            for (let i = 0; i < stepsPerSide; i++) positions.push({
                x: offset + boxSize - i * step,
                y: offset + boxSize
            });
            for (let i = 0; i < stepsPerSide; i++) positions.push({
                x: offset,
                y: offset + boxSize - i * step
            });

            const trails = [];
            const history = [];

            for (let i = 0; i < trailLength; i++) {
                const t = document.createElement('div');
                t.className = 'trail-square';
                t.style.backgroundColor = color;
                t.style.transform = `translate(${offset}px, ${offset}px)`;
                t.style.opacity = '0';
                box.appendChild(t);
                trails.push(t);
                history.push({
                    x: offset,
                    y: offset
                });
            }

            let index = 0;

            function animate() {
                const pos = positions[index];
                square.style.transform = `translate(${pos.x}px, ${pos.y}px)`;

                history.unshift({
                    x: pos.x,
                    y: pos.y
                });
                if (history.length > trailLength) history.length = trailLength;

                for (let i = 0; i < trails.length; i++) {
                    const p = history[i] || history[history.length - 1];
                    const t = trails[i];
                    t.style.transform = `translate(${p.x}px, ${p.y}px)`;
                    t.style.opacity = ((trailLength - i) / trailLength).toFixed(2);
                }

                index = (index + 1) % positions.length;
                setTimeout(animate, delay);
            }

            animate();
        });
    </script>
@endsection
