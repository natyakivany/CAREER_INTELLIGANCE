<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #004AAD; }
        .brand { font-size: 20px; font-weight: 900; color: #004AAD; letter-spacing: 0.5px; margin: 0; }
        .brand span { color: #004AAD; }
        .tagline { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px; }
        h1 { color: #0b233a; font-size: 20px; margin-top: 0; margin-bottom: 14px; font-weight: 800; }
        .message-box { background-color: #f8fafc; padding: 20px 24px; border-radius: 12px; margin: 20px 0; font-size: 14px; line-height: 1.7; border: 1px solid #e2e8f0; color: #1e293b; white-space: pre-wrap; }
        .btn-wrapper { text-align: center; margin: 28px 0 16px; }
        .btn { display: inline-block; padding: 12px 28px; background-color: #004AAD; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 13px; box-shadow: 0 4px 10px rgba(0, 74, 173, 0.25); }
        .footer { margin-top: 32px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="brand">CAREER <span>INTELLIGENCE</span></h2>
            <div class="tagline">Pusat Pengembangan Karier & Asesmen Kompetensi Mahasiswa</div>
        </div>

        @if(!empty($studentName))
            <h1>Halo, {{ $studentName }}!</h1>
        @endif

        <div class="message-box">{!! nl2br(e($messageBody)) !!}</div>

        <div class="btn-wrapper">
            <a href="{{ url('/dashboard') }}" class="btn">Buka Portal Career Intelligence &rarr;</a>
        </div>

        <div class="footer">
            <p style="margin: 4px 0;">Pesan ini dikirimkan resmi oleh <strong>Administrator Pusat Karier Kampus</strong>.</p>
            <p style="margin: 4px 0;">&copy; {{ date('Y') }} Career Intelligence. Hak cipta dilindungi undang-undang.</p>
        </div>
    </div>
</body>
</html>
