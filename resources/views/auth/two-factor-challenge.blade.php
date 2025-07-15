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
            background: rgba(32, 32, 32, 0.5);
            z-index: -1;
        }

        /* Two-Factor Authentication container */
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
        .auth-btn {
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

        .auth-btn:hover {
            background: linear-gradient(90deg,rgb(78, 104, 79),rgb(9, 114, 0));
            box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);
        }

        /* Disabled button (gray) */
        .disabled {
            background-color: #aaa;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Error messages */
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

        /* Success messages */
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

        /* Timer */
        .timer {
            font-size: 14px;
            color: white;
        }

        /* Pop-up message */
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
    <!-- Circular Logo -->
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

    <h2 style="color: white;">One-Time Password(OTP)</h2>
    <p style="color: white;">Enter the OTP code sent to your email:</p>

    @if ($errors->any())
        <p class="error-message">{{ $errors->first() }}</p>
    @endif

    <form action="{{ route('2fa.verify') }}" method="POST">
        @csrf
        <input type="text" name="code" class="input-field" required placeholder="Enter 6-digit code">
        <button type="submit" class="auth-btn">Verify</button>
    </form>

    <!-- Send Button to send code to Mailtrap -->
    <button id="sendButton" class="auth-btn" onclick="handleSend()">Send Code</button>

    <!-- Resend 2FA Code -->
    <p style="color: white;">Didn't receive the code?</p>
    <button id="resendButton" class="auth-btn disabled" onclick="resendCode()" disabled>Resend Code</button>
    <p id="timer" class="timer"></p>

    <!-- Pop-up message -->
    <div id="popupMessage" class="popup-message">Look for Mailtrap, your code is sent already!</div>
</div>

<script>
    let countdown = 30; // Set cooldown timer (in seconds)
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
            resendButton.classList.remove('disabled');  // Remove 'disabled' class
            resendButton.classList.add('auth-btn');  // Apply the 'auth-btn' class (blue color)
            timerDisplay.innerText = "";
        }
    }

    function handleSend() {
        // Disable Send button and hide it after clicking
        sendButton.disabled = true;
        sendButton.style.display = "none";  // Hide the button
        updateTimer();

        // Send request to Laravel to send the 2FA code via Mailtrap
        fetch("{{ route('2fa.send') }}", {
            method: "GET",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        }).then(response => {
            return response.json();
        }).then(data => {
            // Display success message and show it
            popupMessage.style.display = "block";
            setTimeout(() => {
                popupMessage.style.display = "none"; // Hide the message after 5 seconds
            }, 5000);
        }).catch(error => {
            alert("Look for Mailtrap, your code is sent already!");
        });
    }

    function resendCode() {
        resendButton.disabled = true;
        resendButton.classList.add('disabled'); // Apply gray color when disabled
        countdown = 30; // Reset timer
        updateTimer();

        // Send request to Laravel to resend the 2FA code
        fetch("{{ route('2fa.resend') }}", {
            method: "GET",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        }).then(response => {
            return response.json();
        }).then(data => {
            alert(data.message);
        }).catch(error => {
            alert("Failed to resend code. Please try again.");
        });
    }

    // Start initial cooldown
    updateTimer();
</script>

</body>
</html>
