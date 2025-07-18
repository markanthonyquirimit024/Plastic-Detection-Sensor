  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <title>Login</title>
    <style>
      /* General styling */
   body {
  background: url("images/logo.png") no-repeat center center;
  background-size: 1000px 700px; /* Adjust width and height of logo */
  background-repeat: no-repeat;
  background-position: center;
  
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

      /* Login container */
      .login-container {
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

      /* Button */
      .login-btn {
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

      .login-btn:hover {
        background: linear-gradient(90deg,rgb(78, 104, 79),rgb(9, 114, 0));
        box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);
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

      /* Password instruction */
      .password-instruction {
        font-size: 12px;
        color: white;
        text-align: center;
        margin-bottom: 10px;
        width: 90%;
      }
    </style>
  </head>
  <body>

  <div class="login-container">
    <!-- Circular Logo -->

    <h2 style="color: white;">LOGIN</h2>

    <!--Success Message-->
    <x-success-message/>

    <!-- Error Validation -->
    <x-validation-errors/>

    <p style="color: white;">Enter your credentials</p>
    <form id="login-form" method="POST" action="{{ route('login') }}">
      @csrf
      <input type="email" id="email" name="email" class="input-field" placeholder="Email" required autofocus>
      <div style="position: relative; width: 100%;">
    <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
    <button type="button" id="toggle-password" style="
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: white;
      font-size: 18px;
    ">👁️</button>
  </div>

      <button type="submit" class="login-btn">LOGIN</button>
    </form>

    <!-- Additional Links -->
    <div class="links">
      <a href="{{ route('password.request') }}">Forgot Password?</a><br>
      <span>Don't have an account? <a href="{{ route('register') }}">Register here!</a></span>
    </div>
  </div>

  <script>
    let failedAttempts = 0;

    document.getElementById("login-form").addEventListener("submit", function(event) {
      const password = document.getElementById("password").value;
      const passwordError = document.getElementById("password-error");

      // Password validation regex
      const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

      if (!passwordPattern.test(password)) {
        passwordError.style.display = "block";
        failedAttempts++;

        if (failedAttempts >= 3) {
          // Redirect to captcha page after 3 failed attempts
          alert("Too many failed attempts! Redirecting to captcha verification.");
          window.location.href = "{{ route('captcha.page') }}";
        }

        event.preventDefault(); // Stop form submission
      } else {
        passwordError.style.display = "none";
        failedAttempts = 0; // Reset counter on success
      }
    });
  </script>
<script>
  document.getElementById("toggle-password").addEventListener("click", function() {
    let passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
      passwordField.type = "text";
      this.textContent = "🙈"; // Change to hide icon
    } else {
      passwordField.type = "password";
      this.textContent = "👁️"; // Change back to show icon
    }
  });
</script>

  </body>
  </html>
