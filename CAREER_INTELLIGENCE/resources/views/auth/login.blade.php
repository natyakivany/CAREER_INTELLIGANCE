<x-guest-layout>
    <div class="max-w-[450px] w-full">
        
        <!-- Main Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl backdrop-blur-xl bg-white/95 p-8 sm:p-10 relative overflow-hidden">
            
            <!-- Top Card Header -->
            <div class="text-center mb-8">
                <div class="mx-auto w-12 h-12 bg-[#004AAD] text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-[#004AAD]/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-[#0B1B3D] tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Masuk ke akun Portal Intelijen Karier Kampus
                </p>
            </div>

            <!-- Session Status Alert -->
            <x-auth-session-status class="mb-5" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        Alamat E-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="login" type="email" name="login" value="{{ old('login') }}" required autofocus autocomplete="email" 
                            placeholder="Contoh: email@domain.com" 
                            class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-400 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                    </div>
                    <x-input-error :messages="$errors->get('login')" class="mt-1.5 text-xs text-rose-500" />
                </div>

                <!-- Password Input -->
                <div x-data="{ showPassword: false }">
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-600">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#004AAD] hover:underline">Lupa Kata Sandi?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="current-password" placeholder="Kosongkan jika password Anda telah di-reset" class="w-full pl-10 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-200">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded bg-white border-slate-200 text-[#004AAD] focus:ring-offset-slate-900 focus:ring-[#004AAD]">
                        <span class="text-xs text-slate-500">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-[#004AAD] hover:bg-[#083577] text-white font-bold py-3.5 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                    <span>Masuk ke Portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <!-- Register Link -->
                <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
                    Belum memiliki akun mahasiswa atau mitra? <br class="sm:hidden">
                    <a href="{{ route('register') }}" class="font-bold text-[#004AAD] hover:underline mt-1 inline-block">Daftar Akun Baru &rarr;</a>
                </div>

            </form>

        </div>

        <!-- Security / Compliance Subtext -->
        <div class="mt-6 text-center text-[11px] text-slate-500 flex items-center justify-center gap-2">
            <span>🛡️ Terenkripsi Aman</span>
            <span>•</span>
            <span>Terintegrasi Sistem Kampus</span>
        </div>

    </div>
</x-guest-layout>







