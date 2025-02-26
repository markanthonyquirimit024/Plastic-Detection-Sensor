<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captcha Verification</title>
    <style>
        body {
            background-color: #47663B;

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }
        .logo {
            width: 80px; /* Small logo */
            height: auto;
            margin-bottom: 10px;
        }
        h2 {
            margin-bottom: 15px;
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
        .captcha-container {
            margin-top: 10px;
            padding: 10px;
            background: #ddd;
            font-size: 20px;
            font-weight: bold;
            display: inline-block;
            border-radius: 5px;
        }
        .btn {
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
        .btn:hover {
            background-color: #2980b9;
        }
        .refresh-btn {
            background-color: #2ecc71;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .refresh-btn:hover {
            background-color: #27ae60;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    <h2>Captcha Verification</h2>
    
    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <p id="captcha-text" class="captcha-container">{{ session('captcha_text', 'ABC123') }}</p>
    
    <button class="refresh-btn" onclick="reloadCaptcha()">Refresh Captcha</button>

    <form method="POST" action="{{ route('captcha.verify') }}">
        @csrf
        <input type="text" name="captcha" class="input-field" placeholder="Enter captcha" required>
        <button type="submit" class="btn">Verify</button>
    </form>
</div>

<script>
    function reloadCaptcha() {
    fetch("{{ route('captcha.show') }}")
        .then(response => response.json())  // Expect JSON response
        .then(data => {
            document.getElementById('captcha-text').innerText = data.captcha; // Update Captcha
        })
        .catch(error => console.error('Error loading captcha:', error));
}

</script>

</body>
</html>
