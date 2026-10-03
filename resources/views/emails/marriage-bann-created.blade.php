<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Marriage Banns</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4F1EA; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;">
        <div style="background: #4A3728; padding: 24px; text-align: center;">
            <h1 style="color: white; margin: 0;">Holy Cross Parish</h1>
        </div>

        <div style="padding: 32px;">
            <h2 style="color: #1A1A1A;">💍 New Marriage Banns Posted</h2>

            <p style="color: #666;">
                May bagong marriage banns na naka-post sa Holy Cross Parish.
            </p>

            <div style="background: #F8F9FA; border-left: 4px solid #4A3728; padding: 16px; margin: 20px 0;">
                <p style="margin: 0; color: #1A1A1A;">
                    <strong>Groom:</strong> {{ $bann->groom_name }}<br>
                    <strong>Bride:</strong> {{ $bann->bride_name }}<br>
                    <strong>Wedding Date:</strong> {{ $bann->wedding_date->format('F d, Y') }}<br>
                    <strong>Banns Date:</strong> {{ $bann->banns_date->format('F d, Y') }}<br>
                    <strong>Expires:</strong> {{ $bann->expires_at ? $bann->expires_at->format('F d, Y') : 'N/A' }}
                </p>
            </div>

            <p style="color: #666;">
                Ang banns na ito ay naka-post para sa 3 linggo.
            </p>

            <a href="{{ url('/banns') }}" style="display: inline-block; background: #4A3728; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin-top: 16px;">
                View Banns
            </a>
        </div>

        <div style="background: #F8F9FA; padding: 16px; text-align: center; font-size: 12px; color: #999;">
            © {{ date('Y') }} Holy Cross Parish
        </div>
    </div>
</body>
</html>