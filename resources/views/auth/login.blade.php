@extends('layouts.app')

@section('content')
    <style>
        /* Colores institucionales: rojo UCSC y azul/turquesa del logo CREA */
        :root {
            --ucsc-red: #D12421;
            --ucsc-red-dark: #9E1B19;
            --crea-blue: #4A7BA6;
            --crea-teal: #3A9C97;
        }

        /* Franja roja superior, detrás de la tarjeta */
        .login-band {
            position: fixed;
            inset: 0 0 auto 0;
            height: 42vh;
            background: linear-gradient(135deg, var(--ucsc-red) 0%, var(--ucsc-red-dark) 100%);
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            /* 3rem = padding py-4 del <main> del layout */
            min-height: calc(100vh - 3rem);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            width: 100%;
            max-width: 920px;
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
            animation: login-in 0.5s ease-out;
        }

        @keyframes login-in {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Panel izquierdo: identidad */
        .login-brand {
            position: relative;
            background: #fff;
            padding: 3rem 2.5rem 7rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .login-brand img {
            width: 100%;
            max-width: 340px;
            margin: 0 auto 2rem;
        }

        .login-brand .system-name {
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: var(--ucsc-red);
            margin: 0;
        }

        .login-brand .system-desc {
            color: #4b5563;
            font-size: 1.05rem;
            line-height: 1.4;
            margin: 0.25rem 0 0;
        }

        .login-brand .system-place {
            color: #9ca3af;
            font-size: 0.85rem;
            margin-top: 0.75rem;
        }

        /* Ondas decorativas (tema marino, colores del logo) */
        .login-waves {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 90px;
        }

        /* Panel derecho: formulario */
        .login-form {
            background: #f9fafb;
            border-left: 1px solid #eef0f3;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .login-form .form-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .login-form .input-group-text {
            background: #fff;
            color: #9ca3af;
        }

        .login-form .form-control:focus {
            border-color: var(--ucsc-red);
            box-shadow: 0 0 0 0.2rem rgba(209, 36, 33, 0.15);
        }

        .btn-login {
            background: var(--ucsc-red);
            border-color: var(--ucsc-red);
            color: #fff;
            font-weight: 600;
            padding: 0.65rem;
        }

        .btn-login:hover,
        .btn-login:focus {
            background: var(--ucsc-red-dark);
            border-color: var(--ucsc-red-dark);
            color: #fff;
        }

        .login-footer {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        /* En celular: el panel del logo queda como encabezado compacto */
        @media (max-width: 767.98px) {
            .login-brand {
                padding: 2rem 1.5rem 4.5rem;
            }

            .login-brand img {
                max-width: 240px;
                margin-bottom: 1rem;
            }

            .login-brand .system-name {
                font-size: 1.8rem;
            }

            .login-waves {
                height: 60px;
            }

            .login-form {
                border-left: 0;
                padding: 2rem 1.5rem;
            }
        }
    </style>

    <div class="login-band" aria-hidden="true"></div>

    <div class="login-wrapper">
        <div class="card login-card">
            <div class="row g-0">
                {{-- Identidad del sistema --}}
                <div class="col-md-6 login-brand">
                    <img src="{{ asset('img/logo.crea.webp') }}" alt="CREA - Universidad Católica de la Santísima Concepción">
                    <h1 class="system-name">SIDEc</h1>
                    <p class="system-desc">Sistema de Información del<br>Departamento de Ecotoxicología</p>
                    <p class="system-place">Laboratorio de Bioensayos · Sede Talcahuano</p>

                    <svg class="login-waves" viewBox="0 0 600 90" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M0 40 C 100 10, 200 70, 300 40 S 500 10, 600 40 L 600 90 L 0 90 Z"
                            fill="var(--crea-blue)" opacity="0.15" />
                        <path d="M0 55 C 120 30, 220 85, 320 55 S 520 30, 600 55 L 600 90 L 0 90 Z"
                            fill="var(--crea-teal)" opacity="0.2" />
                        <path d="M0 70 C 140 55, 240 95, 340 70 S 540 55, 600 70 L 600 90 L 0 90 Z"
                            fill="var(--crea-blue)" opacity="0.3" />
                    </svg>
                </div>

                {{-- Formulario --}}
                <div class="col-md-6 login-form">
                    <h2>Iniciar sesión</h2>
                    <p class="text-muted mb-4">Ingresa con tu cuenta del laboratorio.</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required autocomplete="email" autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required autocomplete="current-password">
                                {{-- Mostrar / ocultar la contraseña --}}
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword"
                                    aria-label="Mostrar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login w-100 mt-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <p class="login-footer">
            Centro Regional de Estudios Ambientales (CREA)<br>
            Universidad Católica de la Santísima Concepción
        </p>
    </div>

    <script>
        // Botón del ojo: cambia el campo entre texto visible y oculto
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            this.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
            this.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    </script>
@endsection
