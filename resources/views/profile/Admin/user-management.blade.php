@include('layout.base')
<title>User Management</title>
<link rel="stylesheet" href="{{ asset('assets/user-management.css') }}">

  <div class="container">
    <div class="card shadow-lg border-0 rounded-4 p-4" id="usertable">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="fw-bold text-dark mb-0">Manage Analysts</h3>
        <a class="btn btn-success px-4 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#popupForm">
          + Create Analyst
        </a>
        <form method="GET" action="{{ route('admin.user-management') }}" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" 
              placeholder="Search users..." value="{{ request('search') }}">
          <button type="submit" class="btn btn-success w-100 rounded-3">
            <i class="bi bi-search me-1"></i> Search
          </button>        
        </form>
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
              <th>Action</th>x
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
                  <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $analyst->id }}"><i class="fa fa-edit me-1"></i>Edit</button>

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


@if(session('success'))
<div class="toast position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true"
     data-bs-delay="3000" data-bs-autohide="true">
  <div class="toast-header">
    <strong class="me-auto text-success">Success</strong>
    <small class="text-body-secondary">Just now</small>
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
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
          <div>
          <div class="row">
          <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control"
              value="{{ old('first_name') }}" required autofocus>
          </div>

          <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control"
              value="{{ old('last_name') }}" required>
          </div>
        </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control"
              value="{{ old('email') }}" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
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
            <label for="password_confirmation" class="form-label">Confirm Password</label>
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

<script>
  document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", function () {
      const field = document.getElementById(this.dataset.target);
      field.type = field.type === "password" ? "text" : "password";
      this.textContent = field.type === "password" ? "👁️" : "🙈";
    });
  });

  const checklist = { length: false, lowercase: false, uppercase: false, number: false, special: false };

  document.getElementById("password").addEventListener("input", function () {
    const value = this.value;
    checklist.length = value.length >= 8;
    checklist.lowercase = /[a-z]/.test(value);
    checklist.uppercase = /[A-Z]/.test(value);
    checklist.number = /\d/.test(value);
    checklist.special = /[@$!%*?&]/.test(value);

    for (let key in checklist) updateChecklist(key, checklist[key]);

    document.getElementById("password-checklist").classList.toggle("hidden", Object.values(checklist).every(Boolean));
  });

  function updateChecklist(id, isValid) {
    const item = document.getElementById(id);
    item.style.color = isValid ? "lime" : "red";
    item.textContent = `${isValid ? '✔' : '✖'} ${item.textContent.slice(2)}`;
  }

  document.getElementById("register-form").addEventListener("submit", function (event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("password_confirmation").value;

    if (!Object.values(checklist).every(Boolean)) {
      alert("Password does not meet all the requirements.");
      event.preventDefault();
    } else if (password !== confirmPassword) {
      alert("Passwords do not match!");
      event.preventDefault();
    }
  });
  

  document.querySelectorAll(".edit-user-form").forEach(form => {
  const userId = form.querySelector("[name=password]").id.split("_").pop();
  const passwordInput = document.getElementById(`edit_password_${userId}`);
  const confirmInput = document.getElementById(`edit_password_confirmation_${userId}`);
  const checklist = document.getElementById(`edit-password-checklist-${userId}`);

  const requirements = {
    length: /.{8,}/,
    lowercase: /[a-z]/,
    uppercase: /[A-Z]/,
    number: /\d/,
    special: /[@$!%*?&]/
  };

  passwordInput.addEventListener("input", function () {
    if (this.value) {
      checklist.style.display = "block";
      checklist.querySelectorAll("li").forEach(item => {
        const key = item.className;
        const valid = requirements[key].test(this.value);
        item.style.color = valid ? "lime" : "red";
        item.textContent = `${valid ? '✔' : '✖'} ${item.textContent.slice(2)}`;
      });
    } else {
      checklist.style.display = "none";
    }
  });

  form.addEventListener("submit", function (event) {
    if (passwordInput.value) {
      const allValid = Object.values(requirements).every(regex => regex.test(passwordInput.value));
      if (!allValid) {
        alert("New password does not meet all requirements.");
        event.preventDefault();
      } else if (passwordInput.value !== confirmInput.value) {
        alert("Passwords do not match.");
        event.preventDefault();
      }
    }
  });
});

</script>