<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('assets/base.css') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.2/firebase-database-compat.js"></script>

<title>@yield('title', 'EcoScan')</title>
</head>

<body>

<!-- Hamburger Button -->
<button class="hamburger" id="hamburgerBtn">
  <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<aside class="sidebar d-flex flex-column p-3" id="sidebar">
  <h2 class="sidebar-logo mb-3">Eco<span class="text-light">Scan</span></h2>

  <!-- Sensor Status -->
  <div class="sensor-status mb-4 text-center">
    <div id="sensorDot" class="sensor-dot offline"></div>
    <p id="sensorText" class="fw-bold text-secondary mb-0">OFF</p>
    <small>Sensor Status</small>
    <p class="mt-1">Legend</p>
    <p class="text-center">🔴 - Off</p>
    <p class="ms-5 text-center">⚪ - Add Count</p>
  </div>

  <nav class="flex-grow-1">
    <ul class="nav flex-column gap-2">
      <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i class="bi bi-house-door-fill me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('reports') }}" class="nav-link sidebar-link {{ request()->routeIs('reports') ? 'active' : '' }}">
          <i class="bi bi-people-fill me-2"></i> Report
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('data-explorer') }}" class="nav-link sidebar-link {{ request()->routeIs('data-explorer') ? 'active' : '' }}">
          <i class="bi bi-shield-lock-fill me-2"></i> Data Explorer
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('profile.edit') }}" class="nav-link sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
          <i class="bi bi-person-circle me-2"></i> Profile
        </a>
      </li>
      @auth
        @if(Auth::user()->utype === 'Admin')
          <li class="nav-item">
            <a href="{{ route('admin.user-management') }}" class="nav-link sidebar-link {{ request()->routeIs('admin.user-management') ? 'active' : '' }}">
              <i class="bi bi-people-fill me-2"></i> User Management
            </a>
          </li>
        @endif
      @endauth
      <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
          @csrf
          <button type="button" class="nav-link sidebar-link text-danger w-100 text-start bg-danger text-light" onclick="confirmLogout(event)">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
      </li>
    </ul>
  </nav>

  <div class="sidebar-footer mt-auto text-center">
    <img src="{{ asset('images/logo.png') }}" alt="Support" class="img-fluid">
  </div>
</aside>

<!-- Main Content -->
<main class="content" id="mainContent">
  @yield('content')
</main>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-database-compat.js"></script>

<script>
// Logout Notification
  function confirmLogout(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your account.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}
</script>

<script>
const sensorDot = document.getElementById('sensorDot');
const sensorText = document.getElementById('sensorText');
let offlineTimeout;

const firebaseConfig = {
  apiKey: "AIzaSyDx7HErgazhqZq-rzJIM-4nFhMUA5byDzY",
  authDomain: "plastic-sensor.firebaseapp.com",
  databaseURL: "https://plastic-sensor-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "plastic-sensor",
  storageBucket: "plastic-sensor.appspot.com",
  messagingSenderId: "973658571653",
  appId: "1:973658571653:web:ba344e62c400e993f5baec"
};
firebase.initializeApp(firebaseConfig);
const database = firebase.database();

function setSensorStatus(isOnline) {
  if (isOnline) {
    sensorDot.classList.remove('offline');
    sensorDot.classList.add('online');
    sensorText.textContent = 'ON';
    sensorText.classList.remove('text-secondary');
    sensorText.classList.add('text-success');
  } else {
    sensorDot.classList.remove('online');
    sensorDot.classList.add('offline');
    sensorText.textContent = 'OFF';
    sensorText.classList.remove('text-success');
    sensorText.classList.add('text-secondary');
  }
}

function hasDetected(data) {
  if (!data) return false;
  return Object.values(data).some(value => value === "Detected");
}

const obstacleRef = database.ref('logs/Obstacle');
obstacleRef.on('value', snapshot => {
  const data = snapshot.val();
  if (hasDetected(data)) {
    setSensorStatus(true);
    clearTimeout(offlineTimeout);
    offlineTimeout = setTimeout(() => setSensorStatus(false), 5000);
  }
});
obstacleRef.on('child_added', snapshot => {
  if (snapshot.val() === "Detected") {
    setSensorStatus(true);
    clearTimeout(offlineTimeout);
    offlineTimeout = setTimeout(() => setSensorStatus(false), 5000);
  }
});

// Hamburger toggle
const hamburgerBtn = document.getElementById('hamburgerBtn');
const sidebar = document.getElementById('sidebar');

hamburgerBtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');
});
</script>

</body>
</html>
