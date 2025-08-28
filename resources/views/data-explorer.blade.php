@include('layout.base')
<link rel="stylesheet" href="{{ asset('assets/data-explorer.css') }}">
<title>Data Explorer</title>

<div class="container py-5">

    <!-- Header -->
    <header>
        <h1>🧪 DATA EXPLORER</h1>
        <hr>
    </header>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label text-light">Search by keyword</label>
                    <input type="text" class="form-control" id="search">
                </div>
                <div class="col-md-2">
                    <label for="from-date" class="form-label text-light">From Date</label>
                    <input type="date" class="form-control" id="from-date">
                </div>
                <div class="col-md-2">
                    <label for="to-date" class="form-label text-light">To Date</label>
                    <input type="date" class="form-control" id="to-date">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label text-light">Types of Plastic</label>
                    <select class="form-select" id="type">
                        <option value="">All</option>
                        <option>Plastic</option>
                        <option>Non-Plastic</option>
                        <option>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3 text-light">Detected Records</h5>
            <table class="table table-striped table-dark">
                <thead>
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
                        <td>Plastic</td>
                        <td><span class="text-success">Detected</span></td>
                        <td>Admin</td>
                        <td>
                            <a href="{{ route('reports') }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash"> Delete</i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-01</td>
                        <td>10:05 AM</td>
                        <td>Entrance 2</td>
                        <td>Non-Plastic</td>
                        <td><span class="text-success">Detected</span></td>
                        <td>Operator</td>
                        <td>
                            <a href="{{ route('reports') }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash"> Delete</i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-02</td>
                        <td>02:47 PM</td>
                        <td>Gate 3</td>
                        <td>Plastic</td>
                        <td><span class="text-danger">Error</span></td>
                        <td>Staff</td>
                        <td>
                            <a href="{{ route('reports') }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash"> Delete</i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-03</td>
                        <td>11:30 AM</td>
                        <td>Lobby</td>
                        <td>Plastic</td>
                        <td><span class="text-success">Detected</span></td>
                        <td>Staff</td>
                        <td>
                            <a href="{{ route('reports') }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash"> Delete</i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>