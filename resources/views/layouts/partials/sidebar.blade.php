<nav id="sidebar">
    <button type="button" id="closeSidebarMobile" class="btn border-0 text-secondary d-lg-none position-absolute shadow-none" style="top: 15px; right: 15px; z-index: 1060;">
        <i class="bi bi-x-lg fs-4"></i>
    </button>
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo">
        <h6 class="fw-bold mb-0">CBT AL-HUDA</h6>
        <small class="text-muted text-uppercase">{{ Auth::user()->role }} Panel</small>
    </div>

    <ul class="list-unstyled components">
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

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </form>
    </div>
</nav>