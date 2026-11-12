<x-student-layout>
    <div x-data="{ showModal: false, activeFilter: 'all' }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
            <div>
                <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Portofolio & Rekam Proyek</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5">Dokumentasikan karya dan proyek nyata (Bidang IT & Elektro) sebagai bukti kompetensi industri</p>
            </div>
            <div class="flex items-center gap-3 self-start">
                <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-bold shadow-sm">
                    <span class="text-slate-500 font-medium">Kelengkapan:</span>
                    <span class="text-[#004AAD] font-black">{{ $completeness }}%</span>
                </div>
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-[#004AAD]/10 border border-[#004AAD]/30 text-xs font-bold text-[#004AAD] shadow-sm">
                    {{ $portfolios->count() }} Proyek Terdaftar
                </span>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-[#004AAD]/10 border border-[#004AAD]/30 text-[#004AAD] text-xs font-semibold flex items-center gap-2.5">
                <svg class="w-4 h-4 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Flow Notice & Completeness Banner (Persis Seperti di Profile) -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 text-[#0B1B3D] mb-8 border-t-4 border-t-[#004AAD] border-x border-b border-slate-200 shadow-lg relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-3 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-[#004AAD] border border-blue-200 text-[10px] font-bold tracking-wide uppercase">
                            <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Rekam Bukti Proyek &bull; Bobot 35%
                        </span>
                        @if($completeness >= 100)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Portofolio Prima (3+ Proyek &bull; 100%)
                            </span>
                        @elseif($completeness >= 70)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                Portofolio Baik (2 Proyek &bull; 70%)
                            </span>
                        @elseif($completeness >= 35)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Portofolio Terisi (1 Proyek &bull; 35%)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Perlu Diisi (Unggah Minimal 1 Proyek)
                            </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-[#0B1B3D]">Kelengkapan Portofolio Mempengaruhi Skor Kesiapan Kerja (35%)</h3>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed mt-1">
                            Nilai akhir <em>Employability Score</em> dihitung dari Asesmen (40%), Portofolio (35%), dan Profil Akademik (25%). 1 Proyek = 35%, 2 Proyek = 70%, 3+ Proyek = 100%.
                        </p>
                    </div>

                    <!-- Progress Bar & Percentage (Sama Seperti di Profile) -->
                    <div class="pt-2 max-w-2xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11.5px] font-bold text-slate-600">Kelengkapan Portofolio</span>
                            <span class="text-[12px] font-extrabold text-[#004AAD]">{{ $completeness }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 border border-slate-200 rounded-full overflow-hidden p-0.5">
                            <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500 shadow-sm shadow-[#004AAD]/30" style="width: {{ $completeness }}%;"></div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                            Profil & portofolio menyumbang <strong class="text-[#004AAD]">60%</strong> bobot kesiapan kerja Anda sebelum asesmen.
                        </p>

                        <!-- Kriteria / Checklist Micro Badges -->
                        <div class="mt-3 flex flex-wrap items-center gap-1.5 pt-1">
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $portfolios->count() >= 1 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                @if($portfolios->count() >= 1)
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                @endif
                                1 Proyek (35%)
                            </span>

                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $portfolios->count() >= 2 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                @if($portfolios->count() >= 2)
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                @endif
                                2 Proyek (70%)
                            </span>

                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $portfolios->count() >= 3 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                @if($portfolios->count() >= 3)
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                @endif
                                3+ Proyek (100%)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Action Button & Big Stat Box -->
                <div class="flex flex-col sm:flex-row lg:flex-col items-center sm:items-stretch lg:items-center gap-4 shrink-0">
                    <div class="flex items-center gap-3.5 bg-slate-50 border border-slate-200 px-4 py-3.5 rounded-2xl w-full justify-center shadow-inner">
                        <div class="w-12 h-12 rounded-xl bg-[#004AAD]/10 border border-[#004AAD]/20 text-[#004AAD] flex items-center justify-center font-black text-base shadow-sm">
                            {{ $completeness }}%
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Status Portofolio</p>
                            <p class="text-xs font-extrabold text-[#0B1B3D]">
                                @if($completeness >= 100)
                                    Sangat Lengkap (100%)
                                @elseif($completeness >= 70)
                                    Lengkap (70%)
                                @elseif($completeness >= 35)
                                    Cukup Lengkap (35%)
                                @else
                                    Belum Terisi (0%)
                                @endif
                            </p>
                        </div>
                    </div>

                    <button @click="showModal = true" class="w-full px-5 py-3 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Tambah Proyek Baru</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm text-xs font-semibold">
                <button @click="activeFilter = 'all'" :class="activeFilter === 'all' ? 'bg-[#004AAD] text-white font-bold shadow-sm' : 'text-slate-600 hover:text-[#004AAD] hover:bg-blue-50 font-semibold'" class="px-4 py-2 rounded-lg transition">
                    Semua ({{ $totalCount }})
                </button>
                <button @click="activeFilter = 'it'" :class="activeFilter === 'it' ? 'bg-[#004AAD] text-white font-bold shadow-sm' : 'text-slate-600 hover:text-[#004AAD] hover:bg-blue-50 font-semibold'" class="px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                    <span>💻 Bidang IT</span>
                    <span class="text-[10px] opacity-80">({{ $itCount }})</span>
                </button>
                <button @click="activeFilter = 'elektro'" :class="activeFilter === 'elektro' ? 'bg-[#004AAD] text-white font-bold shadow-sm' : 'text-slate-600 hover:text-[#004AAD] hover:bg-blue-50 font-semibold'" class="px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                    <span>⚡ Bidang Elektro</span>
                    <span class="text-[10px] opacity-80">({{ $elektroCount }})</span>
                </button>
            </div>
        </div>

        <!-- Portfolios Grid -->
        @if ($portfolios->isEmpty())
            <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-white border border-slate-300 flex items-center justify-center text-slate-500 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-base font-bold text-[#0B1B3D] mb-1">Belum Ada Proyek Portofolio</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
                    Kelengkapan portofolio Anda saat ini <strong class="text-amber-600">0%</strong>. Tambahkan riwayat proyek kuliah, tugas akhir, atau karya mandiri Anda di bidang IT atau Elektro untuk mulai mengaktifkan skor portofolio Anda (35%).
                </p>
                <button @click="showModal = true" class="px-6 py-3 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl transition shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Unggah Proyek Sekarang</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach ($portfolios as $portfolio)
                    <div x-show="activeFilter === 'all' || activeFilter === '{{ $portfolio->field }}'" class="bg-white rounded-3xl p-6 shadow-md border-t-4 {{ $portfolio->field === 'it' ? 'border-t-[#004AAD]' : 'border-t-indigo-600' }} border-x border-b border-slate-200 flex flex-col justify-between hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $portfolio->field === 'it' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 'bg-accent/15 text-sky-700 border border-sky-500/30' }}">
                                    {{ $portfolio->field === 'it' ? '💻 IT / Software' : '⚡ Elektro / IoT' }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-medium">{{ $portfolio->category ?? 'Proyek' }}</span>
                            </div>

                            <h3 class="text-base font-bold text-[#0B1B3D] leading-snug mb-2">{{ $portfolio->title }}</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4 line-clamp-3">
                                {{ $portfolio->description }}
                            </p>

                            @if ($portfolio->tech_stack)
                                <div class="flex flex-wrap gap-1.5 mb-5">
                                    @foreach (explode(',', $portfolio->tech_stack) as $tech)
                                        <span class="text-[10px] font-medium text-slate-600 bg-white border border-slate-200 px-2 py-0.5 rounded-md">
                                            {{ trim($tech) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Detail Completeness Bar -->
                        <div class="pt-3.5 border-t border-slate-100 mt-4 mb-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10.5px] font-semibold text-slate-500">Kelengkapan Data:</span>
                                <span class="text-[11px] font-extrabold text-[#004AAD]">{{ $portfolio->completeness }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 border border-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500 shadow-sm shadow-[#004AAD]/30" style="width: {{ $portfolio->completeness }}%;"></div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                @if ($portfolio->project_url)
                                    <a href="{{ $portfolio->project_url }}" target="_blank" class="text-[#004AAD] hover:underline font-bold text-[11px]">
                                        Demo ↗
                                    </a>
                                @endif
                                @if ($portfolio->repository_url)
                                    <a href="{{ $portfolio->repository_url }}" target="_blank" class="text-slate-500 hover:text-[#0B1B3D] font-semibold text-[11px]">
                                        Repo ↗
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('portfolio.destroy', $portfolio) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-600 font-medium text-[11px] transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Modal Tambah Portofolio -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-300 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-5">
                    <div>
                        <h2 class="text-lg font-extrabold text-[#0B1B3D]">Tambah Proyek Portofolio</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Isi detail karya nyata perkuliahan atau mandiri Anda</p>
                    </div>
                    <button @click="showModal = false" class="text-slate-500 hover:text-[#0B1B3D] p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('portfolio.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">Bidang Spesialisasi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="field" value="it" class="peer sr-only" checked>
                                <div class="p-3 text-center text-xs font-bold rounded-xl border border-slate-300 bg-white text-slate-500 peer-checked:border-[#004AAD] peer-checked:bg-[#004AAD]/10 peer-checked:text-[#004AAD] transition">
                                    💻 Bidang IT
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="field" value="elektro" class="peer sr-only">
                                <div class="p-3 text-center text-xs font-bold rounded-xl border border-slate-300 bg-white text-slate-500 peer-checked:border-sky-500 peer-checked:bg-accent/10 peer-checked:text-sky-700 transition">
                                    ⚡ Bidang Elektro
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-600 mb-1.5">Judul Proyek</label>
                        <input id="title" name="title" type="text" required placeholder="Contoh: Sistem Monitoring Suhu Lab IoT berbasis ESP32" class="w-full text-xs px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-xs font-bold text-slate-600 mb-1.5">Kategori</label>
                            <input id="category" name="category" type="text" placeholder="e.g. IoT, Web App, Firmware" class="w-full text-xs px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                        <div>
                            <label for="tech_stack" class="block text-xs font-bold text-slate-600 mb-1.5">Tech Stack / Alat</label>
                            <input id="tech_stack" name="tech_stack" type="text" placeholder="e.g. Arduino, C++, ESP32, MQTT" class="w-full text-xs px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-600 mb-1.5">Deskripsi Singkat & Kontribusi</label>
                        <textarea id="description" name="description" rows="3" required placeholder="Jelaskan tujuan proyek, fitur utama, dan peran yang Anda kerjakan..." class="w-full text-xs px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="project_url" class="block text-xs font-bold text-slate-600 mb-1.5">Link Demo (Opsional)</label>
                            <input id="project_url" name="project_url" type="url" placeholder="https://..." class="w-full text-xs px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                        <div>
                            <label for="repository_url" class="block text-xs font-bold text-slate-600 mb-1.5">Link Repository (Opsional)</label>
                            <input id="repository_url" name="repository_url" type="url" placeholder="https://github.com/..." class="w-full text-xs px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-[#0B1B3D] transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm">
                            Simpan Proyek
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-student-layout>





