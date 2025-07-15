<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

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
            font-family: 'Poppins', sans-serif;
            display: flex;
            background: url("../images/coverwel2.png") no-repeat center center/cover;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 110%;
        background: rgba(32, 32, 32, 0.5);
        z-index: -1;
      }
    /* Sidebar */
    .sidebar {
            width: 270px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            position: fixed;
            left: -270px; /* Initially hidden */
            top: 0;
            height: 100%;
            transition: left 0.4s ease-in-out;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-radius: 0 20px 20px 0;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        /* Back Button */
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px;
            border: none;
            font-size: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s;
            width: 100%;
            margin-bottom: 20px;
            text-align: left;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
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

        .profile-name {
            font-weight: bold;
            font-size: 1rem;
            color: white;
        }

        .nav-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.3s, transform 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        /* Logout Button */
        .logout-btn {
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            background: rgba(255, 0, 0, 0.6);
            border: none;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 0, 0, 0.8);
            transform: scale(1.05);
        }

        /* Hamburger Button */
        .hamburger {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #333;
            color: white;
            padding: 12px;
            border: none;
            font-size: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s;
            z-index: 1001;
        }

        .hamburger:hover {
            background: #555;
            transform: scale(1.05);
        }

        /* Main Content */
         /* Main Content */
         .main-content {
            flex: 1;
            padding: 40px;
            color: white;
            margin-left: 0;
            transition: margin-left 0.3s ease;
            width: 100%;
            
        }

        .sidebar.active ~ .main-content {
            margin-left: 290px;
        }

    /* Main Content */
    .main-content {
      flex: 1;
      background-color: #fff;
      padding: 16px;
      display: flex;
      flex-direction: column;
      position: relative;
      margin-left: 60px;
      background: none;
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
    <aside class="sidebar" id="sidebar">
    <button class="back-btn" onclick="toggleSidebar()">← Close Dashboard</button>

        <div class="profile-section">
            <div class="profile-icon">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A9 9 0 1118.88 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <a href="{{ route('profile.edit') }}" class="profile-link">MY PROFILE</a>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span>DASHBOARD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span>PLASTIC ANALYSIS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span>NON-PLASTIC ANALYSIS</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="logout-btn">
            @csrf
            <button type="submit" class="logout-btn">
                <span>LOGOUT</span>
            </button>
        </form>
    </aside>

    <!-- Hamburger Button -->
    <button class="hamburger" id="hamburger-btn" onclick="toggleSidebar()">☰</button>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header-filter-container">
            <h1>PLASTIC ANALYSIS</h1>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Calendar Section -->
            <div class="calendar-container">
                <h2 class="calendar-title" id="calendar-title">Calendar</h2>
                <table class="calendar-table" id="calendar-table"></table>
            </div>
            <!-- Chart Section -->
            <div class="chart-container">
                <img src="https://via.placeholder.com/300x150?text=Chart+Placeholder" alt="Chart Placeholder" />
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        function generateCalendar(year, month) {
            const calendarTable = document.getElementById("calendar-table");
            const calendarTitle = document.getElementById("calendar-title");

            const monthNames = [
                "January", "February", "March", "April", "May", "June", "July",
                "August", "September", "October", "November", "December"
            ];
            calendarTitle.textContent = monthNames[month] + " " + year;

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
            for (let i = 0; i < 6; i++) {
                const row = document.createElement("tr");
                for (let j = 0; j < 7; j++) {
                    const cell = document.createElement("td");
                    if (i === 0 && j < firstDay) {
                        cell.textContent = "";
                    } else if (date > daysInMonth) {
                        cell.textContent = "";
                    } else {
                        cell.textContent = date;

                        // Chart Popup
                        const popup = document.createElement("div");
                        popup.className = "chart-popup";
                        popup.innerHTML = `<img src="https://via.placeholder.com/250x150?text=Plastic+Counts" alt="Plastic Counts Chart">`;
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

        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let hamburger = document.getElementById("hamburger-btn");

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
