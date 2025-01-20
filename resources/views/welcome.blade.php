<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" /> --}}

    <!-- Inline Critical CSS -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
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
</head>
<body style="background-color: white;">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color:teal;">
          <div class="container">
              <div class="row col-12">
                  <div class="col-md-4">
                      <label class="text-white">{{ __('NO. PERAKUAN PENDAFTARAN : DK036(N)') }}</label>
                  </div>
                  <div class="col-md-2">
                      <label class="text-white"><i class="bi bi-telephone-fill"></i>&nbsp;{{ __('+606-6490350') }}</label>
                  </div>
                  <div class="col-md-4">
                      <label class="text-white"><i class="bi bi-envelope-at-fill"></i>&nbsp;{{ __('info@uniti.edu.my') }}</label>
                  </div>
                  <div class="col-md-2 d-flex justify-content-md-end justify-content-start mt-md-0">
                      <a href="https://uniti.edu.my" target="_blank" class="text-white mx-2"><i class="bi bi-globe-central-south-asia"></i></a>
                      <a href="https://www.facebook.com/kolejunitiportdickson" target="_blank" class="text-white mx-2"><i class="bi bi-facebook"></i></a>
                      <a href="https://www.instagram.com/kolejunitiportdickson/" target="_blank" class="text-white mx-2"><i class="bi bi-instagram"></i></a>
                      <a href="https://www.youtube.com/KOLEJUNITIPORTDICKSON" target="_blank" class="text-white mx-2"><i class="bi bi-youtube"></i></a>
                      <a href="https://www.tiktok.com/@kolejunitipd" target="_blank" class="text-white mx-2"><i class="bi bi-tiktok"></i></a>
                  </div>
              </div>
          </div>
        </nav>
        
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
          <div class="container">
                <a class="navbar-brand" href="{{ url('/') . (isset($source) ? '?source=' . $source : '') . (isset($ref) ? (isset($source) ? '&' : '?') . 'ref=' . $ref : '') }}">
                    <picture>
                        <source srcset="https://ku-storage-object.ap-south-1.linodeobjects.com/eaduan/images/logo/eaduan.webp" type="image/webp">
                        <img src="https://ku-storage-object.ap-south-1.linodeobjects.com/eaduan/images/logo/eaduan.png" alt="Logo" class="img-fluid" style="width: 150px; height: auto;">
                    </picture>
                </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <!-- Left Side Of Navbar -->
                  <ul class="navbar-nav me-auto">

                  </ul>

                  <!-- Right Side Of Navbar -->
                  <ul class="navbar-nav ms-auto">
                      <!-- Authentication Links -->
                      @guest
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Login
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                          <li><a class="dropdown-item" href="{{ route('staff.login') }}">Staff Login</a></li>
                          <li><a class="dropdown-item" href="{{ route('student.login') }}">Student Login</a></li>
                        </ul>
                      </li>
                      @else
                          <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link dropdown-toggle text-uppercase" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                  {{ Auth::user()->name }}
                              </a>

                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                  <a class="dropdown-item" href="{{ route('logout') }}"
                                      onclick="event.preventDefault();
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

        <main class="py-3" style="max-height: 90vh;">
            {{-- @yield('content') --}}
            <div class="container mb-3">
                <br><br><br><br><br><br><br><br><br><br><br><br>
                {{-- <!-- Use the <picture> element for WebP support -->
                <picture>
                    <!-- WebP Format -->
                    <source 
                        srcset="
                            https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-480.webp 480w,
                            https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-768.webp 768w,
                            https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-1200.webp 1200w"
                        sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, 1200px"
                        type="image/webp"
                    >
                    <!-- Fallback for non-WebP browsers -->
                    <img 
                        srcset="
                            https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-480.jpg 480w,
                            https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-768.jpg 768w,
                            https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-1200.jpg 1200w"
                        sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, 1200px"
                        src="https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/banners/banner-samsung-tab-1200.jpg"
                        alt="Banner" 
                        class="img-fluid"
                        width="1200" 
                        height="600"
                    >
                </picture> --}}
            </div>
        
            <div class="container">
                <footer>
                  <div class="row">
                    <div class="col-md-2 offset-md-5 mb-3">
                      {{-- <h5>Pautan Segera</h5>
                      <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a href="{{ route('student.about') . (isset($source) ? '?source=' . $source : '') . (isset($ref) ? (isset($source) ? '&' : '?') . 'ref=' . $ref : '') }}" class="nav-link p-0 text-body-secondary">Daftar Kemasukan</a>
                        </li>
                      </ul> --}}
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <h5>Info Kualiti</h5>
                        <ul class="nav flex-column">
                          <li class="nav-item mb-2"><a href="https://uniti.edu.my/polisi-kualiti/" class="nav-link p-0 text-body-secondary">Polisi Kualiti</a></li>
                          <li class="nav-item mb-2"><a href="https://uniti.edu.my/objektif-kualiti/" class="nav-link p-0 text-body-secondary">Objektif Kualiti</a></li>
                          <li class="nav-item mb-2"><a href="https://uniti.edu.my/sijil-dan-logo-iso/" class="nav-link p-0 text-body-secondary">Logo & Sijil ISO</a></li>
                        </ul>
                      </div>

                    <div class="col-md-3 mb-3">
                        <form>
                          <h5>UNITI, Pilihan Terbaik Anda</h5>
                          <label class="mb-1">UNITI Village Persiaran UNITI</label><br>
                          <label class="mb-1">Tanjung Agas, 71250 Port Dickson</label><br>
                          <label class="mb-1">Negeri Sembilan</label><br>
                          <label class="mb-1"><i class="bi bi-telephone-fill"></i>&nbsp;{{ __('+606-6490350') }}</label><br>
                          <label class="mb-1"><i class="bi bi-envelope-at-fill"></i>&nbsp;{{ __('info@uniti.edu.my') }}</label>
                        </form>
                      </div>
                  </div>
              
                  <div class="d-flex flex-column flex-sm-row justify-content-between py-2 border-top">
                    <p>&copy; Copyright Kolej UNITI 2024.</p>
                    <ul class="list-unstyled d-flex">
                      <li class="ms-3"><a href="https://uniti.edu.my" target="_blank" class="text-dark"><i class="bi bi-globe-central-south-asia"></i></a></li>
                      <li class="ms-3"><a href="https://www.facebook.com/kolejunitiportdickson" target="_blank" class="text-dark"><i class="bi bi-facebook"></i></a></li>
                      <li class="ms-3"><a href="https://www.instagram.com/kolejunitiportdickson/" target="_blank" class="text-dark"><i class="bi bi-instagram"></i></a></li>
                      <li class="ms-3"><a href="https://www.youtube.com/KOLEJUNITIPORTDICKSON" target="_blank" class="text-dark"><i class="bi bi-youtube"></i></a></li>
                      <li class="ms-3"><a href="https://www.tiktok.com/@kolejunitipd" target="_blank" class="text-dark"><i class="bi bi-tiktok"></i></a></li>
                    </ul>
                  </div>
                </footer>
            </div>
        </main>
    </div>
</body>
</html>
