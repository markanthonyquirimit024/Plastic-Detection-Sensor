@include('layout.base')
<link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
<title>Report</title>

<div class="container py-5">
    <header class="mb-4 text-center">
        <h1 class="fw-bold">📈 Analytical Report</h1>
        <p class="text-muted">Analyze detection logs.</p>
        <hr>
    </header>

    <div class="mb-3">
        <button class="btn btn-success btn-l">⬇ Export to Excel</button>
        <button class="btn btn-danger btn-l">⬇ Export to PDF</button>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 justify-content-center">
        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Total Reports</h5>
                    <p class="stat-number text-highlight">{{ $reportCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Successful Detections</h5>
                    <p class="stat-number text-highlight">{{ $successCount ?? 134 }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card text-center flex-fill">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Failed Detections</h5>
                    <p class="stat-number text-highlight">{{ $failCount ?? 26 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="mt-5 chart-card">
        <h5 class="mb-3" style="color: black;">Monthly Report Trends</h5>
        <canvas id="reportChart" height="50"></canvas>
    </div>

    <!-- Report Table -->
    <div class="mt-5">
        <h5 class="mb-3 text-dark">Recent Reports</h5>
        <div class="card">
            <div class="card-body table-wrapper">
                <div class="table-scroll">
                    <table class="table table-striped table-dark mb-0">
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
                                <td><span class="text-success">Success</span></td>
                                <td>Plastic detected at Entrance 1</td>
                            </tr>
                            <tr>
                                <td>2025-08-02</td>
                                <td>Operator</td>
                                <td><span class="text-danger">Failed</span></td>
                                <td>Scan error</td>
                            </tr>
                            <tr>
                                <td>2025-08-03</td>
                                <td>Staff</td>
                                <td><span class="text-success">Success</span></td>
                                <td>Non-Plastic detected at Entrance 2</td>
                            </tr>
                            <tr>
                                <td>2025-08-04</td>
                                <td>Staff</td>
                                <td><span class="text-success">Success</span></td>
                                <td>Non-Plastic detected at Entrance 3</td>
                            </tr>
                            <tr>
                                <td>2025-08-05</td>
                                <td>Staff</td>
                                <td><span class="text-danger">Failed</span></td>
                                <td>Scan interrupted</td>
                            </tr>
                            <tr>
                                <td>2025-08-05</td>
                                <td>Staff</td>
                                <td><span class="text-danger">Failed</span></td>
                                <td>Scan interrupted</td>
                            </tr>
                            <tr>
                                <td>2025-08-05</td>
                                <td>Staff</td>
                                <td><span class="text-danger">Failed</span></td>
                                <td>Scan interrupted</td>
                            </tr>
                            <tr>
                                <td>2025-08-05</td>
                                <td>Staff</td>
                                <td><span class="text-danger">Failed</span></td>
                                <td>Scan interrupted</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ChartJS -->
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
                    backgroundColor: '#4fd1c5'
                },
                {
                    label: 'Failed',
                    data: [5, 8, 3, 12, 7, 10],
                    backgroundColor: '#ff6384'
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
</script>
