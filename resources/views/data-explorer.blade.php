@include('layout.base')
<link rel="stylesheet" href="{{ asset('assets/data-explorer.css') }}">
<title>Data Explorer</title>

<div class="data-explorer-wrapper d-flex flex-column min-vh-100">

    <!-- Header -->
    <header class="mb-4 text-center">
        <h1 class="fw-bold">🧪 Data Explorer</h1>
        <p class="text-muted">Analyze detection logs with filters and search options.</p>
        <hr>
    </header>

    <!-- Content Section -->
    <div class="flex-grow-1 px-4">

        <!-- Search & Filters -->
        <div class="card shadow-sm border-0 mb-4 rounded-4">
            <div class="card-body">
                <form class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label fw-semibold">Search by keyword</label>
                        <input type="text" class="form-control rounded-3" id="search" placeholder="Enter keyword">
                    </div>
                    <div class="col-md-2">
                        <label for="from-date" class="form-label fw-semibold">From Date</label>
                        <input type="date" class="form-control rounded-3" id="from-date">
                    </div>
                    <div class="col-md-2">
                        <label for="to-date" class="form-label fw-semibold">To Date</label>
                        <input type="date" class="form-control rounded-3" id="to-date">
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label fw-semibold">Types of Plastic</label>
                        <select class="form-select rounded-3" id="type">
                            <option value="">All</option>
                            <option>Plastic</option>
                            <option>Non-Plastic</option>
                            <option>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100 rounded-3">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">📊 Detected Records</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-success text-success">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-08-01</td>
                                <td>09:12 AM</td>
                                <td>Entrance 1</td>
                                <td><span class="badge bg-success">Plastic</span></td>
                                <td><span class="text-success fw-semibold">Detected</span></td>
                                <td>Admin</td>
                                <td>
                                    <a href="{{ route('reports') }}" target="_blank" class="btn btn-outline-info btn-sm rounded-3">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2025-08-01</td>
                                <td>10:05 AM</td>
                                <td>Entrance 2</td>
                                <td><span class="badge bg-warning text-dark">Non-Plastic</span></td>
                                <td><span class="text-success fw-semibold">Detected</span></td>
                                <td>Operator</td>
                                <td>
                                    <a href="{{ route('reports') }}" target="_blank" class="btn btn-outline-info btn-sm rounded-3">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2025-08-02</td>
                                <td>02:47 PM</td>
                                <td>Gate 3</td>
                                <td><span class="badge bg-success">Plastic</span></td>
                                <td><span class="text-danger fw-semibold">Error</span></td>
                                <td>Staff</td>
                                <td>
                                    <a href="{{ route('reports') }}" target="_blank" class="btn btn-outline-info btn-sm rounded-3">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2025-08-03</td>
                                <td>11:30 AM</td>
                                <td>Lobby</td>
                                <td><span class="badge bg-success">Plastic</span></td>
                                <td><span class="text-success fw-semibold">Detected</span></td>
                                <td>Staff</td>
                                <td>
                                    <a href="{{ route('reports') }}" target="_blank" class="btn btn-outline-info btn-sm rounded-3">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
