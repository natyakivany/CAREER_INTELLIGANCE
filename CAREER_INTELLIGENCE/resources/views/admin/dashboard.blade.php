@extends('layouts.admin')

@section('header')
    <div>
        <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
            <span>Pusat Kendali Administrator Kampus</span>
            <span class="px-2.5 py-0.5 text-[10px] font-bold bg-[#004AAD] text-white rounded-full shadow-sm tracking-wide uppercase px-3 py-1">Live Data</span>
        </h1>
        <p class="text-xs text-slate-600 font-medium mt-0.5">
            Pantau statistik kesiapan kerja mahasiswa, portofolio proyek terverifikasi, dan kemitraan industri.
        </p>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Total Mahasiswa -->
        <div class="bg-white p-5 rounded-2xl border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 shadow-md relative overflow-hidden flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-600 font-medium">Total Mahasiswa</span>
                <div class="w-10 h-10 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-[#0B1B3D] tracking-tight">{{ $totalStudents }}</p>
                <p class="text-[11px] text-slate-600 font-medium mt-1">Akun aktif terdaftar</p>
            </div>
        </div>

        <!-- Total Mitra -->
        <div class="bg-white p-5 rounded-2xl border-t-4 border-t-indigo-600 border-x border-b border-slate-200 shadow-md relative overflow-hidden flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-600 font-medium">Mitra Industri</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-[#0B1B3D] tracking-tight">{{ $totalMitra }}</p>
                <p class="text-[11px] text-slate-600 font-medium mt-1">Perusahaan terhubung</p>
            </div>
        </div>

        <!-- Total Portofolio -->
        <div class="bg-white p-5 rounded-2xl border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 shadow-md relative overflow-hidden flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-600 font-medium">Portofolio Proyek</span>
                <div class="w-10 h-10 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-[#0B1B3D] tracking-tight">{{ $totalPortfolios }}</p>
                <p class="text-[11px] text-slate-600 font-medium mt-1">Bukti riil diunggah</p>
            </div>
        </div>

        <!-- Total Asesmen -->
        <div class="bg-white p-5 rounded-2xl border-t-4 border-t-purple-600 border-x border-b border-slate-200 shadow-md relative overflow-hidden flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-600 font-medium">Asesmen Selesai</span>
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-md shadow-purple-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-[#0B1B3D] tracking-tight">{{ $totalAssessments }}</p>
                <p class="text-[11px] text-slate-600 font-medium mt-1">Uji kompetensi studi kasus</p>
            </div>
        </div>

        <!-- Rata-rata Skor Employability -->
        <div class="bg-white p-5 rounded-2xl border-t-4 border-t-emerald-600 border-x border-b border-slate-200 shadow-md relative overflow-hidden flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-600 font-medium">Rata-rata Skor</span>
                <div class="w-10 h-10 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-1.5">
                    <p class="text-2xl font-black text-[#004AAD] tracking-tight">{{ $avgScore }}</p>
                    <span class="text-xs text-slate-400 font-extrabold">/ 100</span>
                </div>
                <p class="text-[11px] text-slate-600 font-medium mt-1">Indeks Kesiapan Kerja</p>
            </div>
        </div>

    </div>

    <!-- Jurusan Distribution Bar -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-sm font-extrabold text-[#0B1B3D] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Distribusi Minat Bidang Mahasiswa
                </h2>
                <p class="text-xs text-slate-600 font-medium mt-0.5">Proporsi fokus bidang mahasiswa antara Teknologi Informasi dan Teknik Elektro</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#004AAD]"></span>
                    <span class="text-slate-600 font-semibold">Teknologi Informasi: {{ $totalIt }} Mahasiswa</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span> <span class="text-slate-700 font-semibold">Teknik Elektro: {{ $totalElektro }} Mahasiswa</span>
                </div>
            </div>
        </div>

        @php
            $totalDist = max(1, $totalIt + $totalElektro);
            $itPercent = round(($totalIt / $totalDist) * 100);
            $elektroPercent = 100 - $itPercent;
        @endphp

        <div class="w-full bg-slate-200 border border-slate-300 h-4 rounded-full overflow-hidden flex p-0.5 shadow-inner">
            <div class="bg-[#004AAD] h-full rounded-l-full transition-all duration-500" style="width: {{ $itPercent }}%"></div>
            <div class="bg-cyan-500 h-full transition-all duration-500" style="width: {{ $elektroPercent }}%"></div>
        </div>
        <div class="flex justify-between text-[11px] text-slate-600 font-medium mt-2">
            <span>TI: {{ $itPercent }}%</span>
            <span>Elektro: {{ $elektroPercent }}%</span>
        </div>
    </div>

    <!-- Two Columns: Recent Students & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Table: Recent Students -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-md p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#0B1B3D] flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Mahasiswa Terdaftar Terbaru
                        </h2>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">Daftar mahasiswa yang baru saja mendaftar atau melengkapi profil</p>
                    </div>
                    <a href="{{ route('admin.students') }}" class="text-xs font-semibold text-[#004AAD] hover:underline flex items-center gap-1">
                        <span>Kelola Semua</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="text-[11px] uppercase bg-slate-50 text-[#004AAD] font-extrabold border-y border-slate-300">
                            <tr>
                                <th class="py-3 px-4">Mahasiswa</th>
                                <th class="py-3 px-3">Prodi / Jurusan</th>
                                <th class="py-3 px-3 text-center">Portofolio</th>
                                <th class="py-3 px-3 text-center">Skor Kesiapan</th>
                                <th class="py-3 px-3 text-right">Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse ($recentStudents as $student)
                                <tr class="hover:bg-blue-50/40 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-[#004AAD] text-white font-black flex items-center justify-center text-xs shrink-0 shadow-md shadow-[#004AAD]/20">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-[#0B1B3D]">{{ $student->name }}</p>
                                                <p class="text-[10px] text-slate-600 font-medium">NIM: {{ $student->nim ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-medium text-[#0B1B3D]">{{ $student->studentProfile?->prodi ?? 'Belum Diisi' }}</p>
                                        <span class="text-[10px] text-slate-600 font-medium uppercase font-semibold">
                                            {{ $student->studentProfile?->target_field === 'elektro' ? 'Teknik Elektro' : 'Teknologi Informasi' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="px-3 py-1 rounded-lg text-xs font-black bg-blue-50 text-[#004AAD] border border-blue-200 shadow-sm">
                                            {{ $student->portfolios->count() }} Proyek
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        @php
                                            $score = $student->studentProfile?->employability_score ?? 0;
                                        @endphp
                                        @if ($score > 0)
                                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-[#004AAD] border border-blue-200 shadow-sm">
                                                {{ $score }} / 100
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600 font-medium border border-slate-200">
                                                Belum Tes
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-right text-slate-600 font-medium text-[11px]">
                                        {{ $student->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-600 font-medium">
                                        Belum ada data mahasiswa terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Menampilkan <strong>{{ $recentStudents->count() }}</strong> mahasiswa terdaftar terbaru</span>
                <a href="{{ route('admin.students') }}" class="font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                    Buka Manajemen Mahasiswa &rarr;
                </a>
            </div>
        </div>

        <!-- Right Column: Mitra & Asesmen -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Mitra Industri Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold text-[#0B1B3D] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Mitra Industri Terbaru
                    </h2>
                    <a href="{{ route('admin.mitra') }}" class="text-[11px] font-semibold text-[#004AAD] hover:underline">Semua &rarr;</a>
                </div>

                <div class="space-y-3">
                    @forelse ($recentMitra as $m)
                        <div class="p-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#004AAD] text-white flex items-center justify-center font-black text-xs shadow-md shadow-[#004AAD]/20">
                                    {{ strtoupper(substr($m->name, 0, 2)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-extrabold text-[#0B1B3D] truncate">{{ $m->name }}</p>
                                    <p class="text-[10px] text-slate-600 font-medium truncate">{{ $m->email }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">Aktif</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-600 font-medium text-center py-4">Belum ada mitra industri terdaftar.</p>
                    @endforelse
                </div>
            </div>

            <!-- Asesmen Terbaru Selesai -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold text-[#0B1B3D] flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Uji Asesmen Terkini
                    </h2>
                </div>

                <div class="space-y-3">
                    @forelse ($recentAssessments as $asm)
                        <div class="p-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-extrabold text-[#0B1B3D]">{{ $asm->user?->name ?? 'Mahasiswa' }}</p>
                                <p class="text-[10px] text-slate-600 font-medium">Bidang: {{ strtoupper($asm->field) }} • {{ $asm->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-extrabold text-[#004AAD]">{{ round($asm->total_score) }} / 100</span>
                                <p class="text-[9px] text-slate-600 font-medium">Skor Uji</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-600 font-medium text-center py-4">Belum ada asesmen yang diselesaikan.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Section: Log Aktivitas Terbaru (Full Width) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#004AAD]/10 text-[#004AAD] flex items-center justify-center border border-[#004AAD]/20 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-extrabold text-[#0B1B3D]">Log Aktivitas Sistem Terkini</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#004AAD] border border-blue-200">
                            Live Feed
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Rekam jejak tindakan admin kampus dan mitra industri secara transparan</p>
                </div>
            </div>
            <a href="{{ route('admin.activity-logs') }}" class="px-4 py-2 rounded-xl bg-slate-50 hover:bg-[#004AAD] text-slate-700 hover:text-white text-xs font-bold transition border border-slate-200 flex items-center gap-1.5 self-start sm:self-auto shadow-2xs">
                <span>Buka Seluruh Audit Trail</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        @if(empty($recentActivityLogs) || $recentActivityLogs->isEmpty())
            <div class="py-10 text-center">
                <p class="text-xs text-slate-400">Belum ada aktivitas yang terekam di sistem.</p>
            </div>
        @else
            <div class="space-y-3 pt-4">
                @foreach($recentActivityLogs as $rLog)
                    @php $rBadge = $rLog->action_badge; @endphp
                    <div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/80 hover:bg-blue-50/40 border border-slate-200/80 hover:border-blue-200 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3.5 min-w-0 flex-1">
                            {{-- Role Avatar Icon --}}
                            <div class="w-10 h-10 rounded-xl {{ $rLog->role === 'admin' ? 'bg-[#004AAD] text-white shadow-md shadow-[#004AAD]/20' : 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' }} flex items-center justify-center font-bold text-xs shrink-0 mt-0.5" title="{{ $rLog->role === 'admin' ? 'Aktivitas Administrator' : 'Aktivitas Mitra Industri' }}">
                                @if($rLog->role === 'admin')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $rBadge['bg'] }}">
                                        {{ $rBadge['label'] }}
                                    </span>
                                    <h4 class="text-xs font-bold text-[#0B1B3D] truncate">{{ $rLog->title }}</h4>
                                    <span class="text-slate-300 text-xs hidden sm:inline">•</span>
                                    <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $rLog->role === 'admin' ? 'bg-[#004AAD]' : 'bg-indigo-600' }}"></span>
                                        <span>{{ $rLog->user->name ?? 'Sistem' }}</span>
                                        <span class="text-slate-400 font-normal">({{ $rLog->role === 'admin' ? 'Admin' : 'Mitra' }})</span>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $rLog->description }}</p>
                            </div>
                        </div>

                        {{-- Timestamp --}}
                        <div class="text-left sm:text-right shrink-0 sm:pl-4 sm:border-l sm:border-slate-200">
                            <span class="text-xs font-bold text-[#0B1B3D] block">
                                {{ $rLog->created_at->diffForHumans() }}
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                {{ $rLog->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection






