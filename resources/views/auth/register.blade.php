<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background-color: #4d6b3d; /* Matches the green background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .logo {
            width: 80px; /* Adjust logo size */
            height: auto;
            margin-bottom: 10px;
        }
        .form-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-container input {
            width: calc(100% - 20px); /* Adjusted width to fit */
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .form-container button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .form-container a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <h2>Register</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required autofocus>
            @error('name') <p class="error-message">{{ $message }}</p> @enderror

            <!-- Email -->
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            @error('email') <p class="error-message">{{ $message }}</p> @enderror

            <!-- Password -->
            <input type="password" name="password" placeholder="Password" required>
            @error('password') <p class="error-message">{{ $message }}</p> @enderror

            <!-- Confirm Password -->
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            @error('password_confirmation') <p class="error-message">{{ $message }}</p> @enderror

            <!-- Register Button -->
            <button type="submit">Register</button>

            <!-- Login Link -->
            <a href="{{ route('login') }}">Already registered? Log in here</a>
        </form>
    </div>

</body>
</html>
