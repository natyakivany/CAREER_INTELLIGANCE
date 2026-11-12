<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Career Intel') }} - Portal Pengguna</title>

        <!-- Modern Typography -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Tailwind & Scripts (Matching Welcome & Guest Portals) -->
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
                                50: '#f0fdf9',
                                100: '#ccfbf1',
                                200: '#99f6e4',
                                400: '#2dd4bf',
                                500: '#004AAD',
                                600: '#0d9488',
                                700: '#0f766e',
                                navy: '#0b1120',
                                surface: '#131b2e',
                                card: '#172033',
                                border: '#334155'
                            }
                        }
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] text-slate-800 overflow-hidden selection:bg-[#004AAD] selection:text-white relative">
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiMwMDAwMDAiIGZpbGwtb3BhY2l0eT0iMC4wMyIvPjwvc3ZnPg==')] opacity-60"></div>
        </div>
        <div class="h-screen flex" x-data="{ sidebarOpen: false }">
            
            <!-- Mobile sidebar backdrop -->
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 text-[#0B1B3D] transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col h-screen shrink-0">
                <!-- Logo -->
                <div class="flex items-center px-5 py-6 border-b border-slate-200/80 bg-white">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 bg-[#004AAD] text-white rounded-xl flex items-center justify-center shadow-md shadow-[#004AAD]/20 group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col leading-none">
                            <span class="text-[13px] font-black tracking-tight text-[#004AAD] group-hover:opacity-90 transition whitespace-nowrap">
                                CAREER <span class="text-[#FF44B7]">INTELLIGENCE</span>
                            </span>
                            <span class="text-[9.5px] font-semibold text-slate-400 uppercase tracking-wider mt-1">Portal Pengguna</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4 px-4 space-y-1.5 scrollbar-thin scrollbar-thumb-slate-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('profile.edit') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Profil & Preferensi
                    </a>
                    <a href="{{ route('portfolio') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('portfolio') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('portfolio') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        Portofolio Proyek
                    </a>
                    <a href="{{ route('assessment') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('assessment*') && !request()->routeIs('assessment.history') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('assessment*') && !request()->routeIs('assessment.history') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Asesmen Skill
                    </a>
                    <a href="{{ route('assessment.history') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('assessment.history') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('assessment.history') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Asesmen
                    </a>
                    <a href="{{ route('skill-gap') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('skill-gap') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('skill-gap') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Analisis Skill Gap
                    </a>
                    <a href="{{ route('recommendation') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('recommendation') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('recommendation') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Rekomendasi Karier
                    </a>
                    <a href="{{ route('roadmap') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('roadmap') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('roadmap') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Career Roadmap
                    </a>
                    <a href="{{ route('cv-builder') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('cv-builder*') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('cv-builder*') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        CV Builder ATS
                    </a>
                    <a href="{{ route('jobs') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('jobs') || request()->routeIs('jobs.*') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('jobs') || request()->routeIs('jobs.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Lowongan Kerja
                    </a>
                    
                    @php
                        $unreadStudentNotifs = Auth::user()->unreadNotifications->count();
                    @endphp
                    <a href="{{ route('applications.index') }}" x-data="{ count: {{ $unreadStudentNotifs }} }" @notifications-read.window="count = 0" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('applications.*') ? 'bg-[#FF44B7] text-white shadow-md shadow-[#FF44B7]/25 font-extrabold' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-[13px] transition">
                        <div class="flex items-center">
                            <svg class="w-[18px] h-[18px] mr-3 {{ request()->routeIs('applications.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7] transition' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Riwayat Lamaran
                        </div>
                        @if($unreadStudentNotifs > 0)
                            <template x-if="count > 0">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('applications.*') ? 'bg-white/20 text-white' : 'bg-pink-100 text-[#FF44B7]' }}" x-text="count">
                                    {{ $unreadStudentNotifs }}
                                </span>
                            </template>
                        @endif
                    </a>

                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <div class="pt-3 border-t border-slate-200/60 mt-3">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 bg-amber-500/15 border border-amber-500/30 text-amber-700 font-bold rounded-xl text-xs hover:bg-amber-500/25 transition">
                                <svg class="w-4 h-4 mr-2.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Panel Admin
                            </a>
                        </div>
                    @endif
                </nav>

                <!-- User Info -->
                <div class="p-4 border-t border-slate-200 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-[#004AAD] overflow-hidden shrink-0 border border-blue-200 shadow-md shadow-[#004AAD]/20">
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div class="overflow-hidden flex-1">
                                <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ Auth::user()->name ?? 'Kandidat' }}</p>
                                @php
                                    $profile = Auth::user()->studentProfile;
                                    $isGraduate = $profile && in_array($profile->semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']);
                                @endphp
                                <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                    @if(Auth::user()->nim)
                                        ID: {{ Auth::user()->nim }}
                                    @elseif($isGraduate)
                                        💼 Fresh Graduate
                                    @else
                                        NIM Belum Terdaftar
                                    @endif
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Keluar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F8FAFC]">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <div class="bg-amber-500/10 border-b border-amber-500/30 px-4 py-2 sm:px-8 text-amber-700 text-xs flex items-center justify-between flex-wrap gap-2 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                            <span><strong>Mode Super Admin:</strong> Anda memiliki akses penuh dan sedang meninjau <strong>Portal Pengguna</strong>.</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 font-bold underline hover:text-amber-200 text-xs">
                            Kembali ke Admin Panel &rarr;
                        </a>
                    </div>
                @endif
                <!-- Topbar Header with Notification Bell -->
                <header class="h-16 bg-white/95 border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between shrink-0 backdrop-blur sticky top-0 z-30">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-[#0B1B3D] hover:bg-slate-100 transition focus:outline-none" title="Buka Menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#004AAD] flex items-center justify-center text-white lg:hidden shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-[#0B1B3D] hidden sm:inline">Pusat Karier & Rekomendasi Mahasiswa</span>
                            <span class="font-extrabold text-[#0B1B3D] text-xs sm:hidden">CAREER INTEL</span>
                        </div>
                    </div>

                    <!-- Right Topbar: Notification Bell & Quick Info -->
                    <div class="flex items-center gap-3">
                        <!-- Notification Dropdown -->
                        @php
                            $studentUnreadCount = Auth::user()->unreadNotifications->count();
                            $studentRecentNotifs = Auth::user()->notifications()->take(6)->get();
                        @endphp
                        <div class="relative" x-data="{ open: false, unreadCount: {{ $studentUnreadCount }} }">
                            <button @click="open = !open; if(open && unreadCount > 0) { fetch('{{ route('notifications.readAll') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); unreadCount = 0; window.dispatchEvent(new CustomEvent('notifications-read')); }" class="relative p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:text-[#004AAD] hover:border-[#004AAD]/30 hover:bg-slate-100 transition shadow-sm cursor-pointer" title="Notifikasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                <template x-if="unreadCount > 0">
                                    <span>
                                        <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                        </span>
                                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-md shadow-rose-500/30" x-text="unreadCount"></span>
                                    </span>
                                </template>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="absolute right-0 mt-2.5 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-[#004AAD]/10 text-[#004AAD] flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        </div>
                                        <h3 class="text-xs font-bold text-[#0B1B3D]">Notifikasi Lamaran &amp; Karier</h3>
                                    </div>
                                    <template x-if="unreadCount > 0">
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full" x-text="unreadCount + ' Baru'"></span>
                                    </template>
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                    @forelse($studentRecentNotifs as $notification)
                                        @php
                                            $isUnread = is_null($notification->read_at);
                                            $status = $notification->data['status'] ?? null;
                                        @endphp
                                        <a href="{{ route('notifications.go', $notification->id) }}" class="block p-3.5 hover:bg-slate-50/80 transition relative {{ $isUnread ? 'bg-blue-50/40' : '' }}">
                                            <div class="flex items-start gap-2.5">
                                                <div class="mt-0.5 shrink-0">
                                                    @if(($notification->data['type'] ?? '') === 'admin_message')
                                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-[#004AAD] flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                        </span>
                                                    @elseif($status === 'Diterima')
                                                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        </span>
                                                    @elseif($status === 'Wawancara')
                                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        </span>
                                                    @elseif($status === 'Ditolak')
                                                        <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </span>
                                                    @else
                                                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <p class="text-[12px] font-bold text-[#0B1B3D] truncate">
                                                            {{ $notification->data['message'] ?? 'Pembaruan lamaran kerja' }}
                                                        </p>
                                                        @if($isUnread)
                                                            <span class="w-2 h-2 rounded-full bg-[#004AAD] shrink-0" title="Belum dibaca"></span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                        @if(($notification->data['type'] ?? '') === 'admin_message')
                                                            {{ $notification->data['content'] ?? 'Pesan resmi dari Administrator Kampus' }}
                                                        @else
                                                            {{ $notification->data['job_title'] ?? 'Lamaran Anda' }} &bull; <span class="font-semibold text-slate-700">{{ $notification->data['mitra_name'] ?? 'Mitra Industri' }}</span>
                                                        @endif
                                                    </p>
                                                    @if(isset($notification->data['interview_date']) && $notification->data['interview_date'])
                                                        <p class="text-[10px] font-bold text-purple-700 mt-1 flex items-center gap-1 bg-purple-50 px-2 py-0.5 rounded border border-purple-100 w-fit">
                                                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($notification->data['interview_date'])->translatedFormat('d M Y, H:i') }} WIB
                                                        </p>
                                                    @endif
                                                    <p class="text-[10px] text-slate-400 mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-6 text-center text-slate-400">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 mx-auto mb-2 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            </div>
                                            <p class="text-xs font-semibold text-slate-600">Belum ada notifikasi</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Semua pembaruan status lamaran akan muncul di sini</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="{{ route('applications.index') }}" class="text-[11px] font-bold text-[#004AAD] hover:underline px-2 py-1">
                                        Lihat Semua Lamaran &rarr;
                                    </a>
                                    @if($studentUnreadCount > 0)
                                        <form action="{{ route('notifications.readAll') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 px-2 py-1">
                                                Tandai Semua Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- User Profile pill link -->
                        <a href="{{ route('profile.edit') }}" class="hidden sm:flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl bg-blue-50/70 border border-blue-200 hover:bg-blue-100/70 transition">
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-6 h-6 rounded-lg object-cover">
                            <span class="text-xs font-bold text-[#0B1B3D] max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                        </a>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto px-4 py-8 sm:px-8 lg:px-10 scrollbar-thin scrollbar-thumb-slate-800">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>









