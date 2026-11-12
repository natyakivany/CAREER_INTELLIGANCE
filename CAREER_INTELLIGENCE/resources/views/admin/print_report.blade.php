<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Rekapitulasi Kesiapan Kerja Mahasiswa - Career Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            .no-print { display: none !important; }
            html, body { background: white !important; color: black !important; padding: 1.5cm !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-[#0B1B3D] font-sans p-4 sm:p-8">

    <!-- Top Action Bar (hidden when printing) -->
    <div class="max-w-5xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl shadow border border-slate-200">
        <div>
            <h1 class="text-sm font-bold text-slate-800">Pratinjau Laporan Resmi Kesiapan Kerja Mahasiswa</h1>
            <p class="text-xs text-slate-500">Format standar akreditasi jurusan Teknologi Informasi & Teknik Elektro</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('admin.reports.print') }}">
                <select name="tingkat" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 text-[#0B1B3D] text-xs font-semibold rounded-lg focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] cursor-pointer">
                    <option value="">Semua (Siswa, Mahasiswa, & Alumni)</option>
                    <option value="Mahasiswa" {{ request('tingkat') === 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa (Perguruan Tinggi)</option>
                    <option value="SMK" {{ request('tingkat') === 'SMK' ? 'selected' : '' }}>Siswa SMK</option>
                    <option value="Alumni" {{ request('tingkat') === 'Alumni' ? 'selected' : '' }}>Alumni / Fresh Graduate</option>
                </select>

                <select name="jurusan" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 text-[#0B1B3D] text-xs font-semibold rounded-lg focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] cursor-pointer">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanOptions ?? [] as $jur)
                        <option value="{{ $jur }}" {{ request('jurusan') === $jur ? 'selected' : '' }}>{{ $jur }}</option>
                    @endforeach
                </select>

                <select name="asal_sekolah" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 text-[#0B1B3D] text-xs font-semibold rounded-lg focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] cursor-pointer">
                    <option value="">Semua Asal Sekolah</option>
                    @foreach($sekolahOptions ?? [] as $sek)
                        <option value="{{ $sek }}" {{ request('asal_sekolah') === $sek ? 'selected' : '' }}>{{ $sek }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-[#0B1B3D] text-xs font-bold rounded-lg transition">
                &larr; Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-[#004AAD] hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Main Printable Paper Sheet -->
    <div class="max-w-5xl mx-auto bg-white text-black p-8 sm:p-12 rounded-xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0">
        
        <!-- Institutional Kop Surat -->
        <div class="border-b-2 border-black pb-4 mb-6 flex items-center gap-5">
            <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-300 text-slate-800 flex items-center justify-center font-extrabold text-xl shrink-0 print:border print:border-black">
                CI
            </div>
            <div class="flex-1">
                <h2 class="text-base sm:text-lg font-black tracking-tight uppercase text-black leading-tight">
                    Pusat Pengembangan Karier & Tracer Study {{ request('tingkat') === 'SMK' ? 'Sekolah' : 'Kampus' }}
                </h2>
                <h3 class="text-xs sm:text-sm font-bold text-slate-700 uppercase tracking-wide">
                    Portal Intelijen Karier Mahasiswa {{ request('jurusan') ? request('jurusan') : 'Teknologi Informasi & Teknik Elektro' }}
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Sistem Evaluasi Berbasis Portofolio Riil & Asesmen Standar Industri • Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}
                </p>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-6">
            <h3 class="text-sm sm:text-base font-black uppercase tracking-wider text-black underline decoration-slate-400 decoration-2 underline-offset-4">
                Laporan Rekapitulasi Evaluasi Kesiapan Kerja Mahasiswa {{ request('tingkat') ? '(' . (request('tingkat') === 'SMK' ? 'Siswa SMK' : (request('tingkat') === 'Alumni' ? 'Alumni' : 'Mahasiswa')) . ')' : '' }} (Employability Index)
            </h3>
            <p class="text-xs text-slate-500 mt-1">Tanggal Cetak: {{ date('d F Y') }} • Dicetak oleh: Administrator Sistem</p>
        </div>

        <!-- Executive Summary Cards -->
        <div class="grid grid-cols-4 gap-3 mb-6">
            <div class="border border-slate-300 p-3 rounded-lg text-center bg-slate-50 print:bg-transparent">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Total Mahasiswa</p>
                <p class="text-lg font-black text-black mt-0.5">{{ $totalStudents }}</p>
            </div>
            <div class="border border-slate-300 p-3 rounded-lg text-center bg-slate-50 print:bg-transparent">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Mitra Industri</p>
                <p class="text-lg font-black text-black mt-0.5">{{ $totalMitra }}</p>
            </div>
            <div class="border border-slate-300 p-3 rounded-lg text-center bg-slate-50 print:bg-transparent">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Portofolio Terkumpul</p>
                <p class="text-lg font-black text-black mt-0.5">{{ $totalPortfolios }} Proyek</p>
            </div>
            <div class="border border-slate-300 p-3 rounded-lg text-center bg-slate-50 print:bg-transparent">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Rata-rata Skor</p>
                <p class="text-lg font-black text-[#004AAD] mt-0.5">{{ $avgScore }} / 100</p>
            </div>
        </div>

        <!-- Student Data Table -->
        <table class="w-full text-left text-xs border border-slate-300 divide-y divide-slate-300 mb-8">
            <thead class="bg-slate-100 text-slate-800 font-bold uppercase text-[10px]">
                <tr>
                    <th class="py-2.5 px-3 border-r border-slate-300 text-center w-10">No</th>
                    <th class="py-2.5 px-3 border-r border-slate-300">Nama {{ request('tingkat') === 'SMK' ? 'Siswa' : 'Peserta' }}</th>
                    <th class="py-2.5 px-3 border-r border-slate-300">NIM / NISN</th>
                    <th class="py-2.5 px-3 border-r border-slate-300">Asal Kampus / Sekolah</th>
                    <th class="py-2.5 px-3 border-r border-slate-300">Program Studi / Bidang</th>
                    <th class="py-2.5 px-3 border-r border-slate-300 text-center">Portofolio</th>
                    <th class="py-2.5 px-3 border-r border-slate-300 text-center">IPK</th>
                    <th class="py-2.5 px-3 border-r border-slate-300 text-center">Skor Kesiapan</th>
                    <th class="py-2.5 px-3 text-center">Kualifikasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-300 text-[11px] text-black">
                @forelse ($students as $index => $s)
                    @php
                        $profile = $s->studentProfile;
                        $score = $profile?->employability_score ?? 0;
                    @endphp
                    <tr>
                        <td class="py-2 px-3 border-r border-slate-300 text-center">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 border-r border-slate-300">
                            <span class="font-bold text-black block">{{ $s->name }}</span>
                            <span class="text-[9px] font-semibold text-slate-600 block">{{ $profile?->isSmk() ? '🏫 Siswa SMK' : '🎓 Mahasiswa' }}</span>
                        </td>
                        <td class="py-2 px-3 border-r border-slate-300 font-mono">{{ $s->nim ?? '-' }}</td>
                        <td class="py-2 px-3 border-r border-slate-300">{{ $profile?->institution_name ?? $profile?->high_school_name ?? '-' }}</td>
                        <td class="py-2 px-3 border-r border-slate-300">
                            {{ $profile?->prodi ?? '-' }}
                            <span class="text-[9px] text-slate-500 block">({{ strtoupper($profile?->target_field ?? 'it') }})</span>
                        </td>
                        <td class="py-2 px-3 border-r border-slate-300 text-center font-bold">{{ $s->portfolios->count() }}</td>
                        <td class="py-2 px-3 border-r border-slate-300 text-center">{{ $profile?->ipk ?? '3.50' }}</td>
                        <td class="py-2 px-3 border-r border-slate-300 text-center font-black {{ $score >= 75 ? 'text-[#004AAD]' : 'text-slate-800' }}">
                            {{ $score > 0 ? $score . '%' : '-' }}
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if ($score >= 75)
                                <span class="font-bold text-[#004AAD]">Siap Kerja</span>
                            @elseif ($score > 0)
                                <span class="font-semibold text-amber-600">Perlu Penguatan</span>
                            @else
                                <span class="text-slate-500 italic">Belum Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-slate-500">Belum ada data mahasiswa terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Sign-Off Signature Section -->
        <div class="flex justify-between items-end pt-8 mt-12 border-t border-slate-300 text-xs">
            <div class="text-slate-500 text-[10px]">
                Dokumen resmi digenerate secara otomatis oleh:<br>
                <strong class="text-black">Career Intelligence Platform System</strong>
            </div>
            <div class="text-center w-64">
                <p class="text-black mb-16">
                    Mengetahui,<br>
                    <strong>Koordinator Pusat Pengembangan Karier</strong>
                </p>
                <div class="border-b border-black mx-auto w-48"></div>
                <p class="text-[11px] font-bold text-black mt-1">Administrator Inovindo Academy</p>
                <p class="text-[10px] text-slate-600">NIP. 19850412 201012 1 004</p>
            </div>
        </div>

    </div>

</body>
</html>






