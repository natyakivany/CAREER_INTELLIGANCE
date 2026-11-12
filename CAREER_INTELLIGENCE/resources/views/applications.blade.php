<x-student-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-[#0B1B3D]">Riwayat Lamaran Pekerjaan</h2>
            <p class="text-slate-500 mt-1 text-xs sm:text-sm">Pantau status seleksi lamaran kerja dan jadwal wawancara Anda secara real-time.</p>
        </div>
        @if(Auth::user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#004AAD]/10 hover:bg-[#004AAD]/20 text-[#004AAD] border border-[#004AAD]/30 text-xs font-bold transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Tandai Semua Notifikasi Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    @php
        $totalApps = $applications->count();
        $reviewCount = $applications->where('status', 'Menunggu Review')->count();
        $processCount = $applications->whereIn('status', ['Proses Seleksi', 'Wawancara'])->count();
        $acceptedCount = $applications->where('status', 'Diterima')->count();
        $rejectedCount = $applications->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])->count();
    @endphp

    @if($totalApps > 0)
        <!-- Ringkasan Status Lamaran -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#004AAD] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Total</span>
                    <span class="text-lg font-black text-[#0B1B3D]">{{ $totalApps }}</span>
                </div>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Screening</span>
                    <span class="text-lg font-black text-amber-600">{{ $reviewCount }}</span>
                </div>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
                <div>
                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Seleksi/Tes</span>
                    <span class="text-lg font-black text-purple-700">{{ $processCount }}</span>
                </div>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Diterima</span>
                    <span class="text-lg font-black text-emerald-600">{{ $acceptedCount }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($applications as $app)
            @php
                $job = $app->jobListing;
            @endphp
            <div x-data="{ showTimeline: false }" class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200 hover:border-[#004AAD]/40 transition">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-5">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700">
                                {{ $job->user->name ?? 'Mitra Perusahaan' }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-[11px] text-slate-400 font-medium">
                                Dilamar: {{ $app->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-[#0B1B3D] mb-2.5">
                            <a href="{{ route('jobs.show', $job->id) }}" class="hover:text-[#004AAD] transition">{{ $job->title }}</a>
                        </h3>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-600 mb-3">
                            <span class="inline-flex items-center gap-1.5 bg-slate-100 px-2.5 py-1 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ $job->department }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-slate-100 px-2.5 py-1 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $job->location }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-slate-100 px-2.5 py-1 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ $job->type }}
                            </span>
                        </div>
                        
                        @if($app->notes)
                        <div class="mt-2.5 p-3 bg-slate-50 border border-slate-200 rounded-xl max-w-xl">
                            <p class="text-xs text-slate-600 font-medium">
                                <span class="font-bold text-slate-700">Catatan/Pesan:</span> {{ $app->notes }}
                            </p>
                        </div>
                        @endif

                        @if(in_array($app->status, ['Proses Seleksi', 'Wawancara']) || $app->interview_date)
                            @php
                                $isTest = ($app->status === 'Proses Seleksi');
                                $isConfirmed = ($app->interview_status === 'Hadir');
                                $isDeclined = ($app->interview_status === 'Tidak Hadir');
                                $isWaiting = (!$app->interview_status || $app->interview_status === 'Menunggu Konfirmasi');
                            @endphp

                            <div class="mt-3.5 p-3.5 sm:p-4 rounded-xl {{ $isTest ? 'bg-indigo-50/70 border-indigo-200' : 'bg-purple-50/70 border-purple-200' }} border shadow-2xs max-w-xl">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="p-1 rounded-md {{ $isTest ? 'bg-indigo-600' : 'bg-purple-600' }} text-white flex items-center justify-center shrink-0">
                                        @if($isTest)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-black {{ $isTest ? 'text-indigo-950' : 'text-purple-900' }} uppercase tracking-wide">
                                                {{ $isTest ? 'Tahap 3: Undangan Tes / Seleksi Khusus' : 'Tahap 4: Jadwal Wawancara Resmi' }}
                                            </h4>
                                            @if(!$isTest && $app->interview_date)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold {{ $app->isOfflineInterview() ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-blue-100 text-blue-900 border border-blue-300' }}">
                                                    {{ $app->isOfflineInterview() ? '🏢 Wawancara Offline (Tatap Muka)' : '🌐 Wawancara Online (Daring)' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[11px] text-slate-600 leading-relaxed mb-2.5">
                                    @if($isTest)
                                        Pihak <strong class="text-indigo-950">{{ $job->user->name ?? 'Mitra Industri' }}</strong> telah meloloskan berkas Anda ke tahap seleksi. Silakan konfirmasi kesediaan Anda untuk mengikuti tahap tes/seleksi ini.
                                    @else
                                        Pihak <strong class="text-purple-950">{{ $job->user->name ?? 'Mitra Industri' }}</strong> mengundang Anda ke sesi wawancara {{ $app->isOfflineInterview() ? 'secara tatap muka langsung (offline)' : 'secara daring (online)' }}. Silakan konfirmasi kesediaan Anda sesuai jadwal.
                                    @endif
                                </p>

                                @if($app->interview_date || $app->interview_link || $app->interview_location)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs mb-3">
                                        <div class="bg-white p-2.5 rounded-lg border {{ $isTest ? 'border-indigo-100' : 'border-purple-100' }}">
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-400 font-bold text-[10px] uppercase block">Waktu Pelaksanaan</span>
                                                @if(!$isTest)
                                                    <span class="text-[9.5px] font-bold {{ $app->isOfflineInterview() ? 'text-amber-700' : 'text-blue-700' }}">
                                                        {{ $app->isOfflineInterview() ? 'Tatap Muka' : 'Daring' }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($app->interview_date)
                                                <p class="font-black {{ $isTest ? 'text-indigo-950' : 'text-purple-950' }} text-xs sm:text-sm mt-0.5">
                                                    {{ \Carbon\Carbon::parse($app->interview_date)->translatedFormat('l, d F Y') }}
                                                </p>
                                                <p class="{{ $isTest ? 'text-indigo-700' : 'text-purple-700' }} font-bold text-[11px]">
                                                    Pukul {{ \Carbon\Carbon::parse($app->interview_date)->format('H:i') }} WIB
                                                </p>
                                            @else
                                                <p class="text-slate-400 italic text-[11px] mt-0.5">Jadwal detail menyusul</p>
                                            @endif
                                        </div>

                                        <div class="bg-white p-2.5 rounded-lg border {{ $isTest ? 'border-indigo-100' : 'border-purple-100' }} flex flex-col justify-between">
                                            <div>
                                                @if(!$isTest && $app->isOfflineInterview())
                                                    <span class="text-slate-400 font-bold text-[10px] uppercase block">Lokasi Pertemuan (Offline)</span>
                                                    @if($app->interview_location)
                                                        <p class="text-xs font-bold text-slate-800 mt-1 leading-snug">
                                                            📍 {{ $app->interview_location }}
                                                        </p>
                                                    @else
                                                        <p class="text-xs font-bold text-slate-800 mt-1 leading-snug">
                                                            🏢 Kantor {{ $job->user->name ?? 'Mitra Industri' }}
                                                        </p>
                                                        <p class="text-slate-500 text-[11px] mt-0.5">
                                                            {{ $job->location ? 'Wilayah: '.$job->location.' (Tatap Muka Langsung)' : 'Tatap muka langsung di kantor mitra' }}
                                                        </p>
                                                    @endif
                                                    @if($app->interview_link)
                                                        <a href="{{ $app->interview_link }}" target="_blank" class="inline-flex items-center gap-1 text-[#004AAD] hover:underline font-bold text-xs mt-1.5">
                                                            <span>Lihat Tautan Lokasi/Peta</span> &rarr;
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-slate-400 font-bold text-[10px] uppercase block">Tautan (Link) Meeting / Tes</span>
                                                    @if($app->interview_link)
                                                        <a href="{{ $app->interview_link }}" target="_blank" class="inline-flex items-center gap-1 text-[#004AAD] hover:underline font-bold text-xs mt-1 break-all">
                                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                            Buka Tautan &rarr;
                                                        </a>
                                                    @else
                                                        <p class="text-slate-400 italic text-[11px] mt-0.5">Menunggu lampiran link</p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Baris Status Respons & Tombol Konfirmasi -->
                                <div class="pt-2.5 border-t {{ $isTest ? 'border-indigo-200/60' : 'border-purple-200/60' }} flex items-center justify-between flex-wrap gap-2 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-600 text-[11px]">Status Respons:</span>
                                        @if($isConfirmed)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 font-black text-[11px] border border-emerald-300">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                Bersedia Mengikuti
                                            </span>
                                        @elseif($isDeclined)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-rose-100 text-rose-800 font-black text-[11px] border border-rose-300">
                                                ✕ Tidak Bersedia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-800 font-bold text-[10.5px] border border-amber-300">
                                                ⏳ Menunggu Konfirmasi Anda
                                            </span>
                                        @endif
                                    </div>

                                    @if($isWaiting)
                                        <div class="flex items-center gap-2">
                                            <form action="{{ route('applications.confirm', $app) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" name="interview_status" value="Tidak Hadir" onclick="return confirm('Apakah Anda yakin tidak bersedia mengikuti tahap ini?')" class="px-3 py-1.5 rounded-xl bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold border border-rose-300 transition shadow-2xs cursor-pointer">
                                                    ✕ Tidak Bersedia
                                                </button>
                                            </form>
                                            <form action="{{ route('applications.confirm', $app) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" name="interview_status" value="Hadir" onclick="return confirm('Konfirmasi bahwa Anda bersedia mengikuti tahap ini?')" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    {{ $isTest ? 'Bersedia Mengikuti' : 'Konfirmasi Hadir' }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex flex-col items-start md:items-end gap-2.5 shrink-0">
                        <!-- Status Badge -->
                        @if($app->status === 'Diterima')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-black text-xs sm:text-[13px] shadow-xs">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Diterima
                            </div>
                            <a href="{{ route('applications.download-letter', $app) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs" target="_blank" title="Unduh Surat Resmi Penerimaan Magang">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh Surat (PDF)
                            </a>
                        @elseif($app->status === 'Ditolak')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 font-black text-xs sm:text-[13px] shadow-xs">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Belum Lolos
                            </div>
                        @elseif(in_array($app->status, ['Proses Seleksi', 'Wawancara']))
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl {{ $app->status === 'Wawancara' ? 'bg-purple-50 border-purple-200 text-purple-700' : 'bg-indigo-50 border-indigo-200 text-indigo-700' }} font-black text-xs sm:text-[13px] shadow-xs border">
                                @if($app->status === 'Wawancara')
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                                    Tahap Wawancara
                                @else
                                    <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Proses Seleksi
                                @endif
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 font-black text-xs sm:text-[13px] shadow-xs">
                                <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Menunggu Review
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Card: Tombol Toggle Timeline (Buka Halaman Dihapus) -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="showTimeline = !showTimeline" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer" :class="showTimeline ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-[#004AAD] hover:bg-blue-100'">
                        <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="showTimeline ? 'Tutup Alur Timeline' : 'Lihat Alur Timeline Rekrutmen'"></span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showTimeline ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <span class="text-[11px] text-slate-400 font-medium">
                        ID: #APP-{{ str_pad($app->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <!-- Timeline Expandable Area -->
                <div x-show="showTimeline" x-transition class="mt-4 pt-3 border-t border-slate-100" style="display: none;">
                    <x-application-timeline :job="$job" :application="$app" />
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 shadow-sm border border-slate-200 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-3 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-800 mb-1">Belum Ada Riwayat Lamaran</h3>
                <p class="text-xs text-slate-500 mb-5 max-w-sm">Anda belum mengirimkan berkas lamaran ke lowongan manapun. Temukan lowongan yang sesuai minat Anda!</p>
                <a href="{{ route('jobs') }}" class="px-5 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Cari Lowongan Sekarang &rarr;
                </a>
            </div>
        @endforelse
    </div>
</x-student-layout>
