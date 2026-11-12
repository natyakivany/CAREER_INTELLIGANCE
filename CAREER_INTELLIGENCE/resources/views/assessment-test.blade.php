<x-student-layout>
    <div x-data="{
        currentQ: 0,
        totalQ: {{ $questions->count() }},
        answers: {},
        answeredCount: 0,
        updateAnswer(qId, val) {
            this.answers[qId] = val;
            this.answeredCount = Object.keys(this.answers).length;
        }
    }">
        <!-- Top Banner -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 mb-8 text-[#0B1B3D] border border-slate-200 shadow-xl relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30 text-[10px] font-extrabold uppercase tracking-wider">
                        Live Assessment &bull; Bidang {{ strtoupper($assessment->field) }}
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#0B1B3D]">Uji Kompetensi Cerdas Gemini AI</h1>
                    <p class="text-xs text-slate-600">Pilihlah salah satu opsi respon teknis yang paling tepat untuk setiap studi kasus.</p>
                </div>
                <div class="flex items-center gap-3 self-start bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-200">
                    <span class="text-lg">⏱️</span>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-500 block font-medium uppercase tracking-wider">Batas Waktu</span>
                        <span class="text-xs font-black text-[#004AAD]">15 Menit</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Navigator Pills -->
        <div class="bg-white rounded-2xl p-4 shadow-xl border border-slate-200 mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto py-1">
                @foreach ($questions as $idx => $q)
                    <button type="button" @click="currentQ = {{ $idx }}" :class="currentQ === {{ $idx }} ? 'bg-[#004AAD] text-white font-black shadow-md' : (answers[{{ $q->id }}] ? 'bg-[#004AAD]/20 text-[#004AAD] font-bold border border-[#004AAD]/40' : 'bg-white text-slate-500 border border-slate-200 hover:border-slate-300')" class="w-9 h-9 rounded-xl text-xs flex items-center justify-center transition shrink-0">
                        {{ $idx + 1 }}
                    </button>
                @endforeach
            </div>
            <div class="text-right shrink-0">
                <span class="text-xs font-bold text-[#0B1B3D]"><span x-text="answeredCount" class="text-[#004AAD]">0</span>/{{ $questions->count() }}</span>
                <span class="text-[10px] text-slate-500 block font-medium">Terjawab</span>
            </div>
        </div>

        <!-- Assessment Form -->
        <form method="POST" action="{{ route('assessment.submit', $assessment) }}" id="assessmentForm">
            @csrf

            @foreach ($questions as $idx => $q)
                <div x-show="currentQ === {{ $idx }}" x-cloak class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-200 mb-8 space-y-6">
                    
                    <!-- Question Header -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] font-black text-xs flex items-center justify-center">
                                #{{ $idx + 1 }}
                            </span>
                            <span class="text-xs sm:text-sm font-bold text-[#0B1B3D]">Pertanyaan {{ $idx + 1 }} dari {{ $questions->count() }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $q->type === 'hard_skill' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-amber-500/20 text-amber-700 border border-amber-500/30' }}">
                                {{ $q->type === 'hard_skill' ? '⚙️ Hard Skill' : '💡 Soft Skill' }}
                            </span>
                            @if ($q->topic)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-medium bg-white border border-slate-200 text-slate-600">
                                    {{ $q->topic }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="text-base sm:text-lg font-bold text-[#0B1B3D] leading-relaxed">
                        {{ $q->question_text }}
                    </div>

                    <!-- Options Grid -->
                    <div class="space-y-3 pt-2">
                        @foreach ($q->options as $optKey => $optText)
                            <label class="relative flex items-start gap-3.5 p-4 rounded-2xl border transition cursor-pointer hover:border-[#004AAD]/60" :class="answers[{{ $q->id }}] === '{{ $optKey }}' ? 'border-[#004AAD] bg-[#004AAD]/10 shadow-sm' : 'border-slate-200 bg-white'">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $optKey }}" @change="updateAnswer({{ $q->id }}, '{{ $optKey }}')" class="peer sr-only">
                                
                                <div class="w-6 h-6 rounded-lg border flex items-center justify-center font-bold text-xs shrink-0 transition mt-0.5" :class="answers[{{ $q->id }}] === '{{ $optKey }}' ? 'border-[#004AAD] bg-[#004AAD] text-white font-black' : 'border-slate-300 text-slate-500 bg-slate-50'">
                                    {{ $optKey }}
                                </div>
                                <div class="text-xs sm:text-sm text-slate-800 leading-normal font-medium">
                                    {{ $optText }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Navigator Controls -->
                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <button type="button" @click="currentQ = Math.max(0, currentQ - 1)" :disabled="currentQ === 0" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:text-[#004AAD] hover:bg-blue-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            &larr; Sebelumnya
                        </button>

                        <div class="flex items-center gap-3">
                            <button type="button" x-show="currentQ < totalQ - 1" @click="currentQ = Math.min(totalQ - 1, currentQ + 1)" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#003B8B] text-white text-xs font-bold shadow-md shadow-[#004AAD]/20 transition">
                                Selanjutnya &rarr;
                            </button>

                            <button type="submit" x-show="currentQ === totalQ - 1" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan dan mengumpulkan ujian ini?');" class="px-6 py-3 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm">
                                Selesaikan & Kumpulkan Asesmen ✓
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach

        </form>
    </div>
</x-student-layout>





