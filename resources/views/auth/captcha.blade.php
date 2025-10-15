<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Captcha Verification</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
background: linear-gradient(135deg, #ffffff, #4caf50);
  background-position: center;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
font-family: 'Montserrat', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  position: relative;
}


    .container {
      background: rgba(255, 255, 255, 0.15);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 400px;
      backdrop-filter: blur(10px);
    }

    .logo {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      margin-bottom: 15px;
      object-fit: cover;
      box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.5);
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: center;
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
      text-align: center;
      outline: none;
    }

    .captcha-container {
      position: relative;
      margin-top: 10px;
      background: white;
      padding: 15px;
      border-radius: 8px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: bold;
      font-size: 28px;
      letter-spacing: 5px;
      color: white;
      overflow: hidden;
      user-select: none;
    }

    canvas {
      position: absolute;
      top: 0;
      left: 0;
      border-radius: 8px;
      pointer-events: none;
      z-index: 1;
    }

    .captcha-letter {
      display: inline-block;
      transform: rotate(var(--angle)) scale(var(--scale));
      color: var(--color);
      margin: 0 3px;
      z-index: 2;
    }

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
        background: linear-gradient(90deg,rgb(71, 71, 71),rgb(74, 143, 68));
        color: white;
    }

    .btn:hover {
        background: linear-gradient(90deg,rgb(78, 104, 79),rgb(9, 114, 0));
        box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);
    }

    .refresh-btn {
        background: linear-gradient(90deg,rgb(71, 71, 71),rgb(74, 143, 68));
        color: white;
    }

    .refresh-btn:hover {
        background: linear-gradient(90deg,rgb(78, 104, 79),rgb(9, 114, 0));
        box-shadow: 0px 6px 15px rgba(27, 131, 23, 0.5);    }

    .message {
      margin-top: 12px;
      width: 90%;
      padding: 10px;
      border-radius: 6px;
      font-weight: 500;
      display: none;
    }

    .success {
      background: rgba(0, 255, 0, 0.2);
      color: #00ff00;
      border: 1px solid #00ff00;
    }

    .error {
      background: rgba(255, 0, 0, 0.2);
      color: #ff4444;
      border: 1px solid #ff4444;
    }
  </style>
</head>
<body>

<div class="container">
  <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

  <!-- CAPTCHA -->
  <div class="captcha-container" id="captcha">
    <canvas id="captcha-noise" width="340" height="80"></canvas>
  </div>

  <!-- Refresh -->
  <button type="button" id="refresh-captcha" class="refresh-btn">Refresh Captcha</button>

  <!-- Form -->
  <form id="captcha-form">
    <input type="text" id="captcha-input" class="input-field" placeholder="Enter captcha" required>
    <button type="submit" class="btn">Verify</button>
    <div id="message" class="message"></div>
  </form>
</div>

<script>
  let generatedCaptcha = "";

  function generateCaptchaText() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let captchaText = '';
    const len = Math.floor(Math.random() * 3) + 6; // 6–8 chars
    for (let i = 0; i < len; i++) captchaText += chars.charAt(Math.floor(Math.random() * chars.length));
    return captchaText;
  }

  function drawNoise() {
    const canvas = document.getElementById('captcha-noise');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // random lines
    for (let i = 0; i < 5; i++) {
      ctx.strokeStyle = `rgba(${Math.random()*255},${Math.random()*255},${Math.random()*255},0.6)`;
      ctx.beginPath();
      ctx.moveTo(Math.random()*canvas.width, Math.random()*canvas.height);
      ctx.lineTo(Math.random()*canvas.width, Math.random()*canvas.height);
      ctx.stroke();
    }

    // random dots
    for (let i = 0; i < 30; i++) {
      ctx.fillStyle = `rgba(${Math.random()*255},${Math.random()*255},${Math.random()*255},0.7)`;
      ctx.beginPath();
      ctx.arc(Math.random()*canvas.width, Math.random()*canvas.height, Math.random()*2, 0, 2*Math.PI);
      ctx.fill();
    }
  }

  function displayCaptcha() {
    const captchaDiv = document.getElementById('captcha');
    captchaDiv.querySelectorAll('.captcha-letter').forEach(e => e.remove());
    drawNoise();

    const text = generateCaptchaText();
    generatedCaptcha = text;

    for (let char of text) {
      const span = document.createElement('span');
      span.textContent = char;
      span.classList.add('captcha-letter');
      span.style.setProperty('--angle', `${Math.random() * 40 - 20}deg`);
      span.style.setProperty('--scale', `${1 + (Math.random() * 0.3 - 0.15)}`);
      span.style.setProperty('--color', `hsl(${Math.random() * 360}, 70%, 70%)`);
      captchaDiv.appendChild(span);
    }
  }

  function showMessage(text, type) {
    const msg = document.getElementById('message');
    msg.textContent = text;
    msg.className = `message ${type}`;
    msg.style.display = 'block';
  }

  document.addEventListener('DOMContentLoaded', displayCaptcha);
  document.getElementById('refresh-captcha').addEventListener('click', displayCaptcha);

  // ✅ form submission (no controller, redirects to login)
  document.getElementById('captcha-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('captcha-input').value.trim();

    if (input === "") {
      showMessage("Please enter the captcha.", "error");
      return;
    }

    if (input.toLowerCase() === generatedCaptcha.toLowerCase()) {
      showMessage("✅ Captcha verified successfully! Redirecting...", "success");
      setTimeout(() => {
        window.location.href = "/login"; // redirect to login
      }, 1500);
    } else {
      showMessage("❌ Incorrect captcha, try again.", "error");
      document.getElementById('captcha-input').value = "";
      displayCaptcha();
    }
  });
</script>

</body>
</html>
