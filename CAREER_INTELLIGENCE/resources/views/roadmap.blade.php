<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Roadmap Karier & Akselerasi Belajar</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">Panduan langkah-demi-langkah terstruktur untuk menutup celah kompetensi (skill gap) Anda</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-xs font-bold text-[#004AAD] shadow-sm">
                Panduan Akselerasi
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-[#004AAD]/10 border border-[#004AAD]/30 text-[#004AAD] text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-4 h-4 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Target Role Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 text-[#0B1B3D] mb-8 border border-slate-200 shadow-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-1.5 relative z-10">
            <span class="text-[10px] font-bold tracking-widest text-[#004AAD] uppercase">Target Profesi Sasaran</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D]">{{ $occupation->title ?? 'Pilih Profesi Sasaran' }}</h2>
            <p class="text-xs sm:text-sm text-slate-600 max-w-xl leading-relaxed">
                Jalur ini dirancang khusus berdasarkan hasil evaluasi Gemini AI dan portofolio Anda untuk mencapai kesiapan kerja optimal.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0 relative z-10">
            <a href="{{ route('skill-gap') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] hover:bg-blue-50 hover:text-[#004AAD] text-xs font-bold transition">
                Cek Gap &rarr;
            </a>
        </div>
    </div>

    <!-- Milestones Stepper / Cards -->
    <div class="space-y-6 mb-8">
        @foreach ($roadmaps as $roadmap)
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border {{ $roadmap->is_completed ? 'border-[#004AAD]/50 bg-white' : 'border-slate-200' }} transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-200 mb-6">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm {{ $roadmap->is_completed ? 'bg-[#004AAD] text-white shadow-md shadow-[#004AAD]/20' : 'bg-white border border-slate-300 text-[#0B1B3D]' }}">
                            0{{ $roadmap->stage_order }}
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#0B1B3D]">{{ $roadmap->milestone_title }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $roadmap->description }}</p>
                        </div>
                    </div>
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold {{ $roadmap->is_completed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-white text-slate-500 border border-slate-200' }}">
                        {{ $roadmap->is_completed ? '✓ Selesai' : 'Sedang Berjalan' }}
                    </span>
                </div>

                <!-- Action Items Checklist -->
                <div class="space-y-3">
                    @foreach ($roadmap->action_items ?? [] as $itemIdx => $item)
                        @php
                            $isDone = ($item['status'] ?? 'pending') === 'completed';
                        @endphp
                        <form method="POST" action="{{ route('roadmap.toggle', $roadmap) }}">
                            @csrf
                            <input type="hidden" name="task_index" value="{{ $itemIdx }}">
                            
                            <button type="submit" class="w-full text-left flex items-center justify-between p-4 rounded-2xl border transition cursor-pointer {{ $isDone ? 'border-[#004AAD]/30 bg-blue-50/70' : 'border-slate-300 bg-slate-50 hover:bg-blue-50/60 hover:border-slate-300' }}">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-5 h-5 rounded-md border flex items-center justify-center transition {{ $isDone ? 'border-[#004AAD] bg-[#004AAD] text-white' : 'border-slate-300 bg-slate-50' }}">
                                        @if ($isDone)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </div>
                                    <span class="text-xs sm:text-sm font-medium {{ $isDone ? 'text-slate-500 line-through' : 'text-slate-800' }}">
                                        {{ $item['task'] }}
                                    </span>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md {{ $isDone ? 'text-[#004AAD] bg-[#004AAD]/15' : 'text-slate-500 bg-slate-50' }}">
                                    {{ $isDone ? 'Tuntas' : 'Tandai Selesai' }}
                                </span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Final Guidance Banner -->
    <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center space-y-4 shadow-xl">
        <h4 class="text-base font-extrabold text-[#0B1B3D]">Selesai Menuntaskan Roadmap?</h4>
        <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto leading-relaxed">
            Setelah menguasai materi dan mengunggah proyek baru ke menu Portofolio, Anda dapat mengulang Skill Assessment untuk meningkatkan skor kesiapan kerja (Employability Score).
        </p>
        <div class="flex flex-wrap justify-center gap-3 pt-2">
            <a href="{{ route('portfolio') }}" class="px-5 py-2.5 bg-white hover:bg-blue-50 hover:text-[#004AAD] border border-slate-300 text-slate-800 text-xs font-bold rounded-xl transition">
                💼 Update Portofolio
            </a>
            <a href="{{ route('assessment') }}" class="px-6 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm">
                Uji Ulang Asesmen
            </a>
        </div>
    </div>
</x-student-layout>





