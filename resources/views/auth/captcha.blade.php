<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captcha Verification</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <style>
        /* General styling */
        body {
            background: url("{{ asset('images/coverwel2.png') }}") no-repeat center center/cover;
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

        /* Captcha container */
        .container {
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

        /* Captcha container */
        .captcha-container {
            margin-top: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.3);
            font-size: 20px;
            font-weight: bold;
            color: white;
            display: inline-block;
            border-radius: 5px;
            width: 90%;
            text-align: center;
        }

        /* Buttons */
        .btn, .refresh-btn {
            width: 90%;
            padding: 12px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn {
            background: linear-gradient(90deg, #3498db, #1e87f0);
            color: white;
            box-shadow: 0px 4px 10px rgba(52, 152, 219, 0.4);
        }

        .btn:hover {
            background: linear-gradient(90deg, #1e87f0, #007aff);
            box-shadow: 0px 6px 15px rgba(52, 152, 219, 0.5);
        }

        .refresh-btn {
            background: linear-gradient(90deg, #2ecc71, #27ae60);
            color: white;
            box-shadow: 0px 4px 10px rgba(46, 204, 113, 0.4);
        }

        .refresh-btn:hover {
            background: linear-gradient(90deg, #27ae60, #1e8449);
            box-shadow: 0px 6px 15px rgba(46, 204, 113, 0.5);
        }

        /* Error messages */
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            background: rgba(255, 0, 0, 0.2);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid red;
            text-align: center;
            width: 90%;
        }

    </style>
</head>
<body>

<div class="container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    <h2 style="color: white;">Captcha Verification</h2>
    
    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <!-- Display Captcha -->
    <p id="captcha-text" class="captcha-container">{{ session('captcha_text', 'ABC123') }}</p>
    
    <!-- Refresh Captcha Button -->
    <button id="refresh-captcha" class="refresh-btn">Refresh Captcha</button>

    <!-- Captcha Form -->
    <form method="POST" action="{{ route('captcha.verify') }}">
        @csrf
        <input type="text" name="captcha" class="input-field" placeholder="Enter captcha" required>
        <button type="submit" class="btn">Verify</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("refresh-captcha").addEventListener("click", function() {
            fetch("{{ route('captcha.show') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("captcha-text").textContent = data.captcha; // ✅ Update captcha
                })
                .catch(error => console.error("Error refreshing captcha:", error));
        });
    });
</script>



</body>
</html>
