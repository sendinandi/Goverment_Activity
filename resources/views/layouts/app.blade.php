<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPDA - Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: 0.5px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.2);
        }

        .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .nav-link i {
            font-size: 1.15rem;
            color: #adb5bd;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #4e73df;
            background-color: rgba(78, 115, 223, 0.05);
        }

        .nav-link:hover i {
            color: #4e73df;
        }

        .nav-link.active {
            color: #4e73df;
            background-color: rgba(78, 115, 223, 0.1);
            font-weight: 600;
        }

        .nav-link.active i {
            color: #4e73df;
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            min-height: 100vh;
            width: 100%;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 15px 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid #e3e6f0;
        }
    </style>
</head>

<body>

    <div class="offcanvas offcanvas-start border-0 shadow-lg" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" style="width: 290px;">

        <div class="offcanvas-header pb-3 pt-4 px-4 border-bottom border-light">
            <a href="{{ route('dashboard') }}" class="sidebar-brand text-decoration-none m-0 d-flex align-items-center gap-3">
                <div class="brand-icon">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>
                <span>SIPDA</span>
            </a>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-4 pt-3">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                @auth
                @if(Auth::user()->role == 'pimpinan')
                <li class="nav-item mt-4 mb-2 text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #a5a8b3; letter-spacing: 1px; padding-left: 15px;">
                    Menu Utama
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan Rekapitulasi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('manual-book.index') }}" 
                        class="nav-link {{ request()->routeIs('manual-book.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Manual Book</span>
                    </a>
                </li>
                @endif
                @endauth

                @auth
                @if(Auth::user()->role == 'operator')
                <li class="nav-item">
                    <a href="{{ route('projects.create') }}" class="nav-link {{ request()->routeIs('projects.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-square"></i> Input Realisasi Baru
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}">
                        <i class="bi bi-list-task"></i> Kelola Kegiatan Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('projects.history') }}" class="nav-link {{ request()->routeIs('projects.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> History Kegiatan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('manual-book.index') }}" 
                        class="nav-link {{ request()->routeIs('manual-book.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Manual Book</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->role == 'admin_opd')
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf"></i> Laporan Rekap
                    </a>
                </li>

                <li class="nav-item mt-4 mb-2 text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #a5a8b3; letter-spacing: 1px; padding-left: 15px;">
                    Pengaturan
                </li>

                <li class="nav-item">
                    <a href="{{ route('programs.index') }}" class="nav-link {{ request()->routeIs('programs.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i> Master Program dan Bagian
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('activities.index') }}" class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Master Kegiatan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Manajemen Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('audit.index') ? 'active text-primary fw-bold' : 'text-secondary' }}" href="{{ route('audit.index') }}">
                        <i class="bi bi-clock-history me-2"></i>
                        <span>Audit Log</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('manual-book.index') }}" 
                        class="nav-link {{ request()->routeIs('manual-book.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Manual Book</span>
                    </a>
                </li>
                @endif

                {{-- Menu Validasi HANYA untuk Verifikator --}}
                @if(auth()->user()->role === 'verifikator')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('validation.index') }}">
                        <i class="bi bi-check2-square"></i> Validasi Data Proyek
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan Rekapitulasi
                    </a>
                </li>
                <li class="nav-item mt-4 mb-2 text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #a5a8b3; letter-spacing: 1px; padding-left: 15px;">
                    Bantuan
                </li>

                <li class="nav-item">
                    <a href="{{ route('manual-book.index') }}" 
                        class="nav-link {{ request()->routeIs('manual-book.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Manual Book</span>
                    </a>
                </li>
                @endif
                @endauth
            </ul>

            <div class="mt-auto pt-4 border-top border-light">
                @auth
                <div class="d-flex align-items-center gap-3 px-2 mb-3">
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div style="line-height: 1.3;">
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ Str::limit(Auth::user()->name, 15) }}</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 mt-1" style="font-size: 0.65rem;">
                            {{ strtoupper(Auth::user()->role) ?? 'USER' }}
                        </span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light text-danger w-100 fw-semibold rounded-3 d-flex align-items-center justify-content-center gap-2 py-2">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
                @else
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-3 fw-bold py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> MASUK
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-white shadow-sm border rounded-3 p-2 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" title="Buka Menu">
                    <i class="bi bi-list fs-4 text-primary"></i>
                </button>

                <div>
                    <h5 class="fw-bolder m-0 text-dark">
                        @if(request()->routeIs('validation.*'))
                        Validasi Data Kegiatan
                        @elseif(request()->routeIs('reports.*'))
                        Laporan Rekapitulasi
                        @elseif(request()->routeIs('projects.create'))
                        Input Realisasi Baru
                        @elseif(request()->routeIs('projects.index'))
                        Kelola Kegiatan Saya
                        @elseif(request()->routeIs('projects.history'))
                        History Kegiatan
                        @elseif(request()->routeIs('users.*'))
                        Manajemen Pengguna
                        @elseif(request()->routeIs('programs.*'))
                        Master Program dan Bagian
                        @elseif(request()->routeIs('activities.*'))
                        Master Kegiatan
                        @elseif(request()->routeIs('manual-book.*'))
                        Manual Book
                        @elseif(request()->routeIs('audit.*'))
                        Audit Log
                        @else
                        Dashboard
                        @endif
                    </h5>
                    <p class="text-muted m-0" style="font-size: 0.8rem;">Monitoring Pembangunan Kota Bekasi</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                @if(request()->routeIs('validation.index'))
                <form action="{{ route('validation.index') }}" method="GET" class="d-none d-md-block">
                    <div class="input-group shadow-sm rounded-pill bg-white border" style="overflow: hidden;">
                        <span class="input-group-text border-0 bg-white ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none" placeholder="Cari data..." value="{{ request('q') }}" style="width: 200px; font-size: 0.9rem;">
                    </div>
                </form>
                @endif

                <!-- <div class="user-profile ms-2">
                    <button class="btn btn-white shadow-sm border rounded-circle position-relative p-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                </div> -->
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @yield('content')

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            toast: true,
            position: 'top-end'
        });
        @endif
    </script>

    @stack('scripts')

</body>

</html>