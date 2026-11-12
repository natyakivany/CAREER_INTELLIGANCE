@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                <span>Log Aktivitas Sistem</span>
                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-blue-50 text-[#004AAD] rounded-full border border-blue-200">
                    {{ number_format($stats['total'] ?? 0) }} Total Log
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Rekam jejak komprehensif seluruh aktivitas operasional administrator kampus dan mitra industri.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Audit Trail Aktif
            </span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ detailModalOpen: false, activeLog: null }">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Aktivitas</span>
                <h3 class="text-2xl sm:text-3xl font-black text-[#0B1B3D] mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Semua riwayat audit</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#004AAD] flex items-center justify-center border border-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Aktivitas Hari Ini</span>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-950 mt-1">{{ number_format($stats['today'] ?? 0) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ now()->translatedFormat('d F Y') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#FF44B7]">Mitra Industri</span>
                <h3 class="text-2xl sm:text-3xl font-black text-[#0B1B3D] mt-1">{{ number_format($stats['mitra'] ?? 0) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Lowongan & wawancara</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-pink-50 text-[#FF44B7] flex items-center justify-center border border-pink-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Administrator</span>
                <h3 class="text-2xl sm:text-3xl font-black text-purple-950 mt-1">{{ number_format($stats['admin'] ?? 0) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Approval & manajemen</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.activity-logs') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengguna, email, judul aksi, deskripsi..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] placeholder:text-slate-400 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD]">
            </div>

            <div class="sm:col-span-3">
                <select name="role" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="all">Semua Peran Pengguna</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>🛡️ Administrator Kampus</option>
                    <option value="mitra" {{ request('role') === 'mitra' ? 'selected' : '' }}>🏢 Mitra Industri</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="action" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="">Semua Kategori Aksi</option>
                    <option value="interview_scheduled" {{ request('action') === 'interview_scheduled' ? 'selected' : '' }}>Wawancara</option>
                    <option value="job_created" {{ request('action') === 'job_created' ? 'selected' : '' }}>Lowongan Dibuat</option>
                    <option value="job_approved" {{ request('action') === 'job_approved' ? 'selected' : '' }}>Persetujuan Lowongan</option>
                    <option value="application_accepted" {{ request('action') === 'application_accepted' ? 'selected' : '' }}>Kandidat Diterima</option>
                    <option value="application_rejected" {{ request('action') === 'application_rejected' ? 'selected' : '' }}>Lamaran Ditolak</option>
                    <option value="student_created" {{ request('action') === 'student_created' ? 'selected' : '' }}>Registrasi Mahasiswa</option>
                    <option value="mitra_created" {{ request('action') === 'mitra_created' ? 'selected' : '' }}>Registrasi Mitra</option>
                    <option value="password_reset" {{ request('action') === 'password_reset' ? 'selected' : '' }}>Reset Password</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'role', 'action']))
                    <a href="{{ route('admin.activity-logs') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Activity Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-[#0B1B3D] text-sm sm:text-base">Daftar Rekam Jejak Audit</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} catatan</span>
        </div>

        @if($logs->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-700 text-sm">Tidak Ditemukan Catatan Log Aktivitas</h4>
                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau ubah filter perannya.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Pengguna / Pelaksana</th>
                            <th class="py-3 px-4">Aktivitas & Kategori</th>
                            <th class="py-3 px-4">Deskripsi Rincian</th>
                            <th class="py-3 px-4">Waktu Eksekusi</th>
                            <th class="py-3 px-4 text-center">Info Teknis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($logs as $log)
                            @php
                                $badge = $log->action_badge;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl {{ $log->role === 'admin' ? 'bg-purple-600 text-white' : 'bg-[#004AAD] text-white' }} font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($log->user->name ?? ($log->role === 'admin' ? 'AD' : 'SY'), 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-[#0B1B3D] truncate">{{ $log->user->name ?? 'Sistem Otomatis' }}</p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ $log->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-[#004AAD]' }}">
                                                    {{ $log->role === 'admin' ? 'Admin Kampus' : 'Mitra Industri' }}
                                                </span>
                                                @if($log->user?->email)
                                                    <span class="text-[10px] text-slate-400 truncate max-w-[130px]">{{ $log->user->email }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <p class="font-bold text-[#0B1B3D]">{{ $log->title }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $badge['bg'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 min-w-[260px] max-w-md">
                                    <p class="text-slate-700 leading-relaxed font-medium">
                                        {{ $log->description }}
                                    </p>
                                    @if(!empty($log->properties))
                                        <div class="mt-1.5 flex items-center gap-1.5 flex-wrap text-[10px]">
                                            @foreach(array_slice($log->properties, 0, 3) as $k => $v)
                                                @if(is_scalar($v) && !empty($v))
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold border border-slate-200">
                                                        {{ ucwords(str_replace('_', ' ', $k)) }}: {{ $v }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500">
                                    <p class="font-bold text-slate-700">{{ $log->created_at->diffForHumans() }}</p>
                                    <p class="text-[10.5px] text-slate-400">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
                                </td>

                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" @click="activeLog = {{ json_encode([
                                        'id' => $log->id,
                                        'title' => $log->title,
                                        'action' => $log->action,
                                        'role' => $log->role,
                                        'user_name' => $log->user->name ?? 'Sistem',
                                        'user_email' => $log->user->email ?? '-',
                                        'description' => $log->description,
                                        'properties' => $log->properties,
                                        'ip_address' => $log->ip_address ?? '127.0.0.1',
                                        'user_agent' => $log->user_agent ?? 'Internal Browser / System',
                                        'created_at' => $log->created_at->translatedFormat('l, d F Y - H:i:s').' WIB',
                                    ]) }}; detailModalOpen = true" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-[#004AAD] hover:text-white text-slate-600 font-bold text-[11px] transition shadow-2xs">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Detail Modal --}}
    <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div @click.away="detailModalOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-[#004AAD] flex items-center justify-center font-bold text-xs">
                        #ID
                    </span>
                    <div>
                        <h3 class="text-sm font-extrabold text-[#0B1B3D]" x-text="activeLog?.title"></h3>
                        <p class="text-[11px] text-slate-400" x-text="'Aksi: ' + activeLog?.action"></p>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">✕</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Deskripsi Aktivitas</span>
                    <p class="font-medium text-slate-800 mt-0.5 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-200" x-text="activeLog?.description"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Pelaksana</span>
                        <p class="font-bold text-[#0B1B3D] mt-0.5" x-text="activeLog?.user_name"></p>
                        <p class="text-[10.5px] text-slate-500" x-text="activeLog?.user_email"></p>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Waktu Tercatat</span>
                        <p class="font-bold text-[#0B1B3D] mt-0.5" x-text="activeLog?.created_at"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Alamat IP</span>
                        <p class="font-mono text-slate-700 mt-0.5 font-bold" x-text="activeLog?.ip_address"></p>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Peran (Role)</span>
                        <p class="font-bold text-[#004AAD] mt-0.5 uppercase" x-text="activeLog?.role"></p>
                    </div>
                </div>

                <template x-if="activeLog?.properties && Object.keys(activeLog.properties).length > 0">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Parameter / Properti Metadata</span>
                        <pre class="bg-slate-900 text-emerald-400 p-3 rounded-xl font-mono text-[10.5px] overflow-x-auto max-h-40" x-text="JSON.stringify(activeLog?.properties, null, 2)"></pre>
                    </div>
                </template>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" @click="detailModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

