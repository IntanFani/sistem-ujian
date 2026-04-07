<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa - @yield('title')</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #10b981;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 85px;
            --bg-body: #f8fafc;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            /* 1. KUNCI: Matikan scroll global di seluruh body */
            overflow: hidden;
            height: 100vh;
        }

        /* --- SIDEBAR --- */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            transition: var(--transition);
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            height: 80px;
            display: flex;
            align-items: center;
            padding: 0 25px;
            border-bottom: 1px solid #f8fafc;
        }

        .brand-icon {
            min-width: 35px;
            height: 35px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        /* Nav Links */
        .nav-list {
            padding: 15px 0;
            flex-grow: 1;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 5px;
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px 0 0 12px;
            transition: var(--transition);
            white-space: nowrap;
            border-right: 4px solid transparent;
        }

        .nav-link i {
            font-size: 1.25rem;
            min-width: 35px;
        }

        .nav-link span {
            opacity: 1;
            transition: var(--transition);
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-link.active {
            background: #f8fafc;
            color: var(--primary);
            font-weight: 700;
            border-right-color: var(--primary);
        }

        /* --- OVERLAY --- */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1045;
            display: none;
            opacity: 0;
            transition: var(--transition);
        }

        /* --- NAVBAR --- */
        #navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: 80px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 30px;
            transition: var(--transition);
            z-index: 1040;
        }

        .toggle-btn {
            background: #f1f5f9;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        /* --- CONTENT (INI YANG DIROMBAK) --- */
        #main-content {
            /* 2. Jadikan konten sebagai kotak yang terkunci... */
            position: absolute;
            top: 80px; /* Mulai dari bawah navbar */
            left: var(--sidebar-width); /* Mulai dari kanan sidebar */
            right: 0;
            bottom: 0;
            padding: 40px;
            /* 3. ...Lalu biarkan isinya bisa di-scroll ke bawah */
            overflow-y: auto;
            overflow-x: hidden;
            transition: var(--transition);
            scrollbar-width: thin;
        }

        /* Sembunyikan scrollbar bawaan agar rapi */
        #main-content::-webkit-scrollbar { width: 6px; }
        #main-content::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.15); border-radius: 10px; }

        /* --- STATES (Logic Toggle) --- */
        body.sidebar-toggled #sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-toggled #sidebar .nav-link span,
        body.sidebar-toggled #sidebar .brand-text {
            display: none;
        }

        body.sidebar-toggled #navbar {
            left: var(--sidebar-collapsed-width);
        }

        body.sidebar-toggled #main-content {
            /* Geser konten utama ke kiri saat sidebar ditutup */
            left: var(--sidebar-collapsed-width);
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            #sidebar {
                left: calc(var(--sidebar-width) * -1);
            }

            #navbar {
                left: 0 !important;
            }

            #main-content {
                left: 0 !important;
                width: 100%;
                padding: 20px;
            }

            body.mobile-nav-active #sidebar {
                left: 0;
            }

            body.mobile-nav-active .sidebar-overlay {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>

<body id="body-pd">

    <div class="sidebar-overlay" id="overlay"></div>

    <aside id="sidebar">
        <div class="sidebar-brand justify-content-between">
            <div class="d-flex align-items-center">
                {{-- Container Logo Sekolah --}}
                <div class="d-flex align-items-center justify-content-center overflow-hidden"
                    style="width: 40px; height: 40px;">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah" class="img-fluid"
                        style="max-height: 100%; width: auto; object-fit: contain;">
                </div>

                {{-- Teks Portal Siswa --}}
                <span class="fw-bold brand-text ms-2">Portal Siswa</span>
            </div>
            <button class="btn btn-link text-dark d-lg-none p-0" id="close-sidebar">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('siswa.dashboard') }}"
                    class="nav-link {{ request()->is('siswa/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> <span>Beranda</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('siswa.riwayat') }}"
                    class="nav-link {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> <span>Riwayat Ujian</span>
                </a>
            </li>
        </ul>

        <div class="p-4 mt-auto border-top">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 shadow-sm d-flex align-items-center justify-content-center py-2">
                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <header id="navbar">
        <button class="toggle-btn me-3" id="header-toggle">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="ms-auto d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <h6 class="mb-0 fw-bold small text-dark">{{ Auth::user()->name }}</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Kelas
                    {{ Auth::user()->siswa->kelas->nama_kelas ?? '-' }}</small>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff"
                class="rounded-circle shadow-sm border border-2 border-white" width="40" height="40">
        </div>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById('header-toggle');
            const closeBtn = document.getElementById('close-sidebar');
            const overlay = document.getElementById('overlay');
            const body = document.getElementById('body-pd');

            function toggleSidebar() {
                if (window.innerWidth > 992) {
                    body.classList.toggle('sidebar-toggled');
                } else {
                    body.classList.toggle('mobile-nav-active');
                }
            }

            // Pemicu buka/tutup
            if (toggle) toggle.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>

    @yield('scripts')
</body>

</html>