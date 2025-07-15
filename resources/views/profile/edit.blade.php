<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            background: url("../images/coverwel2.png") no-repeat center center/cover;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(32, 32, 32, 0.5);
        z-index: -1;
      }


        /* Sidebar */
        .sidebar {
            width: 270px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            position: fixed;
            left: -270px; /* Initially hidden */
            top: 0;
            height: 100%;
            transition: left 0.4s ease-in-out;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-radius: 0 20px 20px 0;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        /* Back Button */
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px;
            border: none;
            font-size: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s;
            width: 100%;
            margin-bottom: 20px;
            text-align: left;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .profile-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #c8a2c8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }

        .profile-name {
            font-weight: bold;
            font-size: 1rem;
            color: white;
        }

        .nav-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.3s, transform 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        /* Logout Button */
        .logout-btn {
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            background: rgba(255, 0, 0, 0.6);
            border: none;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 0, 0, 0.8);
            transform: scale(1.05);
        }

        /* Hamburger Button */
        .hamburger {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #333;
            color: white;
            padding: 12px;
            border: none;
            font-size: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s;
            z-index: 1001;
        }

        .hamburger:hover {
            background: #555;
            transform: scale(1.05);
        }

        /* Main Content */
         /* Main Content */
         .main-content {
            flex: 1;
            padding: 40px;
            color: white;
            margin-left: 0;
            transition: margin-left 0.3s ease;
            width: 100%;
        }

        .sidebar.active ~ .main-content {
            margin-left: 250px;
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
            
            margin-left: 30px;
        }

        .sidebar.active ~ .main-content {
            margin-left: 270px;
        }

    </style>
</head>
<body>

    <!-- Hamburger Button -->
    <button class="hamburger" id="hamburger-btn" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <button class="back-btn" onclick="toggleSidebar()">← Close Dashboard</button>

        <div class="profile-section">
            <div class="profile-icon">👤</div>
            <a href="{{ route('profile.edit') }}" class="profile-name">MY PROFILE</a>
        </div>

        <nav>
            <ul>
                <li><a href="{{ route('dashboard') }}" class="nav-link">DASHBOARD</a></li>
            </ul>
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">LOGOUT</button>
        </form>
    </aside>

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
    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let hamburger = document.getElementById("hamburger-btn");

            if (sidebar.classList.contains("active")) {
                sidebar.classList.remove("active");
                setTimeout(() => {
                    hamburger.style.display = "block"; // Show hamburger button
                }, 300);
            } else {
                sidebar.classList.add("active");
                hamburger.style.display = "none"; // Hide hamburger button
            }
        }
    </script>

</body>
</html>
