<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate Ready</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4F1EA; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: #4A3728; padding: 24px; text-align: center;">
            <h1 style="color: #D4AF37; margin: 0;">Holy Cross Parish</h1>
            <p style="color: #FFFFFF; margin: 8px 0 0 0; font-size: 14px;">Certificate Request Update</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px;">
            <h2 style="color: #1A1A1A;">Hello, {{ $certificate->full_name }}!</h2>

            <p style="color: #666; line-height: 1.6;">
                Your request for <strong>{{ $certificate->certificate_type }} Certificate</strong> has been successfully processed and is now <strong>COMPLETED</strong>.
            </p>

            <div style="background: #F8F9FA; border-left: 4px solid #4A3728; padding: 16px; margin: 20px 0;">
                <p style="margin: 0; color: #1A1A1A; line-height: 1.8;">
                    <strong>Reference ID:</strong> HCP-{{ str_pad($certificate->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <strong>Request Date:</strong> {{ \Carbon\Carbon::parse($certificate->request_date)->format('F d, Y') }}<br>
                    <strong>Status:</strong> Completed
                </p>
            </div>

            <h3 style="color: #4A3728; margin-top: 30px;">📋 What to Bring</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 12px;">
                Please visit the parish office and bring the following documents:
            </p>

            <!-- Requirements List -->
            <ul style="color: #1A1A1A; line-height: 1.8; padding-left: 20px; background: #FFF8E1; padding: 16px 16px 16px 40px; border-radius: 8px;">
                @forelse($requirements as $req)
                    <li>
                        <strong>{{ $req->requirement_name }}</strong> 
                        @if($req->is_required) <span style="color: #C0392B; font-size: 12px;">(Required)</span> @endif
                        <br>
                        <span style="font-size: 12px; color: #666;">{{ $req->description }}</span>
                    </li>
                @empty
                    <li>Please bring a valid ID for verification.</li>
                @endforelse
            </ul>

            <p style="color: #666; font-size: 13px; margin-top: 30px;">
                <strong>Office Hours:</strong> Monday to Saturday, 8:00 AM - 5:00 PM<br>
                <strong>Location:</strong> Holy Cross Parish Office
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #F8F9FA; padding: 16px; text-align: center; font-size: 12px; color: #999;">
            © {{ date('Y') }} Holy Cross Parish. All rights reserved.
        </div>
    </div>
</body>
</html>