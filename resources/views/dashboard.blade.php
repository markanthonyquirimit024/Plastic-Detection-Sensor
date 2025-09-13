@extends('layout.base')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
<title>Dashboard</title>

<div class="dashboard-wrapper d-flex">
    <!-- Main Content -->
    <div class="container-fluid py-5 flex-grow-1 bg-white min-vh-100">
        <!-- Header -->
        <header class="mb-4">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted">Plastic & Non-Plastic Analysis Overview</p>
            <hr>
        </header>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            @auth
            @if(Auth::user()->utype === 'ADM')
            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Users</h6>
                        <h3 class="fw-bold text-success">{{ $userCount ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif
            @endauth

            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Plastic Detected - Entrance 1</h6>
                        <h3 class="fw-bold text-danger">{{ $plasticCount ?? 124 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Plastic Detected - Entrance 2</h6>
                        <h3 class="fw-bold text-warning">{{ $nonPlasticCount ?? 86 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart + Calendar -->
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Weekly Detection Trends</h6>
                        <canvas id="weeklyPlasticChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Calendar</h6>
                        <table class="table table-bordered text-center calendar-table w-100 mb-0" id="calendar-table"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hover Popup -->
<div id="hover-popup" class="position-absolute bg-dark text-white p-2 rounded small shadow" style="display:none; z-index:1000;"></div>

<script>
    // ================== Chart ==================
    const ctx = document.getElementById('weeklyPlasticChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                {
                    label: 'Plastic Detections',
                    data: [45, 60, 38, 72],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Non-Plastic Detections',
                    data: [30, 40, 50, 33],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
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
                y: { ticks: { color: '#333' }, grid: { color: 'rgba(0,0,0,0.05)' } }
            }
        }
    });

    // ================== Calendar ==================
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
        table.innerHTML = "";

        const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        const thead = document.createElement("thead");
        const headerRow = document.createElement("tr");
        days.forEach(day => {
            const th = document.createElement("th");
            th.textContent = day;
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

    // ================== Accurate Hover ==================
    const popup = document.getElementById("hover-popup");
    const calendarTable = document.getElementById("calendar-table");

    calendarTable.addEventListener("mousemove", (e) => {
        const cell = e.target.closest("td[data-day]");
        if (cell) {
            const day = parseInt(cell.getAttribute("data-day"));
            const data = dailyAnalytics[day];
            if (data) {
                popup.innerHTML = `
                    <strong>Day ${day}</strong><br>
                    🟢 Plastic: <b>${data.plastic}</b><br>
                    🔵 Non-Plastic: <b>${data.nonPlastic}</b>
                `;
                popup.style.left = `${e.pageX + 15}px`;
                popup.style.top = `${e.pageY - 40}px`;
                popup.style.display = "block";
            }
        } else {
            popup.style.display = "none";
        }
    });
</script>
@endsection
