<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
  <title>Register</title>
  <style>
    * {
      box-sizing: border-box;
    }

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

    .form-container {
      background: rgba(255, 255, 255, 0.2);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 90%;
      max-width: 400px;
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

    form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .input-field {
      width: 100%;
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

    .register-btn {
      width: 100%;
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

    #password-checklist {
      color: white;
      text-align: left;
      font-size: 12px;
      list-style: none;
      padding: 0;
      margin: 10px 0 0 0;
      width: 100%;
      opacity: 1;
      transition: opacity 0.5s ease;
    }

    #password-checklist.hidden {
      opacity: 0;
      pointer-events: none;
      height: 0;
      overflow: hidden;
    }

    #password-checklist li {
      margin-bottom: 5px;
    }

    @media screen and (max-width: 480px) {
      .form-container {
        padding: 20px;
        border-radius: 10px;
      }

      .input-field, .register-btn {
        font-size: 14px;
      }

      .logo {
        width: 80px;
        height: 80px;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo" />
    <h2 style="color: white;">Register</h2>

    <!-- Success Message -->
    <x-success-message />
    
    <!-- Error Validation -->
    <x-validation-errors />

    <form id="register-form" method="POST" action="{{ route('register') }}">
      @csrf
      <input type="text" name="name" class="input-field" placeholder="Name" value="{{ old('name') }}" required autofocus>
      <input type="email" name="email" class="input-field" placeholder="Email" value="{{ old('email') }}" required>

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
          font-size: 18px;">👁️</button>
      </div>

      <ul id="password-checklist">
        <li id="length">✖ At least 8 characters</li>
        <li id="lowercase">✖ At least one lowercase letter</li>
        <li id="uppercase">✖ At least one uppercase letter</li>
        <li id="number">✖ At least one number</li>
        <li id="special">✖ At least one special character (@$!%*?&)</li>
      </ul>

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
          font-size: 18px;">👁️</button>
      </div>

      <button type="submit" class="register-btn">Register</button>

      <div class="links">
        <a href="{{ route('login') }}">Already registered? Log in here</a>
      </div>
    </form>
  </div>

  <script>
    document.querySelectorAll(".toggle-password").forEach(button => {
      button.addEventListener("click", function () {
        const targetId = this.getAttribute("data-target");
        const field = document.getElementById(targetId);
        field.type = field.type === "password" ? "text" : "password";
        this.textContent = field.type === "password" ? "👁️" : "🙈";
      });
    });

    const checklist = {
      length: false,
      lowercase: false,
      uppercase: false,
      number: false,
      special: false
    };

    document.getElementById("password").addEventListener("input", function () {
      const value = this.value;
      checklist.length = value.length >= 8;
      checklist.lowercase = /[a-z]/.test(value);
      checklist.uppercase = /[A-Z]/.test(value);
      checklist.number = /\d/.test(value);
      checklist.special = /[@$!%*?&]/.test(value);

      for (let key in checklist) {
        updateChecklist(key, checklist[key]);
      }

      const allPassed = Object.values(checklist).every(v => v);
      document.getElementById("password-checklist").classList.toggle("hidden", allPassed);
    });

    function updateChecklist(id, isValid) {
      const item = document.getElementById(id);
      item.style.color = isValid ? "lime" : "red";
      item.textContent = `${isValid ? '✔' : '✖'} ${item.textContent.slice(2)}`;
    }

    document.getElementById("register-form").addEventListener("submit", function (event) {
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("password_confirmation").value;
      const allPassed = Object.values(checklist).every(v => v);

      if (!allPassed) {
        alert("Password does not meet all the requirements.");
        event.preventDefault();
      } else if (password !== confirmPassword) {
        alert("Passwords do not match!");
        event.preventDefault();
      }
    });
  </script>
</body>
</html>
