@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                <span>Manajemen Mitra Industri</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-[#004AAD]/15 text-[#004AAD] rounded-full border border-[#004AAD]/30">
                    {{ $mitraList->total() }} Mitra
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola data perusahaan mitra industri yang bekerja sama untuk rekrutmen pekerjaan mahasiswa.
            </p>
        </div>
        <button type="button" @click="$dispatch('open-create-modal')" class="px-4 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-sm flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Tambah Mitra Baru</span>
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ createMitraModalOpen: false, deleteModalOpen: false, userToDelete: null }" @open-create-modal.window="createMitraModalOpen = true">

    <!-- Search Box -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-md">
        <form method="GET" action="{{ route('admin.mitra') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perusahaan atau email mitra..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD]">
            </div>
            
            <div class="sm:col-span-4">
                <select name="sort" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="">Terbaru (Default)</option>
                    <option value="accepted_desc" {{ request('sort') === 'accepted_desc' ? 'selected' : '' }}>Penerimaan Terbanyak Dulu</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-md">
                    Cari
                </button>
                @if (request()->hasAny(['search', 'sort']))
                    <a href="{{ route('admin.mitra') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-xl transition shrink-0" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Mitra Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase bg-white text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Perusahaan Mitra</th>
                        <th class="py-3.5 px-3">Kontak Email Resmi</th>
                        <th class="py-3.5 px-3 text-center">Status Akun</th>
                        <th class="py-3.5 px-3 text-center">Bergabung Sejak</th>
                        <th class="py-3.5 px-3 text-right">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($mitraList as $mitra)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#004AAD] font-extrabold flex items-center justify-center text-sm border border-[#004AAD]/30 shrink-0">
                                        {{ strtoupper(substr($mitra->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#0B1B3D] text-xs sm:text-sm">{{ $mitra->name }}</p>
                                        <span class="text-[10px] text-slate-500">Mitra Industri Terverifikasi</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3 font-medium text-[#0B1B3D]">
                                {{ $mitra->email }}
                            </td>
                            <td class="py-4 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20">
                                    Terhubung Aktif
                                </span>
                            </td>
                            <td class="py-4 px-3 text-center text-slate-500 text-[11px]">
                                {{ $mitra->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Detail -->
                                    <a href="{{ route('admin.mitra.show', $mitra->id) }}" class="p-1.5 text-sky-400 hover:text-[#0B1B3D] hover:bg-accent/20 rounded-lg transition" title="Lihat Detail Mitra Secara Penuh">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    <!-- Reset Password -->
                                    <form method="POST" action="{{ route('admin.users.reset-password', $mitra) }}" class="inline" onsubmit="return confirm('Reset kata sandi mitra ini ke default (kosong)?');">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-amber-400 hover:text-[#0B1B3D] hover:bg-amber-500/20 rounded-lg transition" title="Reset Kata Sandi ke 'kosong'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </button>
                                    </form>

                                    <!-- Delete Mitra -->
                                    <button type="button" @click="deleteModalOpen = true; userToDelete = { id: {{ $mitra->id }}, name: '{{ addslashes($mitra->name) }}' }" class="p-1.5 text-rose-400 hover:text-[#0B1B3D] hover:bg-rose-500/20 rounded-lg transition" title="Hapus Akun Mitra">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                Belum ada data mitra industri terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($mitraList->hasPages())
            <div class="p-4 bg-white border-t border-slate-200">
                {{ $mitraList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Tambah Mitra Baru Manual -->
    <div x-show="createMitraModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="createMitraModalOpen = false">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-[#0B1B3D]">Daftarkan Mitra Industri Baru</h3>
                <button type="button" @click="createMitraModalOpen = false" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.mitra.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Perusahaan / Instansi *</label>
                    <input type="text" name="name" required placeholder="Contoh: PT Inovindo Digital Media" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email Resmi *</label>
                    <input type="email" name="email" required placeholder="hrd@perusahaan.com" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kata Sandi Awal</label>
                    <input type="password" name="password" placeholder="Default: kosong" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="createMitraModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs shadow-md">
                        Daftarkan Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-center">
            <div class="w-12 h-12 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#0B1B3D]">Konfirmasi Hapus Mitra Industri</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Apakah Anda yakin ingin menghapus akun mitra <strong class="text-[#0B1B3D]" x-text="userToDelete?.name"></strong>?
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false; userToDelete = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 rounded-xl text-xs transition">
                    Batal
                </button>
                <form :action="'/admin/users/' + userToDelete?.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-[#0B1B3D] font-bold rounded-xl text-xs transition shadow-md shadow-rose-600/20">
                        Ya, Hapus Mitra
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection






