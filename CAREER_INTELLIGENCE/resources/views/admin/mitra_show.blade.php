@extends('layouts.admin')

@section('content')
    <div x-data="{ 
        showEmailModal: false,
        subject: 'Pemberitahuan Kerjasama Kampus - Career Intelligence',
        message: 'Halo {{ addslashes($user->name) }},\n\nKami dari Pusat Karier Career Intelligence ingin menghubungi Anda terkait kemitraan industri dan rekrutmen mahasiswa. Silakan beri kabar jika ada hal yang ingin didiskusikan.',
        mitraEmail: '{{ $user->email }}',
        adminEmail: '{{ Auth::user()->email ?? config('mail.from.address', 'admin@gmail.com') }}',
        get gmailUrl() {
            return 'https://mail.google.com/mail/?view=cm&fs=1'
                + '&to=' + encodeURIComponent(this.mitraEmail)
                + '&cc=' + encodeURIComponent(this.adminEmail)
                + '&su=' + encodeURIComponent(this.subject)
                + '&body=' + encodeURIComponent(this.message);
        },
        get mailtoUrl() {
            return 'mailto:' + encodeURIComponent(this.mitraEmail)
                + '?cc=' + encodeURIComponent(this.adminEmail)
                + '&subject=' + encodeURIComponent(this.subject)
                + '&body=' + encodeURIComponent(this.message);
        }
    }">
        {{-- Header & Back Button --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('admin.mitra') }}" class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#0B1B3D] transition w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Manajemen Mitra
            </a>
            
            <button type="button" @click="showEmailModal = true" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Kirim Email ke Mitra
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Mitra Profile --}}
            <div class="space-y-6">
                {{-- Main Profile Card --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-[#004AAD] text-white font-bold text-xl flex items-center justify-center shadow-lg shrink-0 overflow-hidden">
                            {{ collect(explode(' ', $user->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-[#0B1B3D]">{{ $user->name }}</h2>
                            <p class="text-xs text-[#004AAD] font-semibold mt-0.5">Mitra Industri Terverifikasi</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-300 flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Email Resmi</p>
                                <p class="text-slate-600">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-300 flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Bergabung Sejak</p>
                                <p class="text-slate-600">{{ $user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-200">
                        <div class="bg-white rounded-xl p-3 text-center border border-slate-200">
                            <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Total Lowongan</p>
                            <p class="text-2xl font-black text-[#0B1B3D]">{{ $totalJobs }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 text-center border border-slate-200">
                            <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Pelamar</p>
                            <p class="text-2xl font-black text-sky-400">{{ $totalApplications }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Job Listings --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Job Listings List --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#0B1B3D] tracking-tight flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Lowongan Kerja yang Dipublikasikan
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Daftar semua lowongan yang pernah dibuat oleh mitra ini.</p>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 text-[#004AAD] text-xs font-bold rounded-lg border border-slate-300">
                            {{ $activeJobs }} Aktif
                        </span>
                    </div>

                    @if($jobs->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($jobs as $job)
                                <div class="bg-white rounded-2xl p-5 border border-slate-200 hover:border-slate-300 transition">
                                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="text-sm font-bold text-[#0B1B3D]">{{ $job->title }}</h4>
                                                @if($job->status === 'Buka')
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20">Buka</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Tutup</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-500 mb-3">{{ $job->department }} • {{ $job->location }} • {{ $job->type }}</p>
                                            
                                            <div class="flex flex-wrap gap-1.5">
                                                @if(is_array($job->skills))
                                                    @foreach(array_slice($job->skills, 0, 4) as $skill)
                                                        <span class="px-2 py-0.5 bg-blue-50 text-[#004AAD] text-[10px] font-medium rounded-md border border-slate-200">{{ trim($skill) }}</span>
                                                    @endforeach
                                                    @if(count($job->skills) > 4)
                                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-medium rounded-md border border-slate-200">+{{ count($job->skills) - 4 }} lainnya</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex flex-col justify-center sm:items-end sm:text-right border-t sm:border-t-0 sm:border-l border-slate-200 pt-4 sm:pt-0 sm:pl-5">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kandidat Pelamar</p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-2xl font-black text-sky-400">{{ $job->applications->count() }}</span>
                                                <span class="text-xs text-slate-500">Orang</span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-2">Dibuat {{ $job->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                            <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-600">Belum Ada Lowongan</p>
                            <p class="text-xs text-slate-500 mt-1">Mitra ini belum pernah mempublikasikan lowongan pekerjaan apapun.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- Email Modal --}}
        <div x-show="showEmailModal" style="display: none" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
            <div @click.outside="showEmailModal = false" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-2xl w-full max-w-2xl relative" x-transition.scale.95>
                <button type="button" @click="showEmailModal = false" class="absolute top-5 right-5 text-slate-500 hover:text-[#0B1B3D] transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <div class="flex items-center gap-3.5 mb-2">
                    <div class="w-11 h-11 rounded-2xl bg-[#004AAD] text-white flex items-center justify-center shadow-md shadow-[#004AAD]/25 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[#0B1B3D]">Kirim Email ke Mitra Industri</h3>
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
                            Buka langsung di tab Gmail Anda dengan penerima <strong class="text-[#004AAD]">{{ $user->email }}</strong> & teks pesan di bawah ini otomatis terisi.
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
                
                <form action="{{ route('admin.mitra.email', $user->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="subject" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Subjek Email</label>
                        <input id="subject" name="subject" x-model="subject" type="text" required placeholder="Contoh: Pemberitahuan Pembaruan Sistem" class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner">
                    </div>
                    <div>
                        <label for="message" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Isi Pesan / Body</label>
                        <textarea id="message" name="message" x-model="message" rows="5" required placeholder="Tulis pesan lengkap di sini..." class="w-full bg-slate-50 border border-slate-300 text-[#0B1B3D] text-[13px] rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition shadow-inner resize-none"></textarea>
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showEmailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-300 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#003B8B] text-white text-xs font-bold transition shadow-lg shadow-[#004AAD]/20">
                            Kirim Sekarang &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection






