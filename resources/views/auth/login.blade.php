<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      background-color: #47663B;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .login-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
      text-align: center;
      width: 350px;
      position: relative;
    }
    .logo {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }
    .input-field {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }
    .login-btn {
      width: 100%;
      padding: 10px;
      background-color: #3498db;
      border: none;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }
    .login-btn:hover {
      background-color: #2980b9;
    }
    .error-message {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
    .success-message {
      color: green;
      font-size: 14px;
      margin-bottom: 10px;
    }
    .links {
      margin-top: 15px;
      font-size: 14px;
    }
    .links a {
      color: #3498db;
      text-decoration: none;
      font-weight: bold;
    }
    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="login-container">
  <!-- Circular Logo -->
  <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

  <h2>LOGIN</h2>

  @if (session('message'))
    <p class="success-message">{{ session('message') }}</p>
  @endif

  @if ($errors->any())
    <p class="error-message">{{ $errors->first() }}</p>
  @endif

  <form id="login-form" method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" id="email" name="email" class="input-field" placeholder="Email" required autofocus>
    <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
    <button type="submit" class="login-btn">LOGIN</button>
  </form>

  <!-- Additional Links -->
  <div class="links">
    <a href="{{ route('password.request') }}">Forgot Password?</a><br>
    <span>Don't have an account? <a href="{{ route('register') }}">Register here!</a></span>
  </div>
</div>

</body>
</html>
