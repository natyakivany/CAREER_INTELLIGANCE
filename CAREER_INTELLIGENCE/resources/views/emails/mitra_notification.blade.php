<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectLine }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #0f172a; border-bottom: 2px solid #004AAD; display: inline-block; padding-bottom: 5px;">CAREER INTEL</h2>
    </div>
    
    <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0;">
        {!! nl2br(e($messageBody)) !!}
    </div>

    <div style="margin-top: 30px; font-size: 12px; color: #64748b; text-align: center;">
        <p>Email ini dikirim secara otomatis melalui sistem Pusat Karier Kampus - Career Intel.</p>
        <p>&copy; {{ date('Y') }} Career Intel. All rights reserved.</p>
    </div>
</body>
</html>





