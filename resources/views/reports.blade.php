@extends('layout.base')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
<title>Report</title>

<div class="report-wrapper flex-grow-1 w-100 d-flex flex-column">

    <!-- Header -->
    <header>
        <h1>📈 REPORT ANALYTICS</h1>
        <hr>
    </header>

    <!-- Export Buttons -->
    <div class="mb-3">
        <button class="btn btn-green">⬇ Export to Excel</button>
        <button class="btn btn-red">⬇ Export to PDF</button>
    </div>

    <!-- Stats + Chart Row -->
    <div class="row g-3 flex-grow-0">
        <!-- Stats -->
        <div class="col-lg-6">
            <div class="row g-3">
                <div class="col-md-12 d-flex">
                    <div class="card text-center flex-fill compact-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Reports</h5>
                            <p class="stat-number text-highlight">{{ $reportCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card text-center flex-fill compact-card">
                        <div class="card-body">
                            <h5 class="card-title">Successful Detections</h5>
                            <p class="stat-number text-highlight">{{ $successCount ?? 134 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card text-center flex-fill compact-card">
                        <div class="card-body">
                            <h5 class="card-title">Failed Detections</h5>
                            <p class="stat-number text-highlight">{{ $failCount ?? 26 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-lg-6">
            <div class="card chart-card h-100 compact-card">
                <div class="card-body">
                    <h5 class="mb-2">Monthly Report Trends</h5>
                    <div class="chart-wrapper">
                        <canvas id="reportChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="mt-3 flex-grow-1 d-flex flex-column">
        <h5 class="mb-2">Recent Reports</h5>
        <div class="card flex-grow-1 compact-card">
            <div class="card-body table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2025-08-01</td>
                            <td>Admin</td>
                            <td><span class="status-pill status-success">Success</span></td>
                            <td>Plastic detected at Entrance 1</td>
                        </tr>
                        <tr>
                            <td>2025-08-02</td>
                            <td>Operator</td>
                            <td><span class="status-pill status-failed">Failed</span></td>
                            <td>Scan error</td>
                        </tr>
                        <tr>
                            <td>2025-08-03</td>
                            <td>Staff</td>
                            <td><span class="status-pill status-success">Success</span></td>
                            <td>Non-Plastic detected at Entrance 2</td>
                        </tr>
                        <tr>
                            <td>2025-08-03</td>
                            <td>Staff</td>
                            <td><span class="status-pill status-success">Success</span></td>
                            <td>Non-Plastic detected at Entrance 2</td>
                        </tr>
                        <tr>
                            <td>2025-08-03</td>
                            <td>Staff</td>
                            <td><span class="status-pill status-success">Success</span></td>
                            <td>Non-Plastic detected at Entrance 2</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Success',
                    data: [30, 45, 28, 60, 50, 70],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Failed',
                    data: [5, 8, 3, 12, 7, 10],
                    backgroundColor: '#dc3545'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 5 },
            plugins: {
                legend: { labels: { color: '#212529', boxWidth: 12 } }
            },
            scales: {
                x: { ticks: { color: '#212529', font: { size: 10 } }, grid: { color: '#e9ecef' } },
                y: { ticks: { color: '#212529', font: { size: 10 } }, grid: { color: '#e9ecef' } }
            }
        }
    });
</script>
@endsection
