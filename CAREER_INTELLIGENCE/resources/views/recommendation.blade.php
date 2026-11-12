<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Rekomendasi Karier & Penyelarasan Industri</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">Daftar profesi yang paling relevan dengan profil, portofolio, dan skor uji kompetensi Anda</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-xs font-bold text-[#004AAD] shadow-sm">
                Penyelarasan Karier
            </span>
        </div>
    </div>

    <!-- Recommendations Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        @foreach ($recommendations as $idx => $rec)
            @php
                $occ = $rec['occupation'];
                $match = $rec['match_percentage'];
                $isTop = $idx < 3 && ($match >= 60);
            @endphp

            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border {{ $isTop ? 'border-2 border-[#004AAD] shadow-[#004AAD]/10' : 'border-slate-200' }} flex flex-col justify-between relative overflow-hidden transition hover:-translate-y-1 hover:border-[#004AAD]/60">
                @if ($isTop)
                    <div class="absolute top-0 right-0 bg-[#004AAD] text-white text-[10px] font-bold px-3.5 py-1.5 rounded-bl-2xl shadow-sm">
                        ⭐ Rekomendasi Utama
                    </div>
                @endif

                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $occ->field === 'it' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                            {{ strtoupper($occ->field) }} &bull; {{ $occ->category }}
                        </span>
                        @if ($rec['is_ai_recommended'] ?? false)
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-blue-50 text-[#004AAD] border border-blue-200 flex items-center gap-1 shadow-xs">
                                Rekomendasi Sistem
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] leading-snug mb-2">{{ $occ->title }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-5">
                        {{ $occ->description }}
                    </p>

                    <!-- Match Score Ring / Bar -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 mb-5 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tingkat Kecocokan</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-2xl font-black text-[#0B1B3D]">{{ round($match) }}%</span>
                                <span class="text-xs font-bold {{ $match >= 75 ? 'text-[#004AAD]' : 'text-amber-700' }}">
                                    {{ $match >= 75 ? 'Sangat Relevan' : 'Potensial' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Permintaan Pasar</span>
                            <span class="text-xs font-bold text-[#0B1B3D] block mt-0.5">{{ $occ->demand_level }}</span>
                        </div>
                    </div>

                    <!-- Required Key Skills -->
                    <div class="space-y-2 mb-6">
                        <span class="text-xs font-bold text-slate-600 block">Kualifikasi Utama:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($occ->skills->take(4) as $sk)
                                <span class="text-[10px] font-medium px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-300 text-slate-600">
                                    {{ $sk->skill_name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer & CTA -->
                <div class="pt-4 border-t border-slate-200 space-y-3">
                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('skill-gap', ['occupation_id' => $occ->id]) }}" class="flex-1 text-center py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:text-[#0B1B3D] hover:bg-blue-50 hover:text-[#004AAD] transition">
                            Cek Gap Detail
                        </a>
                        <a href="{{ route('roadmap', ['occupation_id' => $occ->id]) }}" class="flex-1 text-center py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm">
                            Buka Roadmap &rarr;
                        </a>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</x-student-layout>






