<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Expiring Soon</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4F1EA; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;">
        <div style="background: #F39C12; padding: 24px; text-align: center;">
            <h1 style="color: white; margin: 0;">Holy Cross Parish</h1>
        </div>

        <div style="padding: 32px;">
            <h2 style="color: #1A1A1A;">⏰ Booking Expiring Soon</h2>

            <p style="color: #666;">Hello <strong>{{ $booking->user_name }}</strong>,</p>

            <p style="color: #666;">
                Ang iyong booking para sa <strong>{{ ucfirst($booking->service_type) }}</strong>
                ay mag-e-expire sa loob ng <strong>1 hour</strong>.
            </p>

            <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 16px; margin: 20px 0;">
                <p style="margin: 0; color: #92400E;">
                    <strong>Booking ID:</strong> #{{ $booking->id }}<br>
                    <strong>Service:</strong> {{ ucfirst($booking->service_type) }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->appointment_date)->format('F d, Y') }}<br>
                    <strong>Time:</strong> {{ $booking->appointment_time }}<br>
                    <strong>Expires:</strong> {{ $booking->expires_at->format('F d, Y g:i A') }}
                </p>
            </div>

            <p style="color: #666;">
                Mangyaring i-confirm ang iyong booking bago mag-expire. Kung hindi, awtomatikong makakansela ito.
            </p>

            <a href="{{ url('/') }}" style="display: inline-block; background: #F39C12; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 16px;">
                Confirm Booking
            </a>
        </div>

        <div style="background: #F8F9FA; padding: 16px; text-align: center; font-size: 12px; color: #999;">
            © {{ date('Y') }} Holy Cross Parish
        </div>
    </div>
</body>
</html>