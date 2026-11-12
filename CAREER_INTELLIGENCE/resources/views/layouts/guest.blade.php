<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Career Intel') }} - Portal Autentikasi</title>

        <!-- Modern Typography -->
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
                                500: '#00c99f',
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
    <body class="font-sans antialiased bg-[#F8FAFC] text-slate-800 min-h-full flex flex-col justify-between selection:bg-[#004AAD] selection:text-white">

        <!-- Top Header Navigation -->
        <header class="w-full py-6 px-4 sm:px-8 border-b border-slate-200 backdrop-blur-sm bg-white/80">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Brand -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-[#004AAD] rounded-xl flex items-center justify-center shadow-md shadow-[#004AAD]/20 group-hover:scale-105 transition-transform shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-base sm:text-lg font-black tracking-tight text-[#004AAD] group-hover:opacity-90 transition">CAREER <span class="text-[#FF44B7]">INTELLIGENCE</span></span>
                </a>

                <!-- Back to Home link -->
                <a href="/" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#004AAD] transition bg-blue-50 text-[#004AAD] hover:bg-blue-100 hover:text-[#003B8B] px-3.5 py-2 rounded-xl border border-blue-200 shadow-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </header>

        <!-- Main Content (Centered Form Slot) -->
        <main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <!-- Minimal Footer -->
        <footer class="w-full py-6 px-4 border-t border-slate-200 text-center text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Career Intelligence. Sinergi Kampus & Mitra Industri.</p>
        </footer>

    </body>
</html>





