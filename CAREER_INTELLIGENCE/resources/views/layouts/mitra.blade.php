<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Career Intel') }} - Portal Mitra Industri</title>

        <!-- Typography -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind & Scripts -->
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
                                card: '#1e293b',
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
            <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 text-[#0B1B3D] transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col h-screen shrink-0">
                <!-- Logo -->
                <div class="flex items-center px-5 py-6 border-b border-slate-200/80">
                    <a href="{{ route('mitra.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 bg-[#004AAD] text-white rounded-xl flex items-center justify-center shadow-md shadow-[#004AAD]/20 group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col leading-none">
                            <span class="text-[13px] font-black tracking-tight text-[#004AAD] group-hover:opacity-90 transition whitespace-nowrap">
                                CAREER <span class="text-[#FF44B7]">INTELLIGENCE</span>
                            </span>
                            <span class="text-[9.5px] font-semibold text-slate-400 uppercase tracking-wider mt-1">Portal Mitra Industri</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-5 px-4 space-y-1.5 scrollbar-thin scrollbar-thumb-slate-800">
                    @php
                        if (!session()->has('mitra_viewed_accepted_at')) {
                            session(['mitra_viewed_accepted_at' => now()]);
                        }
                        if (!session()->has('mitra_viewed_rejected_at')) {
                            session(['mitra_viewed_rejected_at' => now()]);
                        }

                        $viewedAcceptedAt = session('mitra_viewed_accepted_at');
                        $viewedRejectedAt = session('mitra_viewed_rejected_at');

                        $navAcceptedCount = request()->routeIs('mitra.candidates.accepted')
                            ? 0
                            : \App\Models\JobApplication::whereHas('jobListing', fn($q) => $q->where('user_id', Auth::id()))
                                ->where('status', 'Diterima')
                                ->where('updated_at', '>', $viewedAcceptedAt)
                                ->count();

                        $navRejectedCount = request()->routeIs('mitra.candidates.rejected')
                            ? 0
                            : \App\Models\JobApplication::whereHas('jobListing', fn($q) => $q->where('user_id', Auth::id()))
                                ->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])
                                ->where('updated_at', '>', $viewedRejectedAt)
                                ->count();
                    @endphp

                    <a href="{{ route('mitra.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('mitra.dashboard') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard Utama
                    </a>
                    
                    <a href="{{ route('mitra.jobs') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('mitra.jobs') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.jobs') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Kelola Lowongan
                    </a>

                    @php
                        $sidebarUnreadAppsCount = Auth::user()->unreadNotifications
                            ->filter(fn($n) => ($n->data['type'] ?? '') === 'candidate_application' || isset($n->data['applicant_name']) || str_contains($n->type, 'NewJobApplicationNotification'))
                            ->count();
                    @endphp
                    <a href="{{ route('mitra.applications.index') }}" x-data="{ count: {{ $sidebarUnreadAppsCount }} }" @notifications-read.window="count = 0" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('mitra.applications.*') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.applications.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Kelola Pelamar
                        </div>
                        <template x-if="count > 0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('mitra.applications.*') ? 'bg-white/20 text-white' : 'bg-pink-100 text-[#FF44B7] border border-pink-200' }}" x-text="count + ' Baru'">
                            </span>
                        </template>
                    </a>

                    <a href="{{ route('mitra.candidates.accepted') }}" x-data="{ count: {{ $navAcceptedCount }} }" @notifications-read.window="count = 0" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('mitra.candidates.accepted') ? 'bg-emerald-600 text-white font-extrabold shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.candidates.accepted') ? 'text-white' : 'text-emerald-600 group-hover:text-emerald-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Kandidat Diterima
                        </div>
                        <template x-if="count > 0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200" x-text="count + ' Baru'">
                            </span>
                        </template>
                    </a>

                    <a href="{{ route('mitra.candidates.rejected') }}" x-data="{ count: {{ $navRejectedCount }} }" @notifications-read.window="count = 0" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('mitra.candidates.rejected') ? 'bg-rose-600 text-white font-extrabold shadow-md shadow-rose-600/20' : 'text-slate-600 hover:text-rose-700 hover:bg-rose-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.candidates.rejected') ? 'text-white' : 'text-rose-500 group-hover:text-rose-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Kandidat Ditolak
                        </div>
                        <template x-if="count > 0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200" x-text="count + ' Baru'">
                            </span>
                        </template>
                    </a>
                    
                    <a href="{{ route('mitra.candidates') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('mitra.candidates') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.candidates') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Cari Kandidat Mahasiswa
                    </a>

                    <div class="pt-3 border-t border-slate-200/60 my-2"></div>
                    
                    <a href="{{ route('mitra.profile') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('mitra.profile') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.profile') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Profil Perusahaan
                    </a>

                    <a href="{{ route('mitra.activity-logs') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('mitra.activity-logs') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('mitra.activity-logs') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Log Aktivitas
                    </a>
                    
                    <a href="/" target="_blank" class="flex items-center px-4 py-3 text-slate-600 hover:text-[#004AAD] hover:bg-blue-50/70 font-semibold rounded-xl group text-xs transition">
                        <svg class="w-4 h-4 mr-3 text-slate-400 group-hover:text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Lihat Landing Page
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

                <!-- User Info Footer -->
                <div class="p-4 border-t border-slate-200 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-[#004AAD] text-white font-black flex items-center justify-center shrink-0 shadow-md shadow-[#004AAD]/20 text-xs">
                                {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 2)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ Auth::user()->name ?? 'Perusahaan Mitra' }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
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

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <div class="bg-amber-500/10 border-b border-amber-500/30 px-4 py-2 sm:px-8 text-amber-700 text-xs flex items-center justify-between flex-wrap gap-2 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                            <span><strong>Mode Super Admin:</strong> Anda memiliki akses penuh dan sedang meninjau <strong>Portal Mitra Industri</strong>.</span>
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
                            <div class="w-7 h-7 rounded-lg bg-[#004AAD] text-white flex items-center justify-center lg:hidden shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-[#0B1B3D] hidden sm:inline">Pusat Rekrutmen Mitra Industri</span>
                            <span class="font-extrabold text-[#0B1B3D] text-xs sm:hidden">PORTAL MITRA</span>
                        </div>
                    </div>

                    <!-- Right Topbar: Quick Actions & Notification Bell -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('mitra.jobs') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#004AAD]/10 text-[#004AAD] hover:bg-[#004AAD]/20 text-xs font-bold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span>Buka Lowongan</span>
                        </a>

                        <!-- Notification Dropdown -->
                        @php
                            $mitraUnreadCount = Auth::user()->unreadNotifications->count();
                            $mitraRecentNotifs = Auth::user()->notifications()->take(6)->get();
                        @endphp
                        <div class="relative" x-data="{ open: false, unreadCount: {{ $mitraUnreadCount }}, isViewed: false }">
                            <button @click="open = !open; if(open && unreadCount > 0) { fetch('{{ route('notifications.readAll') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }); unreadCount = 0; isViewed = true; window.dispatchEvent(new CustomEvent('notifications-read')); }" class="relative p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:text-[#004AAD] hover:border-[#004AAD]/30 hover:bg-slate-100 transition shadow-sm cursor-pointer" title="Notifikasi Pelamar &amp; Akun">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                <template x-if="unreadCount > 0">
                                    <span>
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
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
                                        <h3 class="text-xs font-bold text-[#0B1B3D]">Notifikasi Mitra</h3>
                                    </div>
                                    @if($mitraUnreadCount > 0)
                                        <template x-if="unreadCount > 0">
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full" x-text="unreadCount + ' Baru'">
                                                {{ $mitraUnreadCount }} Baru
                                            </span>
                                        </template>
                                    @endif
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                    @forelse($mitraRecentNotifs as $notification)
                                        @php
                                            $isUnread = is_null($notification->read_at);
                                            $notifType = $notification->data['type'] ?? 'candidate_application';
                                        @endphp
                                        <a href="{{ route('notifications.go', $notification->id) }}" class="block p-3.5 hover:bg-slate-50/80 transition relative" :class="isViewed ? '' : '{{ $isUnread ? 'bg-blue-50/40' : '' }}'">
                                            <div class="flex items-start gap-2.5">
                                                <div class="mt-0.5 shrink-0">
                                                    @if(str_contains($notifType, 'candidate_response'))
                                                        @if(($notification->data['response'] ?? '') === 'Bersedia')
                                                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0" title="Kandidat Bersedia">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            </span>
                                                        @else
                                                            <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0" title="Kandidat Tidak Bersedia">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </span>
                                                        @endif
                                                    @elseif(str_contains($notifType, 'job_approval'))
                                                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </span>
                                                    @elseif(str_contains($notifType, 'profile_update'))
                                                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                        </span>
                                                    @elseif(str_contains($notifType, 'admin_message'))
                                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-[#004AAD] flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                        </span>
                                                    @else
                                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-[#004AAD] flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <p class="text-[12px] font-bold text-[#0B1B3D] truncate">
                                                            {{ $notification->data['message'] ?? 'Pemberitahuan baru' }}
                                                        </p>
                                                        @if($isUnread)
                                                            <span x-show="!isViewed" class="w-2 h-2 rounded-full bg-[#004AAD] shrink-0" title="Belum dibaca"></span>
                                                        @endif
                                                    </div>
                                                    @if(isset($notification->data['applicant_name']))
                                                        <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                                            <span class="text-[11px] text-slate-500">
                                                                Pelamar: <span class="font-semibold text-slate-700">{{ $notification->data['applicant_name'] }}</span>
                                                            </span>
                                                            @if(isset($notification->data['response']))
                                                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold {{ $notification->data['response'] === 'Bersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                                    {{ $notification->data['response'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if(isset($notification->data['job_title']))
                                                            <p class="text-[10.5px] text-slate-500 truncate mt-0.5">
                                                                Posisi: <span class="font-semibold text-slate-700">{{ $notification->data['job_title'] }}</span>
                                                            </p>
                                                        @endif
                                                    @elseif(str_contains($notifType, 'admin_message'))
                                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-[#004AAD] flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                        </span>
                                                    @elseif(isset($notification->data['job_title']))
                                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                            Lowongan: <span class="font-semibold text-slate-700">{{ $notification->data['job_title'] }}</span>
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
                                            <p class="text-xs font-semibold text-slate-600">Belum ada notifikasi pelamar</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Notifikasi pelamar baru atau persetujuan akun akan tampil di sini</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="{{ route('mitra.applications.index') }}" class="text-[11px] font-bold text-[#004AAD] hover:underline px-2 py-1">
                                        Kelola Pelamar &rarr;
                                    </a>
                                    <template x-if="unreadCount > 0">
                                        <form action="{{ route('notifications.readAll') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 px-2 py-1 cursor-pointer">
                                                Tandai Semua Dibaca
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Initials Badge -->
                        <div class="w-8 h-8 rounded-xl bg-[#004AAD] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 2)) }}
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto px-4 py-8 sm:px-8 lg:px-10 scrollbar-thin scrollbar-thumb-slate-800">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>
