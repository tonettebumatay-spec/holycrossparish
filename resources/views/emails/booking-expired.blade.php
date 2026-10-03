<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Expired</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4F1EA; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;">
        <div style="background: #C0392B; padding: 24px; text-align: center;">
            <h1 style="color: white; margin: 0;">Holy Cross Parish</h1>
        </div>

        <div style="padding: 32px;">
            <h2 style="color: #1A1A1A;">❌ Booking Expired</h2>

            <p style="color: #666;">Hello <strong>{{ $booking->user_name }}</strong>,</p>

            <p style="color: #666;">
                Ang iyong booking para sa <strong>{{ ucfirst($booking->service_type) }}</strong>
                ay nag-expire na dahil hindi ito na-confirm sa loob ng 24 hours.
            </p>

            <div style="background: #FEE2E2; border-left: 4px solid #DC2626; padding: 16px; margin: 20px 0;">
                <p style="margin: 0; color: #991B1B;">
                    <strong>Booking ID:</strong> #{{ $booking->id }}<br>
                    <strong>Service:</strong> {{ ucfirst($booking->service_type) }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->appointment_date)->format('F d, Y') }}<br>
                    <strong>Time:</strong> {{ $booking->appointment_time }}
                </p>
            </div>

            <p style="color: #666;">
                Kung gusto mong mag-book ulit, pumunta lang sa app o website.
            </p>

            <a href="{{ url('/') }}" style="display: inline-block; background: #4A3728; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 16px;">
                Book Again
            </a>
        </div>

        <div style="background: #F8F9FA; padding: 16px; text-align: center; font-size: 12px; color: #999;">
            © {{ date('Y') }} Holy Cross Parish
        </div>
    </div>
</body>
</html>