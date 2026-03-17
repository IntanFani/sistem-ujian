<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | CBT MTs Al Huda</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo">
            <h6 class="fw-bold mb-0">CBT AL-HUDA</h6>
            <small class="text-muted">Administrator Panel</small>
        </div>

        <ul class="list-unstyled components">
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="/admin/dashboard"><i class="bi bi-grid-fill"></i> Dashboard</a>
            </li>
            <li class="{{ request()->is('admin/subjects*') ? 'active' : '' }}">
                <a href="{{ route('admin.subjects.index') }}"> <i class="bi bi-book"></i> Mata Pelajaran</a>
            </li>
            <li class="{{ request()->is('admin/gurus*') ? 'active' : '' }}">
                <a href="{{ route('admin.gurus.index') }}"> <i class="bi bi-people"></i> Manajemen Guru</a>
            </li>
            <li class="nav-item" style="white-space: nowrap;">
                <a class="nav-link {{ request()->is('admin/kelas*', 'admin/siswas*') ? '' : 'collapsed' }} d-flex justify-content-between align-items-center" data-bs-toggle="collapse" 
                    href="#menuSiswa" role="button" aria-expanded="{{ request()->is('admin/kelas*', 'admin/siswas*') ? 'true' : 'false' }}">
                    <span>
                        <i class="bi bi-people-fill"></i>Manajemen Siswa
                    </span>
                    <i class="bi bi-chevron-down small toggle-icon" style="font-size: 11px; margin-right: -5px;"></i>
                </a>
                
                <div class="collapse {{ request()->is('admin/kelas*', 'admin/siswas*') ? 'show' : '' }}" id="menuSiswa">
                    <ul class="nav flex-column ms-4 small sub-menu">
                        <li class="nav-item {{ request()->is('admin/kelas*') ? 'active-sub' : '' }}">
                            <a class="nav-link py-2" href="{{ route('admin.kelas.index') }}">
                                <i class="bi bi-door-open me-2"></i> Data Kelas
                            </a>
                        </li>
                        <li class="nav-item {{ request()->is('admin/siswas*') ? 'active-sub' : '' }}">
                            <a class="nav-link py-2" href="{{ route('admin.siswas.index') }}">
                                <i class="bi bi-person-badge me-2"></i> Data Siswa
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="#"><i class="bi bi-file-earmark-text"></i> Bank Soal</a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </nav>

    <div id="content">
        <nav class="navbar-custom">
            <button type="button" id="sidebarCollapse" class="btn-toggle">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="user-profile">
                <div class="text-end me-3 d-none d-sm-block">
                    <p class="name mb-0">{{ Auth::user()->name }}</p>
                    <p class="role mb-0 text-muted">Admin Sistem</p>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=006a4e&color=fff" alt="User">
            </div>
        </nav>

        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Cek apakah ada session success
        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000, // Muncul selama 3 detik
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif
    });
</script>

@yield('scripts')
</body>
</html>