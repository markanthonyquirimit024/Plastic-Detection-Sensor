@extends('layout.base')

@section('title', 'Data Explorer')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/data-explorer.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="data-explorer-wrapper">

    <header class="text-start d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-2 text-center">Data Explorer</h1>
            <p class="text-muted mb-3 text-center">Analyze detection logs with filters and search options.</p>
        </div>
        <button id="summaryBtn" class="btn btn-outline-success rounded-3">
            <i class="bi bi-bar-chart-line"></i> View Summary
        </button>
    </header>
    <hr class="mt-0">

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

                <div class="mt-3 d-flex justify-content-center align-items-center gap-3">
                    <button id="prevPage" class="btn btn-dark btn-sm">Previous</button>
                    <span id="pageInfo" class="text-dark small">Page 1</span>
                    <button id="nextPage" class="btn btn-dark btn-sm">Next</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-database.js"></script>
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
        // Input: YYYY-MM-DD
        const parts = dateStr.split('-');
        return `${parts[1]}/${parts[2]}/${parts[0]}`; // Output: MM/DD/YYYY
    }

    // New: Format time from HH:MM:SS (24hr) to H:MM:SS AM/PM (12hr)
    function formatTime(timeStr) {
        // timeStr format: HH:MM or HH:MM:SS
        const [hours, minutes, seconds] = timeStr.split(':').map(Number);
        const date = new Date();
        date.setHours(hours, minutes || 0, seconds || 0);

        let h = date.getHours();
        const m = date.getMinutes().toString().padStart(2, '0');
        const s = date.getSeconds().toString().padStart(2, '0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12; // the hour '0' should be '12'
        
        return `${h}:${m}${seconds !== undefined ? `:${s}` : ''} ${ampm}`;
    }

    // Fetch Data
    const logsRef = database.ref('logs/ButtonPress');
    logsRef.on('value', snapshot => {
        const data = snapshot.val();
        const grouped = {};
        let totalCount = 0;

        if (data) {
            Object.keys(data).forEach(date => {
                const logs = data[date];
                Object.keys(logs).forEach(time => {
                    const record = logs[time];

                    if (record.status === "Detected") {
                        totalCount++;
                    }

                    // Key for grouping: Date|Location|User|Type|Status
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
        // Sort by date (YYYY-MM-DD) descending (Newest first)
        tableData.sort((a, b) => b.RawDate.localeCompare(a.RawDate));
        filteredData = [...tableData];
        currentPage = 1;
        renderTable();
        // NOTE: updateTotalCount function is not defined in the original code, removed call.
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

    // 🧮 Summary Modal - Logic remains same, only for brevity I'll keep the existing working code.
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
            // Edit Date Logic (kept as is)
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

            let allEntries = [];
            snapshot.forEach(child => {
                const data = child.val();
                allEntries.push({
                    key: child.key,
                    time: child.key, // HH:MM:SS
                    location: data.location || 'Main Gate',
                    user: data.user || 'Admin',
                    type: data.type || 'Plastic',
                    status: data.status || 'Detected'
                });
            });

            // Initial sort: Newest to Oldest
            allEntries.sort((a, b) => b.time.localeCompare(a.time));

            let currentEntries = [...allEntries]; // Start with all entries
            let currentPage = 1;
            const perPage = 10;
            let totalPages = Math.ceil(currentEntries.length / perPage);
            let modalFromTime = '';
            let modalToTime = '';

            function applyModalFilter() {
                currentEntries = allEntries.filter(entry => {
                    let match = true;
                    // Format entry.time (HH:MM:SS) to HH:MM:SS for comparison
                    // Assumes a time format that includes seconds, otherwise use simpler logic.
                    // Firebase time keys should handle simple string comparison if format is consistent.
                    if (modalFromTime) match = match && entry.time >= modalFromTime;
                    if (modalToTime) match = match && entry.time <= modalToTime;
                    return match;
                });

                // Re-sort after filtering (Newest to Oldest)
                currentEntries.sort((a, b) => b.time.localeCompare(a.time));

                currentPage = 1;
                totalPages = Math.ceil(currentEntries.length / perPage);
                return renderPage(currentPage);
            }

            function renderPage(page) {
                const start = (page - 1) * perPage;
                const end = start + perPage;
                const pageEntries = currentEntries.slice(start, end);

                let listHtml = '';
                pageEntries.forEach((entry, index) => {
                    listHtml += `
                        <tr data-time="${entry.time}">
                            <td>${start + index + 1}</td>
                            <td>${formatTime(entry.time)}</td>
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

                const clearFilterBtnHtml = (modalFromTime || modalToTime) ?
                    `<button id="clearModalFilter" class="btn btn-sm btn-outline-secondary">Clear Filter</button>` : '';

                return `
                    <div class="mb-3 p-3 border rounded">
                        <h6 class="fw-bold mb-2">Time Filter</h6>
                        <form id="modalFilterForm" class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="modal-from-time" class="form-label fw-semibold mb-0 small">From Time</label>
                                <input type="time" step="1" class="form-control form-control-sm rounded-3" id="modal-from-time" value="${modalFromTime}">
                            </div>
                            <div class="col-md-5">
                                <label for="modal-to-time" class="form-label fw-semibold mb-0 small">To Time</label>
                                <input type="time" step="1" class="form-control form-control-sm rounded-3" id="modal-to-time" value="${modalToTime}">
                            </div>
                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-success btn-sm rounded-3"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                        <div class="mt-2 text-end">
                            ${clearFilterBtnHtml}
                        </div>
                    </div>

                    <div style="max-height: 400px; overflow-y: auto;">
                        <p><strong>Total Records:</strong> ${currentEntries.length} (${allEntries.length} in total for this date)</p>
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
                    <div class="d-flex justify-content-between align-items-center mt-3">
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
                // NEW: Prevent closing on backdrop click
                allowOutsideClick: false,
                showConfirmButton: true,
                confirmButtonText: 'Close',
                didRender: () => {
                    // Pagination for modal
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

                    // NEW: Filter Form Submission
                    document.getElementById('modalFilterForm').addEventListener('submit', function(ev) {
                        ev.preventDefault();
                        modalFromTime = document.getElementById('modal-from-time').value;
                        modalToTime = document.getElementById('modal-to-time').value;
                        Swal.update({ html: applyModalFilter() });
                    });

                    // NEW: Clear Filter Button
                    const clearBtn = document.getElementById('clearModalFilter');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            modalFromTime = '';
                            modalToTime = '';
                            Swal.update({ html: applyModalFilter() });
                        });
                    }

                    // The rest of the interactive logic (Edit/Delete single entry) needs to be inside the didRender to re-attach events after a page change/filter
                    // 🛠 Handle Edit or Delete Entry
                    const entryActionHandler = (ev) => {
                        const row = ev.target.closest('tr');
                        if (!row) return;
                        const entryTime = row.dataset.time;
                        // Find the entry in the currently displayed set (currentEntries)
                        const entry = currentEntries.find(e => e.time === entryTime);

                        // --- Edit Logic ---
                        if (ev.target.closest('.editEntryBtn')) {
                            Swal.fire({
                                // ... (Edit modal content and logic remains the same)
                                title: '✏️ Update Entry',
                                html: `
                                    <label class="fw-semibold">Time</label>
                                    <input type="time" step="1" id="editTime" class="form-control mb-2" value="${entry.time}">

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

                                        // Update the data in local arrays (allEntries and currentEntries)
                                        allEntries = allEntries.map(ent =>
                                            ent.time === entry.time ? updated : ent
                                        ).filter(ent => ent.time !== entry.time ? true : true); // Filter is a hacky way to ensure map-replace works
                                        if (updated.time !== entry.time) {
                                            allEntries = allEntries.filter(ent => ent.time !== entry.time);
                                            allEntries.push(updated); // Add new entry with new time
                                            allEntries.sort((a, b) => b.time.localeCompare(a.time)); // Re-sort all entries
                                        } else {
                                             // if time is the same, find and replace
                                            const index = allEntries.findIndex(e => e.time === entry.time);
                                            if (index !== -1) allEntries[index] = updated;
                                        }


                                        Swal.update({ html: applyModalFilter() }); // Re-render table with new data
                                    });
                                }
                            });
                        }
                        // --- Delete Logic ---
                        else if (ev.target.closest('.deleteEntryBtn')) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: `Delete entry at ${formatTime(entryTime)}?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!',
                                confirmButtonColor: '#d33'
                            }).then(res => {
                                if (res.isConfirmed) {
                                    database.ref(`logs/ButtonPress/${date}/${entryTime}`).remove()
                                        .then(() => {
                                            // Remove from local arrays
                                            allEntries = allEntries.filter(e => e.time !== entryTime);
                                            currentEntries = currentEntries.filter(e => e.time !== entryTime);

                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Deleted!',
                                                timer: 1200,
                                                showConfirmButton: false
                                            });
                                            
                                            // Check if current page is now empty after deletion, and if so, go back one page
                                            if (currentEntries.length > 0 && currentPage > Math.ceil(currentEntries.length / perPage)) {
                                                currentPage--;
                                            } else if (currentEntries.length === 0) {
                                                currentPage = 1; // reset page to 1 if no entries left
                                            }

                                            Swal.update({ html: renderPage(currentPage) }); // Re-render table
                                        });
                                }
                            });
                        }
                    };

                    document.querySelector('.swal2-html-container').addEventListener('click', entryActionHandler);

                }
            });
        }


        // 🗑️ Delete logic (for the entire day's records) remains the same
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