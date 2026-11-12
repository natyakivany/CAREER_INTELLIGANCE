<x-mitra-layout>
    <div>
        {{-- Header & Back Button --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{ openContactModal: {{ request('contact') || request('job_id') ? 'true' : 'false' }}, selectedJobId: '{{ request('job_id', '') }}' }">
            <a href="{{ route('mitra.candidates') }}" class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#0B1B3D] transition w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Kandidat
            </a>
            
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $isCvReady = $profile?->isCvFilled() ?? false;
                @endphp

                @if($isCvReady)
                <a href="{{ route('mitra.candidates.cv', $student->id) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#004AAD] text-xs font-bold transition flex items-center justify-center gap-2 w-fit border border-slate-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Lihat CV ATS
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Aktif
                    </span>
                </a>
                @else
                    <button type="button" disabled class="px-5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold cursor-not-allowed flex items-center justify-center gap-2 w-fit opacity-80" title="Kandidat belum mengisi dokumen CV ATS">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        <span>Lihat CV</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-500">
                            Tidak Aktif
                        </span>
                    </button>
                @endif
                
                <button @click="openContactModal = true" type="button" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#083577] text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Hubungi via Email
                </button>
            </div>

            {{-- Modal Kirim Email --}}
            <div x-show="openContactModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center">
                <div x-show="openContactModal" x-transition.opacity class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="openContactModal = false"></div>
                <div x-show="openContactModal" x-transition.scale.95 class="relative bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-[#0B1B3D]">Kirim Pesan ke {{ $student->name }}</h3>
                        <button @click="openContactModal = false" type="button" class="text-slate-500 hover:text-[#0B1B3D] transition p-1 rounded-lg hover:bg-blue-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('mitra.candidates.contact', $student->id) }}" method="POST" class="space-y-4 text-left">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Penerima</label>
                            <input type="text" disabled value="{{ $student->email }}" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Subjek / Topik</label>
                            <input type="text" name="subject" required value="Undangan Wawancara / Tawaran Kerja" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                        </div>
                        @if($mitraJobs->count() > 0)
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Sertakan Link Lowongan <span class="text-slate-500 font-normal">(opsional)</span></label>
                            <select name="job_id" x-model="selectedJobId" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">
                                <option value="">-- Tidak menyertakan lowongan --</option>
                                @foreach($mitraJobs as $job)
                                    <option value="{{ $job->id }}">{{ $job->title }} ({{ $job->type }} • {{ $job->location }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">Link pendaftaran akan otomatis ditambahkan di akhir pesan.</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Pesan Anda</label>
                            <textarea name="message" required rows="5" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-[#0B1B3D] focus:outline-none focus:border-[#004AAD] focus:ring-1 focus:ring-[#004AAD] transition">Halo {{ $student->name }},

Kami telah melihat profil dan portofolio Anda di Career Intelligence. Kami sangat tertarik dengan kualifikasi Anda dan ingin mengundang Anda untuk bergabung bersama kami.

Silakan daftar melalui link lowongan yang terlampir di bawah.

Salam hangat,
{{ Auth::user()->name }}</textarea>
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="openContactModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition border border-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#004AAD] hover:bg-[#004AAD] text-white text-sm font-bold transition shadow-lg shadow-[#004AAD]/20">Kirim Email Sekarang</button>
                        </div>
                    </form>
                </div>
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
                          <div class="flex items-center gap-3 text-sm">
                              <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-300 flex items-center justify-center text-slate-500 shrink-0">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                              </div>
                              <div>
                                  <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Asal Institusi</p>
                                  <p class="text-slate-600">{{ $profile->institution_name ?? '-' }}</p>
                              </div>
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
                    </div>

                    @if($profile->bio)
                        <div class="pt-4 border-t border-slate-200">
                            <p class="text-xs font-bold text-slate-600 mb-2">Tentang</p>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $profile->bio }}</p>
                        </div>
                    @endif
                </div>

                {{-- Assessment Card --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl">
                    <h3 class="text-base font-extrabold text-[#0B1B3D] mb-3">Hasil Penilaian Sistem</h3>
                    
                    @if($latestAssessment)
                        <div class="mb-5 p-3.5 bg-blue-50/70 border border-blue-200 rounded-2xl flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-[#004AAD] text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tanggal Asesmen Terbaru</p>
                                    <p class="text-xs font-black text-[#0B1B3D]">{{ $latestAssessment->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                                </div>
                            </div>
                            <span class="text-[10.5px] font-bold text-[#004AAD] bg-white px-2.5 py-1 rounded-lg border border-blue-200 shadow-2xs">
                                {{ $latestAssessment->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @else
                        <div class="mb-5 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Kandidat belum pernah melakukan tes asesmen AI.</span>
                        </div>
                    @endif

                    @php
                        $empScore = (int) ($latestAssessment->employability_score ?? $profile->employability_score ?? 0);
                        $statusLabel = $empScore >= 80 ? 'Siap Kerja' : ($empScore >= 50 ? 'Berkembang' : 'Baru Mulai');
                        $isReady = $empScore >= 80;
                    @endphp

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Employability</p>
                            <p class="text-3xl font-black {{ $empScore >= 80 ? 'text-emerald-600' : ($empScore >= 50 ? 'text-amber-600' : 'text-slate-600') }}">{{ $empScore }}%</p>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tes Gemini AI</p>
                            <p class="text-3xl font-black text-[#0B1B3D]">{{ round($latestAssessment->total_score ?? 0) }}</p>
                        </div>
                    </div>

                    <div class="mb-5 flex items-center justify-between p-3 rounded-xl {{ $empScore >= 80 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($empScore >= 50 ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-slate-100 border-slate-200 text-slate-700') }} border">
                        <span class="text-xs font-bold">Status Kesiapan:</span>
                        <span class="text-xs font-black uppercase px-2 py-1 rounded {{ $empScore >= 80 ? 'bg-emerald-200/60 text-emerald-900' : ($empScore >= 50 ? 'bg-amber-200/60 text-amber-900' : 'bg-slate-200 text-slate-800') }}">{{ $statusLabel }}</span>
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

                    @if($student->assessments->isNotEmpty())
                        <div class="mt-5 pt-4 border-t border-slate-200">
                            <p class="text-xs font-bold text-slate-700 mb-2.5 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Riwayat Tanggal Asesmen ({{ $student->assessments->count() }})
                            </p>
                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                @foreach($student->assessments as $idx => $asm)
                                    <div class="p-2.5 rounded-xl border {{ $idx === 0 ? 'bg-blue-50/40 border-blue-200' : 'bg-slate-50 border-slate-200' }} flex items-center justify-between gap-2">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-[#0B1B3D]">{{ $asm->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                                @if($idx === 0)
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#004AAD] text-white">Terbaru</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-0.5">
                                                Bidang: {{ strtoupper($asm->field) }} &bull; Jawaban Benar: {{ $asm->correct_answers }}/{{ $asm->total_questions }}
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-black text-[#004AAD]">{{ round($asm->total_score) }}</span>
                                            <span class="text-[9px] text-slate-400 block font-medium">Poin AI</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Skills & Portfolios --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Kesesuaian dengan Lowongan Perusahaan Anda --}}
                @if(isset($jobCompatibilities) && $jobCompatibilities->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div>
                                <h3 class="text-base font-extrabold text-[#0B1B3D] flex items-center gap-2">
                                    <div class="p-1.5 rounded-lg bg-[#004AAD]/15 text-[#004AAD]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    Kesesuaian dengan Lowongan Buka Anda
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Analisis kecocokan profil & skill kandidat terhadap lowongan buka perusahaan Anda</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#004AAD]/10 text-[#004AAD] border border-[#004AAD]/20">
                                {{ $jobCompatibilities->count() }} Lowongan Buka
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach($jobCompatibilities as $comp)
                                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="min-w-0 space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs sm:text-sm font-bold text-[#0B1B3D]">{{ $comp['job_title'] }}</h4>
                                            <span class="text-[10px] text-slate-500 font-medium">({{ $comp['job_department'] }})</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                {{ $comp['matched_skills_count'] }}/{{ $comp['total_required_skills'] }} Skill Cocok
                                            </span>
                                            @foreach($comp['matched_skills'] as $msk)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white text-emerald-800 border border-emerald-300">
                                                    ✓ {{ $msk }}
                                                </span>
                                            @endforeach
                                            @foreach($comp['missing_skills'] as $misk)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                    {{ $misk }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0 self-end sm:self-center">
                                        <div class="text-right">
                                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Kecocokan</p>
                                            <p class="text-sm font-black {{ $comp['match_score'] >= 70 ? 'text-[#004AAD]' : 'text-amber-700' }}">{{ $comp['match_score'] }}%</p>
                                        </div>
                                        <button @click="selectedJobId = '{{ $comp['job_id'] }}'; openContactModal = true" type="button" class="px-3 py-1.5 rounded-xl bg-[#004AAD] hover:bg-[#003B8B] text-white text-xs font-bold transition shadow-sm flex items-center gap-1">
                                            <span>Undang</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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
                            <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-[#0B1B3D] text-sm font-semibold border border-slate-300 shadow-sm">{{ $skill }}</span>
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
                                        <a href="{{ $portfolio->project_url }}" target="_blank" class="text-xs font-semibold text-[#0B1B3D] bg-blue-50 hover:bg-blue-100 text-[#004AAD] px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 border border-blue-200">
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
    </div>
</x-mitra-layout>








