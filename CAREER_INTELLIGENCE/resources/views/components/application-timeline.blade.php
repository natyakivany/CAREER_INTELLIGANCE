@props([
    'job',
    'application' => null,
])

@php
    $appStatus = $application?->status;
    $isRejected = in_array($appStatus, ['Ditolak', 'Tawaran Ditolak'], true);
    $isAccepted = ($appStatus === 'Diterima');
    $hasApplied = (bool) $application;
    $isMitraView = (auth()->check() && in_array(auth()->user()->role, ['mitra', 'admin']));

    // Menentukan tahapan aktif (activeStep 1 - 6):
    if (!$hasApplied) {
        $activeStep = 1;
        $progressPercent = 0;
    } elseif ($appStatus === 'Menunggu Review') {
        $activeStep = 2;
        $progressPercent = 33;
    } elseif ($appStatus === 'Proses Seleksi') {
        $activeStep = 3;
        $progressPercent = 50;
    } elseif ($appStatus === 'Wawancara') {
        $activeStep = 4;
        $progressPercent = 67;
    } elseif ($isAccepted) {
        $activeStep = 6;
        $progressPercent = 100;
    } elseif ($isRejected) {
        $activeStep = 5;
        $progressPercent = 83;
    } else {
        $activeStep = 2;
        $progressPercent = 33;
    }

    $getStepState = function (int $step) use ($hasApplied, $activeStep, $isAccepted, $isRejected): string {
        if (!$hasApplied) {
            return ($step === 1) ? 'ready' : 'upcoming';
        }
        if ($isAccepted) {
            return ($step === 6) ? 'active' : 'completed';
        }
        if ($isRejected) {
            if ($step < 5) return 'completed';
            if ($step === 5) return 'rejected';
            return 'upcoming';
        }
        if ($step < $activeStep) return 'completed';
        if ($step === $activeStep) return 'active';
        return 'upcoming';
    };

    $downloadRoute = $isMitraView
        ? ($application ? route('mitra.applications.download-letter', $application) : '#')
        : ($application ? route('applications.download-letter', $application) : '#');
@endphp

