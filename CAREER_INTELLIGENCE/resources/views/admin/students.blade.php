@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2.5">
                <span>Manajemen Data Mahasiswa</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-[#004AAD] rounded-full border border-blue-200 font-extrabold">
                    {{ $students->total() }} Mahasiswa
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola data akademik, portofolio proyek terunggah, dan reset akun mahasiswa.
            </p>
        </div>
        <button type="button" @click="$dispatch('open-create-modal')" class="px-4 py-2.5 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-sm flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Tambah Mahasiswa Manual</span>
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ 
    selectedStudent: null, 
    editStudentData: null,
    editModalOpen: false,
    createStudentModalOpen: false,
    deleteModalOpen: false, 
    userToDelete: null 
}" @open-create-modal.window="createStudentModalOpen = true">

    <!-- Search & Filter Controls -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-md">
        <form method="GET" action="{{ route('admin.students') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            
            <!-- Search Keyword -->
            <div class="sm:col-span-3 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama talenta..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] placeholder:text-slate-500 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD]">
            </div>

            <!-- Tingkat Jenjang Filter -->
            <div class="sm:col-span-3">
                <select name="tingkat" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="">Semua Jenjang</option>
                    <option value="mahasiswa" {{ request('tingkat') === 'mahasiswa' ? 'selected' : '' }}>🎓 Mahasiswa (PT)</option>
                    <option value="smk" {{ request('tingkat') === 'smk' ? 'selected' : '' }}>🏫 Siswa / Lulusan SMK</option>
                </select>
            </div>

            <!-- Field Filter -->
            <div class="sm:col-span-2">
                <select name="field" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="">Semua Bidang</option>
                    <option value="it" {{ request('field') === 'it' ? 'selected' : '' }}>Teknologi Informasi</option>
                    <option value="elektro" {{ request('field') === 'elektro' ? 'selected' : '' }}>Teknik Elektro</option>
                </select>
            </div>

            <!-- Sort Option -->
            <div class="sm:col-span-2">
                <select name="sort" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    <option value="">Terbaru</option>
                    <option value="score_desc" {{ request('sort') === 'score_desc' ? 'selected' : '' }}>Skor Tertinggi</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-md">
                    Cari
                </button>
                @if (request()->hasAny(['search', 'tingkat', 'field', 'sort']))
                    <a href="{{ route('admin.students') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-xl transition" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Student Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase bg-white text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Mahasiswa</th>
                        <th class="py-3.5 px-3">Program Studi & IPK</th>
                        <th class="py-3.5 px-3 text-center">Portofolio</th>
                        <th class="py-3.5 px-3">Target Profesi</th>
                        <th class="py-3.5 px-3 text-center">Kesiapan Kerja</th>
                        <th class="py-3.5 px-3 text-right">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($students as $student)
                        @php
                            $profile = $student->studentProfile;
                            $score = $profile?->employability_score ?? 0;
                            
                            $studentDataJson = json_encode([
                                'name' => $student->name,
                                'nim' => $student->nim,
                                'prodi' => $profile?->prodi,
                                'portfolios' => $student->portfolios->map(fn($p) => [
                                    'title' => $p->title,
                                    'category' => $p->category,
                                    'tech_stack' => $p->tech_stack,
                                    'project_url' => $p->project_url,
                                    'repository_url' => $p->repository_url,
                                    'description' => $p->description,
                                ]),
                                'assessments' => $student->assessments->where('status', 'completed')->map(fn($a) => [
                                    'field' => strtoupper($a->field),
                                    'score' => round($a->total_score),
                                    'date' => $a->created_at->format('d M Y'),
                                    'feedback' => $a->ai_feedback,
                                ])->values()
                            ]);
                        @endphp
                        <tr class="hover:bg-blue-50/40 transition">
                            <!-- Student Info -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#004AAD] font-extrabold flex items-center justify-center text-xs shrink-0 border border-blue-200 shadow-sm">
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="font-bold text-[#0B1B3D] text-xs sm:text-sm">{{ $student->name }}</p>
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-extrabold {{ $profile?->isSmk() ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-blue-50 text-[#004AAD] border border-blue-200' }}">
                                                {{ $profile?->isSmk() ? '🏫 SMK' : '🎓 PT' }}
                                            </span>
                                        </div>
                                        @if($student->nim)
                                            <p class="text-[11px] text-slate-500">NIM/NISN: <strong class="text-slate-600">{{ $student->nim }}</strong></p>
                                        @else
                                            <div class="mt-0.5">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    🎓 Fresh Graduate
                                                </span>
                                            </div>
                                        @endif
                                        <p class="text-[10px] text-slate-500">{{ $student->email }}</p>
                                        <div class="mt-0.5">
                                            @if($profile?->isCvFilled())
                                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200" title="Dokumen CV ATS Aktif">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    CV Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[9px] font-medium text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200" title="CV ATS Belum Diisi (Tidak Aktif)">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                                    CV Belum Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Academic Info -->
                            <td class="py-4 px-3">
                                <p class="font-semibold text-[#0B1B3D]">{{ $profile?->prodi ?? 'Prodi Belum Diisi' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $profile?->semester ?? '-' }} • IPK: <strong class="text-[#004AAD]">{{ $profile?->ipk ?? '3.50' }}</strong></p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold {{ $profile?->target_field === 'elektro' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20' }}">
                                    {{ $profile?->target_field === 'elektro' ? 'Teknik Elektro' : 'Teknologi Informasi' }}
                                </span>
                            </td>

                            <!-- Portfolios Count & Details -->
                            <td class="py-4 px-3 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $student->portfolios->count() }} Proyek
                                    </span>
                                    @if ($student->portfolios->count() > 0)
                                        <button type="button" @click="selectedStudent = {{ $studentDataJson }}" class="mt-1 text-[10px] text-[#004AAD] hover:underline">
                                            Lihat Detail
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <!-- Target Occupation -->
                            <td class="py-4 px-3">
                                <p class="font-bold text-[#0B1B3D] text-[12px]">{{ $profile?->targetOccupation?->title ?? 'Belum Ditentukan' }}</p>
                                <p class="text-[10px] text-slate-500">{{ $profile?->targetOccupation?->category ?? 'Jalur Karier Mahasiswa' }}</p>
                            </td>

                            <!-- Employability Score -->
                            <td class="py-4 px-3 text-center">
                                @if ($score > 0)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-[#004AAD] border border-blue-200">
                                            {{ $score }}%
                                        </span>
                                        <span class="text-[9px] text-slate-500 mt-0.5">
                                            {{ $score >= 75 ? 'Siap Kerja' : 'Perlu Penguatan' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                        Belum Asesmen
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Detail -->
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="p-1.5 text-sky-400 hover:text-[#0B1B3D] hover:bg-accent/20 rounded-lg transition" title="Lihat Detail Mahasiswa Secara Penuh">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    <!-- CV ATS Action -->
                                    @if($profile?->isCvFilled())
                                        <a href="{{ route('admin.students.cv', $student->id) }}" target="_blank" class="p-1.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Lihat CV ATS Mahasiswa (Aktif)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                    @else
                                        <span class="p-1.5 text-slate-300 cursor-not-allowed rounded-lg" title="CV ATS Belum Diisi (Tidak Aktif)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </span>
                                    @endif

                                    <!-- Edit Profile -->
                                    <button type="button" @click="editStudentData = {{ json_encode([
                                        'id' => $student->id,
                                        'name' => $student->name,
                                        'email' => $student->email,
                                        'nim' => $student->nim,
                                        'prodi' => $profile?->prodi ?? 'Teknik Komputer',
                                        'semester' => $profile?->semester ?? 'Semester 7',
                                        'target_field' => $profile?->target_field ?? 'it',
                                        'ipk' => $profile?->ipk ?? '3.50',
                                    ]) }}; editModalOpen = true" class="p-1.5 text-slate-500 hover:text-[#004AAD] hover:bg-blue-50 rounded-lg transition" title="Edit Data Mahasiswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Reset Password -->
                                    <form method="POST" action="{{ route('admin.users.reset-password', $student) }}" class="inline" onsubmit="return confirm('Reset kata sandi mahasiswa ini ke default (kosong)?');">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-amber-400 hover:text-[#0B1B3D] hover:bg-amber-500/20 rounded-lg transition" title="Reset Kata Sandi ke 'kosong'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </button>
                                    </form>

                                    <!-- Delete Account -->
                                    <button type="button" @click="deleteModalOpen = true; userToDelete = { id: {{ $student->id }}, name: '{{ addslashes($student->name) }}' }" class="p-1.5 text-rose-400 hover:text-[#0B1B3D] hover:bg-rose-500/20 rounded-lg transition" title="Hapus Akun Mahasiswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                Tidak ada data mahasiswa yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div class="p-4 bg-white border-t border-slate-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Modal 1: Tambah Mahasiswa Manual -->
    <div x-show="createStudentModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="createStudentModalOpen = false">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl" x-data="{ userType: 'mahasiswa', semesterVal: 'Semester 7' }">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-[#0B1B3D]">Tambah Pengguna Baru Manual</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun Mahasiswa Aktif atau Fresh Graduate / Alumni</p>
                </div>
                <button type="button" @click="createStudentModalOpen = false" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Tipe Pengguna Selector -->
            <div class="bg-slate-50 p-1.5 rounded-xl border border-slate-200 grid grid-cols-2 gap-1.5">
                <button type="button" 
                    @click="userType = 'mahasiswa'; if(semesterVal === 'Alumni / Fresh Graduate PT') semesterVal = 'Semester 7';" 
                    :class="userType === 'mahasiswa' ? 'bg-white text-[#004AAD] font-bold shadow-sm border border-slate-200' : 'text-slate-600 hover:text-[#0B1B3D] font-medium'" 
                    class="py-2 px-3 rounded-lg text-xs transition flex items-center justify-center gap-2">
                    <span>🎓</span>
                    <span>Mahasiswa Aktif</span>
                </button>
                <button type="button" 
                    @click="userType = 'fresh_grad'; semesterVal = 'Alumni / Fresh Graduate PT';" 
                    :class="userType === 'fresh_grad' ? 'bg-[#004AAD] text-white font-bold shadow-sm' : 'text-slate-600 hover:text-[#0B1B3D] font-medium'" 
                    class="py-2 px-3 rounded-lg text-xs transition flex items-center justify-center gap-2">
                    <span>💼</span>
                    <span>Fresh Graduate / Alumni</span>
                </button>
            </div>

            <!-- Info banner for fresh graduate -->
            <div x-show="userType === 'fresh_grad'" x-transition class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-2.5">
                <span class="text-emerald-600 text-sm mt-0.5 shrink-0">💡</span>
                <p class="text-xs text-emerald-800 leading-relaxed">
                    Pengguna <strong>Fresh Graduate</strong> tidak diwajibkan memiliki NIM. Kata sandi dan informasi login akan <strong>langsung dikirimkan ke email</strong> pengguna yang didaftarkan.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            <span x-text="userType === 'fresh_grad' ? 'NIM (Opsional / Boleh Kosong)' : 'NIM Mahasiswa *'"></span>
                        </label>
                        <input type="text" name="nim" :required="userType === 'mahasiswa'" :placeholder="userType === 'fresh_grad' ? 'Kosongkan jika Fresh Graduate' : 'Contoh: 2401081015'" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email *</label>
                        <input type="email" name="email" required placeholder="pengguna@email.com" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kata Sandi Awal</label>
                        <input type="password" name="password" placeholder="Default: 'password'" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Fokus Bidang *</label>
                        <select name="target_field" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="it">Teknologi Informasi</option>
                            <option value="elektro">Teknik Elektro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Program Studi</label>
                        <select name="prodi" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="">-- Pilih Program Studi --</option>
                            <optgroup label="Jurusan Teknologi Informasi">
                                <option value="D4 Rekayasa Perangkat Lunak">D4 Rekayasa Perangkat Lunak</option>
                                <option value="D3 Manajemen Informatika">D3 Manajemen Informatika</option>
                                <option value="D3 Teknik Komputer">D3 Teknik Komputer</option>
                            </optgroup>
                            <optgroup label="Jurusan Teknik Elektro">
                                <option value="D4 Teknik Telekomunikasi">D4 Teknik Telekomunikasi</option>
                                <option value="D3 Teknik Telekomunikasi">D3 Teknik Telekomunikasi</option>
                                <option value="D4 Teknik Elektronika">D4 Teknik Elektronika</option>
                                <option value="D3 Teknik Elektronika">D3 Teknik Elektronika</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status Pendidikan / Semester</label>
                        <select name="semester" x-model="semesterVal" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="Alumni / Fresh Graduate PT">Alumni / Fresh Graduate PT</option>
                            <option value="Lulusan SMK/SMA (Siap Kerja)">Lulusan SMK/SMA (Siap Kerja)</option>
                            <option value="Semester 1">Semester 1</option>
                            <option value="Semester 2">Semester 2</option>
                            <option value="Semester 3">Semester 3</option>
                            <option value="Semester 4">Semester 4</option>
                            <option value="Semester 5">Semester 5</option>
                            <option value="Semester 6">Semester 6</option>
                            <option value="Semester 7">Semester 7</option>
                            <option value="Semester 8">Semester 8</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Indeks Prestasi Kumulatif (IPK)</label>
                        <input type="number" step="0.01" min="0" max="4.00" name="ipk" value="3.50" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="createStudentModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#004AAD] hover:bg-[#155291] text-white font-bold rounded-xl text-xs shadow-md transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Simpan & Kirim Email Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Data Mahasiswa -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="editModalOpen = false">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-[#0B1B3D]">Edit Data Mahasiswa</h3>
                <button type="button" @click="editModalOpen = false" class="p-1.5 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/admin/students/' + editStudentData?.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Mahasiswa *</label>
                        <input type="text" name="name" :value="editStudentData?.name" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIM (Opsional)</label>
                        <input type="text" name="nim" :value="editStudentData?.nim" placeholder="Kosongkan jika Fresh Graduate" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email *</label>
                        <input type="email" name="email" :value="editStudentData?.email" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Bidang Keahlian *</label>
                        <select name="target_field" :value="editStudentData?.target_field" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="it">Teknologi Informasi</option>
                            <option value="elektro">Teknik Elektro</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Program Studi</label>
                        <select name="prodi" :value="editStudentData?.prodi" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                            <option value="">-- Pilih Program Studi --</option>
                            <optgroup label="Jurusan Teknologi Informasi">
                                <option value="D4 Rekayasa Perangkat Lunak">D4 Rekayasa Perangkat Lunak</option>
                                <option value="D3 Manajemen Informatika">D3 Manajemen Informatika</option>
                                <option value="D3 Teknik Komputer">D3 Teknik Komputer</option>
                            </optgroup>
                            <optgroup label="Jurusan Teknik Elektro">
                                <option value="D4 Teknik Telekomunikasi">D4 Teknik Telekomunikasi</option>
                                <option value="D3 Teknik Telekomunikasi">D3 Teknik Telekomunikasi</option>
                                <option value="D4 Teknik Elektronika">D4 Teknik Elektronika</option>
                                <option value="D3 Teknik Elektronika">D3 Teknik Elektronika</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Semester</label>
                        <input type="text" name="semester" :value="editStudentData?.semester" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">IPK</label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ipk" :value="editStudentData?.ipk" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD]">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#004AAD] hover:bg-[#004AAD] text-white font-bold rounded-xl text-xs shadow-md">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Detail Portofolio & Asesmen -->
    <div x-show="selectedStudent" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="selectedStudent = null">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto p-6 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-[#0B1B3D]" x-text="selectedStudent?.name"></h3>
                    <p class="text-xs text-slate-500" x-text="'NIM: ' + (selectedStudent?.nim || '-') + ' • ' + (selectedStudent?.prodi || '-')"></p>
                </div>
                <button type="button" @click="selectedStudent = null" class="p-2 text-slate-500 hover:text-[#0B1B3D] rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Portofolio List -->
            <div>
                <h4 class="text-xs font-bold text-[#004AAD] uppercase tracking-wider mb-3">Portofolio Proyek Terdaftar</h4>
                <div class="space-y-3">
                    <template x-for="p in selectedStudent?.portfolios" :key="p.title">
                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-bold text-[#0B1B3D]" x-text="p.title"></h5>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-blue-50 text-[#004AAD] font-bold border border-blue-200" x-text="p.category || 'Proyek Mandiri'"></span>
                            </div>
                            <p class="text-[11px] text-slate-600" x-text="p.description || 'Tidak ada deskripsi rinci.'"></p>
                            <p class="text-[10px] text-[#004AAD] font-medium" x-text="'Tech Stack: ' + (p.tech_stack || '-')"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Asesmen History -->
            <div>
                <h4 class="text-xs font-bold text-[#004AAD] uppercase tracking-wider mb-3">Riwayat Uji Kompetensi Asesmen</h4>
                <div class="space-y-2.5">
                    <template x-for="a in selectedStudent?.assessments" :key="a.date">
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-[#0B1B3D]" x-text="'Bidang ' + a.field"></p>
                                <p class="text-[10px] text-slate-500" x-text="'Selesai pada: ' + a.date"></p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-[#004AAD]" x-text="a.score + ' / 100'"></span>
                                <p class="text-[9px] text-slate-500">Skor Uji</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 4: Konfirmasi Hapus Mahasiswa -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-center">
            <div class="w-12 h-12 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#0B1B3D]">Konfirmasi Hapus Mahasiswa</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Apakah Anda yakin ingin menghapus akun mahasiswa <strong class="text-[#0B1B3D]" x-text="userToDelete?.name"></strong>?
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false; userToDelete = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 rounded-xl text-xs transition">
                    Batal
                </button>
                <form :action="'/admin/users/' + userToDelete?.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-[#0B1B3D] font-bold rounded-xl text-xs transition shadow-md shadow-rose-600/20">
                        Ya, Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection






