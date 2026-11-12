<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: #0b233a; font-size: 22px; margin-bottom: 20px; }
        .welcome-box { padding: 20px; border-radius: 10px; background-color: #eef2ff; color: #004AAD; border: 1px solid #c7d2fe; margin: 20px 0; text-align: center; }
        .credentials { background-color: #f8fafc; padding: 20px; border-radius: 10px; margin: 20px 0; font-size: 14px; border: 1px solid #e2e8f0; }
        .credentials p { margin: 8px 0; }
        .credentials .label { color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .credentials .value { color: #0b233a; font-weight: bold; font-size: 16px; background: #fff; padding: 8px 14px; border-radius: 8px; border: 1px solid #e2e8f0; display: inline-block; margin-top: 4px; font-family: 'Courier New', monospace; }
        .warning { background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin: 15px 0; }
        .btn { display: inline-block; padding: 14px 28px; background-color: #004AAD; color: #fff; text-decoration: none; border-radius: 10px; font-weight: bold; margin-top: 15px; font-size: 14px; }
        .footer { margin-top: 30px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Selamat Datang, {{ $user->name }}!</h1>

        <div class="welcome-box">
            <p style="margin:0; font-size: 15px; font-weight: bold;">Akun Mitra Industri Anda Telah Aktif</p>
            <p style="margin: 5px 0 0; font-size: 13px; color: #4f46e5;">Platform Career Intelligence — Pusat Karier Kampus</p>
        </div>

        <p>Administrator telah mendaftarkan perusahaan Anda sebagai <strong>Mitra Industri</strong> pada platform Career Intelligence. Anda sekarang dapat mengelola lowongan kerja, meninjau lamaran mahasiswa, dan melakukan proses seleksi secara digital.</p>

        <div class="credentials">
            <p style="margin-top:0; font-weight: bold; color: #0b233a; font-size: 15px;">🔐 Informasi Login Anda:</p>

            <p class="label">Alamat Email</p>
            <p><span class="value">{{ $user->email }}</span></p>

            <p class="label" style="margin-top: 14px;">Kata Sandi</p>
            <p><span class="value">{{ $plainPassword }}</span></p>
        </div>

        <div class="warning">
            ⚠️ <strong>Penting:</strong> Demi keamanan, segera ubah kata sandi Anda setelah login pertama kali melalui menu <em>Profil → Keamanan Akun</em>.
        </div>

        <center>
            <a href="{{ url('/login') }}" class="btn">Login ke Portal Mitra →</a>
        </center>

        <div class="footer">
            <p>Ini adalah email otomatis dari platform Career Intelligence.</p>
            <p>Jika Anda merasa tidak mendaftar, silakan abaikan email ini.</p>
        </div>
    </div>
</body>
</html>
