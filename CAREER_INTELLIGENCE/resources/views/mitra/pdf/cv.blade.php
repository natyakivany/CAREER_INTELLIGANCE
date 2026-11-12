<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Mahasiswa - {{ $student->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #004AAD;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0b1120;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #555;
            font-size: 14px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #004AAD;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .score-box {
            display: inline-block;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 8px 15px;
            border-radius: 5px;
            text-align: center;
            margin-right: 15px;
        }
        .score-box span {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            color: #15803d;
        }
        .score-box strong {
            font-size: 20px;
        }
        .portfolio-item {
            margin-bottom: 15px;
            background-color: #f8fafc;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }
        .portfolio-item h4 {
            margin: 0 0 5px;
            color: #0f172a;
        }
        .portfolio-item p {
            margin: 0 0 5px;
            font-size: 13px;
            color: #475569;
        }
        .tag {
            display: inline-block;
            background-color: #e2e8f0;
            color: #334155;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .notes {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 10px 15px;
            font-size: 13px;
            color: #334155;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $student->name }}</h1>
        <p>{{ $profile->targetOccupation?->title ?? 'Kandidat Mahasiswa' }}</p>
        <p>{{ $profile->prodi ?? '-' }} • Semester {{ $profile->semester ?? '-' }}</p>
        <p>{{ $student->email }} • NIM: {{ $student->nim ?? '-' }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Profil</div>
        <p style="font-size: 14px; text-align: justify;">{{ $profile->bio ?? 'Belum ada ringkasan profil.' }}</p>
    </div>

    <div class="section">
        <div class="section-title">Hasil Penilaian (Career Intel)</div>
        <div style="margin-bottom: 15px;">
            <div class="score-box">
                <span>Employability Score</span>
                <strong>{{ $latestAssessment->employability_score ?? 0 }}%</strong>
            </div>
            <div class="score-box">
                <span>Skor Asesmen AI</span>
                <strong>{{ round($latestAssessment->total_score ?? 0) }}/100</strong>
            </div>
            <div class="score-box">
                <span>Status Kesiapan</span>
                @php
                    $cvEmpScore = (int) ($latestAssessment->employability_score ?? 0);
                    $cvStatus = $cvEmpScore >= 80 ? 'Siap Kerja' : ($cvEmpScore >= 50 ? 'Berkembang' : 'Baru Mulai');
                    $cvColor = $cvEmpScore >= 80 ? 'color:#16a34a;' : ($cvEmpScore >= 50 ? 'color:#d97706;' : 'color:#64748b;');
                @endphp
                <strong style="{{ $cvColor }}">
                    {{ $cvStatus }}
                </strong>
            </div>
        </div>

        @if($latestAssessment?->ai_feedback)
        <div class="notes">
            <strong>Catatan Evaluasi AI:</strong><br>
            {{ $latestAssessment->ai_feedback }}
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Keterampilan Utama</div>
        <div>
            @forelse($topSkills as $skill)
                <span class="tag">{{ $skill }}</span>
            @empty
                <p style="font-size: 13px; color: #64748b;">Belum ada data skill.</p>
            @endforelse
        </div>
    </div>

    <div class="section">
        <div class="section-title">Portofolio & Proyek</div>
        @forelse($student->portfolios as $portfolio)
            <div class="portfolio-item">
                <h4>{{ $portfolio->title }}</h4>
                <p><strong>Deskripsi:</strong> {{ $portfolio->description ?? 'Tidak ada deskripsi' }}</p>
                <p><strong>Teknologi:</strong> {{ $portfolio->tech_stack ?? '-' }}</p>
                @if($portfolio->project_url)
                    <p><strong>Link Proyek:</strong> <a href="{{ $portfolio->project_url }}">{{ $portfolio->project_url }}</a></p>
                @endif
                @if($portfolio->repository_url)
                    <p><strong>Repositori:</strong> <a href="{{ $portfolio->repository_url }}">{{ $portfolio->repository_url }}</a></p>
                @endif
            </div>
        @empty
            <p style="font-size: 13px; color: #64748b;">Kandidat belum mengunggah portofolio.</p>
        @endforelse
    </div>

    <div style="text-align: center; margin-top: 40px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px;">
        Dokumen ini dibuat otomatis oleh sistem Career Intelligence pada {{ now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>







