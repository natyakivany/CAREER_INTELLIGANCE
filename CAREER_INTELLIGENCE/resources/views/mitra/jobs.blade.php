<x-mitra-layout>
    <div x-data="{
        editingJob: null,
        showEditModal: false,
        showDeleteModal: false,
        deleteJobId: null,
        editForm: {
            title: '',
            department: 'Teknologi Informasi',
            type: 'Pekerjaan Mandiri Kampus',
            location: 'Padang (Onsite)',
            duration: '6 Bulan',
            skills: '',
            status: 'Buka'
        },
        openEdit(job) {
            this.editingJob = job;
            this.editForm = {
                title: job.title,
                department: job.department,
                type: job.type,
                location: job.location,
                duration: job.duration,
                skills: (job.skills || []).join(', '),
                status: job.status
            };
            this.showEditModal = true;
        },
        confirmDelete(jobId) {
            this.deleteJobId = jobId;
            this.showDeleteModal = true;
        }
    }">

        {{-- Flash Message --}}
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

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1B3D] tracking-tight">Kelola Lowongan Pekerjaan</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30">
                        Mitra Kampus
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Buka dan pantau formasi lowongan pekerjaan untuk mahasiswa program studi IT & Elektro.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('mitra.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] text-xs font-semibold transition border border-slate-300">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            {{-- Kolom Kiri: Daftar Lowongan --}}
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#0B1B3D] tracking-tight">Daftar Lowongan Aktif Perusahaan</h2>
                    <span class="text-xs font-bold text-[#004AAD]">{{ $jobs->count() }} Posisi</span>
                </div>
                
                @forelse($jobs as $job)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 transition shadow-xl space-y-3 group">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#004AAD]">{{ $job->department }}</span>
                                <h3 class="text-base font-bold text-[#0B1B3D] mt-0.5">{{ $job->title }}</h3>
                                <p class="text-xs text-slate-500 mt-1">{{ $job->type }} • {{ $job->location }} • Durasi: {{ $job->duration }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5 shrink-0">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold 
                                    {{ $job->status === 'Buka' ? 'bg-[#004AAD]/15 text-[#004AAD] border border-[#004AAD]/30' : 
                                      ($job->status === 'Menunggu Persetujuan' ? 'bg-amber-500/15 text-amber-700 border border-amber-500/30' : 
                                      'bg-red-500/15 text-red-600 border border-red-500/30') }}">
                                    ● {{ $job->status }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/25">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    {{ $job->applications->count() }} Pelamar
                                </span>
                            </div>
                        </div>

                        {{-- Mahasiswa yang Melamar --}}
                        @if($job->applications->isNotEmpty())
                            <div class="pt-3 border-t border-slate-200">
                                <a href="{{ route('mitra.applications.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#004AAD]/10 hover:bg-[#004AAD]/20 text-[#004AAD] border border-[#004AAD]/30 text-[11px] font-bold transition">
                                    Lihat {{ $job->applications->count() }} Pelamar
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        @endif

                        {{-- Skills --}}
                        @if($job->skills && count($job->skills) > 0)
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach($job->skills as $skill)
                                    <span class="px-2 py-0.5 rounded-md bg-white text-slate-600 text-[11px] font-medium border border-slate-300/60">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Footer with Edit & Delete --}}
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500 text-[11px]">
                                Dibuat: {{ $job->created_at->translatedFormat('d M Y') }}
                            </span>
                            <div class="flex items-center gap-2">
                                {{-- Edit Button --}}
                                <button @click="openEdit({
                                    id: {{ $job->id }},
                                    title: '{{ addslashes($job->title) }}',
                                    department: '{{ addslashes($job->department) }}',
                                    type: '{{ addslashes($job->type) }}',
                                    location: '{{ addslashes($job->location) }}',
                                    duration: '{{ addslashes($job->duration) }}',
                                    skills: {{ json_encode($job->skills ?? []) }},
                                    status: '{{ $job->status }}'
                                })"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-accent/10 hover:bg-accent/20 text-blue-400 border border-blue-500/30 text-[11px] font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </button>
                                {{-- Delete Button --}}
                                <button @click="confirmDelete({{ $job->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-600 border border-red-500/30 text-[11px] font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#004AAD] text-white flex items-center justify-center shadow-md">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Belum ada lowongan yang dipublikasikan.</p>
                        <p class="text-slate-500 text-xs mt-1">Gunakan form di samping untuk membuka lowongan pertama Anda.</p>
                    </div>
                @endforelse
            </div>

            {{-- Kolom Kanan: Form Buat Lowongan Baru --}}
            <div class="lg:col-span-5">
                <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200 shadow-xl space-y-5 sticky top-8">
                    <div class="pb-3 border-b border-slate-200">
                        <h2 class="text-sm font-bold text-[#0B1B3D] tracking-tight">Form Buka Lowongan Pekerjaan Baru</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Mahasiswa akan langsung dapat melihat posisi ini di katalog karier.</p>
                    </div>
                    
                    <form action="{{ route('mitra.jobs.store') }}" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Nama Posisi / Formasi</label>
                            <input type="text" name="title" required placeholder="Contoh: Mobile App Developer / PLC Engineer" value="{{ old('title') }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD]">
                            @error('title') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Bidang Peminatan Sasaran</label>
                            <select name="department" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                                <option value="Teknologi Informasi">Teknologi Informasi (Software & Data)</option>
                                <option value="Teknik Elektro">Teknik Elektro (Embedded, IoT & PLC)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Jenis Pekerjaan</label>
                            <select name="type" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                                <option value="Pekerjaan Mandiri Kampus">Pekerjaan Mandiri Kampus</option>
                                <option value="Pekerjaan Bersertifikat">Pekerjaan Bersertifikat</option>
                                <option value="Pekerjaan Industri">Pekerjaan Industri</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1.5">Lokasi Kerja</label>
                                <input type="text" name="location" value="{{ old('location', 'Padang (Onsite)') }}" placeholder="Padang (Hybrid / Onsite)" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD]">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1.5">Durasi</label>
                                <input type="text" name="duration" value="{{ old('duration', '6 Bulan') }}" placeholder="6 Bulan" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD]">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Skill yang Dibutuhkan (Pisahkan dengan koma)</label>
                            <input type="text" name="skills" value="{{ old('skills') }}" placeholder="Contoh: Laravel, REST API, Git, PostgreSQL" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD]">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-3 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl transition shadow-sm text-xs sm:text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <span>Publikasikan Lowongan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- ===== EDIT MODAL ===== --}}
        <div x-show="showEditModal" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.outside="showEditModal = false"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white w-full max-w-lg rounded-2xl border border-slate-300 shadow-2xl p-6 sm:p-8 space-y-5">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-[#0B1B3D]">Edit Lowongan</h2>
                    <button @click="showEditModal = false" class="text-slate-500 hover:text-[#0B1B3D] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="'/mitra/jobs/' + (editingJob ? editingJob.id : '')" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1.5">Nama Posisi</label>
                        <input type="text" name="title" x-model="editForm.title" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1.5">Bidang Peminatan</label>
                        <select name="department" x-model="editForm.department" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="Teknologi Informasi">Teknologi Informasi (Software & Data)</option>
                            <option value="Teknik Elektro">Teknik Elektro (Embedded, IoT & PLC)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1.5">Jenis Pekerjaan</label>
                        <select name="type" x-model="editForm.type" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="Pekerjaan Mandiri Kampus">Pekerjaan Mandiri Kampus</option>
                            <option value="Pekerjaan Bersertifikat">Pekerjaan Bersertifikat</option>
                            <option value="Pekerjaan Industri">Pekerjaan Industri</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Lokasi</label>
                            <input type="text" name="location" x-model="editForm.location" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Durasi</label>
                            <input type="text" name="duration" x-model="editForm.duration" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1.5">Skill (pisahkan koma)</label>
                        <input type="text" name="skills" x-model="editForm.skills" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1.5">Status</label>
                        <select name="status" x-model="editForm.status" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="Buka" x-show="editingJob && editingJob.status !== 'Menunggu Persetujuan'">Buka</option>
                            <option value="Menunggu Persetujuan" x-show="editingJob && editingJob.status === 'Menunggu Persetujuan'">Menunggu Persetujuan</option>
                            <option value="Tutup">Tutup</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showEditModal = false" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] font-semibold text-xs transition border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-[#004AAD] hover:bg-[#003B8B] text-white font-bold rounded-xl transition shadow-lg shadow-[#004AAD]/20 text-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== DELETE CONFIRMATION MODAL ===== --}}
        <div x-show="showDeleteModal" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.outside="showDeleteModal = false"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white w-full max-w-sm rounded-2xl border border-slate-300 shadow-2xl p-6 sm:p-8 text-center space-y-5">
                
                <div class="w-14 h-14 mx-auto rounded-full bg-red-500/15 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold text-[#0B1B3D]">Hapus Lowongan?</h3>
                    <p class="text-sm text-slate-500 mt-2">Lowongan yang dihapus tidak dapat dikembalikan. Semua data terkait posisi ini akan hilang.</p>
                </div>

                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-blue-50 text-[#0B1B3D] font-semibold text-xs transition border border-slate-300">
                        Batal
                    </button>
                    <form :action="'/mitra/jobs/' + deleteJobId" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg shadow-red-600/20 text-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-mitra-layout>







