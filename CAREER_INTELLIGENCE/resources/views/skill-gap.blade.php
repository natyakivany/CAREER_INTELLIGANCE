<x-student-layout>
    @php
        $occupation = $gapData['occupation'] ?? null;
        $match = $gapData['match_percentage'] ?? 75;
        $gapDetails = $gapData['gap_details'] ?? [];
        $aiRecommendation = $gapData['ai_recommendation'] ?? null;
    @endphp

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Analisis Celah Kemampuan (Skill Gap)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">Pemetaan perbandingan kompetensi riil Anda terhadap standar kualifikasi industri kerja</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-bold text-[#004AAD] shadow-sm">
                Standar Industri
            </span>
        </div>
    </div>

    <!-- Target Occupation Selector Bar -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-lg border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ ($occupation->field ?? 'it') === 'it' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 'bg-accent/15 text-sky-700 border border-sky-500/30' }}">
                    {{ strtoupper($occupation->field ?? 'it') }} &bull; {{ $occupation->category ?? 'Spesialisasi' }}
                </span>
                <span class="text-xs text-slate-500">&bull; Permintaan Pasar: <strong class="text-[#0B1B3D]">{{ $occupation->demand_level ?? 'Tinggi' }}</strong></span>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-[#0B1B3D]">{{ $occupation->title ?? 'Pilih Profesi Sasaran' }}</h2>
            <p class="text-xs sm:text-sm text-slate-500 max-w-2xl">{{ $occupation->description ?? '' }}</p>
        </div>

        <form method="GET" action="{{ route('skill-gap') }}" class="flex items-center gap-3 shrink-0">
            <select name="occupation_id" onchange="this.form.submit()" class="text-xs font-bold px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition bg-white text-[#0B1B3D]">
                @foreach ($occupations as $occ)
                    <option value="{{ $occ->id }}" {{ ($occupation->id ?? null) == $occ->id ? 'selected' : '' }}>
                        [{{ strtoupper($occ->field) }}] {{ $occ->title }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Matching Gauge & Summary Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Left: Match Gauge Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 text-[#0B1B3D] border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 shadow-lg flex flex-col justify-between relative overflow-hidden">
            <div>
                <span class="text-[10px] font-bold tracking-widest text-[#004AAD] uppercase">Tingkat Kecocokan</span>
                <h3 class="text-lg font-extrabold text-[#0B1B3D] mt-1">Matching Score</h3>
                <p class="text-xs text-slate-500 mt-1">Kesesuaian profil Anda saat ini terhadap posisi {{ $occupation->title ?? '' }}.</p>
            </div>

            <div class="my-8 flex items-center justify-center">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <path class="text-slate-100" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-[#004AAD]" stroke-dasharray="{{ $match }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-black text-[#0B1B3D]">{{ round($match) }}%</span>
                        <span class="text-xs font-bold text-[#004AAD] mt-0.5">{{ $match >= 75 ? 'Sangat Cocok' : 'Potensial' }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Celah Kompetensi (Gap):</span>
                    <strong class="text-amber-700">{{ round(100 - $match) }}% Perlu Dilatih</strong>
                </div>
            </div>
        </div>

        <!-- Right: Detailed Skill Breakdown -->
        <div class="lg:col-span-2 space-y-6">
            
            @if($aiRecommendation)
            <div class="bg-blue-50/60 border border-blue-200 rounded-3xl p-5 sm:p-6 shadow-sm">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 text-[#004AAD]">
                        <svg class="w-5 h-5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#004AAD] mb-1">Analisis Kualitatif Penilaian</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $aiRecommendation }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-[#0B1B3D]">Perbandingan Celah Tiap Kompetensi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Standar benchmark industri (garis abu-abu) vs kemampuan terdeteksi (garis hijau)</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-xl bg-white border border-slate-200 text-slate-600">
                    {{ count($gapDetails) }} Indikator
                </span>
            </div>

            <div class="space-y-4">
                @foreach ($gapDetails as $detail)
                    <div class="space-y-2 p-3.5 rounded-2xl bg-white border border-slate-200">
                        <div class="flex justify-between items-center text-xs sm:text-sm">
                            <span class="font-bold text-[#0B1B3D]">{{ $detail['skill_name'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-semibold text-slate-500">
                                    Anda: <strong class="text-[#0B1B3D]">{{ $detail['student_level'] }}%</strong> / Standar: {{ $detail['benchmark'] }}%
                                </span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold {{ $detail['gap'] >= 0 ? 'bg-[#004AAD]/20 text-[#004AAD] border border-[#004AAD]/30' : 'bg-amber-500/20 text-amber-700 border border-amber-500/30' }}">
                                    {{ $detail['gap'] >= 0 ? '✓ Memenuhi' : 'Gap: ' . $detail['gap'] . '%' }}
                                </span>
                            </div>
                        </div>

                        <!-- Double Progress Bar (Benchmark vs Student) -->
                        <div class="w-full bg-slate-100 border border-slate-200 h-3 rounded-full overflow-hidden relative mb-2">
                            <!-- Benchmark marker -->
                            <div class="absolute top-0 bottom-0 bg-slate-300 opacity-80 rounded-full" style="width: {{ $detail['benchmark'] }}%;"></div>
                            <!-- Student actual mastery -->
                            <div class="absolute top-0 bottom-0 {{ $detail['gap'] >= 0 ? 'bg-[#004AAD]' : 'bg-amber-500' }} rounded-full" style="width: {{ $detail['student_level'] }}%;"></div>
                        </div>

                        @if(isset($detail['reason']))
                        <p class="text-[11px] text-slate-500 mt-2 bg-white p-2 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-600">Catatan AI:</span> {{ $detail['reason'] }}
                        </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
                <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('recommendation') }}" class="px-5 py-2.5 bg-white hover:bg-blue-50 border border-slate-300 text-slate-600 hover:text-[#004AAD] text-xs font-bold rounded-xl transition">
                        Lihat Rekomendasi Karier Lainnya &rarr;
                    </a>
                    <a href="{{ route('recommendation') }}" class="px-7 py-3 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                        <span>Lihat Rekomendasi Karier &rarr;</span>
                    </a>
                </div>
            </div>

    </div>
</x-student-layout>





