<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/base.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column p-3">
        <!-- Brand -->
        <h2 class="sidebar-logo mb-4">Eco<span class="text-light">Scan</span></h2>

        <!-- Navigation -->
        <nav class="flex-grow-1">
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill me-2"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('reports') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> User Control
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('data-explorer') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('data-explorer') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill me-2"></i> Access Request
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-person-circle me-2"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.user-management') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('admin.user-management') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> User Management
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link sidebar-link text-danger w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Bottom illustration -->
        <div class="sidebar-footer mt-auto text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Support" class="img-fluid" style="max-width: 100px;">
        </div>
    </aside>

    <!-- Main Content -->
    <main class="content">
        @yield('content')
    </main>
</body>
</html>
