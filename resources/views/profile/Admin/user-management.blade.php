@extends('layout.base')

@section('title', 'User Management')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/user-management.css') }}">

<div class="container mt-3">

    <div class="mb-4">
        <h1 class="fw-bold text-dark">User Management</h1>
        <p class="text-muted">Manage your analysts and user accounts below.</p>
    </div>

    <div class="card border-1 rounded-4 p-4" id="usertable">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h3 class="fw-bold text-dark mb-2">Manage Analysts</h3>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a class="btn btn-success px-4 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#popupForm">
                    + Create Analyst
                </a>
                <form method="GET" action="{{ route('admin.user-management') }}" class="d-flex mb-0">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-success rounded-3">
                        <i class="bi bi-search me-1"></i> Search
                    </button>         
                </form>
            </div>
        </div>

        <div class="table-responsive">
            @if($analysts->count() > 0)
            <table class="table table-hover align-middle">
                <thead class="table-gradient text-white">
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analysts as $analyst)
                    <tr>
                        <td>{{ $analyst->id }}</td>
                        <td>{{ $analyst->first_name }}</td>
                        <td>{{ $analyst->last_name }}</td>
                        <td>{{ $analyst->email }}</td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $analyst->id }}">
                                ✏️ Edit
                            </button>

                            <!-- EDIT MODAL -->
                            <div class="modal fade" id="editUserModal{{ $analyst->id }}" tabindex="-1" aria-hidden="true"
                                data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content edit-modal">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="fas fa-user-edit"></i> Edit Analyst</h5>
                                            <button type="button" class="btn-close bg-light me-1" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.edit-user', $analyst->id) }}" method="POST" class="edit-user-form">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $analyst->first_name) }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $analyst->last_name) }}" required>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ old('email', $analyst->email) }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">New Password</label>
                                                        <div class="password-wrapper position-relative">
                                                            <input type="password" name="password" id="edit_password_{{ $analyst->id }}" class="form-control">
                                                            <button type="button" class="toggle-password position-absolute top-50 end-0 translate-middle-y me-2" data-target="edit_password_{{ $analyst->id }}">👁️</button>
                                                        </div>
                                                        <small class="text-light">Leave blank to keep current password</small>
                                                        <ul id="edit-password-checklist-{{ $analyst->id }}" class="mt-2" style="display:none;">
                                                            <li class="length">✖ At least 8 characters</li>
                                                            <li class="lowercase">✖ At least one lowercase letter</li>
                                                            <li class="uppercase">✖ At least one uppercase letter</li>
                                                            <li class="number">✖ At least one number</li>
                                                            <li class="special">✖ At least one special character (@$!%*?&)</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Confirm Password</label>
                                                        <div class="password-wrapper position-relative">
                                                            <input type="password" name="password_confirmation" id="edit_password_confirmation_{{ $analyst->id }}" class="form-control">
                                                            <button type="button" class="toggle-password position-absolute top-50 end-0 translate-middle-y me-2" data-target="edit_password_confirmation_{{ $analyst->id }}">👁️</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.delete-user', $analyst->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-danger btn-sm delete-btn">
                                    🗑️ Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $analysts->appends(['search' => request('search')])->links() }}
            @else
            <div class="alert alert-warning text-center">
                No results found for "<strong>{{ request('search') }}</strong>"
            </div>
            @endif
        </div>
    </div>
</div>

