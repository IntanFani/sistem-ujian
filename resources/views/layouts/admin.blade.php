<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | CBT MTs Al Huda</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">

    <style>
        /* --- FITUR STICKY SIDEBAR & NAVBAR (VERSI AMPUH) --- */
        body {
            overflow: hidden !important; 
            height: 100vh !important;
            margin: 0;
        }

        .wrapper {
            display: flex !important;
            height: 100vh !important;
            width: 100vw !important;
            overflow: hidden !important;
        }

        #sidebar {
            height: 100vh !important;
            overflow-y: auto !important;
            flex-shrink: 0 !important;
        }

        #content {
            display: flex !important;
            flex-direction: column !important;
            flex-grow: 1 !important;
            height: 100vh !important;
            overflow: hidden !important;
            /* Pastikan width menyesuaikan agar tidak melebar keluar layar */
            width: 0 !important; 
            min-width: 0 !important;
        }

        /* Mengunci Navbar (elemen PERTAMA di dalam #content) */
        #content > :first-child {
            flex-shrink: 0 !important;
            z-index: 1000 !important;
        }

        /* Area Konten Utama yang BISA di-scroll (elemen KEDUA di dalam #content) */
        #content > .container-fluid {
            flex-grow: 1 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            height: 100% !important;
        }

        /* Sembunyikan scrollbar bawaan agar lebih rapi (Opsional) */
        #content > .container-fluid::-webkit-scrollbar,
        #sidebar::-webkit-scrollbar { 
            width: 6px; 
        }
        #content > .container-fluid::-webkit-scrollbar-thumb,
        #sidebar::-webkit-scrollbar-thumb { 
            background-color: rgba(0,0,0,0.15); 
            border-radius: 10px; 
        }

        /* --- Style untuk Overlay Gelap (mirip panel siswa) --- */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1045; /* Di bawah sidebar, di atas konten lain */
            display: none; 
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* --- Logika Floating Khusus Layar HP/Tablet --- */
        @media (max-width: 992px) {
            #sidebar {
                position: fixed !important; /* Paksa jadi elemen melayang */
                top: 0;
                left: -300px; /* Sembunyikan jauh ke kiri layar */
                height: 100vh;
                z-index: 1050 !important; /* Harus paling atas */
                transition: left 0.3s ease !important;
                box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            }

            /* Class saat sidebar di-klik / muncul di HP */
            #sidebar.mobile-show {
                left: 0 !important; /* Geser masuk ke layar */
            }

            /* Class saat overlay muncul di HP */
            .sidebar-overlay.mobile-show {
                display: block;
                opacity: 1;
            }

            /* Paksa konten agar tetap lebar penuh dan tidak ikut tergeser */
            #content {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="overlay"></div>

<div class="wrapper">
    @include('layouts.partials.sidebar')

    <div id="content">
        @include('layouts.partials.navbar')

        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </div>
</div>

{{-- Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {

        // Logika Klik Tombol Burger (Sidebar Toggle)
        $('#sidebarCollapse').on('click', function () {
            if ($(window).width() <= 992) {
                $('#sidebar').toggleClass('mobile-show');
                $('#overlay').toggleClass('mobile-show');
            } else {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            }
        });

        // Menutup Sidebar jika Overlay Gelap diklik (hanya jalan di HP)
        $('#overlay').on('click', function() {
            $('#sidebar').removeClass('mobile-show');
            $(this).removeClass('mobile-show');
        });

        // Menutup Sidebar jika Tombol X diklik
        $('#closeSidebarMobile').on('click', function() {
            $('#sidebar').removeClass('mobile-show');
            $('#overlay').removeClass('mobile-show');
        });

        // Mencegah bug tampilan jika pengguna mengubah ukuran layar browser secara drastis
        $(window).resize(function() {
            if ($(window).width() > 992) {
                $('#sidebar').removeClass('mobile-show');
                $('#overlay').removeClass('mobile-show');
            }
        });

        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
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