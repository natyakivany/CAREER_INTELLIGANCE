<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Rapor Hasil Uji Kompetensi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">Evaluasi analitis kecakapan teknis & pemecahan masalah berbantuan Gemini AI</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('skill-gap') }}" class="px-6 py-3 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                <span>Lanjut ke Analisis Skill Gap</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Score Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Total Score -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#004AAD] text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-[#004AAD]/20">
                {{ round($assessment->total_score) }}
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Skor Asesmen</p>
                <h3 class="text-sm sm:text-base font-extrabold text-[#0B1B3D]">{{ $assessment->correct_answers }} / {{ $assessment->total_questions }} Benar</h3>
                <p class="text-xs text-[#004AAD] font-semibold">{{ $assessment->total_score >= 75 ? 'Lulus Standar' : 'Perlu Pengayaan' }}</p>
            </div>
        </div>

        <!-- Hard Skill Score -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-500/20 text-purple-400 border border-purple-500/30 font-black text-xl flex items-center justify-center">
                {{ round($assessment->hard_skill_score) }}%
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Hard Skill Teknis</p>
                <h3 class="text-sm sm:text-base font-extrabold text-[#0B1B3D]">Kode & Arsitektur</h3>
                <p class="text-xs text-slate-500">Logika & implementasi</p>
            </div>
        </div>

        <!-- Soft Skill Score -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-700 border border-amber-500/30 font-black text-xl flex items-center justify-center">
                {{ round($assessment->soft_skill_score) }}%
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Soft Skill & Problem Solving</p>
                <h3 class="text-sm sm:text-base font-extrabold text-[#0B1B3D]">Analisis Skenario</h3>
                <p class="text-xs text-slate-500">Etika & mitigasi risiko</p>
            </div>
        </div>

        <!-- Employability Score -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white text-[#004AAD] border border-[#004AAD]/30 font-black text-2xl flex items-center justify-center">
                {{ $assessment->employability_score }}
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Employability Score</p>
                <h3 class="text-sm sm:text-base font-extrabold text-[#0B1B3D]">Kesiapan Kerja</h3>
                <p class="text-xs text-slate-500">Formula 40% + 35% + 25%</p>
            </div>
        </div>

    </div>

    <!-- Gemini AI Diagnostic Feedback Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 text-[#0B1B3D] border border-slate-200 shadow-2xl mb-8 relative overflow-hidden">
        <div class="flex items-center gap-3.5 mb-4 relative z-10">
            <div class="w-11 h-11 rounded-2xl bg-[#004AAD] text-white font-bold flex items-center justify-center shadow-lg shadow-[#004AAD]/20 text-xl">
                🤖
            </div>
            <div>
                <span class="text-[10px] font-extrabold tracking-widest text-[#004AAD] uppercase">Ulasan Diagnostik Gemini AI</span>
                <h2 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D]">Analisis Kompetensi Otentik Mahasiswa</h2>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6 relative z-10">
            {{ $assessment->ai_feedback ?? 'Mahasiswa telah menyelesaikan asesmen kompetensi dengan baik. Data telah dikalibrasi untuk menentukan skill gap dan rekomendasi karier.' }}
        </p>

        <div class="grid sm:grid-cols-2 gap-4 pt-5 border-t border-slate-200 relative z-10">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <span class="text-xs font-bold text-[#004AAD] flex items-center gap-1.5">
                    <span>✓</span> Kekuatan Teridentifikasi
                </span>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $assessment->ai_strengths ?? 'Pemahaman logika dan algoritma yang baik.' }}
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <span class="text-xs font-bold text-amber-700 flex items-center gap-1.5">
                    <span>⚠️</span> Celah yang Perlu Ditingkatkan
                </span>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $assessment->ai_weaknesses ?? 'Implementasi arsitektur sistem skala besar dan pengujian unit.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Review Jawaban Detail -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 space-y-6">
        <h3 class="text-base font-extrabold text-[#0B1B3D] pb-4 border-b border-slate-200 flex items-center justify-between">
            <span>Pembahasan & Kunci Jawaban Soal</span>
            <span class="text-xs font-normal text-slate-500">{{ $assessment->total_questions }} Soal Terverifikasi</span>
        </h3>

        <div class="space-y-5">
            @foreach ($questions as $idx => $q)
                <div class="p-5 sm:p-6 rounded-2xl border {{ $q->is_correct ? 'border-[#004AAD]/40 bg-white' : 'border-rose-500/40 bg-white' }} space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg text-xs font-bold flex items-center justify-center {{ $q->is_correct ? 'bg-[#004AAD] text-white font-black' : 'bg-rose-500 text-white font-bold' }}">
                                {{ $idx + 1 }}
                            </span>
                            <span class="text-xs font-bold text-slate-600">{{ $q->topic ?? 'Pertanyaan' }}</span>
                        </div>
                        <span class="text-xs font-extrabold {{ $q->is_correct ? 'text-[#004AAD]' : 'text-rose-600' }}">
                            {{ $q->is_correct ? '✓ Benar' : '✗ Salah' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm font-semibold text-[#0B1B3D] leading-relaxed">{{ $q->question_text }}</p>

                    <!-- User Answer vs Correct Answer -->
                    <div class="grid sm:grid-cols-2 gap-3 text-xs pt-1">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-500 block font-bold uppercase tracking-wider">Jawaban Anda:</span>
                            <span class="font-bold {{ $q->is_correct ? 'text-[#004AAD]' : 'text-rose-600' }} mt-0.5 block">
                                ({{ $q->user_answer ?? 'Tidak Diisi' }}) {{ $q->options[$q->user_answer] ?? '-' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-[#004AAD]/30">
                            <span class="text-[10px] text-[#004AAD] block font-bold uppercase tracking-wider">Kunci Jawaban Tepat:</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">
                                ({{ $q->correct_option }}) {{ $q->options[$q->correct_option] ?? '-' }}
                            </span>
                        </div>
                    </div>

                    @if ($q->explanation)
                        <div class="p-3 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 leading-relaxed">
                            <strong class="text-[#004AAD]">💡 Penjelasan:</strong> {{ $q->explanation }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('assessment') }}" class="px-5 py-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-[#004AAD] text-xs font-bold rounded-xl transition">
                &larr; Kembali ke Menu Asesmen
            </a>
            <a href="{{ route('skill-gap') }}" class="px-7 py-3 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                <span>Lihat Analisis Celah Kompetensi (Skill Gap) &rarr;</span>
            </a>
        </div>
    </div>
</x-student-layout>





