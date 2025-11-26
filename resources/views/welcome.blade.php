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

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body style="background-color: white;">
    <div id="app">
        <!-- <nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color:teal;">
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
        </nav> -->
        
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

        <main class="py-3" style="max-height: 90vh; overflow-y: auto;">
            <div class="container">
                <!-- Hero Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white text-center py-4">
                                <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 600;">e-Aduan</h1>
                                <p class="mb-1" style="font-size: 0.95rem;">Sistem Pengurusan Aduan Atas Talian</p>
                                <p class="mb-0" style="font-size: 0.85rem; opacity: 0.9;">
                                    <i class="bi bi-clock-fill"></i> Akses 24/7 | 
                                    <i class="bi bi-check-circle-fill"></i> Diselesaikan dalam tempoh 7 hari
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaint Types -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px; background-color: #f56565; color: white;">
                                        <i class="bi bi-tools" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">Aduan Kerosakan</h5>
                                </div>
                                <p class="mb-2" style="font-size: 0.85rem; color: #4a5568;">Laporkan aduan berkaitan kerosakan berkaitan asrama atau infrastruktur:</p>
                                <ul style="font-size: 0.8rem; color: #718096; margin-bottom: 0;">
                                    <li>Perabot atau peralatan rosak</li>
                                    <li>Masalah penyelenggaraan kemudahan</li>
                                    <li>Masalah elektrik atau paip</li>
                                    <li>Kebimbangan struktur bangunan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px; background-color: #4299e1; color: white;">
                                        <i class="bi bi-chat-dots" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">Aduan Am</h5>
                                </div>
                                <p class="mb-2" style="font-size: 0.85rem; color: #4a5568;">Kemukakan pertanyaan, aduan atau cadangan anda:</p>
                                <ul style="font-size: 0.8rem; color: #718096; margin-bottom: 0;">
                                    <li>Kualiti perkhidmatan / pentadbiran</li>
                                    <li>Kebersihan dan persekitaran</li>
                                    <li>Isu berkaitan akademik</li>
                                    <li>Kemudahan fasiliti</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Why Submit Complaints -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="background-color: #edf2f7;">
                            <div class="card-body py-3">
                                <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                                    <i class="bi bi-question-circle-fill" style="color: #667eea;"></i> Kenapa Aduan Anda Penting?
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul style="font-size: 0.85rem; color: #4a5568; margin-bottom: 0.5rem;">
                                            <li><strong>Tingkatkan Persekitaran Kampus<br></strong> Bantu mengekalkan ruang pembelajaran yang selamat dan selesa</li>
                                            <li><strong>Penyelesaian Pantas<br></strong> Isu anda akan ditangani dengan segera oleh unit yang berkaitan</li>
                                            <li><strong>Proses Telus<br></strong> Status aduan anda boleh disemak dengan segera</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul style="font-size: 0.85rem; color: #4a5568; margin-bottom: 0;">
                                            <li><strong>Suarakan Kebimbangan<br></strong> Memastikan aduan dan maklum balas anda sampai kepada pihak bertanggungjawab</li>
                                            <li><strong>Cegah Masalah Masa Depan<br></strong> Membantu mengenal pasti masalah berulang untuk penyelesaian jangka panjang</li>
                                            <li><strong>Tanggungjawab Komuniti<br></strong> Memastikan semua pihak dapat menikmati suasana kampus yang lebih baik</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advantages of Online System -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="bi bi-star-fill" style="color: #f6ad55;"></i> Kelebihan Sistem Aduan Dalam Talian
                        </h5>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card h-100 border-0" style="background-color: #e6fffa; border-left: 4px solid #38b2ac !important;">
                            <div class="card-body p-3">
                                <div class="text-center mb-2">
                                    <i class="bi bi-clock-history" style="font-size: 1.8rem; color: #38b2ac;"></i>
                                </div>
                                <h6 class="text-center mb-1" style="font-size: 0.9rem; font-weight: 600; color: #2d3748;">Akses 24/7</h6>
                                <p class="text-center mb-0" style="font-size: 0.75rem; color: #4a5568;">Hantar bila-bila masa, di mana sahaja</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card h-100 border-0" style="background-color: #fef5e7; border-left: 4px solid #f6ad55 !important;">
                            <div class="card-body p-3">
                                <div class="text-center mb-2">
                                    <i class="bi bi-lightning-fill" style="font-size: 1.8rem; color: #f6ad55;"></i>
                                </div>
                                <h6 class="text-center mb-1" style="font-size: 0.9rem; font-weight: 600; color: #2d3748;">Penyelesaian Pantas</h6>
                                <p class="text-center mb-0" style="font-size: 0.75rem; color: #4a5568;">Aduan segera diselesaikan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card h-100 border-0" style="background-color: #e8f4fd; border-left: 4px solid #4299e1 !important;">
                            <div class="card-body p-3">
                                <div class="text-center mb-2">
                                    <i class="bi bi-eye-fill" style="font-size: 1.8rem; color: #4299e1;"></i>
                                </div>
                                <h6 class="text-center mb-1" style="font-size: 0.9rem; font-weight: 600; color: #2d3748;">Jejak Status</h6>
                                <p class="text-center mb-0" style="font-size: 0.75rem; color: #4a5568;">Pantau status terkini aduan anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card h-100 border-0" style="background-color: #fce7f3; border-left: 4px solid #d946ef !important;">
                            <div class="card-body p-3">
                                <div class="text-center mb-2">
                                    <i class="bi bi-file-earmark-text-fill" style="font-size: 1.8rem; color: #d946ef;"></i>
                                </div>
                                <h6 class="text-center mb-1" style="font-size: 0.9rem; font-weight: 600; color: #2d3748;">Rekod Digital</h6>
                                <p class="text-center mb-0" style="font-size: 0.75rem; color: #4a5568;">Maklumat direkod didalam sistem</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Features -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body py-3">
                                <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                                    <i class="bi bi-gear-fill" style="color: #667eea;"></i> Ciri-ciri Utama
                                </h5>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #48bb78; font-size: 1rem;"></i>
                                            <div>
                                                <strong style="font-size: 0.85rem; color: #2d3748;">Senang & Mudah</strong>
                                                <p class="mb-0" style="font-size: 0.75rem; color: #718096;">Borang dalam talian dan mudah diakses</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #48bb78; font-size: 1rem;"></i>
                                            <div>
                                                <strong style="font-size: 0.85rem; color: #2d3748;">Maklumbalas segera</strong>
                                                <p class="mb-0" style="font-size: 0.75rem; color: #718096;">Tindakan pantas pihak bertanggungjawab</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #48bb78; font-size: 1rem;"></i>
                                            <div>
                                                <strong style="font-size: 0.85rem; color: #2d3748;">Selamat & Sulit</strong>
                                                <p class="mb-0" style="font-size: 0.75rem; color: #718096;">Akses dilindungi dengan kelayakan log masuk</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Section -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <h5 class="text-center mb-3" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="bi bi-box-arrow-in-right" style="color: #667eea;"></i> Log Masuk untuk Menghantar Aduan Anda
                        </h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="bi bi-person-circle" style="font-size: 3rem; color: white;"></i>
                                </div>
                                <h5 class="text-white mb-2" style="font-size: 1.1rem; font-weight: 600;">Log Masuk Pelajar</h5>
                                <p class="text-white mb-3" style="font-size: 0.8rem; opacity: 0.9;">Akses sistem dengan kelayakan pelajar anda</p>
                                <a href="{{ route('student.login') }}" class="btn btn-light px-4 py-2" style="font-size: 0.85rem; font-weight: 500; border-radius: 6px;">
                                    <i class="bi bi-box-arrow-in-right"></i> Log Masuk sebagai Pelajar
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #38b2ac 0%, #2c7a7b 100%);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="bi bi-person-badge" style="font-size: 3rem; color: white;"></i>
                                </div>
                                <h5 class="text-white mb-2" style="font-size: 1.1rem; font-weight: 600;">Log Masuk Kakitangan</h5>
                                <p class="text-white mb-3" style="font-size: 0.8rem; opacity: 0.9;">Akses sistem dengan kelayakan kakitangan anda</p>
                                <a href="{{ route('staff.login') }}" class="btn btn-light px-4 py-2" style="font-size: 0.85rem; font-weight: 500; border-radius: 6px;">
                                    <i class="bi bi-box-arrow-in-right"></i> Log Masuk sebagai Kakitangan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
