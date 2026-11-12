<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-black text-[#0B1B3D] tracking-tight">Dashboard Ringkasan Karier</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                Politeknik Negeri Padang &bull; Jurusan {{ $profile->jurusan ?? 'Teknologi Informasi' }} &bull; {{ $profile->prodi ?? 'D4 Rekayasa Perangkat Lunak' }} ({{ $profile->semester ?? 'Semester 7' }})
            </p>
        </div>
        <div class="flex items-center gap-2.5 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-600 shadow-sm">
                {{ $user->nim ? 'NIM. ' . $user->nim : 'NIM Belum Terdaftar' }}
            </span>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-xs font-bold text-[#004AAD] shadow-sm">
                Bidang {{ strtoupper($profile->target_field ?? 'IT') }}
            </span>
        </div>
    </div>

    <!-- Upcoming Interviews Alert Banner -->
    @if(isset($upcomingInterviews) && $upcomingInterviews->isNotEmpty())
        <div class="mb-8 bg-[#0B1B3D] text-white rounded-3xl p-6 sm:p-7 shadow-xl border border-[#004AAD]/40 relative overflow-hidden">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 border-b border-white/10 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-500/30 border border-purple-400/40 text-purple-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                            <span>Undangan & Jadwal Wawancara Anda</span>
                            <span class="px-2 py-0.5 rounded-full bg-purple-400/20 text-purple-200 border border-purple-400/30 text-[10px] font-bold uppercase tracking-wider">{{ $upcomingInterviews->count() }} Terjadwal</span>
                        </h2>
                        <p class="text-xs text-purple-200/80 mt-0.5">Mitra telah menetapkan tanggal wawancara untuk lamaran pekerjaan Anda.</p>
                    </div>
                </div>
                <a href="{{ route('applications.index') }}" class="text-xs font-bold text-purple-300 hover:text-white underline flex items-center gap-1 self-start sm:self-auto transition">
                    Lihat Semua Lamaran &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($upcomingInterviews as $interview)
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 flex flex-col justify-between hover:bg-white/[0.15] transition">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-300">{{ $interview->jobListing->department }}</span>
                                    <h3 class="text-sm font-extrabold text-white leading-tight mt-0.5">{{ $interview->jobListing->title }}</h3>
                                    <p class="text-xs text-slate-300 font-medium">{{ $interview->jobListing->user->name ?? 'Mitra Industri' }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold border shrink-0 {{ $interview->status === 'Wawancara' ? 'bg-purple-500/20 text-purple-200 border-purple-400/30' : 'bg-indigo-500/20 text-indigo-200 border-indigo-400/30' }}">
                                        {{ $interview->status }}
                                    </span>
                                    @if($interview->status === 'Wawancara')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $interview->isOfflineInterview() ? 'bg-amber-400/20 text-amber-200 border-amber-300/30' : 'bg-blue-400/20 text-blue-200 border-blue-300/30' }}">
                                            {{ $interview->isOfflineInterview() ? '🏢 Offline' : '🌐 Online' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Detail Jadwal Wawancara -->
                            <div class="mt-3 p-3 rounded-xl bg-black/25 border border-white/5 space-y-1.5">
                                <div class="flex items-center gap-2 text-xs text-white">
                                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="font-bold text-purple-100">
                                        {{ \Carbon\Carbon::parse($interview->interview_date)->translatedFormat('l, d F Y - H:i') }} WIB
                                    </span>
                                </div>
                                @if($interview->isOfflineInterview())
                                    <div class="flex items-start gap-2 text-xs text-slate-200 mt-1">
                                        <span class="shrink-0 text-amber-400 font-bold">📍 Lokasi:</span>
                                        <span class="font-medium text-slate-200 leading-snug">
                                            {{ $interview->interview_location ?: ($interview->jobListing->location ? 'Kantor Mitra ('.$interview->jobListing->location.')' : 'Kantor / Lokasi Mitra') }}
                                        </span>
                                    </div>
                                    @if($interview->interview_link)
                                        <div class="flex items-center gap-2 text-xs pt-0.5">
                                            <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <a href="{{ $interview->interview_link }}" target="_blank" class="text-sky-300 hover:text-white underline font-semibold truncate">
                                                Buka Tautan Lokasi / Info &rarr;
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    @if($interview->interview_link)
                                        <div class="flex items-center gap-2 text-xs">
                                            <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <a href="{{ $interview->interview_link }}" target="_blank" class="text-sky-300 hover:text-white underline font-semibold truncate">
                                                Buka Tautan Meeting / Wawancara &rarr;
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Konfirmasi Kehadiran -->
                        <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between flex-wrap gap-2">
                            <div class="text-[11px] text-purple-200">
                                @if($interview->interview_status === 'Hadir')
                                    <span class="inline-flex items-center gap-1 text-emerald-300 font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        Kehadiran Dikonfirmasi Hadir
                                    </span>
                                @elseif($interview->interview_status === 'Tidak Hadir')
                                    <span class="inline-flex items-center gap-1 text-rose-300 font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Jadwal Ditolak / Batal
                                    </span>
                                @else
                                    <span class="text-amber-300 font-semibold">Menunggu konfirmasi Anda</span>
                                @endif
                            </div>

                            @if($interview->interview_status === 'Menunggu Konfirmasi' || !$interview->interview_status)
                                <div class="flex items-center gap-1.5">
                                    <form action="{{ route('applications.confirm', $interview) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="interview_status" value="Tidak Hadir" onclick="return confirm('Apakah Anda yakin tidak bersedia mengikuti jadwal ini?')" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-200 hover:text-white text-xs font-bold transition border border-rose-400/30">
                                            ✕ Tidak Bersedia
                                        </button>
                                    </form>
                                    <form action="{{ route('applications.confirm', $interview) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="interview_status" value="Hadir" onclick="return confirm('Konfirmasi bahwa Anda bersedia mengikuti jadwal ini?')" class="px-3 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition shadow-sm flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Konfirmasi Hadir
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 5-Step Pipeline Stepper Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-slate-200 mb-8 relative overflow-hidden">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6 pb-5 border-b border-slate-200">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-[#004AAD] border border-blue-200 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    Alur Penilaian & Kesiapan Kerja Mahasiswa
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Tahapan sistematis: Profil & Portofolio &rarr; Asesmen AI Gemini &rarr; Skill Gap &rarr; Rekomendasi &rarr; Roadmap Aksi &rarr; CV ATS
                </p>
            </div>
            <div class="flex items-center gap-2 self-start md:self-auto bg-white px-3.5 py-1.5 rounded-xl border border-slate-200">
                <span class="text-xs text-slate-500">Total Kesiapan:</span>
                <span class="text-sm font-black text-[#FF44B7]">{{ $employability['total'] }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5">
            <!-- Profil Mahasiswa -->
            <div class="p-4 rounded-2xl border {{ $profile->target_occupation_id ? 'bg-white border-[#004AAD]/40' : 'bg-white border-amber-500/40' }} flex flex-col justify-between transition hover:border-[#004AAD]">
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="p-1.5 rounded-lg bg-[#004AAD]/10 text-[#004AAD] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $profile->target_occupation_id ? 'text-[#004AAD]' : 'text-amber-700' }}">
                            {{ $profile->target_occupation_id ? 'Lengkap' : 'Perlu Dilengkapi' }}
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-[#0B1B3D]">Profil Mahasiswa</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Data akademik & preferensi</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Bobot: Profil 25%</p>
                </div>
                <div class="mt-3.5 pt-2.5 border-t border-slate-200">
                    <a href="{{ route('profile.edit') }}" class="text-[11px] font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                        Kelola Profil &rarr;
                    </a>
                </div>
            </div>

            <!-- Portofolio Proyek -->
            <div class="p-4 rounded-2xl border {{ $portfolioCount > 0 ? 'bg-white border-[#004AAD]/40' : 'bg-white border-amber-500/40' }} flex flex-col justify-between transition hover:border-[#004AAD]">
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="p-1.5 rounded-lg bg-[#004AAD]/10 text-[#004AAD] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $portfolioCount > 0 ? 'text-[#004AAD]' : 'text-amber-700' }}">
                            {{ $portfolioCount > 0 ? 'Lengkap ' . $employability['components']['portfolio'] . '%' : 'Perlu Ditambah' }}
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-[#0B1B3D]">Portofolio Proyek</h3>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $portfolioCount }} Proyek terdaftar &bull; {{ $employability['components']['portfolio'] }}%</p>
                    <div class="w-full h-1.5 bg-slate-100 border border-slate-200 rounded-full overflow-hidden mt-2 mb-1">
                        <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500" style="width: {{ $employability['components']['portfolio'] }}%;"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-0.5">Bobot: Portofolio 35%</p>
                </div>
                <div class="mt-3.5 pt-2.5 border-t border-slate-200">
                    <a href="{{ route('portfolio') }}" class="text-[11px] font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                        Kelola Portofolio &rarr;
                    </a>
                </div>
            </div>

            <!-- Asesmen Skill (AI) -->
            <div class="p-4 rounded-2xl border {{ $latestAssessment ? 'bg-white border-[#004AAD]/40' : 'bg-white border-sky-600/30' }} flex flex-col justify-between transition hover:border-sky-400">
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="p-1.5 rounded-lg bg-accent/10 text-sky-700 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $latestAssessment ? 'text-[#004AAD]' : 'text-sky-700' }}">
                            {{ $latestAssessment ? 'Selesai' : 'Siap Diuji' }}
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-[#0B1B3D]">Asesmen Skill (AI)</h3>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ $latestAssessment ? 'Skor Asesmen: ' . $latestAssessment->total_score . '%' : 'Soal dinamis IT/Elektro' }}
                    </p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Bobot: Asesmen 40%</p>
                </div>
                <div class="mt-3.5 pt-2.5 border-t border-slate-200">
                    <a href="{{ route('assessment') }}" class="text-[11px] font-bold text-sky-700 hover:underline flex items-center gap-1">
                        {{ $latestAssessment ? 'Ulangi Asesmen &rarr;' : 'Mulai Asesmen &rarr;' }}
                    </a>
                </div>
            </div>

            <!-- Analisis Skill Gap -->
            <div class="p-4 rounded-2xl border bg-white border-slate-200 flex flex-col justify-between transition hover:border-slate-300">
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="p-1.5 rounded-lg bg-blue-50 text-[#004AAD] flex items-center justify-center border border-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Terhitung</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#0B1B3D]">Analisis Skill Gap</h3>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ $gapData['match_percentage'] ?? 75 }}% Cocok standar
                    </p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Komparasi vs industri</p>
                </div>
                <div class="mt-3.5 pt-2.5 border-t border-slate-200">
                    <a href="{{ route('skill-gap') }}" class="text-[11px] font-bold text-slate-600 hover:text-[#0B1B3D] hover:underline flex items-center gap-1">
                        Lihat Gap &rarr;
                    </a>
                </div>
            </div>

            <!-- Rekomendasi Karier -->
            <div class="p-4 rounded-2xl border bg-white border-slate-200 flex flex-col justify-between transition hover:border-slate-300">
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="p-1.5 rounded-lg bg-blue-50 text-[#004AAD] flex items-center justify-center border border-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tersedia</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#0B1B3D]">Rekomendasi Karier</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Peluang kerja relevan</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Disesuaikan asesmen</p>
                </div>
                <div class="mt-3.5 pt-2.5 border-t border-slate-200">
                    <a href="{{ route('recommendation') }}" class="text-[11px] font-bold text-slate-600 hover:text-[#0B1B3D] hover:underline flex items-center gap-1">
                        Eksplor Karier &rarr;
                    </a>
                </div>
            </div>

            <!-- Career Roadmap -->
            <div class="p-4 rounded-2xl border bg-white border-slate-200 flex flex-col justify-between transition hover:border-slate-300">
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="p-1.5 rounded-lg bg-blue-50 text-[#004AAD] flex items-center justify-center border border-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Aktif</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#0B1B3D]">Career Roadmap</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Akselerasi pekerjaan</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Panduan penutupan gap</p>
                </div>
                <div class="mt-3.5 pt-2.5 border-t border-slate-200">
                    <a href="{{ route('roadmap') }}" class="text-[11px] font-bold text-slate-600 hover:text-[#0B1B3D] hover:underline flex items-center gap-1">
                        Buka Roadmap &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Cards Row -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
        <!-- Employability Score Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 shadow-lg border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 flex flex-col justify-between hover:shadow-xl transition-all">
            <div class="flex items-center gap-6">
                <div class="relative w-[80px] h-[80px] flex items-center justify-center shrink-0">
                    <svg class="w-full h-full transform -rotate-90 absolute inset-0" viewBox="0 0 36 36">
                        <path class="text-slate-100" stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor" stroke-width="3.5" fill="none"/>
                        <path class="text-[#004AAD]" stroke-dasharray="{{ $employability['total'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                    </svg>
                    <span class="text-2xl font-black text-[#0B1B3D]">{{ $employability['total'] }}</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[#004AAD] tracking-widest uppercase mb-1">Employability Score</p>
                    <h3 class="text-lg font-extrabold text-[#0B1B3D] leading-tight">{{ $employability['level'] }}</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $employability['description'] }}</p>
                </div>
            </div>

            <!-- Breakdown chips -->
            <div class="mt-6 pt-5 border-t border-slate-200 grid grid-cols-3 gap-2.5 text-center">
                <div class="bg-white rounded-xl p-2.5 border border-slate-200">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Asesmen (40%)</p>
                    <p class="text-sm font-black text-white mt-0.5">{{ $employability['components']['assessment'] }}%</p>
                </div>
                <div class="bg-white rounded-xl p-2.5 border border-slate-200">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Portofolio (35%)</p>
                    <p class="text-sm font-black text-white mt-0.5">{{ $employability['components']['portfolio'] }}%</p>
                </div>
                <div class="bg-white rounded-xl p-2.5 border border-slate-200">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Profil (25%)</p>
                    <p class="text-sm font-black text-white mt-0.5">{{ $employability['components']['profile'] }}%</p>
                </div>
            </div>
        </div>

        <!-- Profil Sasaran Card -->
        <div class="lg:col-span-3 bg-white rounded-3xl p-6 sm:p-7 shadow-lg border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 flex flex-col justify-between relative overflow-hidden group hover:shadow-xl transition-all duration-500">
            
            <div>
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="w-5 h-5 rounded-md bg-blue-50 text-[#004AAD] border border-blue-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <p class="text-[10px] font-bold text-[#004AAD] tracking-widest uppercase">Target Profesi Utama</p>
                </div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-[#0B1B3D] tracking-tight">{{ $targetOccupation->title ?? 'Full Stack Web Developer' }}</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-1.5">
                    Kategori: {{ $targetOccupation->category ?? 'Software Engineering' }} &bull; Permintaan Industri: <strong class="text-[#004AAD]">{{ $targetOccupation->demand_level ?? 'Sangat Tinggi' }}</strong>
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6 pt-5 border-t border-slate-200">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-600">Kesesuaian Kompetensi:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-50 text-[#004AAD] border border-blue-200 text-xs font-extrabold border border-[#004AAD]/30">
                        {{ $gapData['match_percentage'] ?? 78 }}% Cocok
                    </span>
                </div>
                <a href="{{ route('skill-gap') }}" class="text-xs font-bold text-[#004AAD] hover:text-[#38bdf8] transition flex items-center gap-1">
                    Detail Gap Kompetensi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Action Buttons Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Mulai Uji Asesmen -->
        <a href="{{ route('assessment') }}" class="bg-white rounded-2xl p-5 border border-slate-200 hover:border-[#004AAD]/60 shadow-lg hover:shadow-[#004AAD]/10 transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/25 shrink-0 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0B1B3D] group-hover:text-[#004AAD] transition">Uji Kompetensi AI (Gemini)</h4>
                <p class="text-xs text-slate-500 mt-0.5">Soal adaptif bidang IT & Elektro</p>
            </div>
        </a>

        <!-- Kelola Portofolio -->
        <a href="{{ route('portfolio') }}" class="bg-white rounded-2xl p-5 border border-slate-200 hover:border-[#004AAD]/60 shadow-lg hover:shadow-[#004AAD]/10 transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/25 shrink-0 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0B1B3D] group-hover:text-[#004AAD] transition">Portofolio Proyek</h4>
                <p class="text-xs text-slate-500 mt-0.5">{{ $portfolioCount }} Proyek riil tersimpan</p>
            </div>
        </a>

        <!-- Career Roadmap -->
        <a href="{{ route('roadmap') }}" class="bg-[#004AAD] hover:bg-[#003B8B] text-white rounded-2xl p-5 shadow-lg shadow-[#004AAD]/25 transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-950/20 border border-white/20 flex items-center justify-center text-white shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-white">Buka Career Roadmap</h4>
                <p class="text-xs text-white/90 font-medium mt-0.5">Panduan penutupan skill gap</p>
            </div>
        </a>
    </div>

    <!-- Chart / Evaluasi Gap Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 hover:shadow-xl transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b border-slate-200 gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-extrabold text-[#0B1B3D] tracking-tight">
                    Evaluasi Gap Kompetensi: {{ $targetOccupation->title ?? 'Target Profesi' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1">Perbandingan kemampuan riil mahasiswa terhadap standar okupasi industri.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#004AAD]"></span>
                    Tingkat Anda
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Standar Industri
                </div>
            </div>
        </div>

        @if(!empty($gapData['gap_details']))
            <div class="space-y-4">
                @foreach(array_slice($gapData['gap_details'], 0, 5) as $item)
                    <div class="space-y-1.5 p-3 rounded-xl bg-white border border-slate-200/80">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="font-bold text-[#0B1B3D]">{{ $item['skill_name'] }}</span>
                            <div class="flex items-center gap-2.5">
                                <span class="text-[11px] px-2.5 py-0.5 rounded-md font-bold {{ $item['gap'] >= 0 ? 'bg-blue-50 text-[#004AAD] border border-blue-200 border border-[#004AAD]/30' : 'bg-amber-500/20 text-amber-700 border border-amber-500/30' }}">
                                    {{ $item['gap'] >= 0 ? '+'.$item['gap'].'% Standar Terpenuhi' : $item['gap'].'% Perlu Ditingkatkan' }}
                                </span>
                                <span class="font-black text-[#0B1B3D]">{{ $item['student_level'] }}% <span class="text-slate-500 font-normal">/ {{ $item['benchmark'] }}%</span></span>
                            </div>
                        </div>
                        <div class="h-2.5 w-full bg-slate-200 border border-slate-300 rounded-full overflow-hidden flex shadow-inner">
                            <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500" style="width: {{ min(100, $item['student_level']) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <p class="text-sm text-slate-500">Belum ada data evaluasi. Ikuti Asesmen Kompetensi untuk melihat analisis kompetensi terperinci.</p>
                <a href="{{ route('assessment') }}" class="inline-flex items-center gap-2 px-5 py-2.5 mt-4 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Mulai Asesmen Sekarang
                </a>
            </div>
        @endif
</x-student-layout>









