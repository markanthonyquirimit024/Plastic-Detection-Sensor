<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/base.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'EcoScan')</title>

    <style>
        /* --- Responsive Sidebar --- */
        .hamburger {
            display: none;
            position: fixed;
            top: 18px;
            left: 20px;
            z-index: 2000;
            background: #1b7b47;
            border: none;
            color: #fff;
            font-size: 1.8rem;
            padding: 8px 12px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .hamburger:hover {
            background: #16663a;
        }

        @media (max-width: 992px) {
            .hamburger { display: block; }

            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                height: 100%;
                width: 250px;
                z-index: 1500;
                transition: all 0.4s ease;
                border-radius: 0 25px 25px 0;
            }

            /* When active: black blur glass */
            .sidebar.active {
                left: 0;
                backdrop-filter: blur(14px);
                background: rgba(0, 0, 0, 0.6);
                box-shadow: 4px 0 25px rgba(0,0,0,0.4);
            }

            .sidebar.active .sidebar-logo {
                color: #ffd700;
            }

            .sidebar.active .sidebar-link {
                color: #f8f9fa;
                transition: all 0.3s ease;
            }

            .sidebar.active .sidebar-link:hover {
                background: rgba(255,255,255,0.12);
                box-shadow: 0 0 12px rgba(255,255,255,0.1);
                transform: translateX(4px);
            }

            .content {
                margin-left: 0;
                padding-top: 70px;
            }
        }

        /* Optional smooth content shadow on mobile open */
        .sidebar.active + .content {
            filter: brightness(0.85);
            transition: filter 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Hamburger Button -->
    <button class="hamburger" id="hamburgerBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column p-3" id="sidebar">
        <h2 class="sidebar-logo mb-4">Eco<span class="text-light">Scan</span></h2>

        <nav class="flex-grow-1">
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill me-2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('reports') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('reports') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Report
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('data-explorer') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('data-explorer') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill me-2"></i> Data Explorer
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" 
                       class="nav-link sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-person-circle me-2"></i> Profile
                    </a>
                </li>

                @auth
                    @if(Auth::user()->utype === 'ADM')
                        <li class="nav-item">
                            <a href="{{ route('admin.user-management') }}" 
                               class="nav-link sidebar-link {{ request()->routeIs('admin.user-management') ? 'active' : '' }}">
                                <i class="bi bi-people-fill me-2"></i> User Management
                            </a>
                        </li>
                    @endif
                @endauth

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link sidebar-link text-danger w-100 text-start" onclick="return confirm('Are you sure you want to logout this account?')">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer mt-auto text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Support" class="img-fluid" style="max-width: 100px;">
        </div>
    </aside>

    <!-- Main Content -->
    <main class="content">
        @yield('content')
    </main>

    <!-- Toggle Script -->
    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');

        hamburgerBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            hamburgerBtn.classList.toggle('open');

            if (hamburgerBtn.classList.contains('open')) {
                hamburgerBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
            } else {
                hamburgerBtn.innerHTML = '<i class="bi bi-list"></i>';
            }
        });
    </script>
</body>
</html>
