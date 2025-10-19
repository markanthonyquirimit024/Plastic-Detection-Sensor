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

    <div class="mb-3 text-center">
        <button id="exportExcel" class="btn btn-success">⬇ Export to Excel</button>
    </div>

    <div class="row g-3 justify-content-center">
        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Successful Detections (Within Range)</h6>
                    <h3 id="successReports" class="stat-number text-highlight">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-dark" id="chartTitle">Detection Trends</h5>
            <div class="d-flex align-items-center gap-2">
                <label for="trend-granularity" class="mb-0 small text-muted">View by:</label>
                <select id="trend-granularity" class="form-select form-select-sm" style="max-width: 150px;">
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly" selected>Monthly</option>
                    <option value="Yearly">Yearly</option>
                </select>
            </div>
        </div>
        <canvas id="reportChart" height="60"></canvas>
    </div>

    <div class="card-body table-wrapper mt-5">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 text-dark">Summarization of Records</h5>
            <div class="d-flex gap-2 align-items-center">
                <input type="date" id="fromDate" class="form-control form-control-sm" style="max-width: 160px;">
                <span class="mx-1">to</span>
                <input type="date" id="toDate" class="form-control form-control-sm" style="max-width: 160px;">
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

        <div class="mt-3 d-flex justify-content-center align-items-center gap-3">
            <button id="prevPage" class="btn btn-dark btn-sm">Previous</button>
            <span id="pageInfo" class="text-dark small">Page 1</span>
            <button id="nextPage" class="btn btn-dark btn-sm">Next</button>
        </div>
    </div>
</div>

<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
function initializeReport() {
    const firebaseConfig = @json($firebaseConfig);

    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    const reportBody = document.getElementById('reportBody');
    const successReports = document.getElementById('successReports');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    const filterBtn = document.getElementById('filterBtn');
    const trendDropdown = document.getElementById('trend-granularity');
    const chartTitle = document.getElementById('chartTitle');

    let tableData = [];
    let filteredData = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let activeListeners = [];

    // Chart Setup
    let chartLabels = [];
    let chartCounts = [];
    const ctx = document.getElementById('reportChart').getContext('2d');
    
    const chart = new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Successful Detections',
                data: chartCounts,
                backgroundColor: '#2d6a4f', 
                borderColor: '#2d6a4f',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { 
                    ticks: { color: 'black', maxRotation: 90, minRotation: 45 },
                    grid: { display: false }
                },
                y: { 
                    beginAtZero: true, 
                    // REMOVED 'stepSize: 1' to allow Chart.js to automatically
                    // determine the max and appropriate steps, extending the axis
                    // to fit the "real" trend data.
                    ticks: { 
                        color: 'black',
                        // Ensure ticks are integers for count data
                        callback: function(value, index, values) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }, 
                    min: 0 
                }
            },
            plugins: {
                legend: { labels: { color: 'black' } }
            }
        }
    });

    function clearListeners() {
        activeListeners.forEach(ref => ref.off());
        activeListeners = [];
    }

    function formatTime12Hour(time24) {
        if (!time24) return '';
        const parts = time24.split(':');
        let hours = parseInt(parts[0], 10);
        const minutes = parts[1];
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        return `${hours}:${minutes} ${ampm}`;
    }

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const pageData = filteredData.slice(start, start + itemsPerPage);

        reportBody.innerHTML = pageData.length
            ? pageData.map(row => `
                <tr>
                    <td>${row.Date}</td>
                    <td>${formatTime12Hour(row.Time)}</td>
                    <td><span class="text-success">${row.Status}</span></td>
                    <td>${row.Details}</td>
                </tr>`).join('')
            : `<tr><td colspan="4" class="text-center text-muted">No data</td></tr>`;

        const totalPages = Math.ceil(filteredData.length / itemsPerPage) || 1;
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    // ✅ FIXED: Add Previous/Next pagination event handlers
    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    });

    function aggregateData(data, period) {
        const aggregationMap = {};
        let format;

        if (period === 'Daily') format = d => d;
        else if (period === 'Weekly') {
            format = d => {
                const date = new Date(d);
                const day = date.getDay(); 
                const diff = date.getDate() - day; 
                const sunday = new Date(date.setDate(diff));
                return sunday.toISOString().split('T')[0] + ' (Week)';
            };
        }
        else if (period === 'Monthly') format = d => d.substring(0, 7);
        else if (period === 'Yearly') format = d => d.substring(0, 4);
        else format = d => d;

        data.forEach(item => {
            const key = format(item.Date);
            aggregationMap[key] = (aggregationMap[key] || 0) + 1;
        });

        const sortedKeys = Object.keys(aggregationMap).sort();
        
        return {
            labels: sortedKeys,
            counts: sortedKeys.map(key => aggregationMap[key])
        };
    }

    function renderChart(data) {
        const period = trendDropdown.value;
        const aggregated = aggregateData(data, period);

        chartLabels = aggregated.labels;
        chartCounts = aggregated.counts;

        chartTitle.textContent = `${period} Detection Trends`;
        chart.data.labels = chartLabels;
        chart.data.datasets[0].data = chartCounts;
        chart.data.datasets[0].label = `Successful Detections (${period})`;
        chart.update();
    }
    
    function loadRange(from, to) {
        clearListeners();
        tableData = [];
        successReports.textContent = 0;

        const startDate = new Date(from);
        const endDate = new Date(to);
        let pending = 0;

        for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
            const dateStr = d.toISOString().split('T')[0];
            const ref = db.ref(`logs/ButtonPress/${dateStr}`);
            activeListeners.push(ref);
            pending++;

            ref.once('value', snapshot => {
                const logs = snapshot.val();
                if (logs) {
                    Object.entries(logs).forEach(([time, entry]) => {
                        let isSuccess = (entry === 'Pressed') || (entry.status === 'Detected');
                        if (isSuccess) {
                            tableData.push({
                                Date: dateStr,
                                Time: time,
                                Status: 'Success',
                                Details: entry.details || 'Plastic Detected'
                            });
                        }
                    });
                }
                pending--;
                if (pending === 0) updateDisplay();
            });
        }
        if (pending === 0) updateDisplay();
    }

    function updateDisplay() {
        filteredData = [...tableData].sort((a, b) => (a.Date + a.Time).localeCompare(b.Date + b.Time));
        currentPage = 1;
        renderTable();
        successReports.textContent = filteredData.length;
        renderChart(filteredData); 
    }

    filterBtn.addEventListener('click', () => {
        const from = fromDate.value;
        const to = toDate.value;
        if (!from || !to) return alert("Please select both 'From' and 'To' dates.");
        if (from > to) return alert("'From' date cannot be later than 'To' date.");
        loadRange(from, to);
    });

    trendDropdown.addEventListener('change', () => {
        renderChart(filteredData);
    });

    document.getElementById('exportExcel').addEventListener('click', () => {
        if (!tableData.length) return alert("No data to export!");
        const exportData = tableData.map(row => ({
            Date: row.Date,
            Time: formatTime12Hour(row.Time),
            Status: row.Status,
            Details: row.Details
        }));
        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Reports");
        XLSX.writeFile(wb, "Detection_Reports.xlsx");
    });

    const today = new Date().toISOString().split('T')[0];
    fromDate.value = today;
    toDate.value = today;
    loadRange(today, today);
}

window.addEventListener('load', initializeReport);
</script>
@endsection