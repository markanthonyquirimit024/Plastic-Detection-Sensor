@extends('layout.base')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
<title>Dashboard</title>

<style>
/* Existing styles... */

/* Responsive for iPad Mini only (768px) */
@media (min-width: 768px) and (max-width: 991.98px) {
    /* Make summary cards full width */
    .row.g-4.mb-4 > .col-12.col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Make chart and calendar stacked and full width */
    .row.g-4 > .col-12.col-md-8,
    .row.g-4 > .col-12.col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Chart height adjustment */
    #weeklyPlasticChart {
        height: 350px !important;
    }

    /* Calendar table full width and readable */
    #calendar-table {
        width: 100% !important;
        font-size: 0.85rem;
    }

    /* Cards take full height and smaller padding */
    .card.h-100 {
        height: auto !important;
        min-height: 200px;
        padding: 0.5rem; /* reduce internal spacing */
    }

    /* Reduce card body padding */
    .card-body {
        padding: 0.75rem !important;
    }

    /* Add spacing between stacked elements */
    .row.g-4 > .col-12 {
        margin-bottom: 0.75rem;
    }

    /* Optional: reduce header and title font sizes */
    header h2 {
        font-size: 1.5rem;
    }
    header p {
        font-size: 0.9rem;
    }
}
</style>


