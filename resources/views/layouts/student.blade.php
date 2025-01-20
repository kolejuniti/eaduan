<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    {{-- <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}

     <!-- Inline Critical CSS -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            /* Add other critical styles here */
        }
    </style>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" as="style" onload="this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap">
    </noscript>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            position: relative;
        }

        #spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
        }

        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Spinner -->
    <div id="spinner">
        <div class="loader"></div>
    </div>
    
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                @guest('student')
                <a class="navbar-brand" href="{{ url('/') }}">
                    <picture>
                        <source srcset="https://ku-storage-object.ap-south-1.linodeobjects.com/eaduan/images/logo/eaduan.webp" type="image/webp">
                        <img src="https://ku-storage-object.ap-south-1.linodeobjects.com/eaduan/images/logo/eaduan.png" alt="Logo" class="img-fluid" style="width: 150px; height: auto;">
                    </picture>
                </a>
                @else
                <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                    <picture>
                        <source srcset="https://ku-storage-object.ap-south-1.linodeobjects.com/eaduan/images/logo/eaduan.webp" type="image/webp">
                        <img src="https://ku-storage-object.ap-south-1.linodeobjects.com/eaduan/images/logo/eaduan.png" alt="Logo" class="img-fluid" style="width: 150px; height: auto;">
                    </picture>
                </a>
                @endguest
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @guest('student')
                        @else
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('student.dashboard') }}"><i class="bi bi-house"></i>&nbsp;Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-pencil-square"></i>&nbsp;Borang Aduan</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('student.damageform') }}">Kerosakan</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('student.generalform') }}">Umum</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-info-square"></i>&nbsp;Status Aduan</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('student.damagereport') }}">Kerosakan</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('student.generalreport') }}">Umum</a>
                                </li>
                            </ul>
                        </li>
                        @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest('student')
                            {{-- @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-person-circle"></i>&nbsp;{{ Auth::guard('student')->user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('student.logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('student.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="max-height: 90vh; overflow-y: auto;">
            @yield('content')
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('load', function () {
                document.getElementById('spinner').style.display = 'none';
            });
        });
    </script>
</body>
</html>
