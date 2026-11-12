<x-student-layout>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl md:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">CV Builder ATS-Friendly Berbasis Kompetensi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                Berkas lamaran kerja terotomatisasi dari profil akademik, nilai asesmen kompetensi (Gemini AI), dan portofolio proyek Anda.
            </p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-600 shadow-sm">
                {{ $user->nim ? 'NIM. ' . $user->nim : 'NIM Mahasiswa' }}
            </span>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-xs font-bold text-[#004AAD] shadow-sm">
                Bidang {{ strtoupper($profile->target_field ?? 'IT') }}
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Top Action & Completeness Bar -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-200 mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 flex-1">
            <div class="flex items-center gap-2">
                <span class="text-xs sm:text-sm font-bold text-slate-800 shrink-0">Kelengkapan CV ATS:</span>
                <span class="text-sm font-bold text-[#004AAD]">{{ $completeness }}%</span>
            </div>
            <div class="flex items-center gap-3 w-full max-w-xs">
                <div class="w-full h-2.5 bg-slate-100 border border-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500" style="width: {{ $completeness }}%;"></div>
                </div>
            </div>
            <span class="text-xs text-slate-500 hidden sm:inline">
                &bull; Kesiapan Kerja: <strong class="text-[#0B1B3D]">{{ $employabilityScore }}%</strong>
            </span>
            <div class="sm:ml-2">
                @if($profile?->isCvFilled())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Status CV: Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Simpan formulir CV atau klik optimasi AI di bawah untuk mengaktifkan CV Anda">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        Status CV: Belum Disimpan (Tidak Aktif)
                    </span>
                @endif
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <!-- AI Optimize Button -->
            <form method="POST" action="{{ route('cv-builder.ai-generate') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#004AAD] hover:bg-[#003B8B] text-white text-xs font-bold rounded-xl transition shadow-md shadow-[#004AAD]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    ✨ Optimalkan dengan AI Gemini
                </button>
            </form>

            <!-- Print / PDF Button -->
            @if($profile?->isCvFilled())
                <a href="{{ route('cv-builder.print') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak / Simpan PDF
                </a>
            @else
                <button type="button" disabled class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed opacity-80" title="Simpan formulir CV terlebih dahulu untuk mengaktifkan cetak dokumen CV ATS">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    Cetak / Simpan PDF
                </button>
            @endif
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 mb-8">
        
        <!-- Left Col: Form Builder (5 cols) -->
        <div class="xl:col-span-5 space-y-6">
            <form method="POST" action="{{ route('cv-builder.update') }}" class="space-y-6">
                @csrf

                <!-- 1. Identitas & Kontak -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-4 flex items-center justify-between pb-3 border-b border-slate-200">
                        <span>1. Data Pribadi & Kontak</span>
                        <span class="text-[10px] text-[#004AAD] uppercase tracking-widest font-bold">ATS Header</span>
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap Mahasiswa</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">Domisili / Alamat (Kota)</label>
                                <input type="text" name="address" value="{{ old('address', $profile->address) }}" placeholder="Contoh: Padang, Indonesia" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">Nomor WhatsApp / HP</label>
                                <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="08123456789" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">Email</label>
                                <input type="email" value="{{ $user->email }}" disabled class="w-full bg-slate-50 border border-slate-200 text-slate-500 text-xs rounded-xl px-4 py-2.5 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">URL Profil LinkedIn</label>
                                <input type="text" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" placeholder="https://linkedin.com/in/... atau www.linkedin.com/in/..." class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">URL GitHub / Portofolio</label>
                                <input type="text" name="github_url" value="{{ old('github_url', $profile->github_url) }}" placeholder="https://github.com/... atau github.com/..." class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Headline Profesi Sasaran</label>
                            <input type="text" name="cv_headline" value="{{ old('cv_headline', $profile->cv_headline ?: ($targetOccupation->title ?? 'Full Stack Web Developer')) }}" placeholder="Contoh: Junior Full Stack Developer | Laravel & React" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                    </div>
                </div>

                <!-- 2. Ringkasan Profesional -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <div class="flex items-center justify-between mb-2 pb-3 border-b border-slate-200">
                        <h3 class="text-sm font-extrabold text-[#0B1B3D]">2. Ringkasan Profesional</h3>
                        <span class="text-[10px] text-[#004AAD] font-bold uppercase tracking-wider">Teroptimasi ATS</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3.5 leading-relaxed">
                        Teks ini menonjolkan capaian asesmen kompetensi dan portofolio Anda. Anda dapat mengedit teks ini secara manual.
                    </p>
                    
                    <textarea name="cv_summary" rows="5" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs leading-relaxed rounded-xl p-4 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition resize-none">{{ old('cv_summary', $profile->cv_summary) }}</textarea>
                </div>

                <!-- 3. Keahlian & Kata Kunci ATS -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-2 pb-3 border-b border-slate-200">3. Kata Kunci Keahlian (Skills)</h3>
                    <p class="text-xs text-slate-500 mb-3">Pisahkan setiap keahlian dengan tanda koma (,).</p>
                    
                    @php
                        $defaultSkills = !empty($profile->cv_custom_skills) 
                            ? implode(', ', $profile->cv_custom_skills) 
                            : ($targetOccupation->skills ? $targetOccupation->skills->pluck('skill_name')->implode(', ') : 'Laravel, React, REST API, Git, MySQL');
                    @endphp
                    <input type="text" name="cv_custom_skills" value="{{ old('cv_custom_skills', $defaultSkills) }}" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-3 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">

                    <!-- Verified Skills Badges -->
                    <div class="mt-4 pt-4 border-t border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Kompetensi Terverifikasi Asesmen AI:</span>
                        <div class="flex flex-wrap gap-2">
                            @if($latestAssessment)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] text-[11px] font-bold rounded-lg">
                                    <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Hard Skills: {{ $latestAssessment->hard_skill_score }}%
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] text-[11px] font-bold rounded-lg">
                                    <svg class="w-3 h-3 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Soft Skills: {{ $latestAssessment->soft_skill_score }}%
                                </span>
                            @else
                                <span class="text-xs text-slate-500 italic">Belum mengikuti asesmen kompetensi.</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. Riwayat Pendidikan -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-4 pb-3 border-b border-slate-200">4. Riwayat Pendidikan</h3>
                    
                    <!-- Pendidikan Tinggi -->
                    <div class="mb-5 pb-5 border-b border-slate-100">
                        <h4 class="text-xs font-bold text-slate-800 mb-3">Pendidikan Tinggi (Perguruan Tinggi)</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Nama Perguruan Tinggi</label>
                                <input type="text" name="institution_name" value="{{ old('institution_name', $profile->institution_name) }}" placeholder="Contoh: Politeknik Negeri Padang" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Jurusan / Fakultas</label>
                                    <input type="text" name="jurusan" value="{{ old('jurusan', $profile->jurusan) }}" placeholder="Contoh: Teknologi Informasi" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Program Studi</label>
                                    <input type="text" name="prodi" value="{{ old('prodi', $profile->prodi) }}" placeholder="Contoh: D4 Rekayasa Perangkat Lunak" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pendidikan Menengah -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 mb-3">Pendidikan Menengah (SMK/SMA)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Nama Sekolah</label>
                                <input type="text" name="high_school_name" value="{{ old('high_school_name', $profile->high_school_name) }}" placeholder="Contoh: SMK Negeri 1 Solok" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Jurusan</label>
                                <input type="text" name="high_school_major" value="{{ old('high_school_major', $profile->high_school_major) }}" placeholder="Contoh: Teknik Komputer Jaringan" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Tahun Lulus</label>
                            <input type="text" name="high_school_year" value="{{ old('high_school_year', $profile->high_school_year) }}" placeholder="Contoh: 2021" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs rounded-xl px-4 py-2 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                    </div>
                </div>

                <!-- 5. Pengalaman Magang -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-2 pb-3 border-b border-slate-200">5. Pengalaman Magang</h3>
                    <p class="text-xs text-slate-500 mb-3.5 leading-relaxed">Format Markdown: gunakan <strong>**teks tebal**</strong> untuk judul dan <strong>-</strong> untuk bullet point.</p>
                    <textarea name="cv_internships" rows="5" placeholder="Contoh:&#10;**BKPSDM Kota Solok | Teknisi Jaringan (Des 2022 - Mar 2023)**&#10;- Melakukan perbaikan jaringan&#10;- Memastikan konektivitas" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs leading-relaxed rounded-xl p-4 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition resize-none">{{ old('cv_internships', $profile->cv_internships) }}</textarea>
                </div>

                <!-- 6. Pengalaman Organisasi -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-2 pb-3 border-b border-slate-200">6. Pengalaman Organisasi</h3>
                    <textarea name="cv_organizations" rows="4" placeholder="Contoh:&#10;**HMJ Teknologi Informasi | Anggota (2022 - 2023)**&#10;- Merencanakan program kerja..." class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs leading-relaxed rounded-xl p-4 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition resize-none">{{ old('cv_organizations', $profile->cv_organizations) }}</textarea>
                </div>

                <!-- 7. Tools & Technology -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-2 pb-3 border-b border-slate-200">7. Tools & Technology</h3>
                    <p class="text-xs text-slate-500 mb-3.5 leading-relaxed">Pisahkan dengan baris baru (Enter) agar menjadi list bullet.</p>
                    <textarea name="cv_tools" rows="4" placeholder="Contoh:&#10;Visual Studio Code&#10;Docker&#10;Figma" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs leading-relaxed rounded-xl p-4 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition resize-none">{{ old('cv_tools', $profile->cv_tools) }}</textarea>
                </div>

                <!-- 8. Sertifikat -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-[#0B1B3D] mb-2 pb-3 border-b border-slate-200">8. Sertifikat & Keterlibatan Lainnya</h3>
                    <textarea name="cv_certificates" rows="4" placeholder="Contoh:&#10;Sertifikat Kompetensi BNSP Junior Web Developer&#10;Peserta Workshop Cloud AWS" class="w-full bg-white border border-slate-300 text-[#0B1B3D] text-xs leading-relaxed rounded-xl p-4 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition resize-none">{{ old('cv_certificates', $profile->cv_certificates) }}</textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Simpan Perubahan Data CV
                </button>
            </form>

            <!-- Quick Portfolios Overview -->
            <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-200">
                    <h4 class="text-xs sm:text-sm font-bold text-[#0B1B3D]">Portofolio Terhubung ({{ $portfolios->count() }})</h4>
                    <a href="{{ route('portfolio') }}" class="text-xs font-bold text-[#004AAD] hover:underline">+ Kelola Proyek</a>
                </div>
                <div class="space-y-2">
                    @forelse($portfolios->take(3) as $proj)
                        <div class="p-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-800 truncate pr-2">{{ $proj->title }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-50 border border-slate-300 text-slate-600 font-medium shrink-0">
                                {{ $proj->category ?? 'Proyek' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic">Belum ada portofolio. <a href="{{ route('portfolio') }}" class="text-[#004AAD] underline">Tambah sekarang</a></p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Col: Live ATS Paper Preview (7 cols) -->
        <div class="xl:col-span-7">
            <div class="sticky top-6">
                
                <!-- Preview Frame Container: Authentic White Paper Sheet on Dark Canvas -->
                <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-slate-200 font-sans text-slate-800 relative">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex-1 pr-4">
                            <h2 class="text-2xl sm:text-3xl font-extrabold uppercase text-black mb-3">{{ $user->name }}</h2>
                            <div class="text-[11px] sm:text-[11.5px] space-y-1.5 text-black font-medium">
                                @if($profile->phone)
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <span>{{ $profile->phone }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span>{{ $user->email }}</span>
                                </div>
                                @if($profile->address)
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>{{ $profile->address }}</span>
                                    </div>
                                @endif
                                @if($profile->linkedin_url)
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <a href="{{ $profile->linkedin_url }}" target="_blank" class="hover:underline text-blue-800">{{ $profile->linkedin_url }}</a>
                                    </div>
                                @endif
                                @if($profile->github_url)
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                        <a href="{{ $profile->github_url }}" target="_blank" class="hover:underline text-blue-800">{{ $profile->github_url }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($profile->avatar_url)
                            <div class="shrink-0 ml-4">
                                <img src="{{ $profile->avatar_url }}" alt="Foto Profil" class="w-[90px] h-[120px] object-cover border border-slate-200 shadow-sm">
                            </div>
                        @endif
                    </div>

                    <!-- PROFIL -->
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Profil
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <p class="text-[11.5px] leading-relaxed text-black text-justify">
                            {{ $profile->cv_summary ?: 'Mahasiswa aktif tingkat akhir dengan rekam jejak penguasaan kompetensi teruji di bidang ' . strtoupper($profile->target_field ?? 'IT') . ', siap mengimplementasikan keahlian teknis dan kolaboratif pada industri modern.' }}
                        </p>
                    </div>

                    <!-- KEAHLIAN TEKNIS -->
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Keahlian Teknis
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <ul class="list-disc list-outside ml-4 text-[11.5px] leading-relaxed text-black space-y-1">
                            @if(!empty($profile->cv_custom_skills))
                                @php
                                    $skillsArray = is_array($profile->cv_custom_skills) ? $profile->cv_custom_skills : [];
                                @endphp
                                @foreach($skillsArray as $skill)
                                    <li>{{ $skill }}</li>
                                @endforeach
                            @else
                                <li><strong class="font-semibold">Software Development:</strong> Laravel, React, MySQL, Git.</li>
                                <li><strong class="font-semibold">Tools & Lainnya:</strong> VS Code, Postman, Problem Solving.</li>
                            @endif
                            @if($latestAssessment)
                                <li><strong class="font-semibold">Verifikasi AI:</strong> Hard Skills ({{ $latestAssessment->hard_skill_score }}%), Soft Skills ({{ $latestAssessment->soft_skill_score }}%)</li>
                            @endif
                        </ul>
                    </div>

                    <!-- PENDIDIKAN -->
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Pendidikan
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <div class="flex justify-between items-start text-[11.5px] text-black">
                            <div class="w-1/2 pr-4">
                                <strong class="font-bold block">Politeknik Negeri Padang</strong>
                                <span class="block">Jurusan {{ $profile->jurusan ?? 'Teknologi Informasi' }} / {{ $profile->prodi ?? 'D4 RPL' }}</span>
                                <span class="block mt-0.5">2021 - Sekarang</span>
                            </div>
                            <div class="w-1/2">
                                <strong class="font-bold block">{{ $profile->high_school_name ?: 'Sekolah Menengah Kejuruan (SMK)' }}</strong>
                                <span class="block">Jurusan {{ $profile->high_school_major ?: 'Teknologi Komputer / Relevan' }}</span>
                                <span class="block mt-0.5">Lulus Tahun {{ $profile->high_school_year ?: '2021' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- PENGALAMAN MAGANG -->
                    @if($profile->cv_internships)
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Pengalaman Magang
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <div class="text-[11.5px] text-black leading-relaxed text-justify [&>p]:mb-1 [&>ul]:list-disc [&>ul]:ml-4 [&>ul>li]:mb-0.5 [&>p>strong]:font-bold">
                            {!! Str::markdown($profile->cv_internships) !!}
                        </div>
                    </div>
                    @endif

                    <!-- PENGALAMAN PROYEK -->
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Pengalaman Proyek
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        @if($portfolios->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($portfolios as $project)
                                    <div class="text-[11.5px] text-black">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <div>
                                                <strong class="font-bold">{{ $project->title }}</strong> 
                                                <span class="mx-1">|</span> 
                                                <span>{{ $project->category ?? 'Pengembang' }}</span>
                                            </div>
                                        </div>
                                        <p class="leading-relaxed text-justify">
                                            {{ $project->description }}
                                        </p>
                                        @if($project->tech_stack)
                                            <p class="mt-0.5"><strong class="font-semibold">Tech Stack:</strong> {{ $project->tech_stack }}</p>
                                        @endif
                                        @if($project->project_url)
                                            <p class="mt-0.5"><strong class="font-semibold">Tautan:</strong> <a href="{{ $project->project_url }}" target="_blank" class="text-blue-800 hover:underline">{{ $project->project_url }}</a></p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[11.5px] text-slate-500 italic">
                                Belum ada portofolio proyek terdaftar.
                            </p>
                        @endif
                    </div>

                    <!-- PENGALAMAN ORGANISASI -->
                    @if($profile->cv_organizations)
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Pengalaman Organisasi
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <div class="text-[11.5px] text-black leading-relaxed text-justify [&>p]:mb-1 [&>ul]:list-disc [&>ul]:ml-4 [&>ul>li]:mb-0.5 [&>p>strong]:font-bold">
                            {!! Str::markdown($profile->cv_organizations) !!}
                        </div>
                    </div>
                    @endif

                    <!-- TOOLS & TECHNOLOGY -->
                    @if($profile->cv_tools)
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Tools & Technology
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <ul class="list-disc list-outside ml-4 text-[11.5px] leading-relaxed text-black space-y-1">
                            @foreach(explode("\n", str_replace("\r", "", $profile->cv_tools)) as $tool)
                                @if(trim($tool)) <li>{{ $tool }}</li> @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- SERTIFIKAT -->
                    @if($profile->cv_certificates)
                    <div class="mb-5">
                        <h3 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                            Sertifikat & Keterlibatan Lainnya
                        </h3>
                        <hr class="border-t-[1.5px] border-black mb-2.5">
                        <ul class="list-disc list-outside ml-4 text-[11.5px] leading-relaxed text-black space-y-1">
                            @foreach(explode("\n", str_replace("\r", "", $profile->cv_certificates)) as $cert)
                                @if(trim($cert)) <li>{{ $cert }}</li> @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Bottom Verification Badge -->
                    <div class="mt-8 pt-4 border-t border-gray-200 flex items-center justify-between text-[10px] text-slate-500">
                        <span>Career Intelligence &bull; Politeknik Negeri Padang</span>
                        <span class="flex items-center gap-1 font-semibold text-emerald-700">
                            <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Kompetensi Terverifikasi Sistem
                        </span>
                    </div>

                </div>

            </div>
        </div>

    </div>
</x-student-layout>





