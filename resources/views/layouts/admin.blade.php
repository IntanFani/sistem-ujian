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
            <li>
                <a href="#"><i class="bi bi-people"></i> Data Siswa</a>
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