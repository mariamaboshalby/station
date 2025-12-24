<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Fuel Station</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts - Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            color: #333;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background: #222222;
            color: #fff;
            transition: all 0.3s ease-in-out;
            width: var(--sidebar-width);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            position: fixed;
            height: 100vh;
            right: 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 20px;
            border-radius: 0;
            border-right: 3px solid transparent;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-right: 3px solid #fff;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: 500;
        }

        .sidebar .brand {
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
            padding: 1.5rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .brand i {
            margin-left: 10px;
            color: #ffc107;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease-in-out;
            width: 100%;
            margin-right: 0;
            padding-right: var(--sidebar-width);
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                padding-right: 0;
            }

            .main-content.shifted {
                width: 100%;
            }
        }

        /* Responsive Tables */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            
            h1, h2, h3 {
                font-size: 1.5rem !important;
            }
            
            .col-md-2, .col-md-3, .col-md-4 {
                margin-bottom: 1rem;
            }
        }

        /* Navbar */
        .main-navbar {
            background: #fff;
            border-bottom: 1px solid #e1e5eb;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .main-navbar .navbar-nav .nav-link {
            color: #343a40;
            font-weight: 500;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }
    </style>

</head>

<body>
    <div class="layout">
        @canany([
            'add user',
            'show users',
            'open shift',
            'show shifts',
            'close shift',
            'add transaction',
            'show
            transaction',
            'add tank',
            'edit tank',
            'show tanks',
            'view dashboard',
            ])
            <!-- Sidebar -->
            <x-sidebar />
            <!-- Overlay -->
            <x-overlay />
            <!-- Main Content -->
            <div class="main-content" id="mainContent">

                <!-- Navbar -->
                <x-navbar />

                <!-- Content Area -->
                <main class="p-4 flex-grow-1">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </main>
            </div>
        @endcanany
    </div>

    {{-- نفس السكريبتات --}}
</body>
<script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay = document.getElementById('overlay');

    // Toggle sidebar on mobile
    toggleBtn?.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('shifted');
        overlay.classList.toggle('active');
    });

    // Close sidebar when clicking on overlay
    overlay?.addEventListener('click', function() {
        sidebar.classList.remove('active');
        mainContent.classList.remove('shifted');
        overlay.classList.remove('active');
    });

    // Close sidebar when clicking on a link (mobile)
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('active');
                mainContent.classList.remove('shifted');
                overlay.classList.remove('active');
            }
        });
    });

    // Close sidebar when window is resized to desktop size
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            mainContent.classList.remove('shifted');
            overlay.classList.remove('active');
        }
    });
</script>
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Laravel stack -->
@stack('script')
</body>

</html>
