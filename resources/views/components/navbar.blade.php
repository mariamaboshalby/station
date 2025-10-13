<nav class="main-navbar navbar navbar-expand">
    <div class="container-fluid">
        <!-- Mobile toggle button -->
        <button class="btn btn-outline-secondary d-lg-none me-2" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="text-center">
            <p>
                <i class="fas fa-coins"></i>
                رأس المال:
                <span class="badge bg-success">{{ number_format($capital, 2) }} جنيه</span>
            </p>
        </div>
        <ul class="navbar-nav ms-auto">

            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="fas fa-home me-1"></i> الرئيسية
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">
                        <i class="fas fa-sign-out-alt me-1"></i> تسجيل الخروج
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
