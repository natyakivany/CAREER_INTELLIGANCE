<x-student-layout>
    <div class="max-w-4xl mx-auto py-8">
        <a href="{{ route('jobs') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-[#004AAD] transition mb-6">
            &larr; Kembali ke Bursa Lowongan
        </a>
        
        <div class="bg-white rounded-3xl p-6 md:p-10 shadow-2xl border border-[#004AAD]/30 flex flex-col gap-8 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 z-10">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#004AAD] text-white flex items-center justify-center shrink-0 shadow-lg shadow-[#004AAD]/25">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 bg-[#004AAD]/10 text-[#004AAD] text-[10px] font-extrabold rounded-full uppercase tracking-widest border border-[#004AAD]/20 shadow-sm shadow-[#004AAD]/10">Detail Lowongan Pekerjaan</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-[#0B1B3D] leading-tight mb-2">{{ $job->title }}</h2>
                        <p class="text-sm md:text-base text-slate-500 font-medium">{{ $job->user->name ?? 'Mitra Industri' }} &bull; {{ $job->location }} ({{ $job->type }})</p>
                    </div>
                </div>
                
                <div class="flex flex-col md:items-end z-10">
                    @php
                        $score = $job->match_score ?? 0;
                    @endphp
                    
                    @if($score >= 80)
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-[#004AAD]/15 text-[#004AAD] font-bold text-xs border border-[#004AAD]/30 shadow-sm shadow-[#004AAD]/10">Top Match &bull; {{ $score }}%</span>
                    @elseif($score >= 60)
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-accent/15 text-sky-700 font-bold text-xs border border-sky-500/30">Sesuai &bull; {{ $score }}%</span>
                    @elseif($score >= 40)
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-amber-500/15 text-amber-700 font-bold text-xs border border-amber-500/30">Potensial &bull; {{ $score }}%</span>
                    @else
                        <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 font-bold text-xs border border-slate-300">Tidak Sesuai &bull; {{ $score }}%</span>
                    @endif
                    <span class="text-sm text-slate-500 mt-2 font-medium">Durasi: {{ $job->duration }}</span>
                </div>
            </div>
            
            <div class="z-10">
                <h3 class="text-sm font-bold text-slate-600 mb-3">Kompetensi yang Dibutuhkan:</h3>
                <div class="flex flex-wrap gap-2.5">
                    @foreach($job->skills ?? [] as $skill)
                        <span class="px-3.5 py-1.5 bg-blue-50 border border-blue-200 rounded-xl text-xs font-bold text-[#004AAD] shadow-sm">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
            
            <div class="z-10 bg-slate-50 p-6 rounded-2xl border border-slate-200 text-sm text-slate-700 leading-relaxed shadow-inner">
                <h3 class="text-base font-bold text-[#0B1B3D] mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Deskripsi Pekerjaan:
                </h3>
                <p>{{ $job->description ?? 'Deskripsi tidak tersedia. Silakan hubungi perusahaan untuk detail lebih lanjut terkait tawaran ini.' }}</p>
            </div>

            <!-- Timeline Alur & Tahapan Proses Melamar -->
            <x-application-timeline :job="$job" :application="$userApp" />
            
            <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5 z-10 bg-slate-50 -mx-6 md:-mx-10 -mb-6 md:-mb-10 p-6 md:p-10">
                <p class="text-sm md:text-base text-slate-500">
                    @if($userApp)
                        @if($userApp->is_invited && $userApp->status === 'Menunggu Review')
                            <span class="font-extrabold text-amber-600 block text-lg mb-1">Tawaran Wawancara dari Mitra</span> 
                            Mitra mengundang Anda untuk mengikuti wawancara/tes untuk posisi ini.
                        @elseif($userApp->status === 'Diterima')
                            <span class="font-extrabold text-[#004AAD] block text-lg mb-1">🎉 Selamat! Anda telah diterima.</span>
                            Silakan tunggu informasi selanjutnya dari perusahaan.
                        @elseif($userApp->status === 'Ditolak')
                            <span class="font-extrabold text-rose-600 block text-lg mb-1">Mohon Maaf, Anda belum lolos.</span>
                            Jangan menyerah! Terus tingkatkan portofolio Anda.
                        @elseif($userApp->status === 'Proses Seleksi')
                            <span class="font-extrabold text-indigo-600 block text-lg mb-1">Tes / Proses Seleksi Berlangsung</span>
                            Mitra sedang memproses Anda ke tahap seleksi lebih lanjut.
                        @elseif($userApp->status === 'Wawancara')
                            <span class="font-extrabold text-purple-600 block text-lg mb-1">Tahap Wawancara</span>
                            Selamat! Anda masuk ke tahap wawancara. Silakan cek pesan dari Mitra.
                        @else
                            <span class="font-extrabold text-[#004AAD] block text-lg mb-1">✓ Lamaran Telah Dikirim.</span> 
                            Profil Anda sedang direview oleh Mitra.
                        @endif
                    @else
                        <span class="font-extrabold text-[#0B1B3D] block text-lg mb-1">Tindakan Diperlukan:</span> 
                        Apakah Anda ingin melamar posisi ini?
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0 w-full sm:w-auto mt-4 sm:mt-0">
                    @if(!$userApp || ($userApp->is_invited && $userApp->status === 'Menunggu Review'))
                        <form action="{{ route('jobs.apply', $job) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin {{ $userApp ? 'menerima tawaran' : 'melamar posisi' }} {{ addslashes($job->title) }} di {{ addslashes($job->user->name ?? 'Mitra') }}?');" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-[#004AAD] hover:bg-[#083577] text-white text-sm md:text-base font-bold rounded-2xl transition shadow-sm flex items-center justify-center gap-2 hover:scale-105 transform duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                {{ $userApp ? 'Terima Tawaran Wawancara' : 'Lamar Pekerjaan' }}
                            </button>
                        </form>
                        
                        @if($userApp && $userApp->is_invited && $userApp->status === 'Menunggu Review')
                            <form action="{{ route('jobs.reject', $job) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak tawaran ini?');" class="w-full sm:w-auto mt-2 sm:mt-0">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 text-sm md:text-base font-bold rounded-2xl transition flex items-center justify-center gap-2 hover:scale-105 transform duration-200">
                                    Tolak Tawaran
                                </button>
                            </form>
                        @endif
                    @elseif($userApp && in_array($userApp->status, ['Proses Seleksi', 'Wawancara']) && in_array($userApp->interview_status, ['Menunggu Konfirmasi', null]))
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <form action="{{ route('applications.confirm', $userApp) }}" method="POST" class="w-full sm:w-auto">
                                @csrf @method('PATCH')
                                <button type="submit" name="interview_status" value="Hadir" onclick="return confirm('Anda yakin mengonfirmasi bersedia mengikuti tahap ini?')" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    {{ $userApp->status === 'Proses Seleksi' ? 'Bersedia Mengikuti Tes' : 'Konfirmasi Bersedia Hadir' }}
                                </button>
                            </form>
                            <form action="{{ route('applications.confirm', $userApp) }}" method="POST" class="w-full sm:w-auto">
                                @csrf @method('PATCH')
                                <button type="submit" name="interview_status" value="Tidak Hadir" onclick="return confirm('Anda yakin tidak bersedia mengikuti tahap ini?')" class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-rose-50 text-rose-600 border border-rose-300 text-sm font-bold rounded-xl transition shadow-sm cursor-pointer">
                                    ✕ Tidak Bersedia
                                </button>
                            </form>
                        </div>
                    @else
                        <button disabled class="px-6 py-2.5 bg-slate-200 border border-slate-300 text-slate-500 text-sm font-bold rounded-xl cursor-not-allowed flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ ($userApp->is_invited ?? false) ? 'Tawaran Direspon' : ($userApp->interview_status === 'Hadir' ? '✓ Bersedia Mengikuti' : ($userApp->interview_status === 'Tidak Hadir' ? '✕ Tidak Bersedia' : 'Sudah Dilamar')) }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>