<div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm relative overflow-hidden">
    <!-- Header Timeline (Versi Diperkecil & Rapi) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[9.5px] font-black uppercase tracking-wider bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Alur Rekrutmen &amp; Seleksi
                </span>
                <span class="text-xs sm:text-[13px] font-bold text-[#0B1B3D]">
                    @if($isMitraView)
                        Timeline: {{ $job->title }}
                    @else
                        Timeline: {{ $job->user->name ?? 'Mitra Perusahaan' }}
                    @endif
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-0.5">
                @if(!$hasApplied)
                    {{ $isMitraView ? 'Standar alur 6 tahap seleksi untuk pelamar di posisi ini' : 'Alur 6 tahapan proses pelamaran dan evaluasi kandidat' }}
                @elseif($isAccepted)
                    <span class="text-emerald-700 font-bold">🎉 Lolos Seleksi &amp; Resmi Diterima Bekerja</span>
                @elseif($isRejected)
                    <span class="text-rose-700 font-bold">Proses seleksi selesai (Belum Lolos)</span>
                @else
                    Status saat ini: <strong class="text-[#004AAD]">{{ $application->status }}</strong>
                    @if($application && $application->created_at)
                        &bull; Melamar: {{ $application->created_at->translatedFormat('d M Y, H:i') }} WIB
                    @endif
                @endif
            </p>
        </div>

        <!-- Progress Tracker Mini -->
        <div class="flex items-center gap-2.5 shrink-0 self-end sm:self-auto bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
            <span class="text-[10.5px] font-bold text-slate-700">
                @if(!$hasApplied)
                    Siap Diajukan
                @elseif($isAccepted)
                    Tahap 6/6 (100%)
                @elseif($isRejected)
                    Tahap 5 (Selesai)
                @else
                    Tahap {{ $activeStep }}/6
                @endif
            </span>
            <div class="w-20 sm:w-24 h-2 bg-slate-200 rounded-full overflow-hidden p-0.5 border border-slate-300">
                <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Alur Vertikal Zig-Zag / Selang-Seling Klasik (Proporsi Compact / Diperkecil) -->
    <div class="relative py-2">
        <!-- Garis Tengah Putus-Putus -->
        <div class="hidden md:block absolute left-1/2 top-4 bottom-4 -translate-x-1/2 w-0.5 border-l-2 border-dashed border-slate-300"></div>
        <div class="md:hidden absolute left-4 top-4 bottom-4 w-0.5 border-l-2 border-dashed border-slate-300"></div>

        <div class="space-y-3 sm:space-y-3.5 relative">
            
            {{-- STEP 1: Pengajuan Lamaran (Kiri) --}}
            @php $state1 = $getStepState(1); @endphp
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="w-full md:w-1/2 md:pr-7 md:text-right pl-10 md:pl-0">
                    <div class="inline-block md:max-w-sm p-2.5 sm:p-3 rounded-xl transition duration-150 text-left md:text-right
                        {{ $state1 === 'completed' ? 'bg-emerald-50/70 border border-emerald-200' : ($state1 === 'ready' ? 'bg-amber-50/60 border border-amber-300' : 'bg-white border border-slate-200') }}">
                        <div class="flex items-center gap-1.5 md:justify-end mb-0.5">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase
                                {{ $state1 === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $state1 === 'completed' ? '✓ Selesai Diajukan' : 'Siap Diajukan' }}
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-[13px] font-extrabold {{ $state1 === 'completed' ? 'text-emerald-900' : 'text-slate-800' }}">
                            Tahap 1: Pengajuan Lamaran
                        </h4>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
                            Berkas lamaran dikirim dengan CV ATS terverifikasi &amp; portofolio proyek.
                        </p>
                        @if($application)
                            <div class="mt-1 md:justify-end flex">
                                <span class="text-[9.5px] font-semibold text-emerald-700 bg-white px-2 py-0.5 rounded border border-emerald-200">
                                    Terkirim: {{ $application->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Circle 1 --}}
                <div class="absolute left-4 md:left-1/2 -translate-x-1/2 top-2 md:top-auto flex items-center justify-center">
                    <div class="hidden md:block absolute right-full w-5 lg:w-6 border-t-2 {{ $state1 === 'completed' ? 'border-emerald-400' : 'border-dashed border-amber-300' }}"></div>
                    @if($state1 === 'completed')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs ring-2 ring-emerald-200 shrink-0 z-10">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white border-2 border-amber-500 text-amber-600 font-bold text-xs flex items-center justify-center shadow-xs shrink-0 z-10">
                            1
                        </div>
                    @endif
                </div>

                <div class="hidden md:block md:w-1/2 md:pl-7"></div>
            </div>

            {{-- STEP 2: Review Mitra Industri (Kanan) --}}
            @php $state2 = $getStepState(2); @endphp
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="hidden md:block md:w-1/2 md:pr-7"></div>

                {{-- Circle 2 --}}
                <div class="absolute left-4 md:left-1/2 -translate-x-1/2 top-2 md:top-auto flex items-center justify-center">
                    @if($state2 === 'completed')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs ring-2 ring-emerald-200 shrink-0 z-10">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @elseif($state2 === 'active')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-[#004AAD] border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-md ring-2 ring-blue-200 shrink-0 z-10 animate-pulse scale-105">
                            2
                        </div>
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-100 border border-slate-300 text-slate-400 font-bold text-xs flex items-center justify-center shadow-xs shrink-0 z-10">
                            2
                        </div>
                    @endif
                    <div class="hidden md:block absolute left-full w-5 lg:w-6 border-t-2 {{ $state2 === 'completed' ? 'border-emerald-400' : ($state2 === 'active' ? 'border-[#004AAD]' : 'border-dashed border-slate-300') }}"></div>
                </div>

                <div class="w-full md:w-1/2 md:pl-7 text-left pl-10 md:pl-7">
                    <div class="inline-block md:max-w-sm p-2.5 sm:p-3 rounded-xl transition duration-150
                        {{ $state2 === 'active' ? 'bg-blue-50/90 border-2 border-[#004AAD] shadow-xs' : ($state2 === 'completed' ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-white/80 border border-slate-200 opacity-60') }}">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase
                                {{ $state2 === 'active' ? 'bg-[#004AAD] text-white' : ($state2 === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500') }}">
                                {{ $state2 === 'active' ? '● Tahap Aktif' : ($state2 === 'completed' ? '✓ Lolos Screening' : 'Antre') }}
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-[13px] font-extrabold {{ $state2 === 'active' ? 'text-[#004AAD]' : ($state2 === 'completed' ? 'text-emerald-900' : 'text-slate-700') }}">
                            Tahap 2: Review Mitra Industri
                        </h4>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
                            Mitra memvalidasi kualifikasi, skor kesiapan, dan kecocokan portofolio.
                        </p>
                        @if($state2 === 'active')
                            <div class="mt-1 p-1.5 rounded-lg bg-white border border-blue-200 text-[10px] text-[#004AAD] font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#004AAD] animate-ping shrink-0"></span>
                                <span>{{ $isMitraView ? 'Berkas sedang dalam peninjauan oleh tim Anda' : 'Berkas Anda sedang discreening oleh HR Mitra' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- STEP 3: Tes / Seleksi Khusus (Kiri) --}}
            @php $state3 = $getStepState(3); @endphp
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="w-full md:w-1/2 md:pr-7 md:text-right pl-10 md:pl-0">
                    <div class="inline-block md:max-w-sm p-2.5 sm:p-3 rounded-xl transition duration-150 text-left md:text-right
                        {{ $state3 === 'active' ? 'bg-indigo-50/90 border-2 border-indigo-600 shadow-xs' : ($state3 === 'completed' ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-white/80 border border-slate-200 opacity-60') }}">
                        <div class="flex items-center gap-1.5 md:justify-end mb-0.5">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase
                                {{ $state3 === 'active' ? 'bg-indigo-600 text-white' : ($state3 === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500') }}">
                                {{ $state3 === 'active' ? '● Tahap Aktif' : ($state3 === 'completed' ? '✓ Lolos Tes' : 'Antre') }}
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-[13px] font-extrabold {{ $state3 === 'active' ? 'text-indigo-700' : ($state3 === 'completed' ? 'text-emerald-900' : 'text-slate-700') }}">
                            Tahap 3: Tes / Seleksi Khusus
                        </h4>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
                            Pelamar mengikuti uji kompetensi teknis atau penugasan studi kasus.
                        </p>
                        @if($state3 === 'active')
                            <div class="mt-1 p-1.5 rounded-lg bg-white border border-indigo-200 text-[10px] md:justify-end flex items-center gap-1">
                                @if($application?->interview_status === 'Hadir')
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="text-emerald-700 font-bold">{{ $isMitraView ? 'Mahasiswa Bersedia Mengikuti Tes' : 'Anda Bersedia Mengikuti Tes' }}</span>
                                @elseif($application?->interview_status === 'Tidak Hadir')
                                    <span class="text-rose-600 font-bold">{{ $isMitraView ? 'Mahasiswa Tidak Bersedia' : 'Anda Tidak Bersedia Mengikuti' }}</span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                                    <span class="text-amber-800 font-bold">{{ $isMitraView ? 'Menunggu Konfirmasi Mahasiswa' : 'Menunggu Konfirmasi Kesediaan Anda' }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Circle 3 --}}
                <div class="absolute left-4 md:left-1/2 -translate-x-1/2 top-2 md:top-auto flex items-center justify-center">
                    <div class="hidden md:block absolute right-full w-5 lg:w-6 border-t-2 {{ $state3 === 'completed' ? 'border-emerald-400' : ($state3 === 'active' ? 'border-indigo-600' : 'border-dashed border-slate-300') }}"></div>
                    @if($state3 === 'completed')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs ring-2 ring-emerald-200 shrink-0 z-10">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @elseif($state3 === 'active')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-indigo-600 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-md ring-2 ring-indigo-200 shrink-0 z-10 animate-pulse scale-105">
                            3
                        </div>
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-100 border border-slate-300 text-slate-400 font-bold text-xs flex items-center justify-center shadow-xs shrink-0 z-10">
                            3
                        </div>
                    @endif
                </div>

                <div class="hidden md:block md:w-1/2 md:pl-7"></div>
            </div>

            {{-- STEP 4: Sesi Wawancara (Kanan) --}}
            @php $state4 = $getStepState(4); @endphp
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="hidden md:block md:w-1/2 md:pr-7"></div>

                {{-- Circle 4 --}}
                <div class="absolute left-4 md:left-1/2 -translate-x-1/2 top-2 md:top-auto flex items-center justify-center">
                    @if($state4 === 'completed')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs ring-2 ring-emerald-200 shrink-0 z-10">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @elseif($state4 === 'active')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-purple-600 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-md ring-2 ring-purple-200 shrink-0 z-10 animate-pulse scale-105">
                            4
                        </div>
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-100 border border-slate-300 text-slate-400 font-bold text-xs flex items-center justify-center shadow-xs shrink-0 z-10">
                            4
                        </div>
                    @endif
                    <div class="hidden md:block absolute left-full w-5 lg:w-6 border-t-2 {{ $state4 === 'completed' ? 'border-emerald-400' : ($state4 === 'active' ? 'border-purple-600' : 'border-dashed border-slate-300') }}"></div>
                </div>

                <div class="w-full md:w-1/2 md:pl-7 text-left pl-10 md:pl-7">
                    <div class="inline-block md:max-w-sm p-2.5 sm:p-3 rounded-xl transition duration-150
                        {{ $state4 === 'active' ? 'bg-purple-50/90 border-2 border-purple-600 shadow-xs' : ($state4 === 'completed' ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-white/80 border border-slate-200 opacity-60') }}">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase
                                {{ $state4 === 'active' ? 'bg-purple-600 text-white' : ($state4 === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500') }}">
                                {{ $state4 === 'active' ? '● Tahap Aktif' : ($state4 === 'completed' ? '✓ Wawancara Selesai' : 'Antre') }}
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-[13px] font-extrabold {{ $state4 === 'active' ? 'text-purple-800' : ($state4 === 'completed' ? 'text-emerald-900' : 'text-slate-700') }}">
                            Tahap 4: Sesi Wawancara
                        </h4>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
                            Wawancara pendalaman teknis &amp; kesiapan kerja secara daring/tatap muka.
                        </p>
                        @if($state4 === 'active' && $application)
                            <div class="mt-1.5 p-2 rounded-lg bg-white border border-purple-200 text-[10px] space-y-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9.5px] font-bold {{ $application->isOfflineInterview() ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-blue-100 text-blue-900 border border-blue-300' }}">
                                        {{ $application->isOfflineInterview() ? '🏢 Wawancara Offline' : '🌐 Wawancara Online' }}
                                    </span>
                                </div>
                                @if($application->interview_date)
                                    <p class="font-bold text-purple-900 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                        Jadwal: {{ \Carbon\Carbon::parse($application->interview_date)->translatedFormat('d M Y - H:i') }} WIB
                                    </p>
                                @endif
                                @if($application->isOfflineInterview())
                                    @if($application->interview_location)
                                        <p class="text-slate-700 font-medium">📍 Lokasi: {{ $application->interview_location }}</p>
                                    @endif
                                    @if($application->interview_link)
                                        <a href="{{ $application->interview_link }}" target="_blank" class="inline-flex items-center gap-1 text-[10.5px] text-[#004AAD] hover:underline font-bold">
                                            <span>Buka Link Peta / Petunjuk</span> &rarr;
                                        </a>
                                    @endif
                                @else
                                    @if($application->interview_link)
                                        <a href="{{ $application->interview_link }}" target="_blank" class="inline-flex items-center gap-1 text-[10.5px] text-[#004AAD] hover:underline font-bold">
                                            <span>Buka Link Meeting</span> &rarr;
                                        </a>
                                    @endif
                                @endif

                                @if($application->interview_status === 'Hadir')
                                    <div class="mt-1 pt-1 border-t border-purple-100 flex items-center gap-1 text-[9.5px] font-bold text-emerald-700">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Konfirmasi: {{ $isMitraView ? 'Mahasiswa Bersedia Hadir' : 'Anda Bersedia Hadir' }}</span>
                                    </div>
                                @elseif($application->interview_status === 'Tidak Hadir')
                                    <div class="mt-1 pt-1 border-t border-purple-100 flex items-center gap-1 text-[9.5px] font-bold text-rose-700">
                                        <span>✕ Konfirmasi: {{ $isMitraView ? 'Mahasiswa Tidak Bersedia' : 'Anda Menolak Jadwal' }}</span>
                                    </div>
                                @elseif($application->interview_date)
                                    <div class="mt-1 pt-1 border-t border-purple-100 flex items-center gap-1 text-[9.5px] font-semibold text-amber-700">
                                        <span>⏳ {{ $isMitraView ? 'Menunggu Konfirmasi Mahasiswa' : 'Menunggu Konfirmasi Anda' }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- STEP 5: Keputusan Akhir (Kiri) --}}
            @php $state5 = $getStepState(5); @endphp
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="w-full md:w-1/2 md:pr-7 md:text-right pl-10 md:pl-0">
                    <div class="inline-block md:max-w-sm p-2.5 sm:p-3 rounded-xl transition duration-150 text-left md:text-right
                        {{ $state5 === 'completed' ? 'bg-emerald-50/70 border border-emerald-200' : ($state5 === 'rejected' ? 'bg-rose-50 border-2 border-rose-300' : ($state5 === 'active' ? 'bg-blue-50 border border-[#004AAD]' : 'bg-white/80 border border-slate-200 opacity-60')) }}">
                        <div class="flex items-center gap-1.5 md:justify-end mb-0.5">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase
                                {{ $state5 === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($state5 === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-500') }}">
                                {{ $state5 === 'completed' ? '🎉 Lolos' : ($state5 === 'rejected' ? '✕ Belum Lolos' : 'Antre') }}
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-[13px] font-extrabold {{ $state5 === 'completed' ? 'text-emerald-900' : ($state5 === 'rejected' ? 'text-rose-800' : 'text-slate-700') }}">
                            Tahap 5: Keputusan Akhir
                        </h4>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
                            Penetapan status kelulusan (Diterima / Belum Lolos) yang dikirim ke sistem.
                        </p>
                        @if($state5 === 'completed')
                            <div class="mt-1 md:justify-end flex">
                                <span class="text-[9.5px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded border border-emerald-200">
                                    {{ $isMitraView ? 'Kandidat dinyatakan Diterima' : 'Selamat! Anda dinyatakan Diterima' }}
                                </span>
                            </div>
                        @elseif($state5 === 'rejected')
                            <div class="mt-1 md:justify-end flex">
                                <span class="text-[9.5px] font-bold text-rose-700 bg-white px-2 py-0.5 rounded border border-rose-200">
                                    {{ $isMitraView ? 'Kandidat belum memenuhi kualifikasi' : 'Belum lolos seleksi kali ini' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Circle 5 --}}
                <div class="absolute left-4 md:left-1/2 -translate-x-1/2 top-2 md:top-auto flex items-center justify-center">
                    <div class="hidden md:block absolute right-full w-5 lg:w-6 border-t-2 {{ $state5 === 'completed' ? 'border-emerald-400' : ($state5 === 'rejected' ? 'border-rose-400' : 'border-dashed border-slate-300') }}"></div>
                    @if($state5 === 'completed')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs ring-2 ring-emerald-200 shrink-0 z-10">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @elseif($state5 === 'rejected')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-rose-500 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-xs ring-2 ring-rose-200 shrink-0 z-10">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-100 border border-slate-300 text-slate-400 font-bold text-xs flex items-center justify-center shadow-xs shrink-0 z-10">
                            5
                        </div>
                    @endif
                </div>

                <div class="hidden md:block md:w-1/2 md:pl-7"></div>
            </div>

            {{-- STEP 6: Surat Tugas & Onboarding (Kanan) --}}
            @php $state6 = $getStepState(6); @endphp
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="hidden md:block md:w-1/2 md:pr-7"></div>

                {{-- Circle 6 --}}
                <div class="absolute left-4 md:left-1/2 -translate-x-1/2 top-2 md:top-auto flex items-center justify-center">
                    @if($state6 === 'active' || $state6 === 'completed')
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-600 border-2 border-white text-white font-black text-xs flex items-center justify-center shadow-md ring-2 ring-emerald-200 shrink-0 z-10 animate-pulse scale-105">
                            6
                        </div>
                    @else
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-100 border border-slate-300 text-slate-400 font-bold text-xs flex items-center justify-center shadow-xs shrink-0 z-10">
                            6
                        </div>
                    @endif
                    <div class="hidden md:block absolute left-full w-5 lg:w-6 border-t-2 {{ ($state6 === 'active' || $state6 === 'completed') ? 'border-emerald-400' : 'border-dashed border-slate-300' }}"></div>
                </div>

                <div class="w-full md:w-1/2 md:pl-7 text-left pl-10 md:pl-7">
                    <div class="inline-block md:max-w-sm p-2.5 sm:p-3 rounded-xl transition duration-150
                        {{ ($state6 === 'active' || $state6 === 'completed') ? 'bg-emerald-50/90 border-2 border-emerald-500 shadow-xs' : 'bg-white/80 border border-slate-200 opacity-60' }}">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold uppercase
                                {{ ($state6 === 'active' || $state6 === 'completed') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ ($state6 === 'active' || $state6 === 'completed') ? '🎉 Onboarding Resmi' : 'Antre' }}
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-[13px] font-extrabold {{ ($state6 === 'active' || $state6 === 'completed') ? 'text-emerald-900' : 'text-slate-700' }}">
                            Tahap 6: Surat Tugas &amp; Onboarding
                        </h4>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
                            Penerbitan Surat Penerimaan resmi dan persiapan hari pertama kerja/magang.
                        </p>
                        @if($isAccepted && $application)
                            <div class="mt-1.5 flex">
                                <a href="{{ $downloadRoute }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[10.5px] font-bold transition shadow-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh Surat Penerimaan (PDF)</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
