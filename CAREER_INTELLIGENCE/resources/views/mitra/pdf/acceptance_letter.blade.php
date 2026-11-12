<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penerimaan - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 25mm 20mm 25mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
            color: #000000;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-bottom: 0;
        }
        .kop-institution {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1.2;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .kop-subtitle {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111111;
            margin: 3px 0 0 0;
            padding: 0;
        }
        .kop-contact {
            font-size: 9.5pt;
            color: #222222;
            margin: 3px 0 0 0;
            padding: 0;
            line-height: 1.35;
        }
        .kop-divider {
            width: 100%;
            margin-top: 8px;
            margin-bottom: 20px;
            border-top: 2.5px solid #000000;
            border-bottom: 0.75px solid #000000;
            height: 2px;
        }

        /* Judul dan Nomor Surat */
        .letter-title-block {
            text-align: center;
            margin-bottom: 18px;
        }
        .letter-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.3px;
            line-height: 1.3;
            color: #000000;
            margin: 0;
        }
        .letter-number {
            font-size: 11pt;
            color: #000000;
            margin: 4px 0 0 0;
        }

        /* Isi Surat */
        .salutation {
            font-size: 12pt;
            line-height: 1.5;
            margin: 0 0 8px 0;
            color: #000000;
        }
        .paragraph {
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
            text-indent: 10mm;
            margin: 0 0 10px 0;
            color: #000000;
        }

        /* Tabel Data Mahasiswa */
        .data-table {
            width: 95%;
            margin: 6px 0 12px 8mm;
            border-collapse: collapse;
        }
        .data-table td {
            font-size: 11pt;
            line-height: 1.45;
            padding: 2.5px 2px;
            vertical-align: top;
            color: #000000;
        }
        .td-label {
            width: 38%;
            white-space: nowrap;
        }
        .td-colon {
            width: 3%;
            text-align: center;
        }
        .td-value {
            width: 59%;
        }
        .status-accepted {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tanda Tangan & Pengesahan */
        .signature-table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
        }
        .verification-card {
            border: 1px solid #777777;
            padding: 8px 10px;
            background-color: #fafafa;
            border-radius: 4px;
            width: 92%;
        }
        .verification-header {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 0.5px solid #999999;
            padding-bottom: 3px;
            margin-bottom: 4px;
            color: #111111;
        }
        .verification-subtable {
            width: 100%;
            border-collapse: collapse;
        }
        .verification-subtable td {
            font-size: 8pt;
            line-height: 1.4;
            padding: 1.5px 0;
            color: #222222;
        }
        .sign-block {
            text-align: center;
            width: 100%;
        }
        .sign-date {
            font-size: 11.5pt;
            line-height: 1.4;
            color: #000000;
        }
        .sign-company {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            margin-top: 2px;
        }
        .sign-role {
            font-size: 11pt;
            color: #000000;
            margin-top: 1px;
        }
        .stamp-area {
            height: 64px;
            margin: 6px 0;
            text-align: center;
        }
        .stamp-box {
            display: inline-block;
            border: 1.5px solid #004AAD;
            color: #004AAD;
            padding: 5px 12px;
            margin-top: 10px;
            border-radius: 4px;
            text-align: center;
        }
        .stamp-org {
            display: block;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .stamp-tag {
            display: block;
            font-size: 7pt;
            font-weight: bold;
            margin: 1px 0;
            color: #047857;
        }
        .stamp-sys {
            display: block;
            font-size: 6.5pt;
            letter-spacing: 0.3px;
        }
        .sign-name {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            color: #000000;
            margin-top: 4px;
        }

        /* Footer */
        .footer-note {
            margin-top: 18px;
            padding-top: 6px;
            border-top: 0.5px solid #555555;
            font-size: 8.5pt;
            font-style: italic;
            text-align: center;
            color: #444444;
            line-height: 1.35;
        }
    </style>
</head>
<body>

    @php
        $bulanIndo = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        $formattedDate = $issueDate;
        foreach ($bulanIndo as $en => $id) {
            $formattedDate = str_replace($en, $id, $formattedDate);
        }
    @endphp

    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td>
                <div class="kop-institution">{{ $companyName }}</div>
                <div class="kop-subtitle">MITRA INDUSTRI SISTEM KEMITRAAN KAMPUS</div>
                <div class="kop-contact">
                    @if($companyLocation && $companyLocation !== 'Indonesia')
                        Lokasi: {{ $companyLocation }} &bull;
                    @endif
                    @if($companyEmail)
                        Email: {{ $companyEmail }} &bull;
                    @endif
                    Portal Resmi: Career Intelligence Platform
                </div>
            </td>
        </tr>
    </table>
    
    <!-- Garis Ganda Pembatas Kop Surat -->
    <div class="kop-divider"></div>

    <!-- Judul dan Nomor Surat -->
    <div class="letter-title-block">
        <h1 class="letter-title">SURAT KETERANGAN PENERIMAAN MAGANG / KERJA</h1>
        <div class="letter-number">Nomor: {{ $refNumber }}</div>
    </div>

    <!-- Salam Pembuka & Paragraf Pengantar -->
    <div class="salutation">Dengan hormat,</div>

    <p class="paragraph">
        Berdasarkan hasil peninjauan berkas lamaran, penilaian kualifikasi kompetensi, dan seluruh tahapan seleksi yang telah diselenggarakan oleh <strong>{{ $companyName }}</strong>, dengan ini kami menerangkan dan mengonfirmasi secara resmi bahwa:
    </p>

    <!-- Rincian Data Mahasiswa -->
    <table class="data-table">
        <tr>
            <td class="td-label">Nama Lengkap</td>
            <td class="td-colon">:</td>
            <td class="td-value"><strong>{{ $student->name }}</strong></td>
        </tr>
        <tr>
            <td class="td-label">Nomor Induk Mahasiswa (NIM)</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $student->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">Program Studi / Jurusan</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $profile->prodi ?? 'Mahasiswa' }} @if($profile?->jurusan) ({{ $profile->jurusan }}) @endif</td>
        </tr>
        <tr>
            <td class="td-label">Posisi Penugasan</td>
            <td class="td-colon">:</td>
            <td class="td-value"><strong>{{ $job->title }}</strong></td>
        </tr>
        <tr>
            <td class="td-label">Departemen / Divisi</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $job->department }}</td>
        </tr>
        <tr>
            <td class="td-label">Jenis Penugasan &amp; Lokasi</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $job->type }} &bull; {{ $job->location }}</td>
        </tr>
        <tr>
            <td class="td-label">Durasi Pelaksanaan</td>
            <td class="td-colon">:</td>
            <td class="td-value">{{ $job->duration }}</td>
        </tr>
        <tr>
            <td class="td-label">Status Keputusan</td>
            <td class="td-colon">:</td>
            <td class="td-value status-accepted">
                LOLOS &amp; DITERIMA
            </td>
        </tr>
    </table>

    <!-- Paragraf Penetapan & Penutup -->
    <p class="paragraph">
        Kandidat yang bersangkutan dinyatakan <strong>DITERIMA</strong> untuk mengikuti program kerja/magang industri pada posisi tersebut. Kandidat diharapkan berkoordinasi lebih lanjut dengan tim pembimbing industri terkait jadwal efektif penugasan serta petunjuk teknis pelaksanaan program.
    </p>

    <p class="paragraph">
        Demikian surat keterangan penerimaan ini diterbitkan sebagai bukti sah penerimaan magang/kerja untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <!-- Bagian Tanda Tangan & Verifikasi Keabsahan -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; vertical-align: middle;">
                <div class="verification-card">
                    <div class="verification-header">Catatan Otentikasi Digital Sistem</div>
                    <table class="verification-subtable">
                        <tr>
                            <td style="width: 44%;">Verifikasi Dokumen</td>
                            <td style="width: 6%; text-align: center;">:</td>
                            <td style="width: 50%; font-weight: bold; color: #047857;">VALID &amp; RESMI</td>
                        </tr>
                        <tr>
                            <td>ID Lamaran</td>
                            <td style="text-align: center;">:</td>
                            <td><strong>#APP-{{ $application->id }}</strong></td>
                        </tr>
                        <tr>
                            <td>Match Score</td>
                            <td style="text-align: center;">:</td>
                            <td><strong>{{ $application->match_score ?? 0 }}%</strong></td>
                        </tr>
                        <tr>
                            <td>Pangkalan Data</td>
                            <td style="text-align: center;">:</td>
                            <td>Sistem Kemitraan Kampus</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 50%; text-align: center;">
                <div class="sign-block">
                    <div class="sign-date">{{ $companyLocation }}, {{ $formattedDate }}</div>
                    <div class="sign-company">{{ $companyName }}</div>
                    <div class="sign-role">Tim Rekrutmen &amp; Kemitraan</div>
                    
                    <div class="stamp-area">
                        <div class="stamp-box">
                            <span class="stamp-org">{{ Str::limit(strtoupper($companyName), 22) }}</span>
                            <span class="stamp-tag">&bull; DITERBITKAN RESMI &bull;</span>
                            <span class="stamp-sys">VERIFIKASI SISTEM DIGITAL</span>
                        </div>
                    </div>

                    <div class="sign-name"><strong><u>( {{ $companyName }} )</u></strong></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Catatan Kaki Resmi -->
    <div class="footer-note">
        Dokumen ini dibuat dan diterbitkan secara resmi melalui Sistem Career Intelligence Platform. Keaslian data dapat diverifikasi pada database sistem kemitraan kampus.
    </div>

</body>
</html>
