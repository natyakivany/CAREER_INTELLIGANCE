<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Selamat Datang di Career Intelligence</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        h1 { color: #0b233a; font-size: 22px; margin-top: 0; margin-bottom: 16px; font-weight: 800; }
        .welcome-box { padding: 18px; border-radius: 12px; background: #eff6ff; color: #004AAD; border: 1px solid #bfdbfe; margin: 20px 0; text-align: center; }
        .credentials { background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 24px 0; font-size: 14px; border: 1px solid #e2e8f0; }
        .credentials p { margin: 8px 0; }
        .credentials .label { color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .credentials .value { color: #0b233a; font-weight: bold; font-size: 15px; background: #ffffff; padding: 8px 14px; border-radius: 8px; border: 1px solid #cbd5e1; display: inline-block; margin-top: 4px; font-family: 'Courier New', monospace; }
        .info-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .warning { background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin: 18px 0; }
        .btn { display: inline-block; padding: 14px 28px; background-color: #004AAD; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 14px; box-shadow: 0 4px 10px rgba(23, 95, 168, 0.25); }
        .footer { margin-top: 32px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Halo, {{ $user->name }}!</h1>

        <div class="welcome-box">
            <p style="margin: 0; font-size: 16px; font-weight: 800; color: #0b233a;">Akun Anda Telah Aktif di Career Intelligence</p>
            <p style="margin: 6px 0 0; font-size: 13px; color: #004AAD;">Pusat Pengembangan Karier, Asesmen Kompetensi & Rekomendasi Industri</p>
        </div>

        <p>Administrator telah mendaftarkan akun Anda pada platform <strong>Career Intelligence</strong>. Anda sekarang dapat mengakses asesmen kompetensi berbasis Google Gemini AI, membangun portofolio profesional, menyusun CV ATS, serta melamar lowongan kerja langsung ke Mitra Industri.</p>

        <div class="credentials">
            <p style="margin-top: 0; font-weight: bold; color: #0b233a; font-size: 14px;">🔐 Informasi Kredensial Login Anda:</p>

            <p class="label">Alamat Email</p>
            <p><span class="value">{{ $user->email }}</span></p>

            @if($user->nim)
                <p class="label" style="margin-top: 12px;">NIM Mahasiswa</p>
                <p><span class="value">{{ $user->nim }}</span></p>
            @else
                <p class="label" style="margin-top: 12px;">Status Akun</p>
                <p><span class="info-badge">🎓 Fresh Graduate / Non-NIM</span></p>
            @endif

            <p class="label" style="margin-top: 12px;">Kata Sandi Awal</p>
            <p><span class="value">{{ $plainPassword }}</span></p>
        </div>

        <div class="warning">
            ⚠️ <strong>Saran Keamanan:</strong> Harap segera perbarui kata sandi Anda setelah berhasil login pertama kali melalui menu <em>Profil & Pengaturan Akun</em>.
        </div>

        <div style="text-align: center; margin: 26px 0;">
            <a href="{{ url('/login') }}" class="btn">Masuk / Login ke Akun Anda &rarr;</a>
        </div>

        <div class="footer">
            <p style="margin: 4px 0;">Email ini dikirimkan otomatis oleh sistem <strong>Career Intelligence</strong>.</p>
            <p style="margin: 4px 0;">Jika Anda merasa tidak terdaftar, silakan hubungi tim administrator kampus.</p>
        </div>
    </div>
</body>
</html>
