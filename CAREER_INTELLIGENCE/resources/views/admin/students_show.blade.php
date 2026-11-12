@extends('layouts.admin')

@section('content')
    <div x-data="{ 
        showEmailModal: false,
        subject: 'Pemberitahuan Pusat Karier Kampus - Career Intelligence',
        message: 'Halo {{ addslashes($student->name) }},\n\nKami dari Pusat Karier Career Intelligence ingin menghubungi Anda terkait profil akademik dan kesiapan karier Anda. Silakan beri kabar jika ada hal yang ingin didiskusikan.',
        studentEmail: '{{ $student->email }}',
        adminEmail: '{{ Auth::user()->email ?? config('mail.from.address', 'admin@gmail.com') }}',
        get gmailUrl() {
            return 'https://mail.google.com/mail/?view=cm&fs=1'
                + '&to=' + encodeURIComponent(this.studentEmail)
                + '&cc=' + encodeURIComponent(this.adminEmail)
                + '&su=' + encodeURIComponent(this.subject)
                + '&body=' + encodeURIComponent(this.message);
        },
        get mailtoUrl() {
            return 'mailto:' + encodeURIComponent(this.studentEmail)
                + '?cc=' + encodeURIComponent(this.adminEmail)
                + '&subject=' + encodeURIComponent(this.subject)
                + '&body=' + encodeURIComponent(this.message);
        }
    }">

        {{-- Header & Back Button --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('admin.students') }}" class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#0B1B3D] transition w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Data Mahasiswa
            </a>
            
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $isCvReady = $profile?->isCvFilled() ?? false;
                @endphp

                @if($isCvReady)
                    <a href="{{ route('admin.students.cv', $student->id) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#004AAD] text-xs font-bold transition flex items-center justify-center gap-2 w-fit border border-slate-300 shadow-sm" title="Lihat CV ATS Mahasiswa (Aktif)">
                        <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Lihat CV ATS</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Aktif
                        </span>
                    </a>
                @else
                    <button type="button" disabled class="px-5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold cursor-not-allowed flex items-center justify-center gap-2 w-fit opacity-80" title="Mahasiswa belum mengisi dokumen CV ATS">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        <span>Lihat CV</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-500">
                            Tidak Aktif
                        </span>
                    </button>
                @endif
                @if($profile?->phone)
                    <a href="{{ $profile->getWhatsappLinkWithMessage('Halo '.$student->name.', kami dari Admin Career Intelligence Kampus ingin menghubungi Anda terkait profil dan kesiapan karier Anda.') }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 w-fit">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.37C9.32 7.37 8.97 7.45 8.67 7.78C8.37 8.1 7.53 8.89 7.53 10.49C7.53 12.09 8.7 13.64 8.86 13.85C9.02 14.07 11.16 17.37 14.44 18.79C15.22 19.13 15.82 19.33 16.3 19.48C17.08 19.73 17.8 19.69 18.36 19.61C18.98 19.52 20.28 18.83 20.55 18.06C20.82 17.3 20.82 16.64 20.74 16.5C20.66 16.37 20.45 16.3 20.13 16.14C19.82 15.98 18.27 15.22 17.98 15.11C17.7 15.01 17.5 14.95 17.3 15.26C17.09 15.56 16.5 16.27 16.32 16.48C16.14 16.68 15.96 16.71 15.65 16.55C15.34 16.39 14.34 16.06 13.16 15.01C12.24 14.19 11.62 13.18 11.44 12.87C11.26 12.57 11.42 12.4 11.58 12.24C11.72 12.1 11.89 11.87 12.05 11.69C12.21 11.5 12.26 11.37 12.36 11.16C12.47 10.96 12.42 10.78 12.34 10.62C12.26 10.47 11.65 8.97 11.39 8.36C11.14 7.76 10.89 7.84 10.7 7.84C10.52 7.83 10.32 7.83 10.11 7.83C9.9 7.83 9.53 7.37 9.53 7.37Z"/>
                        </svg>
                        <span>Hubungi via WhatsApp</span>
                    </a>
                @endif
                <button type="button" @click="showEmailModal = true" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Hubungi via Email</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Profile Card & Assessment --}}
            <div class="space-y-6">
                {{-- Main Profile Card --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-[#004AAD] text-white font-bold text-xl flex items-center justify-center shadow-lg shrink-0 overflow-hidden">
                            @if($student->avatar_url)
                                <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                            @else
                                {{ collect(explode(' ', $student->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-[#0B1B3D]">{{ $student->name }}</h2>
                            <p class="text-sm text-slate-500">{{ $profile->targetOccupation?->title ?? 'Belum ditentukan target profesi' }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-300 flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"></path></svg>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Program Studi</p>
                                <p class="text-slate-600">{{ $profile->prodi ?? '-' }} (Sem. {{ $profile->semester ?? '-' }})</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-300 flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">NIM</p>
                                <p class="text-slate-600">{{ $student->nim ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-300 flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Email</p>
                                <p class="text-slate-600">{{ $student->email }}</p>
                            </div>
                        </div>

                        {{-- WhatsApp / Kontak Section --}}
                        <div class="flex items-center justify-between gap-3 text-sm p-3 rounded-2xl {{ $profile?->phone ? 'bg-emerald-50/80 border border-emerald-200/80' : 'bg-slate-50 border border-slate-200' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg {{ $profile?->phone ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.37C9.32 7.37 8.97 7.45 8.67 7.78C8.37 8.1 7.53 8.89 7.53 10.49C7.53 12.09 8.7 13.64 8.86 13.85C9.02 14.07 11.16 17.37 14.44 18.79C15.22 19.13 15.82 19.33 16.3 19.48C17.08 19.73 17.8 19.69 18.36 19.61C18.98 19.52 20.28 18.83 20.55 18.06C20.82 17.3 20.82 16.64 20.74 16.5C20.66 16.37 20.45 16.3 20.13 16.14C19.82 15.98 18.27 15.22 17.98 15.11C17.7 15.01 17.5 14.95 17.3 15.26C17.09 15.56 16.5 16.27 16.32 16.48C16.14 16.68 15.96 16.71 15.65 16.55C15.34 16.39 14.34 16.06 13.16 15.01C12.24 14.19 11.62 13.18 11.44 12.87C11.26 12.57 11.42 12.4 11.58 12.24C11.72 12.1 11.89 11.87 12.05 11.69C12.21 11.5 12.26 11.37 12.36 11.16C12.47 10.96 12.42 10.78 12.34 10.62C12.26 10.47 11.65 8.97 11.39 8.36C11.14 7.76 10.89 7.84 10.7 7.84C10.52 7.83 10.32 7.83 10.11 7.83C9.9 7.83 9.53 7.37 9.53 7.37Z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] {{ $profile?->phone ? 'text-emerald-800' : 'text-slate-500' }} font-bold uppercase tracking-wider">WhatsApp / Kontak</p>
                                    <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ $profile?->phone ?? 'Belum ada kontak' }}</p>
                                </div>
                            </div>
                            @if($profile?->phone)
                                <a href="{{ $profile->getWhatsappLinkWithMessage('Halo '.$student->name.', kami dari Admin Career Intelligence Kampus ingin menghubungi Anda.') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition flex items-center gap-1.5 shrink-0 shadow-sm shadow-emerald-600/20">
                                    <span>Chat WA</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($profile->bio)
                        <div class="pt-4 border-t border-slate-200">
                            <p class="text-xs font-bold text-slate-600 mb-2">Tentang</p>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $profile->bio }}</p>
                        </div>
                    @endif
                </div>

                {{-- Status Dokumen CV ATS --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-extrabold text-[#0B1B3D] flex items-center gap-2">
                            <div class="p-1.5 rounded-lg {{ $isCvReady ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            Dokumen CV ATS
                        </h3>
                        @if($isCvReady)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Belum Diisi (Tidak Aktif)
                            </span>
                        @endif
                    </div>

                    @if($isCvReady)
                        <div class="space-y-3">
                            @if($profile->cv_headline)
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Headline Profil</p>
                                    <p class="text-xs font-bold text-[#0B1B3D] mt-0.5">{{ $profile->cv_headline }}</p>
                                </div>
                            @endif
                            @if($profile->cv_summary)
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Ringkasan CV</p>
                                    <p class="text-xs text-slate-600 line-clamp-3 mt-0.5 leading-relaxed">{{ $profile->cv_summary }}</p>
                                </div>
                            @endif
                            <a href="{{ route('admin.students.cv', $student->id) }}" target="_blank" class="w-full mt-2 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-600/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Buka Dokumen CV ATS Lengkap</span>
                            </a>
                        </div>
                    @else
                        <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200/80 text-xs text-amber-900 leading-relaxed">
                            <p class="font-bold text-amber-800 mb-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                CV ATS Belum Dibuat
                            </p>
                            <p class="text-[11px] text-amber-800/90">
                                Data profil akademik mahasiswa telah terdaftar di sistem, namun mahasiswa belum mengisi ringkasan atau mengekspor CV ATS melalui modul CV Builder. Tombol CV akan otomatis aktif saat mahasiswa telah menyusun data CV.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Assessment Card --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl">
                    <h3 class="text-base font-extrabold text-[#0B1B3D] mb-4">Hasil Penilaian Sistem</h3>
                    
                    @php
                        $empScore = $latestAssessment->employability_score ?? 0;
                        $isReady = $empScore >= 80;
                    @endphp

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Employability</p>
                            <p class="text-3xl font-black {{ $isReady ? 'text-[#004AAD]' : 'text-amber-400' }}">{{ $empScore }}%</p>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tes Gemini AI</p>
                            <p class="text-3xl font-black text-[#0B1B3D]">{{ round($latestAssessment->total_score ?? 0) }}</p>
                        </div>
                    </div>

                    <div class="mb-5 flex items-center justify-between p-3 rounded-xl {{ $isReady ? 'bg-[#004AAD]/10 border-[#004AAD]/20' : 'bg-amber-500/10 border-amber-500/20' }} border">
                        <span class="text-xs font-bold {{ $isReady ? 'text-[#004AAD]' : 'text-amber-400' }}">Status Kesiapan:</span>
                        <span class="text-xs font-black {{ $isReady ? 'text-[#004AAD]' : 'text-amber-400' }} uppercase px-2 py-1 rounded bg-black/20">{{ $isReady ? 'Siap Kerja' : 'Berkembang' }}</span>
                    </div>

                    @if($latestAssessment?->ai_feedback)
                        <div>
                            <p class="text-xs font-bold text-slate-600 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Catatan Evaluasi AI
                            </p>
                            <div class="bg-white rounded-xl border border-slate-200 p-4">
                                <p class="text-sm text-slate-500 italic leading-relaxed">{{ $latestAssessment->ai_feedback }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Skills & Portfolios --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Skills --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl">
                    <h3 class="text-base font-extrabold text-[#0B1B3D] mb-4 flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-[#004AAD]/20 text-[#004AAD]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        Keahlian / Kompetensi Terdeteksi
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($topSkills as $skill)
                            <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-[#004AAD] text-sm font-bold border border-blue-200 shadow-sm">{{ $skill }}</span>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada data skill yang diekstrak dari portofolio.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Portfolios --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-base font-extrabold text-[#0B1B3D] flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-accent/20 text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            Karya & Portofolio
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">{{ $student->portfolios->count() }} Proyek</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($student->portfolios as $portfolio)
                            <div class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-slate-300 transition">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <h4 class="text-base font-bold text-[#0B1B3D] group-hover:text-[#004AAD] transition">{{ $portfolio->title }}</h4>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-500 mb-4 leading-relaxed">{{ $portfolio->description ?? 'Tidak ada deskripsi proyek.' }}</p>
                                
                                @if($portfolio->tech_stack)
                                    <div class="mb-4 flex flex-wrap gap-1.5">
                                        @foreach(array_map('trim', explode(',', $portfolio->tech_stack)) as $tech)
                                            <span class="px-2 py-1 rounded bg-slate-100 text-[#0B1B3D] text-[10px] font-bold tracking-wide uppercase">{{ $tech }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-200">
                                    @if($portfolio->project_url)
                                        <a href="{{ $portfolio->project_url }}" target="_blank" class="text-xs font-semibold text-[#0B1B3D] bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 border border-slate-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            Live Demo
                                        </a>
                                    @endif
                                    @if($portfolio->repository_url)
                                        <a href="{{ $portfolio->repository_url }}" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-[#0B1B3D] bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 border border-slate-200 hover:border-slate-300">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                            Repository
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-white rounded-2xl border border-slate-200">
                                <div class="w-12 h-12 rounded-full bg-[#004AAD] text-white flex items-center justify-center shadow-md mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-500">Belum Ada Portofolio</p>
                                <p class="text-xs text-slate-500 mt-1">Kandidat ini belum mengunggah karya atau proyek apapun.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- In-App Email Modal --}}
        <div x-show="showEmailModal" style="display: none" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
            <div @click.outside="showEmailModal = false" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-2xl w-full max-w-2xl relative" x-transition.scale.95>
                <button type="button" @click="showEmailModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-[#0B1B3D] transition p-1.5 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <div class="flex items-center gap-3.5 mb-2">
                    <div class="w-11 h-11 rounded-2xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/25 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[#0B1B3D]">Kirim Email ke Mahasiswa</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pengirim: <strong class="text-[#004AAD]">{{ Auth::user()->name }} ({{ Auth::user()->email }})</strong>
                        </p>
                    </div>
                </div>

                {{-- Direct Webmail / Client Shortcut Option --}}
                <div class="mt-4 mb-5 p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80 text-[11px] text-blue-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                    <div>
                        <span class="font-extrabold text-[#0B1B3D] text-xs flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Kirim Langsung dari Browser Gmail Anda (100% Masuk ke Inbox)
                        </span>
                        <span class="text-slate-600 text-[11px] block mt-0.5">
                            Buka langsung di tab Gmail Anda dengan penerima <strong class="text-[#004AAD]">{{ $student->email }}</strong> & teks pesan di bawah ini otomatis terisi.
                        </span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a :href="gmailUrl" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white text-rose-600 hover:bg-rose-50 border border-rose-200 font-extrabold transition shadow-sm text-xs hover:border-rose-300">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/></svg>
                            <span>Buka di Gmail Web</span>
                        </a>
                        <a :href="mailtoUrl" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border border-slate-300 font-bold transition shadow-sm text-xs">
                            <span>Aplikasi Mail</span>
                        </a>
                    </div>
                </div>
                
                {{-- In-App Direct SMTP Form --}}
                <form action="{{ route('admin.students.email', $student->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="student_email_subject" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Subjek Email</label>
                        <input id="student_email_subject" name="subject" x-model="subject" type="text" required class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                    </div>
                    <div>
                        <label for="student_email_message" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Isi Pesan / Body Email</label>
                        <textarea id="student_email_message" name="message" x-model="message" rows="5" required placeholder="Tuliskan pesan resmi dari kampus di sini..." class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner resize-none"></textarea>
                    </div>
                    <div class="pt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-500">
                            @if(config('mail.default') === 'log')
                                <span class="text-amber-700 font-semibold">Mode simulasi server:</span> Tombol "Kirim Sekarang" mencatat ke log server. Gunakan tombol merah "Buka di Gmail Web" untuk pengiriman nyata.
                            @else
                                Pesan dikirimkan resmi menggunakan Mailer SMTP sistem.
                            @endif
                        </span>
                        <div class="flex items-center gap-2.5 self-end sm:self-auto">
                            <button type="button" @click="showEmailModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">Batal</button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#004AAD] hover:bg-[#003B8B] text-white text-xs font-bold transition shadow-md shadow-[#004AAD]/25 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                <span>Kirim Sekarang</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection






