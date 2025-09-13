@include('layout.base')
<title>User Management</title>
<link rel="stylesheet" href="{{ asset('assets/user-management.css') }}">

<div class="main-content py-5" id="main-content">
  <div class="container">

    <!-- Page Header -->
    <div class="page-header text-center mb-5">
      <h1 class="fw-bold ">👥 User Management</h1>
      <p class="text-muted fs-5">Manage all analysts in the system — create, edit, or remove accounts with ease.</p>
    </div>

    <!-- User Table Card -->
    <div class="card shadow-lg border-0 rounded-4 p-4" id="usertable">
      
      <!-- Actions -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-3 mb-md-0">Analysts List</h4>
        
        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
          <a class="btn btn-gradient px-4 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#popupForm">
            + Create Analyst
          </a>
          <form method="GET" action="{{ route('admin.user-management') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" 
                  placeholder="Search users..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
          </form>
        </div>
      </div>

      <!-- Table -->
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
                  <!-- Edit Button -->
                  <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $analyst->id }}">
                    <i class="fa fa-edit me-1"></i>Edit
                  </button>

                  <!-- Edit Modal -->
                  <div class="modal fade" id="editUserModal{{ $analyst->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                      <div class="modal-content edit-modal bg-secondary">
                        <div class="modal-header">
                          <h5 class="modal-title"><i class="fas fa-user-edit"></i> Edit Analyst</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.edit-user', $analyst->id) }}" method="POST" class="edit-user-form">
                          @csrf
                          @method('PUT')
                          <div class="modal-body">
                            <div class="row g-3">
                              <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                      value="{{ old('first_name', $analyst->first_name) }}" required>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                      value="{{ old('last_name', $analyst->last_name) }}" required>
                              </div>
                              <div class="col-md-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                      value="{{ old('email', $analyst->email) }}" required>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <div class="password-wrapper">
                                  <input type="password" name="password" id="edit_password_{{ $analyst->id }}" class="form-control">
                                  <button type="button" class="toggle-password" data-target="edit_password_{{ $analyst->id }}">👁️</button>
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
                                <div class="password-wrapper">
                                  <input type="password" name="password_confirmation" id="edit_password_confirmation_{{ $analyst->id }}" class="form-control">
                                  <button type="button" class="toggle-password" data-target="edit_password_confirmation_{{ $analyst->id }}">👁️</button>
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

                  <!-- Delete -->
                  <form action="{{ route('admin.delete-user', $analyst->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"
                      onclick="return confirm('Are you sure want to delete this account?')">
                      <i class="fa fa-trash me-1"></i>Delete
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
</div>

<!-- Toast -->
@if(session('success'))
<div class="toast position-fixed bottom-0 end-0 m-3" role="alert" data-bs-delay="3000" data-bs-autohide="true">
  <div class="toast-header">
    <strong class="me-auto text-success">Success</strong>
    <small class="text-body-secondary">Just now</small>
    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
  </div>
  <div class="toast-body bg-dark text-white">
    {{ session('success') }}
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const toastEl = document.querySelector('.toast');
    if (toastEl) new bootstrap.Toast(toastEl).show();
  });
</script>
@endif

<!-- Create Modal -->
<div class="modal fade" id="popupForm" tabindex="-1" aria-labelledby="popupFormLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary bg-opacity-75">
      <div class="modal-header">
        <h5 class="modal-title" id="popupFormLabel">Create Analyst</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="register-form" action="{{ route('admin.create-analyst') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" required autofocus>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
              <input type="password" id="password" name="password" class="form-control" required>
              <button type="button" class="toggle-password" data-target="password">👁️</button>
            </div>
          </div>
          <ul id="password-checklist">
            <li id="length">✖ At least 8 characters</li>
            <li id="lowercase">✖ At least one lowercase letter</li>
            <li id="uppercase">✖ At least one uppercase letter</li>
            <li id="number">✖ At least one number</li>
            <li id="special">✖ At least one special character (@$!%*?&)</li>
          </ul>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="password-wrapper">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
              <button type="button" class="toggle-password" data-target="password_confirmation">👁️</button>
            </div>
          </div>
          <button type="submit" class="btn btn-success">Create</button>
        </form>
      </div>
    </div>
  </div>
</div>