<div class="dashboard-wrapper">
    <div class="container-fluid py-4 flex-grow-1 min-vh-100">
        <!-- Header -->
        <header class="mb-4">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted">Plastic Detection Analysis Overview</p>
            <hr>
        </header>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            @auth
            @if(Auth::user()->utype === 'Admin')
            <div class="col-12 col-md-6">
                <div class="card border-1 h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h6>Total Users</h6>
                        <h3 class="fw-bold text-success">{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>
            @endif
            @endauth

            <div class="col-12 col-md-6">
                <div class="card border-1 h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h6 class="text-muted">Total Confirmed Plastic Detected</h6>
                        <h3 id="plasticCount" class="fw-bold text-danger">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Calendar -->
        <div class="row g-4">
            <!-- Chart -->
            <div class="col-12 col-md-8">
                <div class="card border-1 h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="mb-3 fw-bold">Weekly Plastic Detected Trends</h6>
                        <p class="text-muted fst-italic">Auto-updates from Firebase.</p>
                        <canvas id="weeklyPlasticChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="col-12 col-md-4">
                <div class="card border-1 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <select id="month-filter" class="form-select form-select-sm"></select>
                                <select id="year-filter" class="form-select form-select-sm"></select>
                            </div>
                            <div>
                                <button id="prev-month" class="btn btn-sm btn-outline-secondary">&lt;</button>
                                <button id="next-month" class="btn btn-sm btn-outline-secondary">&gt;</button>
                            </div>
                        </div>

                        <h6 id="calendar-title" class="fw-bold text-center mb-2"></h6>
                        <p class="text-muted fst-italic text-center">Hover over a date to see counts.</p>
                        <div class="overflow-auto">
                            <table class="table table-bordered text-center calendar-table w-100 mb-0" id="calendar-table"></table>
                        </div>
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
    const firebaseConfig = @json($firebaseConfig);
    if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
    }
    if (typeof firebase === "undefined" || !firebase.apps.length) {
    console.error("Firebase SDK did not load or initialization failed!");
    return;
    }
    const database = firebase.database();

    // Chart setup
    const ctx = document.getElementById('weeklyPlasticChart').getContext('2d');
    const weeklyPlasticChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Button Press Count',
                data: [],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#333' } } },
            scales: { x: { ticks: { color: '#333' } }, y: { beginAtZero: true, ticks: { color: '#333' } } }
        }
    });

    let fullData = {};
    database.ref('logs/ButtonPress').on('value', snapshot => {
        const data = snapshot.val() || {};
        let totalPresses = 0;
        let dailyCounts = {};

        Object.keys(data).forEach(date => {
            const logs = data[date];
            const count = Object.values(logs).filter(v => v === "Pressed").length;
            dailyCounts[date] = count;
            totalPresses += count;
        });

        document.getElementById('plasticCount').textContent = totalPresses;

        const lastDates = Object.keys(dailyCounts).slice(-4);
        const lastValues = lastDates.map(d => dailyCounts[d]);
        weeklyPlasticChart.data.labels = lastDates;
        weeklyPlasticChart.data.datasets[0].data = lastValues;
        weeklyPlasticChart.update();

        fullData = dailyCounts;
        generateCalendar(currentYear, currentMonth);
    });

    // Calendar logic
    const today = new Date();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();

    const yearSelect = document.getElementById('year-filter');
    for (let y = currentYear - 5; y <= currentYear + 2; y++) {
        const option = document.createElement('option');
        option.value = y;
        option.textContent = y;
        if (y === currentYear) option.selected = true;
        yearSelect.appendChild(option);
    }

    const monthSelect = document.getElementById('month-filter');
    const monthNames = [ "January","February","March","April","May","June","July","August","September","October","November","December" ];
    monthNames.forEach((m,i)=> monthSelect.appendChild(new Option(m,i)));
    monthSelect.value = currentMonth;

    function updateCalendarTitle() {
        document.getElementById('calendar-title').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }

    function generateCalendar(year, month) {
        updateCalendarTitle();
        const table = document.getElementById("calendar-table");
        table.innerHTML = "";
        const days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
        const thead = document.createElement("thead");
        const headerRow = document.createElement("tr");
        days.forEach(d=>{ const th=document.createElement("th"); th.textContent=d; th.classList.add("p-2"); headerRow.appendChild(th); });
        thead.appendChild(headerRow);
        table.appendChild(thead);

        const tbody = document.createElement("tbody");
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month+1, 0).getDate();
        let date=1;

        for(let i=0;i<6;i++){
            const row=document.createElement("tr");
            for(let j=0;j<7;j++){
                const cell=document.createElement("td");
                cell.classList.add("p-2","calendar-cell");
                if((i===0 && j<firstDay)||date>daysInMonth){ cell.textContent=""; }
                else{
                    cell.textContent=date;
                    const dateStr=`${year}-${(month+1).toString().padStart(2,'0')}-${date.toString().padStart(2,'0')}`;
                    cell.dataset.date=dateStr;

                    cell.addEventListener('mouseenter',(e)=>{
                        const presses=fullData[e.target.dataset.date]||0;
                        const popup=document.getElementById('hover-popup');
                        popup.innerHTML=`Plastic Counts: ${presses}`;
                        popup.style.display="block";
                        popup.style.top=(e.pageY+10)+"px";
                        popup.style.left=(e.pageX+10)+"px";
                    });
                    cell.addEventListener('mouseleave',()=>{ document.getElementById('hover-popup').style.display="none"; });
                    date++;
                }
                row.appendChild(cell);
            }
            tbody.appendChild(row);
            if(date>daysInMonth) break;
        }
        table.appendChild(tbody);
    }

    document.getElementById('prev-month').addEventListener('click',()=>{
        currentMonth--; if(currentMonth<0){currentMonth=11; currentYear--;}
        monthSelect.value=currentMonth; yearSelect.value=currentYear;
        generateCalendar(currentYear,currentMonth);
    });

    document.getElementById('next-month').addEventListener('click',()=>{
        currentMonth++; if(currentMonth>11){currentMonth=0; currentYear++;}
        monthSelect.value=currentMonth; yearSelect.value=currentYear;
        generateCalendar(currentYear,currentMonth);
    });

    monthSelect.addEventListener('change',()=>{ currentMonth=parseInt(monthSelect.value); generateCalendar(currentYear,currentMonth); });
    yearSelect.addEventListener('change',()=>{ currentYear=parseInt(yearSelect.value); generateCalendar(currentYear,currentMonth); });

    generateCalendar(currentYear,currentMonth);
}

window.addEventListener('load',initializeDashboard);
</script>
@endsection
