<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome | Plastic Detection System</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  <style>
    .custom-body {
      background-color: #47663B; 
      color: rgb(233, 233, 233); 
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1.5rem;
      position: relative; 
    }
    .dark.custom-body {
      background-color: #1F2937; 
      color: #E5E7EB; 
    }


    header {
      position: absolute;
      top: 1rem;
      right: 1.5rem;
      width: auto;
    }


    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
      color:#000;
    }
    .visible {
      opacity: 1;
      transform: translateY(0);
    }


    .welcome-box {
      text-align: center;
      background-color: #ffffff; 
      box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); 
      border-radius: 0.5rem; 
      padding: 2rem; 
      max-width: 36rem; 
    }
    .dark .welcome-box {
      background-color: #1F2937; 
    }

    
    .custom-paragraph {
      font-size: 1.125rem; 
      color:rgb(0, 0, 0); 
    }
    .dark .custom-paragraph {
      color:rgb(0, 0, 0); 
    }
  </style>
</head>
<body class="custom-body">
  <header>
    @if (Route::has('login'))
      <nav class="flex gap-4">
        @auth
          <a href="{{ url('/dashboard') }}" class="px-5 py-1.5 text-sm border rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            Dashboard
          </a>
        @else
          <a href="{{ route('login') }}" class="px-5 py-1.5 text-sm border rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            Log in
          </a>
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="px-5 py-1.5 text-sm border rounded hover:bg-gray-200 dark:hover:bg-gray-700">
              Register
            </a>
          @endif
        @endauth
      </nav>
    @endif
  </header>

  <div id="welcomeBox" class="welcome-box fade-in">
    <h1 class="text-3xl">Welcome to Plastic Detection System</h1>
    <p class="custom-paragraph">
      <span id="greeting"></span>, <strong>
      @auth
        {{ Auth::user()->name }}
      @else
        Guest
      @endauth
      </strong>!
    </p>
    <p class="mt-2 text-sm">This system helps in detecting plastic waste at PHINMA - University of Pangasinan.</p>
  </div>

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

    function fadeInEffect() {
      const element = document.getElementById("welcomeBox");
      setTimeout(() => {
        element.classList.add("visible");
      }, 500);
    }


    document.addEventListener("DOMContentLoaded", () => {
      setGreeting();
      fadeInEffect();
    });
  </script>
</body>
</html>
