@extends('layout.base')

@section('title', 'Data Explorer')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/data-explorer.css') }}">

<div class="data-explorer-wrapper">

    <!-- Header -->
    <header class="text-start">
        <h1 class="fw-bold mb-2">Data Explorer</h1>
        <p class="text-muted mb-3">Analyze detection logs with filters and search options.</p>
        <hr class="mt-0">
    </header>

    <!-- Content -->
    <div class="content-section">

        <!-- Search & Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3 align-items-end">
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
                        <button type="submit" class="btn btn-success w-100 rounded-3">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-body position-relative">
                <h5 class="fw-bold text-success mb-3">📊 Detected Records</h5>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="dataTable">
                        <thead class="table-success text-success">
                            <tr>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Count</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Loading data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3 d-flex justify-content-center align-items-center gap-3">
                    <button id="prevPage" class="btn btn-dark btn-sm">Previous</button>
                    <span id="pageInfo" class="text-dark small">Page 1</span>
                    <button id="nextPage" class="btn btn-dark btn-sm">Next</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Firebase & JS -->
<script>
function initializeDataExplorer() {
    // Firebase config from Laravel
    const firebaseConfig = @json($firebaseConfig);

    // Initialize Firebase (compat)
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    const database = firebase.database();

    // DOM Elements
    const tableBody = document.getElementById('tableBody');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('search');
    const fromDate = document.getElementById('from-date');
    const toDate = document.getElementById('to-date');
    const filterForm = document.getElementById('filterForm');

    // Data
    let tableData = [];
    let filteredData = [];
    let currentPage = 1;
    const itemsPerPage = 5;

    // Format date to MM/DD/YYYY for display
    function formatDate(dateStr) {
        const parts = dateStr.split('-'); // ["YYYY","MM","DD"]
        return `${parts[1]}/${parts[2]}/${parts[0]}`;
    }

    // Fetch Firebase Data
    const logsRef = database.ref('logs/ButtonPress');
    logsRef.on('value', snapshot => {
        const data = snapshot.val();
        const grouped = {}; // Group by date+location+user+type+status

        if (data) {
            Object.keys(data).forEach(date => {
                const logs = data[date];
                Object.keys(logs).forEach(time => {
                    const record = logs[time];
                    const key = `${date}|${record.location || "Main Gate"}|${record.user || "Admin"}|${record.type || "Plastic"}|${record.status || "Detected"}`;
                    if (!grouped[key]) {
                        grouped[key] = {
                            Date: formatDate(date),
                            RawDate: date,
                            Location: record.location || "Main Gate",
                            User: record.user || "Admin",
                            Type: record.type || "Plastic",
                            Status: record.status || "Detected",
                            Count: 1
                        };
                    } else {
                        grouped[key].Count++;
                    }
                });
            });
        }

        tableData = Object.values(grouped);
        filteredData = [...tableData];
        currentPage = 1;
        renderTable();
    });

    // Render Table
    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginated = filteredData.slice(start, end);

        if (paginated.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No results</td></tr>`;
            pageInfo.textContent = '';
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            return;
        }

        tableBody.innerHTML = paginated.map(row => `
            <tr data-date="${row.RawDate}">
                <td>${row.Date}</td>
                <td>${row.Location}</td>
                <td><span class="badge ${row.Type==="Plastic"?"bg-success":"bg-warning text-dark"}">${row.Type}</span></td>
                <td><span class="${row.Status==="Detected"?"text-success fw-semibold":"text-danger fw-semibold"}">${row.Status}</span></td>
                <td>${row.Count}</td>
                <td>${row.User}</td>
                <td>
                    <button class="btn btn-outline-info btn-sm rounded-3 viewBtn"><i class="bi bi-eye"></i> View</button>
                    <button class="btn btn-outline-danger btn-sm rounded-3 deleteBtn"><i class="bi bi-trash"></i> Delete</button>
                </td>
            </tr>
        `).join('');

        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    // Pagination
    prevBtn.addEventListener('click', () => { if(currentPage>1){currentPage--; renderTable();} });
    nextBtn.addEventListener('click', () => { if(currentPage<Math.ceil(filteredData.length/itemsPerPage)){currentPage++; renderTable();} });

    // Filters
    filterForm.addEventListener('submit', e => {
        e.preventDefault();
        const keyword = searchInput.value.toLowerCase();
        const from = fromDate.value;
        const to = toDate.value;

        filteredData = tableData.filter(row => {
            let match = true;
            if(keyword) match = match && (row.Location.toLowerCase().includes(keyword) || row.User.toLowerCase().includes(keyword));
            if(from) match = match && row.RawDate >= from;
            if(to) match = match && row.RawDate <= to;
            return match;
        });

        currentPage = 1;
        renderTable();
    });

    // Event Delegation for Actions
    tableBody.addEventListener('click', e => {
        const tr = e.target.closest('tr');
        if (!tr) return;
        const date = tr.dataset.date;

        if (e.target.closest('.viewBtn')) {
            window.location.href = `{{ route('reports') }}?date=${date}`;
        }

        if (e.target.closest('.deleteBtn')) {
            // SweetAlert2 confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: `This will delete ALL records for ${date}!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Delete entire day from Firebase
                    database.ref(`logs/ButtonPress/${date}`).remove()
                        .then(() => {
                            // Remove from local data
                            tableData = tableData.filter(r => r.RawDate !== date);
                            filteredData = filteredData.filter(r => r.RawDate !== date);
                            renderTable();

                            // Success notification
                            Swal.fire({
                                title: 'Deleted!',
                                text: `All records for ${date} have been deleted.`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(err => {
                            // Error notification
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error deleting the records: ' + err,
                                icon: 'error'
                            });
                        });
                }
            });
        }
    });
}

window.addEventListener('load', initializeDataExplorer);
</script>

@endsection
