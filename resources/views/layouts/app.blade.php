<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Fuel Station') }}</title>

  <!-- Bootstrap RTL -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f4f6f9;
    }
    /* Sidebar */
    .sidebar {
      width: 250px;
      min-height: 100vh;
      background: #343a40;
      color: #fff;
      position: fixed;
      right: 0;
      top: 0;
    }
    .sidebar .nav-link {
      color: #c2c7d0;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background: #495057;
      color: #fff;
    }
    .sidebar .brand {
      font-size: 1.3rem;
      font-weight: bold;
      text-align: center;
      padding: 1rem;
      background: #23272b;
      border-bottom: 1px solid #444;
    }

    /* Main Content */
    .main-content {
      margin-right: 250px; /* نفس عرض السايد بار */
      padding: 0;
    }

    /* Navbar */
    .main-navbar {
      background: #fff;
      border-bottom: 1px solid #ddd;
      padding: 0.75rem 1rem;
    }

    .main-navbar .nav-link {
      color: #333;
    }
  </style>
</head>
<body>
  <div class="wrapper d-flex">

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
      <div class="brand">
        <i class="fas fa-gas-pump"></i> محطة الوقود
      </div>
      <nav class="nav flex-column mt-3">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}">
          <i class="fas fa-exchange-alt me-2"></i> المعاملات
        </a>
        <a href="{{ route('shifts.index') }}" class="nav-link {{ request()->is('shifts*') ? 'active' : '' }}">
          <i class="fas fa-clock me-2"></i> الورديات
        </a>
        <a href="{{ route('tanks.index') }}" class="nav-link {{ request()->is('tanks*') ? 'active' : '' }}">
          <i class="fas fa-gas-pump me-2"></i> تانكات
        </a>
        <a href="#" class="nav-link">
          <i class="fas fa-cogs me-2"></i> الإعدادات
        </a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
      
      <!-- Navbar -->
      <nav class="main-navbar navbar navbar-expand">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link">
              <i class="fas fa-home"></i> الرئيسية
            </a>
          </li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="btn btn-link nav-link">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
              </button>
            </form>
          </li>
        </ul>
      </nav>

      <!-- Content Area -->
      <main class="p-4">
        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
