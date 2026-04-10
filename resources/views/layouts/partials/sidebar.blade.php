<nav id="sidebar" class="d-flex flex-column" style="height: 100vh;">
    <button type="button" id="closeSidebarMobile" class="btn border-0 text-secondary d-lg-none position-absolute shadow-none" style="top: 15px; right: 15px; z-index: 1060;">
        <i class="bi bi-x-lg fs-4"></i>
    </button>
    
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo">
        <h6 class="fw-bold mb-0">CBT AL-HUDA</h6>
        <small class="text-muted text-uppercase">{{ Auth::user()->role }} Panel</small>
    </div>

    {{-- Area Menu yang bisa di-scroll --}}
    <ul class="list-unstyled components flex-grow-1" style="overflow-y: auto; margin-bottom: 0;">
        
        {{-- MENU DASHBOARD (Bisa dilihat keduanya) --}}
        <li class="{{ request()->is('admin/dashboard', 'guru/dashboard') ? 'active' : '' }}">
            <a href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('guru.dashboard') }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>

        {{-- MENU KHUSUS ADMIN --}}
        @if(Auth::user()->role == 'admin')
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
            
            <li class="{{ request()->is('admin/exams*') ? 'active' : '' }}">
                <a href="{{ route('admin.exams.index') }}"> <i class="bi bi-card-checklist"></i> Manajemen Ujian</a>
            </li>
            <li class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                <a href="{{ route('admin.reports.index') }}"> <i class="bi bi-file-earmark-bar-graph"></i> Rekap Nilai</a>
            </li>
        @endif

        {{-- MENU KHUSUS GURU --}}
        @if(Auth::user()->role == 'guru')
            <li class="{{ request()->is('guru/exams*') ? 'active' : '' }}">
                <a href="{{ route('guru.exams.index') }}"><i class="bi bi-calendar-check"></i> Manajemen Ujian</a>
            </li>
            <li class="{{ request()->is('guru/results*') ? 'active' : '' }}">
                <a href="{{ route('guru.results.index') }}"><i class="bi bi-person-check"></i> Hasil Ujian Siswa</a>
            </li>
        @endif
    </ul>

    {{-- KELUAR DI POSISI PALING BAWAH --}}
    {{-- Tambahan pb-lg-3 dan mb-lg-0 akan mengembalikan posisinya ke normal saat di laptop --}}
    <div class="sidebar-footer mt-auto px-3 pt-3 pb-5 pb-lg-3 mb-4 mb-lg-0">
        <hr class="text-secondary opacity-25 mt-0 mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-logout w-100 d-flex align-items-center justify-content-center text-danger fw-bold transition-3d" style="background: rgba(220, 53, 69, 0.1); border-radius: 10px; padding: 10px; border: none;">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </button>
        </form>
    </div>
</nav>