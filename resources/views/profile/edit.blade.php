@include('layout.base')
<title>Profile</title>

<style>
    /* Main Content */
    .main-content {
    flex: 1;
    padding: 40px;
    color: white;
    margin-left: 0;
    transition: margin-left 0.3s ease;
    width: 100%;
    }

    .welcome-message {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* Profile Wrapper */
    .profile-wrapper {
        display: flex;
        justify-content: space-between;
        gap: 30px;
        max-width: 900px;
        margin: auto;
    }

    .left-section, .right-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Profile Sections */
    .profile-section-card {
        background: rgba(255, 255, 255, 0.15);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        text-align: center;
    }

    /* Form Styling */
    .form-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        width: 100%;
        max-width: 350px;
        margin: auto;
        text-align: left;
    }

    /* Input Fields */
    .input-group {
        display: flex;
        flex-direction: column;
    }

    .input-label {
        font-size: 14px;
        color: white;
        margin-bottom: 5px;
    }

    .input-field {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.3);
        color: white;
        outline: none;
        transition: 0.3s;
        box-shadow: inset 0px 0px 5px rgba(255, 255, 255, 0.3);
    }

    .input-field::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    /* Buttons */
    .action-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(90deg, rgb(71, 71, 71), rgb(74, 143, 68));
        border: none;
        color: white;
        font-size: 16px;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }

    .delete-btn {
        background: red;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .profile-wrapper {
            flex-direction: column;
            align-items: center;
        }

        .left-section, .right-section {
            width: 100%;
        }
    }

    .welcome-message {
        font-size: 1.8rem;
        font-weight: bold;
        
        margin-left: 50px;
    }

    .sidebar.active ~ .main-content {
        margin-left: 270px;
    }

    </style>
</head>
<body>

    <!-- Main Content -->
    <main class="main-content">
        <div class="welcome-message">Welcome to Your Profile, {{ Auth::user()->name }}!</div>

        <div class="profile-wrapper">
            <!-- Left Side: Edit Profile & Update Password -->
            <div class="left-section">
                <!-- Edit Profile -->
                <div class="profile-section-card">
                    <h2>Edit Profile</h2>
                    <form class="form-container" action="{{ route('profile.edit') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <label class="input-label">Your Full Name</label>
                            <input type="text" name="name" class="input-field" required value="{{ Auth::user()->name }}">
                        </div>

                        <div class="input-group">
                            <label class="input-label">Your Email Address</label>
                            <input type="email" name="email" class="input-field" required value="{{ Auth::user()->email }}">
                        </div>

                        <button type="submit" class="action-btn">Update Profile</button>
                    </form>
                </div>

                <!-- Update Password -->
                <div class="profile-section-card">
                    <h2>Update Password</h2>
                    <form class="form-container" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <label class="input-label">Current Password</label>
                            <input type="password" name="current_password" class="input-field" required>
                        </div>

                        <div class="input-group">
                            <label class="input-label">New Password</label>
                            <input type="password" name="new_password" class="input-field" required>
                        </div>

                        <button type="submit" class="action-btn">Update Password</button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Delete Account -->
            <div class="right-section">
                <div class="profile-section-card">
                <h2>Delete Account</h2>
                <form class="form-container" action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <label class="input-label">Confirm Your Password</label>
                        <input type="password" name="password" class="input-field" placeholder="Enter Password" required>
                    </div>

                    <button type="submit" class="action-btn delete-btn"
                        onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                        Delete Account
                    </button>
                </div>
    </div>
        </div>
    </main>

</body>
