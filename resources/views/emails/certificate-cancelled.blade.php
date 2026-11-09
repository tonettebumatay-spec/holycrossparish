<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate Cancelled</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4F1EA; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;">
        <div style="background: #C0392B; padding: 24px; text-align: center;">
            <h1 style="color: white; margin: 0;">Holy Cross Parish</h1>
        </div>

        <div style="padding: 32px;">
            <h2 style="color: #1A1A1A;">Certificate Request Cancelled</h2>

            <p style="color: #666; line-height: 1.6;">
                Hello <strong>{{ $certificate->full_name }}</strong>,
            </p>

            <p style="color: #666; line-height: 1.6;">
                We regret to inform you that your request for a <strong>{{ $certificate->certificate_type }} Certificate</strong> has been cancelled.
            </p>

            <div style="background: #FFF3E0; border-left: 4px solid #C0392B; padding: 16px; margin: 20px 0;">
                <p style="margin: 0; color: #1A1A1A; font-size: 14px;">
                    <strong>Reason for Cancellation:</strong><br>
                    <span style="font-style: italic;">"{{ $reason }}"</span>
                </p>
            </div>

            <p style="color: #666; line-height: 1.6;">
                If you have any questions or wish to appeal this decision, please contact the Parish Office.
            </p>
        </div>

        <div style="background: #F8F9FA; padding: 16px; text-align: center; font-size: 12px; color: #999;">
            © {{ date('Y') }} Holy Cross Parish
        </div>
    </div>
</body>
</html>