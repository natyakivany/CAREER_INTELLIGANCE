<x-mitra-layout>
    <div x-data="{ 
        selectedApplicant: null,
        showReviewModal: false,
        openReview(applicant) {
            this.selectedApplicant = applicant;
            this.showReviewModal = true;
        }
    }">

        <!-- Top Header & Action Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Dashboard Mitra Industri</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#004AAD] border border-blue-200 tracking-wide uppercase">Portal Aktif</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Kelola pembukaan lowongan pekerjaan dan evaluasi kesiapan talenta mahasiswa kampus secara objektif.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-600 shadow-sm">
                    📅 T.A. 2025/2026 Ganjil
                </span>
                
                <a href="{{ route('mitra.applications.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 hover:border-[#004AAD]/30 text-xs font-bold text-slate-700 rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Pelamar Masuk</span>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="px-1.5 py-0.2 bg-rose-500 text-white text-[9px] font-bold rounded-full">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('mitra.jobs') }}" class="px-5 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Buka Lowongan Baru</span>
                </a>
            </div>
        </div>

        <!-- 4 Metric Cards (Real Data) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            
            <!-- Stat 1: Lowongan Aktif -->
            <div class="bg-white p-5 rounded-2xl border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 shadow-md flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Lowongan Pekerjaan</p>
                    <span class="px-2 py-0.5 rounded bg-[#004AAD]/15 text-[#004AAD] text-[10px] font-bold border border-[#004AAD]/20">Aktif</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-3xl font-extrabold text-[#0B1B3D]">{{ $openJobCount }} <span class="text-xs font-medium text-slate-500">Posisi</span></h2>
                    <span class="text-[11px] text-[#004AAD] font-semibold">{{ $jobCount }} Total</span>
                </div>
            </div>

            <!-- Stat 2: Mahasiswa Terdaftar -->
            <div class="bg-white p-5 rounded-2xl border-t-4 border-t-indigo-600 border-x border-b border-slate-200 shadow-md flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Mahasiswa</p>
                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-200">Terdaftar</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-3xl font-extrabold text-[#0B1B3D]">{{ $students->count() }} <span class="text-xs font-medium text-slate-500">Mahasiswa</span></h2>
                    <span class="text-[11px] text-slate-500">{{ $studentsWithPortfolio }} berportofolio</span>
                </div>
            </div>

            <!-- Stat 3: Rata-rata Score -->
            <div class="bg-white p-5 rounded-2xl border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 shadow-md flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Rata-rata Skor</p>
                    @if($avgScore >= 70)
                        <span class="px-2 py-0.5 rounded bg-[#004AAD]/15 text-[#004AAD] text-[10px] font-bold border border-[#004AAD]/20">Tinggi</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-700 text-[10px] font-bold border border-amber-500/20">Sedang</span>
                    @endif
                </div>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-3xl font-extrabold {{ $avgScore >= 70 ? 'text-[#004AAD]' : 'text-amber-700' }}">{{ $avgScore }}%</h2>
                    <span class="text-[11px] text-slate-500">Employability</span>
                </div>
            </div>

            <!-- Stat 4: Kandidat Siap -->
            @php
                $readyCandidates = $recentCandidates->where('matchScore', '>=', 70)->count();
            @endphp
            <div class="bg-white p-5 rounded-2xl border-t-4 border-t-emerald-600 border-x border-b border-slate-200 shadow-md flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kandidat Siap</p>
                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-200">≥70%</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-3xl font-extrabold text-[#0B1B3D]">{{ $readyCandidates }} <span class="text-xs font-medium text-slate-500">Kandidat</span></h2>
                    <span class="text-[11px] text-emerald-700 font-bold">Siap Interview</span>
                </div>
            </div>

        </div>

        <!-- Main Content: Job Stats & Candidates -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            <!-- Left: Lowongan Summary -->
            <div class="lg:col-span-5 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200 shadow-xl flex flex-col">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-200">
                    <div>
                        <h2 class="text-sm font-bold text-[#0B1B3D] tracking-tight">Lowongan Anda</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Status semua posisi pekerjaan yang dibuka</p>
                    </div>
                    <a href="{{ route('mitra.jobs') }}" class="text-xs font-bold text-[#004AAD] hover:underline">Kelola &rarr;</a>
                </div>

                @forelse($jobs as $job)
                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3 mb-2.5 hover:border-[#004AAD]/30 transition">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ $job->title }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ $job->department }} • {{ $job->location }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold shrink-0 {{ $job->status === 'Buka' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 'bg-red-500/15 text-red-600 border border-red-500/30' }}">
                            {{ $job->status }}
                        </span>
                    </div>
                @empty
                    <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-[#004AAD] text-white flex items-center justify-center shadow-md mb-3">
                            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-slate-500 text-xs">Belum ada lowongan.</p>
                        <a href="{{ route('mitra.jobs') }}" class="text-[#004AAD] text-xs font-bold mt-2 hover:underline">Buat Sekarang &rarr;</a>
                    </div>
                @endforelse
            </div>

            <!-- Right: Kandidat Rekomendasi Sesuai Lowongan -->
            <div class="lg:col-span-7 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200 shadow-xl flex flex-col">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-[#0B1B3D] tracking-tight">Kandidat Rekomendasi Sesuai Lowongan</h2>
                            @if($openJobCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20">
                                    {{ $openJobCount }} Lowongan Aktif
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Talenta yang paling sesuai dengan kualifikasi & kebutuhan lowongan buka Anda</p>
                    </div>
                    <a href="{{ route('mitra.candidates') }}" class="text-[11px] font-bold text-slate-500 hover:text-[#004AAD] transition">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($openJobCount === 0)
                    <div class="mb-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Perusahaan Anda belum membuka lowongan aktif. Buka lowongan untuk rekomendasi presisi AI.</span>
                        </div>
                        <a href="{{ route('mitra.jobs') }}" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-[11px] transition shrink-0 text-center">
                            + Buka Lowongan
                        </a>
                    </div>
                @endif

                @if($recentCandidates->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($recentCandidates as $candidate)
                            <div class="p-3.5 rounded-xl bg-white border border-slate-200 hover:border-[#004AAD]/40 transition flex items-center justify-between gap-3 group cursor-pointer"
                                 @click="openReview({{ json_encode($candidate) }})">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-[#004AAD] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow overflow-hidden">
                                        @if(!empty($candidate['avatarUrl']))
                                            <img src="{{ $candidate['avatarUrl'] }}" alt="{{ $candidate['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            {{ collect(explode(' ', $candidate['name']))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-xs font-bold text-[#0B1B3D] truncate group-hover:text-[#004AAD] transition">{{ $candidate['name'] }}</p>
                                            @if(!empty($candidate['recommendedJob']))
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20 truncate" title="{{ $candidate['recommendedJob']['job_title'] }}">
                                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                    <span class="truncate max-w-[130px]">{{ $candidate['recommendedJob']['job_title'] }}</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500 truncate">
                                            <span class="truncate">{{ $candidate['role'] }} • {{ $candidate['prodi'] }}</span>
                                            @if(!empty($candidate['assessmentDate']))
                                                <span class="shrink-0 text-slate-400">&bull;</span>
                                                <span class="shrink-0 text-[10.5px] font-medium text-[#004AAD] flex items-center gap-1" title="Tanggal Asesmen AI Terbaru: {{ $candidate['assessmentDate'] }}">
                                                    <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                                    Asesmen: {{ $candidate['assessmentDate'] }}
                                                </span>
                                            @endif
                                        </div>
                                        @if(!empty($candidate['recommendedJob']['matched_skills']))
                                            <div class="flex items-center gap-1 mt-1 flex-wrap">
                                                <span class="text-[9px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                    {{ $candidate['recommendedJob']['matched_skills_count'] }} Skill Cocok
                                                </span>
                                                @foreach(array_slice($candidate['recommendedJob']['matched_skills'], 0, 2) as $mskill)
                                                    <span class="text-[9px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 truncate max-w-[110px]">{{ $mskill }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-right hidden sm:block">
                                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Kecocokan</p>
                                        <p class="text-xs font-extrabold {{ $candidate['matchScore'] >= 70 ? 'text-[#004AAD] font-black' : 'text-slate-600 font-bold' }}">{{ $candidate['matchScore'] }}%</p>
                                    </div>
                                    
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $candidate['status'] === 'Siap Kerja' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $candidate['status'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-[#004AAD] text-white flex items-center justify-center shadow-md mb-3">
                            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <p class="text-slate-500 text-xs">Belum ada kandidat mahasiswa terdaftar.</p>
                        <p class="text-slate-500 text-[11px] mt-1">Kandidat akan muncul setelah mahasiswa melengkapi profil dan portofolio.</p>
                    </div>
                @endif

                <div class="mt-auto pt-3 border-t border-slate-200 text-center">
                    <a href="{{ route('mitra.candidates') }}" class="text-xs font-bold text-[#004AAD] hover:underline">
                        Cari Semua Kandidat Sesuai Lowongan &rarr;
                    </a>
                </div>
            </div>

        </div>

        {{-- Section: Log Aktivitas Terbaru --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#004AAD] flex items-center justify-center border border-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-[#0B1B3D] text-sm sm:text-base">Log Aktivitas Terbaru</h3>
                        <p class="text-[11px] text-slate-500">Riwayat aksi operasional dan perubahan status terkini</p>
                    </div>
                </div>
                <a href="{{ route('mitra.activity-logs') }}" class="text-xs font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                    Lihat Semua Log &rarr;
                </a>
            </div>

            @if(empty($recentActivityLogs) || $recentActivityLogs->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada aktivitas terekam.</p>
            @else
                <div class="divide-y divide-slate-100 pt-1">
                    @foreach($recentActivityLogs as $rLog)
                        @php $rBadge = $rLog->action_badge; @endphp
                        <div class="py-3 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $rBadge['bg'] }} shrink-0 mt-0.5">
                                    {{ $rBadge['label'] }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ $rLog->title }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $rLog->description }}</p>
                                </div>
                            </div>
                            <span class="text-[10.5px] text-slate-400 shrink-0 font-medium">
                                {{ $rLog->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Review Candidate Modal -->
        <div x-show="showReviewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" @click="showReviewModal = false"></div>

                <div class="relative bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 text-left shadow-2xl transform transition-all sm:max-w-2xl sm:w-full space-y-6">
                    
                    <!-- Modal Header -->
                    <div class="flex justify-between items-start pb-4 border-b border-slate-200">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-[#004AAD] text-white font-bold text-base flex items-center justify-center shadow-lg overflow-hidden shrink-0">
                                <template x-if="selectedApplicant?.avatarUrl">
                                    <img :src="selectedApplicant.avatarUrl" :alt="selectedApplicant?.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!selectedApplicant?.avatarUrl">
                                    <span x-text="selectedApplicant?.name ? selectedApplicant.name.split(' ').map(n => n[0]).slice(0,2).join('') : 'MA'"></span>
                                </template>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-[#0B1B3D]" x-text="selectedApplicant?.name"></h3>
                                <p class="text-xs text-slate-500" x-text="selectedApplicant?.prodi"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] bg-slate-100 text-[#0B1B3D] px-2 py-0.5 rounded border border-slate-300" x-text="'NIM: ' + (selectedApplicant?.nim || '-')"></span>
                                    <span class="text-[10px] bg-[#004AAD]/15 text-[#004AAD] px-2 py-0.5 rounded font-bold border border-[#004AAD]/30" x-text="selectedApplicant?.status"></span>
                                </div>
                            </div>
                        </div>
                        <button @click="showReviewModal = false" class="text-slate-500 hover:text-[#0B1B3D] p-1">✕</button>
                    </div>

                    <!-- Recommended Job Box -->
                    <template x-if="selectedApplicant?.recommendedJob">
                        <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-200 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-[#004AAD] text-white flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Rekomendasi Lowongan Buka</p>
                                        <p class="text-xs font-extrabold text-[#0B1B3D]" x-text="selectedApplicant?.recommendedJob?.job_title"></p>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-[#004AAD] px-2.5 py-1 rounded-xl bg-white border border-blue-200 shadow-sm" x-text="(selectedApplicant?.recommendedJob?.match_score || selectedApplicant?.matchScore || 0) + '% Cocok'"></span>
                            </div>

                            <div class="pt-1.5 space-y-1.5 text-xs">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[11px] font-semibold text-slate-600">Skill Cocok:</span>
                                    <template x-for="ms in (selectedApplicant?.recommendedJob?.matched_skills || [])" :key="ms">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300" x-text="ms"></span>
                                    </template>
                                    <span x-show="!selectedApplicant?.recommendedJob?.matched_skills || selectedApplicant.recommendedJob.matched_skills.length === 0" class="text-[11px] text-slate-500 italic">Belum ada skill yang identik</span>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap" x-show="selectedApplicant?.recommendedJob?.missing_skills && selectedApplicant.recommendedJob.missing_skills.length > 0">
                                    <span class="text-[11px] font-semibold text-slate-500">Skill Dibutuhkan Lowongan:</span>
                                    <template x-for="mis in (selectedApplicant?.recommendedJob?.missing_skills || [])" :key="mis">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200" x-text="mis"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Evaluation Scores Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Kecocokan Lowongan</p>
                            <p class="text-xl font-extrabold text-[#004AAD]" x-text="(selectedApplicant?.matchScore || 0) + '%'"></p>
                            <p class="text-[10px] text-[#004AAD]">Skor Relevansi</p>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Employability</p>
                            <p class="text-xl font-extrabold text-[#0B1B3D]" x-text="(selectedApplicant?.employabilityScore || selectedApplicant?.matchScore || 0) + '%'"></p>
                            <p class="text-[10px] text-slate-500">Kesiapan Kerja</p>
                        </div>
                        <div class="p-3 bg-white rounded-xl border border-slate-200 text-center col-span-2 sm:col-span-1">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Target Profesi</p>
                            <p class="text-xs font-bold text-[#0B1B3D] truncate mt-1" x-text="selectedApplicant?.role || 'N/A'"></p>
                            <p class="text-[10px] text-slate-500">Peminatan</p>
                        </div>
                    </div>

                    <!-- Portfolio Evidence -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 space-y-2">
                        <span class="text-xs font-bold text-[#0B1B3D] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Portofolio Terkait:
                        </span>
                        <p class="text-xs text-slate-600" x-text="selectedApplicant?.portfolio || 'Belum ada portofolio'"></p>
                        <div class="pt-1 flex items-center justify-between">
                            <a :href="selectedApplicant?.github || '#'" target="_blank" class="text-xs text-[#004AAD] font-semibold hover:underline flex items-center gap-1" x-show="selectedApplicant?.github && selectedApplicant?.github !== '#'">
                                <span>🔗 Buka Repositori</span>
                            </a>
                        </div>
                    </div>

                    <!-- Key Skills -->
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-2">Kompetensi Terdeteksi:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="skill in (selectedApplicant?.topSkills || [])" :key="skill">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-[#004AAD] text-xs font-bold" x-text="skill"></span>
                            </template>
                            <span x-show="!selectedApplicant?.topSkills || selectedApplicant.topSkills.length === 0" class="text-xs text-slate-500">Belum ada data skill.</span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div x-show="selectedApplicant?.notes" class="p-3 bg-blue-50/70 rounded-xl border border-slate-300">
                        <p class="text-[11px] text-slate-500 font-semibold mb-1">Catatan AI:</p>
                        <p class="text-xs text-slate-600" x-text="selectedApplicant?.notes"></p>
                    </div>

                    <!-- Close -->
                    <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                        <a :href="'/mitra/candidates/' + selectedApplicant?.id + (selectedApplicant?.recommendedJob?.job_id ? '?job_id=' + selectedApplicant.recommendedJob.job_id : '')" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Lihat Profil & Hubungi
                        </a>
                        <button type="button" @click="showReviewModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#0B1B3D] text-xs font-bold transition">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-mitra-layout>







