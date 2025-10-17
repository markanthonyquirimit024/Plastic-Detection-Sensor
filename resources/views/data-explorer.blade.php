@extends('layout.base')

@section('title', 'Data Explorer')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/data-explorer.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="data-explorer-wrapper">

    <!-- Header -->
    <header class="text-start d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-2 text-center">Data Explorer</h1>
            <p class="text-muted mb-3 text-center">Analyze detection logs with filters and search options.</p>
        </div>
        <!-- Summary Button -->
        <button id="summaryBtn" class="btn btn-outline-success rounded-3">
            <i class="bi bi-bar-chart-line"></i> View Summary
        </button>
    </header>
    <hr class="mt-0">

    <!-- Content -->
    <div class="content-section">
        @auth
        @if(Auth::user()->utype === 'Admin')
        <div class="d-flex justify-content-end mb-3">
            <button id="addEntryBtn" class="btn btn-success rounded-3">
                <i class="bi bi-plus-circle"></i> Add Manual Entry
            </button>
        </div>
        @endif
        @endauth

        <!-- Search & Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold">Date Filter</h5>
                <form id="filterForm" class="row g-3 align-items-end">
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

<script>
function initializeDataExplorer() {
    const firebaseConfig = @json($firebaseConfig);
    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
    const database = firebase.database();

    const tableBody = document.getElementById('tableBody');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    const fromDate = document.getElementById('from-date');
    const toDate = document.getElementById('to-date');
    const filterForm = document.getElementById('filterForm');
    const summaryBtn = document.getElementById('summaryBtn');
    const addEntryBtn = document.getElementById('addEntryBtn');
    const isAdmin = @json(Auth::check() && Auth::user()->utype === 'Admin');


    let tableData = [];
    let filteredData = [];
    let currentPage = 1;
    const itemsPerPage = 5;

    function formatDate(dateStr) {
        const parts = dateStr.split('-');
        return `${parts[1]}/${parts[2]}/${parts[0]}`;
    }

    // Fetch Data
   const logsRef = database.ref('logs/ButtonPress');
logsRef.on('value', snapshot => {
    const data = snapshot.val();
    const grouped = {};
    let totalCount = 0; // <-- initialize total count

    if (data) {
        Object.keys(data).forEach(date => {
            const logs = data[date];
            Object.keys(logs).forEach(time => {
                const record = logs[time];

                // Only count detected records
                if (record.status === "Detected") {
                    totalCount++;
                }

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
    tableData.sort((a, b) => b.RawDate.localeCompare(a.RawDate));
    filteredData = [...tableData];
    currentPage = 1;
    renderTable();

    // Update total count in the header or badge
    updateTotalCount(totalCount);
});


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
                    ${isAdmin ? '<button class="btn btn-outline-primary btn-sm rounded-3 editDateBtn"><i class="bi bi-pencil"></i> Edit</button>' : ''}
                    ${isAdmin ? '<button class="btn btn-outline-danger btn-sm rounded-3 deleteBtn"><i class="bi bi-trash"></i> Delete</button>' : ''}
                </td>
            </tr>
        `).join('');

        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    // 🧮 Summary Modal
    summaryBtn.addEventListener('click', () => {
        if (tableData.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Data',
                text: 'There are no records to summarize.'
            });
            return;
        }

        const totalRecords = tableData.reduce((acc, row) => acc + row.Count, 0);
        const typeCounts = {};
        const locationCounts = {};

        tableData.forEach(row => {
            typeCounts[row.Type] = (typeCounts[row.Type] || 0) + row.Count;
            locationCounts[row.Location] = (locationCounts[row.Location] || 0) + row.Count;
        });

        let typeHtml = '';
        for (const type in typeCounts) {
            typeHtml += `<li><strong>${type}</strong>: ${typeCounts[type]}</li>`;
        }

        let locHtml = '';
        for (const loc in locationCounts) {
            locHtml += `<li><strong>${loc}</strong>: ${locationCounts[loc]}</li>`;
        }

        Swal.fire({
            title: '📈 Summary of Data Entries',
            html: `
                <div class="text-start">
                    <p><strong>Total Records:</strong> ${totalRecords}</p>
                    <hr>
                    <p><strong>By Type:</strong></p>
                    <ul>${typeHtml}</ul>
                    <p><strong>By Location:</strong></p>
                    <ul>${locHtml}</ul>
                </div>
            `,
            width: 500,
            confirmButtonText: 'Close',
            confirmButtonColor: '#198754'
        });
    });

    // Pagination
    prevBtn.addEventListener('click', () => { if(currentPage>1){currentPage--; renderTable();} });
    nextBtn.addEventListener('click', () => { if(currentPage<Math.ceil(filteredData.length/itemsPerPage)){currentPage++; renderTable();} });

    // Filters
        filterForm.addEventListener('submit', e => {
        e.preventDefault();

        const from = fromDate.value;
        const to = toDate.value;

        filteredData = tableData.filter(row => {
            let match = true;
            if (from) match = match && row.RawDate >= from;
            if (to) match = match && row.RawDate <= to;
            return match;
        });

        currentPage = 1;
        renderTable();
    });


    // Delete & View Actions
    tableBody.addEventListener('click', async e => {
    const tr = e.target.closest('tr');
    if (!tr) return;
    const date = tr.dataset.date;

    if (e.target.closest('.editDateBtn')) {
    Swal.fire({
        title: '📝 Edit Record Date',
        html: `
            <label class="fw-semibold">New Date</label>
            <input type="date" id="newDateInput" class="form-control" value="${date}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        confirmButtonColor: '#198754',
        preConfirm: () => {
            const newDate = document.getElementById('newDateInput').value;
            if (!newDate) {
                Swal.showValidationMessage('Please select a date.');
                return false;
            }
            return newDate;
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            const newDate = result.value;

            if (newDate === date) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Changes',
                    text: 'The date remains the same.',
                });
                return;
            }

            try {
                const oldRef = database.ref(`logs/ButtonPress/${date}`);
                const newRef = database.ref(`logs/ButtonPress/${newDate}`);

                const oldSnapshot = await oldRef.get();
                if (!oldSnapshot.exists()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No records found for this date.',
                    });
                    return;
                }

                const newSnapshot = await newRef.get();
                const oldData = oldSnapshot.val() || {};
                const newData = newSnapshot.exists() ? newSnapshot.val() : {};

                // 🧠 Merge records: if time already exists, append with a unique key
                Object.keys(oldData).forEach(timeKey => {
                    let newKey = timeKey;

                    // if time already exists in target, create a unique key
                    if (newData[newKey]) {
                        newKey = `${timeKey}_m${Date.now()}`;
                    }

                    newData[newKey] = oldData[timeKey];
                });

                // ✅ Save merged data
                await newRef.set(newData);

                // 🧹 Delete old date folder
                await oldRef.remove();

                // 🆙 Update table and UI
                tableData = tableData.filter(r => r.RawDate !== date);
                filteredData = filteredData.filter(r => r.RawDate !== date);
                renderTable();

                Swal.fire({
                    icon: 'success',
                    title: 'Date Updated!',
                    text: `Records have been moved and merged into ${newDate}.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update date: ' + err
                });
            }
        }
    });
}

    // 📌 When "View" is clicked
if (e.target.closest('.viewBtn')) {
    const dayRef = database.ref(`logs/ButtonPress/${date}`);
    const snapshot = await dayRef.get();

    if (!snapshot.exists()) {
        Swal.fire({
            icon: 'info',
            title: 'No Records',
            text: `No records found for ${date}`
        });
        return;
    }

    let entries = [];
    snapshot.forEach(child => {
        const data = child.val();
        entries.push({
            key: child.key,
            time: child.key,
            location: data.location || 'Main Gate',
            user: data.user || 'Admin',
            type: data.type || 'Plastic',
            status: data.status || 'Detected'
        });
    });

    entries.sort((a, b) => b.time.localeCompare(a.time));

    let currentPage = 1;
    const perPage = 10;
    const totalPages = Math.ceil(entries.length / perPage);

    function renderPage(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const pageEntries = entries.slice(start, end);

        let listHtml = '';
        pageEntries.forEach((entry, index) => {
            listHtml += `
                <tr data-time="${entry.time}">
                    <td>${start + index + 1}</td>
                    <td>${entry.time}</td>
                    <td>${entry.location}</td>
                    <td>${entry.user}</td>
                    <td><span class="badge ${entry.type === 'Plastic' ? 'bg-success' : 'bg-warning text-dark'}">${entry.type}</span></td>
                    <td><span class="${entry.status === 'Detected' ? 'text-success fw-semibold' : 'text-danger fw-semibold'}">${entry.status}</span></td>
                    <td>
                        ${isAdmin ? '<button class="btn btn-sm btn-outline-primary editEntryBtn"><i class="bi bi-pencil"></i></button>' : ''}
                        ${isAdmin ? '<button class="btn btn-sm btn-outline-danger deleteEntryBtn"><i class="bi bi-trash"></i></button>' : ''}
                    </td>
                </tr>
            `;
        });

        return `
            <div style="max-height: 400px; overflow-y: auto;">
                <p><strong>Total Records:</strong> ${entries.length}</p>
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Status</th>
                            ${isAdmin ? '<th>Action</th>' : ''}
                        </tr>
                    </thead>
                    <tbody>${listHtml}</tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <button id="prevModalPage" class="btn btn-sm btn-outline-dark" ${page === 1 ? 'disabled' : ''}>Previous</button>
                <span>Page ${page} of ${totalPages}</span>
                <button id="nextModalPage" class="btn btn-sm btn-outline-dark" ${page === totalPages ? 'disabled' : ''}>Next</button>
            </div>
        `;
    }

    Swal.fire({
        title: `📅 Records for ${date}`,
        html: renderPage(currentPage),
        width: 850,
        showConfirmButton: true,
        confirmButtonText: 'Close',
        didRender: () => {
            const prev = document.getElementById('prevModalPage');
            const next = document.getElementById('nextModalPage');

            if (prev) prev.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    Swal.update({ html: renderPage(currentPage) });
                }
            });

            if (next) next.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    Swal.update({ html: renderPage(currentPage) });
                }
            });

            // 🛠 Handle Edit or Delete Entry
            document.querySelectorAll('.editEntryBtn').forEach(btn => {
                btn.addEventListener('click', (ev) => {
                    const row = ev.target.closest('tr');
                    const entryTime = row.dataset.time;
                    const entry = entries.find(e => e.time === entryTime);

                    Swal.fire({
                        title: '✏️ Update Entry',
                        html: `
                            <label class="fw-semibold">Time</label>
                            <input type="time" id="editTime" class="form-control mb-2" value="${entry.time}">

                            <label class="fw-semibold">Location</label>
                            <input type="text" id="editLocation" class="form-control mb-2" value="${entry.location}">

                            <label class="fw-semibold">User</label>
                            <input type="text" id="editUser" class="form-control mb-2" value="${entry.user}">

                            <label class="fw-semibold">Type</label>
                            <select id="editType" class="form-select mb-2">
                                <option value="Plastic" ${entry.type === 'Plastic' ? 'selected' : ''}>Plastic</option>
                                <option value="Other" ${entry.type === 'Other' ? 'selected' : ''}>Other</option>
                            </select>

                            <label class="fw-semibold">Status</label>
                            <select id="editStatus" class="form-select mb-2">
                                <option value="Detected" ${entry.status === 'Detected' ? 'selected' : ''}>Detected</option>
                                <option value="Not Detected" ${entry.status === 'Not Detected' ? 'selected' : ''}>Not Detected</option>
                            </select>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Save Changes',
                        confirmButtonColor: '#198754',
                        preConfirm: () => {
                            return {
                                time: document.getElementById('editTime').value,
                                location: document.getElementById('editLocation').value,
                                user: document.getElementById('editUser').value,
                                type: document.getElementById('editType').value,
                                status: document.getElementById('editStatus').value
                            };
                        }
                    }).then(res => {
                        if (res.isConfirmed) {
                            const updated = res.value;

                            // If time changed, remove old key and add new
                            if (updated.time !== entry.time) {
                                database.ref(`logs/ButtonPress/${date}/${entry.time}`).remove();
                            }

                            database.ref(`logs/ButtonPress/${date}/${updated.time}`).set({
                                location: updated.location,
                                user: updated.user,
                                type: updated.type,
                                status: updated.status,
                                source: 'manual'
                            }).then(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Update UI entry list
                                entries = entries.map(ent =>
                                    ent.time === entry.time ? updated : ent
                                );
                                Swal.update({ html: renderPage(currentPage) });
                            });
                        }
                    });
                });
            });

            // 🗑 Handle Single Entry Delete
            document.querySelectorAll('.deleteEntryBtn').forEach(btn => {
                btn.addEventListener('click', (ev) => {
                    const row = ev.target.closest('tr');
                    const entryTime = row.dataset.time;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Delete entry at ${entryTime}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        confirmButtonColor: '#d33'
                    }).then(res => {
                        if (res.isConfirmed) {
                            database.ref(`logs/ButtonPress/${date}/${entryTime}`).remove()
                                .then(() => {
                                    entries = entries.filter(e => e.time !== entryTime);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        timer: 1200,
                                        showConfirmButton: false
                                    });
                                    Swal.update({ html: renderPage(currentPage) });
                                });
                        }
                    });
                });
            });
        }
    });
}


    // 🗑️ Delete remains the same
    if (e.target.closest('.deleteBtn')) {
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
                database.ref(`logs/ButtonPress/${date}`).remove()
                    .then(() => {
                        tableData = tableData.filter(r => r.RawDate !== date);
                        filteredData = filteredData.filter(r => r.RawDate !== date);
                        renderTable();
                        Swal.fire({
                            title: 'Deleted!',
                            text: `All records for ${date} have been deleted.`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .catch(err => {
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

    addEntryBtn.addEventListener('click', () => {
    Swal.fire({
        title: '➕ Add Manual Entry',
        html: `
            <div class="text-start">
                <label class="fw-semibold">Date</label>
                <input type="date" id="manualDate" class="form-control mb-2">

                <label class="fw-semibold">Time</label>
                <input type="time" id="manualTime" step="1" class="form-control mb-2">

                <label class="fw-semibold">Location</label>
                <input type="text" id="manualLocation" class="form-control mb-2" value="Main Gate">

                <label class="fw-semibold">Type</label>
                <select id="manualType" class="form-select mb-2">
                    <option value="Plastic">Plastic</option>
                    <option value="Other">Other</option>
                </select>

                <label class="fw-semibold">Status</label>
                <select id="manualStatus" class="form-select mb-2">
                    <option value="Detected">Detected</option>
                    <option value="Not Detected">Not Detected</option>
                </select>

                <label class="fw-semibold">User</label>
                <input type="text" id="manualUser" class="form-control mb-2" value="Admin">
            </div>
        `,
        confirmButtonText: 'Save Entry',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        preConfirm: () => {
            const date = document.getElementById('manualDate').value;
            const time = document.getElementById('manualTime').value;
            const location = document.getElementById('manualLocation').value;
            const type = document.getElementById('manualType').value;
            const status = document.getElementById('manualStatus').value;
            const user = document.getElementById('manualUser').value;

            if (!date || !time) {
                Swal.showValidationMessage('Please select both date and time.');
                return false;
            }

            return { date, time, location, type, status, user };
        }
    }).then(result => {
        if (result.isConfirmed) {
            const { date, time, location, type, status, user } = result.value;

            const entryRef = database.ref(`logs/ButtonPress/${date}/${time}`);
            entryRef.set({
                location: location,
                user: user,
                type: type,
                status: status,
                source: 'manual'
            }).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Manual Entry Added!',
                    timer: 1500,
                    showConfirmButton: false
                });
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Adding Entry',
                    text: err.message
                });
            });
        }
    });
});
}

window.addEventListener('load', initializeDataExplorer);
</script>
@endsection
