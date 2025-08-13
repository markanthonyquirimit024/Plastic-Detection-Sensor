@include('layout.base')
<link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
<title>Dashboard</title>

<div class="container py-5">

    <!-- Header -->
    <header>
        <h1>♻ PLASTIC & NON-PLASTIC ANALYSIS</h1>
        <hr>
    </header>

    <!-- Stats Cards -->
    <div class="row g-4 justify-content-center">
        @auth
        @if(Auth::user()->utype === 'ADM')
        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Total Users</h5>
                    <p class="stat-number text-highlight">{{ $userCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        @endif
        @endauth

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Plastic Detected</h5>
                    <h5 class="card-title">Entrance 1</h5>
                    <p class="stat-number text-highlight">{{ $plasticCount ?? 124 }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Plastic Detected</h5>
                    <h5 class="card-title">Entrance 2</h5>
                    <p class="stat-number text-highlight">{{ $nonPlasticCount ?? 86 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="mt-5 chart-card">
        <h5 class="mb-3">Weekly Detection Trends</h5>
        <canvas id="weeklyPlasticChart" height="100"></canvas>
    </div>

    <!-- Calendar -->
    <div class="calendar-container">
        <h2 class="calendar-title" id="calendar-title">Calendar</h2>
        <table class="calendar-table" id="calendar-table"></table>
    </div>
</div>

<div id="hover-popup"></div>

<script>
    // Chart
    const ctx = document.getElementById('weeklyPlasticChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                {
                    label: 'Plastic Detections',
                    data: [45, 60, 38, 72],
                    borderColor: '#4fd1c5',
                    backgroundColor: 'rgba(79, 209, 197, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Non-Plastic Detections',
                    data: [30, 40, 50, 33],
                    borderColor: '#ff6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: 'white' } }
            },
            scales: {
                x: { ticks: { color: 'white' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: 'white' }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        }
    });

    // Calendar generation
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

    // Hover popup
    const popup = document.getElementById("hover-popup");
    document.addEventListener("mousemove", (e) => {
        const cells = document.querySelectorAll("td[data-day]");
        let found = false;
        const todayDateOnly = new Date(today);
        todayDateOnly.setHours(0, 0, 0, 0);

        cells.forEach(cell => {
            const day = parseInt(cell.getAttribute("data-day"));
            const data = dailyAnalytics[day];
            const cellDate = new Date(today.getFullYear(), today.getMonth(), day);
            cellDate.setHours(0, 0, 0, 0);

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
        if (!found) popup.style.display = "none";
    });
</script>
