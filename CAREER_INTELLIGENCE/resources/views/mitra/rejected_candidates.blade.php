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
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Kandidat Ditolak</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-300 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Arsip Riwayat Seleksi
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Daftar pelamar yang belum memenuhi kualifikasi atau tidak lolos seleksi. Anda dapat memulihkan status kandidat jika diperlukan.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('mitra.candidates.accepted') }}" class="px-4 py-2.5 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Kandidat Diterima
                </a>
                <a href="{{ route('mitra.applications.index') }}" class="px-4 py-2.5 bg-slate-50 border border-slate-300 hover:bg-slate-100 hover:text-[#0B1B3D] text-slate-600 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Semua Lamaran
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-rose-50/50 p-5 rounded-2xl border border-rose-500/20 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Total Kandidat Ditolak</span>
                    <h3 class="text-3xl font-black text-[#0B1B3D] mt-1">{{ $totalRejected }} <span class="text-sm font-semibold text-slate-500 font-normal">Kandidat</span></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Tersimpan dalam arsip seleksi</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Rata-rata Match Score</span>
                    <h3 class="text-3xl font-black text-[#0B1B3D] mt-1">{{ $avgScore }}%</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Skor kesesuaian kandidat yang ditolak</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
            <form action="{{ route('mitra.candidates.rejected') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
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
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-[#004AAD] hover:bg-[#0b233a] text-white text-xs font-bold rounded-xl transition shadow-sm text-center">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'job_id', 'sort']))
                        <a href="{{ route('mitra.candidates.rejected') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Reset Filter">
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
                    $latestAssessment = $student->assessments->first();
                @endphp

                <div x-data="{ showRestoreModal: false, showDeleteModal: false, showTimeline: false }" 
                     class="bg-white rounded-2xl border-2 border-rose-200 hover:border-rose-400 transition-all duration-200 shadow-sm overflow-hidden group">
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        
                        {{-- Identity & Lowongan --}}
                        <div class="flex items-start gap-4">
                            <div class="relative shrink-0">
                                <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" 
                                     class="w-14 h-14 rounded-2xl object-cover border-2 border-rose-300 shadow-md grayscale group-hover:grayscale-0 transition duration-300">
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-rose-500 text-white rounded-full flex items-center justify-center text-[10px] font-black border-2 border-white shadow" title="Ditolak">
                                    ✕
                                </span>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-extrabold text-[#0B1B3D] group-hover:text-rose-700 transition">
                                        {{ $student->name }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        ✕ Ditolak
                                    </span>
                                </div>

                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $student->nim ?? 'NIM: -' }} &bull; {{ $profile->prodi ?? 'Program Studi' }} ({{ $profile->jurusan ?? '-' }})
                                </p>

                                <div class="flex items-center gap-2 pt-1 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $app->jobListing->title }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        &bull; Tanggal Ditinjau: {{ $app->updated_at->isoFormat('D MMMM Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Match Score & Actions --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                            {{-- Score Badges --}}
                            <div class="flex items-center gap-2">
                                <div class="text-right px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200">
                                    <div class="text-[9px] font-bold text-rose-700 uppercase tracking-wider">Match Score</div>
                                    <div class="text-base font-black text-rose-800">{{ $app->match_score ?? 0 }}%</div>
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

                                {{-- Alur Timeline --}}
                                <button @click="showTimeline = !showTimeline" 
                                        class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#004AAD] text-xs font-bold transition flex items-center gap-1.5 border border-blue-200" title="Lihat Alur Timeline Rekrutmen">
                                    <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="showTimeline ? 'Tutup Timeline' : 'Alur Timeline'"></span>
                                </button>

                                {{-- Pulihkan Kandidat Modal Trigger --}}
                                <button @click="showRestoreModal = true" 
                                        class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm shadow-amber-500/20" title="Pulihkan / Beri Kesempatan Seleksi Lagi">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Pulihkan
                                </button>

                                {{-- Hapus Data Trigger --}}
                                <button @click="showDeleteModal = true" 
                                        class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition border border-slate-200" title="Hapus Riwayat">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Timeline Expandable Area --}}
                        <div x-show="showTimeline" x-transition class="p-5 border-t border-slate-100 bg-slate-50/50" style="display: none;">
                            <x-application-timeline :job="$app->jobListing" :application="$app" />
                        </div>
                    </div>

                    {{-- Modal: Pulihkan Kandidat --}}
                    <div x-show="showRestoreModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showRestoreModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5 text-slate-800">
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                                            ↺
                                        </div>
                                        <h3 class="text-sm font-extrabold text-[#0B1B3D]">Pulihkan Status Kandidat</h3>
                                    </div>
                                    <button @click="showRestoreModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Apakah Anda ingin memulihkan kandidat <strong>{{ $student->name }}</strong> untuk posisi <strong>{{ $app->jobListing->title }}</strong>?
                                </p>

                                <form action="{{ route('mitra.applications.status', $app) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-bold text-[#0B1B3D] mb-1">Pulihkan Ke Status:</label>
                                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#004AAD]">
                                            <option value="Proses Seleksi" selected>Proses Seleksi</option>
                                            <option value="Menunggu Review">Menunggu Review</option>
                                            <option value="Wawancara">Tahap Wawancara</option>
                                            <option value="Diterima">Langsung Terima Kandidat</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2">
                                        <button type="button" @click="showRestoreModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow">Konfirmasi Pulihkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal: Hapus Riwayat --}}
                    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-4 text-slate-800">
                                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </div>
                                <div class="text-center space-y-1">
                                    <h3 class="text-sm font-extrabold text-[#0B1B3D]">Hapus Riwayat Lamaran?</h3>
                                    <p class="text-xs text-slate-500">
                                        Data lamaran {{ $student->name }} akan dihapus secara permanen dari sistem.
                                    </p>
                                </div>
                                <form action="{{ route('mitra.applications.destroy', $app) }}" method="POST" class="pt-2">
                                    @csrf
                                    @method('DELETE')
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" @click="showDeleteModal = false" class="flex-1 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                                        <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow">Ya, Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[#0B1B3D]">Belum Ada Riwayat Kandidat Ditolak</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                            Saat ini tidak ada kandidat yang tercatat dengan status ditolak. Semua proses seleksi berjalan sesuai alur.
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
