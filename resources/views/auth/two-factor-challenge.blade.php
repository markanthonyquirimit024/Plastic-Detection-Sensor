<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <title>Two-Factor Authentication</title>
    <style>
        /* General styling */
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

        /* Container */
        .auth-container {
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

        /* Input fields */
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

        .input-field:focus {
            background: rgba(255, 255, 255, 0.5);
            box-shadow: inset 0px 0px 10px rgba(255, 255, 255, 0.5);
        }

        /* Buttons */
        .auth-btn {
            width: 90%;
            padding: 12px;
            background: linear-gradient(90deg, rgb(71, 71, 71), rgb(74, 143, 68));
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

        .auth-btn:hover {
            background: linear-gradient(90deg, rgb(78, 104, 79), rgb(9, 114, 0));
            box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);
        }

        .disabled {
            background-color: #aaa;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Back to Login link */
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: black;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #0f8a12;
            text-decoration: underline;
        }

        /* Messages */
        .error-message, .success-message {
            width: 90%;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .error-message {
            color: red;
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid red;
        }

        .success-message {
            color: green;
            background: rgba(0, 255, 0, 0.2);
            border: 1px solid green;
        }

        .timer {
            font-size: 14px;
            color: white;
        }

        .popup-message {
            display: none;
            background-color: rgba(0, 255, 0, 0.2);
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            color: green;
            border: 1px solid green;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <!-- Logo -->
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

    <h2 style="color: black;">One-Time Password (OTP)</h2>
    <p style="color: black;">Enter the OTP code sent to your email:</p>

    @if ($errors->any())
        <p class="error-message">{{ $errors->first() }}</p>
    @endif

    <form action="{{ route('2fa.verify') }}" method="POST">
        @csrf
        <input type="text" name="code" class="input-field" required placeholder="Enter 6-digit code">
        <button type="submit" class="auth-btn">Verify</button>
    </form>

    <button id="sendButton" class="auth-btn" onclick="handleSend()">Send Code</button>

    <p style="color: black;">Didn't receive the code?</p>
    <button id="resendButton" class="auth-btn disabled" onclick="resendCode()" disabled>Resend Code</button>
    <p id="timer" class="timer"></p>

    <div id="popupMessage" class="popup-message">Look for Mailtrap, your code is sent already!</div>

    <!-- ✅ Back to Login word link -->
    <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
</div>

<script>
    let countdown = 30;
    const resendButton = document.getElementById('resendButton');
    const sendButton = document.getElementById('sendButton');
    const timerDisplay = document.getElementById('timer');
    const popupMessage = document.getElementById('popupMessage');

    function updateTimer() {
        if (countdown > 0) {
            timerDisplay.innerText = `You can resend the code in ${countdown} seconds`;
            countdown--;
            setTimeout(updateTimer, 1000);
        } else {
            resendButton.disabled = false;
            resendButton.classList.remove('disabled');
            resendButton.classList.add('auth-btn');
            timerDisplay.innerText = "";
        }
    }

    function handleSend() {
        sendButton.disabled = true;
        sendButton.style.display = "none";
        updateTimer();

        fetch("{{ route('2fa.send') }}", {
            method: "GET",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        }).then(response => response.json())
        .then(() => {
            popupMessage.style.display = "block";
            setTimeout(() => { popupMessage.style.display = "none"; }, 5000);
        }).catch(() => {
            alert("Look for Mailtrap, your code is sent already!");
        });
    }

    function resendCode() {
        resendButton.disabled = true;
        resendButton.classList.add('disabled');
        countdown = 30;
        updateTimer();

        fetch("{{ route('2fa.resend') }}", {
            method: "GET",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        }).then(response => response.json())
        .then(data => alert(data.message))
        .catch(() => alert("Failed to resend code. Please try again."));
    }

    updateTimer();
</script>

</body>
</html>
