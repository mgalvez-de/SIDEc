<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@php
    use App\Support\Navigation;

    // El menú, la barra superior y el pie solo se muestran con la sesión iniciada (no en el login)
    $showMenu = auth()->check();
    $breadcrumb = $showMenu ? Navigation::breadcrumb(Route::currentRouteName()) : [];
    $inBioassays = Navigation::inBioassays();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Título de la pestaña según la página, ej.: "Recepciones › Editar · SIDEc" --}}
    <title>{{ Navigation::pageTitle(Route::currentRouteName()) }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- SweetAlert va aquí arriba para que esté listo antes que los scripts de cualquier página --}}
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        :root {
            --ucsc-red: #D12421;
            --ucsc-red-dark: #9E1B19;
            --sidebar-width: 250px;
        }

        /* ===== Barra superior ===== */
        .app-navbar {
            background: var(--ucsc-red);
        }

        .dropdown-menu .logout-link:hover,
        .dropdown-menu .logout-link:focus {
            color: var(--ucsc-red) !important;
        }

        #sidebarToggle {
            background-color: transparent;
            border: 1px solid #fff;
            color: #fff;
            transition: all 0.2s ease-in-out;
        }

        #sidebarToggle:hover,
        #sidebarToggle:focus {
            background-color: #fff;
            color: var(--ucsc-red);
        }

        /* ===== Menú lateral ===== */
        #sidebar {
            position: fixed;
            top: 0;
            left: calc(-1 * var(--sidebar-width));
            width: var(--sidebar-width);
            height: 100%;
            overflow-y: auto;
            background: #fff;
            transition: left 0.3s ease;
            z-index: 1051;
            border-right: 1px solid #e5e7eb;
        }

        #sidebar.active {
            left: 0;
        }

        /* La X para cerrar va dentro del título rojo */
        #sidebarClose {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            font-size: 1.1rem;
            color: #fff;
        }

        #sidebar .sidebar-title {
            position: relative;
            background: var(--ucsc-red);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            color: #111827;
            text-decoration: none;
            padding: 10px 16px;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover {
            background-color: var(--ucsc-red);
            color: #fff !important;
            transform: translateX(4px);
        }

        /* Sección en la que está el usuario */
        #sidebar .nav-link.active {
            background-color: #fdecec;
            color: var(--ucsc-red) !important;
            font-weight: 700;
            box-shadow: inset 4px 0 0 var(--ucsc-red);
        }

        /* Lista de bioensayos (se despliega hacia abajo) */
        #sidebar .submenu .nav-link {
            font-size: 0.875rem;
            padding: 7px 12px;
        }

        #sidebar .submenu-toggle .chevron {
            transition: transform 0.2s ease;
        }

        #sidebar .submenu-toggle[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        #sidebarOverlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1050;
        }

        #sidebarOverlay.active {
            display: block;
        }

        /* En pantallas grandes el menú queda siempre visible a la izquierda */
        @media (min-width: 1200px) {
            body.has-sidebar #sidebar {
                left: 0;
            }

            body.has-sidebar .app-navbar,
            body.has-sidebar #content,
            body.has-sidebar > footer {
                margin-left: var(--sidebar-width);
            }

            body.has-sidebar #sidebarClose,
            body.has-sidebar #sidebarOverlay {
                display: none !important;
            }

            /* Con el botón ☰ se puede ocultar el menú para tener más espacio (tablas anchas) */
            body.has-sidebar.sidebar-hidden #sidebar {
                left: calc(-1 * var(--sidebar-width));
            }

            body.has-sidebar.sidebar-hidden .app-navbar,
            body.has-sidebar.sidebar-hidden #content,
            body.has-sidebar.sidebar-hidden > footer {
                margin-left: 0;
            }
        }

        /* ===== Barra de ubicación y encabezado de página ===== */
        #appBreadcrumb .breadcrumb {
            font-size: 0.875rem;
        }

        #appBreadcrumb a {
            color: var(--ucsc-red);
            text-decoration: none;
        }

        #appBreadcrumb a:hover {
            text-decoration: underline;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .page-title .bi {
            color: var(--ucsc-red);
            margin-right: 0.5rem;
        }

        .page-subtitle {
            color: #6b7280;
            margin: 0.25rem 0 0;
        }

        /* Botón de la acción principal de cada listado ("Nuevo", "Filtrar"): rojo institucional */
        .btn-ucsc {
            background-color: var(--ucsc-red);
            border-color: var(--ucsc-red);
            color: #fff;
        }

        .btn-ucsc:hover,
        .btn-ucsc:focus {
            background-color: var(--ucsc-red-dark);
            border-color: var(--ucsc-red-dark);
            color: #fff;
        }

        /* Campos con calendario (flatpickr): ícono de calendario y ancho suficiente para leer la fecha completa */
        input.flatpickr-input {
            min-width: 165px;
            padding-right: 2rem !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z'/%3E%3Cpath d='M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.6rem center;
            background-size: 14px;
            cursor: pointer;
        }

        /* En tablet el texto de los botones se partía en dos líneas; mejor que la fila completa baje. */
        .btn {
            white-space: nowrap;
        }

        /* Al imprimir no van el menú, la barra de ubicación ni el pie, y el contenido usa todo el ancho */
        @media print {
            .app-navbar,
            #sidebar,
            #sidebarOverlay,
            #appBreadcrumb,
            .app-flash,
            body > footer {
                display: none !important;
            }

            body.has-sidebar #content,
            body.has-sidebar .app-navbar {
                margin-left: 0 !important;
            }

            main {
                padding: 0 !important;
            }

            /* El contenido usa todo el ancho de la hoja */
            .container,
            .container-fluid {
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Tablas completas, sin barras de desplazamiento ni columnas cortadas */
            .table-responsive,
            .table-box,
            .table-box-scroll {
                overflow: visible !important;
                max-height: none !important;
            }

            .table input,
            .modern-table input {
                min-width: 0 !important;
            }

            /* Que se vea como documento y no como formulario para editar */
            .form-control,
            .form-select {
                border: 0 !important;
                border-bottom: 1px solid #bbb !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            td .form-control,
            td .form-select {
                border-bottom: 0 !important;
            }

            .form-select {
                background-image: none !important;
            }

            ::placeholder {
                color: transparent !important;
            }

            .input-group-text {
                background: transparent !important;
                border: 0 !important;
                color: #333 !important;
            }
        }
    </style>

    @stack('head')
</head>

<body class="d-flex flex-column min-vh-100 {{ $showMenu ? 'has-sidebar' : '' }}">
    <div id="app" class="flex-grow-1">

        @if ($showMenu)
            <nav class="navbar navbar-dark shadow-sm app-navbar">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <button class="btn me-2" id="sidebarToggle" aria-label="Abrir menú">
                            <i class="bi bi-list"></i>
                        </button>

                        <a class="navbar-brand d-flex align-items-center gap-2 m-0 text-white fw-bold" href="{{ route('home') }}">
                            <span class="fs-5">SIDEc</span>
                            <span class="badge bg-white text-dark d-none d-sm-inline-block" style="font-size: 0.7rem;">UCSC</span>
                        </a>
                    </div>

                    {{-- Usuario conectado: nombre, cargo y cerrar sesión (visible también en celular) --}}
                    <div class="dropdown ms-auto">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4"></i>
                            <span class="d-none d-sm-flex flex-column lh-sm text-start">
                                <span class="fw-semibold">{{ Auth::user()->name }}</span>
                                <small class="opacity-75">{{ Auth::user()->roleLabel() }}</small>
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <div class="dropdown-header d-sm-none">
                                {{ Auth::user()->name }}<br>{{ Auth::user()->roleLabel() }}
                            </div>
                            <a class="dropdown-item logout-link d-flex align-items-center gap-2" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').requestSubmit();">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"
                                data-confirm="¿Cerrar sesión?" data-confirm-icon="question"
                                data-confirm-color="#D12421" data-confirm-button="Sí, cerrar sesión">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Menú lateral -->
            <nav id="sidebar" class="p-3 shadow-sm" aria-label="Menú principal">
                <div class="sidebar-title mb-3 py-2 px-3 text-center rounded-3">
                    Menú - SIDEc
                    <button id="sidebarClose" aria-label="Cerrar menú"><i class="bi bi-x-lg"></i></button>
                </div>

                <ul class="nav flex-column">
                    @role('Manager|Supervisor|Area Manager')
                        <li class="nav-item mb-1">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                    @endrole

                    @role('Supervisor|Area Manager')
                        <li class="nav-item mb-1">
                            <a href="{{ route('receptions.index') }}"
                                class="nav-link {{ request()->is('receptions', 'receptions/*') ? 'active' : '' }}">
                                <i class="bi bi-box-arrow-in-down me-2"></i> Recepciones
                            </a>
                        </li>
                    @endrole

                    @role('Analist|Manager|Supervisor|Area Manager')
                        <li class="nav-item mb-1">
                            <a href="{{ route('sample_entries.index') }}"
                                class="nav-link {{ request()->is('sample_entries', 'sample_entries/*') ? 'active' : '' }}">
                                <i class="bi bi-journal-plus me-2"></i> Ingreso de muestras
                            </a>
                        </li>
                    @endrole

                    @role('Supervisor|Area Manager')
                        <li class="nav-item mb-1">
                            <a href="{{ route('rejections.index') }}"
                                class="nav-link {{ request()->is('rejections', 'rejections/*') ? 'active' : '' }}">
                                <i class="bi bi-x-circle me-2"></i> Rechazos
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->is('users', 'users/*') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i> Usuarios
                            </a>
                        </li>
                    @endrole

                    {{-- Bioensayos: se despliega hacia abajo y queda abierto si estás en uno de ellos --}}
                    @role('Analist|Manager|Supervisor|Area Manager')
                        <li class="nav-item mb-1">
                            <a class="nav-link submenu-toggle {{ $inBioassays ? 'active' : '' }}" data-bs-toggle="collapse"
                                href="#bioassaysMenu" role="button" aria-controls="bioassaysMenu"
                                aria-expanded="{{ $inBioassays ? 'true' : 'false' }}">
                                <i class="bi bi-eyedropper me-2"></i> Bioensayos
                                <i class="bi bi-chevron-down chevron ms-auto small"></i>
                            </a>
                            <ul class="collapse submenu list-unstyled ps-2 mt-1 {{ $inBioassays ? 'show' : '' }}" id="bioassaysMenu">
                                @foreach (Navigation::BIOASSAYS as $section => [$label, $icon])
                                    <li class="mb-1">
                                        <a href="{{ route("$section.index") }}"
                                            class="nav-link {{ request()->is($section, "$section/*") ? 'active' : '' }}">
                                            <i class="bi {{ $icon }} me-2"></i> {{ $label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endrole
                </ul>
            </nav>
        @endif

        <div id="sidebarOverlay"></div>

        <!-- Contenido principal -->
        <div id="content">
            <main class="py-4">
                {{-- Barra de ubicación: Inicio › Bioensayos › Daphnia magna (agudo) › Editar --}}
                @if ($breadcrumb)
                    <nav id="appBreadcrumb" aria-label="Ubicación" class="container-fluid px-4 mb-3">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Inicio</a>
                            </li>
                            @foreach ($breadcrumb as $step)
                                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}"
                                    @if ($loop->last) aria-current="page" @endif>
                                    @if ($step['url'])
                                        <a href="{{ $step['url'] }}">{{ $step['label'] }}</a>
                                    @else
                                        {{ $step['label'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                {{-- Mensajes del servidor ("guardado correctamente", errores, avisos), igual en todas las páginas --}}
                @if ($showMenu)
                    @foreach (['success' => ['success', 'check-circle'], 'error' => ['danger', 'x-octagon'],
                               'warning' => ['warning', 'exclamation-triangle'], 'info' => ['info', 'info-circle']] as $type => [$color, $icon])
                        @if (session($type))
                            <div class="container-fluid px-4 app-flash">
                                <div class="alert alert-{{ $color }} alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                                    <i class="bi bi-{{ $icon }}"></i>
                                    <div>{{ session($type) }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @if ($showMenu)
        <script>
            // Menú lateral. En pantallas chicas el botón ☰ lo abre encima de la página; en las grandes
            // (desde 1200 px) el menú está fijo y el botón lo oculta o lo muestra (se recuerda la preferencia).
            const pantallaGrande = () => window.matchMedia('(min-width: 1200px)').matches;
            try {
                if (localStorage.getItem('sidec-menu-oculto') === '1') document.body.classList.add('sidebar-hidden');
            } catch (e) {}

            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');

                function openSidebar() {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                }

                function closeSidebar() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }

                document.getElementById('sidebarToggle').addEventListener('click', function () {
                    if (!pantallaGrande()) return openSidebar();
                    const oculto = document.body.classList.toggle('sidebar-hidden');
                    try { localStorage.setItem('sidec-menu-oculto', oculto ? '1' : '0'); } catch (e) {}
                });
                document.getElementById('sidebarClose').addEventListener('click', closeSidebar);
                overlay.addEventListener('click', closeSidebar);
            });
        </script>
    @endif

    {{-- Confirmación con SweetAlert para cualquier formulario que tenga data-confirm="pregunta" --}}
    <script>
        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!form.matches('form[data-confirm]')) return;

            // Si SweetAlert no cargó (sin internet o bloqueado), se usa la confirmación del navegador.
            // Antes el botón quedaba sin hacer nada (por ejemplo, "Cerrar sesión").
            if (!window.Swal) {
                if (!window.confirm(form.dataset.confirm)) event.preventDefault();
                return;
            }

            event.preventDefault();
            Swal.fire({
                title: form.dataset.confirm,
                text: form.dataset.confirmText || '',
                icon: form.dataset.confirmIcon || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D12421',
                cancelButtonColor: '#6c757d',
                confirmButtonText: form.dataset.confirmButton || 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                // form.submit() no vuelve a disparar el evento submit, así que no se pregunta dos veces
                if (result.isConfirmed) form.submit();
            });
        });
    </script>

    {{-- Números con coma decimal y punto de miles en todos los campos numéricos --}}
    <script src="{{ asset('js/decimal-comma.js') }}"></script>

    @stack('scripts')

    @stack('styles')

    {{-- Tema institucional (rojo UCSC y blanco): va al final para mandar por sobre los colores de cada vista --}}
    <link href="{{ asset('css/sidec-theme.css') }}" rel="stylesheet">

    @if ($showMenu)
        <footer class="mt-auto py-3 bg-white border-top text-muted small">
            <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between px-4 gap-2">
                <div><strong>SIDEc</strong> · Sistema de Información del Departamento de Ecotoxicología</div>
                <div>CREA · Universidad Católica de la Santísima Concepción</div>
            </div>
        </footer>
    @endif
</body>

</html>
