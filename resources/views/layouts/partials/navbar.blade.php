<nav class="navbar-custom">
    <button type="button" id="sidebarCollapse" class="btn-toggle">
        <i class="bi bi-list"></i>
    </button>
    
    <div class="user-profile">
        <div class="text-end me-3 d-none d-sm-block">
            <p class="name mb-0">{{ Auth::user()->name }}</p>
            <p class="role mb-0 text-muted text-uppercase" style="font-size: 10px;">{{ Auth::user()->role }}</p>
        </div>
        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=006a4e&color=fff" alt="User">
    </div>
</nav>