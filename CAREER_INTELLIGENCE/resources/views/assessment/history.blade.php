<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Riwayat Asesmen Kompetensi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                Daftar historis hasil uji kompetensi AI yang pernah Anda kerjakan dan selesaikan.
            </p>
        </div>
        <div class="flex items-center gap-2.5 self-start">
            <a href="{{ route('assessment') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Lakukan Asesmen Baru
            </a>
        </div>
    </div>

    <!-- Riwayat Asesmen Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 mb-8">
        @if($assessmentsHistory->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-slate-500 border-b border-slate-200">
                            <th class="py-3 px-4 font-semibold uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-4 font-semibold uppercase tracking-wider">Profesi</th>
                            <th class="py-3 px-4 font-semibold uppercase tracking-wider">Hard Skills</th>
                            <th class="py-3 px-4 font-semibold uppercase tracking-wider">Soft Skills</th>
                            <th class="py-3 px-4 font-semibold uppercase tracking-wider text-right">Skor Total</th>
                            <th class="py-3 px-4 font-semibold uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs sm:text-sm text-slate-600 divide-y divide-slate-800/80">
                        @foreach($assessmentsHistory as $history)
                        <tr class="hover:bg-white/50 transition duration-150">
                            <td class="py-4 px-4 whitespace-nowrap">{{ $history->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-4 px-4 font-medium text-slate-800">
                                @if($history->field === 'all')
                                    Bidang Gabungan (IT & Elektro)
                                @elseif($history->field === 'it')
                                    Teknologi Informasi
                                @else
                                    Teknik Elektro
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-accent/10 text-sky-700 border border-sky-500/20">
                                    {{ $history->hard_skill_score ?? 0 }}%
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                    {{ $history->soft_skill_score ?? 0 }}%
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-black bg-[#004AAD]/20 text-[#004AAD] border border-[#004AAD]/30">
                                    {{ $history->total_score ?? 0 }}%
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('assessment.result', $history->id) }}" class="inline-flex items-center text-[11px] font-bold text-slate-500 hover:text-[#0B1B3D] transition">
                                    Lihat Detail &rarr;
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                <p class="text-sm text-slate-500">Belum ada riwayat asesmen yang diselesaikan.</p>
                <a href="{{ route('assessment') }}" class="inline-flex items-center gap-2 px-5 py-2.5 mt-4 bg-white border border-slate-300 text-slate-600 font-bold text-xs rounded-xl hover:border-sky-500 hover:text-sky-700 transition">
                    Mulai Asesmen Pertama
                </a>
            </div>
        @endif
    </div>
</x-student-layout>





