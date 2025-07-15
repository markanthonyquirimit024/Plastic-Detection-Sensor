<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Plastic Detection System</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      color: white;
      background: url("{{ asset('images/coverwel2.png') }}") no-repeat center center/cover;
      height: 100vh;
      overflow: hidden; /* Prevent scrolling */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-end;
      padding: 50px;
      text-align: right;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(32, 32, 32, 0.5);
      z-index: 0; /* Ensures it covers the background */
    }

    /* Navbar Styles */
    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background: rgba(179, 179, 179, 0.6); /* Darker for better visibility */
      padding: 15px 20px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      z-index: 1000; /* Keeps navbar above overlay */
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
      color: white;
      margin-right: auto; /* Push logo to the left */
      z-index: 1;
    }

    .navbar ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      gap: 15px;
      margin-right: 30px;
    }

    .navbar ul li {
      display: inline;
    }

    .navbar ul li a {
      text-decoration: none;
      color: white;
      font-size: 16px;
      padding: 10px 15px;
      border-radius: 8px;
      transition: 0.3s ease-in-out;
      font-weight: 500;
      background: rgba(255, 255, 255, 0.2);
    }

    .navbar ul li a:hover {
      background: rgba(255, 255, 255, 0.5);
    }

    /* Welcome Text */
    .welcome-text {
      max-width: 600px;
      font-size: 2.8rem;
      font-weight: bold;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
      margin-top: 100px;
      position: relative;
      z-index: 1;
    }

    .subtext {
      font-size: 1.5rem;
      margin-top: 10px;
      max-width: 550px;
      text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.6);
      font-weight: 500;
      position: relative;
      z-index: 1;
    }

    .highlight {
      color: #f1c40f;
      font-weight: bold;
    }
    .footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: rgba(179, 179, 179, 0.6);
    color: white;
    text-align: center;
    padding: 10px 0;
    font-size: 14px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
  }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="logo">Plastic Detection</div>
    <ul>
      <li><a href="#">Home</a></li>
      <li><a href="{{ route('login') }}">Login</a></li>
      <li><a href="{{ route('register') }}">Register</a></li>
    </ul>
  </nav>

  <!-- Welcome Section -->
  <div class="welcome-text">
    <span class="highlight">"One Bottle at a Time, One Future Together!"</span> 🌍
  </div>
  <p class="subtext">
    <span id="greeting"></span>, <strong>
    @auth
      {{ Auth::user()->name }}
    @else
      Eco-Warrior
    @endauth
    </strong>! Welcome to a smarter way of fighting plastic pollution.
  </p>

  <p class="subtext">
    Join our mission to keep PHINMA - University of Pangasinan <span class="highlight">clean and sustainable.</span>  
    Every piece of plastic detected brings us closer to a greener tomorrow. 🌱💚
  </p>

  <footer class="footer">
  <p>&copy; 2025 Plastic Detection System. All Rights Reserved.</p>
</footer>

  <script>
    function setGreeting() {
      const hours = new Date().getHours();
      let greeting = "Hello";
      if (hours < 12) {
        greeting = "Good Morning";
      } else if (hours < 18) {
        greeting = "Good Afternoon";
      } else {
        greeting = "Good Evening";
      }
      document.getElementById("greeting").innerText = greeting;
    }

    document.addEventListener("DOMContentLoaded", () => {
      setGreeting();
    });
  </script>

</body>
</html>
