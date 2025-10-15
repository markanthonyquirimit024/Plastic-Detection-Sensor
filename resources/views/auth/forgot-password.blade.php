<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <style>
    body {
      background: linear-gradient(135deg, #ffffff, #4caf50);
  background-position: center;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
  font-family: 'Poppins', sans-serif;
  position: relative;
}


    .forgot-container {
      background: rgba(255, 255, 255, 0.15);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 400px;
      backdrop-filter: blur(10px);
      position: relative;
    }

    .logo {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
      box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.5);
    }

    .input-field {
        width: 90%;
        padding: 12px;
        margin: 8px 0;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.3);
        color: black;
        outline: none;
        transition: 0.3s;
        box-shadow: inset 0px 0px 5px rgba(255, 255, 255, 0.3);
        text-align: center;
      }

      .input-field::placeholder {
        color: black;
      }

      .input-field:focus {
        background: rgba(255, 255, 255, 0.5);
        box-shadow: inset 0px 0px 10px rgba(255, 255, 255, 0.5);
      }

    .reset-btn {
      width: 90%;
        padding: 12px;
        background: linear-gradient(90deg,rgb(71, 71, 71),rgb(74, 143, 68));
        border: none;
        color: white;
        font-size: 16px;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
        box-shadow: 0px 4px 10px rgba(23, 148, 33, 0.4);
    }

    .reset-btn:hover {
      background: linear-gradient(90deg,rgb(78, 104, 79),rgb(9, 114, 0));
        box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);
    }

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
  <div class="forgot-container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    <h2 style="color: black;">Forgot Password</h2>
    <p style="color: black; font-size: 14px;">Enter your email to receive a password reset link.</p>
    
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <input type="email" name="email" class="input-field" placeholder="Email" required autofocus>
      <button type="submit" class="reset-btn">Send Reset Link</button>
    </form>
    
    <div class="links">
      <a href="{{ route('login') }}">Back to Login</a>
    </div>
  </div>
</body>
</html>
