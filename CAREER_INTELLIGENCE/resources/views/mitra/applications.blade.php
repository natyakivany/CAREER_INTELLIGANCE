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

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-6 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-600 text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Kelola Pelamar Mahasiswa</h1>
                    <span class="hidden sm:inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30">
                        Mitra Kampus
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Tinjau dan kelola semua mahasiswa yang telah melamar ke posisi Anda.
                </p>
            </div>
            <a href="{{ route('mitra.jobs') }}" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 hover:text-[#004AAD] text-slate-600 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                &larr; Kembali ke Daftar Lowongan
            </a>
        </div>

        @php
            $totalNewApplicants = $jobs->sum(fn($j) => $j->applications->where('status', 'Menunggu Review')->count());
            $jobsWithNewApplicants = $jobs->filter(fn($j) => $j->applications->where('status', 'Menunggu Review')->count() > 0)->count();
            $totalInProcess = $jobs->sum(fn($j) => $j->applications->whereIn('status', ['Menunggu Review', 'Proses Seleksi', 'Wawancara'])->count());
        @endphp

        {{-- Highlight Notification Banner jika ada pelamar baru yang belum dilihat --}}
        @if($totalNewApplicants > 0 && ($hasUnreadApplicants ?? false))
            <div x-data="{ showBanner: true }" x-show="showBanner" class="p-4 sm:p-5 rounded-2xl bg-pink-50/80 border border-pink-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 mb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-[#FF44B7] text-white flex items-center justify-center shrink-0 shadow-sm shadow-[#FF44B7]/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-[#0B1B3D] flex items-center gap-2 flex-wrap">
                            <span>Ada {{ $totalNewApplicants }} Pelamar Baru Menunggu Peninjauan!</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FF44B7] text-white">
                                {{ $jobsWithNewApplicants }} Posisi Terisi
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            Posisi dengan pelamar baru otomatis diurutkan di <strong>barisan paling awal</strong> untuk memudahkan prioritas evaluasi Anda.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                    <button type="button" @click="showBanner = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-white transition cursor-pointer" title="Tutup pemberitahuan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            {{-- Kolom Kiri: Daftar Lowongan --}}
            <div class="lg:col-span-12 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-200/80 gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm font-extrabold text-[#0B1B3D] tracking-tight">Daftar Lowongan &amp; Status Pelamar</h2>
                        @if($jobsWithNewApplicants > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $jobsWithNewApplicants }} Menunggu Review
                            </span>
                        @endif
                        <span class="text-xs text-slate-500 font-medium">({{ $jobs->count() }} Posisi Terdaftar)</span>
                    </div>

                    {{-- Urutkan Lowongan: Pelamar Terbaru vs Terlama --}}
                    <div class="flex items-center gap-1 self-start sm:self-auto bg-slate-100 p-1 rounded-xl border border-slate-200">
                        <span class="text-[11px] font-semibold text-slate-500 px-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                            Urutan:
                        </span>
                        <a href="{{ route('mitra.applications.index', ['sort' => 'terbaru']) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ ($sort ?? 'terbaru') === 'terbaru' ? 'bg-[#004AAD] text-white shadow-xs' : 'text-slate-600 hover:text-[#004AAD] hover:bg-white' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Pelamar Terbaru</span>
                        </a>
                        <a href="{{ route('mitra.applications.index', ['sort' => 'terlama']) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ ($sort ?? 'terbaru') === 'terlama' ? 'bg-[#004AAD] text-white shadow-xs' : 'text-slate-600 hover:text-[#004AAD] hover:bg-white' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                            <span>Pelamar Terlama</span>
                        </a>
                    </div>
                </div>
                
                @forelse($jobs as $job)
                    @php
                        $newCount = $job->applications->where('status', 'Menunggu Review')->count();
                        $inProcessCount = $job->applications->whereIn('status', ['Menunggu Review', 'Proses Seleksi', 'Wawancara'])->count();
                        $acceptedCount = $job->applications->where('status', 'Diterima')->count();
                        $rejectedCount = $job->applications->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])->count();
                        $hasNew = ($newCount > 0);
                        $latestApp = $job->applications->sortByDesc('created_at')->first();
                        $oldestApp = $job->applications->sortBy('created_at')->first();
                    @endphp
                    <div x-data="{ showJobTimeline: false }" class="p-5 sm:p-6 rounded-2xl transition duration-200 shadow-sm space-y-3.5 group relative overflow-hidden bg-white border {{ $hasNew ? 'border-[#FF44B7]/50 ring-1 ring-[#FF44B7]/20' : 'border-slate-200 hover:border-slate-300' }}">
                        @if($hasNew)
                            <div class="absolute top-0 right-0 bg-[#FF44B7] text-white text-[9.5px] font-extrabold px-3 py-0.5 rounded-bl-xl tracking-wider uppercase shadow-xs">
                                PRIORITAS: PELAMAR BARU
                            </div>
                        @endif

                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-1.5 pr-2 flex-1">
                                {{-- Penanda Bidang Lowongan --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                        Bidang: {{ $job->department }}
                                    </span>

                                    @if($hasNew)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-pink-50 text-[#FF44B7] border border-pink-200">
                                            ● {{ $newCount }} Pelamar Baru
                                        </span>
                                    @elseif($inProcessCount > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-[#004AAD] border border-blue-200">
                                            {{ $inProcessCount }} Dalam Proses Seleksi
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            Belum ada pelamar baru
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-[#0B1B3D] group-hover:text-[#004AAD] transition">
                                    {{ $job->title }}
                                </h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $job->type }} &bull; {{ $job->location }} &bull; Durasi: {{ $job->duration }}
                                </p>

                                {{-- Informasi Pelamar Terbaru & Terlama --}}
                                @if($job->applications->isNotEmpty())
                                    <div class="mt-3 py-2.5 px-3 rounded-xl bg-slate-50/80 border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                                        {{-- Pelamar Terbaru --}}
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                                            <span class="text-slate-500 font-medium shrink-0">Pelamar Terbaru:</span>
                                            <span class="font-bold text-[#0B1B3D] truncate">{{ $latestApp->user->name ?? 'Kandidat' }}</span>
                                            <span class="text-slate-400 text-[11px] shrink-0">&bull; {{ $latestApp->created_at->diffForHumans() }}</span>
                                            @if($latestApp->status === 'Menunggu Review')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">Perlu Review</span>
                                            @elseif($latestApp->status === 'Proses Seleksi')
                                                @if($latestApp->interview_status === 'Hadir')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">✓ Bersedia Tes</span>
                                                @elseif($latestApp->interview_status === 'Tidak Hadir')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200 shrink-0">✕ Tolak Tes</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">Menunggu Respon</span>
                                                @endif
                                            @elseif($latestApp->status === 'Wawancara')
                                                @if($latestApp->interview_status === 'Hadir')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">✓ Hadir ({{ $latestApp->isOfflineInterview() ? 'Offline' : 'Online' }})</span>
                                                @elseif($latestApp->interview_status === 'Tidak Hadir')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200 shrink-0">✕ Tolak ({{ $latestApp->isOfflineInterview() ? 'Offline' : 'Online' }})</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-800 border border-purple-200 shrink-0">{{ $latestApp->isOfflineInterview() ? 'Wawancara Offline' : 'Wawancara Online' }}</span>
                                                @endif
                                            @endif
                                        </div>

                                        {{-- Pelamar Terlama --}}
                                        @if($job->applications->count() > 1 && $oldestApp->id !== $latestApp->id)
                                            <div class="hidden sm:block w-px h-4 bg-slate-200"></div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full bg-slate-400 shrink-0"></span>
                                                <span class="text-slate-500 font-medium shrink-0">Pelamar Terlama:</span>
                                                <span class="font-bold text-[#0B1B3D] truncate">{{ $oldestApp->user->name ?? 'Kandidat' }}</span>
                                                <span class="text-slate-400 text-[11px] shrink-0">&bull; {{ $oldestApp->created_at->diffForHumans() }}</span>
                                                @if($oldestApp->status === 'Menunggu Review')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">Menunggu</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0 pt-1">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $job->status === 'Buka' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                    ● {{ $job->status }}
                                </span>
                                
                                @if($hasNew)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $newCount }} Menunggu Review
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10.5px] font-medium {{ $inProcessCount > 0 ? 'bg-blue-50 text-[#004AAD] border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $inProcessCount }} Dalam Proses
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-2.5">
                            @if($hasNew)
                                <a href="{{ route('mitra.applications.show', $job) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#FF44B7] hover:bg-[#e038a0] text-white text-xs font-bold transition shadow-sm shadow-[#FF44B7]/25">
                                    <span>Tinjau {{ $newCount }} Pelamar Baru &rarr;</span>
                                </a>
                            @else
                                <a href="{{ route('mitra.applications.show', $job) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm">
                                    <span>Kelola Pelamar ({{ $inProcessCount }} Dalam Proses) &rarr;</span>
                                </a>
                            @endif

                            <div class="flex items-center gap-2">
                                <button type="button" @click="showJobTimeline = !showJobTimeline" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#004AAD] border border-blue-200 text-[10.5px] font-bold transition cursor-pointer">
                                    <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="showJobTimeline ? 'Tutup Timeline' : 'Alur Timeline'"></span>
                                </button>
                                @if($acceptedCount > 0)
                                    <a href="{{ route('mitra.candidates.accepted', ['job_id' => $job->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10.5px] font-bold transition" title="Lihat kandidat yang telah diterima untuk lowongan ini">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        {{ $acceptedCount }} Diterima
                                    </a>
                                @endif
                                @if($rejectedCount > 0)
                                    <a href="{{ route('mitra.candidates.rejected', ['job_id' => $job->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[10.5px] font-bold transition" title="Lihat kandidat yang ditolak untuk lowongan ini">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        {{ $rejectedCount }} Ditolak
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Skills --}}
                        @if($job->skills && count($job->skills) > 0)
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach($job->skills as $skill)
                                    <span class="px-2 py-0.5 rounded-md bg-white text-slate-600 text-[10.5px] font-medium border border-slate-300/60">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Footer --}}
                        <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs flex-wrap gap-2">
                            <div class="flex items-center gap-2 text-slate-500 text-[11px] flex-wrap">
                                <span>Dipublikasikan: {{ $job->created_at->translatedFormat('d M Y') }}</span>
                                @if($job->applications->isNotEmpty())
                                    <span>&bull;</span>
                                    <span class="font-bold text-slate-600">Total: {{ $job->applications->count() }} Pelamar</span>
                                @endif
                            </div>
                            @if($hasNew && ($hasUnreadApplicants ?? false))
                                <span class="text-rose-600 font-extrabold text-[11px] flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                    Ada {{ $newCount }} pelamar belum direview
                                </span>
                            @elseif($hasNew)
                                <span class="text-amber-700 font-semibold text-[11px] flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ $newCount }} pelamar menunggu review
                                </span>
                            @endif
                        </div>

                        {{-- Timeline Standar Alur Lowongan --}}
                        <div x-show="showJobTimeline" x-transition class="pt-3 border-t border-slate-200" style="display: none;">
                            <x-application-timeline :job="$job" />
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#004AAD] text-white flex items-center justify-center shadow-md">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Belum ada lowongan yang dipublikasikan.</p>
                        <p class="text-slate-500 text-xs mt-1">Gunakan form untuk membuka lowongan pertama Anda.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-mitra-layout>
