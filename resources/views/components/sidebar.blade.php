<div class="sidebar d-flex flex-column min-vh-100" id="sidebar">
    <div class="brand">
        <i class="fas fa-gas-pump"></i> محطة الوقود
    </div>
    <nav class="nav flex-column mt-3">
        @can('view dashboard')
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
            </a>
        @endcan

        @can('show transaction')
            <a href="{{ route('transactions.index') }}"
               class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt me-2"></i> المعاملات
            </a>
        @endcan

        @can('show shifts')
            <a href="{{ route('shifts.index') }}"
               class="nav-link {{ request()->is('shifts*') ? 'active' : '' }}">
                <i class="fas fa-clock me-2"></i> الورديات
            </a>
        @endcan

        @can('show tanks')
            <a href="{{ route('tanks.index') }}"
               class="nav-link {{ request()->is('tanks*') ? 'active' : '' }}">
                <i class="fas fa-gas-pump me-2"></i> التانكات
            </a>
        @endcan

        @can('show users')
            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>  الموظفين
            </a>
        @endcan

        {{-- @can('show clients') --}}
            <a href="{{ route('clients.index') }}"
               class="nav-link {{ request()->is('clients*') ? 'active' : '' }}">
                <i class="fa-solid fa-handshake"></i>  العملاء
            </a>
        {{-- @endcan --}}
    </nav>
</div>
