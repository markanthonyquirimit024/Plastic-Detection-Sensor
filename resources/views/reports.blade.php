@include('layout.base')
<link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
<title>Report</title>

<div class="container py-5">
    <header class="mb-4 text-center">
        <h1 class="fw-bold">Analytical Report</h1>
        <p class="text-muted">Analyze detection logs (live from Firebase).</p>
        <hr>
    </header>

    <div class="mb-3 text-center">
        <button id="exportExcel" class="btn btn-success">⬇ Export to Excel</button>
        <button id="exportPDF" class="btn btn-danger">⬇ Export to PDF</button>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 justify-content-center">
        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Total Reports</h5>
                    <h3  id="totalReports" class="stat-number text-highlight">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Successful Detections</h5>
                    <h3  id="successReports" class="stat-number text-highlight">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body">
                    <h6 class="text-muted">Failed Detections</h5>
                    <h3 id="failedReports" class="stat-number text-highlight">0</h3>
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
    <div class="mt-5">
        <h5 class="mb-3 text-dark">Recent Reports (Live)</h5>
        <div class="card">
            <div class="card-body table-wrapper">
                <div class="table-scroll">
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
                </div>
            </div>
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

    // 🔥 Firebase Config
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

    // DOM
    const reportBody = document.getElementById('reportBody');
    const totalReports = document.getElementById('totalReports');
    const successReports = document.getElementById('successReports');
    const failedReports = document.getElementById('failedReports');

    // 🧩 Make tableData global so Excel & PDF buttons can access it
    window.tableData = [];

    // Chart setup
    const ctx = document.getElementById('reportChart').getContext('2d');
    const chartData = { labels: [], success: [], failed: [] };
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                { label: 'Success', data: chartData.success, backgroundColor: '#2d6a4f' },
                { label: 'Failed', data: chartData.failed, backgroundColor: '#ff6384' }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: 'black' } } },
            scales: { x: { ticks: { color: 'black' } }, y: { ticks: { color: 'black' } } }
        }
    });

    // 🔄 Fetch live Firebase data
    const logsRef = ref(db, 'logs/ButtonPress');
    onValue(logsRef, (snapshot) => {
        const data = snapshot.val();
        reportBody.innerHTML = '';
        window.tableData = [];
        let successCount = 0, failedCount = 0, totalCount = 0;

        chartData.labels = [];
        chartData.success = [];
        chartData.failed = [];

        if (data) {
            Object.keys(data).forEach(date => {
                const logs = data[date];
                let daySuccess = 0, dayFailed = 0;

                Object.keys(logs).forEach(time => {
                    const status = logs[time];
                    const success = status === 'Pressed';
                    totalCount++;
                    success ? successCount++ : failedCount++;
                    success ? daySuccess++ : dayFailed++;

                    const row = `
                        <tr>
                            <td>${date}</td>
                            <td>${time}</td>
                            <td><span class="${success ? 'text-success' : 'text-danger'}">${success ? 'Success' : 'Failed'}</span></td>
                            <td>${success ? 'Plastic Detected' : 'Error / No Detection'}</td>
                        </tr>
                    `;
                    reportBody.insertAdjacentHTML('afterbegin', row);
                    window.tableData.push({
                        Date: date,
                        Time: time,
                        Status: success ? 'Success' : 'Failed',
                        Details: success ? 'Plastic Detected' : 'Error / No Detection'
                    });
                });

                chartData.labels.push(date);
                chartData.success.push(daySuccess);
                chartData.failed.push(dayFailed);
            });
        } else {
            reportBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>`;
        }

        totalReports.textContent = totalCount;
        successReports.textContent = successCount;
        failedReports.textContent = failedCount;

        chart.data.labels = chartData.labels;
        chart.data.datasets[0].data = chartData.success;
        chart.data.datasets[1].data = chartData.failed;
        chart.update();
    });
</script>

<script>
    // 📦 Export to Excel
    document.getElementById('exportExcel').addEventListener('click', () => {
        if (!window.tableData || window.tableData.length === 0)
            return alert("No data to export!");
        const ws = XLSX.utils.json_to_sheet(window.tableData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Reports");
        XLSX.writeFile(wb, "Detection_Reports.xlsx");
    });

    // 🧾 Export to PDF
    document.getElementById('exportPDF').addEventListener('click', () => {
        const element = document.querySelector('.container');
        html2canvas(element, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }
            pdf.save('Detection_Report.pdf');
        });
    });
</script>
