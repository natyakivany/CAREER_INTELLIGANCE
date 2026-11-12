<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Career Intelligence - Sistem Kesiapan Karier Mahasiswa IT & Elektro</title>

    <meta name="description" content="Platform evaluasi kesiapan karier mahasiswa berbasis portofolio proyek nyata dan asesmen adaptif Gemini AI untuk bidang Teknologi Informasi dan Teknik Elektro.">

    

    <!-- Modern Typography -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    

    <!-- Scripts & Styles -->

    <script src="https://cdn.tailwindcss.com"></script>

    <script>

                tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                            primary: '#004AAD',
                            accent: '#FF44B7',
                            digiblue: '#004AAD',
                            digipink: '#FF44B7',
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#38bdf8',
                            600: '#004AAD',
                            700: '#003B8B',
                            800: '#0b233a',
                            900: '#071a2e',
                        },
                    }
                }
            }
        }

            }

        }

    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>



    <style>

        [x-cloak] { display: none !important; }

        .glow-effect {

            box-shadow: 0 0 50px -10px rgba(0, 201, 159, 0.25);

        }

    </style>

</head>

<body class="antialiased bg-white text-slate-800 font-sans selection:bg-[#004AAD] selection:text-slate-900">



    <!-- Header Navigation -->

        <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200 transition duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="/" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 bg-[#004AAD] rounded-xl flex items-center justify-center shadow-md shadow-[#004AAD]/25 group-hover:scale-105 transition-transform shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-black tracking-tight text-[#004AAD] group-hover:opacity-90 transition">CAREER <span class="text-[#FF44B7]">INTELLIGENCE</span></span>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-8 text-[14px] font-medium text-slate-600">
                    <a href="#fitur" class="hover:text-[#004AAD] transition">Fitur Utama</a>
                    <a href="#alur" class="hover:text-[#004AAD] transition">Alur Penilaian</a>
                    <a href="#eksplorasi" class="hover:text-[#004AAD] transition">Standar Profesi</a>
                    <a href="#faq" class="hover:text-[#004AAD] transition">Panduan & FAQ</a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="flex items-center gap-3">
                    @if(Route::has('login'))
                        @if(auth()->guard()->check())
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#004AAD] hover:bg-[#003B8B] rounded-xl transition shadow-lg flex items-center gap-2">
                                    Dashboard Admin
                                </a>
                            @elseif(Auth::user()->role === 'mitra')
                                <a href="{{ route('mitra.dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#004AAD] hover:bg-[#003B8B] rounded-xl transition shadow-lg flex items-center gap-2">
                                    Dashboard Mitra
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#004AAD] hover:bg-[#003B8B] rounded-xl transition shadow-lg flex items-center gap-2">
                                    Buka Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition">
                                Masuk Portal
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-[#004AAD] hover:bg-[#003B8B] rounded-xl transition shadow-md shadow-[#004AAD]/20 flex items-center gap-1.5">
                                <span>Daftar Mahasiswa</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-16 pb-24 lg:pt-24 lg:pb-32 bg-white border-b border-slate-200">
        <!-- Minimal Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9IiNlNWEwZWMiLz48L3N2Zz4=')] opacity-50"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-12 gap-16 lg:gap-8 items-center">
                
                <!-- Left Column: Copy & Value Proposition -->
                <div class="lg:col-span-6 space-y-8 text-left">
                    
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-blue-50 border border-blue-200 shadow-sm">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#004AAD]"></span>
                        </span>
                        <span class="text-[12px] font-bold tracking-wide text-[#004AAD]">
                            Sistem Penilaian Mandiri Mahasiswa
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl xl:text-[3.5rem] font-extrabold text-[#0B1B3D] tracking-tight leading-[1.15]">
                        Buktikan Kompetensi, <br>
                        Temukan <span class="text-[#004AAD]">Kesiapan Kerja Nyata</span> Anda.
                    </h1>

                    <p class="text-slate-600 text-lg leading-relaxed max-w-xl font-medium">
                        Platform intelijen karier terpadu untuk mahasiswa Teknologi Informasi dan Teknik Elektro. Evaluasi profil akademik, validasi portofolio proyek riil, dan petakan kesenjangan skill secara objektif.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl shadow-sm hover:-translate-y-0.5 transition duration-200 flex items-center gap-2.5 text-base">
                            <span>Mulai Evaluasi Mandiri</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="#alur" class="px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-300 transition flex items-center gap-2 text-base shadow-sm">
                            <span>Pelajari Alur Sistem</span>
                        </a>
                    </div>

                    <div class="pt-6 flex flex-wrap gap-6 text-sm font-medium text-slate-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#38bdf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Evaluasi Portofolio</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#38bdf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Didukung AI</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#38bdf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>CV ATS Otomatis</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Dashboard Preview (Clean Light Version) -->
                <div class="lg:col-span-6 relative">
                    <div class="relative bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                        <!-- Topbar -->
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-slate-300"></div>
                                <div class="w-3 h-3 rounded-full bg-slate-300"></div>
                                <div class="w-3 h-3 rounded-full bg-slate-300"></div>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 flex-1">dashboard.careerintel.app</span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-8 space-y-6">
                            
                            <!-- Profile -->
                            <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-[#f0f9ff] text-[#004AAD] flex items-center justify-center font-bold text-xl border border-[#e0f2fe] shadow-sm">
                                        MH
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900">Mahasiswa Pengguna</h2>
                                        <p class="text-xs text-slate-500 mt-1">Teknologi Informasi & Teknik Elektro</p>
                                    </div>
                                </div>
                                <span class="hidden sm:inline-flex px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-bold border border-green-200 shadow-sm">
                                    Profil Terisi
                                </span>
                            </div>

                            <!-- Metrics -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 flex flex-col justify-center items-center text-center">
                                    <div class="text-4xl font-extrabold text-[#0B1B3D] tracking-tight">82%</div>
                                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-2">Employability</div>
                                </div>
                                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 flex flex-col justify-center">
                                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Target Profesi</div>
                                    <div class="text-sm font-bold text-[#004AAD]">Full Stack Web Engineer</div>
                                </div>
                            </div>

                            <!-- Skills -->
                            <div class="space-y-4 pt-2">
                                <div class="flex justify-between items-center text-sm mb-3">
                                    <span class="font-bold text-slate-900">Peta Kesiapan Kompetensi</span>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-slate-600 font-medium">Laravel & RESTful Backend</span>
                                        <span class="text-green-600 font-bold">90%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-green-500 h-full rounded-full" style="width: 90%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-slate-600 font-medium">JavaScript & Frontend Framework</span>
                                        <span class="text-[#38bdf8] font-bold">80%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-[#38bdf8] h-full rounded-full" style="width: 80%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-slate-600 font-medium">Deployment & Containerization</span>
                                        <span class="text-yellow-600 font-bold">60%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-yellow-500 h-full rounded-full" style="width: 60%"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<section id="fitur" class="py-24 bg-slate-50 border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            

            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">

                <span class="text-xs font-extrabold uppercase tracking-widest text-[#004AAD] bg-[#004AAD]/10 px-3 py-1 rounded-full border border-[#004AAD]/30">

                    Fitur Lengkap Platform

                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0B1B3D] tracking-tight">

                    Ekosistem Pemetaan Kesiapan Karier Mahasiswa

                </h2>

                <p class="text-slate-500 text-base">

                    Menggabungkan validasi karya nyata dan evaluasi kecerdasan buatan untuk mengukur kesiapan kerja secara adil dan terukur.

                </p>

            </div>



            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                

                <!-- Card 1: Profil Mahasiswa -->

                <div class="bg-white p-7 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 hover:-translate-y-1 transition duration-300 group shadow-lg">

                    <div class="w-12 h-12 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">

                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>

                    </div>

                    <span class="text-[11px] font-bold tracking-wider uppercase text-blue-400">Fondasi Akademik</span>

                    <h3 class="text-xl font-bold text-[#0B1B3D] mt-1 mb-2">Profil Mahasiswa</h3>

                    <p class="text-slate-500 text-sm leading-relaxed">

                        Data diri akademik terstruktur meliputi program studi, semester, keahlian utama, dan target profesi yang ingin dicapai setelah lulus.

                    </p>

                </div>



                <!-- Card 2: Portofolio Proyek Nyata -->

                <div class="bg-white p-7 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 hover:-translate-y-1 transition duration-300 group shadow-lg">

                    <div class="w-12 h-12 rounded-xl bg-[#004AAD]/15 text-[#004AAD] flex items-center justify-center mb-6 group-hover:scale-110 transition">

                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>

                    </div>

                    <span class="text-[11px] font-bold tracking-wider uppercase text-[#38bdf8]">Bukti Hasil Karya</span>

                    <h3 class="text-xl font-bold text-[#0B1B3D] mt-1 mb-2">Portofolio Proyek Nyata</h3>

                    <p class="text-slate-500 text-sm leading-relaxed">

                        Bukan sekadar klaim keahlian. Mahasiswa mengunggah tautan repositori, dokumentasi sistem hardware, atau laporan proyek sebagai bobot penilaian utama.

                    </p>

                </div>



                <!-- Card 3: Skill Assessment Adaptif Gemini AI -->

                <div class="bg-white p-7 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 hover:-translate-y-1 transition duration-300 group shadow-lg">

                    <div class="w-12 h-12 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">

                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>

                    </div>

                    <span class="text-[11px] font-bold tracking-wider uppercase text-purple-400">Uji Logika & Praktik</span>

                    <h3 class="text-xl font-bold text-[#0B1B3D] mt-1 mb-2">Asesmen Cerdas Gemini AI</h3>

                    <p class="text-slate-500 text-sm leading-relaxed">

                        Soal asesmen teknis yang dibuat secara dinamis dan adaptif oleh Google Gemini API sesuai bidang minat Anda (IT atau Elektro).

                    </p>

                </div>



                <!-- Card 4: Skill Gap Analysis -->

                <div class="bg-white p-7 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 hover:-translate-y-1 transition duration-300 group shadow-lg">

                    <div class="w-12 h-12 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">

                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>

                    </div>

                    <span class="text-[11px] font-bold tracking-wider uppercase text-amber-400">Peta Kesenjangan</span>

                    <h3 class="text-xl font-bold text-[#0B1B3D] mt-1 mb-2">Skill Gap Analysis</h3>

                    <p class="text-slate-500 text-sm leading-relaxed">

                        Visualisasi objektif yang membandingkan kemampuan riil Anda dengan tolak ukur kompetensi profesi industri. Ketahui materi yang perlu dikejar.

                    </p>

                </div>



                <!-- Card 5: Rekomendasi Karier & Roadmap -->

                <div class="bg-white p-7 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 hover:-translate-y-1 transition duration-300 group shadow-lg">

                    <div class="w-12 h-12 rounded-xl bg-rose-500/15 text-rose-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">

                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>

                    </div>

                    <span class="text-[11px] font-bold tracking-wider uppercase text-rose-400">Panduan Akselerasi</span>

                    <h3 class="text-xl font-bold text-[#0B1B3D] mt-1 mb-2">Roadmap Belajar Terarah</h3>

                    <p class="text-slate-500 text-sm leading-relaxed">

                        Sistem menyusun rekomendasi langkah belajar konkret modul demi modul untuk menambal celah kompetensi Anda sebelum masa pekerjaan atau wisuda.

                    </p>

                </div>



                <!-- Card 6: CV Builder ATS -->

                <div class="bg-white p-7 rounded-2xl border border-slate-200 hover:border-[#004AAD]/40 hover:-translate-y-1 transition duration-300 group shadow-lg">

                    <div class="w-12 h-12 rounded-xl bg-teal-500/15 text-[#004AAD] flex items-center justify-center mb-6 group-hover:scale-110 transition">

                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>

                    </div>

                    <span class="text-[11px] font-bold tracking-wider uppercase text-[#004AAD]">Output Siap Kerja</span>

                    <h3 class="text-xl font-bold text-[#0B1B3D] mt-1 mb-2">Pembuat CV ATS Otomatis</h3>

                    <p class="text-slate-500 text-sm leading-relaxed">

                        Otomatisasi pembuatan CV berstandar ATS (Applicant Tracking System) yang merangkum data profil, riwayat portofolio, dan nilai asesmen terverifikasi.

                    </p>

                </div>



            </div>



        </div>

    </section>



    <!-- Interactive Career Explorer Section (IT & Elektro Real Tracks) -->

    <section id="eksplorasi" class="py-24 bg-white border-b border-slate-200" x-data="{

        activeTab: 'web',

        careers: {

            'web': {

                title: 'Full Stack Web Developer',

                department: 'Bidang Teknologi Informasi',

                description: 'Merancang arsitektur aplikasi web modern, membangun RESTful API yang andal, dan menghubungkan backend ke antarmuka pengguna interaktif.',

                demand: 'Standar Industri Terkini',

                skills: [

                    { name: 'Laravel & RESTful Backend API', level: 85, focus: 'Backend' },

                    { name: 'Modern JavaScript (Vue / React / Blade)', level: 80, focus: 'Frontend' },

                    { name: 'Database MySQL & PostgreSQL Design', level: 75, focus: 'Database' },

                    { name: 'Version Control (Git & GitHub Workflow)', level: 80, focus: 'Tools' }

                ],

                sampleProject: 'Aplikasi Web CRUD Terpadu dengan Autentikasi dan API Endpoint'

            },

            'iot': {

                title: 'IoT & Embedded Systems Specialist',

                department: 'Bidang Teknik Elektro',

                description: 'Mengembangkan sistem kendali perangkat pintar berbasis mikrokontroler, integrasi sensor cerdas, dan transmisi data telemetri ke cloud.',

                demand: 'Standar Industri Terkini',

                skills: [

                    { name: 'Pemrograman C / C++ Mikrokontroler (ESP32 / STM32)', level: 85, focus: 'Hardware Logic' },

                    { name: 'Protokol Komunikasi (MQTT, HTTP, I2C, SPI)', level: 80, focus: 'Networking' },

                    { name: 'Desain Skematik & Perakitan Sensor / Aktuator', level: 75, focus: 'Electronics' },

                    { name: 'Monitoring Dashboard & Integrasi Cloud IoT', level: 70, focus: 'Platform' }

                ],

                sampleProject: 'Sistem Telemetri Monitoring Energi / Lingkungan Berbasis IoT & Dashboard'

            },

            'plc': {

                title: 'Industrial Automation & PLC Specialist',

                department: 'Bidang Teknik Elektro',

                description: 'Memprogram logika pengendali terprogram (PLC), mengintegrasikan sistem SCADA/HMI, dan memelihara sistem kendali mesin produksi industri.',

                demand: 'Standar Industri Terkini',

                skills: [

                    { name: 'Ladder Diagram & Pemrograman PLC Standar', level: 85, focus: 'Control Logic' },

                    { name: 'Desain Antarmuka HMI / SCADA', level: 75, focus: 'Interface' },

                    { name: 'Instrumentasi Sensor & Pengawatan Listrik Industri', level: 80, focus: 'Hardware' },

                    { name: 'Troubleshooting Sistem Pneumatik & Motor Listrik', level: 70, focus: 'Maintenance' }

                ],

                sampleProject: 'Simulasi Sistem Konveyor Otomatis dengan Kontrol PLC dan Monitor HMI'

            },

            'data': {

                title: 'Data Analyst & Business Intelligence',

                department: 'Bidang Teknologi Informasi',

                description: 'Membersihkan dan mengolah data operasional, merancang visualisasi data interaktif, dan menyusun laporan analitik untuk pengambilan keputusan.',

                demand: 'Standar Industri Terkini',

                skills: [

                    { name: 'SQL Querying & Data Extraction', level: 85, focus: 'Query' },

                    { name: 'Python for Data Analysis (Pandas, NumPy)', level: 75, focus: 'Analysis' },

                    { name: 'Dashboarding & Visualisasi (Power BI / Looker)', level: 80, focus: 'Dashboard' },

                    { name: 'Interpretasi Data & Penyusunan Insight Bisnis', level: 75, focus: 'Insight' }

                ],

                sampleProject: 'Dashboard Visualisasi Kinerja Akademik atau Tren Transaksi Penjualan'

            }

        }

    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            

            <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">

                <span class="text-xs font-extrabold uppercase tracking-widest text-[#004AAD] bg-[#004AAD]/10 px-3 py-1 rounded-full border border-[#004AAD]/30">

                    Tolak Ukur Profesi

                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0B1B3D] tracking-tight">

                    Eksplorasi Standar Kompetensi Profesi

                </h2>

                <p class="text-slate-500 text-base">

                    Pilih target profesi di bawah untuk melihat contoh pemetaan keahlian dan proyek yang dievaluasi dalam sistem Career Intelligence.

                </p>

            </div>



            <!-- Tabs Nav -->

            <div class="flex flex-wrap justify-center gap-3 mb-10">

                <button @click="activeTab = 'web'" :class="activeTab === 'web' ? 'bg-[#004AAD] text-white font-bold shadow-lg shadow-[#38bdf8]/50/20' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">

                    <span>💻 Web Developer (IT)</span>

                </button>

                <button @click="activeTab = 'iot'" :class="activeTab === 'iot' ? 'bg-[#004AAD] text-white font-bold shadow-lg shadow-[#38bdf8]/50/20' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">

                    <span>📡 IoT & Embedded (Elektro)</span>

                </button>

                <button @click="activeTab = 'plc'" :class="activeTab === 'plc' ? 'bg-[#004AAD] text-white font-bold shadow-lg shadow-[#38bdf8]/50/20' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">

                    <span>⚙️ Otomasi & PLC (Elektro)</span>

                </button>

                <button @click="activeTab = 'data'" :class="activeTab === 'data' ? 'bg-[#004AAD] text-white font-bold shadow-lg shadow-[#38bdf8]/50/20' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">

                    <span>📊 Data Analyst (IT)</span>

                </button>

            </div>



            <!-- Tab Content Panel -->

            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-2xl max-w-5xl mx-auto">

                <div class="grid lg:grid-cols-12 gap-8 items-start">

                    

                    <!-- Left: Role Overview -->

                    <div class="lg:col-span-5 space-y-6">

                        <div>

                            <span class="text-xs font-bold uppercase tracking-wider text-[#004AAD]" x-text="careers[activeTab].department"></span>

                            <h3 class="text-2xl font-bold text-[#0B1B3D] mt-1" x-text="careers[activeTab].title"></h3>

                            <p class="text-slate-600 text-sm mt-3 leading-relaxed" x-text="careers[activeTab].description"></p>

                        </div>



                        <div class="space-y-3 pt-2">

                            <div class="p-3.5 rounded-xl bg-slate-50/90 border border-slate-200">

                                <span class="text-xs text-slate-500 block mb-1">Contoh Portofolio yang Dianjurkan:</span>

                                <span class="text-xs font-semibold text-[#38bdf8]" x-text="careers[activeTab].sampleProject"></span>

                            </div>

                        </div>



                        <div class="pt-2">

                            <p class="text-xs text-slate-500 leading-relaxed">

                                Sistem akan membandingkan hasil isian profil, bukti portofolio, dan jawaban asesmen Anda terhadap standar di atas.

                            </p>

                        </div>

                    </div>



                    <!-- Right: Required Competency Breakdown -->

                    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200 space-y-5">

                        <div class="flex justify-between items-center pb-2 border-b border-slate-200">

                            <span class="text-xs font-bold text-[#0B1B3D] tracking-wide uppercase">Parameter Kompetensi yang Dinilai</span>

                            <span class="text-[11px] text-[#004AAD]">Standar Kesiapan: 75%</span>

                        </div>



                        <div class="space-y-4">

                            <template x-for="skill in careers[activeTab].skills" :key="skill.name">

                                <div class="space-y-1.5">

                                    <div class="flex justify-between text-xs">

                                        <span class="font-medium text-[#0B1B3D]" x-text="skill.name"></span>

                                        <div class="flex items-center gap-2">

                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-white text-slate-500 font-semibold" x-text="skill.focus"></span>

                                            <span class="font-bold text-[#004AAD]" x-text="skill.level + '%'"></span>

                                        </div>

                                    </div>

                                    <div class="w-full bg-white h-2.5 rounded-full overflow-hidden">

                                        <div class="bg-[#004AAD] h-2.5 rounded-full transition-all duration-500" :style="`width: ${skill.level}%`"></div>

                                    </div>

                                </div>

                            </template>

                        </div>



                        <div class="pt-3 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-3">

                            <span class="text-xs text-slate-500">Ingin mengecek kesiapan Anda pada profesi ini?</span>

                            <a href="{{ route('register') }}" class="px-4 py-2 bg-[#004AAD] hover:bg-[#38bdf8] text-white font-bold text-xs rounded-lg transition shadow-md">

                                Mulai Asesmen Mandiri &rarr;

                            </a>

                        </div>

                    </div>



                </div>

            </div>



        </div>

    </section>



    <!-- 5 Langkah Alur Penilaian Mahasiswa (Real Aligned) -->

    <section id="alur" class="py-24 bg-slate-50 border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            

            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">

                <span class="text-xs font-extrabold uppercase tracking-widest text-[#004AAD] bg-[#004AAD]/10 px-3 py-1 rounded-full border border-[#004AAD]/30">

                    Alur Kerja Sistem

                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0B1B3D] tracking-tight">

                    5 Langkah Mudah Evaluasi Mandiri Mahasiswa

                </h2>

                <p class="text-slate-500 text-base">

                    Proses terstruktur yang memandu Anda dari pengisian profil hingga mendapatkan CV ATS siap pakai.

                </p>

            </div>



            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-5">

                

                <!-- Step 1 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200 relative group hover:border-[#004AAD]/50 transition flex flex-col justify-between">

                    <div>

                        <div class="w-10 h-10 rounded-xl bg-[#004AAD]/15 text-[#004AAD] flex items-center justify-center font-extrabold text-sm mb-4">

                            1

                        </div>

                        <h3 class="text-base font-bold text-[#0B1B3D] mb-2">Lengkapi Profil</h3>

                        <p class="text-slate-500 text-xs leading-relaxed">

                            Isi biodata akademik, program studi (IT / Elektro), semester aktif, dan minat peminatan karier.

                        </p>

                    </div>

                    <span class="mt-4 text-[10px] text-[#38bdf8] font-semibold block">Tahap Awal</span>

                </div>



                <!-- Step 2 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200 relative group hover:border-[#004AAD]/50 transition flex flex-col justify-between">

                    <div>

                        <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center font-extrabold text-sm mb-4">

                            2

                        </div>

                        <h3 class="text-base font-bold text-[#0B1B3D] mb-2">Unggah Portofolio</h3>

                        <p class="text-slate-500 text-xs leading-relaxed">

                            Masukkan tautan proyek nyata, repositori Git, atau dokumentasi hardware yang pernah Anda kerjakan.

                        </p>

                    </div>

                    <span class="mt-4 text-[10px] text-blue-400 font-semibold block">Bukti Karya Riil</span>

                </div>



                <!-- Step 3 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200 relative group hover:border-[#004AAD]/50 transition flex flex-col justify-between">

                    <div>

                        <div class="w-10 h-10 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center font-extrabold text-sm mb-4">

                            3

                        </div>

                        <h3 class="text-base font-bold text-[#0B1B3D] mb-2">Asesmen Gemini AI</h3>

                        <p class="text-slate-500 text-xs leading-relaxed">

                            Kerjakan soal tes adaptif yang digenerate oleh Gemini AI sesuai bidang peminatan Anda secara dinamis.

                        </p>

                    </div>

                    <span class="mt-4 text-[10px] text-purple-400 font-semibold block">Uji Kompetensi AI</span>

                </div>



                <!-- Step 4 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200 relative group hover:border-[#004AAD]/50 transition flex flex-col justify-between">

                    <div>

                        <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center font-extrabold text-sm mb-4">

                            4

                        </div>

                        <h3 class="text-base font-bold text-[#0B1B3D] mb-2">Skill Gap & Roadmap</h3>

                        <p class="text-slate-500 text-xs leading-relaxed">

                            Sistem menghitung kesenjangan kemampuan dan memberikan modul rekomendasi belajar yang terarah.

                        </p>

                    </div>

                    <span class="mt-4 text-[10px] text-amber-400 font-semibold block">Peta Kesenjangan</span>

                </div>



                <!-- Step 5 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200 relative group hover:border-[#004AAD]/50 transition flex flex-col justify-between">

                    <div>

                        <div class="w-10 h-10 rounded-xl bg-teal-500/15 text-[#004AAD] flex items-center justify-center font-extrabold text-sm mb-4">

                            5

                        </div>

                        <h3 class="text-base font-bold text-[#0B1B3D] mb-2">Ekspor CV ATS</h3>

                        <p class="text-slate-500 text-xs leading-relaxed">

                            Unduh CV berstandar ATS yang telah disinkronkan dengan hasil evaluasi dan portofolio terverifikasi.

                        </p>

                    </div>

                    <span class="mt-4 text-[10px] text-[#004AAD] font-semibold block">Siap Lamaran</span>

                </div>



            </div>



        </div>

    </section>



    <!-- FAQ & Panduan Platform (Realistic & Informative - Replacing Fake Testimonials) -->

    <section id="faq" class="py-24 bg-white border-b border-slate-200">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            

            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">

                <span class="text-xs font-extrabold uppercase tracking-widest text-[#004AAD] bg-[#004AAD]/10 px-3 py-1 rounded-full border border-[#004AAD]/30">

                    Transparansi & Panduan

                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0B1B3D] tracking-tight">

                    Pertanyaan yang Sering Diajukan

                </h2>

                <p class="text-slate-500 text-base">

                    Pelajari bagaimana sistem Career Intelligence bekerja menilai kesiapan mahasiswa secara adil dan objektif.

                </p>

            </div>



            <div class="space-y-5">

                

                <!-- FAQ 1 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200">

                    <h3 class="text-base font-bold text-[#0B1B3D] mb-2 flex items-center gap-2.5">

                        <span class="w-6 h-6 rounded-lg bg-[#004AAD]/20 text-[#004AAD] flex items-center justify-center text-xs font-bold">?</span>

                        Mengapa penilaian membutuhkan portofolio, bukan hanya tes pilihan ganda?

                    </h3>

                    <p class="text-slate-600 text-sm leading-relaxed pl-8">

                        Dunia kerja tidak hanya melihat skor tes teori, tetapi membutuhkan bukti karya nyata. Dengan mengintegrasikan penilaian portofolio proyek (seperti aplikasi web, repositori Git, atau rancang bangun mikrokontroler/IoT) bersama tes asesmen, nilai kesiapan kerja (<em class="text-[#004AAD]">Employability Score</em>) menjadi jauh lebih akurat dan realistis.

                    </p>

                </div>



                <!-- FAQ 2 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200">

                    <h3 class="text-base font-bold text-[#0B1B3D] mb-2 flex items-center gap-2.5">

                        <span class="w-6 h-6 rounded-lg bg-[#004AAD]/20 text-[#004AAD] flex items-center justify-center text-xs font-bold">?</span>

                        Bagaimana Google Gemini AI digunakan dalam sistem ini?

                    </h3>

                    <p class="text-slate-600 text-sm leading-relaxed pl-8">

                        Sistem terhubung langsung dengan Google Gemini API untuk menghasilkan paket soal asesmen teknis yang bervariasi dan adaptif sesuai bidang yang dipilih (Teknologi Informasi atau Teknik Elektro). Hal ini memastikan soal relevan dengan tren industri saat ini dan meminimalisir pengulangan soal.

                    </p>

                </div>



                <!-- FAQ 3 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200">

                    <h3 class="text-base font-bold text-[#0B1B3D] mb-2 flex items-center gap-2.5">

                        <span class="w-6 h-6 rounded-lg bg-[#004AAD]/20 text-[#004AAD] flex items-center justify-center text-xs font-bold">?</span>

                        Apa manfaat fitur Skill Gap Analysis bagi mahasiswa?

                    </h3>

                    <p class="text-slate-600 text-sm leading-relaxed pl-8">

                        Fitur ini menunjukkan secara transparan di mana posisi kemampuan Anda saat ini dibandingkan standar kebutuhan industri untuk peran yang Anda tuju. Jika terdeteksi kesenjangan (misalnya pada topik Docker atau protokol MQTT), sistem akan merekomendasikan materi pembelajaran spesifik di Career Roadmap.

                    </p>

                </div>



                <!-- FAQ 4 -->

                <div class="bg-white p-6 rounded-2xl border border-slate-200">

                    <h3 class="text-base font-bold text-[#0B1B3D] mb-2 flex items-center gap-2.5">

                        <span class="w-6 h-6 rounded-lg bg-[#004AAD]/20 text-[#004AAD] flex items-center justify-center text-xs font-bold">?</span>

                        Bagaimana cara mengunduh CV ATS dari sistem?

                    </h3>

                    <p class="text-slate-600 text-sm leading-relaxed pl-8">

                        Setelah Anda melengkapi profil, mengisi portofolio, dan menyelesaikan asesmen, menu <span class="text-[#0B1B3D] font-semibold">CV Builder</span> akan secara otomatis merangkum semua data tersebut ke dalam format dokumen yang ramah sistem penyaring rekruter (ATS-Friendly) dan siap diunduh.

                    </p>

                </div>



            </div>



        </div>

    </section>



    <!-- Final Call to Action Banner -->

    <section class="py-20 relative overflow-hidden bg-slate-50">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="relative rounded-3xl bg-white border border-slate-200 p-8 sm:p-14 text-center shadow-xl overflow-hidden">

                <div class="relative z-10 space-y-6 max-w-2xl mx-auto">

                    <span class="inline-block px-3 py-1 bg-[#004AAD]/10 text-[#004AAD] font-bold text-xs rounded-full border border-[#004AAD]/20">

                        Akses Terbuka untuk Mahasiswa

                    </span>

                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0B1B3D] tracking-tight leading-tight">

                        Ketahui Seberapa Siap Anda Masuk ke Dunia Profesi

                    </h2>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">

                        Mulai langkah awal Anda sekarang. Daftarkan akun mahasiswa, isi portofolio proyek Anda, dan dapatkan laporan pemetaan skill gap serta roadmap belajar terarah.

                    </p>

                    <div class="flex flex-wrap justify-center gap-4 pt-2">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-[#004AAD] hover:bg-[#083577] text-white font-bold rounded-xl shadow-sm transition text-base">
                            Daftar & Mulai Evaluasi
                        </a>
                        <a href="{{ route('login') }}" class="px-7 py-4 bg-white hover:bg-slate-100 text-[#0B1B3D] font-semibold rounded-xl border border-slate-200 transition text-base">
                            Masuk ke Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (Real & Grounded) -->
    <footer class="bg-white text-slate-500 py-16 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                <!-- Col 1: Brand & Info -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#004AAD] text-white rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-[#004AAD] tracking-tight">CAREER <span class="text-[#FF44B7]">INTELLIGENCE</span></span>
                            <p class="text-[9px] text-[#004AAD] font-semibold tracking-wider uppercase">Sistem Kesiapan Karier Mahasiswa</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed max-w-sm">

                        Platform evaluasi kesiapan kerja berbasis portofolio proyek nyata dan asesmen adaptif Gemini AI. Dikhususkan untuk membantu mahasiswa memetakan kompetensi riil dan menambal skill gap sebelum lulus.

                    </p>

                    <div class="pt-2 text-xs text-slate-500">

                        Inisiatif Pengembangan Karier Mahasiswa Kampus<br>

                        Fokus: Jurusan Teknologi Informasi & Teknik Elektro

                    </div>

                </div>



                <!-- Col 2: Fitur Mahasiswa -->

                <div class="md:col-span-3 space-y-3">

                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#0B1B3D]">Alur Mahasiswa</h4>

                    <ul class="space-y-2 text-xs">

                        <li><a href="{{ route('register') }}" class="hover:text-[#004AAD] transition">Registrasi Akun</a></li>

                        <li><a href="#fitur" class="hover:text-[#004AAD] transition">Profil & Portofolio Proyek</a></li>

                        <li><a href="#fitur" class="hover:text-[#004AAD] transition">Asesmen Adaptif Gemini AI</a></li>

                        <li><a href="#fitur" class="hover:text-[#004AAD] transition">Peta Skill Gap</a></li>

                        <li><a href="#fitur" class="hover:text-[#004AAD] transition">Roadmap & Pembuat CV ATS</a></li>

                    </ul>

                </div>



                <!-- Col 3: Bidang Keahlian -->

                <div class="md:col-span-4 space-y-3">

                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#0B1B3D]">Bidang Keahlian</h4>

                    <ul class="space-y-2 text-xs">

                        <li><span class="text-slate-600">Teknologi Informasi:</span> Rekayasa Perangkat Lunak, Web, Cloud & Data</li>

                        <li><span class="text-slate-600">Teknik Elektro:</span> Embedded Systems, IoT Solutions, Otomasi & PLC</li>

                    </ul>

                    <div class="pt-3">

                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-50 text-[#004AAD] text-[11px] font-semibold border border-slate-200">

                            <span class="w-1.5 h-1.5 rounded-full bg-[#004AAD]"></span>

                            Didukung Google Gemini AI API

                        </span>

                    </div>

                </div>



            </div>



            <div class="mt-12 pt-8 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-4">

                <p>&copy; {{ date('Y') }} Career Intelligence Platform. Inisiasi Kesiapan Karier Mahasiswa.</p>

                <div class="flex gap-6">

                    <a href="{{ route('login') }}" class="hover:text-slate-600 transition">Masuk Portal</a>

                    <a href="{{ route('register') }}" class="hover:text-slate-600 transition">Daftar Akun</a>

                </div>

            </div>

        </div>

    </footer>



</body>

</html>


<?php /**PATH D:\CAREER_INTELLIGENCE\resources\views/welcome.blade.php ENDPATH**/ ?>








