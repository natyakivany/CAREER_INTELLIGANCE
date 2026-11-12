<x-student-layout>
    @php
        // Hitung kelengkapan profil dinamis
        $completeness = 10; // Akun dasar
        $isGraduate = in_array($profile->semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']);
        
        if (Auth::user()->nim || $isGraduate) $completeness += 10;
        if ($profile->phone) $completeness += 10;
        if ($profile->target_occupation_id || !empty($profile->target_occupation_ids)) $completeness += 20;
        if ($profile->avatar) $completeness += 10;
        if (Auth::user()->portfolios()->count() > 0) $completeness += 20;
        
        // Tambahkan poin jika CV ATS sudah dibuat/lengkap
        $isCvReady = !empty($profile->cv_summary) && !empty($profile->cv_headline);
        if ($isCvReady) $completeness += 20;
        
        $completeness = min(100, $completeness);
    @endphp

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-[#004AAD] border border-blue-200">Langkah Awal</span>
                <span class="text-xs text-slate-500 font-medium">Sinergi Kampus & Industri</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-[#0B1B3D] tracking-tight">Profil Talenta & Preferensi Karier</h1>
            <p class="text-[13px] text-slate-500 mt-1">Lengkapi data pendidikan, peminatan bidang, dan target profesi untuk kalibrasi asesmen AI dan portofolio</p>
        </div>
        @if (session('status') === 'profile-updated')
            <div class="px-4 py-2.5 bg-[#004AAD]/15 border border-[#004AAD]/30 text-[#004AAD] text-xs font-semibold rounded-xl flex items-center gap-2 shadow-lg shadow-[#004AAD]/10">
                <svg class="w-4 h-4 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>Profil dan preferensi karier berhasil diperbarui!</span>
            </div>
        @endif
    </div>

    <div x-data="{
        photoPreview: '{{ Auth::user()->avatar_url }}',
        hasCustomPhoto: {{ $profile->avatar ? 'true' : 'false' }},
        removePhoto: false,
        fileName: '',
        updatePreview(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.removePhoto = false;
            this.fileName = file.name;

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const maxDim = 800;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxDim || height > maxDim) {
                        if (width > height) {
                            height = Math.round((height * maxDim) / width);
                            width = maxDim;
                        } else {
                            width = Math.round((width * maxDim) / height);
                            height = maxDim;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (blob && window.DataTransfer) {
                            try {
                                const optimizedFile = new File([blob], file.name.replace(/\.[^/.]+$/, '') + '.jpg', {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                const dt = new DataTransfer();
                                dt.items.add(optimizedFile);
                                const inputElem = document.getElementById('avatar-input');
                                if (inputElem) {
                                    inputElem.files = dt.files;
                                }
                            } catch (err) {
                                console.warn('Auto-compress fallback:', err);
                            }
                        }
                        this.photoPreview = canvas.toDataURL('image/jpeg', 0.88);
                    }, 'image/jpeg', 0.88);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        triggerFileInput() {
            document.getElementById('avatar-input').click();
        },
        markRemovePhoto() {
            this.removePhoto = true;
            this.fileName = '';
            this.photoPreview = 'https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Kandidat') }}&background=0f172a&color=00c99f&size=150';
            const input = document.getElementById('avatar-input');
            if (input) input.value = '';
        }
    }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Kolom Kiri: Avatar & Identitas Singkat -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-200 flex flex-col items-center text-center relative overflow-hidden">
                <!-- Aksen Latar Atas -->
                <div class="absolute top-0 left-0 w-full h-32 bg-blue-50/70 border-b border-slate-200 shadow-sm overflow-hidden"></div>
                
                <div class="relative w-28 h-28 rounded-full border-4 border-white shadow-xl overflow-hidden bg-white mt-8 mb-3 ring-4 ring-[#004AAD]/20 shadow-[0_0_20px_rgba(23,95,168,0.3)] group">
                    <img :src="photoPreview" alt="Avatar" class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                    
                    {{-- Hover Overlay to Change Photo --}}
                    <button type="button" @click="triggerFileInput" class="absolute inset-0 bg-black/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-200 flex flex-col items-center justify-center text-[#0B1B3D] cursor-pointer" title="Ubah Foto Profil">
                        <svg class="w-6 h-6 text-[#004AAD] mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-[10px] font-bold tracking-wide">Ubah Foto</span>
                    </button>
                </div>

                {{-- Action Buttons for Photo on Left Card --}}
                <div class="flex items-center gap-1.5 mb-2">
                    <button type="button" @click="triggerFileInput" class="px-3 py-1 rounded-lg bg-[#004AAD]/15 hover:bg-[#004AAD]/25 text-[#004AAD] border border-[#004AAD]/30 text-[11px] font-bold transition flex items-center gap-1 shadow-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span x-text="fileName ? 'Ganti Lagi' : (hasCustomPhoto && !removePhoto ? 'Ganti Foto' : 'Unggah Foto')"></span>
                    </button>
                    <button type="button" x-show="(hasCustomPhoto && !removePhoto) || fileName" @click="markRemovePhoto" class="px-2 py-1 rounded-lg bg-rose-500/15 hover:bg-rose-500/25 text-rose-600 border border-rose-500/30 text-[11px] font-semibold transition cursor-pointer" title="Hapus Foto">
                        Hapus
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 mb-2" x-show="fileName">
                    File: <span class="text-[#004AAD] font-semibold" x-text="fileName"></span>
                </p>
                
                <h2 class="text-lg font-bold text-[#0B1B3D] leading-tight">{{ Auth::user()->name ?? 'Kandidat' }}</h2>
                @php
                    $isGraduate = in_array($profile->semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']);
                @endphp
                @if(!$isGraduate)
                    <p class="text-[12px] text-slate-500 mt-1 font-medium">
                        {{ Auth::user()->nim ? 'NIM/NISN. ' . Auth::user()->nim : 'NIM/NISN belum diisi' }}
                    </p>
                @else
                    <p class="text-[12px] text-slate-500 mt-1 font-medium">
                        💼 Fresh Graduate
                    </p>
                @endif
                
                  @if($profile->institution_name)
                      <p class="text-[12px] text-slate-500 mt-0.5 font-medium flex items-center justify-center gap-1">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                          {{ $profile->institution_name }}
                      </p>
                  @endif
                  <div class="mt-2.5 inline-flex items-center px-3 py-1 rounded-full bg-[#004AAD]/15 text-[#004AAD] font-semibold text-[11px] border border-[#004AAD]/30">
                    {{ $profile->prodi ?? 'Teknik Komputer' }} • {{ $profile->jurusan ?? 'Teknologi Informasi' }}
                </div>

                @if($profile->targetOccupations->isNotEmpty())
                    <div class="mt-3 flex flex-wrap justify-center gap-1.5 max-w-xs">
                        @foreach($profile->targetOccupations as $occ)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#004AAD]/10 text-emerald-700 border border-[#004AAD]/20 font-semibold truncate max-w-[200px]">
                                🎯 {{ $occ->title }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="w-full h-px bg-slate-200 my-6"></div>

                <div class="w-full">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11.5px] font-bold text-slate-600">Kelengkapan Profil</span>
                        <span class="text-[12px] font-extrabold text-[#004AAD]">{{ $completeness }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 border border-slate-200 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-[#004AAD] rounded-full transition-all duration-500 shadow-sm shadow-[#004AAD]/30" style="width: {{ $completeness }}%;"></div>
                    </div>

                    @if($completeness < 100)
                        <div class="mt-3 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-left">
                            <span class="text-[11px] font-bold text-slate-700 block mb-1">Tinggal sedikit lagi menuju 100%:</span>
                            <ul class="text-[10.5px] space-y-1 text-slate-600">
                                @if(!$profile->avatar)
                                    <li class="flex items-center gap-1.5 text-amber-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                        Unggah Foto Profil (+10%)
                                    </li>
                                @endif
                                @if(!Auth::user()->nim && !$isGraduate)
                                    <li class="flex items-center gap-1.5 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Isi NIM / NISN (+10%)
                                    </li>
                                @endif
                                @if(!$profile->phone)
                                    <li class="flex items-center gap-1.5 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Isi Nomor Kontak/WhatsApp (+10%)
                                    </li>
                                @endif
                                @if(!$profile->target_occupation_id && empty($profile->target_occupation_ids))
                                    <li class="flex items-center gap-1.5 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Pilih Target Profesi (+20%)
                                    </li>
                                @endif
                                @if(Auth::user()->portfolios()->count() == 0)
                                    <li class="flex items-center gap-1.5 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Unggah Portofolio Proyek (+20%)
                                    </li>
                                @endif
                                @if(!$isCvReady)
                                    <li class="flex items-center gap-1.5 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Lengkapi Ringkasan CV ATS (+20%)
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <p class="text-[11px] text-slate-500 mt-3 leading-relaxed text-left">
                        Profil & portofolio menyumbang <strong class="text-[#004AAD]">60%</strong> bobot kesiapan kerja Anda sebelum asesmen.
                    </p>
                </div>

                <a href="{{ route('portfolio') }}" class="w-full mt-6 px-4 py-2.5 bg-white border border-slate-300 text-slate-800 text-[12px] font-bold rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-[#004AAD] transition shadow-sm flex items-center justify-between">
                    <span>💼 Kelola Portofolio ({{ Auth::user()->portfolios()->count() }})</span>
                    <span class="text-[11px] font-extrabold text-[#004AAD] bg-[#004AAD]/10 px-2.5 py-0.5 rounded-lg border border-[#004AAD]/20">{{ $portfolioCompleteness ?? app(\App\Services\SkillGapService::class)->calculatePortfolioCompleteness(Auth::user()) }}% Lengkap</span>
                </a>

                @php
                    $isCvReady = !empty($profile->cv_summary) && !empty($profile->cv_headline);
                @endphp
                <a href="{{ route('cv-builder') }}" class="w-full mt-3 px-4 py-2.5 {{ $isCvReady ? 'bg-emerald-50 hover:bg-emerald-100 border-emerald-200 text-emerald-700' : 'bg-slate-50 hover:bg-slate-100 border-slate-300 text-slate-500' }} border text-[12px] font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    @if($isCvReady)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status CV: Tersedia (Aktif)
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Status CV: Belum Dibuat
                    @endif
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Formulir Data & Keamanan -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Form Data Pribadi -->
            <div class="bg-white p-6 md:p-8 shadow-xl border border-slate-200 rounded-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-bold text-[#0B1B3D] mb-1">Informasi Akademik & Preferensi Karier</h3>
                        <p class="text-[12px] text-slate-500">Lengkapi data ini untuk kalibrasi generator soal Gemini AI dan tolak ukur analisis kesenjangan skill.</p>
                    </div>
                    <button type="submit" form="profile-form" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-xs rounded-xl shadow-md shadow-[#004AAD]/20 transition shrink-0 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Profil</span>
                    </button>
                </div>
                
                <form id="profile-form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('patch')

                    <!-- Hidden Avatar File Input & Remove Flag -->
                    <input type="file" id="avatar-input" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only" @change="updatePreview($event)">
                    <input type="hidden" name="remove_avatar" :value="removePhoto ? '1' : '0'">

                    <!-- Bagian Foto Profil (Form Input Bar) -->
                    <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-[#004AAD]/50 shadow-md shrink-0 bg-white relative">
                                <img :src="photoPreview" alt="Preview Foto" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <label class="block text-xs font-bold text-[#0B1B3D] uppercase tracking-wider">Foto Profil Mahasiswa</label>
                                <p class="text-[11px] text-slate-500 mt-0.5">Format JPG, PNG, atau WEBP (Maksimal 10MB, otomatis dioptimalkan). Foto akan tampil di profil dan kartu kandidat mitra.</p>
                                <div x-show="fileName" class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-[#004AAD] text-[11px] font-semibold">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Foto dipilih: <strong x-text="fileName"></strong> — Klik <strong>"Simpan Profil"</strong> untuk menyimpan permanen.</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="triggerFileInput" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-300 transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Pilih Foto</span>
                            </button>
                            <button type="button" x-show="(hasCustomPhoto && !removePhoto) || fileName" @click="markRemovePhoto" class="px-3 py-2 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-600 border border-rose-500/30 text-xs font-semibold transition cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('avatar')" class="mt-1 text-xs text-rose-600" />

                    <!-- Nama, Status Pendidikan, NIM, Asal Sekolah -->
                      <div x-data="{ semesterVal: '{{ old('semester', $profile->semester ?? '') }}' }">
                          
                          <!-- Baris 1: Nama & Status Pendidikan -->
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                              <div>
                                  <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                  <input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                                  <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-600" />
                              </div>
                          <div>
                              <label for="semester" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Pendidikan / Semester Saat Ini</label>
                              <select id="semester" name="semester" x-model="semesterVal" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner cursor-pointer">
                                  <option value="Lulusan SMK/SMA (Siap Kerja)" {{ old('semester', $profile->semester) === 'Lulusan SMK/SMA (Siap Kerja)' ? 'selected' : '' }}>Lulusan SMK/SMA (Siap Kerja)</option>
                                  <option value="Siswa SMK/SMA Kelas 12" {{ old('semester', $profile->semester) === 'Siswa SMK/SMA Kelas 12' ? 'selected' : '' }}>Siswa SMK/SMA Kelas 12</option>
                                  @for ($i = 1; $i <= 8; $i++)
                                      <option value="Semester {{ $i }}" {{ old('semester', $profile->semester) === "Semester $i" ? 'selected' : '' }}>Mahasiswa Semester {{ $i }}</option>
                                  @endfor
                                  <option value="Alumni / Fresh Graduate PT" {{ old('semester', $profile->semester) === 'Alumni / Fresh Graduate PT' ? 'selected' : '' }}>Alumni / Fresh Graduate Perguruan Tinggi</option>
                              </select>
                          </div>
                          </div>

                          <!-- Baris 2: NIM & Asal Sekolah -->
                          <div class="grid grid-cols-1 mt-6" :class="(semesterVal === 'Alumni / Fresh Graduate PT' || semesterVal === 'Lulusan SMK/SMA (Siap Kerja)') ? '' : 'md:grid-cols-2 gap-6'">
                              
                              <div x-show="semesterVal !== 'Alumni / Fresh Graduate PT' && semesterVal !== 'Lulusan SMK/SMA (Siap Kerja)'" x-transition>
                                  <label for="nim" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">NIM / NISN / ID Identitas</label>
                                  <input id="nim" name="nim" type="text" value="{{ old('nim', Auth::user()->nim) }}" placeholder="Contoh: 2111082012 (NIM) atau 0051234567 (NISN)" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                                  <x-input-error :messages="$errors->get('nim')" class="mt-1 text-xs text-rose-600" />
                              </div>

                              <div>
                                  <label for="institution_name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Asal Sekolah / Universitas</label>
                                  <input id="institution_name" name="institution_name" type="text" value="{{ old('institution_name', $profile->institution_name ?? '') }}" placeholder="Contoh: Politeknik Negeri Padang atau SMKN 1 Padang" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                                  <x-input-error :messages="$errors->get('institution_name')" class="mt-1 text-xs text-rose-600" />
                              </div>
                          </div>
                      </div>

                      <!-- Jurusan & Prodi / Keahlian -->
                    @php
                        $currentJurusan = old('jurusan', $profile->jurusan ?? 'Teknologi Informasi');
                        // Normalisasi nama jurusan jika lama
                        if ($currentJurusan === 'Teknologi Informasi') {
                            $currentJurusan = 'Teknologi Informasi (Perguruan Tinggi)';
                        } elseif ($currentJurusan === 'Teknik Elektro') {
                            $currentJurusan = 'Teknik Elektro (Perguruan Tinggi)';
                        }
                    @endphp
                    <div x-data="{
                        jurusan: '{{ $currentJurusan }}',
                        prodi: '{{ old('prodi', $profile->prodi) === 'Teknologi Rekayasa Perangkat Lunak' ? 'Teknik Rekayasa Perangkat Lunak' : old('prodi', $profile->prodi ?? 'Teknik Komputer') }}',
                        prodiList: {
                            'Teknologi Informasi (Perguruan Tinggi)': [
                                'Teknik Rekayasa Perangkat Lunak',
                                'Teknik Komputer',
                                'Manajemen Informatika',
                                'Animasi & Desain Grafis'
                            ],
                            'Teknik Elektro (Perguruan Tinggi)': [
                                'Teknik Elektronika',
                                'Teknik Telekomunikasi',
                                'Teknik Listrik'
                            ],
                            'SMK - Teknologi Informasi & Komunikasi': [
                                'Rekayasa Perangkat Lunak (RPL)',
                                'Teknik Komputer & Jaringan (TKJ)',
                                'Multimedia / Desain Komunikasi Visual',
                                'Pengembangan Gim & Animasi'
                            ],
                            'SMK - Teknik Elektronika & Otomasi': [
                                'Teknik Elektronika Industri',
                                'Teknik Otomasi Industri',
                                'Teknik Audio Video',
                                'Teknik Mekatronika / IoT'
                            ],
                            'Umum / Otodidak / Lainnya': [
                                'Teknologi & Informasi Umum',
                                'Pemrograman & Otodidak'
                            ]
                        }
                    }" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jurusan" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jurusan / Rumpun Keahlian</label>
                            <select id="jurusan" name="jurusan" x-model="jurusan" @change="prodi = prodiList[jurusan] ? prodiList[jurusan][0] : ''" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner cursor-pointer">
                                <option value="Teknologi Informasi (Perguruan Tinggi)">Teknologi Informasi (Perguruan Tinggi)</option>
                                <option value="Teknik Elektro (Perguruan Tinggi)">Teknik Elektro (Perguruan Tinggi)</option>
                                <option value="SMK - Teknologi Informasi & Komunikasi">SMK - Teknologi Informasi & Komunikasi (RPL/TKJ)</option>
                                <option value="SMK - Teknik Elektronika & Otomasi">SMK - Teknik Elektronika & Otomasi</option>
                                <option value="Umum / Otodidak / Lainnya">Umum / Otodidak / Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="prodi" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Program Studi / Konsentrasi Keahlian</label>
                            <select id="prodi" name="prodi" x-model="prodi" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner cursor-pointer">
                                <template x-for="item in (prodiList[jurusan] || [])" :key="item">
                                    <option :value="item" x-text="item"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- IPK / Nilai Raport -->
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                          <div>
                              <label for="ipk" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">IPK / Nilai Rata-Rata Ijazah</label>
                              <input id="ipk" name="ipk" type="number" step="0.01" min="0" max="100" value="{{ old('ipk', $profile->ipk ?? 3.50) }}" placeholder="Contoh: 3.50 (IPK) atau 85.0 (Nilai Raport)" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                              <x-input-error :messages="$errors->get('ipk')" class="mt-1 text-xs text-rose-600" />
                          </div>
                      </div>
                      <!-- Target Bidang & Profesi Sasaran -->
                    @php
                        $selectedOccIds = old('target_occupation_ids', $profile->target_occupation_ids ?? ($profile->target_occupation_id ? [$profile->target_occupation_id] : []));
                        if (!is_array($selectedOccIds)) {
                            $selectedOccIds = [$selectedOccIds];
                        }
                        $selectedOccIds = array_values(array_map('intval', array_filter($selectedOccIds)));
                        $initialTargetField = old('target_field', $profile->target_field ?? 'it');
                        if (in_array($initialTargetField, ['all', 'both'])) {
                            $initialTargetField = 'all';
                        }
                    @endphp

                    <div class="p-5 rounded-2xl bg-slate-50/90 border border-slate-200 space-y-5"
                         x-data="{
                             targetField: '{{ $initialTargetField }}',
                             open: false,
                             search: '',
                             selectedFieldFilter: '{{ $initialTargetField }}',
                             selected: {{ json_encode($selectedOccIds) }},
                             isLoadingAi: false,
                             recommendedIds: [],
                             semester: '',
                             occupations: {{ json_encode($occupations->map(fn($o) => [
                                 'id' => $o->id,
                                 'title' => $o->title,
                                 'field' => $o->field,
                                 'category' => $o->category,
                                 'demand' => $o->demand_level,
                                 'salary' => $o->average_salary,
                             ])) }},
                             init() {
                                 this.semester = document.getElementById('semester').value;
                                 document.getElementById('semester').addEventListener('change', (e) => {
                                     this.semester = e.target.value;
                                 });

                                 this.$watch('targetField', (val) => {
                                     if (val === 'it') {
                                         this.selectedFieldFilter = 'it';
                                     } else if (val === 'elektro') {
                                         this.selectedFieldFilter = 'elektro';
                                     } else {
                                         this.selectedFieldFilter = 'all';
                                     }
                                     this.filterSelectionsByField(val);
                                 });
                                 this.filterSelectionsByField(this.targetField);
                             },
                             filterSelectionsByField(val) {
                                 if (val === 'it' || val === 'elektro') {
                                     this.selected = this.selected.filter(id => {
                                         const occ = this.occupations.find(o => o.id === parseInt(id));
                                         return occ && occ.field === val;
                                     });
                                 }
                             },
                             toggle(id) {
                                 id = parseInt(id);
                                 if (this.selected.includes(id)) {
                                     this.selected = this.selected.filter(item => item !== id);
                                 } else {
                                     this.selected.push(id);
                                 }
                             },
                             remove(id) {
                                 id = parseInt(id);
                                 this.selected = this.selected.filter(item => item !== id);
                             },
                             isSelected(id) {
                                 return this.selected.includes(parseInt(id));
                             },
                             isRecommended(id) {
                                 return this.recommendedIds.includes(parseInt(id));
                             },
                             async fetchAiRecommendations() {
                                 this.isLoadingAi = true;
                                 this.recommendedIds = [];
                                 
                                 const jurusan = document.getElementById('jurusan').value;
                                 const prodi = document.getElementById('prodi').value;
                                 const semester = document.getElementById('semester').value;
                                 
                                 try {
                                     const response = await fetch('{{ route('profile.recommend-occupations') }}', {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                         },
                                         body: JSON.stringify({
                                             jurusan: jurusan,
                                             prodi: prodi,
                                             semester: semester,
                                             target_field: this.targetField
                                         })
                                     });
                                     
                                     const data = await response.json();
                                     if(data.success) {
                                         this.recommendedIds = data.recommended_ids;
                                         if(this.recommendedIds.length > 0) {
                                             this.open = true;
                                         }
                                     }
                                 } catch (e) {
                                     console.error(e);
                                 } finally {
                                     this.isLoadingAi = false;
                                 }
                             },
                             get selectedOccupations() {
                                 return this.occupations.filter(o => this.selected.includes(o.id));
                             },
                             get filteredOccupations() {
                                 let filtered = this.occupations.filter(o => {
                                     let matchesField = false;
                                     if (this.targetField === 'it') {
                                         matchesField = (o.field === 'it');
                                     } else if (this.targetField === 'elektro') {
                                         matchesField = (o.field === 'elektro');
                                     } else {
                                         matchesField = (this.selectedFieldFilter === 'all' || this.selectedFieldFilter === 'both') || o.field === this.selectedFieldFilter;
                                     }
                                     const matchesSearch = !this.search || o.title.toLowerCase().includes(this.search.toLowerCase()) || (o.category && o.category.toLowerCase().includes(this.search.toLowerCase()));
                                     return matchesField && matchesSearch;
                                 });
                                 
                                 const isSmk = this.semester.toLowerCase().includes('smk') || this.semester.toLowerCase().includes('sma');
                                 
                                 filtered.forEach(o => {
                                     let score = 0;
                                     const title = o.title.toLowerCase();
                                     if (isSmk) {
                                         if (title.includes('junior') || title.includes('technician') || title.includes('support') || title.includes('operator') || title.includes('admin') || title.includes('tester') || title.includes('assistant')) {
                                             score = 5;
                                         }
                                     } else {
                                         if (title.includes('engineer') || title.includes('architect') || title.includes('manager') || title.includes('senior') || title.includes('analyst') || title.includes('scientist') || title.includes('developer')) {
                                             score = 5;
                                         }
                                     }
                                     o.autoScore = score;
                                 });
                                 
                                 filtered.sort((a, b) => {
                                     const aRec = this.recommendedIds.includes(a.id) ? 100 : 0;
                                     const bRec = this.recommendedIds.includes(b.id) ? 100 : 0;
                                     
                                     const aTotal = aRec + a.autoScore;
                                     const bTotal = bRec + b.autoScore;
                                     
                                     if (aTotal > bTotal) return -1;
                                     if (aTotal < bTotal) return 1;
                                     return 0;
                                 });
                                 
                                 return filtered;
                             }
                         }">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <div>
                                <label for="target_field" class="block text-xs font-bold text-[#004AAD] uppercase tracking-wider mb-2">
                                    🎯 Bidang Asesmen Sasaran
                                </label>
                                <select id="target_field" name="target_field" x-model="targetField" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner cursor-pointer">
                                    <option value="it">💻 Bidang IT (Software & Data)</option>
                                    <option value="elektro">⚡ Bidang Elektro (Embedded & IoT)</option>
                                    <option value="all">🌐 Bidang IT & Elektro (Software, IoT & Data)</option>
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1.5">Menentukan kustomisasi bank soal AI dan penyaringan target profesi.</p>
                            </div>

                            <!-- Multi-Select Target Profesi Industri -->
                            <div class="relative" @click.outside="open = false">
                                
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-[#004AAD] uppercase tracking-wider">
                                        💼 Target Profesi Industri
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="fetchAiRecommendations()" class="px-2 py-1 bg-accent/20 hover:bg-accent/30 text-indigo-700 border border-indigo-600/30 rounded-lg text-[10px] font-bold transition flex items-center gap-1 shadow-sm">
                                            <svg x-show="isLoadingAi" class="animate-spin w-3 h-3 text-indigo-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span x-show="!isLoadingAi">✨ Rekomendasi AI</span>
                                            <span x-show="isLoadingAi">Menganalisis...</span>
                                        </button>
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-[#004AAD] border border-blue-200">
                                            <span x-text="selected.length"></span> Terpilih
                                        </span>
                                    </div>
                                </div>

                                <!-- Hidden Inputs for Form Submission -->
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="target_occupation_ids[]" :value="id">
                                </template>
                                <input type="hidden" name="target_occupation_id" :value="selected[0] || ''">

                                <!-- Trigger Box -->
                                <div @click="open = !open" class="min-h-[42px] w-full bg-white border border-slate-300 hover:border-slate-300 rounded-xl px-3 py-2 cursor-pointer transition shadow-inner flex items-center justify-between gap-2" :class="open ? 'border-[#004AAD] ring-1 ring-[#004AAD]' : ''">
                                    <div class="flex flex-wrap gap-1.5 flex-1">
                                        <template x-if="selectedOccupations.length === 0">
                                            <span class="text-xs text-slate-500 italic py-0.5">-- Klik untuk memilih satu atau beberapa profesi impian --</span>
                                        </template>
                                        <template x-for="occ in selectedOccupations" :key="occ.id">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#004AAD] border border-blue-200">
                                                <span x-text="'[' + occ.field.toUpperCase() + '] ' + occ.title"></span>
                                                <button type="button" @click.stop="remove(occ.id)" class="text-slate-500 hover:text-rose-600 rounded-full w-4 h-4 flex items-center justify-center transition">
                                                    &times;
                                                </button>
                                            </span>
                                        </template>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-500 transition-transform shrink-0" :class="open ? 'rotate-180 text-[#004AAD]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>

                                <!-- Dropdown Panel -->
                                <div x-show="open" x-cloak x-transition class="absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-300 rounded-2xl shadow-2xl overflow-hidden">
                                    <!-- Search & Filter Bar inside dropdown -->
                                    <div class="p-3 border-b border-slate-200 bg-white space-y-2.5">
                                        <div class="relative">
                                            <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            <input type="text" x-model="search" placeholder="Cari nama profesi atau kategori..." class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-xs rounded-xl pl-9 pr-3 py-2 focus:outline-none focus:border-[#004AAD]">
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <template x-if="targetField === 'all'">
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" @click="selectedFieldFilter = 'all'" :class="(selectedFieldFilter === 'all' || selectedFieldFilter === 'both') ? 'bg-[#004AAD] text-white font-bold' : 'bg-slate-100 text-slate-700 hover:text-white'" class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">Semua</button>
                                                    <button type="button" @click="selectedFieldFilter = 'it'" :class="selectedFieldFilter === 'it' ? 'bg-[#004AAD]/20 text-emerald-700 font-bold border border-[#004AAD]/40' : 'bg-slate-100 text-slate-700 hover:text-[#004AAD]'" class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">💻 IT</button>
                                                    <button type="button" @click="selectedFieldFilter = 'elektro'" :class="selectedFieldFilter === 'elektro' ? 'bg-accent/20 text-sky-700 font-bold border border-sky-600/30' : 'bg-slate-100 text-slate-700 hover:text-[#0B1B3D]'" class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">⚡ Elektro</button>
                                                </div>
                                            </template>
                                            <template x-if="targetField === 'it'">
                                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#004AAD]/20 text-emerald-700 border border-[#004AAD]/40 flex items-center gap-1">
                                                    💻 Khusus Profesi IT (Software & Data)
                                                </span>
                                            </template>
                                            <template x-if="targetField === 'elektro'">
                                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-accent/20 text-sky-700 border border-sky-600/30 flex items-center gap-1">
                                                    ⚡ Khusus Profesi Elektro (Embedded & IoT)
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Occupation List -->
                                    <div class="max-h-60 overflow-y-auto p-2 space-y-1 scrollbar-thin scrollbar-thumb-slate-700">
                                        <template x-for="occ in filteredOccupations" :key="occ.id">
                                            <div @click="toggle(occ.id)" class="flex items-center justify-between p-2.5 rounded-xl cursor-pointer transition relative overflow-hidden" :class="isSelected(occ.id) ? 'bg-[#004AAD]/10 border border-[#004AAD]/30' : (isRecommended(occ.id) ? 'bg-accent/10 border border-indigo-600/30' : (occ.autoScore > 0 ? 'bg-amber-500/5 border border-amber-500/20' : 'hover:bg-blue-50/60 border border-transparent'))">
                                                
                                                <div x-show="isRecommended(occ.id)" class="absolute top-0 right-0 w-1 h-full bg-accent shadow-[0_0_8px_rgba(99,102,241,0.6)]"></div>
                                                <div x-show="!isRecommended(occ.id) && occ.autoScore > 0" class="absolute top-0 right-0 w-1 h-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.4)]"></div>

                                                <div class="flex items-center gap-3 flex-1 min-w-0 pr-2">
                                                    <!-- Checkbox -->
                                                    <div class="w-4 h-4 rounded border flex items-center justify-center transition shrink-0" :class="isSelected(occ.id) ? 'bg-[#004AAD] border-[#004AAD] text-white' : (isRecommended(occ.id) ? 'border-accent/50 bg-white' : (occ.autoScore > 0 ? 'border-amber-500/50 bg-white' : 'border-slate-300 bg-white'))">
                                                        <svg x-show="isSelected(occ.id)" class="w-3 h-3 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <div class="truncate">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <span class="text-[9px] font-black px-1.5 py-0.2 rounded" :class="occ.field === 'elektro' ? 'bg-cyan-500/20 text-cyan-700' : 'bg-[#004AAD]/20 text-emerald-700'" x-text="occ.field.toUpperCase()"></span>
                                                            <span class="text-xs font-bold text-[#0B1B3D] truncate" x-text="occ.title"></span>
                                                            <span x-show="isRecommended(occ.id)" class="text-[9px] font-bold text-indigo-700 bg-accent/20 px-1 rounded border border-indigo-600/30 shrink-0">✨ AI Match</span>
                                                            <span x-show="!isRecommended(occ.id) && occ.autoScore > 0" class="text-[9px] font-bold text-amber-700 bg-amber-500/10 px-1 rounded border border-amber-500/30 shrink-0" x-text="semester.toLowerCase().includes('smk') ? '🎓 Standar SMK' : '🎓 Standar Mahasiswa'"></span>
                                                        </div>
                                                        <p class="text-[10px] text-slate-500 truncate mt-0.5" x-text="occ.category || 'Standar Industri'"></p>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded shrink-0" :class="occ.demand === 'Sangat Tinggi' ? 'bg-amber-500/15 text-amber-700' : 'bg-slate-100 text-slate-700'" x-text="occ.demand"></span>
                                            </div>
                                        </template>
                                        <template x-if="filteredOccupations.length === 0">
                                            <div class="p-4 text-center text-xs text-slate-500">
                                                Tidak ada profesi yang cocok dengan pencarian / filter bidang ini.
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Dropdown Footer -->
                                    <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                                        <span class="text-slate-500 text-[11px]"><span x-text="selected.length" class="text-[#004AAD] font-bold"></span> profesi dipilih</span>
                                        <button type="button" @click="open = false" class="px-3 py-1.5 rounded-lg bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold text-xs transition">
                                            Selesai Memilih
                                        </button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-slate-500 mt-1.5">Klik opsi di atas untuk memilih satu atau beberapa profesi sekaligus.</p>
                            </div>
                        </div>

                        <!-- Selected Occupations Showcase Badge List -->
                        @php
                            $targetOccupationsList = $profile->targetOccupations;
                        @endphp
                        @if($targetOccupationsList->isNotEmpty())
                            <div class="pt-3 border-t border-slate-200">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">
                                    Daftar Target Profesi Tersimpan (Prioritas Utama: {{ $targetOccupationsList->first()->title }}):
                                </span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($targetOccupationsList as $idx => $occ)
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs">
                                            <span class="w-5 h-5 rounded-full bg-[#004AAD]/15 text-[#004AAD] text-[10px] font-black flex items-center justify-center border border-[#004AAD]/30">
                                                #{{ $idx + 1 }}
                                            </span>
                                            <span class="font-bold text-[#0B1B3D]">[{{ strtoupper($occ->field) }}] {{ $occ->title }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Email & No HP -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Email Akun</label>
                            <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600" />
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nomor WhatsApp / Kontak</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $profile->phone ?? '') }}" placeholder="Contoh: +62 812-3456-7890" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                        </div>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Ringkasan Minat & Profil Profesional</label>
                        <textarea id="bio" name="bio" rows="3" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] placeholder-slate-500 text-[13px] rounded-xl px-4 py-3 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner resize-none leading-relaxed" placeholder="Ceritakan latar belakang teknis Anda, minat karier, dan proyek yang sedang Anda pelajari...">{{ old('bio', $profile->bio ?? 'Mahasiswa teknik dengan antusiasme tinggi pada pengembangan teknologi dan pemecahan masalah praktis.') }}</textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold text-[13px] rounded-xl shadow-sm transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Profil & Preferensi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bagian Keamanan Kata Sandi -->
            <div class="bg-white p-6 md:p-8 shadow-xl border border-slate-200 rounded-2xl">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Bagian Hapus Akun -->
            <div class="bg-white p-6 md:p-8 shadow-xl border border-rose-500/30 rounded-2xl relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500"></div>
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-student-layout>













