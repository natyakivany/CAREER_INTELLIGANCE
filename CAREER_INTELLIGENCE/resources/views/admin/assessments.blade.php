@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                <span>Pengawasan Asesmen Gemini AI</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-500/15 text-purple-400 rounded-full border border-purple-500/30">
                    {{ $assessments->total() }} Sesi Uji
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pantau riwayat pengerjaan soal adaptif, jawaban mahasiswa, dan evaluasi kekuatan/kelemahan dari Gemini AI.
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ selectedAssessment: null, deleteModalOpen: false, assessmentToDelete: null }">

    <!-- Search & Filter Controls -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-md">
        <form method="GET" action="{{ route('admin.assessments') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa atau NIM..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD]">
            </div>

            <div class="sm:col-span-4">
                <select name="field" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="">Semua Bidang Keahlian</option>
                    <option value="it" {{ request('field') === 'it' ? 'selected' : '' }}>Teknologi Informasi</option>
                    <option value="elektro" {{ request('field') === 'elektro' ? 'selected' : '' }}>Teknik Elektro</option>
                    <option value="all" {{ request('field') === 'all' ? 'selected' : '' }}>Gabungan (IT & Elektro)</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-md">
                    Cari
                </button>
                @if (request()->hasAny(['search', 'field']))
                    <a href="{{ route('admin.assessments') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-xl transition" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Assessments Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase bg-white text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Mahasiswa Peserta</th>
                        <th class="py-3.5 px-3">Bidang Uji</th>
                        <th class="py-3.5 px-3 text-center">Skor Total</th>
                        <th class="py-3.5 px-3 text-center">Hard Skill / Soft Skill</th>
                        <th class="py-3.5 px-3 text-center">Status</th>
                        <th class="py-3.5 px-3 text-center">Waktu Pengerjaan</th>
                        <th class="py-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($assessments as $asm)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#004AAD] font-extrabold flex items-center justify-center text-xs shrink-0 border border-blue-200 shadow-sm">
                                        {{ strtoupper(substr($asm->user?->name ?? 'M', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#0B1B3D] text-xs sm:text-sm">{{ $asm->user?->name ?? 'Mahasiswa' }}</p>
                                        <p class="text-[10px] text-slate-500">NIM: {{ $asm->user?->nim ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-3">
                                @if($asm->field === 'all')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-purple-500/10 text-purple-600 border border-purple-500/20">Gabungan (IT & Elektro)</span>
                                @elseif($asm->field === 'elektro')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-cyan-500/10 text-cyan-600 border border-cyan-500/20">Teknik Elektro</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20">Teknologi Informasi</span>
                                @endif
                            </td>

                            <td class="py-4 px-3 text-center">
                                <span class="text-sm font-black text-[#004AAD]">{{ round($asm->total_score) }}</span>
                                <span class="text-[10px] text-slate-500">/ 100</span>
                            </td>

                            <td class="py-4 px-3 text-center">
                                <span class="text-slate-600 font-medium">{{ round($asm->hard_skill_score) }}%</span>
                                <span class="text-slate-500">/</span>
                                <span class="text-teal-400 font-medium">{{ round($asm->soft_skill_score) }}%</span>
                            </td>

                            <td class="py-4 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $asm->status === 'completed' ? 'bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ $asm->status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
                                </span>
                            </td>

                            <td class="py-4 px-3 text-center text-slate-500 text-[11px]">
                                {{ $asm->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="py-4 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Detail Modal Trigger -->
                                    <button type="button" @click="selectedAssessment = {{ json_encode([
                                        'student_name' => $asm->user?->name,
                                        'nim' => $asm->user?->nim,
                                        'field' => strtoupper($asm->field),
                                        'total_score' => round($asm->total_score),
                                        'hard_skill_score' => round($asm->hard_skill_score),
                                        'soft_skill_score' => round($asm->soft_skill_score),
                                        'ai_feedback' => $asm->ai_feedback,
                                        'ai_strengths' => $asm->ai_strengths,
                                        'ai_weaknesses' => $asm->ai_weaknesses,
                                        'questions' => $asm->questions->map(fn($q) => [
                                            'text' => $q->question_text,
                                            'topic' => $q->topic,
                                            'type' => $q->type,
                                            'correct_option' => $q->correct_option,
                                            'user_answer' => $q->user_answer,
                                            'is_correct' => $q->is_correct,
                                            'explanation' => $q->explanation,
                                        ])
                                    ]) }}" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-[#004AAD] border border-blue-200 rounded-lg text-[11px] font-bold transition" title="Lihat Evaluasi Lengkap">
                                        Detail AI
                                    </button>

                                    <!-- Reset Asesmen -->
                                    <button type="button" @click="assessmentToDelete = { id: {{ $asm->id }}, name: '{{ addslashes($asm->user?->name ?? 'Mahasiswa') }}' }; deleteModalOpen = true" class="p-1.5 text-rose-400 hover:text-[#0B1B3D] hover:bg-rose-500/20 rounded-lg transition" title="Reset / Hapus Sesi Asesmen Ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                Belum ada data asesmen yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($assessments->hasPages())
            <div class="p-4 bg-white border-t border-slate-200">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Detail Asesmen & Evaluasi AI -->
    <div x-show="selectedAssessment" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="selectedAssessment = null">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-3xl w-full max-h-[85vh] overflow-y-auto p-6 space-y-6 shadow-2xl">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-[#0B1B3D]" x-text="'Hasil Evaluasi Asesmen • ' + selectedAssessment?.student_name"></h3>
                    <p class="text-xs text-slate-500" x-text="'NIM: ' + (selectedAssessment?.nim || '-') + ' • Bidang: ' + selectedAssessment?.field"></p>
                </div>
                <button type="button" @click="selectedAssessment = null" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Scores Overview -->
            <div class="grid grid-cols-3 gap-3">
                <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Skor Total</p>
                    <p class="text-xl font-black text-[#004AAD] mt-1" x-text="selectedAssessment?.total_score + ' / 100'"></p>
                </div>
                <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Hard Skill</p>
                    <p class="text-xl font-black text-[#0B1B3D] mt-1" x-text="selectedAssessment?.hard_skill_score + '%'"></p>
                </div>
                <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Soft Skill</p>
                    <p class="text-xl font-black text-teal-400 mt-1" x-text="selectedAssessment?.soft_skill_score + '%'"></p>
                </div>
            </div>

            <!-- AI Feedback Section -->
            <template x-if="selectedAssessment?.ai_feedback">
                <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 space-y-2">
                    <p class="text-xs font-bold text-purple-300 uppercase tracking-wider">Ringkasan Evaluasi Gemini AI:</p>
                    <p class="text-xs text-[#0B1B3D] leading-relaxed" x-text="selectedAssessment?.ai_feedback"></p>
                </div>
            </template>

            <!-- Questions Breakdown -->
            <div>
                <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Butir Pertanyaan & Analisis Jawaban:</h4>
                <div class="space-y-3">
                    <template x-for="(q, idx) in selectedAssessment?.questions" :key="idx">
                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-slate-500" x-text="'Soal #' + (idx + 1) + ' (' + (q.type === 'hard_skill' ? 'Hard Skill' : 'Soft Skill') + ' • ' + (q.topic || '-') + ')'"></span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold" :class="q.is_correct ? 'bg-[#004AAD]/15 text-[#004AAD]' : 'bg-rose-500/15 text-rose-400'" x-text="q.is_correct ? 'Jawaban Benar' : 'Jawaban Salah'"></span>
                            </div>
                            <p class="text-xs text-[#0B1B3D] font-medium" x-text="q.text"></p>
                            <div class="flex items-center gap-4 text-[11px] pt-1 border-t border-slate-200/60">
                                <span>Jawaban Mahasiswa: <strong :class="q.is_correct ? 'text-[#004AAD]' : 'text-rose-400'" x-text="q.user_answer || 'Kosong'"></strong></span>
                                <span>Kunci Jawaban: <strong class="text-[#004AAD]" x-text="q.correct_option"></strong></span>
                            </div>
                            <template x-if="q.explanation">
                                <p class="text-[10px] text-slate-500 italic pt-1" x-text="'Penjelasan: ' + q.explanation"></p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal: Konfirmasi Reset Asesmen -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-center">
            <div class="w-12 h-12 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#0B1B3D]">Reset Sesi Asesmen</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Apakah Anda yakin ingin mereset/menghapus hasil asesmen mahasiswa <strong class="text-[#0B1B3D]" x-text="assessmentToDelete?.name"></strong>? Mahasiswa akan dapat melakukan asesmen ulang dari awal.
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false; assessmentToDelete = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 rounded-xl text-xs">
                    Batal
                </button>
                <form :action="'/admin/assessments/' + assessmentToDelete?.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md">
                        Ya, Reset Asesmen
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection






