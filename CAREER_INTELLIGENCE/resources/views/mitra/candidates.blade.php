<x-mitra-layout>
    <div x-data="{ 
        showReviewModal: false, 
        selectedApplicant: null,
        openReview(student) {
            this.selectedApplicant = student;
            this.showReviewModal = true;
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Cari Kandidat Mahasiswa</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-accent/15 text-blue-400 border border-blue-500/30">
                        {{ $students->count() }} Mahasiswa
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Temukan talenta terbaik berdasarkan kompetensi, program studi, dan skor kesiapan industri.
                </p>
            </div>
            <a href="{{ route('mitra.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] text-xs font-semibold transition border border-slate-300">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('mitra.candidates') }}" class="mb-8 bg-white p-5 rounded-2xl border border-slate-200 shadow-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Lowongan Buka Anda
                    </label>
                    <select name="job_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-[#0B1B3D] focus:border-[#004AAD] focus:outline-none cursor-pointer">
                        <option value="">Semua Lowongan Buka</option>
                        @foreach($openJobs as $oj)
                            <option value="{{ $oj->id }}" {{ request('job_id') == $oj->id ? 'selected' : '' }}>
                                {{ $oj->title }} ({{ count($oj->skills ?? []) }} Skill)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Cari Skill / Keahlian</label>
                    <input type="text" name="skill" value="{{ request('skill') }}" placeholder="Contoh: Laravel, ESP32, PLC..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-[#0B1B3D] placeholder:text-slate-500 focus:border-[#004AAD] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Program Studi</label>
                    <select name="prodi" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-[#0B1B3D] focus:border-[#004AAD] focus:outline-none cursor-pointer">
                        <option value="">Semua Program Studi</option>
                        @foreach($prodiList as $jurusan => $prodis)
                            <optgroup label="{{ $jurusan }}" class="bg-slate-100 text-[#0B1B3D] font-bold">
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi }}" class="bg-white text-[#0B1B3D] font-normal" {{ request('prodi') === $prodi ? 'selected' : '' }}>
                                        {{ $prodi }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Status Kesiapan</label>
                    <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-[#0B1B3D] focus:border-[#004AAD] focus:outline-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Siap Kerja" {{ request('status') === 'Siap Kerja' ? 'selected' : '' }}>Siap Kerja</option>
                        <option value="Berkembang" {{ request('status') === 'Berkembang' ? 'selected' : '' }}>Berkembang</option>
                        <option value="Baru Mulai" {{ request('status') === 'Baru Mulai' ? 'selected' : '' }}>Baru Mulai</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-[#004AAD] hover:bg-[#003B8B] text-white font-bold rounded-xl text-xs transition shadow-lg shadow-[#004AAD]/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Cari
                    </button>
                    @if(request('job_id') || request('skill') || request('prodi') || request('status'))
                        <a href="{{ route('mitra.candidates') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] text-xs font-semibold transition border border-slate-300">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Specific Job Banner if Selected --}}
        @if($selectedJob)
            <div class="mb-6 p-4 rounded-2xl bg-blue-50/60 border border-blue-200 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#004AAD] text-white">Rekomendasi Khusus</span>
                        <h2 class="text-sm font-bold text-[#0B1B3D]">Menampilkan rekomendasi untuk: <span class="text-[#004AAD]">{{ $selectedJob->title }}</span></h2>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">Departemen: <strong>{{ $selectedJob->department }}</strong> • Tipe: {{ $selectedJob->type }} • Lokasi: {{ $selectedJob->location }}</p>
                </div>
                @if(!empty($selectedJob->skills))
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Kebutuhan Skill:</span>
                        @foreach($selectedJob->skills as $sk)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white text-[#004AAD] border border-blue-200 shadow-xs">{{ $sk }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Candidates Section --}}
        @if($students->isNotEmpty())
            @php
                $topCandidates = $students->take(3);
                $otherCandidates = $students->slice(3);
            @endphp

            {{-- Section: 3 Kandidat Paling Direkomendasikan --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-white flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base sm:text-lg font-extrabold text-[#0B1B3D]">3 Kandidat Paling Direkomendasikan</h2>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                    ⭐ Top Matches
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Talenta terbaik dengan skor kecocokan kompetensi dan kesiapan industri tertinggi untuk lowongan Anda</p>
                        </div>
                    </div>
                </div>

                {{-- Top 3 Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($topCandidates as $idx => $student)
                        @php
                            $rankNumber = $idx + 1;
                            $rankBadge = match($rankNumber) {
                                1 => [
                                    'badge' => 'bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 text-white shadow-amber-500/25 ring-1 ring-amber-400/50',
                                    'border' => 'border-amber-300 ring-2 ring-amber-300/40 bg-gradient-to-b from-amber-50/40 via-white to-white',
                                    'icon' => '🥇',
                                    'label' => 'Rekomendasi Utama #1',
                                    'tag' => 'Pilihan Terbaik'
                                ],
                                2 => [
                                    'badge' => 'bg-gradient-to-r from-slate-500 to-slate-600 text-white shadow-slate-500/25 ring-1 ring-slate-400/50',
                                    'border' => 'border-slate-300 ring-1 ring-slate-200 bg-gradient-to-b from-slate-50/50 via-white to-white',
                                    'icon' => '🥈',
                                    'label' => 'Rekomendasi #2',
                                    'tag' => 'Sangat Cocok'
                                ],
                                3 => [
                                    'badge' => 'bg-gradient-to-r from-amber-700 to-amber-800 text-white shadow-amber-800/25 ring-1 ring-amber-700/50',
                                    'border' => 'border-amber-700/30 ring-1 ring-amber-700/20 bg-gradient-to-b from-amber-50/30 via-white to-white',
                                    'icon' => '🥉',
                                    'label' => 'Rekomendasi #3',
                                    'tag' => 'Unggulan'
                                ],
                            };
                        @endphp

                        <div class="bg-white p-5 rounded-3xl border {{ $rankBadge['border'] }} hover:border-[#004AAD]/60 transition shadow-xl hover:shadow-2xl space-y-3.5 group cursor-pointer flex flex-col relative overflow-hidden"
                             @click="openReview({{ json_encode($student) }})">
                            
                            {{-- Top Rank Badge Header --}}
                            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black {{ $rankBadge['badge'] }} shadow-sm">
                                    <span>{{ $rankBadge['icon'] }}</span>
                                    <span>{{ $rankBadge['label'] }}</span>
                                </span>
                                <span class="text-[10.5px] font-black text-[#004AAD] bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200">
                                    {{ $student['matchScore'] }}% Match
                                </span>
                            </div>

                            {{-- Profile Header --}}
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-[#004AAD] text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-md overflow-hidden relative">
                                    @if(!empty($student['avatarUrl']))
                                        <img src="{{ $student['avatarUrl'] }}" alt="{{ $student['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        {{ collect(explode(' ', $student['name']))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-extrabold text-[#0B1B3D] truncate group-hover:text-[#004AAD] transition">{{ $student['name'] }}</h3>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $student['prodi'] }} • {{ $student['semester'] }}</p>
                                    <p class="text-[10px] text-slate-400">NIM: {{ $student['nim'] }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold shrink-0
                                    {{ $student['status'] === 'Siap Kerja' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($student['status'] === 'Berkembang' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                    {{ $student['status'] }}
                                </span>
                            </div>

                            {{-- Recommended Job Pill --}}
                            @if(!empty($student['recommendedJob']))
                                <div class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-100 flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">
                                            {{ $selectedJob ? 'Kecocokan Lowongan Ini' : 'Rekomendasi Lowongan' }}
                                        </p>
                                        <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ $student['recommendedJob']['job_title'] }}</p>
                                    </div>
                                    <span class="text-xs font-black text-[#004AAD] shrink-0 bg-white px-2 py-0.5 rounded-lg border border-blue-200 shadow-2xs">
                                        {{ $student['matchScore'] }}% Cocok
                                    </span>
                                </div>
                            @endif

                            {{-- Target Profesi --}}
                            <div class="flex items-center gap-1.5 text-[11px]">
                                <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                <span class="text-slate-500">Target:</span>
                                <span class="text-[#0B1B3D] font-semibold truncate">{{ $student['role'] }}</span>
                            </div>

                            {{-- Scores --}}
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-white rounded-xl p-2 text-center border border-slate-200 shadow-2xs">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">Kecocokan</p>
                                    <p class="text-sm font-black {{ $student['matchScore'] >= 70 ? 'text-[#004AAD]' : 'text-amber-700' }}">{{ $student['matchScore'] }}%</p>
                                </div>
                                <div class="bg-white rounded-xl p-2 text-center border border-slate-200 shadow-2xs">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">Kesiapan</p>
                                    <p class="text-sm font-extrabold text-[#0B1B3D]">{{ $student['employabilityScore'] }}%</p>
                                </div>
                                <div class="bg-white rounded-xl p-2 text-center border border-slate-200 shadow-2xs">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">Portofolio</p>
                                    <p class="text-sm font-extrabold text-[#0B1B3D]">{{ $student['portfolioCount'] }}</p>
                                </div>
                            </div>

                            {{-- Riwayat Tanggal Asesmen Terbaru --}}
                            @if(!empty($student['assessmentDate']))
                                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1.5 px-0.5 border-t border-slate-100">
                                    <span class="flex items-center gap-1 font-medium text-slate-500">
                                        <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                        Asesmen:
                                    </span>
                                    <span class="font-bold text-[#0B1B3D]" title="{{ $student['assessmentDateFull'] }}">
                                        {{ $student['assessmentDate'] }} <span class="text-slate-400 font-normal">({{ $student['assessmentDateHuman'] }})</span>
                                    </span>
                                </div>
                            @endif

                            {{-- Skills --}}
                            <div class="mt-auto pt-2 space-y-1.5">
                                @if(!empty($student['recommendedJob']['matched_skills']))
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="text-[9px] font-bold text-emerald-700">Skill Cocok:</span>
                                        @foreach($student['recommendedJob']['matched_skills'] as $mskill)
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9.5px] font-bold border border-emerald-200 flex items-center gap-0.5">
                                                <span>✓</span> {{ $mskill }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if(count($student['topSkills']) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($student['topSkills'] as $skill)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-50 text-slate-600 text-[10px] font-medium border border-slate-200">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="pt-2 border-t border-slate-100 flex items-center gap-2">
                                <button type="button" class="flex-1 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#004AAD] text-xs font-bold transition flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Quick Review
                                </button>
                                <a :href="'/mitra/candidates/' + {{ $student['id'] }} + '{{ $selectedJob ? '?job_id=' . $selectedJob->id : (!empty($student['recommendedJob']['job_id']) ? '?job_id=' . $student['recommendedJob']['job_id'] : '') }}'" 
                                   @click.stop
                                   class="px-3.5 py-2 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition flex items-center gap-1 shadow-xs" title="Lihat Profil Lengkap">
                                    <span>Profil</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Section: Kandidat Mahasiswa Lainnya --}}
            @if($otherCandidates->isNotEmpty())
                <div class="mt-8 mb-4 flex items-center justify-between pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-extrabold text-[#0B1B3D] flex items-center gap-2">
                            <span>Kandidat Mahasiswa Lainnya</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $otherCandidates->count() }} Mahasiswa
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar talenta mahasiswa lainnya yang juga memenuhi kriteria pencarian</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($otherCandidates as $student)
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 transition shadow-xl space-y-3 group cursor-pointer flex flex-col"
                             @click="openReview({{ json_encode($student) }})">
                            {{-- Header --}}
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl bg-[#004AAD] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow overflow-hidden">
                                    @if(!empty($student['avatarUrl']))
                                        <img src="{{ $student['avatarUrl'] }}" alt="{{ $student['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        {{ collect(explode(' ', $student['name']))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-[#0B1B3D] truncate group-hover:text-[#004AAD] transition">{{ $student['name'] }}</h3>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $student['prodi'] }} • {{ $student['semester'] }}</p>
                                    <p class="text-[10px] text-slate-500">NIM: {{ $student['nim'] }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold shrink-0
                                    {{ $student['status'] === 'Siap Kerja' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($student['status'] === 'Berkembang' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                    {{ $student['status'] }}
                                </span>
                            </div>

                            {{-- Recommended Job Pill --}}
                            @if(!empty($student['recommendedJob']))
                                <div class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-100 flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">
                                            {{ $selectedJob ? 'Kecocokan Lowongan Ini' : 'Rekomendasi Lowongan' }}
                                        </p>
                                        <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ $student['recommendedJob']['job_title'] }}</p>
                                    </div>
                                    <span class="text-xs font-black text-[#004AAD] shrink-0 bg-white px-2 py-0.5 rounded-lg border border-blue-200 shadow-xs">
                                        {{ $student['matchScore'] }}% Cocok
                                    </span>
                                </div>
                            @endif

                            {{-- Target Profesi --}}
                            <div class="flex items-center gap-1.5 text-[11px]">
                                <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                <span class="text-slate-500">Target:</span>
                                <span class="text-[#0B1B3D] font-semibold truncate">{{ $student['role'] }}</span>
                            </div>

                            {{-- Scores --}}
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-white rounded-lg p-2 text-center border border-slate-200">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">Kecocokan</p>
                                    <p class="text-sm font-extrabold {{ $student['matchScore'] >= 70 ? 'text-[#004AAD]' : 'text-amber-700' }}">{{ $student['matchScore'] }}%</p>
                                </div>
                                <div class="bg-white rounded-lg p-2 text-center border border-slate-200">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">Kesiapan</p>
                                    <p class="text-sm font-extrabold text-[#0B1B3D]">{{ $student['employabilityScore'] }}%</p>
                                </div>
                                <div class="bg-white rounded-lg p-2 text-center border border-slate-200">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">Portofolio</p>
                                    <p class="text-sm font-extrabold text-[#0B1B3D]">{{ $student['portfolioCount'] }}</p>
                                </div>
                            </div>

                            {{-- Riwayat Tanggal Asesmen Terbaru --}}
                            @if(!empty($student['assessmentDate']))
                                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1.5 px-0.5 border-t border-slate-100">
                                    <span class="flex items-center gap-1 font-medium text-slate-500">
                                        <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                        Asesmen:
                                    </span>
                                    <span class="font-bold text-[#0B1B3D]" title="{{ $student['assessmentDateFull'] }}">
                                        {{ $student['assessmentDate'] }} <span class="text-slate-400 font-normal">({{ $student['assessmentDateHuman'] }})</span>
                                    </span>
                                </div>
                            @endif

                            {{-- Skills --}}
                            <div class="mt-auto pt-2 space-y-1.5">
                                @if(!empty($student['recommendedJob']['matched_skills']))
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="text-[9px] font-bold text-emerald-700">Skill Cocok:</span>
                                        @foreach($student['recommendedJob']['matched_skills'] as $mskill)
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9.5px] font-bold border border-emerald-200 flex items-center gap-0.5">
                                                <span>✓</span> {{ $mskill }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if(count($student['topSkills']) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($student['topSkills'] as $skill)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-50 text-slate-600 text-[10px] font-medium border border-slate-200">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#004AAD] text-white flex items-center justify-center shadow-md">
                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">Tidak ada kandidat ditemukan.</p>
                <p class="text-slate-500 text-xs mt-1">Coba ubah kata kunci atau filter pencarian Anda.</p>
            </div>
        @endif

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
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Kesesuaian Lowongan Buka</p>
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

                    <!-- Riwayat Tanggal Asesmen AI Terbaru -->
                    <template x-if="selectedApplicant?.assessmentDate">
                        <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200 flex items-center justify-between gap-2 text-xs">
                            <span class="text-slate-600 flex items-center gap-1.5 font-medium">
                                <svg class="w-4 h-4 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                <span>Tanggal Asesmen Terbaru:</span>
                            </span>
                            <span class="font-bold text-[#0B1B3D] bg-white px-2.5 py-1 rounded-lg border border-blue-200 shadow-2xs" x-text="selectedApplicant?.assessmentDateFull + ' (' + selectedApplicant?.assessmentDateHuman + ')'"></span>
                        </div>
                    </template>

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

                    <!-- Close -->
                    <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                        <a :href="'/mitra/candidates/' + selectedApplicant?.id + (selectedApplicant?.recommendedJob?.job_id ? '?job_id=' + selectedApplicant.recommendedJob.job_id : '')" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Lihat Profil & Hubungi
                        </a>
                        <button type="button" @click="showReviewModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] text-xs font-bold transition">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-mitra-layout>







