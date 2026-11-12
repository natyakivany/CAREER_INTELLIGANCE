<x-guest-layout>
    <div class="max-w-[540px] w-full" x-data="{ 
        role: 'mahasiswa',
        showPassword: false
    }">
        
        <!-- Main Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl backdrop-blur-xl bg-white/95 p-8 sm:p-10 relative overflow-hidden">
            
            <!-- Top Card Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-[#004AAD] text-[11px] font-bold tracking-wide uppercase mb-3">
                    Registrasi Akun Baru
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Mulai Karier Impianmu</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed max-w-md mx-auto">
                    Daftar untuk memetakan kompetensi riil dan terhubung dengan ekosistem karier kampus & mitra industri
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Role Selection Cards -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2.5">Pilih Kategori Pendaftar</label>
                    <div class="grid grid-cols-2 gap-3">
                        
                        <!-- Mahasiswa & Alumni SMK Card -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="mahasiswa" x-model="role" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl bg-white border-2 border-slate-200 peer-checked:border-[#004AAD] peer-checked:bg-[#004AAD]/5 transition hover:border-slate-200 h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xl">🎓</span>
                                    <div class="w-4 h-4 rounded-full border border-slate-300 peer-checked:border-[#004AAD] peer-checked:bg-[#004AAD] flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="role === 'mahasiswa'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#0B1B3D]">Mahasiswa / Alumni SMK</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Asesmen bakat, skill gap & rekomendasi karier</p>
                                </div>
                            </div>
                        </label>

                        <!-- Mitra Industri Card -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="mitra" x-model="role" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl bg-white border-2 border-slate-200 peer-checked:border-[#004AAD] peer-checked:bg-[#004AAD]/5 transition hover:border-slate-200 h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xl">🏢</span>
                                    <div class="w-4 h-4 rounded-full border border-slate-300 peer-checked:border-[#004AAD] peer-checked:bg-[#004AAD] flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="role === 'mitra'"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#0B1B3D]">Mitra Industri</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Akses talenta mahasiswa & pasang loker</p>
                                </div>
                            </div>
                        </label>

                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-1.5 text-xs text-rose-400" />
                </div>

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <span x-show="role === 'mahasiswa'">Nama Lengkap</span>
                        <span x-show="role === 'mitra'" x-cloak>Nama Perusahaan / Instansi</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" :placeholder="role === 'mahasiswa' ? 'Contoh: Rizky Aditya Pratama' : 'Contoh: PT Inovindo Digital Media'" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-400" />
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <span x-show="role === 'mahasiswa'">Alamat E-mail Aktif</span>
                        <span x-show="role === 'mitra'" x-cloak>Alamat E-mail Resmi HRD / Perusahaan</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" :placeholder="role === 'mahasiswa' ? 'mahasiswa@kampus.ac.id' : 'hrd@perusahaan.com'" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400" />
                </div>

                <!-- Password & Confirmation Grid -->
                <div class="grid sm:grid-cols-2 gap-4">
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Min. 8 karakter" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1.5">Ulangi Kata Sandi</label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang sandi" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-400" />
                    </div>

                </div>

                <!-- Show password toggle & Terms -->
                <div class="space-y-3 pt-1">
                    <div class="flex items-center justify-between">
                        <button type="button" @click="showPassword = !showPassword" class="text-xs text-slate-500 hover:text-slate-200 flex items-center gap-1.5">
                            <span x-text="showPassword ? '🙈 Sembunyikan Sandi' : '👁️ Tampilkan Sandi'"></span>
                        </button>
                    </div>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input id="terms" type="checkbox" required class="w-4 h-4 mt-0.5 rounded bg-white border-slate-200 text-[#004AAD] focus:ring-offset-slate-900 focus:ring-[#004AAD]">
                        <span class="text-xs text-slate-500 leading-relaxed">
                            Saya menyetujui <a href="#" class="text-[#004AAD] hover:underline">Ketentuan Layanan</a> serta bersedia melakukan sinkronisasi data kesiapan karier kampus.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-[#004AAD] hover:bg-[#083577] text-white font-bold py-3.5 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm mt-2">
                    <span>Buat Akun & Mulai Asesmen</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <!-- Login Link -->
                <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
                    Sudah memiliki akun terdaftar? 
                    <a href="{{ route('login') }}" class="font-bold text-[#004AAD] hover:underline ml-1">Masuk Portal &rarr;</a>
                </div>

            </form>

        </div>

        <!-- Security Badge -->
        <div class="mt-6 text-center text-[11px] text-slate-500 flex items-center justify-center gap-2">
            <span>🛡️ Data Terenkripsi SSL</span>
            <span>•</span>
            <span>Kepatuhan Privasi Kampus</span>
        </div>

    </div>
</x-guest-layout>






