@extends('layout.base')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
<title>Dashboard</title>

<div class="dashboard-wrapper d-flex">
    <!-- Main Content -->
    <div class="container-fluid py-5 flex-grow-1 min-vh-100">
        <!-- Header -->
        <header class="mb-4">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted">Plastic Detection Analysis Overview</p>
            <hr>
        </header>

        <div class="row g-4 mb-4">
            @auth
            @if(Auth::user()->utype === 'ADM')
            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6>Total Users</h6>
                        <h3 class="fw-bold text-success">{{ $userCount ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif
            @endauth

            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Button Pressed</h6>
                        <h3 id="plasticCount" class="fw-bold text-danger">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Non-Plastic Count (Reserved)</h6>
                        <h3 id="nonPlasticCount" class="fw-bold text-warning">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Weekly Button Press Trends</h6>
                        <p class="text-muted fst-italic">Auto-updates from Firebase.</p>
                        <canvas id="weeklyPlasticChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Calendar</h6>
                        <p class="text-muted fst-italic">Hover over a date to see total presses.</p>
                        <table class="table table-bordered text-center calendar-table w-100 mb-0" id="calendar-table"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hover popup -->
<div id="hover-popup" class="position-absolute bg-dark text-white p-2 rounded small shadow"
    style="display:none; z-index:1000;"></div>

<!-- Firebase + Chart.js -->
<script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-database-compat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
function initializeDashboard() {
    const firebaseConfig = {
        apiKey: "AIzaSyDx7HErgazhqZq-rzJIM-4nFhMUA5byDzY",
        authDomain: "plastic-sensor.firebaseapp.com",
        databaseURL: "https://plastic-sensor-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "plastic-sensor",
        storageBucket: "plastic-sensor.appspot.com",
        messagingSenderId: "973658571653",
        appId: "1:973658571653:web:ba344e62c400e993f5baec"
    };

    if (typeof firebase === "undefined" || !firebase.apps) {
        console.error("Firebase SDK did not load!");
        return;
    }

    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();
    console.log("✅ Firebase connected");

    // Chart.js setup
    const ctx = document.getElementById('weeklyPlasticChart').getContext('2d');
    const weeklyPlasticChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Button Press Count',
                    data: [],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.2)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#333' } } },
            scales: {
                x: { ticks: { color: '#333' }, grid: { color: 'rgba(0,0,0,0.05)' } },
                y: { beginAtZero: true, ticks: { color: '#333' }, grid: { color: 'rgba(0,0,0,0.05)' } }
            }
        }
    });

    // Store daily counts for calendar
    let fullData = {};

    // Firebase listener
    database.ref('logs/ButtonPress').on('value', snapshot => {
        const data = snapshot.val() || {};
        fullData = data;

        let totalPresses = 0;
        let dailyCounts = {};

        // Loop through each date
        Object.keys(data).forEach(date => {
            const logs = data[date];
            const count = Object.values(logs).filter(v => v === "Pressed").length;
            dailyCounts[date] = count;
            totalPresses += count;
        });

        // Update dashboard
        document.getElementById('plasticCount').textContent = totalPresses;
        document.getElementById('nonPlasticCount').textContent = 0;

        // Update chart with latest 4 days
        const lastDates = Object.keys(dailyCounts).slice(-4);
        const lastValues = lastDates.map(d => dailyCounts[d]);
        weeklyPlasticChart.data.labels = lastDates;
        weeklyPlasticChart.data.datasets[0].data = lastValues;
        weeklyPlasticChart.update();

        // Save daily counts for calendar hover
        fullData = dailyCounts;
    });

    // Calendar
    const today = new Date();
    function generateCalendar(year, month) {
        const table = document.getElementById("calendar-table");
        table.innerHTML = "";
        const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        const thead = document.createElement("thead");
        const headerRow = document.createElement("tr");
        days.forEach(d => {
            const th = document.createElement("th");
            th.textContent = d;
            th.classList.add("p-2");
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
                cell.classList.add("p-2", "calendar-cell");
                if ((i === 0 && j < firstDay) || date > daysInMonth) {
                    cell.textContent = "";
                } else {
                    cell.textContent = date;
                    const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
                    cell.dataset.date = dateStr;

                    // Hover
                    cell.addEventListener('mouseenter', (e) => {
                        const d = e.target.dataset.date;
                        const presses = fullData[d] || 0;
                        const popup = document.getElementById('hover-popup');
                        popup.innerHTML = `Button Pressed: ${presses}`;
                        popup.style.display = "block";
                        popup.style.top = (e.pageY + 10) + "px";
                        popup.style.left = (e.pageX + 10) + "px";
                    });

                    cell.addEventListener('mouseleave', () => {
                        const popup = document.getElementById('hover-popup');
                        popup.style.display = "none";
                    });
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
}

window.addEventListener('load', initializeDashboard);
</script>
@endsection
