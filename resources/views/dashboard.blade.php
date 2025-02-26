<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Dashboard</title>
  <style>
    /* Utility CSS */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      background-color: #7D8B60;
      min-height: 100vh;
      display: flex;
    }
    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #f3f4f6;
      display: flex;
      flex-direction: column;
      padding: 16px;
    }
    .profile-section {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .profile-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #c8a2c8;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
    }
    .profile-icon svg {
      width: 16px;
      height: 16px;
    }
    .profile-name {
      font-weight: bold;
      font-size: 0.95rem;
    }
    /* Sidebar nav */
    .sidebar-nav {
      flex: 1;
    }
    .nav-item {
      margin-bottom: 8px;
    }
    .nav-link {
      display: flex;
      align-items: center;
      text-decoration: none;
      color: #333;
      padding: 6px;
      border-radius: 4px;
      font-size: 0.85rem;
    }
    .nav-link:hover {
      background-color: #e5e7eb;
    }
    .nav-icon {
      width: 16px;
      height: 16px;
      margin-right: 6px;
    }
    /* Logout */
    .logout-btn {
      display: flex;
      align-items: center;
      color: #b91c1c;
      text-decoration: none;
      padding: 6px;
      border-radius: 4px;
      margin-top: auto;
      font-size: 0.85rem;
    }
    .logout-btn:hover {
      background-color: #e5e7eb;
    }
    .logout-btn .nav-icon {
      width: 16px;
      height: 16px;
      margin-right: 6px;
    }
    /* Main Content */
    .main-content {
      flex: 1;
      background-color: #fff;
      padding: 16px;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    .header-filter-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }
    .header-filter-container h1 {
      font-size: 1.25rem;
      font-weight: bold;
      color: #4b5563;
      margin-right: 16px;
    }
    .filter-group {
      display: flex;
      align-items: center;
    }
    .filter-label {
      font-weight: 600;
      color: #4b5563;
      margin-right: 8px;
      font-size: 0.85rem;
    }
    .filter-input {
      border: 1px solid #d1d5db;
      border-radius: 4px;
      padding: 4px 6px;
      font-size: 0.85rem;
    }
    /* Dashboard Grid */
    .dashboard-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 16px;
    }
    @media (min-width: 1024px) {
      .dashboard-grid {
        grid-template-columns: 1fr 1fr;
      }
    }
    /* Calendar Section */
    /* Increase calendar container size */
.calendar-container {
  background-color: #7D8B60;
  color: #fff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 30px; /* Increased padding */
  position: relative;
  width: 100%; /* Make it take full width */
  max-width: 1000px; /* Increase max width */
}

/* Increase calendar title size */
.calendar-title {
  font-size: 1.5rem; /* Larger text */
  font-weight: bold;
  margin-bottom: 12px;
}

/* Increase calendar table size */
.calendar-table {
  width: 100%;
  text-align: center;
  background-color: #fff;
  color: #000;
  border-radius: 4px;
  border-collapse: collapse;
  font-size: 1rem; /* Increased font size */
}

/* Increase cell padding and font size */
.calendar-table th,
.calendar-table td {
  padding: 35px; /* Increased padding */
  border: 1px solid #e5e7eb;
  font-size: 1rem; /* Larger text */
}

/* Increase header size */
.calendar-table thead {
  background-color: #f3f4f6;
  font-size: 1.1rem; /* Slightly larger text */
}

  /* Popup Chart */
.chart-popup {
  display: none;
  position: absolute;
  background-color: #fff;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  padding: 8px;
  z-index: 10;
  transform: translate(-50%, -100%); /* Center above cursor */
  white-space: nowrap;
}

/* Show popup on hover */
.calendar-table td {
  position: relative;
}

.calendar-table td:hover .chart-popup {
  display: block;
  position: absolute;
  top: -10px; /* Adjust to align above */
  left: 50%;
  transform: translateX(-50%);
}

    /* Chart Section (for overall dashboard chart if needed) */
    .chart-container {
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .chart-container img {
      max-width: 100%;
      height: auto;
      display: block;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="profile-section">
      <div class="profile-icon">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A9 9 0 1118.88 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      
      <a href="{{ route('profile.edit') }}" class="profile-link">MY PROFILE</a>
      </div>
    <!-- Navigation -->
    <nav class="sidebar-nav">
      <ul>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7h18M3 12h18M3 17h18" />
            </svg>
            <span>DASHBOARD</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h18v18H3V3z" />
            </svg>
            <span>PLASTIC ANALYSIS</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7h14z" />
            </svg>
            <span>NON-PLASTIC ANALYSIS</span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}" class="logout-btn">
    @csrf
    <button type="submit" class="logout-btn" style="background: none; border: none; display: flex; align-items: center; color: inherit; cursor: pointer;">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-6V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h6a2 2 0 002-2v-1" />
        </svg>
        <span>LOGOUT</span>
    </button>
</form>

  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Heading & Filter Section -->
    <div class="header-filter-container">
      <h1>PLASTIC ANALYSIS</h1>
      <div class="filter-group">
        <label class="filter-label">FILTER:</label>
        <input type="text" placeholder="Filter..." class="filter-input" />
      </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
      <!-- Calendar Section -->
      <div class="calendar-container">
        <h2 class="calendar-title" id="calendar-title">Calendar</h2>
        <table class="calendar-table" id="calendar-table">
          <!-- Calendar will be generated by JavaScript -->
        </table>
      </div>
      <!-- Chart Section -->
      <div class="chart-container">
        <img src="https://via.placeholder.com/300x150?text=Chart+Placeholder" alt="Chart Placeholder" />
      </div>
    </div>
  </main>

  <!-- JavaScript to auto-generate the calendar and attach hover popups -->
  <script>
    function generateCalendar(year, month) {
      const calendarTable = document.getElementById("calendar-table");
      const calendarTitle = document.getElementById("calendar-title");

      const monthNames = [
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"
      ];
      calendarTitle.textContent = monthNames[month] + " " + year;

      // Clear existing table content
      calendarTable.innerHTML = "";

      // Create header for days of week
      const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      const thead = document.createElement("thead");
      const headerRow = document.createElement("tr");
      days.forEach(day => {
        const th = document.createElement("th");
        th.textContent = day;
        headerRow.appendChild(th);
      });
      thead.appendChild(headerRow);
      calendarTable.appendChild(thead);

      // Create body for dates
      const tbody = document.createElement("tbody");
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      let date = 1;
      for (let i = 0; i < 6; i++) { // up to 6 rows
        const row = document.createElement("tr");
        for (let j = 0; j < 7; j++) {
          const cell = document.createElement("td");
          if (i === 0 && j < firstDay) {
            cell.textContent = "";
          } else if (date > daysInMonth) {
            cell.textContent = "";
          } else {
            cell.textContent = date;
            // Create a hidden chart popup inside each cell
            const popup = document.createElement("div");
            popup.className = "chart-popup";
            // Use your own chart logic or replace the image source below
            const img = document.createElement("img");
            img.src = "https://via.placeholder.com/250x150?text=Plastic+Counts";
            img.alt = "Plastic Counts Chart";
            popup.appendChild(img);
            cell.appendChild(popup);
            date++;
          }
          row.appendChild(cell);
        }
        tbody.appendChild(row);
        if (date > daysInMonth) break;
      }
      calendarTable.appendChild(tbody);
    }

    const today = new Date();
    generateCalendar(today.getFullYear(), today.getMonth());
  </script>
</body>
</html>
