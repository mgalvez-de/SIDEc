<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">



<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>{{ config('app.name', 'Laravel') }}</title>



    <!-- Scripts -->

    <script src="{{ asset('js/app.js') }}"></script>



    <!-- Fonts -->

    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">



    <!-- Icons -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">



    <!-- Styles -->

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">



    {{-- Icons --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">





    <style>
        #sidebar {

            position: fixed;

            top: 0;

            left: -250px;

            width: 250px;

            height: 100%;

            background: #ffffff;

            color: #6b7280;

            /* gris medio */

            transition: all 0.3s ease;

            z-index: 1051;

            padding-top: 60px;

            border-right: 1px solid #e5e7eb;

        }



        #sidebar.active {

            left: 0;

        }



        #sidebarClose {

            position: absolute;

            top: 10px;

            right: 15px;

            background: transparent;

            border: none;

            font-size: 1.5rem;

            cursor: pointer;

        }



        #sidebar .nav-link {

            font-weight: 500;

            color: #6b7280;

            /* gris medio */

            border-radius: 0.375rem;

            transition: all 0.2s ease;

        }



        #sidebar .nav-link:hover,

        #sidebar .dropdown-menu .dropdown-item:hover {

            background-color: #f3f4f6;

            color: #111827 !important;

            /* gris oscuro */

            transform: translateX(5px);

        }



        #sidebar .dropdown-menu {

            border: none;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);

        }



        #sidebar h4 {

            font-size: 1.1rem;

            font-weight: 600;

        }



        /* Overlay */

        #sidebarOverlay {

            position: fixed;

            top: 0;

            left: 0;

            width: 100%;

            height: 100%;

            background: rgba(0, 0, 0, 0.1);

            display: none;

            z-index: 1050;

        }



        #sidebarOverlay.active {

            display: block;

        }



        /* Efecto hover para enlaces del sidebar */

        #sidebar .nav-link {

            position: relative;

            display: block;

            color: #111827;

            /* Texto oscuro para visibilidad en fondo blanco */

            text-decoration: none;

            padding: 10px 20px;

            transition: all 0.3s ease;

            /* Transición más suave */

            font-weight: 500;

            border-radius: 0.375rem;

        }



        #sidebar .dropdown-menu .dropdown-item {

            color: #111827;

        }



        /* Cambiar fondo y color al pasar el mouse */

        #sidebar .nav-link:hover,

        #sidebar .dropdown-menu .dropdown-item:hover {

            background-color: #ff0000;

            color: #ffffff !important;

            /* Texto blanco para un contraste limpio */

            padding-left: 30px;

            /* Ligero desplazamiento */

            transform: translateX(5px);

            /* Efecto de movimiento */

        }
    </style>



    @stack('head')

</head>



