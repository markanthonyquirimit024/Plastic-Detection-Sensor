<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
     <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

     

    <link rel="stylesheet" href="{{ asset('assets/base.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <button class="back-btn" onclick="toggleSidebar()">← Close</button>
        <div class="profile-section">
            <div class="profile-icon">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A9 9 0 1118.88 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <a href="{{ route('profile.edit') }}" class="profile-link">{{ ucfirst(Str::before(Auth::user()->name, ' ')) }}</a>
        </div>
  
        <!-- Navigation -->
        <nav class="sidebar-nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <span>📊 DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports') }}" class="nav-link">
                        <span>📈 REPORTS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('data-explorer') }}" class="nav-link">
                        <span>🧪 DATA EXPLORER</span>
                    </a>
                </li>
                <li class="nav-item">
                <a class="nav-link account-settings-toggle d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                href="#accountMenu"
                role="button"
                aria-expanded="false"
                aria-controls="accountMenu">
                    <span>⚙️ ACCOUNT SETTINGS</span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse hide" id="accountMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link account-settings-link text-light" href="{{route('profile.edit')}}">Profile</a>
                        </li>
                        @auth
                        @if(Auth::user()->utype === 'ADM')
                        <li class="nav-item">
                            <a class="nav-link account-settings-link text-light" href="{{route('admin.user-management')}}">User Management</a>
                        </li>
                        @endif
                        @endauth
                    </ul>
                </div>
            </li>
            </ul>
        </nav>

        <form method="POST" id="logout-form" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" onclick="event.preventDefault(); if(confirm('Are you sure you want to log out?')) {document.getElementById('logout-form').submit();}">
                🔓 LOGOUT
            </button>
        </form>

    </aside>

    <button class="hamburger" id="hamburger-btn" onclick="toggleSidebar()">☰</button>

    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let hamburger = document.getElementById("hamburger-btn");
            let back_btn = document.getElementsByClassName("back-btn");

            sidebar.classList.toggle("active");

            if (sidebar.classList.contains("active")) {
                hamburger.style.display = "none";
            } else {
                hamburger.style.display = "block";
            }
        }
    </script>
</body>

</html>
