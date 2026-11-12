<section>
    <header>
        <h2 class="text-base font-bold text-[#0B1B3D]">
            Perbarui Kata Sandi
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Pastikan akun Anda menggunakan kata sandi yang kuat dan unik untuk menjaga keamanan data Anda.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        @if(!empty(auth()->user()->password) && !Hash::check('', auth()->user()->password))
            <div>
                <label for="update_password_current_password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-white border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5 text-xs text-rose-600" />
            </div>
        @else
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl mb-4">
                <p class="text-xs text-blue-700">
                    <strong class="font-bold">Info:</strong> Anda belum mengatur kata sandi atau kata sandi telah direset oleh admin. Silakan buat kata sandi baru Anda di bawah ini tanpa perlu memasukkan kata sandi saat ini.
                </p>
            </div>
        @endif

        <div>
            <label for="update_password_password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" class="w-full bg-white border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-white border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-[13px] rounded-xl shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Simpan Kata Sandi</span>
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-[#004AAD] flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Kata sandi berhasil diperbarui.</span>
                </p>
            @endif
        </div>
    </form>
</section>