<body>

    <div id="app">

        @unless (request()->is('login'))

            <nav class="navbar navbar-expand-md navbar-dark shadow-sm"
                style="background: linear-gradient(90deg, #8B1E1E 0%, #A32323 100%);">

                <div class="container-fluid">

                    <!-- Botón para abrir/cerrar sidebar -->

                    <button class="btn btn-outline-secondary me-2 text-white border border-white" id="sidebarToggle">☰</button>

                   <!-- Titulo seleccionable SIDEc ucsc -->

                    <a class="navbar-brand d-flex align-items-center gap-2 m-0 text-white fw-bold"
                        href="{{ url('/dashboard') }}">
                        <span class="fs-5 tracking-wide">SIDEc</span>
                        <span class="badge bg-white text-dark d-none d-sm-inline-block"
                            style="font-size: 0.7rem;">UCSC</span>
                    </a>



                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                        <!-- Left Side -->

                        <ul class="navbar-nav me-auto"></ul>



                        <!-- Right Side -->

                        <ul class="navbar-nav ms-auto">

                            @guest

                                @if (Route::has('register'))

                                    <li class="nav-item">

                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>

                                    </li>

                                @endif

                            @else

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        {{ Auth::user()->name }}

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();

                                                         document.getElementById('logout-form').submit();">

                                            {{ __('Logout') }}

                                        </a>



                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">

                                            @csrf

                                        </form>

                                    </div>

                                </li>

                            @endguest

                        </ul>

                    </div>

                </div>

            </nav>



            <!-- Sidebar -->

            <nav id="sidebar" class="p-3 shadow">



                <!-- Botón cerrar -->

                <button id="sidebarClose" class="text-gray-500 hover:text-red-500">✖</button>



                <h4 class="mb-4 text-dark fw-bold">Menú - SIDEc</h4>

                <ul class="nav flex-column">



                    {{-- Dashboard: Manager, Area Manager --}}

                    @role('Manager|Area Manager')

                    <li class="nav-item mb-1">

                        <a href="{{ url('/dashboard') }}" class="nav-link text-gray-700 d-flex align-items-center">

                            <i class="bi bi-speedometer2 me-2"></i> Dashboard

                        </a>

                    </li>

                    @endrole



                    {{-- Recepciones: Manager, Area Manager --}}

                    @role('Manager|Area Manager')

                    <li class="nav-item mb-1">

                        <a href="{{ url('/receptions') }}" class="nav-link text-gray-700 d-flex align-items-center">

                            <i class="bi bi-box-arrow-in-down me-2"></i> Recepciones

                        </a>

                    </li>

                    @endrole



                    {{-- Sample Entries: Analist, Manager, Area Manager --}}

                    @role('Analist|Manager|Area Manager')

                    <li class="nav-item mb-1">

                        <a href="{{ url('/sample_entries') }}" class="nav-link text-gray-700 d-flex align-items-center">

                            <i class="bi bi-journal-plus me-2"></i> Ingreso de muestras

                        </a>

                    </li>

                    @endrole



                    {{-- Rechazos: Manager, Area Manager --}}

                    @role('Manager|Area Manager')

                    <li class="nav-item mb-1">

                        <a href="{{ url('/rejections') }}" class="nav-link text-gray-700 d-flex align-items-center">

                            <i class="bi bi-x-circle me-2"></i> Rechazos

                        </a>

                    </li>

                    @endrole



                    {{-- Bioensayos --}}

                    @role('Analist|Manager|Area Manager')

                    <li class="nav-item dropdown mb-1">

                        <a class="nav-link text-gray-700 d-flex align-items-center dropdown-toggle" href="#"
                            id="bioassaysDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <i class="bi bi-flask me-2"></i> Bioensayos

                        </a>

                        <ul class="dropdown-menu" aria-labelledby="bioassaysDropdown">

                            <li><a class="dropdown-item" href="{{ url('/daphnia-magna') }}"><i
                                        class="bi bi-droplet me-2"></i> Daphnia magna</a></li>

                            <li><a class="dropdown-item" href="{{ url('/isochrysis-galbana') }}"><i
                                        class="bi bi-water me-2"></i> Isochrysis galbana (crónico)</a></li>

                            <li><a class="dropdown-item" href="{{ url('/selenastrum') }}"><i class="bi bi-flower3 me-2"></i>

                                    Selenastrum capricornutum</a></li>

                            <li><a class="dropdown-item disabled-link" href="{{ url('/tisbe-longicornis-water') }}"><i
                                        class="bi bi-bug me-2"></i> Tisbe

                                    longicornis aguas marinas</a></li>

                            <li>

                                <a class="dropdown-item disabled-link" href="#">

                                    <i class="bi bi-bezier2 me-2"></i> Tisbe longicornis Sustancias Químicas

                                </a>

                            </li>

                            <li>

                                <a class="dropdown-item disabled-link" href="#">

                                    <i class="bi bi-egg me-2"></i> Arbacia spatuligera Estado Larval

                                </a>

                            </li>

                            <li>

                                <a class="dropdown-item disabled-link" href="{{ url('/arbacia_fertilization') }}">

                                    <i class="bi bi-egg-fried me-2"></i> Arbacia spatuligera fecundación

                                </a>

                            </li>

                        </ul>

                    </li>

                    @endrole



                </ul>

            </nav>





        @endunless



        <!-- Overlay -->

        <div id="sidebarOverlay"></div>



        <!-- Contenido principal -->

        <div id="content">

            <main class="py-4">

                @yield('content')

            </main>

        </div>

    </div>



    <!-- Script para el toggle -->

    @unless (request()->is('login'))

        <script>

            document.addEventListener("DOMContentLoaded", function () {

                const toggleBtn = document.getElementById("sidebarToggle");

                const closeBtn = document.getElementById("sidebarClose");

                const sidebar = document.getElementById("sidebar");

                const content = document.getElementById("content");

                const overlay = document.getElementById("sidebarOverlay");



                function openSidebar() {

                    sidebar.classList.add("active");

                    content.classList.add("shifted");

                    overlay.classList.add("active");

                }



                function closeSidebar() {

                    sidebar.classList.remove("active");

                    content.classList.remove("shifted");

                    overlay.classList.remove("active");

                }



                toggleBtn.addEventListener("click", openSidebar);

                closeBtn.addEventListener("click", closeSidebar);

                overlay.addEventListener("click", closeSidebar);

            });

        </script>

    @endunless

    @stack('scripts')

    @stack('styles')

</body>

<!-- FOOTER -->
@unless (request()->is('login'))
    <footer class="mt-auto py-3 bg-white border-top text-muted small">
        <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between  px-4 gap-2">
            <div>
                <strong>SIDEc</strong> 
            </div>
            <div>
                <span class="badge bg-light text-secondary ">Laboratorio de Bioensayos</span>
            </div>
        </div>
    </footer>
@endunless

</html>