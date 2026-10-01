<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code - Holy Cross Parish</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F4F1EA;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4A3728;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #FFFFFF;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            color: #1A1A1A;
            margin-bottom: 16px;
        }
        .message {
            font-size: 14px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .otp-box {
            background-color: #F8F9FA;
            border: 2px dashed #4A3728;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #4A3728;
            letter-spacing: 8px;
            font-family: monospace;
        }
        .expiry {
            font-size: 12px;
            color: #999999;
            text-align: center;
            margin-top: 16px;
        }
        .footer {
            background-color: #F8F9FA;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Holy Cross Parish</h1>
        </div>

        <div class="content">
            <p class="greeting">Hello, {{ $userName }}!</p>

            <p class="message">
                @if($purpose === 'login')
                    Narito ang iyong One-Time Password (OTP) para sa pag-login sa Holy Cross Parish app.
                @elseif($purpose === 'register')
                    Salamat sa pag-register! Narito ang iyong OTP para i-verify ang iyong email.
                @elseif($purpose === 'password_reset')
                    Narito ang iyong OTP para i-reset ang iyong password.
                @elseif($purpose === 'admin_login')
                    Narito ang iyong Admin OTP para sa pag-login sa Holy Cross Parish admin panel.
                @endif
            </p>

            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <p class="expiry">
                ⏰ Valid for 10 minutes. Huwag ibahagi sa iba.
            </p>

            <p class="message">
                Kung hindi ka nag-request ng OTP, i-ignore na lang ang email na ito.
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Holy Cross Parish. All rights reserved.</p>
            <p>J. Ramos Street 267, Poblacion West, Asingan, Pangasinan</p>
        </div>
    </div>
</body>
</html>