<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Evaluasi & Skill Assessment Cerdas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">Uji kompetensi adaptif didukung oleh Google Gemini AI untuk memetakan keahlian riil</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-bold text-[#004AAD] shadow-sm">
                Evaluasi Mandiri Mahasiswa
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-[#004AAD]/10 border border-[#004AAD]/30 text-[#004AAD] text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-4 h-4 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Active Assessment Notice -->
    @if ($activeAssessment)
        <div class="bg-white border border-amber-500/40 rounded-3xl p-6 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 text-amber-700 flex items-center justify-center font-bold text-xl">
                    ⏱️
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-[#0B1B3D]">Anda Memiliki Sesi Asesmen yang Sedang Berjalan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Bidang: <strong class="text-amber-700">{{ strtoupper($activeAssessment->field) }}</strong> &bull; {{ $activeAssessment->questions()->count() }} Soal</p>
                </div>
            </div>
            <a href="{{ route('assessment.show', $activeAssessment) }}" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition shadow-md text-center">
                Lanjutkan Mengerjakan &rarr;
            </a>
        </div>
    @endif

    <!-- Prerequisite Checklist Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200 mb-8">
        <h3 class="text-sm sm:text-base font-extrabold text-[#0B1B3D] mb-2 flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-[#004AAD]/20 text-[#004AAD] flex items-center justify-center text-xs">📋</span>
            <span>Status Prasyarat Evaluasi Mahasiswa</span>
        </h3>
        <p class="text-xs text-slate-500 mb-5 leading-relaxed">
            Penilaian kesiapan kerja (<em>Employability Score</em>) menggunakan formula pembobotan terpadu: Asesmen (40%) + Portofolio Proyek (35%) + Profil Akademik (25%).
        </p>
        <div class="grid sm:grid-cols-2 gap-4">
            <!-- Check 1: Profile -->
            <div class="p-4 rounded-2xl border {{ $hasProfile ? 'bg-white border-[#004AAD]/40' : 'bg-white border-rose-500/40' }} flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ $hasProfile ? '✅' : '⚠️' }}</span>
                    <div>
                        @php
                            $isGraduate = in_array($profile->semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']);
                        @endphp
                        <p class="text-xs font-bold {{ $hasProfile ? 'text-[#004AAD]' : 'text-rose-600' }}">Data Profil {{ $isGraduate ? '' : '& NIM' }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            @if ($hasProfile)
                                {{ $isGraduate ? '💼 Fresh Graduate' : 'NIM: ' . Auth::user()->nim }} ({{ $profile->prodi ?? 'Teknik' }})
                            @else
                                {{ $isGraduate ? 'Data profil belum lengkap' : 'NIM belum diisi di profil' }}
                            @endif
                        </p>
                    </div>
                </div>
                @if (! $hasProfile)
                    <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-rose-600 hover:underline">Lengkapi &rarr;</a>
                @endif
            </div>

            <!-- Check 2: Portfolio -->
            <div class="p-4 rounded-2xl border {{ $hasPortfolio ? 'bg-white border-[#004AAD]/40' : 'bg-white border-rose-500/40' }} flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ $hasPortfolio ? '✅' : '⚠️' }}</span>
                    <div>
                        <p class="text-xs font-bold {{ $hasPortfolio ? 'text-[#004AAD]' : 'text-rose-600' }}">Portofolio Karya / Proyek</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            {{ $hasPortfolio ? $portfolios->count() . ' proyek terdaftar' : 'Belum ada proyek terdaftar' }}
                        </p>
                    </div>
                </div>
                @if (! $hasPortfolio)
                    <a href="{{ route('portfolio') }}" class="text-xs font-bold text-rose-600 hover:underline">Tambah &rarr;</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Start New Assessment: Choose Field -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 mb-8"
         x-data="{
            @if($targetField === 'all')
                selectedField: 'all'
            @elseif($targetField === 'elektro')
                selectedField: 'elektro'
            @else
                selectedField: 'it'
            @endif
         }">
        <div class="max-w-2xl mb-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30 text-[10px] font-extrabold uppercase tracking-wider mb-2.5">
                🤖 Google Gemini AI Question Engine
            </span>
            <h2 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D]">Pilih Bidang Uji Kompetensi</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                @if($targetField === 'all')
                    Anda memilih peminatan <strong class="text-[#004AAD]">IT & Elektro</strong>. Asesmen ini akan mencakup soal gabungan dari kedua bidang: 50% <em>hard skill</em> teknis dan 50% <em>soft skill</em> penyelesaian masalah.
                @elseif($targetField === 'elektro')
                    Berdasarkan peminatan profil Anda di bidang <strong class="text-sky-700">Teknik Elektro</strong>, bank soal AI telah dikalibrasi khusus untuk kompetensi sistem tertanam, mikrokontroler, dan otomasi.
                @else
                    Berdasarkan peminatan profil Anda di bidang <strong class="text-[#004AAD]">Teknologi Informasi (IT)</strong>, bank soal AI telah dikalibrasi khusus untuk kompetensi software development, database, dan web.
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('assessment.start') }}">
            @csrf
            <div class="grid {{ $targetField === 'all' ? 'sm:grid-cols-1 max-w-2xl' : 'sm:grid-cols-1 max-w-lg' }} gap-5 mb-6">
                
                @if($targetField === 'it')
                    {{-- Only IT Card --}}
                    <input type="hidden" name="field" value="it">
                    <div class="p-6 rounded-2xl border-2 border-[#004AAD] bg-[#004AAD]/10 shadow-lg shadow-[#004AAD]/10 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-3xl">💻</span>
                                <span class="w-5 h-5 rounded-full border-2 border-[#004AAD] bg-[#004AAD] flex items-center justify-center">
                                    <span class="w-2 h-2 bg-slate-950 rounded-full"></span>
                                </span>
                            </div>
                            <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-1.5">Bidang Teknologi Informasi (IT)</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Mencakup Web Development, RESTful API, Database Design, Logic & Algoritma, dan Git Version Control.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-300/60 flex flex-wrap gap-1.5">
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">Laravel / React</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">SQL & Indexing</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">Docker & DevOps</span>
                        </div>
                    </div>
                @elseif($targetField === 'elektro')
                    {{-- Only Elektro Card --}}
                    <input type="hidden" name="field" value="elektro">
                    <div class="p-6 rounded-2xl border-2 border-sky-500 bg-accent/10 shadow-lg shadow-sky-500/10 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-3xl">⚡</span>
                                <span class="w-5 h-5 rounded-full border-2 border-sky-500 bg-accent flex items-center justify-center">
                                    <span class="w-2 h-2 bg-slate-950 rounded-full"></span>
                                </span>
                            </div>
                            <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-1.5">Bidang Teknik Elektro & Komputer</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Mencakup Sistem Tertanam (Embedded C/C++), Mikrokontroler (ESP32/STM32), Protokol IoT (MQTT/I2C), dan Otomasi PLC.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-300/60 flex flex-wrap gap-1.5">
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">Embedded C</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">ESP32 & Sensors</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">PLC Ladder</span>
                        </div>
                    </div>
                @else
                    {{-- Combined IT & Elektro Card --}}
                    <input type="hidden" name="field" value="all">
                    <div class="p-6 rounded-2xl border-2 border-violet-500 bg-violet-50/50 shadow-sm h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-3xl">💻</span>
                                    <span class="text-lg text-violet-400 font-black">+</span>
                                    <span class="text-3xl">⚡</span>
                                </div>
                                <span class="w-5 h-5 rounded-full border-2 border-violet-500 bg-violet-500 flex items-center justify-center">
                                    <span class="w-2 h-2 bg-slate-950 rounded-full"></span>
                                </span>
                            </div>
                            <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-1.5">Bidang Gabungan: IT & Teknik Elektro</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Soal campuran dari kedua bidang: Web Development, RESTful API, Database, <strong class="text-[#004AAD]">plus</strong> Embedded Systems, Mikrokontroler, Protokol IoT, dan Otomasi PLC.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-300/60 space-y-2">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="text-[10px] px-2 py-0.5 rounded bg-blue-50 text-[#004AAD] border border-blue-200 font-bold">IT</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">Laravel / React</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">SQL & Indexing</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">Docker & DevOps</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="text-[10px] px-2 py-0.5 rounded bg-sky-50 text-sky-700 border border-sky-200 font-bold">ELEKTRO</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">Embedded C</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">ESP32 & Sensors</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-300">PLC Ladder</span>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-200">
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-slate-500">Estimasi waktu pengerjaan: <strong class="text-[#0B1B3D]">30 Menit (20 Soal Dinamis)</strong></span>
                    <span class="text-[11px] text-slate-500">
                        Bidang asesmen ditentukan dari <a href="{{ route('profile.edit') }}" class="text-[#004AAD] hover:underline font-semibold">pengaturan profil Anda</a>. Ubah di sana jika ingin bidang lain.
                    </span>
                </div>
                <button type="submit" class="px-7 py-3.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <span>Mulai Asesmen AI Gemini Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Asesmen -->
    @if ($history->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200">
            <h3 class="text-base font-bold text-[#0B1B3D] mb-4">Riwayat Hasil Asesmen Sebelumnya</h3>
            <div class="divide-y divide-slate-800">
                @foreach ($history as $h)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl {{ $h->field === 'all' ? 'bg-violet-500/15 text-violet-400 border border-violet-500/30' : ($h->field === 'it' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 'bg-accent/15 text-sky-700 border border-sky-500/30') }} flex items-center justify-center font-bold text-xs">
                                {{ $h->field === 'all' ? 'ALL' : ($h->field === 'it' ? 'IT' : 'ELK') }}
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-[#0B1B3D]">
                                    Uji Kompetensi Bidang {{ $h->field === 'all' ? 'IT & Elektro' : strtoupper($h->field) }}
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $h->created_at->format('d M Y, H:i') }} &bull; Status: <span class="text-[#004AAD] font-semibold">{{ ucfirst($h->status) }}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <span class="text-base font-black text-[#0B1B3D]">{{ round($h->total_score) }}/100</span>
                                <p class="text-[10px] text-[#004AAD] font-bold">Skor Kompetensi</p>
                            </div>
                            <a href="{{ route('assessment.result', $h) }}" class="px-4 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-[#004AAD] text-xs font-bold rounded-xl transition">
                                Lihat Rapor &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-student-layout>





