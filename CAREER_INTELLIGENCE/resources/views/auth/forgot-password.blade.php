<x-guest-layout>
    <div class="max-w-[450px] w-full">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200 p-8 sm:p-10 relative overflow-hidden">
            
            <div class="text-center mb-6">
                <div class="mx-auto w-12 h-12 bg-[#004AAD]/10 text-[#004AAD] rounded-2xl border border-[#004AAD]/20 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-[#0B1B3D] tracking-tight">Atur Ulang Kata Sandi</h1>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Masukkan alamat email Anda yang terdaftar. Sistem akan mengirimkan tautan (link) untuk mengatur ulang kata sandi ke email Anda.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-5" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat E-mail Terdaftar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@kampus.ac.id" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
                </div>

                <button type="submit" class="w-full bg-[#004AAD] hover:bg-[#083577] text-white font-bold py-3.5 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                    <span>Kirim Tautan Reset Sandi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
                    Ingat kata sandi Anda? 
                    <a href="{{ route('login') }}" class="font-bold text-[#004AAD] hover:underline ml-1">Masuk Kembali &rarr;</a>
                </div>
            </form>

        </div>
    </div>
</x-guest-layout>





