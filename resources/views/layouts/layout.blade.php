<!DOCTYPE html>
<html lang="en" class="h-full bg-[#0B0B14]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CineMovie — Stream Movies & TV Shows')</title>
    
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
                <span class="text-2xl font-black bg-gradient-to-r from-violet-500 to-fuchsia-500 bg-clip-text text-transparent tracking-wider">CINEMOVIE</span>
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
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.anime-manager') ? 'bg-violet-600/20 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-[#1E1E2E]/50' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14l6-6-6-6v12zM16 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L16 14M4 18h8a2 2 0 002-2V8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Anime Manager</span>
                </a>
            </div>
        </aside>


        <!-- Main View Area -->
        <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
            <!-- Mobile Top Bar (Hidden on desktop) -->
            <header class="md:hidden sticky top-0 flex items-center justify-between px-6 py-4 bg-[#0B0B14]/80 backdrop-blur-md border-b border-[#1E1E2E] z-40 select-none">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-xl font-black bg-gradient-to-r from-violet-500 to-fuchsia-500 bg-clip-text text-transparent tracking-wider">CINEMOVIE</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('search') }}" class="text-slate-300 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a>
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
