@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex flex-wrap items-center gap-2">
                <span>Peringkat & Statistik Ekosistem</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-bold bg-blue-50 text-[#004AAD] rounded-full border border-blue-200 tracking-wide uppercase">
                    <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Pusat Prestasi
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Pemantauan komprehensif performa kesiapan kerja mahasiswa & siswa SMK, penyerapan mitra industri, dan dinamika kebutuhan pasar.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.reports.print') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Laporan</span>
            </a>
            <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-[#004AAD] hover:bg-[#003B8B] rounded-xl shadow-md shadow-[#004AAD]/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Kelola Data Talenta</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-8 pb-10">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Top Skor Kesiapan (Mahasiswa & SMK Toggle) -->
        <div x-data="{ kpiTab: 'mahasiswa' }" class="group relative bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="flex items-center justify-between mb-3 relative">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Top Skor Kesiapan</span>
                <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg text-[10px] font-bold">
                    <button type="button" @click="kpiTab = 'mahasiswa'" :class="kpiTab === 'mahasiswa' ? 'bg-[#004AAD] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2 py-0.5 rounded-md transition">PT</button>
                    <button type="button" @click="kpiTab = 'smk'" :class="kpiTab === 'smk' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2 py-0.5 rounded-md transition">SMK</button>
                </div>
            </div>
            
            <!-- Tab Mahasiswa (PT) -->
            <div x-show="kpiTab === 'mahasiswa'" x-transition>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[#0B1B3D] tracking-tight">
                        {{ $highestScoreMahasiswa ? round($highestScoreMahasiswa->employability_score) : 0 }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">/ 100</span>
                    <span class="ml-auto text-[10px] font-extrabold text-[#004AAD] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                        🎓 Mahasiswa #1
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-800 truncate mt-1">
                    {{ $highestScoreMahasiswa ? $highestScoreMahasiswa->user->name : 'Belum Ada Data' }}
                </p>
                <p class="text-[11px] text-slate-600 truncate">
                    {{ $highestScoreMahasiswa ? ($highestScoreMahasiswa->prodi ?? 'Perguruan Tinggi') : 'Ikuti asesmen' }}
                </p>
            </div>

            <!-- Tab SMK -->
            <div x-show="kpiTab === 'smk'" x-transition style="display: none;">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[#0B1B3D] tracking-tight">
                        {{ $highestScoreSmk ? round($highestScoreSmk->employability_score) : 0 }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">/ 100</span>
                    <span class="ml-auto text-[10px] font-extrabold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                        🏫 SMK #1
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-800 truncate mt-1">
                    {{ $highestScoreSmk ? $highestScoreSmk->user->name : 'Belum Ada Data SMK' }}
                </p>
                <p class="text-[11px] text-slate-600 truncate">
                    {{ $highestScoreSmk ? ($highestScoreSmk->prodi ?? 'SMK / Vokasi') : 'Ikuti asesmen' }}
                </p>
            </div>
        </div>

        <!-- Mitra Perekrut Terbanyak -->
        <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="flex items-center justify-between mb-3 relative">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mitra Teraktif</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="relative">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[#0B1B3D] tracking-tight">
                        {{ $totalAcceptedStudents }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Talenta Diterima</span>
                    <span class="ml-auto text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        Terserap
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-800 truncate mt-1">
                    {{ $topHiringMitra ? $topHiringMitra->name : 'Belum Ada Mitra' }}
                </p>
                <p class="text-[11px] text-slate-600 truncate">
                    Penyerap lulusan terbanyak saat ini
                </p>
            </div>
        </div>

        <!-- Rata-rata Skor Kesiapan -->
        <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="flex items-center justify-between mb-3 relative">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rata-rata Skor Kesiapan</span>
                <div class="w-9 h-9 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <div class="relative">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[#004AAD] tracking-tight">
                        {{ $avgTopScore }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">/ 100</span>
                    <span class="ml-auto text-[11px] font-extrabold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                        Indeks
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-[#004AAD] h-1.5 rounded-full" style="width: {{ min($avgTopScore, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-600 mt-2 font-medium">
                    <span>🎓 Mhs: <strong class="text-slate-800">{{ $avgScoreMahasiswa }}%</strong></span>
                    <span class="text-slate-300">•</span>
                    <span>🏫 SMK: <strong class="text-slate-800">{{ $avgScoreSmk }}%</strong></span>
                </div>
            </div>
        </div>

        <!-- Lowongan & Peminatan -->
        <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="flex items-center justify-between mb-3 relative">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Peluang & Lamaran</span>
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="relative">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[#0B1B3D] tracking-tight">
                        {{ $totalOpenJobs }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">Lowongan</span>
                    <span class="ml-auto text-[11px] font-extrabold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">
                        Aktif
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-800 truncate mt-1">
                    {{ $totalApplications }} Berkas Lamaran Diajukan
                </p>
                <p class="text-[11px] text-slate-600 truncate">
                    Tersebar di {{ $popularFields->count() }} bidang industri
                </p>
            </div>
        </div>

    </div>

    <!-- Podium Juara Kesiapan Kerja (Hall of Fame Top Performer) -->
    <div class="bg-[#0B1B3D] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden" x-data="{ podiumTab: 'mahasiswa' }">

        <div class="relative z-10 text-center max-w-xl mx-auto mb-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur border border-white/20 text-amber-300 text-xs font-extrabold uppercase tracking-widest shadow-inner">
                ✨ Hall of Fame Kesiapan Kerja
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-white mt-2 tracking-tight">
                Podium Terbaik Kesiapan Kerja
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                Peringkat talenta teratas berdasarkan Skor Kesiapan Kerja (Employability Index) hasil evaluasi komprehensif AI.
            </p>

            <!-- Tingkat Switcher Tabs pada Podium -->
            <div class="inline-flex items-center gap-1 p-1 bg-white/10 backdrop-blur-md rounded-2xl mt-5 border border-white/15">
                <button type="button" @click="podiumTab = 'mahasiswa'" 
                        :class="podiumTab === 'mahasiswa' ? 'bg-[#004AAD] text-white shadow-lg font-black' : 'text-slate-300 hover:text-white font-semibold'" 
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <span>🎓 Tingkat Mahasiswa (PT)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-black">{{ $topMahasiswa->count() }}</span>
                </button>
                <button type="button" @click="podiumTab = 'smk'" 
                        :class="podiumTab === 'smk' ? 'bg-amber-600 text-white shadow-lg font-black' : 'text-slate-300 hover:text-white font-semibold'" 
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <span>🏫 Tingkat SMK / Vokasi</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-black">{{ $topSmk->count() }}</span>
                </button>
            </div>
        </div>

        <!-- PODIUM TAB: MAHASISWA -->
        <div x-show="podiumTab === 'mahasiswa'" x-transition>
            @if ($topMahasiswa->count() >= 3)
                @php
                    $mRank1 = $topMahasiswa[0];
                    $mRank2 = $topMahasiswa[1];
                    $mRank3 = $topMahasiswa[2];
                @endphp
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-end max-w-4xl mx-auto pt-4 pb-2">
                    
                    <!-- JUARA 2 MAHASISWA (Silver - Kiri) -->
                    <div class="order-2 md:order-1 flex flex-col items-center">
                        <div class="relative mb-3 flex flex-col items-center group">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-200 text-slate-800 font-extrabold flex items-center justify-center text-lg sm:text-xl shadow-lg ring-4 ring-slate-300/60 overflow-hidden relative">
                                @if($mRank2->avatar_url)
                                    <img src="{{ $mRank2->avatar_url }}" alt="{{ $mRank2->user->name }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($mRank2->user->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <span class="absolute -top-3 px-2 py-0.5 rounded-full bg-slate-300 text-slate-900 font-black text-[11px] shadow border border-white flex items-center gap-1">
                                🥈 #2 Mhs
                            </span>
                        </div>
                        <div class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-center hover:bg-white/15 transition shadow-lg">
                            <h3 class="text-sm sm:text-base font-extrabold text-white truncate" title="{{ $mRank2->user->name }}">
                                {{ $mRank2->user->name }}
                            </h3>
                            <p class="text-xs text-slate-300 truncate mt-0.5">{{ $mRank2->prodi ?? 'Prodi Belum Diisi' }}</p>
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-200/20 text-slate-100 font-black text-sm border border-slate-200/30">
                                    {{ round($mRank2->employability_score) }} <span class="text-[10px] font-semibold text-slate-300">SKOR</span>
                                </span>
                                @if($mRank2->ipk)
                                    <span class="px-2 py-1 rounded-lg bg-white/10 text-slate-200 font-bold text-xs">
                                        {{ $mRank2->ipk > 4 ? 'Nilai' : 'IPK' }} {{ number_format((float)$mRank2->ipk, 2) }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-center gap-2">
                                <a href="{{ route('admin.students.show', $mRank2->user) }}" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white transition">
                                    Lihat Profil
                                </a>
                                @if($mRank2->isCvFilled())
                                    <a href="{{ route('admin.students.cv', $mRank2->user) }}" target="_blank" class="text-[11px] font-bold px-2.5 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-200 transition" title="Lihat CV ATS (Aktif)">
                                        CV ATS
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- JUARA 1 MAHASISWA (Gold - Tengah, elevated) -->
                    <div class="order-1 md:order-2 flex flex-col items-center -mt-4 md:-mt-8">
                        <div class="relative mb-3 flex flex-col items-center group">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-amber-400 text-amber-950 font-black flex items-center justify-center text-2xl shadow-2xl ring-4 ring-amber-400/80 overflow-hidden relative animate-pulse">
                                @if($mRank1->avatar_url)
                                    <img src="{{ $mRank1->avatar_url }}" alt="{{ $mRank1->user->name }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($mRank1->user->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <span class="absolute -top-4 px-3 py-1 rounded-full bg-amber-400 text-amber-950 font-black text-xs shadow-lg border-2 border-white flex items-center gap-1.5">
                                👑 🥇 #1 TOP MAHASISWA
                            </span>
                        </div>
                        <div class="w-full bg-white/15 backdrop-blur-md border-2 border-amber-400/40 rounded-2xl p-5 text-center shadow-2xl relative overflow-hidden">
                            <div class="absolute top-0 inset-x-0 h-1 bg-amber-400"></div>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-amber-400/20 text-amber-300 font-extrabold text-[10px] uppercase tracking-wider mb-1">
                                🏆 Juara Kesiapan Kerja Mahasiswa
                            </span>
                            <h3 class="text-base sm:text-lg font-black text-white truncate" title="{{ $mRank1->user->name }}">
                                {{ $mRank1->user->name }}
                            </h3>
                            <p class="text-xs text-amber-100 font-medium truncate mt-0.5">{{ $mRank1->prodi ?? 'Prodi Belum Diisi' }}</p>
                            
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <span class="px-3.5 py-1.5 rounded-xl bg-amber-400 text-amber-950 font-black text-base shadow-md">
                                    {{ round($mRank1->employability_score) }} <span class="text-xs font-bold text-amber-900">SKOR</span>
                                </span>
                                @if($mRank1->ipk)
                                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-white font-bold text-xs border border-white/20">
                                        {{ $mRank1->ipk > 4 ? 'Nilai' : 'IPK' }} {{ number_format((float)$mRank1->ipk, 2) }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-center gap-2">
                                <a href="{{ route('admin.students.show', $mRank1->user) }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-white text-[#0B1B3D] hover:bg-amber-50 shadow-md transition font-extrabold">
                                    Buka Profil
                                </a>
                                @if($mRank1->isCvFilled())
                                    <a href="{{ route('admin.students.cv', $mRank1->user) }}" target="_blank" class="text-xs font-bold px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-md transition" title="Lihat CV ATS (Aktif)">
                                        CV ATS
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- JUARA 3 MAHASISWA (Bronze - Kanan) -->
                    <div class="order-3 flex flex-col items-center">
                        <div class="relative mb-3 flex flex-col items-center group">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-amber-700 text-amber-100 font-extrabold flex items-center justify-center text-lg sm:text-xl shadow-lg ring-4 ring-amber-700/60 overflow-hidden relative">
                                @if($mRank3->avatar_url)
                                    <img src="{{ $mRank3->avatar_url }}" alt="{{ $mRank3->user->name }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($mRank3->user->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <span class="absolute -top-3 px-2 py-0.5 rounded-full bg-amber-700 text-amber-100 font-black text-[11px] shadow border border-white/40 flex items-center gap-1">
                                🥉 #3 Mhs
                            </span>
                        </div>
                        <div class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-center hover:bg-white/15 transition shadow-lg">
                            <h3 class="text-sm sm:text-base font-extrabold text-white truncate" title="{{ $mRank3->user->name }}">
                                {{ $mRank3->user->name }}
                            </h3>
                            <p class="text-xs text-slate-300 truncate mt-0.5">{{ $mRank3->prodi ?? 'Prodi Belum Diisi' }}</p>
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-700/30 text-amber-200 font-black text-sm border border-amber-600/30">
                                    {{ round($mRank3->employability_score) }} <span class="text-[10px] font-semibold text-amber-300">SKOR</span>
                                </span>
                                @if($mRank3->ipk)
                                    <span class="px-2 py-1 rounded-lg bg-white/10 text-slate-200 font-bold text-xs">
                                        {{ $mRank3->ipk > 4 ? 'Nilai' : 'IPK' }} {{ number_format((float)$mRank3->ipk, 2) }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-center gap-2">
                                <a href="{{ route('admin.students.show', $mRank3->user) }}" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white transition">
                                    Lihat Profil
                                </a>
                                @if($mRank3->isCvFilled())
                                    <a href="{{ route('admin.students.cv', $mRank3->user) }}" target="_blank" class="text-[11px] font-bold px-2.5 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-200 transition" title="Lihat CV ATS (Aktif)">
                                        CV ATS
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-xs text-slate-300">Data mahasiswa terasesmen belum mencapai 3 orang untuk podium lengkap.</p>
                </div>
            @endif
        </div>

        <!-- PODIUM TAB: SMK / VOKASI -->
        <div x-show="podiumTab === 'smk'" x-transition style="display: none;">
            @if ($topSmk->isNotEmpty())
                @if ($topSmk->count() >= 3)
                    @php
                        $sRank1 = $topSmk[0];
                        $sRank2 = $topSmk[1];
                        $sRank3 = $topSmk[2];
                    @endphp
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-end max-w-4xl mx-auto pt-4 pb-2">
                        
                        <!-- JUARA 2 SMK -->
                        <div class="order-2 md:order-1 flex flex-col items-center">
                            <div class="relative mb-3 flex flex-col items-center group">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-200 text-slate-800 font-extrabold flex items-center justify-center text-lg sm:text-xl shadow-lg ring-4 ring-slate-300/60 overflow-hidden relative">
                                    <span>{{ strtoupper(substr($sRank2->user->name, 0, 2)) }}</span>
                                </div>
                                <span class="absolute -top-3 px-2 py-0.5 rounded-full bg-slate-300 text-slate-900 font-black text-[11px] shadow border border-white flex items-center gap-1">
                                    🥈 #2 SMK
                                </span>
                            </div>
                            <div class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-center hover:bg-white/15 transition shadow-lg">
                                <h3 class="text-sm sm:text-base font-extrabold text-white truncate">{{ $sRank2->user->name }}</h3>
                                <p class="text-xs text-slate-300 truncate mt-0.5">{{ $sRank2->prodi ?? 'SMK' }}</p>
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-200/20 text-slate-100 font-black text-sm border border-slate-200/30">
                                        {{ round($sRank2->employability_score) }} <span class="text-[10px] font-semibold text-slate-300">SKOR</span>
                                    </span>
                                </div>
                                <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.students.show', $sRank2->user) }}" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white transition">Lihat Profil</a>
                                </div>
                            </div>
                        </div>

                        <!-- JUARA 1 SMK -->
                        <div class="order-1 md:order-2 flex flex-col items-center -mt-4 md:-mt-8">
                            <div class="relative mb-3 flex flex-col items-center group">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-amber-400 text-amber-950 font-black flex items-center justify-center text-2xl shadow-2xl ring-4 ring-amber-400/80 overflow-hidden relative animate-pulse">
                                    <span>{{ strtoupper(substr($sRank1->user->name, 0, 2)) }}</span>
                                </div>
                                <span class="absolute -top-4 px-3 py-1 rounded-full bg-amber-400 text-amber-950 font-black text-xs shadow-lg border-2 border-white flex items-center gap-1.5">
                                    👑 🥇 #1 TOP SMK
                                </span>
                            </div>
                            <div class="w-full bg-white/15 backdrop-blur-md border-2 border-amber-400/40 rounded-2xl p-5 text-center shadow-2xl relative overflow-hidden">
                                <div class="absolute top-0 inset-x-0 h-1 bg-amber-400"></div>
                                <span class="inline-block px-2 py-0.5 rounded-md bg-amber-400/20 text-amber-300 font-extrabold text-[10px] uppercase tracking-wider mb-1">
                                    🏆 Juara Kesiapan Kerja Tingkat SMK
                                </span>
                                <h3 class="text-base sm:text-lg font-black text-white truncate">{{ $sRank1->user->name }}</h3>
                                <p class="text-xs text-amber-100 font-medium truncate mt-0.5">{{ $sRank1->prodi ?? 'SMK' }}</p>
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <span class="px-3.5 py-1.5 rounded-xl bg-amber-400 text-amber-950 font-black text-base shadow-md">
                                        {{ round($sRank1->employability_score) }} <span class="text-xs font-bold text-amber-900">SKOR</span>
                                    </span>
                                </div>
                                <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.students.show', $sRank1->user) }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-white text-[#0B1B3D] hover:bg-amber-50 shadow-md transition font-extrabold">Buka Profil</a>
                                    @if($sRank1->isCvFilled())
                                        <a href="{{ route('admin.students.cv', $sRank1->user) }}" target="_blank" class="text-xs font-bold px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-md transition">CV ATS</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- JUARA 3 SMK -->
                        <div class="order-3 flex flex-col items-center">
                            <div class="relative mb-3 flex flex-col items-center group">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-amber-700 text-amber-100 font-extrabold flex items-center justify-center text-lg sm:text-xl shadow-lg ring-4 ring-amber-700/60 overflow-hidden relative">
                                    <span>{{ strtoupper(substr($sRank3->user->name, 0, 2)) }}</span>
                                </div>
                                <span class="absolute -top-3 px-2 py-0.5 rounded-full bg-amber-700 text-amber-100 font-black text-[11px] shadow border border-white/40 flex items-center gap-1">
                                    🥉 #3 SMK
                                </span>
                            </div>
                            <div class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-center hover:bg-white/15 transition shadow-lg">
                                <h3 class="text-sm sm:text-base font-extrabold text-white truncate">{{ $sRank3->user->name }}</h3>
                                <p class="text-xs text-slate-300 truncate mt-0.5">{{ $sRank3->prodi ?? 'SMK' }}</p>
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-700/30 text-amber-200 font-black text-sm border border-amber-600/30">
                                        {{ round($sRank3->employability_score) }} <span class="text-[10px] font-semibold text-amber-300">SKOR</span>
                                    </span>
                                </div>
                                <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.students.show', $sRank3->user) }}" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white transition">Lihat Profil</a>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <!-- Spotlight Juara SMK (Jika < 3 talenta) -->
                    @php $sTop = $topSmk->first(); @endphp
                    <div class="max-w-md mx-auto pt-2 pb-2">
                        <div class="flex flex-col items-center">
                            <div class="relative mb-3 flex flex-col items-center group">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-amber-400 text-amber-950 font-black flex items-center justify-center text-2xl shadow-2xl ring-4 ring-amber-400/80 overflow-hidden relative">
                                    @if($sTop->avatar_url)
                                        <img src="{{ $sTop->avatar_url }}" alt="{{ $sTop->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span>{{ strtoupper(substr($sTop->user->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <span class="absolute -top-4 px-3 py-1 rounded-full bg-amber-400 text-amber-950 font-black text-xs shadow-lg border-2 border-white flex items-center gap-1.5">
                                    👑 🥇 #1 TOP SMK / VOKASI
                                </span>
                            </div>
                            <div class="w-full bg-white/15 backdrop-blur-md border-2 border-amber-400/40 rounded-2xl p-6 text-center shadow-2xl relative overflow-hidden">
                                <div class="absolute top-0 inset-x-0 h-1 bg-amber-400"></div>
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-amber-400/20 text-amber-300 font-extrabold text-[10px] uppercase tracking-wider mb-1">
                                    🏆 Juara Kesiapan Kerja Tingkat SMK
                                </span>
                                <h3 class="text-base sm:text-lg font-black text-white truncate" title="{{ $sTop->user->name }}">
                                    {{ $sTop->user->name }}
                                </h3>
                                <p class="text-xs text-amber-100 font-medium truncate mt-0.5">
                                    {{ $sTop->prodi ?? 'Konsentrasi Keahlian SMK' }} • {{ $sTop->semester ?? 'Siswa SMK' }}
                                </p>
                                
                                <div class="mt-4 flex items-center justify-center gap-2">
                                    <span class="px-4 py-1.5 rounded-xl bg-amber-400 text-amber-950 font-black text-lg shadow-md">
                                        {{ round($sTop->employability_score) }} <span class="text-xs font-bold text-amber-900">SKOR</span>
                                    </span>
                                    @if($sTop->ipk)
                                        <span class="px-2.5 py-1.5 rounded-lg bg-white/20 text-white font-bold text-xs border border-white/20">
                                            Nilai {{ number_format((float)$sTop->ipk, 2) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.students.show', $sTop->user) }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-white text-[#0B1B3D] hover:bg-amber-50 shadow-md transition font-extrabold">
                                        Buka Profil Siswa
                                    </a>
                                    @if($sTop->isCvFilled())
                                        <a href="{{ route('admin.students.cv', $sTop->user) }}" target="_blank" class="text-xs font-bold px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-md transition" title="Lihat CV ATS (Aktif)">
                                            CV ATS
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-xs text-slate-300">Belum ada data siswa SMK yang menyelesaikan asesmen AI.</p>
                </div>
            @endif
        </div>

    </div>

    <!-- Main Two-Column Leaderboard: Mitra Industri & Talenta (Mahasiswa / SMK) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        <!-- KOLOM KIRI: Peringkat Mitra Industri -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg overflow-hidden flex flex-col" x-data="{ tab: 'all' }">
            <!-- Header Mitra -->
            <div class="p-6 bg-emerald-700 text-white relative overflow-hidden">
                <div class="flex items-start justify-between relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">
                                Mitra Industri
                            </span>
                            <span class="text-xs text-emerald-100 font-medium">Rekrutmen & Penyerapan</span>
                        </div>
                        <h2 class="text-lg font-black text-white flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Peringkat Mitra Industri
                        </h2>
                        <p class="text-xs text-emerald-100 mt-1 max-w-md">
                            Diurutkan berdasarkan kontribusi penyerapan pelamar yang disetujui (Diterima) serta keaktifan lowongan kerja/magang.
                        </p>
                    </div>
                </div>

                <!-- Tabs Filter Mitra -->
                <div class="flex items-center gap-2 mt-5 relative z-10">
                    <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-white text-emerald-800 font-bold shadow' : 'bg-white/15 text-white hover:bg-white/25'" class="px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5">
                        <span>Semua Mitra Aktif</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="tab === 'all' ? 'bg-emerald-100 text-emerald-800' : 'bg-white/20 text-white'">{{ $topMitra->count() }}</span>
                    </button>
                    <button @click="tab = 'hired'" :class="tab === 'hired' ? 'bg-white text-emerald-800 font-bold shadow' : 'bg-white/15 text-white hover:bg-white/25'" class="px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5">
                        <span>Telah Merekrut</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="tab === 'hired' ? 'bg-emerald-100 text-emerald-800' : 'bg-white/20 text-white'">{{ $topMitra->where('accepted_students_count', '>', 0)->count() }}</span>
                    </button>
                </div>
            </div>

            <!-- List Mitra -->
            <div class="p-6 divide-y divide-slate-100">
                @forelse ($topMitra as $index => $mitra)
                    <div x-show="tab === 'all' || (tab === 'hired' && {{ $mitra->accepted_students_count }} > 0)" 
                         class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group transition hover:bg-slate-50/70 p-3 rounded-2xl">
                        
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Rank Badge -->
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 shadow-sm
                                {{ $index === 0 ? 'bg-amber-500 text-white shadow-amber-500/20' : 
                                   ($index === 1 ? 'bg-slate-400 text-white' : 
                                   ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500 border border-slate-200')) }}">
                                #{{ $index + 1 }}
                            </div>

                            <!-- Avatar / Initial -->
                            <div class="w-11 h-11 rounded-xl bg-[#004AAD] text-white font-extrabold flex items-center justify-center text-sm shrink-0 shadow-sm">
                                {{ strtoupper(substr($mitra->name, 0, 2)) }}
                            </div>

                            <!-- Detail Mitra -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-extrabold text-[#0B1B3D] truncate group-hover:text-[#004AAD] transition">
                                        <a href="{{ route('admin.mitra.show', $mitra) }}" class="hover:underline">
                                            {{ $mitra->name }}
                                        </a>
                                    </h3>
                                    @if($mitra->accepted_students_count > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold shrink-0">
                                            Merekrut
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 truncate mt-0.5">{{ $mitra->email }}</p>
                                
                                <!-- Sub Stats -->
                                <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-600">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $mitra->active_jobs_count }} Lowongan
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        {{ $mitra->total_applications_count }} Pelamar
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Stats & Actions -->
                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <div class="text-left sm:text-right">
                                <div class="flex items-baseline sm:justify-end gap-1">
                                    <span class="text-xl font-black {{ $mitra->accepted_students_count > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $mitra->accepted_students_count }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-600">Talenta</span>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wider {{ $mitra->accepted_students_count > 0 ? 'text-emerald-700' : 'text-slate-500' }}">
                                    Diterima Kerja
                                </span>
                            </div>
                            <a href="{{ route('admin.mitra.show', $mitra) }}" class="p-2 text-slate-400 hover:text-[#004AAD] hover:bg-slate-100 rounded-xl transition" title="Lihat detail mitra">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-14 h-14 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center mb-3 text-slate-400">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-700">Belum ada mitra terdaftar</h4>
                        <p class="text-xs text-slate-400 mt-1">Data kemitraan industri akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Footer Card Mitra -->
            <div class="mt-auto p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Total {{ $topMitra->count() }} mitra terhubung di ekosistem</span>
                <a href="{{ route('admin.mitra') }}" class="font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                    <span>Lihat Direktori Mitra</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- KOLOM KANAN: Peringkat Talenta (Dibedakan Tingkat Mahasiswa & SMK) -->
        @php
            $prodisMahasiswa = $topMahasiswa->pluck('prodi')->filter()->unique()->values();
            $prodisSmk = $topSmk->pluck('prodi')->filter()->unique()->values();
            $prodisAll = $topStudents->pluck('prodi')->filter()->unique()->values();
        @endphp
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg overflow-hidden flex flex-col" 
             x-data="{ 
                currentLevel: 'mahasiswa', // 'mahasiswa', 'smk', 'all'
                search: '', 
                selectedProdi: 'all',
                matches(name, prodi) {
                    const matchSearch = this.search === '' || name.toLowerCase().includes(this.search.toLowerCase()) || (prodi && prodi.toLowerCase().includes(this.search.toLowerCase()));
                    const matchProdi = this.selectedProdi === 'all' || prodi === this.selectedProdi;
                    return matchSearch && matchProdi;
                }
             }">
            
            <!-- Header Talenta -->
            <div class="p-6 bg-[#004AAD] text-white relative overflow-hidden">
                <div class="flex items-start justify-between relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">
                                Evaluasi Kesiapan Kerja
                            </span>
                            <span class="text-xs text-indigo-100 font-medium">Standar AI Industri</span>
                        </div>
                        <h2 class="text-lg font-black text-white flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                            Peringkat Mahasiswa Top Skor & Siswa SMK
                        </h2>
                        <p class="text-xs text-indigo-100 mt-1 max-w-md">
                            Perengkingan dipisahkan berdasarkan jenjang pendidikan (Mahasiswa PT & Siswa SMK) sesuai kualifikasi standar industri.
                        </p>
                    </div>
                </div>

                <!-- LEVEL SWITCHER TABS (Mahasiswa vs SMK vs Semua) -->
                <div class="grid grid-cols-3 gap-1.5 p-1 bg-black/25 backdrop-blur-md rounded-2xl mt-4 border border-white/10">
                    <button type="button" @click="currentLevel = 'mahasiswa'; selectedProdi = 'all'" 
                            :class="currentLevel === 'mahasiswa' ? 'bg-white text-[#004AAD] font-black shadow-md' : 'text-white/80 hover:text-white font-semibold'" 
                            class="py-2 px-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                        <span>🎓 Mahasiswa (PT)</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="currentLevel === 'mahasiswa' ? 'bg-blue-100 text-[#004AAD]' : 'bg-white/20 text-white'">{{ $topMahasiswa->count() }}</span>
                    </button>
                    <button type="button" @click="currentLevel = 'smk'; selectedProdi = 'all'" 
                            :class="currentLevel === 'smk' ? 'bg-white text-amber-800 font-black shadow-md' : 'text-white/80 hover:text-white font-semibold'" 
                            class="py-2 px-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                        <span>🏫 Siswa SMK</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="currentLevel === 'smk' ? 'bg-amber-100 text-amber-800' : 'bg-white/20 text-white'">{{ $topSmk->count() }}</span>
                    </button>
                    <button type="button" @click="currentLevel = 'all'; selectedProdi = 'all'" 
                            :class="currentLevel === 'all' ? 'bg-white text-slate-800 font-black shadow-md' : 'text-white/80 hover:text-white font-semibold'" 
                            class="py-2 px-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                        <span>🌐 Semua</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="currentLevel === 'all' ? 'bg-slate-200 text-slate-800' : 'bg-white/20 text-white'">{{ $topStudents->count() }}</span>
                    </button>
                </div>

                <!-- Live Search Bar -->
                <div class="mt-4 space-y-2.5 relative z-10">
                    <div class="relative">
                        <svg class="w-4 h-4 text-white/60 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" x-model="search" placeholder="Cari nama talenta atau program studi..." 
                               class="w-full pl-9 pr-4 py-2 bg-white/15 hover:bg-white/20 focus:bg-white focus:text-slate-800 focus:placeholder-slate-400 placeholder-white/60 text-white rounded-xl text-xs border border-white/25 focus:outline-none focus:ring-2 focus:ring-white/50 transition">
                    </div>

                    <!-- Prodi Filter Pills Dinamis -->
                    <!-- Prodi Mahasiswa Pills -->
                    <div x-show="currentLevel === 'mahasiswa' && {{ $prodisMahasiswa->count() }} > 1" class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-[11px]">
                        <button @click="selectedProdi = 'all'" :class="selectedProdi === 'all' ? 'bg-white text-indigo-900 font-bold' : 'bg-white/10 text-white hover:bg-white/20'" class="px-2.5 py-1 rounded-lg shrink-0 transition">
                            Semua Prodi PT ({{ $topMahasiswa->count() }})
                        </button>
                        @foreach ($prodisMahasiswa as $p)
                            <button @click="selectedProdi = '{{ $p }}'" :class="selectedProdi === '{{ $p }}' ? 'bg-white text-indigo-900 font-bold' : 'bg-white/10 text-white hover:bg-white/20'" class="px-2.5 py-1 rounded-lg shrink-0 transition">
                                {{ $p }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Prodi SMK Pills -->
                    <div x-show="currentLevel === 'smk' && {{ $prodisSmk->count() }} > 1" class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-[11px]" style="display: none;">
                        <button @click="selectedProdi = 'all'" :class="selectedProdi === 'all' ? 'bg-white text-amber-900 font-bold' : 'bg-white/10 text-white hover:bg-white/20'" class="px-2.5 py-1 rounded-lg shrink-0 transition">
                            Semua Keahlian SMK ({{ $topSmk->count() }})
                        </button>
                        @foreach ($prodisSmk as $p)
                            <button @click="selectedProdi = '{{ $p }}'" :class="selectedProdi === '{{ $p }}' ? 'bg-white text-amber-900 font-bold' : 'bg-white/10 text-white hover:bg-white/20'" class="px-2.5 py-1 rounded-lg shrink-0 transition">
                                {{ $p }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Prodi Semua Pills -->
                    <div x-show="currentLevel === 'all' && {{ $prodisAll->count() }} > 1" class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-[11px]" style="display: none;">
                        <button @click="selectedProdi = 'all'" :class="selectedProdi === 'all' ? 'bg-white text-slate-900 font-bold' : 'bg-white/10 text-white hover:bg-white/20'" class="px-2.5 py-1 rounded-lg shrink-0 transition">
                            Semua Jurusan ({{ $topStudents->count() }})
                        </button>
                        @foreach ($prodisAll as $p)
                            <button @click="selectedProdi = '{{ $p }}'" :class="selectedProdi === '{{ $p }}' ? 'bg-white text-slate-900 font-bold' : 'bg-white/10 text-white hover:bg-white/20'" class="px-2.5 py-1 rounded-lg shrink-0 transition">
                                {{ $p }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- List Peringkat Talenta -->
            <div class="p-6 divide-y divide-slate-100 max-h-[640px] overflow-y-auto">
                
                <!-- TAB 1: LIST MAHASISWA (PERGURUAN TINGGI) -->
                <div x-show="currentLevel === 'mahasiswa'" class="divide-y divide-slate-100">
                    @forelse ($topMahasiswa as $index => $student_profile)
                        @php
                            $score = round($student_profile->employability_score);
                            $tierName = $score >= 75 ? 'Sangat Unggul' : ($score >= 60 ? 'Kompeten' : ($score >= 45 ? 'Berkembang' : 'Tahap Dasar'));
                            $tierColor = $score >= 75 ? 'emerald' : ($score >= 60 ? 'blue' : ($score >= 45 ? 'amber' : 'slate'));
                        @endphp
                        <div x-show="matches('{{ addslashes($student_profile->user->name) }}', '{{ addslashes($student_profile->prodi ?? '') }}')"
                             class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group transition hover:bg-slate-50/70 p-3 rounded-2xl">
                            
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Rank Badge Mahasiswa -->
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 shadow-sm
                                    {{ $index === 0 ? 'bg-amber-500 text-white shadow-amber-500/20' : 
                                       ($index === 1 ? 'bg-slate-400 text-white' : 
                                       ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500 border border-slate-200')) }}">
                                    #{{ $index + 1 }}
                                </div>

                                <!-- Avatar -->
                                <div class="w-11 h-11 rounded-xl bg-[#004AAD] text-white font-extrabold flex items-center justify-center text-sm shrink-0 shadow-sm overflow-hidden">
                                    @if($student_profile->avatar_url)
                                        <img src="{{ $student_profile->avatar_url }}" alt="{{ $student_profile->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($student_profile->user->name, 0, 2)) }}
                                    @endif
                                </div>

                                <!-- Detail Mahasiswa -->
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-extrabold text-[#0B1B3D] truncate group-hover:text-[#004AAD] transition">
                                            <a href="{{ route('admin.students.show', $student_profile->user) }}" class="hover:underline">
                                                {{ $student_profile->user->name }}
                                            </a>
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 text-[#004AAD] border border-blue-200 shrink-0">
                                            🎓 Mahasiswa (PT)
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0
                                            {{ $tierColor === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                               ($tierColor === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                               ($tierColor === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-50 text-slate-600 border border-slate-200')) }}">
                                            {{ $tierName }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 truncate mt-0.5">
                                        {{ $student_profile->prodi ?? 'Program Studi Belum Diisi' }}
                                        @if($student_profile->institution_name)
                                            • {{ $student_profile->institution_name }}
                                        @endif
                                    </p>
                                    
                                    <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-600">
                                        @if($student_profile->ipk)
                                            <span class="font-bold text-slate-700">{{ $student_profile->ipk > 4 ? 'Nilai' : 'IPK' }} {{ number_format((float)$student_profile->ipk, 2) }}</span>
                                            <span class="text-slate-300">•</span>
                                        @endif
                                        <span>{{ $student_profile->semester ?? 'Mahasiswa Aktif' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Skor & Bar -->
                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <div class="text-left sm:text-right min-w-[70px]">
                                    <div class="flex items-baseline sm:justify-end gap-1">
                                        <span class="text-2xl font-black {{ $index === 0 ? 'text-amber-600' : 'text-[#004AAD]' }}">
                                            {{ $score }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-500">/ 100</span>
                                    </div>
                                    <div class="w-16 sm:w-20 bg-slate-100 rounded-full h-1.5 mt-1 sm:ml-auto overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $index === 0 ? 'bg-amber-500' : 'bg-[#004AAD]' }}" style="width: {{ min($score, 100) }}%"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.students.show', $student_profile->user) }}" class="p-2 text-slate-400 hover:text-[#004AAD] hover:bg-slate-100 rounded-xl transition" title="Profil Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @if($student_profile->isCvFilled())
                                        <a href="{{ route('admin.students.cv', $student_profile->user) }}" target="_blank" class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl transition" title="Lihat CV ATS (Aktif)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-10">
                            <h4 class="text-sm font-bold text-slate-700">Belum ada mahasiswa PT terasesmen</h4>
                            <p class="text-xs text-slate-400 mt-1">Mahasiswa yang telah mengikuti asesmen AI akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>

                <!-- TAB 2: LIST SISWA / LULUSAN SMK -->
                <div x-show="currentLevel === 'smk'" class="divide-y divide-slate-100" style="display: none;">
                    @forelse ($topSmk as $index => $student_profile)
                        @php
                            $score = round($student_profile->employability_score);
                            $tierName = $score >= 75 ? 'Sangat Unggul' : ($score >= 60 ? 'Kompeten' : ($score >= 45 ? 'Berkembang' : 'Tahap Dasar'));
                            $tierColor = $score >= 75 ? 'emerald' : ($score >= 60 ? 'blue' : ($score >= 45 ? 'amber' : 'slate'));
                        @endphp
                        <div x-show="matches('{{ addslashes($student_profile->user->name) }}', '{{ addslashes($student_profile->prodi ?? '') }}')"
                             class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group transition hover:bg-amber-50/40 p-3 rounded-2xl">
                            
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Rank Badge SMK -->
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 shadow-sm
                                    {{ $index === 0 ? 'bg-amber-500 text-white shadow-amber-500/20' : 
                                       ($index === 1 ? 'bg-slate-400 text-white' : 
                                       ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500 border border-slate-200')) }}">
                                    #{{ $index + 1 }}
                                </div>

                                <!-- Avatar -->
                                <div class="w-11 h-11 rounded-xl bg-amber-600 text-white font-extrabold flex items-center justify-center text-sm shrink-0 shadow-sm overflow-hidden">
                                    @if($student_profile->avatar_url)
                                        <img src="{{ $student_profile->avatar_url }}" alt="{{ $student_profile->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($student_profile->user->name, 0, 2)) }}
                                    @endif
                                </div>

                                <!-- Detail Siswa SMK -->
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-extrabold text-[#0B1B3D] truncate group-hover:text-amber-700 transition">
                                            <a href="{{ route('admin.students.show', $student_profile->user) }}" class="hover:underline">
                                                {{ $student_profile->user->name }}
                                            </a>
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                            🏫 Siswa / Lulusan SMK
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0
                                            {{ $tierColor === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                               ($tierColor === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                               ($tierColor === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-50 text-slate-600 border border-slate-200')) }}">
                                            {{ $tierName }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 truncate mt-0.5">
                                        {{ $student_profile->prodi ?? 'Konsentrasi Keahlian SMK' }}
                                        @if($student_profile->institution_name)
                                            • {{ $student_profile->institution_name }}
                                        @endif
                                    </p>
                                    
                                    <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-600">
                                        @if($student_profile->ipk)
                                            <span class="font-bold text-slate-700">Nilai {{ number_format((float)$student_profile->ipk, 2) }}</span>
                                            <span class="text-slate-300">•</span>
                                        @endif
                                        <span>{{ $student_profile->semester ?? 'Siswa SMK/SMA' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Skor & Bar -->
                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <div class="text-left sm:text-right min-w-[70px]">
                                    <div class="flex items-baseline sm:justify-end gap-1">
                                        <span class="text-2xl font-black {{ $index === 0 ? 'text-amber-600' : 'text-amber-700' }}">
                                            {{ $score }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-500">/ 100</span>
                                    </div>
                                    <div class="w-16 sm:w-20 bg-slate-100 rounded-full h-1.5 mt-1 sm:ml-auto overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $index === 0 ? 'bg-amber-500' : 'bg-amber-600' }}" style="width: {{ min($score, 100) }}%"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.students.show', $student_profile->user) }}" class="p-2 text-slate-400 hover:text-amber-700 hover:bg-amber-50 rounded-xl transition" title="Profil Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @if($student_profile->isCvFilled())
                                        <a href="{{ route('admin.students.cv', $student_profile->user) }}" target="_blank" class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl transition" title="Lihat CV ATS (Aktif)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-10">
                            <h4 class="text-sm font-bold text-slate-700">Belum ada siswa SMK terasesmen</h4>
                            <p class="text-xs text-slate-400 mt-1">Siswa SMK yang telah mengikuti asesmen AI akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>

                <!-- TAB 3: LIST SEMUA (GABUNGAN) -->
                <div x-show="currentLevel === 'all'" class="divide-y divide-slate-100" style="display: none;">
                    @forelse ($topStudents as $index => $student_profile)
                        @php
                            $score = round($student_profile->employability_score);
                            $tierName = $score >= 75 ? 'Sangat Unggul' : ($score >= 60 ? 'Kompeten' : ($score >= 45 ? 'Berkembang' : 'Tahap Dasar'));
                            $tierColor = $score >= 75 ? 'emerald' : ($score >= 60 ? 'blue' : ($score >= 45 ? 'amber' : 'slate'));
                            $isSmk = $student_profile->isSmk();
                        @endphp
                        <div x-show="matches('{{ addslashes($student_profile->user->name) }}', '{{ addslashes($student_profile->prodi ?? '') }}')"
                             class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group transition hover:bg-slate-50/70 p-3 rounded-2xl">
                            
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- Rank Badge Overall -->
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 shadow-sm
                                    {{ $index === 0 ? 'bg-amber-500 text-white shadow-amber-500/20' : 
                                       ($index === 1 ? 'bg-slate-400 text-white' : 
                                       ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500 border border-slate-200')) }}">
                                    #{{ $index + 1 }}
                                </div>

                                <!-- Avatar -->
                                <div class="w-11 h-11 rounded-xl {{ $isSmk ? 'bg-amber-600' : 'bg-[#004AAD]' }} text-white font-extrabold flex items-center justify-center text-sm shrink-0 shadow-sm overflow-hidden">
                                    @if($student_profile->avatar_url)
                                        <img src="{{ $student_profile->avatar_url }}" alt="{{ $student_profile->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($student_profile->user->name, 0, 2)) }}
                                    @endif
                                </div>

                                <!-- Detail Talenta -->
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-extrabold text-[#0B1B3D] truncate group-hover:text-[#004AAD] transition">
                                            <a href="{{ route('admin.students.show', $student_profile->user) }}" class="hover:underline">
                                                {{ $student_profile->user->name }}
                                            </a>
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold {{ $isSmk ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-blue-50 text-[#004AAD] border border-blue-200' }} shrink-0">
                                            {{ $isSmk ? '🏫 SMK' : '🎓 Mahasiswa' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0
                                            {{ $tierColor === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                               ($tierColor === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                               ($tierColor === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-50 text-slate-600 border border-slate-200')) }}">
                                            {{ $tierName }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 truncate mt-0.5">
                                        {{ $student_profile->prodi ?? 'Program Keahlian Belum Diisi' }}
                                    </p>
                                    
                                    <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-600">
                                        @if($student_profile->ipk)
                                            <span class="font-bold text-slate-700">{{ $student_profile->ipk > 4 ? 'Nilai' : 'IPK' }} {{ number_format((float)$student_profile->ipk, 2) }}</span>
                                            <span class="text-slate-300">•</span>
                                        @endif
                                        <span>{{ $student_profile->semester ?? 'Aktif' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Skor & Bar -->
                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <div class="text-left sm:text-right min-w-[70px]">
                                    <div class="flex items-baseline sm:justify-end gap-1">
                                        <span class="text-2xl font-black {{ $index === 0 ? 'text-amber-600' : 'text-[#004AAD]' }}">
                                            {{ $score }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-500">/ 100</span>
                                    </div>
                                    <div class="w-16 sm:w-20 bg-slate-100 rounded-full h-1.5 mt-1 sm:ml-auto overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $index === 0 ? 'bg-amber-500' : 'bg-[#004AAD]' }}" style="width: {{ min($score, 100) }}%"></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.students.show', $student_profile->user) }}" class="p-2 text-slate-400 hover:text-[#004AAD] hover:bg-slate-100 rounded-xl transition" title="Profil Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @if($student_profile->isCvFilled())
                                        <a href="{{ route('admin.students.cv', $student_profile->user) }}" target="_blank" class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl transition" title="Lihat CV ATS (Aktif)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-10">
                            <h4 class="text-sm font-bold text-slate-700">Belum ada talenta terasesmen</h4>
                            <p class="text-xs text-slate-400 mt-1">Data talenta yang telah mengikuti asesmen AI akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>

            </div>

            <!-- Footer Card Talenta -->
            <div class="mt-auto p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">
                    <span x-show="currentLevel === 'mahasiswa'">Menampilkan {{ $topMahasiswa->count() }} mahasiswa (PT) terasesmen</span>
                    <span x-show="currentLevel === 'smk'" style="display: none;">Menampilkan {{ $topSmk->count() }} siswa SMK terasesmen</span>
                    <span x-show="currentLevel === 'all'" style="display: none;">Menampilkan {{ $topStudents->count() }} talenta keseluruhan</span>
                </span>
                <a href="{{ route('admin.students') }}" class="font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                    <span>Lihat Semua Talenta</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

    </div>

    <!-- Bidang Paling Diminati (Analytics & Breakdown) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg overflow-hidden">
        <div class="p-6 bg-[#0B1B3D] text-white relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">
                            Analitik Pasar
                        </span>
                        <span class="text-xs text-blue-100 font-medium">Tren Peminatan Karier</span>
                    </div>
                    <h2 class="text-lg font-black text-white flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Peringkat Bidang Pekerjaan Paling Diminati
                    </h2>
                    <p class="text-xs text-blue-100 mt-1 max-w-2xl">
                        Statistik distribusi minat karier talenta berdasarkan volume berkas lamaran pekerjaan yang dikirimkan ke lowongan mitra.
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <span class="px-3.5 py-1.5 rounded-xl bg-white/15 text-white text-xs font-bold border border-white/20 backdrop-blur-sm">
                        {{ $totalApplications }} Total Lamaran Terdaftar
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                @php
                    $maxApp = $popularFields->max('applications_count') ?: 1;
                @endphp
                @forelse ($popularFields as $index => $field)
                    @php
                        $percentage = round(($field->applications_count / $maxApp) * 100);
                        $shareOfTotal = $totalApplications > 0 ? round(($field->applications_count / $totalApplications) * 100, 1) : 0;
                    @endphp
                    <div class="space-y-1.5 p-3 rounded-2xl hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 font-extrabold text-[11px] text-slate-700 flex items-center justify-center">
                                    #{{ $index + 1 }}
                                </span>
                                <span class="font-extrabold text-[#0B1B3D] text-sm">
                                    {{ $field->department ?: 'Umum / Lintas Bidang' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-sm text-[#004AAD]">{{ $field->applications_count }}</span>
                                <span class="text-slate-600 text-[11px]">Lamaran ({{ $shareOfTotal }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-[#004AAD] h-2.5 rounded-full transition-all duration-700" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <p class="text-xs text-slate-400">Belum ada data lamaran pekerjaan masuk untuk dianalisis.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
