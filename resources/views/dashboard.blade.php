<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: radial-gradient(circle, rgba(114, 114, 114, 1) 0%, rgba(9, 121, 54, 1) 92%);
      color: white;
      min-height: 100vh;
      overflow-x: hidden;
    }
    .sidebar {
      width: 270px;
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(14px);
      border-right: 1px solid rgba(255,255,255,0.1);
      position: fixed;
      left: -270px;
      top: 0;
      height: 100%;
      transition: left 0.3s ease-in-out;
      padding: 20px;
      z-index: 1000;
      display: flex;
      flex-direction: column;
    }
    .sidebar.active { left: 0; }
    .hamburger {
      position: fixed;
      top: 20px;
      left: 20px;
      background: #ccc;
      color: #fff;
      padding: 10px 16px;
      border: none;
      font-size: 20px;
      border-radius: 8px;
      cursor: pointer;
      z-index: 1001;
    }
    .back-btn {
      background: rgba(255,255,255,0.15);
      color: white;
      padding: 10px;
      border: none;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      margin-bottom: 20px;
      width: 100%;
      text-align: left;
      transition: background 0.2s;
    }
    .back-btn:hover { background: rgba(255,255,255,0.25); }
    .profile-section {
      display: flex;
      align-items: center;
      margin-bottom: 30px;
    }
    .profile-icon {
      width: 40px; height: 40px; border-radius: 50%;
      background-color: #8e44ad;
      display: flex; align-items: center; justify-content: center;
      margin-right: 10px;
    }
    .profile-link, .nav-link, .logout-btn {
      color: white;
      text-decoration: none;
    }
    .nav-link {
      display: block;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 10px;
      background: rgba(255,255,255,0.08);
      transition: background 0.2s;
    }
    .nav-link:hover { background: rgba(255,255,255,0.15); }
    .logout-btn {
      background: #e74c3c;
      padding: 12px;
      border: none;
      border-radius: 10px;
      margin-top: auto;
      cursor: pointer;
      color: white;
      font-weight: 600;
      transition: background 0.2s;
    }
    .logout-btn:hover { background: #c0392b; }
    .main-content {
      padding: 40px;
      transition: margin-left 0.3s ease;
      margin-left: 0;
    }
    .sidebar.active ~ .main-content {
      margin-left: 270px;
    }
    .header-filter-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      margin-left: 40px;
    }
    .header-filter-container h1 {
      font-size: 2rem;
      font-weight: 700;
    }
    .count-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }
    .count-card {
      flex: 1;
      min-width: 260px;
      background: rgba(52, 52, 52, 0.08);
      padding: 24px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
    .count-title {
      font-size: 1.2rem;
      margin-bottom: 10px;
      color: #ccc;
    }
    .count-value {
      font-size: 2.8rem;
      font-weight: bold;
      color: #ccc;
    }
    .chart-container {
      background: #f5f5f510;
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      margin-bottom: 40px;
    }
    .calendar-container {
      background: rgba(255,255,255,0.08);
      border-radius: 16px;
      padding: 30px;
    }
    .calendar-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 20px;
    }
    .calendar-table {
      width: 100%;
      text-align: center;
      background-color: #fff;
      color: #000;
      border-radius: 6px;
      border-collapse: collapse;
    }
    .calendar-table th, .calendar-table td {
      padding: 20px;
      border: 1px solid #ddd;
      position: relative;
    }
    .calendar-table thead {
      background-color: #f0f0f0;
    }
    #hover-popup {
      display: none;
      position: absolute;
      background-color: #fff;
      color: #000;
      padding: 10px;
      border-radius: 8px;
      font-size: 14px;
      pointer-events: none;
      z-index: 9999;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>

  <button class="hamburger" id="hamburger-btn" onclick="toggleSidebar()">☰</button>

  <aside class="sidebar" id="sidebar">
    <button class="back-btn" onclick="toggleSidebar()">← Close</button>
    <div class="profile-section">
      <div class="profile-icon">👤</div>
      <a href="{{ route('profile.edit') }}" class="profile-link">MY PROFILE</a>
    </div>
    <nav>
      <a href="#" class="nav-link">DASHBOARD</a>
      <a href="#" class="nav-link">PLASTIC ANALYSIS</a>
      <a href="#" class="nav-link">NON-PLASTIC ANALYSIS</a>
    </nav>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="logout-btn">LOGOUT</button>
    </form>
  </aside>

  <main class="main-content" id="main">
    <div class="header-filter-container">
      <h1>PLASTIC & NON-PLASTIC ANALYSIS</h1>
    </div>

    <div class="count-container">
      <div class="count-card">
        <div class="count-title">Plastic Detected</div>
        <div class="count-value">{{ $plasticCount ?? 124 }}</div>
      </div>
      <div class="count-card">
        <div class="count-title">Non-Plastic Detected</div>
        <div class="count-value">{{ $nonPlasticCount ?? 86 }}</div>
      </div>
    </div>

    <div class="chart-container">
      <canvas id="weeklyPlasticChart" width="600" height="300"></canvas>
    </div>

    <div class="calendar-container">
      <h2 class="calendar-title" id="calendar-title">Calendar</h2>
      <table class="calendar-table" id="calendar-table"></table>
    </div>
  </main>

  <div id="hover-popup"></div>

  <!-- Only the JavaScript part is shown for brevity -->
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const hamburger = document.getElementById("hamburger-btn");
    sidebar.classList.toggle("active");
    hamburger.style.display = sidebar.classList.contains("active") ? "none" : "block";
  }

  const ctx = document.getElementById('weeklyPlasticChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
      datasets: [
        {
          label: 'Plastic Detections',
          data: [45, 60, 38, 72],
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.3,
          pointRadius: 5
        },
        {
          label: 'Non-Plastic Detections',
          data: [30, 40, 50, 33],
          borderColor: 'rgba(255, 99, 132, 1)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.3,
          pointRadius: 5
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
          labels: { color: '#fff' }
        }
      },
      scales: {
        x: { ticks: { color: '#fff' }, grid: { color: '#444' } },
        y: { beginAtZero: true, ticks: { color: '#fff' }, grid: { color: '#444' } }
      }
    }
  });

  const today = new Date();
  const dailyAnalytics = {};
  const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();

  for (let i = 1; i <= daysInMonth; i++) {
    const date = new Date(today.getFullYear(), today.getMonth(), i);
    if (date <= today) {
      dailyAnalytics[i] = {
        plastic: Math.floor(Math.random() * 20),
        nonPlastic: Math.floor(Math.random() * 20)
      };
    }
  }

  function generateCalendar(year, month) {
    const table = document.getElementById("calendar-table");
    const title = document.getElementById("calendar-title");
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    title.textContent = `${monthNames[month]} ${year}`;
    table.innerHTML = "";

    const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const thead = document.createElement("thead");
    const headerRow = document.createElement("tr");
    days.forEach(day => {
      const th = document.createElement("th");
      th.textContent = day;
      headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    let date = 1;

    for (let i = 0; i < 6; i++) {
      const row = document.createElement("tr");
      for (let j = 0; j < 7; j++) {
        const cell = document.createElement("td");
        if (i === 0 && j < firstDay || date > daysInMonth) {
          cell.textContent = "";
        } else {
          cell.textContent = date;
          cell.setAttribute("data-day", date);
          date++;
        }
        row.appendChild(cell);
      }
      tbody.appendChild(row);
      if (date > daysInMonth) break;
    }
    table.appendChild(tbody);
  }

  generateCalendar(today.getFullYear(), today.getMonth());

  const popup = document.getElementById("hover-popup");

  document.addEventListener("mousemove", (e) => {
    const cells = document.querySelectorAll("td[data-day]");
    let found = false;

    const todayDateOnly = new Date(today);
    todayDateOnly.setHours(0, 0, 0, 0); // Reset time to compare dates only

    cells.forEach(cell => {
      const day = parseInt(cell.getAttribute("data-day"));
      const data = dailyAnalytics[day];

      const cellDate = new Date(today.getFullYear(), today.getMonth(), day);
      cellDate.setHours(0, 0, 0, 0); // Reset cell date to compare date only

      if (data && cellDate <= todayDateOnly) {
        const rect = cell.getBoundingClientRect();
        const dx = e.clientX - (rect.left + rect.width / 2);
        const dy = e.clientY - (rect.top + rect.height / 2);
        const distance = Math.sqrt(dx * dx + dy * dy);

        if (distance < 100) {
          popup.innerHTML = `
            <strong>Day ${day}</strong><br>
            🟢 Plastic: <b>${data.plastic}</b><br>
            🔵 Non-Plastic: <b>${data.nonPlastic}</b>
          `;
          popup.style.left = `${e.pageX + 15}px`;
          popup.style.top = `${e.pageY - 50}px`;
          popup.style.display = "block";
          found = true;
        }
      }
    });

    if (!found) {
      popup.style.display = "none";
    }
  });
</script>

</body>
</html>