<!-- CREATE ANALYST MODAL -->
<div class="modal fade" id="popupForm" tabindex="-1" aria-labelledby="popupFormLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title" id="popupFormLabel">Create Analyst</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="register-form" action="{{ route('admin.create-analyst') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" required autofocus>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper position-relative">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <button type="button" class="toggle-password position-absolute top-50 end-0 translate-middle-y me-2" data-target="password">👁️</button>
                        </div>
                    </div>

                    <ul id="password-checklist" class="mb-3">
                        <li id="length">✖ At least 8 characters</li>
                        <li id="lowercase">✖ At least one lowercase letter</li>
                        <li id="uppercase">✖ At least one uppercase letter</li>
                        <li id="number">✖ At least one number</li>
                        <li id="special">✖ At least one special character (@$!%*?&)</li>
                    </ul>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="password-wrapper position-relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            <button type="button" class="toggle-password position-absolute top-50 end-0 translate-middle-y me-2" data-target="password_confirmation">👁️</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Create Analyst</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const editForms = document.querySelectorAll('.edit-user-form');

    editForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update this analyst?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: 'Analyst information has been updated successfully.',
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'An error occurred.');
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message,
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('.delete-form');

            Swal.fire({
                title: 'Are you sure?',
                text: "This account will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to create this analyst?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, create it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Analyst account created successfully!',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    return response.json().then(data => {
                        let errorMessage = data.message || 'Something went wrong';
                        if (data.errors) {
                            errorMessage += '<br><ul>' + Object.values(data.errors).map(err => `<li>${err[0]}</li>`).join('') + '</ul>';
                        }
                        throw new Error(errorMessage);
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: error.message,
                    confirmButtonColor: '#d33'
                });
            });
        }
    });
});

document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", function() {
        const field = document.getElementById(this.dataset.target);
        field.type = field.type === "password" ? "text" : "password";
        this.textContent = field.type === "password" ? "👁️" : "🙈";
    });
});

// Password checklist
const checklist = { length: false, lowercase: false, uppercase: false, number: false, special: false };
const passwordField = document.getElementById("password");
passwordField.addEventListener("input", function() {
    const value = this.value;
    checklist.length = value.length >= 8;
    checklist.lowercase = /[a-z]/.test(value);
    checklist.uppercase = /[A-Z]/.test(value);
    checklist.number = /\d/.test(value);
    checklist.special = /[@$!%*?&]/.test(value);

    for (let key in checklist) updateChecklist(key, checklist[key]);
    document.getElementById("password-checklist").classList.toggle("hidden", Object.values(checklist).every(Boolean));
});

function updateChecklist(id, valid) {
    const item = document.getElementById(id);
    item.style.color = valid ? "lime" : "red";
    item.textContent = `${valid ? '✔' : '✖'} ${item.textContent.slice(2)}`;
}

document.getElementById("register-form").addEventListener("submit", function(event) {
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("password_confirmation").value;

    if (password && !Object.values(checklist).every(Boolean)) {
        event.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Password Error',
            text: 'Password does not meet all requirements.',
            confirmButtonColor: '#d33'
        });
        return;
    } 
    if (password !== confirm) {
        event.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'Passwords do not match!',
            confirmButtonColor: '#d33'
        });
        return;
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const requirements = {
        length: /.{8,}/,
        lowercase: /[a-z]/,
        uppercase: /[A-Z]/,
        number: /\d/,
        special: /[@$!%*?&]/
    };

    document.querySelectorAll(".edit-user-form").forEach(form => {
        const passwordInput = form.querySelector("[name='password']");
        const confirmInput = form.querySelector("[name='password_confirmation']");
        const checklistEl = form.querySelector("ul[id^='edit-password-checklist']");
        const checklistItems = checklistEl ? checklistEl.querySelectorAll("li") : [];

        if (passwordInput) {
            passwordInput.addEventListener("input", () => {
                const value = passwordInput.value;
                if (checklistEl) checklistEl.style.display = value ? "block" : "none";

                checklistItems.forEach(item => {
                    const key = item.className;
                    const isValid = requirements[key].test(value);
                    item.style.color = isValid ? "lime" : "red";
                    item.textContent = `${isValid ? '✔' : '✖'} ${item.textContent.slice(2)}`;
                });
            });
        }

        form.addEventListener("submit", (event) => {
            const newPassword = passwordInput.value.trim();
            const confirmPassword = confirmInput.value.trim();

            if (newPassword) {
                const allValid = Object.entries(requirements).every(([_, regex]) => regex.test(newPassword));

                if (!allValid) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Password',
                        text: 'New password does not meet all requirements.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Passwords do not match!',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }
        });
    });
});
</script>
@endsection
