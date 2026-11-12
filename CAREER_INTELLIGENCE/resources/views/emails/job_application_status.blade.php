<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: #0b233a; font-size: 22px; margin-bottom: 20px; }
        .status-box { padding: 15px; border-radius: 8px; font-weight: bold; margin: 20px 0; text-align: center; }
        .bg-indigo { background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
        .bg-purple { background-color: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }
        .bg-emerald { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .bg-rose { background-color: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
        .details { background-color: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #004AAD; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 10px; }
        .footer { margin-top: 30px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Halo {{ $application->user->name }},</h1>
        <p>Ada pembaruan mengenai lamaran Anda untuk posisi <strong>{{ $application->jobListing->title }}</strong> di <strong>{{ $application->jobListing->user->name ?? 'Mitra Perusahaan' }}</strong>.</p>
        
        @php
            $statusClass = 'bg-indigo';
            $statusText = $application->status;
            
            if ($application->status === 'Wawancara') {
                $statusClass = 'bg-purple';
            } elseif ($application->status === 'Diterima') {
                $statusClass = 'bg-emerald';
            } elseif ($application->status === 'Ditolak') {
                $statusClass = 'bg-rose';
            }
        @endphp

        <div class="status-box {{ $statusClass }}">
            Status Saat Ini: {{ $statusText }}
        </div>

        @if($application->status === 'Wawancara' && $application->interview_date)
        <div class="details">
            <p style="margin-top:0;"><strong>Jadwal Wawancara ({{ $application->isOfflineInterview() ? 'Tatap Muka / Offline' : 'Daring / Online' }}):</strong></p>
            <p><strong>Metode:</strong> {{ $application->isOfflineInterview() ? '🏢 Offline (Tatap Muka Langsung)' : '🌐 Online (Meeting Virtual)' }}</p>
            <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($application->interview_date)->translatedFormat('l, d F Y - H:i') }} WIB</p>
            @if($application->isOfflineInterview() && $application->interview_location)
                <p><strong>Lokasi Pertemuan:</strong> {{ $application->interview_location }}</p>
            @endif
            @if($application->interview_link)
                <p><strong>Tautan (Link):</strong> <a href="{{ $application->interview_link }}">{{ $application->interview_link }}</a></p>
            @endif
            <p style="margin-bottom:0;">Silakan konfirmasi kehadiran Anda melalui dasbor mahasiswa.</p>
        </div>
        @endif

        <center>
            <a href="{{ route('applications.index') }}" class="btn">Cek Dasbor Lamaran</a>
        </center>

        <div class="footer">
            Ini adalah email otomatis dari platform Career Intelligence.
        </div>
    </div>
</body>
</html>
