<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-[#004AAD] border border-blue-200">Peluang Industri</span>
                <span class="text-xs text-slate-500 font-medium">Kemitraan Perusahaan Terverifikasi</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-[#0B1B3D] tracking-tight">Bursa Lowongan Pekerjaan</h1>
            <p class="text-[13px] text-slate-500 mt-1">Salurkan bakat dan kompetensi Anda langsung menuju lowongan terarah dari mitra industri digital</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-[11.5px] font-bold text-slate-600 shadow-sm">
                T.A. 2025/2026 Ganjil
            </span>
            <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#004AAD]/10 hover:bg-[#004AAD]/20 text-[#004AAD] border border-[#004AAD]/30 text-xs font-bold transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Riwayat Lamaran</span>
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <span class="px-1.5 py-0.2 bg-rose-500 text-white text-[9px] font-black rounded-full animate-pulse">
                        {{ Auth::user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-6 p-4 rounded-xl bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-lg">
            <svg class="w-5 h-5 shrink-0 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 p-4 rounded-xl bg-accent/15 border border-blue-500/30 text-blue-400 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-lg">
            <svg class="w-5 h-5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-600 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-lg">
            <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search Bar -->
    <form method="GET" action="{{ route('jobs') }}" class="bg-white rounded-2xl p-2.5 shadow-xl border border-slate-200 mb-8 flex items-center gap-3">
        <div class="flex-1 flex items-center px-4">
            <svg class="w-5 h-5 text-slate-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi pekerjaan impian Anda, contoh: React Developer, Embedded Engineer, Data Analyst..." class="w-full bg-transparent border-none focus:outline-none focus:ring-0 text-[13.5px] text-[#0B1B3D] placeholder-slate-500 py-2.5">
        </div>
        <button type="submit" class="px-8 py-3 bg-[#004AAD] hover:bg-[#003B8B] text-white text-[13px] font-bold rounded-xl transition shadow-lg shadow-[#004AAD]/20 whitespace-nowrap">
            Cari Lowongan
        </button>
    </form>

    <div class="grid grid-cols-1 gap-8 mb-8">
        <!-- Left Col: Job List -->
        <div class="space-y-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-base font-bold text-[#0B1B3D]">Lowongan Pekerjaan Paling Sesuai</h3>
                <span class="text-xs text-slate-500">Disesuaikan dengan profil & skor asesmen</span>
            </div>
            <div class="space-y-4">
                @forelse($jobs as $job)
                @php
                    $isMainRec = $loop->iteration <= 3 && ($job->match_score >= 60);
                @endphp
                <div id="job-{{ $job->id }}" class="bg-white rounded-2xl p-6 shadow-xl border {{ $isMainRec ? 'border-2 border-[#004AAD] shadow-[#004AAD]/10' : 'border-slate-200' }} flex flex-col gap-5 hover:border-[#004AAD]/50 transition target:border-[#004AAD] target:ring-2 target:ring-[#004AAD] scroll-mt-24 relative overflow-hidden">
                    @if($isMainRec)
                        <div class="absolute top-0 right-0 bg-[#004AAD] text-white text-[10px] font-bold px-3.5 py-1.5 rounded-bl-2xl shadow-sm z-10">
                            ⭐ Rekomendasi Utama
                        </div>
                    @endif
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 relative z-20">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center shrink-0 shadow-sm text-[#004AAD]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <a href="{{ route('jobs.show', $job) }}" class="text-base md:text-lg font-bold text-[#0B1B3D] leading-snug hover:text-[#004AAD] transition">{{ $job->title }}</a>
                                <p class="text-[12.5px] text-slate-500 mt-0.5">{{ $job->user->name ?? 'Mitra Industri' }} &bull; {{ $job->location }} ({{ $job->type }})</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:items-end">
                            @php
                                $score = $job->match_score ?? 0;
                            @endphp
                            
                            @if($score >= 80)
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-[#004AAD]/15 text-[#004AAD] font-bold text-[10px] border border-[#004AAD]/30">Top Match &bull; {{ $score }}%</span>
                            @elseif($score >= 60)
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-accent/15 text-sky-700 font-bold text-[10px] border border-sky-500/30">Sesuai &bull; {{ $score }}%</span>
                            @elseif($score >= 40)
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-700 font-bold text-[10px] border border-amber-500/30">Potensial &bull; {{ $score }}%</span>
                            @else
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px] border border-slate-200">Tidak Sesuai &bull; {{ $score }}%</span>
                            @endif
                            <span class="text-[10px] text-slate-500 mt-1 font-medium">{{ $job->duration }}</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->skills ?? [] as $skill)
                            <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-medium text-slate-700">{{ $skill }}</span>
                        @endforeach
                    </div>
                    
                    <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        @php
                            $userApp = $userApplications->get($job->id) ?? null;
                        @endphp

                        <p class="text-[12px] text-slate-500 line-clamp-1">
                            @if($userApp)
                                @if($userApp->status === 'Diterima')
                                    <span class="font-bold text-[#004AAD]">Diterima!</span>
                                @elseif($userApp->status === 'Ditolak')
                                    <span class="font-bold text-rose-600">Belum Lolos</span>
                                @else
                                    <span class="font-bold text-amber-700">Sedang Direview</span>
                                @endif
                            @else
                                <span class="font-bold text-[#004AAD]">Tersedia:</span> Menerima pelamar
                            @endif
                        </p>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$userApp)
                                <a href="{{ route('jobs.show', $job) }}" class="px-5 py-2 bg-[#004AAD] hover:bg-[#083577] text-white text-[12px] font-bold rounded-xl transition shadow-sm">
                                    Lihat Detail
                                </a>
                            @else
                                <a href="{{ route('jobs.show', $job) }}" class="px-5 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-[#004AAD] text-[12px] font-bold rounded-xl transition">
                                    Lihat Status
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-slate-200 text-center text-slate-500">
                    Belum ada lowongan pekerjaan saat ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-student-layout>




