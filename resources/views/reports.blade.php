@extends('layout.base')

@section('title', 'Report')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
<link rel="stylesheet" href="{{ asset('assets/calendar-filter.css') }}">

<div class="container py-5 mt-4">
    <header class="mb-4 text-center">
        <h1 class="fw-bold">Analytical Report</h1>
        <p class="text-muted">Analyze successful detection logs (live from Firebase)</p>
        <hr>
    </header>

    <!-- Export Buttons -->
    <div class="mb-3 text-center">
        <button id="exportExcel" class="btn btn-success">⬇ Export to Excel</button>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 justify-content-center">
        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Successful Detections within the day</h6>
                    <h3 id="successReports" class="stat-number text-highlight">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="mt-5 chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-dark" id="chartTitle">Today's Report Trends (6 AM - 6 PM)</h5>
            <button id="toggleView" class="btn btn-outline-primary btn-sm">
                Switch to 24 Hours
            </button>
        </div>
        <canvas id="reportChart" height="50"></canvas>
    </div>

    <!-- Report Table -->
    <div class="card-body  table-wrapper mt-5">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 text-dark">Summarization of Today's Records</h5>
            <div class="d-flex gap-2">
                <input type="date" id="dateFilter" class="form-control form-control-sm" style="max-width: 180px;">
                <button id="filterBtn" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </div>

        <table class="table table-striped table-dark mb-0" id="reportTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="reportBody">
                <tr><td colspan="4" class="text-center text-muted">Loading data...</td></tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-center align-items-center gap-3">
            <button id="prevPage" class="btn btn-dark btn-sm">Previous</button>
            <span id="pageInfo" class="text-dark small">Page 1</span>
            <button id="nextPage" class="btn btn-dark btn-sm">Next</button>
        </div>
    </div>
</div>

<!-- Firebase + Chart + Export -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
function initializeReport() {
    const firebaseConfig = @json($firebaseConfig);

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }

    const database = firebase.database();

    const reportBody = document.getElementById('reportBody');
    const successReports = document.getElementById('successReports');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    const dateFilter = document.getElementById('dateFilter');
    const filterBtn = document.getElementById('filterBtn');

    window.tableData = [];
    let filteredData = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let activeListenerRef = null;

    function generateWorkingHours() {
        const arr = [];
        for (let hour = 6; hour <= 18; hour++) {
            arr.push(`${hour.toString().padStart(2,'0')}:00`);
            if (hour < 18) arr.push(`${hour.toString().padStart(2,'0')}:30`);
        }
        return arr;
    }

    function generateFullDay() {
        const arr = [];
        for (let hour = 0; hour < 24; hour++) {
            arr.push(`${hour.toString().padStart(2,'0')}:00`);
            if (hour < 23) arr.push(`${hour.toString().padStart(2,'0')}:30`);
        }
        return arr;
    }

    let isFullDay = false;
    let chartLabels = generateWorkingHours();
    const chartData = { labels: chartLabels, success: Array(chartLabels.length).fill(0) };
    const ctx = document.getElementById('reportChart').getContext('2d');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Successful Detections',
                data: chartData.success,
                borderColor: '#2d6a4f',
                backgroundColor: '#2d6a4f',
                tension: 0.3,
                fill: false,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { ticks: { color: 'black', maxRotation: 90, minRotation: 45, autoSkip: true, maxTicksLimit: 25 } },
                y: { beginAtZero: true, ticks: { color: 'black', stepSize: 1 } }
            },
            plugins: {
                legend: { labels: { color: 'black' } },
                tooltip: { callbacks: { label: function(context) { return `Detections: ${context.parsed.y}`; } } }
            }
        }
    });

    function getToday() {
        return new Date().toLocaleDateString('en-CA');
    }

    function removeActiveListener() {
        if (activeListenerRef) {
            activeListenerRef.off();
            activeListenerRef = null;
        }
    }

    function listenForDay(date) {
        removeActiveListener();
        activeListenerRef = database.ref(`logs/ButtonPress/${date}`);
        activeListenerRef.on('value', function(snapshot) {
            const logs = snapshot.val();
            window.tableData = [];
            let successCount = 0;

            chartData.success = Array(chartLabels.length).fill(0);

            if (logs) {
                Object.entries(logs).forEach(([time, entry]) => {
                    let isSuccess = false;

                    if (typeof entry === 'string' && entry === 'Pressed') isSuccess = true;
                    else if (typeof entry === 'object' && entry.status === 'Detected') isSuccess = true;

                    if (isSuccess) {
                        successCount++;
                        window.tableData.push({
                            Date: date,
                            Time: time,
                            Status: 'Success',
                            Details: typeof entry === 'object' && entry.details ? entry.details : 'Plastic Detected'
                        });

                        const [h, m] = time.split(':').map(Number);
                        let slotLabel = m < 30 ? `${h.toString().padStart(2,'0')}:00` : `${h.toString().padStart(2,'0')}:30`;
                        const index = chartData.labels.indexOf(slotLabel);
                        if (index !== -1) chartData.success[index]++;
                    }
                });
            }

            filteredData = [...window.tableData];
            currentPage = 1;
            renderTable();

            successReports.textContent = successCount;
            chart.data.datasets[0].data = chartData.success;
            chart.update();
        });
    }

    let currentDay = getToday();
    dateFilter.value = currentDay;
    listenForDay(currentDay);

    setInterval(() => {
        const today = getToday();
        if (today !== currentDay) {
            currentDay = today;
            dateFilter.value = currentDay;
            listenForDay(currentDay);
        }
    }, 60 * 1000);

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginated = filteredData.slice(start, end);

        reportBody.innerHTML = paginated.length
            ? paginated.map(row => `<tr>
                <td>${row.Date}</td>
                <td>${row.Time}</td>
                <td><span class="text-success">${row.Status}</span></td>
                <td>${row.Details}</td>
            </tr>`).join('')
            : `<tr><td colspan="4" class="text-center text-muted">No results</td></tr>`;

        const totalPages = Math.ceil(filteredData.length / itemsPerPage) || 1;
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    prevBtn.addEventListener('click', () => { if(currentPage>1){ currentPage--; renderTable(); } });
    nextBtn.addEventListener('click', () => { const totalPages = Math.ceil(filteredData.length / itemsPerPage); if(currentPage<totalPages){ currentPage++; renderTable(); } });

    filterBtn.addEventListener('click', () => {
        const selectedDate = dateFilter.value;
        if (!selectedDate) { alert('Please select a date.'); return; }
        currentDay = selectedDate;
        listenForDay(selectedDate);
    });

    const toggleBtn = document.getElementById('toggleView');
    const chartTitle = document.getElementById('chartTitle');

    toggleBtn.addEventListener('click', () => {
        isFullDay = !isFullDay;

        chartLabels = isFullDay ? generateFullDay() : generateWorkingHours();
        toggleBtn.textContent = isFullDay ? "Switch to Working Hours" : "Switch to 24 Hours";
        chartTitle.textContent = isFullDay ? "Today's Report Trends (24 Hours)" : "Today's Report Trends (6 AM - 6 PM)";

        chartData.labels = chartLabels;
        chartData.success = Array(chartLabels.length).fill(0);
        chart.data.labels = chartLabels;
        chart.data.datasets[0].data = chartData.success;

        window.tableData.forEach(row => {
            const [h, m] = row.Time.split(':').map(Number);
            let slotLabel = m < 30 ? `${h.toString().padStart(2,'0')}:00` : `${h.toString().padStart(2,'0')}:30`;
            const index = chartLabels.indexOf(slotLabel);
            if (index !== -1) chartData.success[index]++;
        });

        chart.update();
    });

    document.getElementById('exportExcel').addEventListener('click', () => {
        if (!window.tableData.length) return alert("No data to export!");
        const ws = XLSX.utils.json_to_sheet(window.tableData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Reports");
        XLSX.writeFile(wb, "Detection_Reports.xlsx");
    });
}

window.addEventListener('load', initializeReport);
</script>

@endsection
