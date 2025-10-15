@extends('layout.base')

@section('title', 'My Profile')
<link rel="stylesheet" href="{{ asset('assets/edit.css') }}">

@section('content')
    <div class="page-title">My Profile</div>

    <div class="profile-layout">
        <!-- Left Overview -->
        <div class="profile-overview">
            <img src="{{ asset('images/person.png') }}" class="profile-avatar" alt="Avatar">
            <div class="user-name">{{ Auth::user()->first_name }}</div>
            <div class="user-role">Role: {{ Auth::user()->utype ?? 'Analyst' }}</div>

            <div class="stats">
                <div class="stat-card">
                    <h3>Reports Generated</h3>
                    <p>12</p>
                </div>
                <div class="stat-card">
                    <h3>Data Queries</h3>
                    <p>34</p>
                </div>
                <div class="stat-card">
                    <h3>Last Login</h3>
                    <p>{{ Auth::user()->last_login ?? 'Today' }}</p>
                </div>
            </div>
        </div>

        <!-- Right Settings -->
        <div class="profile-settings">
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('profile')">👤 Profile Info</button>
                <button class="tab-btn" onclick="switchTab('security')">🔒 Security</button>
                <button class="tab-btn" onclick="switchTab('danger')">⚠️ Danger Zone</button>
            </div>

            <x-success-message/>
            <x-validation-errors/>

            {{-- Profile Info --}}
            <div id="profile" class="tab-content active">
                <form class="form-container" action="{{ route('profile.edit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <label class="input-label">First Name</label>
                    <input type="text" name="first_name" class="input-field" value="{{ Auth::user()->first_name }}">

                    <label class="input-label">Last Name</label>
                    <input type="text" name="last_name" class="input-field" value="{{ Auth::user()->last_name }}">

                    <label class="input-label">Email</label>
                    <input type="email" name="email" class="input-field" value="{{ Auth::user()->email }}">

                    <button type="submit" class="action-btn primary" onclick="return confirm('Are you sure you want to update your profile details?')">
                        💾 Save Changes
                    </button>
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

                    <button type="submit" class="action-btn primary mt-4" onclick="return confirm('Are you sure you want to change your password?')">
                        🔑 Update Password
                    </button>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div id="danger" class="tab-content">
                <form class="form-container" action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <label class="input-label">Confirm Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Enter Password" required>

                    <button type="submit" class="action-btn danger"
                        onclick="return confirm('⚠️ This will permanently delete your account. Continue?')">
                        🗑️ Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    // Toggle password visibility
    document.querySelectorAll(".toggle-password").forEach(button => {
        button.addEventListener("click", function () {
            const field = document.getElementById(this.dataset.target);
            field.type = field.type === "password" ? "text" : "password";
            this.textContent = field.type === "password" ? "👁️" : "🙈";
        });
    });

    const passwordInput = document.getElementById('password');
    const checklist = {
        length: document.getElementById('length'),
        lowercase: document.getElementById('lowercase'),
        uppercase: document.getElementById('uppercase'),
        number: document.getElementById('number'),
        special: document.getElementById('special')
    };

    let requirements = {
        length: false,
        lowercase: false,
        uppercase: false,
        number: false,
        special: false
    };

    passwordInput.addEventListener('input', function () {
        const value = passwordInput.value;

        requirements.length = value.length >= 8;
        checklist.length.textContent = requirements.length ? "✔ At least 8 characters" : "✖ At least 8 characters";
        checklist.length.style.color = requirements.length ? "green" : "red";

        requirements.lowercase = /[a-z]/.test(value);
        checklist.lowercase.textContent = requirements.lowercase ? "✔ At least one lowercase letter" : "✖ At least one lowercase letter";
        checklist.lowercase.style.color = requirements.lowercase ? "green" : "red";

        requirements.uppercase = /[A-Z]/.test(value);
        checklist.uppercase.textContent = requirements.uppercase ? "✔ At least one uppercase letter" : "✖ At least one uppercase letter";
        checklist.uppercase.style.color = requirements.uppercase ? "green" : "red";

        requirements.number = /[0-9]/.test(value);
        checklist.number.textContent = requirements.number ? "✔ At least one number" : "✖ At least one number";
        checklist.number.style.color = requirements.number ? "green" : "red";

        requirements.special = /[@$!%*?&]/.test(value);
        checklist.special.textContent = requirements.special ? "✔ At least one special character (@$!%*?&)" : "✖ At least one special character (@$!%*?&)";
        checklist.special.style.color = requirements.special ? "green" : "red";
    });

    document.getElementById("change-password-form").addEventListener("submit", function (event) {
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("password_confirmation").value;
        const allRequirementsMet = Object.values(requirements).every(Boolean);

        if (!allRequirementsMet) {
            alert("⚠ Password does not meet all the requirements.");
            event.preventDefault();
        } else if (password !== confirmPassword) {
            alert("⚠ Passwords do not match!");
            event.preventDefault();
        }
    });
</script>
