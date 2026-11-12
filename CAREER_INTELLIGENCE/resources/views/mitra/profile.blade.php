<x-mitra-layout>
    <div>
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-6 p-4 rounded-xl bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        
        @php
            $hasPendingUpdate = \App\Models\PendingProfileUpdate::where('user_id', Auth::id())->where('status', 'pending')->exists();
        @endphp

        @if($hasPendingUpdate)
            <div class="mb-6 p-4 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-700 text-sm font-semibold flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="p-2 bg-amber-500/20 rounded-full shrink-0 w-max">
                    <svg class="w-5 h-5 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-amber-800">Menunggu Persetujuan Admin</h3>
                    <p class="text-xs mt-0.5 opacity-90">Pengajuan perubahan nama atau email sedang ditinjau. Anda tidak dapat mengubah profil hingga pengajuan sebelumnya diproses.</p>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Profil Perusahaan</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30">
                        Mitra Terdaftar
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Informasi identitas perusahaan / instansi mitra industri.
                </p>
            </div>
            <a href="{{ route('mitra.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#0B1B3D] text-xs font-semibold transition border border-slate-300">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Left: Company Card --}}
            <div class="lg:col-span-5">
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xl text-center space-y-5">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-[#004AAD] text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-[#004AAD]/20">
                        {{ strtoupper(substr(Auth::user()->name ?? 'M', 0, 2)) }}
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-extrabold text-[#0B1B3D]">{{ Auth::user()->name }}</h2>
                        <p class="text-xs text-[#004AAD] font-semibold mt-1">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="space-y-2.5 text-left">
                        <div class="flex items-center justify-between py-2.5 px-4 bg-white rounded-xl border border-slate-200">
                            <span class="text-[11px] text-slate-500 font-semibold">Peran Akun</span>
                            <span class="text-xs text-[#004AAD] font-bold">Mitra Industri</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-4 bg-white rounded-xl border border-slate-200">
                            <span class="text-[11px] text-slate-500 font-semibold">Lowongan Aktif</span>
                            <span class="text-xs text-[#0B1B3D] font-bold">{{ Auth::user()->jobListings()->where('status', 'Buka')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-4 bg-white rounded-xl border border-slate-200">
                            <span class="text-[11px] text-slate-500 font-semibold">Total Lowongan</span>
                            <span class="text-xs text-[#0B1B3D] font-bold">{{ Auth::user()->jobListings()->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-4 bg-white rounded-xl border border-slate-200">
                            <span class="text-[11px] text-slate-500 font-semibold">Bergabung Sejak</span>
                            <span class="text-xs text-[#0B1B3D] font-bold">{{ Auth::user()->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Edit Form --}}
            <div class="lg:col-span-7">
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xl space-y-6">
                    <div class="pb-4 border-b border-slate-200">
                        <h2 class="text-sm font-bold text-[#0B1B3D] tracking-tight">Edit Informasi Perusahaan</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Perbarui nama perusahaan yang ditampilkan ke mahasiswa.</p>
                    </div>

                    <form action="{{ route('mitra.profile.update') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Perusahaan / Instansi</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" {{ $hasPendingUpdate ? 'disabled' : 'required' }} class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 {{ $hasPendingUpdate ? 'text-slate-400 cursor-not-allowed' : 'text-[#0B1B3D] focus:border-[#004AAD]' }} text-sm focus:outline-none transition">
                            @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat E-mail</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" {{ $hasPendingUpdate ? 'disabled' : 'required' }} class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 {{ $hasPendingUpdate ? 'text-slate-400 cursor-not-allowed' : 'text-[#0B1B3D] focus:border-[#004AAD]' }} text-sm focus:outline-none transition">
                            <p class="text-[10px] text-slate-500 mt-1">Email ini digunakan untuk login dan menerima notifikasi terkait lamaran.</p>
                            @error('email') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-3">
                            <button type="submit" {{ $hasPendingUpdate ? 'disabled' : '' }} class="px-8 py-3 {{ $hasPendingUpdate ? 'bg-slate-300 cursor-not-allowed' : 'bg-[#004AAD] hover:bg-[#083577] shadow-sm' }} text-white font-bold rounded-xl text-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Change Password Section --}}
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xl space-y-5 mt-6">
                    <div class="pb-4 border-b border-slate-200">
                        <h2 class="text-sm font-bold text-[#0B1B3D] tracking-tight">Keamanan Akun</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kelola kata sandi dan keamanan akun Anda.</p>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        @if(!empty(auth()->user()->password) && !\Illuminate\Support\Facades\Hash::check('', auth()->user()->password))
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password Saat Ini</label>
                            <input type="password" name="current_password" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] text-sm focus:outline-none focus:border-[#004AAD]">
                            @error('current_password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        @else
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <p class="text-xs text-blue-700">
                                <strong class="font-bold">Info:</strong> Kata sandi Anda telah direset oleh admin. Silakan langsung buat kata sandi baru di bawah ini.
                            </p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password Baru</label>
                                <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] text-sm focus:outline-none focus:border-[#004AAD]">
                                @error('password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] text-sm focus:outline-none focus:border-[#004AAD]">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 bg-blue-50 hover:bg-blue-100 text-[#004AAD] font-semibold rounded-xl text-xs transition border border-slate-300 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-mitra-layout>







