@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                <span>Standar Profesi & Benchmarking Industri</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-[#004AAD]/15 text-[#004AAD] rounded-full border border-[#004AAD]/30">
                    {{ $occupations->count() }} Profesi Acuan
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Daftar standar profesi kerja dan indikator keahlian yang digunakan sebagai benchmark algoritma skill gap mahasiswa.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Generate AI Dropdown -->
            <div x-data="{ aiDropdownOpen: false }" class="relative">
                <button type="button" @click="aiDropdownOpen = !aiDropdownOpen" class="px-4 py-2.5 bg-[#004AAD] hover:bg-[#003B8B] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-[#004AAD]/20 flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>Generate dengan AI</span>
                </button>
                <div x-show="aiDropdownOpen" @click.away="aiDropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden z-50">
                    <form method="POST" action="{{ route('admin.occupations.generate-ai') }}">
                        @csrf
                        <input type="hidden" name="field" value="it">
                        <button type="submit" @click="aiDropdownOpen = false" class="w-full text-left px-4 py-3 text-xs text-slate-600 hover:text-[#0B1B3D] hover:bg-white transition flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#004AAD]"></span> Untuk Teknologi Informasi
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.occupations.generate-ai') }}">
                        @csrf
                        <input type="hidden" name="field" value="elektro">
                        <button type="submit" @click="aiDropdownOpen = false" class="w-full text-left px-4 py-3 text-xs text-slate-600 hover:text-[#0B1B3D] hover:bg-white transition border-t border-slate-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span> Untuk Teknik Elektro
                        </button>
                    </form>
                </div>
            </div>

            <!-- Manual Add -->
            <button type="button" @click="$dispatch('open-create-occupation')" class="px-4 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-sm flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Manual</span>
            </button>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ 
    createModalOpen: false, 
    editModalOpen: false,
    addSkillModalOpen: false,
    deleteModalOpen: false,
    currentOccupation: null,
    targetOccupationForSkill: null
}" @open-create-occupation.window="createModalOpen = true">

    <!-- Occupations Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($occupations as $occ)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between hover:border-slate-300 transition shadow-xl relative group">
                
                <div class="space-y-4">
                    <!-- Badges & Actions Header -->
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg {{ $occ->field === 'elektro' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20' }}">
                            {{ $occ->field === 'elektro' ? 'Teknik Elektro' : 'Teknologi Informasi' }}
                        </span>
                        
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] font-bold text-slate-500 mr-1.5">
                                Demand: <strong class="text-[#004AAD]">{{ $occ->demand_level ?? 'Tinggi' }}</strong>
                            </span>

                            <!-- Edit Button -->
                            <button type="button" @click="currentOccupation = {{ json_encode($occ) }}; editModalOpen = true" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] hover:bg-slate-100 rounded-lg transition" title="Edit Profesi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <!-- Delete Button -->
                            <button type="button" @click="currentOccupation = {{ json_encode($occ) }}; deleteModalOpen = true" class="p-1.5 text-rose-400 hover:text-[#0B1B3D] hover:bg-rose-500/20 rounded-lg transition" title="Hapus Profesi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Title & Category -->
                    <div>
                        <h3 class="text-base font-extrabold text-[#0B1B3D] tracking-tight">{{ $occ->title }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $occ->category }}</p>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                        {{ $occ->description }}
                    </p>

                    <!-- Core Skills Benchmark Section -->
                    <div class="pt-3 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-2.5">
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Benchmark Keahlian Inti:</p>
                            <button type="button" @click="targetOccupationForSkill = {{ json_encode($occ) }}; addSkillModalOpen = true" class="text-[11px] font-bold text-[#004AAD] hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah Skill</span>
                            </button>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($occ->skills as $skill)
                                <div class="px-2 py-1 bg-white border border-slate-200 text-slate-600 rounded-lg text-[10px] flex items-center gap-1.5 group/skill hover:border-slate-300 transition">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $skill->benchmark_score >= 85 ? 'bg-[#004AAD]' : 'bg-teal-400' }}"></span>
                                    <span>{{ $skill->skill_name }}</span>
                                    <span class="text-slate-500 font-bold">({{ $skill->benchmark_score }}%)</span>
                                    
                                    <!-- Delete Skill Button -->
                                    <form method="POST" action="{{ route('admin.occupations.skills.destroy', $skill) }}" class="inline" onsubmit="return confirm('Hapus keahlian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-500 hover:text-rose-400 transition ml-0.5 p-0.5">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <span class="text-[11px] text-slate-500 italic">Belum ada benchmark skill terdaftar.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Modal 1: Tambah Profesi Baru -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="createModalOpen = false">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-[#0B1B3D]">Tambah Standar Profesi Baru</h3>
                <button type="button" @click="createModalOpen = false" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.occupations.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Profesi Sasaran *</label>
                    <input type="text" name="title" required placeholder="Contoh: AI & Machine Learning Engineer" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Bidang Keahlian *</label>
                        <select name="field" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="it">Teknologi Informasi</option>
                            <option value="elektro">Teknik Elektro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tingkat Permintaan Industri *</label>
                        <select name="demand_level" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="Sangat Tinggi">Sangat Tinggi</option>
                            <option value="Tinggi" selected>Tinggi</option>
                            <option value="Menengah">Menengah</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Profesi</label>
                        <input type="text" name="category" placeholder="Contoh: Artificial Intelligence" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi Tugas & Peran</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi ringkas peran profesi di industri..." class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Keahlian Benchmark Awal (Pisahkan dengan koma)</label>
                    <input type="text" name="skills" placeholder="Contoh: Python, PyTorch, Model Deployment, SQL" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <p class="text-[10px] text-slate-500 mt-1">Skill benchmark awal akan otomatis diatur dengan target 85%.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs shadow-md">
                        Simpan Standar Profesi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Profesi -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="editModalOpen = false">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-[#0B1B3D]">Edit Standar Profesi</h3>
                <button type="button" @click="editModalOpen = false" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/admin/occupations/' + currentOccupation?.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Profesi Sasaran *</label>
                    <input type="text" name="title" :value="currentOccupation?.title" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Bidang Keahlian *</label>
                        <select name="field" :value="currentOccupation?.field" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="it">Teknologi Informasi</option>
                            <option value="elektro">Teknik Elektro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tingkat Permintaan Industri *</label>
                        <select name="demand_level" :value="currentOccupation?.demand_level" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="Sangat Tinggi">Sangat Tinggi</option>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Menengah">Menengah</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Profesi</label>
                        <input type="text" name="category" :value="currentOccupation?.category" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi Tugas & Peran</label>
                    <textarea name="description" rows="3" :value="currentOccupation?.description" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs shadow-md">
                        Perbarui Standar Profesi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Tambah Skill Benchmark ke Profesi Terpilih -->
    <div x-show="addSkillModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="addSkillModalOpen = false">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-[#0B1B3D]">Tambah Skill Benchmark</h3>
                    <p class="text-xs text-[#004AAD]" x-text="'Untuk profesi: ' + targetOccupationForSkill?.title"></p>
                </div>
                <button type="button" @click="addSkillModalOpen = false" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/admin/occupations/' + targetOccupationForSkill?.id + '/skills'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Keahlian / Teknologi *</label>
                    <input type="text" name="skill_name" required placeholder="Contoh: Docker & Kubernetes" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Skor Benchmark Standar (50 - 100%) *</label>
                    <input type="number" name="benchmark_score" min="50" max="100" value="85" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="addSkillModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs shadow-md">
                        Tambahkan Skill
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Konfirmasi Hapus Profesi -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-center">
            <div class="w-12 h-12 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#0B1B3D]">Hapus Standar Profesi</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Apakah Anda yakin ingin menghapus profesi <strong class="text-[#0B1B3D]" x-text="currentOccupation?.title"></strong>? Seluruh benchmark skill terkait akan ikut terhapus.
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false; currentOccupation = null" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-xs">
                    Batal
                </button>
                <form :action="'/admin/occupations/' + currentOccupation?.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-[#0B1B3D] font-bold rounded-xl text-xs shadow-md shadow-rose-600/20">
                        Ya, Hapus Profesi
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection






