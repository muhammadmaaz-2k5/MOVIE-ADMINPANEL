<!DOCTYPE html>
<html lang="en" class="h-full bg-[#0B0B14]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ENGORA — Stream Movies & TV Shows')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-['Outfit',sans-serif] text-slate-100 antialiased overflow-x-hidden">

    <div class="min-h-full flex flex-col md:flex-row bg-[#0B0B14]">
        <!-- Desktop Sidebar (Hidden on mobile) -->
        <aside class="hidden md:flex flex-col w-64 fixed inset-y-0 left-0 bg-[#121220] border-r border-[#1E1E2E] px-4 py-6 z-30 overflow-y-auto scrollbar-thin">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 mb-8 select-none">
                <span class="text-2xl font-black bg-gradient-to-r from-violet-500 to-fuchsia-500 bg-clip-text text-transparent tracking-wider">ENGORA</span>
                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-violet-600/30 text-violet-400 rounded-md border border-violet-500/20">PRO</span>
            </a>

            <!-- Navigation Links -->
            <nav class="flex-1 space-y-1.5">
                <a href="{{ route('home') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('home') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Home</span>
                </a>

                <a href="{{ route('movies') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('movies') ? 'bg-[#0984E3]/20 text-[#0984E3] border border-[#0984E3]/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    <span>Movies</span>
                </a>

                <a href="{{ route('tv-shows') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('tv-shows') ? 'bg-[#00B894]/20 text-[#00B894] border border-[#00B894]/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h8m-4-4v4M3 4h18a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1z" />
                    </svg>
                    <span>TV Shows</span>
                </a>

                <a href="{{ route('anime') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('anime') ? 'bg-[#FF6B9D]/20 text-[#FF6B9D] border border-[#FF6B9D]/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span>Anime</span>
                </a>

                <a href="{{ route('search') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('search') ? 'bg-[#FDAA07]/20 text-[#FDAA07] border border-[#FDAA07]/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                </a>

                <a href="{{ route('languages') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('languages') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5c-.313 1.565-.953 3.051-1.895 4.385m1.89-4.385a18.004 18.004 0 011.047 3.5m-5.454 4.885a17.973 17.973 0 01-1.047-3.5m0 0a17.962 17.962 0 00-1.895-4.384m0 0H12.75" />
                    </svg>
                    <span>Languages</span>
                </a>
            </nav>

            <!-- Bottom utility -->
            <div class="mt-auto space-y-1.5">
                <a href="{{ route('admin.movie-manager') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.movie-manager') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    <span>Movie Manager</span>
                </a>
                <a href="{{ route('admin.tv-manager') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.tv-manager') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>TV Shows Manager</span>
                </a>
                <a href="{{ route('admin.anime-manager') }}" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.anime-manager') ? 'bg-[#FF6B9D]/20 text-[#FF6B9D] border border-[#FF6B9D]/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14l6-6-6-6v12zM16 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L16 14M4 18h8a2 2 0 002-2V8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Anime Manager</span>
                </a>
                <a href="{{ route('admin.download-manager') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.download-manager') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Manager</span>
                </a>
                <a href="{{ route('admin.home-section-manager') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.home-section-manager') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Home Sections</span>
                </a>
                <a href="{{ route('admin.midnight-manager') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.midnight-manager') ? 'bg-gradient-to-r from-[#FF1A75]/20 to-[#9D4EDD]/20 text-[#FF1A75] border border-[#FF1A75]/40 shadow-lg shadow-[#FF1A75]/10' : 'text-slate-400 hover:text-[#FF1A75] hover:bg-[#1E1E2E]/50' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#FF1A75]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span>Midnight 18+</span>
                    </div>
                    <span class="px-1.5 py-0.5 text-[9px] font-black tracking-wider uppercase rounded bg-[#FF1A75]/20 text-[#FF1A75] border border-[#FF1A75]/30">VIP</span>
                </a>
                <a href="{{ route('admin.video-servers') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.video-servers') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 012-2h10a2 2 0 012 2m-14 0a2 2 0 002 2h10a2 2 0 002-2M12 5v14" />
                    </svg>
                    <span>Video Servers</span>
                </a>
                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.settings') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.667 2.153-1.667 2.58 0l.399 1.716a2.5 2.5 0 002.34.177l1.717-.399c1.667-.426 1.667-2.153 0-2.58l-1.717-.399a2.5 2.5 0 00-2.34.177l-.399 1.717zm-5.16 9.117c.426-1.667 2.153-1.667 2.58 0l.399 1.717a2.5 2.5 0 002.34.176l1.717-.399c1.667-.426 1.667-2.153 0-2.58l-1.717-.399a2.5 2.5 0 00-2.34.176l-.399 1.717z"/>
                    </svg>
                    <span>Settings</span>
                </a>
                <a href="{{ route('admin.notification-manager') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.notification-manager') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Notifications</span>
                </a>

                @auth
                    <div class="pt-4 mt-3 border-t border-white/10 space-y-2">
                        <div class="flex items-center gap-3 px-3 py-2 rounded-2xl bg-white/[0.04] border border-white/5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#E50914] to-[#991218] flex items-center justify-center font-black text-white text-xs shadow-lg shadow-[#E50914]/20">
                                {{ strtoupper(substr(Auth::user()->username ?? Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">@<span>{{ Auth::user()->username }}</span></p>
                            </div>
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition duration-150">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-4 mt-3 border-t border-white/10">
                        <a href="{{ route('admin.login') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition duration-150">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Admin Login</span>
                        </a>
                    </div>
                @endauth
            </div>
        </aside>


        <!-- Main View Area -->
        <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
            <!-- Mobile Top Bar (Hidden on desktop) -->
            <header class="md:hidden sticky top-0 flex items-center justify-between px-6 py-4 bg-[#0B0B14]/80 backdrop-blur-md border-b border-[#1E1E2E] z-40 select-none">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-xl font-black bg-gradient-to-r from-violet-500 to-fuchsia-500 bg-clip-text text-transparent tracking-wider">ENGORA</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('search') }}" class="text-slate-300 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>
                    @auth
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Logout" class="text-rose-400 hover:text-white p-1 rounded-lg bg-rose-500/10 border border-rose-500/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- Main Content Slot -->
            <main class="flex-1 pb-24 md:pb-6">
                @yield('content')
            </main>
        </div>

        <!-- Mobile Bottom Nav Bar (Hidden on desktop) -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-[#121220]/90 backdrop-blur-lg border-t border-[#1E1E2E]/80 flex items-center justify-around py-3 px-2 z-50 select-none text-[10px]">
            <a href="{{ route('home') }}" 
               class="flex flex-col items-center gap-1 font-semibold transition {{ request()->routeIs('home') ? 'text-violet-400' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Home</span>
            </a>
            
            <a href="{{ route('movies') }}" 
               class="flex flex-col items-center gap-1 font-semibold transition {{ request()->routeIs('movies') ? 'text-[#0984E3]' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
                <span>Movies</span>
            </a>

            <a href="{{ route('tv-shows') }}" 
               class="flex flex-col items-center gap-1 font-semibold transition {{ request()->routeIs('tv-shows') ? 'text-[#00B894]' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h8m-4-4v4M3 4h18a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1z" />
                </svg>
                <span>TV Shows</span>
            </a>

            <a href="{{ route('anime') }}" 
               class="flex flex-col items-center gap-1 font-semibold transition {{ request()->routeIs('anime') ? 'text-[#FF6B9D]' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Anime</span>
            </a>

            <a href="{{ route('languages') }}" 
               class="flex flex-col items-center gap-1 font-semibold transition {{ request()->routeIs('languages') ? 'text-emerald-400' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5c-.313 1.565-.953 3.051-1.895 4.385m1.89-4.385a18.004 18.004 0 011.047 3.5m-5.454 4.885a17.973 17.973 0 01-1.047-3.5m0 0a17.962 17.962 0 00-1.895-4.384m0 0H12.75" />
                </svg>
                <span>Languages</span>
            </a>

            <a href="{{ route('search') }}" 
               class="flex flex-col items-center gap-1 font-semibold transition {{ request()->routeIs('search') ? 'text-[#FDAA07]' : 'text-slate-400 hover:text-slate-200' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Search</span>
            </a>
        </nav>
    </div>

</body>
</html>
