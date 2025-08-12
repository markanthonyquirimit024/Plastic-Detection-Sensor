<!-- the include is for navigation bar -->
@include('layout.base')
<title>Dashboard</title>
<link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">

<body>

  <main class="main-content" id="main">
    <div class="header-filter-container">
      <h1>PLASTIC & NON-PLASTIC ANALYSIS</h1>
    </div>

    <div class="count-container">
      @auth
        @if(Auth::user()->utype === 'ADM')
      <div class="count-card" id="usercount"><div>
        <div class="count-title">Total Users</div>
        </div>
        <div class="count-value">{{ $plasticCount ?? 124 }}</div>
      </div>
        @endif
      @endauth
      <div class="count-card">
        <div class="count-title">Plastic Detected</div>
        <div class="count-title">Entrance 1</div>
        <div class="count-value">{{ $plasticCount ?? 124 }}</div>
    </div>
      <div class="count-card">
        <div class="count-title">Plastic Detected</div>
        <div class="count-title">Entrance 2</div>
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
