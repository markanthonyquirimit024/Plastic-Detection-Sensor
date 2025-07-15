<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <title>Reset Password</title>
    <style>
        /* General styling */
        body {
            background: url("../images/coverwel2.png") no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        /* Background overlay */
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

        /* Reset Password container */
        .reset-container {
            background: rgba(255, 255, 255, 0.15);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 400px;
            backdrop-filter: blur(10px);
            position: relative;
        }

        /* Logo */
        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.5);
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Input fields */
        .input-field {
            width: 90%;
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            outline: none;
            transition: 0.3s;
            box-shadow: inset 0px 0px 5px rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.5);
            box-shadow: inset 0px 0px 10px rgba(255, 255, 255, 0.5);
        }

        /* Password field container */
        .password-container {
            position: relative;
            width: 90%;
        }

        /* Show/Hide password icon */
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: white;
            font-size: 18px;
        }

        /* Success messages */
        .success-message {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
            background: rgba(0, 255, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid green;
            width: 90%;
        }

        /* Button */
        .reset-btn {
            width: 90%;
            padding: 12px;
            background: linear-gradient(90deg, #3498db, #1e87f0);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
            box-shadow: 0px 4px 10px rgba(52, 152, 219, 0.4);
        }

        .reset-btn:hover {
            background: linear-gradient(90deg, #1e87f0, #007aff);
            box-shadow: 0px 6px 15px rgba(52, 152, 219, 0.5);
        }

        /* Links */
        .links {
            margin-top: 15px;
            font-size: 14px;
        }

        .links a {
            color: #f1f1f1;
            text-decoration: none;
            font-weight: bold;
        }

        .links a:hover {
            text-decoration: underline;
            color: #d9e2ec;
        }
    </style>
</head>
<body>

<div class="reset-container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    <h2 style="color: white;">RESET PASSWORD</h2>

    <!-- Success Message -->
    @if (session('status'))
        <p class="success-message">
            {{ session('status') }}
        </p>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <input type="email" id="email" name="email" class="input-field" placeholder="Email" 
               value="{{ old('email', $request->email) }}" required autofocus>

        <div class="password-container">
            <input type="password" id="password" name="password" class="input-field" placeholder="New Password" required>
            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
        </div>

        <div class="password-container">
            <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" 
                   placeholder="Confirm Password" required>
            <span class="toggle-password" onclick="togglePassword('password_confirmation')">👁️</span>
        </div>

        <button type="submit" class="reset-btn">Reset Password</button>
    </form>

    <div class="links">
        <a href="{{ route('login') }}">Back to Login</a>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        let field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }
</script>

<!-- Show success message before redirecting to login -->
@if (session('status'))
    <script>
        window.onload = function() {
            setTimeout(function() {
                alert("{{ session('status') }}"); // Show success popup
                window.location.href = "{{ route('login') }}"; // Redirect to login
            }, 1000); // 1-second delay before redirection
        };
    </script>
@endif

</body>
</html>
