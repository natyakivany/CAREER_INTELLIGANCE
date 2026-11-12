<x-mitra-layout>
    <div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-6 p-4 rounded-xl bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Kelola Pelamar Mahasiswa</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30">
                        Mitra Kampus
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Tinjau dan kelola semua mahasiswa yang telah melamar ke posisi Anda.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('mitra.applications.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] text-xs font-bold transition border border-slate-300">
                    &larr; Kembali ke Daftar Lowongan
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            {{-- Kolom Kiri: Detail Lowongan & Pelamar --}}
            <div class="lg:col-span-12 space-y-4">
                
                @php
                    $inProcessApps = $job->applications->whereIn('status', ['Menunggu Review', 'Proses Seleksi', 'Wawancara']);
                    $totalApps = $job->applications->count();
                    $countProses = $inProcessApps->count();
                    $countMenunggu = $inProcessApps->where('status', 'Menunggu Review')->count();
                    $countTes = $inProcessApps->where('status', 'Proses Seleksi')->count();
                    $countWawancara = $inProcessApps->where('status', 'Wawancara')->count();
                    $countDiterima = $job->applications->where('status', 'Diterima')->count();
                    $countDitolak = $job->applications->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])->count();

                    $newestInProcessId = $inProcessApps->sortByDesc('created_at')->first()?->id;
                    $oldestInProcessId = $inProcessApps->sortBy('created_at')->first()?->id;
                @endphp

                <div x-data="{ showJobTimeline: false }" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xl space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#004AAD]">{{ $job->department }}</span>
                            <h3 class="text-base font-bold text-[#0B1B3D] mt-0.5">{{ $job->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $job->type }} • {{ $job->location }} • Durasi: {{ $job->duration }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $job->status === 'Buka' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 'bg-red-500/15 text-red-600 border border-red-500/30' }}">
                                    ● {{ $job->status }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-500/15 text-amber-700 border border-amber-500/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $countProses }} Dalam Proses
                                </span>
                                <button type="button" @click="showJobTimeline = !showJobTimeline" class="mt-1 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#004AAD] text-[11px] font-bold border border-blue-200 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="showJobTimeline ? 'Tutup Alur Lowongan' : 'Lihat Standar Timeline Lowongan'"></span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="showJobTimeline ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                        </div>
                    </div>

                    {{-- Timeline Standar Alur Lowongan --}}
                    <div x-show="showJobTimeline" x-transition class="pt-3 border-t border-slate-200" style="display: none;">
                        <x-application-timeline :job="$job" />
                    </div>

                    {{-- Mahasiswa yang Melamar --}}
                    <div>
                        @if($inProcessApps->isNotEmpty())
                            <div x-data="{ activeTab: 'semua' }" class="pt-3 border-t border-slate-200">
                                
                                {{-- Tab Filter Dalam Proses & Urutan Pelamar --}}
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[11px] font-bold text-slate-600 mr-2">Status Proses:</span>
                                        
                                        <button @click="activeTab = 'semua'" 
                                            :class="activeTab === 'semua' ? 'bg-[#0b233a] text-white border-[#0b233a] shadow-md shadow-[#0b233a]/20' : 'bg-white text-slate-600 border-slate-300 hover:border-[#004AAD]/50 hover:text-[#004AAD]'" 
                                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                            Semua Dalam Proses
                                            <span :class="activeTab === 'semua' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'" class="px-1.5 py-0.5 rounded-md text-[9px] font-bold ml-0.5 transition-all">{{ $countProses }}</span>
                                        </button>
                                        
                                        <button @click="activeTab = 'menunggu'" 
                                            :class="activeTab === 'menunggu' ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20' : 'bg-white text-amber-700 border-amber-300 hover:border-amber-400 hover:bg-amber-50'" 
                                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Menunggu Review
                                            <span :class="activeTab === 'menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-600'" class="px-1.5 py-0.5 rounded-md text-[9px] font-bold ml-0.5 transition-all">{{ $countMenunggu }}</span>
                                        </button>
                                        
                                        <button @click="activeTab = 'tes'" 
                                            :class="activeTab === 'tes' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/20' : 'bg-white text-indigo-700 border-indigo-300 hover:border-indigo-400 hover:bg-indigo-50'" 
                                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            Sedang Tes / Seleksi
                                            <span :class="activeTab === 'tes' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-600'" class="px-1.5 py-0.5 rounded-md text-[9px] font-bold ml-0.5 transition-all">{{ $countTes }}</span>
                                        </button>

                                        <button @click="activeTab = 'wawancara'" 
                                            :class="activeTab === 'wawancara' ? 'bg-purple-600 text-white border-purple-600 shadow-md shadow-purple-600/20' : 'bg-white text-purple-700 border-purple-300 hover:border-purple-400 hover:bg-purple-50'" 
                                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                            Tahap Wawancara
                                            <span :class="activeTab === 'wawancara' ? 'bg-purple-100 text-purple-600'" class="px-1.5 py-0.5 rounded-md text-[9px] font-bold ml-0.5 transition-all">{{ $countWawancara }}</span>
                                        </button>
                                    </div>

                                    {{-- Urutkan Pelamar: Terbaru vs Terlama --}}
                                    <div class="flex items-center gap-1 bg-slate-100/90 p-1 rounded-xl border border-slate-200 self-start md:self-auto">
                                        <span class="text-[10.5px] font-bold text-slate-500 px-2 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                            Urutkan:
                                        </span>
                                        <a href="{{ route('mitra.applications.show', ['job' => $job->id, 'sort' => 'terbaru']) }}"
                                           class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition flex items-center gap-1 {{ ($sort ?? 'terbaru') === 'terbaru' ? 'bg-[#004AAD] text-white shadow-xs' : 'text-slate-600 hover:text-[#004AAD] hover:bg-white' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Terbaru</span>
                                        </a>
                                        <a href="{{ route('mitra.applications.show', ['job' => $job->id, 'sort' => 'terlama']) }}"
                                           class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition flex items-center gap-1 {{ ($sort ?? 'terbaru') === 'terlama' ? 'bg-[#004AAD] text-white shadow-xs' : 'text-slate-600 hover:text-[#004AAD] hover:bg-white' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                            <span>Terlama</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="space-y-2.5">
                                    @foreach($inProcessApps as $app)
                                        @php
                                            $appCategory = match($app->status) {
                                                'Menunggu Review' => 'menunggu',
                                                'Proses Seleksi' => 'tes',
                                                'Wawancara' => 'wawancara',
                                                default => 'proses',
                                            };
                                        @endphp
                                        <div x-data="{ showWawancaraModal: false, showTimeline: false }" 
                                             x-show="activeTab === 'semua' || activeTab === '{{ $appCategory }}'"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm text-xs space-y-2.5 relative">

                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center font-bold text-[#004AAD] border border-blue-200 text-[11px] shrink-0 border border-slate-300 overflow-hidden">
                                                    @if($app->user->avatar_url)
                                                        <img src="{{ $app->user->avatar_url }}" alt="{{ $app->user->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($app->user->name ?? 'M', 0, 2)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <a href="{{ route('mitra.candidates.show', $app->user) }}" class="font-bold text-[#0B1B3D] hover:text-[#004AAD] transition inline-flex items-center gap-1.5">
                                                            <span>{{ $app->user->name }}</span>
                                                            <span class="text-[10px] text-slate-500 hover:text-[#004AAD] font-normal underline">Lihat Profil &rarr;</span>
                                                        </a>

                                                        @if($app->id === $newestInProcessId)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-pink-50 text-[#FF44B7] border border-pink-200">
                                                                Pelamar Terbaru
                                                            </span>
                                                        @elseif($app->id === $oldestInProcessId && $countProses > 1)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                                Pelamar Terlama
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 text-[10.5px] text-slate-500 flex-wrap mt-0.5">
                                                        <span>{{ $app->user->nim ?? '-' }} &bull; {{ $app->user->studentProfile->prodi ?? 'Mahasiswa' }}</span>
                                                        <span>&bull;</span>
                                                        <span class="flex items-center gap-1 text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            Melamar: {{ $app->created_at->translatedFormat('d M Y, H:i') }} ({{ $app->created_at->diffForHumans() }})
                                                        </span>
                                                        @php
                                                            $applicantAssessment = $app->user->assessments->first();
                                                        @endphp
                                                        @if($applicantAssessment)
                                                            <span>&bull;</span>
                                                            <span class="inline-flex items-center gap-1 font-semibold text-[#004AAD] bg-blue-50/80 px-1.5 py-0.5 rounded border border-blue-200" title="Tanggal Tes Asesmen AI Terakhir: {{ $applicantAssessment->created_at->translatedFormat('d F Y, H:i') }} WIB">
                                                                <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                                                Asesmen: {{ $applicantAssessment->created_at->translatedFormat('d M Y') }} ({{ $applicantAssessment->created_at->diffForHumans() }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($app->interview_date)
                                                        <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                            <div class="flex items-center gap-1.5 text-[10.5px] text-purple-700 font-semibold bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200 w-fit">
                                                                <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                                                <span class="font-bold {{ $app->isOfflineInterview() ? 'text-amber-800 bg-amber-100' : 'text-blue-800 bg-blue-100' }} px-1.5 py-0.2 rounded text-[9.5px]">
                                                                    {{ $app->isOfflineInterview() ? '🏢 Offline' : '🌐 Online' }}
                                                                </span>
                                                                <span>Jadwal: {{ \Carbon\Carbon::parse($app->interview_date)->translatedFormat('d M Y, H:i') }} WIB</span>
                                                                @if($app->isOfflineInterview())
                                                                    @if($app->interview_location)
                                                                        <span class="text-slate-600 truncate max-w-[180px]" title="{{ $app->interview_location }}">📍 {{ $app->interview_location }}</span>
                                                                    @endif
                                                                    @if($app->interview_link)
                                                                        <a href="{{ $app->interview_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline ml-0.5 font-bold" title="Buka tautan lokasi / peta">Peta</a>
                                                                    @endif
                                                                @else
                                                                    @if($app->interview_link)
                                                                        <a href="{{ $app->interview_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline ml-0.5 font-bold" title="Buka tautan meeting">Link</a>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                            @if($app->interview_status === 'Hadir')
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                    Kandidat Bersedia Hadir
                                                                </span>
                                                            @elseif($app->interview_status === 'Tidak Hadir')
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                                    ✕ Kandidat Tidak Bersedia
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                                    ⏳ Menunggu Konfirmasi Mahasiswa
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 self-end sm:self-center flex-wrap">
                                                @php
                                                    $appEmpScore = (int) ($app->user->assessments->first()?->employability_score ?? $app->user->studentProfile?->employability_score ?? 0);
                                                    $appKesiapan = $appEmpScore >= 80 ? 'Siap Kerja' : ($appEmpScore >= 50 ? 'Berkembang' : 'Baru Mulai');
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold {{ $appEmpScore >= 80 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($appEmpScore >= 50 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}" title="Skor Kesiapan Kerja: {{ $appEmpScore }}%">
                                                    {{ $appKesiapan }} ({{ $appEmpScore }}%)
                                                </span>

                                                <span class="px-2 py-0.5 rounded-md bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20 font-bold text-[10.5px]">
                                                    Match {{ round($app->match_score ?? 0) }}%
                                                </span>

                                                {{-- Form Konfirmasi Status Lamaran --}}
                                                <form action="{{ route('mitra.applications.status', $app) }}" method="POST" class="flex items-center gap-1.5">
                                                    @csrf
                                                    @method('PATCH')

                                                    @if($app->status === 'Menunggu Review')
                                                        {{-- Tahap 1: Menunggu Review --}}
                                                        <div x-data="{ openMenu: false }" class="relative inline-block text-left">
                                                            <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                                                <span>Menunggu Review</span>
                                                                <svg class="w-3.5 h-3.5 text-amber-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                            </button>

                                                            <div x-show="openMenu" @click.away="openMenu = false" x-transition
                                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-200 py-1.5 z-50 text-left divide-y divide-slate-100" style="display: none;">
                                                                <div class="px-3 py-1 text-[10px] font-bold uppercase text-slate-400">Pilih Aksi Proses:</div>
                                                                <div class="p-1">
                                                                    <button type="submit" name="status" value="Proses Seleksi" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-indigo-700 hover:bg-indigo-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                                        <span>Lanjut ke Tes</span>
                                                                    </button>
                                                                    <button type="button" @click="openMenu = false; showWawancaraModal = true" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-purple-700 hover:bg-purple-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                                                        <span>Jadwal Wawancara</span>
                                                                    </button>
                                                                </div>
                                                                <div class="p-1">
                                                                    <button type="submit" name="status" value="Ditolak" onclick="return confirm('Apakah Anda yakin ingin menolak lamaran ini?');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        <span>Tolak Lamaran</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @elseif($app->status === 'Proses Seleksi')
                                                        {{-- Tahap 2: Tes --}}
                                                        <div x-data="{ openMenu: false }" class="relative inline-block text-left">
                                                            @if($app->interview_status === 'Hadir')
                                                                <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Mahasiswa telah mengonfirmasi bersedia mengikuti tes">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                    <span>Mahasiswa Bersedia (Tes)</span>
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>
                                                            @elseif($app->interview_status === 'Tidak Hadir')
                                                                <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Mahasiswa menyatakan tidak bersedia mengikuti tes">
                                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                    <span>Mahasiswa Tidak Bersedia</span>
                                                                    <svg class="w-3.5 h-3.5 text-rose-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>
                                                            @else
                                                                <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Tes: Menunggu konfirmasi kesediaan dari mahasiswa">
                                                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                                                    <span>Menunggu Mahasiswa</span>
                                                                    <svg class="w-3.5 h-3.5 text-amber-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>
                                                            @endif

                                                            <div x-show="openMenu" @click.away="openMenu = false" x-transition
                                                                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-2xl border border-slate-200 py-1.5 z-50 text-left divide-y divide-slate-100" style="display: none;">
                                                                <div class="px-3 py-1 text-[10px] font-bold uppercase text-slate-400">Hasil Tes / Aksi:</div>
                                                                <div class="p-1">
                                                                    <button type="button" @click="openMenu = false; showWawancaraModal = true" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-purple-700 hover:bg-purple-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                                                        <span>Lolos Tes & Wawancara</span>
                                                                    </button>
                                                                    <button type="submit" name="status" value="Menunggu Review" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                                                        <span>Kembalikan ke Review</span>
                                                                    </button>
                                                                </div>
                                                                <div class="p-1">
                                                                    <button type="submit" name="status" value="Ditolak" onclick="return confirm('Kandidat tidak lolos tes?');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        <span>Tolak Hasil Tes</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @elseif($app->status === 'Wawancara')
                                                        {{-- Tahap 3: Wawancara --}}
                                                        <div x-data="{ openMenu: false }" class="relative inline-block text-left">
                                                            @if($app->interview_status === 'Hadir')
                                                                <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Mahasiswa telah mengonfirmasi bersedia hadir wawancara">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                    <span>Mahasiswa Bersedia (Wawancara)</span>
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>
                                                            @elseif($app->interview_status === 'Tidak Hadir')
                                                                <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Mahasiswa tidak dapat hadir / menolak jadwal wawancara">
                                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                    <span>Mahasiswa Menolak Wawancara</span>
                                                                    <svg class="w-3.5 h-3.5 text-rose-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>
                                                            @else
                                                                <button type="button" @click="openMenu = !openMenu" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="Wawancara: Menunggu konfirmasi kehadiran mahasiswa">
                                                                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-ping"></span>
                                                                    <span>Wawancara (Menunggu Mahasiswa)</span>
                                                                    <svg class="w-3.5 h-3.5 text-purple-600 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>
                                                            @endif

                                                            <div x-show="openMenu" @click.away="openMenu = false" x-transition
                                                                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-2xl border border-slate-200 py-1.5 z-50 text-left divide-y divide-slate-100" style="display: none;">
                                                                <div class="px-3 py-1 text-[10px] font-bold uppercase text-slate-400">Keputusan Wawancara:</div>
                                                                <div class="p-1">
                                                                    <button type="submit" name="status" value="Diterima" onclick="return confirm('Terima mahasiswa ini untuk magang/kerja?');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                        <span>Terima Kerja / Magang</span>
                                                                    </button>
                                                                    <button type="button" @click="openMenu = false; showWawancaraModal = true" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-purple-700 hover:bg-purple-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                                                        <span>Ubah Jadwal</span>
                                                                    </button>
                                                                </div>
                                                                <div class="p-1">
                                                                    <button type="submit" name="status" value="Ditolak" onclick="return confirm('Tolak lamaran ini?');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition cursor-pointer">
                                                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        <span>Tolak Lamaran</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </form>

                                                <form action="{{ route('mitra.applications.destroy', $app) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pelamar ini? Aksi ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-500 text-red-500 hover:text-white transition border border-red-200 hover:border-red-500 text-[11px] font-bold shadow-sm flex items-center gap-1 cursor-pointer ml-1" title="Hapus Pelamar">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                                
                                                <!-- Modal Wawancara (Online & Offline) -->
                                                <div x-show="showWawancaraModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" style="display: none;">
                                                    <div @click.away="showWawancaraModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left" x-data="{ interviewType: '{{ $app->isOnlineInterview() ? 'online' : 'offline' }}' }">
                                                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                                            <div>
                                                                <h3 class="text-base font-extrabold text-[#0B1B3D]">Jadwalkan Sesi Wawancara</h3>
                                                                <p class="text-xs text-slate-500 mt-0.5">Tentukan jadwal & metode (Online/Offline) untuk <strong>{{ $app->user->name }}</strong>.</p>
                                                            </div>
                                                            <button type="button" @click="showWawancaraModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <form action="{{ route('mitra.applications.status', $app) }}" method="POST" class="space-y-4">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="Wawancara">

                                                            <!-- Pilihan Metode: Online vs Offline -->
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pelaksanaan Wawancara <span class="text-rose-500">*</span></label>
                                                                <div class="grid grid-cols-2 gap-3">
                                                                    <label class="cursor-pointer">
                                                                        <input type="radio" name="interview_type" value="online" x-model="interviewType" class="peer sr-only">
                                                                        <div class="p-3 rounded-xl border-2 transition flex items-center gap-2.5"
                                                                             :class="interviewType === 'online' ? 'border-[#004AAD] bg-blue-50/60 text-[#004AAD]' : 'border-slate-200 hover:border-slate-300 text-slate-600'">
                                                                            <span class="text-xl">🌐</span>
                                                                            <div>
                                                                                <p class="text-xs font-extrabold">Online (Daring)</p>
                                                                                <p class="text-[10px] text-slate-500">Google Meet / Zoom (Wajib Link)</p>
                                                                            </div>
                                                                        </div>
                                                                    </label>

                                                                    <label class="cursor-pointer">
                                                                        <input type="radio" name="interview_type" value="offline" x-model="interviewType" class="peer sr-only">
                                                                        <div class="p-3 rounded-xl border-2 transition flex items-center gap-2.5"
                                                                             :class="interviewType === 'offline' ? 'border-amber-600 bg-amber-50/60 text-amber-900' : 'border-slate-200 hover:border-slate-300 text-slate-600'">
                                                                            <span class="text-xl">🏢</span>
                                                                            <div>
                                                                                <p class="text-xs font-extrabold">Offline (Tatap Muka)</p>
                                                                                <p class="text-[10px] text-slate-500">Tanpa Link / Lokasi Kantor</p>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal & Waktu Wawancara <span class="text-rose-500">*</span></label>
                                                                <input type="datetime-local" name="interview_date" value="{{ $app->interview_date ? \Carbon\Carbon::parse($app->interview_date)->format('Y-m-d\TH:i') : '' }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-[#0B1B3D] focus:border-[#004AAD] focus:ring focus:ring-[#004AAD]/20 transition">
                                                            </div>

                                                            <!-- Input Khusus Online -->
                                                            <div x-show="interviewType === 'online'" x-transition>
                                                                <label class="block text-xs font-bold text-slate-700 mb-1">Tautan / Link Meeting (Google Meet, Zoom, dll.) <span class="text-rose-500">*</span></label>
                                                                <input type="url" name="interview_link" 
                                                                       :disabled="interviewType === 'offline'"
                                                                       value="{{ $app->isOnlineInterview() ? ($app->interview_link ?? '') : '' }}" 
                                                                       placeholder="https://meet.google.com/... atau https://zoom.us/j/..." 
                                                                       :required="interviewType === 'online'" 
                                                                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-[#0B1B3D] focus:border-[#004AAD] focus:ring focus:ring-[#004AAD]/20 transition">
                                                                <p class="text-[11px] text-slate-400 mt-1">Sertakan tautan meeting online. Jika tidak diisi tautan, wawancara otomatis dianggap sebagai <strong>Offline (Tatap Muka)</strong>.</p>
                                                            </div>

                                                            <!-- Input Khusus Offline -->
                                                            <div x-show="interviewType === 'offline'" x-transition style="display: none;">
                                                                <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi / Alamat Lengkap Pertemuan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                                                <textarea name="interview_location" rows="2" placeholder="Contoh: Kantor Utama PT Mitra, Ruang Rapat Lt. 2, Jl. Khatib Sulaiman No. 10, Padang" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-[#0B1B3D] focus:border-amber-600 focus:ring focus:ring-amber-500/20 transition">{{ $app->interview_location ?? '' }}</textarea>
                                                                <p class="text-[11px] text-slate-400 mt-1">Tuliskan nama gedung, ruangan, atau alamat kantor. Jika dikosongkan, otomatis menggunakan lokasi kantor mitra.</p>
                                                            </div>

                                                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                                <button type="button" @click="showWawancaraModal = false" class="px-4 py-2 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition text-xs sm:text-sm">Batal</button>
                                                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#004AAD] text-white font-bold hover:bg-[#083577] transition text-xs sm:text-sm shadow-md">Simpan & Kirim Undangan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            {{-- Footer Card: Tombol Toggle Timeline Pelamar --}}
                                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                                <button type="button" @click="showTimeline = !showTimeline" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer" 
                                                    :class="showTimeline ? 'bg-slate-100 text-slate-800' : 'bg-blue-50 text-[#004AAD] hover:bg-blue-100'">
                                                    <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span x-text="showTimeline ? 'Tutup Alur Timeline' : 'Lihat Alur Timeline Rekrutmen'"></span>
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showTimeline ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>
                                                
                                                <span class="text-[11px] text-slate-400 font-medium">
                                                    ID Pelamar: #APP-{{ str_pad($app->id, 4, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </div>

                                            {{-- Timeline Expandable Area --}}
                                            <div x-show="showTimeline" x-transition class="pt-2 border-t border-slate-100" style="display: none;">
                                                <x-application-timeline :job="$job" :application="$app" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Empty State per Tab saat ada pelamar dalam proses tapi tab tertentu kosong --}}
                                <div x-show="activeTab === 'menunggu' && {{ $countMenunggu }} === 0" class="py-8 text-center" style="display: none;">
                                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-amber-50 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600">Tidak ada pelamar baru yang menunggu review</p>
                                </div>
                                <div x-show="activeTab === 'tes' && {{ $countTes }} === 0" class="py-8 text-center" style="display: none;">
                                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-indigo-50 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600">Tidak ada pelamar dalam tahap tes / seleksi</p>
                                </div>
                                <div x-show="activeTab === 'wawancara' && {{ $countWawancara }} === 0" class="py-8 text-center" style="display: none;">
                                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-purple-50 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600">Tidak ada pelamar dalam tahap wawancara</p>
                                </div>

                            </div>
                        @else
                            <div class="pt-6 pb-4 border-t border-slate-200 text-center">
                                @if($totalApps > 0)
                                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600 shadow-sm">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-[#0B1B3D]">Semua Pelamar Selesai Diproses!</h4>
                                    <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-4">
                                        Tidak ada pelamar yang masih dalam proses. Seluruh pelamar telah dipindahkan ke menu khusus kandidat diterima atau ditolak.
                                    </p>
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('mitra.candidates.accepted', ['job_id' => $job->id]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Kandidat Diterima ({{ $countDiterima }})
                                        </a>
                                        <a href="{{ route('mitra.candidates.rejected', ['job_id' => $job->id]) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-rose-50 text-rose-600 border border-rose-300 font-bold text-xs transition shadow-sm inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Kandidat Ditolak ({{ $countDitolak }})
                                        </a>
                                    </div>
                                @else
                                    <div class="w-14 h-14 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium">Belum ada mahasiswa yang melamar untuk posisi ini.</p>
                                @endif
                            </div>
                        @endif
                        </div>

                        {{-- Skills --}}
                        @if($job->skills && count($job->skills) > 0)
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach($job->skills as $skill)
                                    <span class="px-2 py-0.5 rounded-md bg-white text-slate-600 text-[11px] font-medium border border-slate-300/60">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Footer --}}
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500 text-[11px]">
                                Dibuat: {{ $job->created_at->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                    </div>
            </div>

        </div>
    </div>
</x-mitra-layout>
