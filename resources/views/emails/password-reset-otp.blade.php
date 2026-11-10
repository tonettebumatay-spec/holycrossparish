<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4F1EA; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;">
        <div style="background: #4A3728; padding: 24px; text-align: center;">
            <h1 style="color: white; margin: 0;">Holy Cross Parish</h1>
        </div>
        <div style="padding: 32px; text-align: center;">
            <h2 style="color: #1A1A1A;">Password Reset Request</h2>
            <p style="color: #666;">Use the OTP code below to reset your password. This code will expire in 15 minutes.</p>
            
            <div style="background: #F8F9FA; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px dashed #4A3728;">
                <!-- ✅ INALIS ANG letter-spacing PARA WALANG ESPASYO -->
                <h1 style="color: #4A3728; margin: 0; font-size: 36px;">{{ $otp }}</h1>
            </div>
            
            <p style="color: #999; font-size: 12px;">If you did not request a password reset, please ignore this email.</p>
        </div>
    </div>
</body>
</html>