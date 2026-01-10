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
               class="nav-link {{ request()->is('tanks') || request()->is('tanks/create') || request()->is('tanks/*/edit') || request()->is('tanks-report/*') ? 'active' : '' }}">
                <i class="fas fa-gas-pump me-2"></i> التانكات
            </a>
        @endcan

        @can('show users')
            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>  الموظفين
            </a>
        @endcan

        @can('show clients')
            <a href="{{ route('clients.index') }}"
               class="nav-link {{ request()->is('clients*') ? 'active' : '' }}">
                <i class="fa-solid fa-handshake"></i>  العملاء
            </a>
        @endcan

     
        @can('view dashboard')
            <a href="#financeSubmenu" data-bs-toggle="collapse" 
               class="nav-link d-flex justify-content-between align-items-center {{ request()->is('reports*') || request()->is('expenses*') || request()->is('treasury*') ? 'active' : '' }}" 
               role="button" aria-expanded="{{ request()->is('reports*') || request()->is('expenses*') || request()->is('treasury*') ? 'true' : 'false' }}">
               <span>
                   <i class="fas fa-coins me-2"></i> الحسابات والتقارير
               </span>
               <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->is('reports*') || request()->is('expenses*') || request()->is('treasury*') ? 'show' : '' }} bg-black bg-opacity-25" id="financeSubmenu">
                <nav class="nav flex-column ps-3">
                    <a href="{{ route('treasury.index') }}" class="nav-link {{ request()->routeIs('treasury.index') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-wallet text-warning me-2"></i> الصندوق اليومي
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-file-invoice-dollar me-2"></i> كشف الحساب العام
                    </a>
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.index') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-arrow-down text-danger me-2"></i> المصروفات
                    </a>
                    <a href="{{ route('reports.revenues') }}" class="nav-link {{ request()->routeIs('reports.revenues') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-arrow-up text-success me-2"></i> الإيرادات
                    </a>
                </nav>
            </div>
        @endcan

        @can('show tanks')
            <a href="#inventorySubmenu" data-bs-toggle="collapse" 
               class="nav-link d-flex justify-content-between align-items-center {{ request()->is('inventory*') ? 'active' : '' }}" 
               role="button" aria-expanded="{{ request()->is('inventory*') ? 'true' : 'false' }}">
               <span>
                   <i class="fas fa-boxes me-2"></i> الجرد
               </span>
               <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ request()->is('inventory*') ? 'show' : '' }} bg-black bg-opacity-25" id="inventorySubmenu">
                <nav class="nav flex-column ps-3">
                    <a href="{{ route('inventory.monthly.index') }}" class="nav-link {{ request()->routeIs('inventory.monthly.*') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-chart-bar me-2"></i> الجرد الشهري التلقائي
                    </a>
                    <a href="{{ route('inventory.pump.index') }}" class="nav-link {{ request()->routeIs('inventory.pump.*') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-cog me-2"></i> جرد الطلمبات
                    </a>
                    <a href="{{ route('inventory.daily.summary') }}" class="nav-link {{ request()->routeIs('inventory.daily.summary') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-table me-2"></i> اليومي المجمل
                    </a>
                    <a href="{{ route('inventory.pump.report') }}" class="nav-link {{ request()->routeIs('inventory.pump.report') ? 'text-white fw-bold' : 'text-white-50' }} py-2">
                        <i class="fas fa-chart-line me-2"></i> تقارير الطلمبات
                    </a>
                </nav>
            </div>
        @endcan
    </nav>
</div>
