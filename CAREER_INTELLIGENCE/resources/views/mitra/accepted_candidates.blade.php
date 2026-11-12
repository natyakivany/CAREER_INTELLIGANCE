<x-mitra-layout>
    <div class="space-y-6">

        {{-- Flash Message --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Kandidat Diterima</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Fitur Penerimaan Magang
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Kelola dan tinjau seluruh mahasiswa yang telah resmi diterima bekerja atau magang di perusahaan Anda.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('mitra.applications.index') }}" class="px-4 py-2.5 bg-slate-50 border border-slate-300 hover:bg-slate-100 hover:text-[#0B1B3D] text-slate-600 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Semua Lamaran
                </a>
                <a href="{{ route('mitra.candidates.rejected') }}" class="px-4 py-2.5 bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Kandidat Ditolak
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-emerald-50/50 p-5 rounded-2xl border border-emerald-500/20 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Total Diterima</span>
                    <h3 class="text-3xl font-black text-[#0B1B3D] mt-1">{{ $totalAccepted }} <span class="text-sm font-semibold text-slate-500 font-normal">Kandidat</span></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Resmi bergabung di tim</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#004AAD]">Posisi Terisi</span>
                    <h3 class="text-3xl font-black text-[#0B1B3D] mt-1">{{ $distinctJobsCount }} <span class="text-sm font-semibold text-slate-500 font-normal">Lowongan</span></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Dari total {{ $jobs->count() }} lowongan aktif</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-[#004AAD]/15 text-[#004AAD] flex items-center justify-center border border-[#004AAD]/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Rata-rata Match Score</span>
                    <h3 class="text-3xl font-black text-[#0B1B3D] mt-1">{{ $avgScore }}%</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kesesuaian kualifikasi & skill</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-600 flex items-center justify-center border border-amber-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
            <form action="{{ route('mitra.candidates.accepted') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                {{-- Search Keyword --}}
                <div class="sm:col-span-5 relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama, NIM, atau program studi..."
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#004AAD]/20 focus:border-[#004AAD]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                {{-- Filter Lowongan --}}
                <div class="sm:col-span-3">
                    <select name="job_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004AAD]/20 focus:border-[#004AAD]">
                        <option value="">Semua Lowongan</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                                {{ Str::limit($job->title, 28) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div class="sm:col-span-2">
                    <select name="sort" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004AAD]/20 focus:border-[#004AAD]">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="score_desc" {{ request('sort') == 'score_desc' ? 'selected' : '' }}>Skor Tertinggi</option>
                        <option value="score_asc" {{ request('sort') == 'score_asc' ? 'selected' : '' }}>Skor Terendah</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-[#004AAD] hover:bg-[#0b233a] text-white text-xs font-bold rounded-xl transition shadow-sm text-center">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'job_id', 'sort']))
                        <a href="{{ route('mitra.candidates.accepted') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Candidate List --}}
        <div class="space-y-4">
            @forelse($applications as $app)
                @php
                    $student = $app->user;
                    $profile = $student->studentProfile;
                    $phone = $profile?->phone ?? '';
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                    $latestAssessment = $student->assessments->first();
                @endphp

                <div x-data="{ showDetail: false, showOfferModal: false, showStatusModal: false, showTimeline: false }" 
                     class="bg-white rounded-2xl border-2 border-emerald-300/80 hover:border-emerald-500 transition-all duration-200 shadow-sm overflow-hidden group">
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        
                        {{-- Identity & Lowongan --}}
                        <div class="flex items-start gap-4">
                            <div class="relative shrink-0">
                                <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" 
                                     class="w-14 h-14 rounded-2xl object-cover border-2 border-emerald-400 shadow-md">
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center text-[10px] font-black border-2 border-white shadow" title="Diterima Resmi">
                                    ✓
                                </span>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-extrabold text-[#0B1B3D] group-hover:text-emerald-700 transition">
                                        {{ $student->name }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        ✓ Diterima Kerja / Magang
                                    </span>
                                </div>

                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $student->nim ?? 'NIM: -' }} &bull; {{ $profile->prodi ?? 'Program Studi' }} ({{ $profile->jurusan ?? '-' }})
                                </p>

                                <div class="flex items-center gap-2 pt-1 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $app->jobListing->title }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        &bull; Diterima: {{ $app->updated_at->isoFormat('D MMMM Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Match Score & Actions --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                            {{-- Score Badges --}}
                            <div class="flex items-center gap-2">
                                <div class="text-right px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200">
                                    <div class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider">Match Score</div>
                                    <div class="text-base font-black text-emerald-800">{{ $app->match_score ?? 0 }}%</div>
                                </div>
                                @if($latestAssessment)
                                    <div class="text-right px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Assessment</div>
                                        <div class="text-base font-black text-[#0B1B3D]">{{ round($latestAssessment->total_score ?? 0) }}</div>
                                        <div class="text-[8.5px] text-slate-400 mt-0.5" title="Tanggal Asesmen Terbaru">{{ $latestAssessment->created_at?->translatedFormat('d M Y') }}</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                {{-- Lihat Profil --}}
                                <a href="{{ route('mitra.candidates.show', $student) }}" 
                                   class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-[#004AAD] hover:text-white text-slate-700 text-xs font-bold transition flex items-center gap-1 shadow-sm" title="Lihat Profil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profil
                                </a>

                                {{-- Lihat CV --}}
                                @if($student->isCvFilled())
                                    <a href="{{ route('mitra.candidates.cv', $student) }}" target="_blank"
                                       class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-800 border border-emerald-200 text-xs font-bold transition flex items-center gap-1 shadow-sm" title="Lihat CV ATS (Aktif)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        CV
                                    </a>
                                @else
                                    <span class="px-3 py-2 rounded-xl bg-slate-50 text-slate-400 border border-slate-200 text-xs font-bold flex items-center gap-1 cursor-not-allowed opacity-75" title="CV Belum Diisi (Tidak Aktif)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        CV
                                    </span>
                                @endif

                                 {{-- Surat Penerimaan Modal Trigger --}}
                                <button @click="showOfferModal = true" 
                                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 text-xs font-bold transition flex items-center gap-1.5 border border-slate-200 hover:border-emerald-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Surat
                                </button>

                                {{-- Direct PDF Download --}}
                                <a href="{{ route('mitra.applications.download-letter', $app) }}" 
                                   class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm shadow-emerald-600/30" title="Download Surat Penerimaan (PDF Asli)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Unduh PDF
                                </a>

                                {{-- Kontak WA --}}
                                @if(!empty($cleanPhone))
                                    <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($student->name) }},%20selamat%20Anda%20resmi%20diterima%20untuk%20posisi%20{{ urlencode($app->jobListing->title) }}%20di%20{{ urlencode(Auth::user()->name) }}." target="_blank"
                                       class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition border border-emerald-300 flex items-center gap-1" title="Hubungi via WhatsApp">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 2.012.827 3.28.827 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.767-5.768-5.767zm7.545 5.766c-.002 4.161-3.385 7.544-7.545 7.544-1.311 0-2.591-.341-3.717-.991l-4.148 1.088 1.107-4.045c-.714-1.185-1.091-2.544-1.09-3.951.002-4.162 3.386-7.545 7.546-7.545 4.16 0 7.547 3.383 7.547 7.545z"/></svg>
                                        WA
                                    </a>
                                @endif

                                {{-- Alur Timeline --}}
                                <button @click="showTimeline = !showTimeline" 
                                        class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#004AAD] text-xs font-bold transition flex items-center gap-1.5 border border-blue-200" title="Lihat Alur Timeline Rekrutmen">
                                    <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="showTimeline ? 'Tutup Timeline' : 'Alur Timeline'"></span>
                                </button>

                                {{-- Status Options / Ubah --}}
                                <button @click="showStatusModal = true" 
                                        class="px-2.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-200 text-slate-500 hover:text-slate-700 text-xs font-bold transition border border-slate-200" title="Kelola / Ubah Status">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Timeline Expandable Area --}}
                        <div x-show="showTimeline" x-transition class="p-5 border-t border-slate-100 bg-slate-50/50" style="display: none;">
                            <x-application-timeline :job="$app->jobListing" :application="$app" />
                        </div>
                    </div>

                    {{-- Modal: Surat Keterangan Penerimaan (Printable Letter) --}}
                    <div x-show="showOfferModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showOfferModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 sm:p-8 space-y-6 text-slate-800">
                                
                                <div class="flex items-center justify-between border-b pb-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">
                                            ✓
                                        </div>
                                        <div>
                                            <h3 class="text-base font-extrabold text-[#0B1B3D]">Surat Keterangan Diterima Magang / Kerja</h3>
                                            <p class="text-xs text-slate-500">Bukti resmi penerimaan mitra industri</p>
                                        </div>
                                    </div>
                                    <button @click="showOfferModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                                </div>

                                {{-- Surat Preview Box --}}
                                <div id="letter-content-{{ $app->id }}" class="bg-white p-6 sm:p-8 rounded-xl border border-slate-300 shadow-sm space-y-4 text-xs sm:text-sm text-black leading-relaxed" style="font-family: 'Times New Roman', Times, serif;">
                                    <div class="text-center pb-1">
                                        <h4 class="font-bold text-black text-base sm:text-lg uppercase tracking-wide">{{ Auth::user()->name }}</h4>
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-700 mt-0.5">MITRA INDUSTRI SISTEM KEMITRAAN KAMPUS</p>
                                        <p class="text-[10px] text-slate-600 mt-0.5">
                                            @if($app->jobListing->location && $app->jobListing->location !== 'Indonesia')
                                                Lokasi: {{ $app->jobListing->location }} &bull;
                                            @endif
                                            @if(Auth::user()->email)
                                                Email: {{ Auth::user()->email }} &bull;
                                            @endif
                                            Portal Resmi: Career Intelligence Platform
                                        </p>
                                        <div class="mt-2 border-t-2 border-b border-black h-1"></div>
                                    </div>

                                    <div class="text-center my-2">
                                        <h5 class="font-bold text-black text-xs sm:text-sm uppercase underline tracking-wide">SURAT KETERANGAN PENERIMAAN MAGANG / KERJA</h5>
                                        <p class="text-[11px] text-slate-700 mt-0.5">Nomor: SPM/{{ date('Y/m', strtotime($app->updated_at ?? now())) }}/{{ str_pad((string) $app->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>

                                    <p class="text-xs sm:text-sm">Dengan hormat,</p>
                                    <p class="text-xs sm:text-sm text-justify" style="text-indent: 1.5rem;">
                                        Berdasarkan hasil peninjauan berkas lamaran, penilaian kualifikasi kompetensi, dan seluruh tahapan seleksi yang telah diselenggarakan oleh <strong>{{ Auth::user()->name }}</strong>, dengan ini kami menerangkan dan mengonfirmasi secara resmi bahwa:
                                    </p>
                                    
                                    <div class="ml-2 sm:ml-4 space-y-1.5 text-xs sm:text-sm">
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Nama Lengkap</span>
                                            <span class="col-span-7 sm:col-span-8 font-bold text-black">: {{ $student->name }}</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Nomor Induk Mahasiswa (NIM)</span>
                                            <span class="col-span-7 sm:col-span-8 text-black">: {{ $student->nim ?? '-' }}</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Program Studi / Jurusan</span>
                                            <span class="col-span-7 sm:col-span-8 text-black">: {{ $profile->prodi ?? 'Mahasiswa' }} @if($profile?->jurusan) ({{ $profile->jurusan }}) @endif</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Posisi Penugasan</span>
                                            <span class="col-span-7 sm:col-span-8 font-bold text-black">: {{ $app->jobListing->title }}</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Departemen / Divisi</span>
                                            <span class="col-span-7 sm:col-span-8 text-black">: {{ $app->jobListing->department }}</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Jenis Penugasan &amp; Lokasi</span>
                                            <span class="col-span-7 sm:col-span-8 text-black">: {{ $app->jobListing->type }} &bull; {{ $app->jobListing->location }}</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Durasi Pelaksanaan</span>
                                            <span class="col-span-7 sm:col-span-8 text-black">: {{ $app->jobListing->duration }}</span>
                                        </div>
                                        <div class="grid grid-cols-12 gap-2">
                                            <span class="col-span-5 sm:col-span-4 text-slate-700">Status Keputusan</span>
                                            <span class="col-span-7 sm:col-span-8 font-bold text-black">: LOLOS &amp; DITERIMA</span>
                                        </div>
                                    </div>

                                    <p class="text-xs sm:text-sm text-justify" style="text-indent: 1.5rem;">
                                        Kandidat yang bersangkutan dinyatakan <strong>DITERIMA</strong> untuk mengikuti program kerja/magang industri pada posisi tersebut. Kandidat diharapkan berkoordinasi lebih lanjut dengan tim pembimbing industri terkait jadwal efektif penugasan serta petunjuk teknis pelaksanaan program.
                                    </p>

                                    <p class="text-xs sm:text-sm text-justify" style="text-indent: 1.5rem;">
                                        Demikian surat keterangan penerimaan ini diterbitkan sebagai bukti sah penerimaan magang/kerja untuk dapat dipergunakan sebagaimana mestinya.
                                    </p>
                                    
                                    <div class="pt-4 grid grid-cols-2 gap-4 items-end text-xs">
                                        <div class="p-2.5 rounded border border-slate-300 bg-slate-50 text-[10px] space-y-0.5">
                                            <div class="font-bold text-slate-800 uppercase tracking-wider border-b pb-1 mb-1">Catatan Otentikasi Digital</div>
                                            <div>&bull; Verifikasi: <strong class="text-emerald-700">VALID &amp; RESMI</strong></div>
                                            <div>&bull; ID Lamaran: <strong>#APP-{{ $app->id }}</strong></div>
                                            <div>&bull; Match Score: <strong>{{ $app->match_score ?? 0 }}%</strong></div>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-slate-700">{{ $app->jobListing->location ?? 'Indonesia' }}, {{ $app->updated_at ? $app->updated_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</p>
                                            <p class="font-bold text-black text-xs mt-0.5">{{ Auth::user()->name }}</p>
                                            <p class="text-[10px] text-slate-600">Tim Rekrutmen &amp; Kemitraan</p>
                                            <div class="my-2 inline-block px-2.5 py-0.5 border border-[#004AAD] text-[#004AAD] text-[9px] font-bold rounded">
                                                [ DITERBITKAN RESMI SECARA DIGITAL ]
                                            </div>
                                            <p class="font-bold text-black text-xs underline">( {{ Auth::user()->name }} )</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Buttons --}}
                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <a href="{{ route('mitra.applications.download-letter', $app) }}" 
                                       class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download PDF Resmi
                                    </a>
                                    <button @click="showOfferModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal: Ubah Status --}}
                    <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showStatusModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5 text-slate-800">
                                <div class="flex items-center justify-between border-b pb-3">
                                    <h3 class="text-sm font-extrabold text-[#0B1B3D]">Ubah Status Kandidat</h3>
                                    <button @click="showStatusModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Anda sedang mengubah status lamaran untuk <strong>{{ $student->name }}</strong> pada posisi <strong>{{ $app->jobListing->title }}</strong>.
                                </p>

                                <form action="{{ route('mitra.applications.status', $app) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-bold text-[#0B1B3D] mb-1">Pilih Status Baru:</label>
                                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004AAD]">
                                            <option value="Diterima" selected>Diterima (Tetap)</option>
                                            <option value="Proses Seleksi">Kembalikan ke Proses Seleksi</option>
                                            <option value="Menunggu Review">Kembalikan ke Menunggu Review</option>
                                            <option value="Ditolak">Ubah menjadi Ditolak</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2">
                                        <button type="button" @click="showStatusModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-[#004AAD] hover:bg-[#0b233a] text-white text-xs font-bold rounded-xl transition">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[#0B1B3D]">Belum Ada Kandidat yang Diterima</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                            Anda belum menandai pelamar sebagai "Diterima". Silakan tinjau pelamar pada menu Kelola Pelamar dan ubah status kandidat pilihan Anda menjadi Diterima.
                        </p>
                    </div>
                    <a href="{{ route('mitra.applications.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#004AAD] hover:bg-[#0b233a] text-white text-xs font-bold rounded-xl transition shadow">
                        Buka Kelola Pelamar &rarr;
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($applications->hasPages())
            <div class="pt-4">
                {{ $applications->links() }}
            </div>
        @endif

    </div>
</x-mitra-layout>
