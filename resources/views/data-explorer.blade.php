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

                <!-- Plastic Count Badge -->
                <span id="plasticCount" class="position-absolute top-0 end-0 badge bg-success fs-6 m-3">
                    Plastic: 0
                </span>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="dataTable">
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
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.2/firebase-app.js";
import { getDatabase, ref, onValue, remove } from "https://www.gstatic.com/firebasejs/10.7.2/firebase-database.js";

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
const tableBody = document.getElementById('tableBody');
const prevBtn = document.getElementById('prevPage');
const nextBtn = document.getElementById('nextPage');
const pageInfo = document.getElementById('pageInfo');
const searchInput = document.getElementById('search');
const fromDate = document.getElementById('from-date');
const toDate = document.getElementById('to-date');
const typeSelect = document.getElementById('type');
const filterForm = document.getElementById('filterForm');

// Data
window.tableData = [];
let filteredData = [];
let currentPage = 1;
const itemsPerPage = 5;

// Fetch Firebase Data
const logsRef = ref(db, 'logs/ButtonPress');
onValue(logsRef, (snapshot) => {
    const data = snapshot.val();
    window.tableData = [];

    if(data) {
        Object.keys(data).forEach(date => {
            const logs = data[date];
            Object.keys(logs).forEach(time => {
                const record = logs[time];
                window.tableData.push({
                    id: time+"-"+date,
                    Date: date,
                    Time: time,
                    Location: record.location || "Unknown",
                    Type: record.type || "Plastic",
                    Status: record.status || "Detected",
                    User: record.user || "Admin"
                });
            });
        });
    }

    filteredData = [...window.tableData];
    currentPage = 1;
    renderTable();
});

// Render Table
function renderTable() {
    const start = (currentPage-1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = filteredData.slice(start, end);

    if(paginated.length === 0){
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No results</td></tr>`;
        pageInfo.textContent = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        updatePlasticCount();
        return;
    }

    tableBody.innerHTML = paginated.map(row => `
        <tr data-id="${row.id}">
            <td>${row.Date}</td>
            <td>${row.Time}</td>
            <td>${row.Location}</td>
            <td><span class="badge ${row.Type==="Plastic"?"bg-success":"bg-warning text-dark"}">${row.Type}</span></td>
            <td><span class="${row.Status==="Detected"?"text-success fw-semibold":"text-danger fw-semibold"}">${row.Status}</span></td>
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

    updatePlasticCount();
}

// Update Plastic Count
function updatePlasticCount() {
    const count = filteredData.filter(r => r.Type === "Plastic").length;
    document.getElementById('plasticCount').textContent = `Plastic: ${count}`;
}

// Pagination
prevBtn.addEventListener('click', ()=> { if(currentPage>1){currentPage--; renderTable();} });
nextBtn.addEventListener('click', ()=> { if(currentPage<Math.ceil(filteredData.length/itemsPerPage)){currentPage++; renderTable();} });

// Filters
filterForm.addEventListener('submit', (e)=>{
    e.preventDefault();
    const keyword = searchInput.value.toLowerCase();
    const from = fromDate.value;
    const to = toDate.value;
    const type = typeSelect.value;

    filteredData = window.tableData.filter(row=>{
        let match = true;
        if(keyword) match = match && (row.Location.toLowerCase().includes(keyword) || row.User.toLowerCase().includes(keyword));
        if(from) match = match && row.Date >= from;
        if(to) match = match && row.Date <= to;
        if(type) match = match && row.Type === type;
        return match;
    });
    currentPage = 1;
    renderTable();
});

// Event Delegation for Actions
tableBody.addEventListener('click', (e)=>{
    const tr = e.target.closest('tr');
    if(!tr) return;
    const id = tr.dataset.id;

    if(e.target.closest('.viewBtn')){
        const [time, date] = id.split("-");
        window.location.href = `{{ route('reports') }}?date=${date}&time=${time}`;
    }

    if(e.target.closest('.deleteBtn')){
        if(!confirm("Are you sure you want to delete this record?")) return;
        const [time, date] = id.split("-");
        remove(ref(db, `logs/ButtonPress/${date}/${time}`))
        .then(()=>{
            alert("Record deleted");
            window.tableData = window.tableData.filter(r=>r.id!==id);
            filteredData = filteredData.filter(r=>r.id!==id);
            renderTable();
        })
        .catch(err => alert("Error deleting record: "+err));
    }
});
</script>
@endsection
