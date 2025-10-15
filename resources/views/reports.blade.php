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
        <button id="exportPDF" class="btn btn-danger">⬇ Export to PDF</button>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 justify-content-center">
        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Total Reports</h6>
                    <h3 id="totalReports" class="stat-number text-highlight">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Successful Detections</h6>
                    <h3 id="successReports" class="stat-number text-highlight">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="mt-5 chart-card">
        <h5 class="mb-3 text-dark">Monthly Report Trends</h5>
        <canvas id="reportChart" height="50"></canvas>
    </div>

  
    <!-- Report Table -->
    <div class="card-body  table-wrapper mt-5">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 text-dark">Recent Records</h5>
            <!-- Date Filter -->
            <input type="date" id="dateFilter" class="form-control form-control-sm" style="max-width: 180px;">
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

<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.2/firebase-app.js";
import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.7.2/firebase-database.js";

const firebaseConfig = {
    apiKey: "AIzaSyDx7HErgazhqZq-rzJIM-4nFhMUA5byDzY",
    authDomain: "plastic-sensor.firebaseapp.com",
    databaseURL: "https://plastic-sensor-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "plastic-sensor",
    storageBucket: "plastic-sensor.appspot.com",
    messagingSenderId: "973658571653",
    appId: "1:973658571653:web:ba344e62c400e993f5baec"
};

const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

// DOM Elements
const reportBody = document.getElementById('reportBody');
const totalReports = document.getElementById('totalReports');
const successReports = document.getElementById('successReports');
const prevBtn = document.getElementById('prevPage');
const nextBtn = document.getElementById('nextPage');
const pageInfo = document.getElementById('pageInfo');
const dateFilter = document.getElementById('dateFilter');

// Data
window.tableData = [];
let filteredData = [];
let currentPage = 1;
const itemsPerPage = 10;

// Chart
const ctx = document.getElementById('reportChart').getContext('2d');
const chartData = { labels: [], success: [] };
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [
            { label: 'Successful Detections', data: chartData.success, backgroundColor: '#2d6a4f' }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: 'black' } } },
        scales: { x: { ticks: { color: 'black' } }, y: { ticks: { color: 'black' } } }
    }
});

// Firebase Data
const logsRef = ref(db, 'logs/ButtonPress');
onValue(logsRef, (snapshot) => {
    const data = snapshot.val();
    window.tableData = [];
    let successCount = 0, totalCount = 0;
    chartData.labels = []; chartData.success = [];

    if (data) {
        Object.keys(data).forEach(date => {
            const logs = data[date];
            let daySuccess = 0;

            Object.keys(logs).forEach(time => {
                const status = logs[time];
                if (status === 'Pressed') {
                    totalCount++;
                    successCount++;
                    daySuccess++;
                    window.tableData.push({
                        Date: date,
                        Time: time,
                        Status: 'Success',
                        Details: 'Plastic Detected'
                    });
                }
            });

            if (daySuccess > 0) {
                chartData.labels.push(date);
                chartData.success.push(daySuccess);
            }
        });

        filteredData = [...window.tableData];
        currentPage = 1;
        renderTable();
    } else {
        reportBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>`;
    }

    totalReports.textContent = totalCount;
    successReports.textContent = successCount;
    chart.data.labels = chartData.labels;
    chart.data.datasets[0].data = chartData.success;
    chart.update();
});

// Table Rendering
function renderTable() {
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = filteredData.slice(start, end);

    if (paginated.length === 0) {
        reportBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No results</td></tr>`;
        pageInfo.textContent = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }

    reportBody.innerHTML = paginated.map(row => `
        <tr>
            <td>${row.Date}</td>
            <td>${row.Time}</td>
            <td><span class="text-success">${row.Status}</span></td>
            <td>${row.Details}</td>
        </tr>
    `).join('');

    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

// Pagination Events
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

// Date Filter Event
dateFilter.addEventListener('change', () => {
    const selectedDate = dateFilter.value;
    filteredData = selectedDate
        ? window.tableData.filter(row => row.Date === selectedDate)
        : [...window.tableData];
    currentPage = 1;
    renderTable();
});
</script>

<script>
    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', () => {
        if (!window.tableData.length) return alert("No data to export!");
        const ws = XLSX.utils.json_to_sheet(window.tableData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Reports");
        XLSX.writeFile(wb, "Detection_Reports.xlsx");
    });

    // Export PDF
    document.getElementById('exportPDF').addEventListener('click', () => {
        const element = document.querySelector('.container');
        html2canvas(element, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            pdf.save('Detection_Report.pdf');
        });
    });
</script>
@endsection
