<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <title>Register</title>
    <style>
        /* General body styling */
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
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Form container */
        .form-container {
            background: rgba(255, 255, 255, 0.2);
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
        .register-btn {
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

        .register-btn:hover {
            background: linear-gradient(90deg,rgb(78, 104, 79),rgb(9, 114, 0));
          box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);
        }

        /* Success & Error messages */
        .success-message {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
            background: rgba(0, 255, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid green;
            text-align: center;
            width: 90%;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            background: rgba(255, 0, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid red;
            text-align: center;
            width: 90%;
        }

        /* Password instruction */
        .password-instruction {
            font-size: 12px;
            color: white;
            text-align: center;
            margin-bottom: 10px;
            width: 90%;
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

    <div class="form-container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <h2 style="color: white;">Register</h2>

        <!-- ✅ Display success message if registration is successful -->
        @if (session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <p class="error-message">{{ $errors->first() }}</p>
        @endif

        <form id="register-form" method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <input type="text" name="name" class="input-field" placeholder="Name" value="{{ old('name') }}" required autofocus>
            @error('name') <p class="error-message">{{ $message }}</p> @enderror

            <!-- Email -->
            <input type="email" name="email" class="input-field" placeholder="Email" value="{{ old('email') }}" required>
            @error('email') <p class="error-message">{{ $message }}</p> @enderror


            <!-- Password Field with Toggle -->
            <div style="position: relative; width: 100%;">
            <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
            <button type="button" class="toggle-password" data-target="password" style="
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

            <!-- Password Error Message (Laravel + JavaScript) -->
            @error('password')
            <p class="error-message">{{ $message }}</p>
            @enderror
            <p id="password-error" class="error-message" style="display: none;">
            - Uppercase & lowercase letters<br>
            - At least one number<br>
            - At least one special symbol (@$!%*?&) 
            </p>


            <!-- Confirm Password -->
            <!-- Confirm Password Field with Toggle -->
            <div style="position: relative; width: 100%;">
            <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" placeholder="Confirm Password" required>
            <button type="button" class="toggle-password" data-target="password_confirmation" style="
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

            <!-- Confirm Password Error Message -->
            @error('password_confirmation')
            <p class="error-message">{{ $message }}</p>
            @enderror


            <button type="submit" class="register-btn">Register</button>

            <!-- Login Link -->
            <div class="links">
                <a href="{{ route('login') }}">Already registered? Log in here</a>
            </div>
        </form>
    </div>

<script>
  document.getElementById("register-form").addEventListener("submit", function(event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("password_confirmation").value;
    const passwordError = document.getElementById("password-error");

    // Password validation regex
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!passwordPattern.test(password)) {
      passwordError.style.display = "block";
      event.preventDefault(); // Stop form submission
    } else if (password !== confirmPassword) {
      passwordError.innerHTML = "Passwords do not match!";
      passwordError.style.display = "block";
      event.preventDefault();
    } else {
      passwordError.style.display = "none";
    }
  });
</script>
<script>
  document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", function() {
      let targetId = this.getAttribute("data-target");
      let passwordField = document.getElementById(targetId);

      if (passwordField.type === "password") {
        passwordField.type = "text";
        this.textContent = "🙈"; // Change to hide icon
      } else {
        passwordField.type = "password";
        this.textContent = "👁️"; // Change back to show icon
      }
    });
  });

  document.getElementById("register-form").addEventListener("submit", function(event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("password_confirmation").value;
    const passwordError = document.getElementById("password-error");

    // Password validation regex
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!passwordPattern.test(password)) {
      passwordError.style.display = "block";
      event.preventDefault(); // Stop form submission
    } else if (password !== confirmPassword) {
      passwordError.innerHTML = "Passwords do not match!";
      passwordError.style.display = "block";
      event.preventDefault();
    } else {
      passwordError.style.display = "none";
    }
  });
</script>

</body>
</html>
