@extends('layout.base')

@section('title', 'My Profile')
<link rel="stylesheet" href="{{ asset('assets/edit.css') }}">

@section('content')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="page-title">My Profile</div>

    <div class="profile-layout">
        <!-- Left Overview -->
        <div class="profile-overview">
            <img src="{{ asset('images/person.png') }}" class="profile-avatar" alt="Avatar">
            <div class="user-name">{{ Auth::user()->first_name }}</div>
            <div class="user-role">Role: {{ Auth::user()->utype ?? 'Analyst' }}</div>

            <div class="stats">
                <div class="stat-card">
                    <h3>Last Login</h3>
                    <p>{{ Auth::user()->last_login ? \Carbon\Carbon::parse(Auth::user()->last_login)->format('M d, Y h:i A') : 'Today'  }}</p>
                </div>
            </div>
        </div>

        <!-- Right Settings -->
        <div class="profile-settings">
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab(event, 'profile')">👤 Profile Info</button>
                <button class="tab-btn" onclick="switchTab(event, 'security')">🔒 Security</button>
                <button class="tab-btn" onclick="switchTab(event, 'danger')">⚠️ Danger Zone</button>
            </div>

            <x-validation-errors/>

            {{-- Profile Info --}}
            <div id="profile" class="tab-content active">
                <form id="profileForm" class="form-container" action="{{ route('profile.edit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <label class="input-label">First Name</label>
                    <input type="text" name="first_name" class="input-field" value="{{ Auth::user()->first_name }}" required>

                    <label class="input-label">Last Name</label>
                    <input type="text" name="last_name" class="input-field" value="{{ Auth::user()->last_name }}" required>

                    <label class="input-label">Email</label>
                    <input type="email" name="email" class="input-field" value="{{ Auth::user()->email }}" required>

                    <button type="button" class="action-btn primary" onclick="confirmUpdate()">💾 Save Changes</button>
                </form>
            </div>

            {{-- Security --}}
            <div id="security" class="tab-content">
                <form id="change-password-form" class="form-container" action="{{ route('profile.change_password') }}" method="POST">
                    @csrf
                    <!-- Current Password -->
                    <label class="input-label" for="current_password">Current Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="current_password" name="current_password" class="input-field" required>
                        <button type="button" class="toggle-password" data-target="current_password">👁️</button>
                    </div>

                    <!-- New Password -->
                    <label class="input-label mt-3" for="password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="input-field" required>
                        <button type="button" class="toggle-password" data-target="password">👁️</button>
                    </div>

                    <ul id="password-checklist" class="mt-2 text-sm text-gray-600">
                        <li id="length">✖ At least 8 characters</li>
                        <li id="lowercase">✖ At least one lowercase letter</li>
                        <li id="uppercase">✖ At least one uppercase letter</li>
                        <li id="number">✖ At least one number</li>
                        <li id="special">✖ At least one special character (@$!%*?&)</li>
                    </ul>

                    <!-- Confirm Password -->
                    <label class="input-label mt-3" for="password_confirmation">Confirm New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" required>
                        <button type="button" class="toggle-password" data-target="password_confirmation">👁️</button>
                    </div>

                    <button type="button" class="action-btn primary mt-4" onclick="confirmPasswordChange()">
                        🔑 Update Password
                    </button>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div id="danger" class="tab-content">
                <form class="form-container delete-form" action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <label class="input-label">Confirm Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Enter Password" required>

                    <button type="button" class="action-btn danger delete-btn">
                        🗑️ Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ✅ Delete Account Confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
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
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // ✅ Profile Update Confirmation
    window.confirmUpdate = function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to update your profile details?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('profileForm').submit();
            }
        });
    }

    // ✅ Password Change Confirmation
    window.confirmPasswordChange = function() {
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("password_confirmation").value;

        if (!Object.values(requirements).every(Boolean)) {
            Swal.fire({ icon: 'warning', title: 'Weak Password', text: 'Your password does not meet all the requirements.' });
            return;
        }
        if (password !== confirmPassword) {
            Swal.fire({ icon: 'error', title: 'Mismatch', text: 'Passwords do not match.' });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to change your password?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('change-password-form').submit();
            }
        });
    }

    // ✅ Password visibility toggle
    document.querySelectorAll(".toggle-password").forEach(button => {
        button.addEventListener("click", function () {
            const field = document.getElementById(this.dataset.target);
            field.type = field.type === "password" ? "text" : "password";
            this.textContent = field.type === "password" ? "👁️" : "🙈";
        });
    });

    // ✅ Password strength indicator
    const passwordInput = document.getElementById('password');
    const checklist = {
        length: document.getElementById('length'),
        lowercase: document.getElementById('lowercase'),
        uppercase: document.getElementById('uppercase'),
        number: document.getElementById('number'),
        special: document.getElementById('special')
    };

    window.requirements = {
        length: false,
        lowercase: false,
        uppercase: false,
        number: false,
        special: false
    };

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;

        requirements.length = value.length >= 8;
        requirements.lowercase = /[a-z]/.test(value);
        requirements.uppercase = /[A-Z]/.test(value);
        requirements.number = /[0-9]/.test(value);
        requirements.special = /[@$!%*?&]/.test(value);

        for (let key in requirements) {
            checklist[key].textContent = (requirements[key] ? '✔' : '✖') + checklist[key].textContent.substring(1);
            checklist[key].style.color = requirements[key] ? 'green' : 'red';
        }
    });

    // ✅ Tab switcher
    window.switchTab = function(event, tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));

        event.currentTarget.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    // ✅ Global SweetAlerts for session messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please check your input and try again.',
            confirmButtonText: 'OK'
        });
    @endif

});
</script>

