<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-rose-600">
            Hapus Akun Pengguna
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Setelah akun Anda dihapus, semua sumber daya, portofolio, dan riwayat asesmen kompetensi akan dihapus secara permanen. Pastikan Anda telah mengunduh data atau CV yang ingin disimpan sebelum melanjutkan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-4 py-2.5 bg-rose-500/15 text-rose-600 border border-rose-500/30 rounded-xl hover:bg-rose-600 hover:text-white transition font-bold text-xs shadow-sm inline-flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        <span>Hapus Akun Permanen</span>
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 md:p-8 space-y-5">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#0B1B3D]">
                        Apakah Anda yakin ingin menghapus akun?
                    </h2>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi akun Anda untuk mengonfirmasi penghapusan permanen akun dan seluruh data terkait.
                    </p>
                </div>
            </div>

            <div>
                <label for="password" class="sr-only">Kata Sandi</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full bg-white border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition"
                    placeholder="Masukkan kata sandi Anda untuk konfirmasi"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5 text-xs text-rose-600" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2.5 bg-slate-100 text-slate-700 border border-slate-200 hover:bg-rose-50 hover:text-rose-700 rounded-xl text-xs font-semibold transition"
                >
                    Batalkan
                </button>

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-rose-600/30"
                >
                    Ya, Hapus Akun Saya
                </button>
            </div>
        </form>
    </x-modal>
</section>





