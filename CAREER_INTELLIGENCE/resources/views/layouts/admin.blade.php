<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Career Intel') }} - Portal Administrator</title>
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
                <!-- Logo & Brand -->
                <div class="flex items-center px-5 py-6 border-b border-slate-200/80">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 bg-[#004AAD] text-white rounded-xl flex items-center justify-center shadow-md shadow-[#004AAD]/20 group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col leading-none">
                            <span class="text-[13px] font-black tracking-tight text-[#004AAD] group-hover:opacity-90 transition whitespace-nowrap">
                                CAREER <span class="text-[#FF44B7]">INTELLIGENCE</span>
                            </span>
                            <span class="text-[9.5px] font-semibold text-slate-400 uppercase tracking-wider mt-1">Administrator Kampus</span>
                        </div>
                    </a>
                </div>
                <!-- Navigation Links -->
                <nav class="flex-1 overflow-y-auto py-5 px-4 space-y-1.5 scrollbar-thin scrollbar-thumb-slate-300">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard Utama
                    </a>
                    <a href="{{ route('admin.leaderboards') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.leaderboards') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.leaderboards') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Peringkat & Statistik
                    </a>
                    @php
                        $sidebarPendingResets = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
                    @endphp
                    <a href="{{ route('admin.students') }}" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('admin.students') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.students') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Data Mahasiswa
                        </div>
                        @if($sidebarPendingResets > 0)
                            <span class="{{ request()->routeIs('admin.students') ? 'bg-white/20 text-white' : 'bg-pink-100 text-[#FF44B7]' }} text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $sidebarPendingResets }} Reset</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.mitra') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.mitra') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.mitra') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Mitra Industri
                    </a>
                    <a href="{{ route('admin.occupations') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.occupations') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.occupations') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Standar Profesi & Benchmarking
                    </a>
                    <a href="{{ route('admin.assessments') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.assessments') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.assessments') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Pengawasan Asesmen AI
                    </a>
                    <a href="{{ route('admin.approvals') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.approvals') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.approvals') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Persetujuan Mitra
                        @php
                            $pendingCount = \App\Models\JobListing::where('status', 'Menunggu Persetujuan')->count() + \App\Models\PendingProfileUpdate::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto {{ request()->routeIs('admin.approvals') ? 'bg-white/20 text-white' : 'bg-pink-100 text-[#FF44B7]' }} text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.reports.print') }}" target="_blank" class="flex items-center px-4 py-3 text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 text-slate-500 group-hover:text-[#FF44B7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Laporan Kampus
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.activity-logs') ? 'bg-[#FF44B7] text-white font-extrabold shadow-md shadow-[#FF44B7]/25' : 'text-slate-600 hover:text-[#FF44B7] hover:bg-pink-50/70 font-semibold' }} rounded-xl group text-xs sm:text-[13px] transition">
                        <svg class="w-4 h-4 mr-3 {{ request()->routeIs('admin.activity-logs') ? 'text-white' : 'text-slate-400 group-hover:text-[#FF44B7]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Log Aktivitas
                    </a>
                    <div class="pt-4 border-t border-slate-200/80 my-2">
                        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 font-bold">Akses Lintas Portal</p>
                    </div>
                    <a href="/dashboard?preview=1" target="_blank" class="flex items-center px-4 py-2.5 text-slate-600 hover:text-[#004AAD] hover:bg-blue-50/70 font-semibold rounded-xl group text-xs transition">
                        <svg class="w-4 h-4 mr-3 text-slate-500 group-hover:text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Pratinjau Portal Pengguna
                    </a>
                    <a href="/mitra/dashboard" target="_blank" class="flex items-center px-4 py-2.5 text-slate-600 hover:text-[#004AAD] hover:bg-blue-50/70 font-semibold rounded-xl group text-xs transition">
                        <svg class="w-4 h-4 mr-3 text-slate-500 group-hover:text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Pratinjau Portal Mitra
                    </a>
                    <a href="/" target="_blank" class="flex items-center px-4 py-2.5 text-slate-600 hover:text-[#004AAD] hover:bg-blue-50/70 font-semibold rounded-xl group text-xs transition">
                        <svg class="w-4 h-4 mr-3 text-slate-500 group-hover:text-[#004AAD]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Lihat Landing Page
                    </a>
                </nav>
                <!-- Admin User Info & Logout Button -->
                <div class="p-4 border-t border-slate-200 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-[#004AAD] flex items-center justify-center font-black text-white text-xs shrink-0 shadow-md shadow-[#004AAD]/20">
                                AD
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-bold text-[#0B1B3D] truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Keluar dari Portal" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
                <!-- Topbar Header -->
                <header class="min-h-20 h-auto py-3 bg-white/95 border-b border-slate-200 px-6 sm:px-8 flex items-center justify-between shrink-0 backdrop-blur relative z-30">
                    <div class="flex items-center gap-4 flex-1 min-w-0 mr-4">
                        <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-500 hover:text-[#0B1B3D] hover:bg-slate-100 rounded-lg transition shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="flex-1 min-w-0">
                            @yield('header')
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @php
                            $pendingResets = \App\Models\PasswordResetRequest::where('status', 'pending')->with('user')->get();
                            $unreadAdminNotifs = Auth::user()->unreadNotifications;
                            $adminRecentNotifs = Auth::user()->notifications()->take(6)->get();
                            $adminUnreadCount = $unreadAdminNotifs->count() + $pendingResets->count();
                        @endphp
                        <!-- Notification Dropdown -->
                        <div class="relative" x-data="{ open: false, unreadCount: {{ $adminUnreadCount }}, isViewed: false }">
                            <button @click="open = !open; if(open && unreadCount > 0) { fetch('{{ route('notifications.readAll') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }); unreadCount = 0; isViewed = true; window.dispatchEvent(new CustomEvent('notifications-read')); }" @click.away="open = false" class="relative p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:text-[#004AAD] hover:border-[#004AAD]/30 hover:bg-slate-100 transition shadow-sm cursor-pointer" title="Pusat Notifikasi Admin">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                <template x-if="unreadCount > 0">
                                    <span>
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-md shadow-rose-500/30" x-text="unreadCount">
                                        </span>
                                    </span>
                                </template>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="absolute right-0 mt-2.5 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 text-[#0B1B3D]">
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-[#004AAD]/10 text-[#004AAD] flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        </div>
                                        <h3 class="text-xs font-bold text-[#0B1B3D]">Notifikasi Administrator</h3>
                                    </div>
                                    @if($adminUnreadCount > 0)
                                        <template x-if="unreadCount > 0">
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full" x-text="unreadCount + ' Baru'">
                                                {{ $adminUnreadCount }} Baru
                                            </span>
                                        </template>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                    <!-- Pengajuan dari Mitra (DB Notifications) -->
                                    @forelse($adminRecentNotifs as $notification)
                                        @php
                                            $isUnread = is_null($notification->read_at);
                                            $notifType = $notification->data['type'] ?? 'approval';
                                        @endphp
                                        <a href="{{ route('notifications.go', $notification->id) }}" class="block p-3.5 hover:bg-slate-50/80 transition relative" :class="isViewed ? '' : '{{ $isUnread ? 'bg-blue-50/40' : '' }}'">
                                            <div class="flex items-start gap-2.5">
                                                <div class="mt-0.5 shrink-0">
                                                    @if($notifType === 'job_approval')
                                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-[#004AAD] flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                        </span>
                                                    @else
                                                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <p class="text-[12px] font-bold text-[#0B1B3D] truncate">
                                                            {{ $notification->data['title'] ?? 'Pengajuan Mitra' }}
                                                        </p>
                                                        @if($isUnread)
                                                            <span x-show="!isViewed" class="w-2 h-2 rounded-full bg-[#004AAD] shrink-0" title="Belum dibaca"></span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2">
                                                        {{ $notification->data['message'] ?? 'Ada pengajuan baru dari mitra industri.' }}
                                                    </p>
                                                    <div class="flex items-center justify-between mt-1 text-[10px]">
                                                        <span class="text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                                                        <span class="font-bold text-[#004AAD]">Tinjau &rarr;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                    @endforelse
                                    <!-- Permintaan Reset Sandi -->
                                    @foreach($pendingResets as $reset)
                                        <div class="p-3.5 hover:bg-slate-50/80 transition relative bg-amber-50/30">
                                            <div class="flex items-start gap-2.5">
                                                <div class="mt-0.5 shrink-0">
                                                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <p class="text-[12px] font-bold text-[#0B1B3D] truncate">
                                                            Permintaan Reset Sandi
                                                        </p>
                                                        <span class="px-1.5 py-0.2 text-[9px] font-extrabold bg-amber-100 text-amber-700 rounded border border-amber-200">
                                                            Mahasiswa
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-700 mt-0.5 font-medium truncate">
                                                        {{ $reset->user->name ?? 'Mahasiswa' }} ({{ $reset->user->nim ?? '-' }})
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 truncate">
                                                        {{ $reset->user->email ?? '-' }}
                                                    </p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-[10px] text-slate-400">{{ $reset->created_at->diffForHumans() }}</span>
                                                        <a href="{{ route('admin.students', ['search' => $reset->user->nim ?? $reset->user->email]) }}" class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 bg-[#004AAD]/10 text-[#004AAD] hover:bg-[#004AAD]/20 rounded-lg transition">
                                                            Proses Reset &rarr;
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($adminRecentNotifs->isEmpty() && $pendingResets->isEmpty())
                                        <div class="p-6 text-center text-slate-400">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 mx-auto mb-2 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            </div>
                                            <p class="text-xs font-semibold text-slate-600">Semua Terkendali!</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Tidak ada permintaan reset atau pengajuan tertunda saat ini.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <a href="{{ route('admin.approvals') }}" class="text-[11px] font-bold text-[#004AAD] hover:underline px-2 py-1">
                                        Ke Menu Persetujuan &rarr;
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
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-[#004AAD] font-semibold">
                            <span class="w-2 h-2 rounded-full bg-[#004AAD] animate-pulse"></span>
                            <span>Status: Administrator Terhubung</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </header>
                <!-- Page Body -->
                <main class="flex-1 overflow-y-auto p-6 sm:p-8 space-y-6">
                    <!-- Flash Messages -->
                    @if (session('status'))
                        <div class="p-4 rounded-xl bg-[#004AAD]/15 border border-[#004AAD]/30 text-emerald-700 text-xs sm:text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                            <div class="flex items-center gap-2.5 mb-2 font-bold">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Terdapat kesalahan pada isian form Anda:</span>
                            </div>
                            <ul class="list-disc list-inside ml-7">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
