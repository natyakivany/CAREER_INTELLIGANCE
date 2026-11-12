<x-mitra-layout>
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Log Aktivitas Mitra</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-[#004AAD] border border-blue-200 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Audit Trail Perusahaan
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Rekam jejak seluruh tindakan operasional, penjadwalan wawancara, pembaruan status lamaran, dan publikasi lowongan Anda.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('mitra.dashboard') }}" class="px-4 py-2.5 bg-slate-50 border border-slate-300 hover:bg-slate-100 hover:text-[#0B1B3D] text-slate-600 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Riwayat</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-[#0B1B3D] mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Semua aksi terekam</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#004AAD] flex items-center justify-center border border-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Wawancara</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-purple-950 mt-1">{{ number_format($stats['interviews'] ?? 0) }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Jadwal diatur</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Kandidat Diterima</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-emerald-950 mt-1">{{ number_format($stats['accepted'] ?? 0) }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Resmi diterima</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#FF44B7]">Lowongan</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-[#0B1B3D] mt-1">{{ number_format($stats['jobs'] ?? 0) }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Aktivitas lowongan</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-pink-50 text-[#FF44B7] flex items-center justify-center border border-pink-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <form action="{{ route('mitra.activity-logs') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Cari Aktivitas</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelamar, lowongan, atau deskripsi..." class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-[#004AAD] focus:ring focus:ring-[#004AAD]/20 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Filter Kategori Aksi</label>
                    <select name="action" class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-[#004AAD] focus:ring focus:ring-[#004AAD]/20 transition bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="interview_scheduled" {{ request('action') === 'interview_scheduled' ? 'selected' : '' }}>Wawancara Dijadwalkan</option>
                        <option value="application_accepted" {{ request('action') === 'application_accepted' ? 'selected' : '' }}>Kandidat Diterima</option>
                        <option value="application_rejected" {{ request('action') === 'application_rejected' ? 'selected' : '' }}>Lamaran Ditolak</option>
                        <option value="application_selection" {{ request('action') === 'application_selection' ? 'selected' : '' }}>Tahap Tes/Seleksi</option>
                        <option value="job_created" {{ request('action') === 'job_created' ? 'selected' : '' }}>Lowongan Dibuat</option>
                        <option value="job_updated" {{ request('action') === 'job_updated' ? 'selected' : '' }}>Lowongan Diperbarui</option>
                        <option value="candidate_contacted" {{ request('action') === 'candidate_contacted' ? 'selected' : '' }}>Hubungi Mahasiswa</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-end gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl transition shadow-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'action']))
                        <a href="{{ route('mitra.activity-logs') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Reset filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Activity Timeline List --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-extrabold text-[#0B1B3D] text-sm sm:text-base">Daftar Aktivitas Operasional</h3>
                <span class="text-xs text-slate-500">Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log</span>
            </div>

            @if($logs->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="font-bold text-slate-700 text-sm">Belum Ada Catatan Log Aktivitas</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Setiap tindakan yang Anda lakukan pada portal mitra (jadwal wawancara, membuat lowongan, proses pelamar) akan dicatat di sini secara otomatis.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($logs as $log)
                        @php
                            $badge = $log->action_badge;
                        @endphp
                        <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl {{ $badge['bg'] }} flex items-center justify-center shrink-0 border mt-0.5">
                                @if(str_contains($log->action, 'interview'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @elseif(str_contains($log->action, 'accepted'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif(str_contains($log->action, 'rejected'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif(str_contains($log->action, 'job'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @elseif(str_contains($log->action, 'contact') || str_contains($log->action, 'email'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs sm:text-sm font-extrabold text-[#0B1B3D]">{{ $log->title }}</h4>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badge['bg'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-medium shrink-0" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                                        {{ $log->created_at->diffForHumans() }} ({{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB)
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    {{ $log->description }}
                                </p>

                                @if(!empty($log->properties))
                                    <div class="mt-2 flex items-center gap-2 flex-wrap text-[10.5px]">
                                        @if(isset($log->properties['applicant_name']))
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold">
                                                Pelamar: <strong>{{ $log->properties['applicant_name'] }}</strong>
                                            </span>
                                        @endif
                                        @if(isset($log->properties['interview_type']))
                                            <span class="px-2 py-0.5 rounded-md {{ $log->properties['interview_type'] === 'offline' ? 'bg-amber-100 text-amber-900' : 'bg-blue-100 text-blue-900' }} font-bold">
                                                Metode: {{ $log->properties['interview_type'] === 'offline' ? '🏢 Offline' : '🌐 Online' }}
                                            </span>
                                        @endif
                                        @if(isset($log->properties['interview_date']))
                                            <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-semibold">
                                                Waktu: {{ $log->properties['interview_date'] }} WIB
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-mitra-layout>

