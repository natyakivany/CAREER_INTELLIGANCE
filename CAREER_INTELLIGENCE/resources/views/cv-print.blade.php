<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV ATS - {{ $user->name }} - {{ $targetOccupation->title ?? 'Professional' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 0; /* Menghilangkan URL 127.0.0.1, header, dan footer bawaan browser secara otomatis */
        }
        @media print {
            html, body {
                background: white !important;
                color: #0f172a !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 14mm 16mm !important; /* Margin dokumen ATS A4 yang rapi dan presisi */
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased min-h-screen py-8">

    <!-- Top floating print toolbar -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        @php
            $backUrl = route('cv-builder');
            $backText = 'Kembali ke Editor CV';

            if (auth()->check()) {
                if (auth()->user()->role === 'mitra') {
                    $backUrl = route('mitra.candidates.show', $user->id);
                    $backText = 'Kembali ke Detail Kandidat';
                } elseif (auth()->user()->role === 'admin') {
                    $backUrl = route('admin.students.show', $user->id);
                    $backText = 'Kembali ke Detail Mahasiswa';
                }
            }
        @endphp
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-[#0B1B3D] bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200 transition">
            &larr; {{ $backText }}
        </a>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-medium">Format Standar ATS A4</span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-[#004AAD] hover:bg-[#00b08a] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Paper Sheet (A4 Proportion) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-10 sm:p-14 rounded-2xl shadow-xl border border-gray-200">
        
        <!-- Header -->
        <header class="flex justify-between items-start mb-6">
            <div class="flex-1 pr-4">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-black uppercase mb-3">{{ $user->name }}</h1>
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
        </header>

        <!-- PROFIL -->
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Profil
            </h2>
            <hr class="border-t-[1.5px] border-black mb-2.5">
            <p class="text-[11.5px] leading-relaxed text-black text-justify">
                {{ $profile->cv_summary ?: 'Mahasiswa aktif tingkat akhir dengan rekam jejak penguasaan kompetensi teruji di bidang ' . strtoupper($profile->target_field ?? 'IT') . ', siap mengimplementasikan keahlian teknis dan kolaboratif pada industri modern.' }}
            </p>
        </section>

        <!-- KEAHLIAN TEKNIS -->
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Keahlian Teknis
            </h2>
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
        </section>

        <!-- PENDIDIKAN -->
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Pendidikan
            </h2>
            <hr class="border-t-[1.5px] border-black mb-2.5">
            <div class="flex justify-between items-start text-[11.5px] text-black">
                <div class="w-1/2 pr-4">
                    <strong class="font-bold block">{{ $profile->institution_name ?: 'Politeknik Negeri Padang' }}</strong>
                    <span class="block">Jurusan {{ $profile->jurusan ?? 'Teknologi Informasi' }} / {{ $profile->prodi ?? 'D4 RPL' }}</span>
                    <span class="block mt-0.5">2021 - Sekarang</span>
                </div>
                <div class="w-1/2">
                    <strong class="font-bold block">{{ $profile->high_school_name ?: 'Sekolah Menengah Kejuruan (SMK)' }}</strong>
                    <span class="block">Jurusan {{ $profile->high_school_major ?: 'Teknologi Komputer / Relevan' }}</span>
                    <span class="block mt-0.5">Lulus Tahun {{ $profile->high_school_year ?: '2021' }}</span>
                </div>
            </div>
        </section>

        <!-- PENGALAMAN MAGANG -->
        @if($profile->cv_internships)
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Pengalaman Magang
            </h2>
            <hr class="border-t-[1.5px] border-black mb-2.5">
            <div class="text-[11.5px] text-black leading-relaxed text-justify [&>p]:mb-1 [&>ul]:list-disc [&>ul]:ml-4 [&>ul>li]:mb-0.5 [&>p>strong]:font-bold">
                {!! Str::markdown($profile->cv_internships) !!}
            </div>
        </section>
        @endif

        <!-- PENGALAMAN PROYEK -->
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Pengalaman Proyek
            </h2>
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
        </section>

        <!-- PENGALAMAN ORGANISASI -->
        @if($profile->cv_organizations)
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Pengalaman Organisasi
            </h2>
            <hr class="border-t-[1.5px] border-black mb-2.5">
            <div class="text-[11.5px] text-black leading-relaxed text-justify [&>p]:mb-1 [&>ul]:list-disc [&>ul]:ml-4 [&>ul>li]:mb-0.5 [&>p>strong]:font-bold">
                {!! Str::markdown($profile->cv_organizations) !!}
            </div>
        </section>
        @endif

        <!-- TOOLS & TECHNOLOGY -->
        @if($profile->cv_tools)
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Tools & Technology
            </h2>
            <hr class="border-t-[1.5px] border-black mb-2.5">
            <ul class="list-disc list-outside ml-4 text-[11.5px] leading-relaxed text-black space-y-1">
                @foreach(explode("\n", str_replace("\r", "", $profile->cv_tools)) as $tool)
                    @if(trim($tool)) <li>{{ $tool }}</li> @endif
                @endforeach
            </ul>
        </section>
        @endif

        <!-- SERTIFIKAT -->
        @if($profile->cv_certificates)
        <section class="mb-5">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.2em] text-black mb-1.5">
                Sertifikat & Keterlibatan Lainnya
            </h2>
            <hr class="border-t-[1.5px] border-black mb-2.5">
            <ul class="list-disc list-outside ml-4 text-[11.5px] leading-relaxed text-black space-y-1">
                @foreach(explode("\n", str_replace("\r", "", $profile->cv_certificates)) as $cert)
                    @if(trim($cert)) <li>{{ $cert }}</li> @endif
                @endforeach
            </ul>
        </section>
        @endif



    </div>

</body>
</html>






