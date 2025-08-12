@include('layout.base')
<title>User Management</title>
<link rel="stylesheet" href="{{ asset('assets/user-management.css') }}">

<div class="main-content" id="main-content">
  <div class="container-fluid">
    <div class="table-responsive" id="usertable">
      <h1>Manage Analysts</h1>
      <div class="text-end mb-3">
        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#popupForm">Create Analyst</a>
      </div>
      <table class="table table-bordered table-light table-striped text-center">
        <thead class="table-info">
          <tr>
            <th>ID</th>
            <th>FIRST NAME</th>
            <th>LAST NAME</th>
            <th>EMAIL</th>
            <th>ACTION</th>
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
              <form action="#" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-info">
                  <i class="fa fa-edit"></i> Edit Account
                </button>
              </form>
              <form action="{{ route('admin.delete-user', $analyst->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                  onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
                  <i class="fa fa-times"></i> Delete Account
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
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
    <div class="modal-content bg-light bg-opacity-75">
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
  // Toggle password visibility
  document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", function () {
      const field = document.getElementById(this.dataset.target);
      field.type = field.type === "password" ? "text" : "password";
      this.textContent = field.type === "password" ? "👁️" : "🙈";
    });
  });

  // Password checklist
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

  // Form validation
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
</script>