@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                <span>Manajemen Persetujuan</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-500/15 text-amber-600 rounded-full border border-amber-500/30">
                    {{ $pendingJobs->count() + $pendingProfiles->count() }} Menunggu
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pusat persetujuan (approval) untuk lowongan pekerjaan baru dan pembaruan profil Mitra Industri.
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-8">
    
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="p-4 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- Section: Persetujuan Lowongan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <h2 class="text-sm font-bold text-[#0B1B3D] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Persetujuan Lowongan Pekerjaan
            </h2>
            <span class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 text-[10px] font-bold border border-amber-500/20">{{ $pendingJobs->count() }} Menunggu</span>
        </div>
        
        <div class="p-5">
            @if($pendingJobs->isEmpty())
                <div class="text-center py-6">
                    <p class="text-slate-500 text-sm font-medium">Tidak ada lowongan yang menunggu persetujuan.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingJobs as $job)
                        <div class="p-4 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-[#0B1B3D]">{{ $job->title }}</h3>
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-semibold">{{ $job->type }}</span>
                                </div>
                                <p class="text-xs text-slate-500">Oleh: <span class="font-bold text-[#004AAD]">{{ $job->user->name }}</span> • {{ $job->location }} • {{ $job->duration }}</p>
                                <p class="text-xs text-slate-500 mt-1">Diajukan: {{ $job->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <form action="{{ route('admin.jobs.reject', $job) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('Tolak lowongan ini?')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-lg text-[11px] border border-rose-200 transition">Tolak</button>
                                </form>
                                <form action="{{ route('admin.jobs.approve', $job) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('Setujui lowongan ini?')" class="px-3 py-1.5 bg-[#004AAD] hover:brightness-110 text-white font-bold rounded-lg text-[11px] shadow-md transition">Setujui & Publikasikan</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Section: Persetujuan Profil --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <h2 class="text-sm font-bold text-[#0B1B3D] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Persetujuan Perubahan Profil Mitra
            </h2>
            <span class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 text-[10px] font-bold border border-amber-500/20">{{ $pendingProfiles->count() }} Menunggu</span>
        </div>
        
        <div class="p-5">
            @if($pendingProfiles->isEmpty())
                <div class="text-center py-6">
                    <p class="text-slate-500 text-sm font-medium">Tidak ada pengajuan perubahan profil.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingProfiles as $profile)
                        <div class="p-4 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Data Lama (Saat Ini)</p>
                                    <p class="text-xs font-bold text-[#0B1B3D]">{{ $profile->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $profile->user->email }}</p>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200 relative">
                                    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-400 shadow-sm z-10 hidden md:flex">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </div>
                                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Data Baru (Pengajuan)</p>
                                    <p class="text-xs font-bold text-[#004AAD]">{{ $profile->name }}</p>
                                    <p class="text-xs text-blue-800">{{ $profile->email }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <p class="text-[10px] text-slate-500">Diajukan: {{ $profile->created_at->translatedFormat('d M Y H:i') }}</p>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.profile-updates.reject', $profile) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('Tolak perubahan profil ini?')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-lg text-[11px] border border-rose-200 transition">Tolak</button>
                                    </form>
                                    <form action="{{ route('admin.profile-updates.approve', $profile) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('Setujui perubahan profil ini?')" class="px-3 py-1.5 bg-[#004AAD] hover:brightness-110 text-white font-bold rounded-lg text-[11px] shadow-md transition">Setujui Profil</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
