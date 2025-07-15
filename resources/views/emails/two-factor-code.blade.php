<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #2d89ef;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello,</h2>
        <p>Your Two-Factor Authentication (2FA) code is:</p>
        <p class="code">{{ $code }}</p>
        <p>Please enter this code within the next 5 minutes to complete your login.</p>
        <p>If you did not request this code, please ignore this email.</p>
        <p>Thank you!</p>
        <div class="footer">
            &copy; {{ date('Y') }} Your Application Name. All rights reserved.
        </div>
    </div>
</body>
</html>
