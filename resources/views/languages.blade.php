@extends('layouts.layout')

@section('title', 'Browse by Language — CineMovie')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-8 max-w-7xl mx-auto select-none">
    
    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <span>Browse by Language</span>
            <span class="text-xl">🌐</span>
        </h1>
        <p class="text-slate-400 text-sm">Discover and filter global cinema databases by original release language.</p>
    </div>

    <!-- Languages Scroll/Chips List -->
    <div class="space-y-3">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Select Language</h3>
        <div id="langs-container" class="flex gap-3 overflow-x-auto no-scrollbar py-2"></div>
    </div>

    <!-- Type Selection & Filter Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#1E1E2E] pb-3">
        <div class="flex gap-4">
            <button onclick="switchType('all')" id="type-all" class="type-btn pb-1 text-sm font-extrabold border-b-2 border-emerald-500 text-white transition-all">All content</button>
            <button onclick="switchType('movie')" id="type-movie" class="type-btn pb-1 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">Movies</button>
            <button onclick="switchType('tv')" id="type-tv" class="type-btn pb-1 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">TV Shows</button>
        </div>
        <span id="results-count" class="text-xs font-bold text-slate-500">Popular matching titles</span>
    </div>

    <!-- Results Grid -->
    <div class="space-y-4">
        <div id="results-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
            <!-- Skeletons -->
            @for ($i = 0; $i < 10; $i++)
            <div class="aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
            @endfor
        </div>
    </div>

</div>

<script>
    const languages = [
        { code: 'en', label: 'English', flag: '🇺🇸', native: 'English' },
        { code: 'hi', label: 'Hindi', flag: '🇮🇳', native: 'हिन्दी' },
        { code: 'pa', label: 'Punjabi', flag: '🎵', native: 'ਪੰਜਾਬੀ' },
        { code: 'ko', label: 'Korean', flag: '🇰🇷', native: '한국어' },
        { code: 'ja', label: 'Japanese', flag: '🇯🇵', native: '日本語' },
        { code: 'zh', label: 'Chinese', flag: '🇨🇳', native: '中文' },
        { code: 'es', label: 'Spanish', flag: '🇪🇸', native: 'Español' },
        { code: 'fr', label: 'French', flag: '🇫🇷', native: 'Français' },
        { code: 'tr', label: 'Turkish', flag: '🇹🇷', native: 'Türkçe' },
        { code: 'ar', label: 'Arabic', flag: '🇸🇦', native: 'العربية' },
        { code: 'ta', label: 'Tamil', flag: '🎞️', native: 'தமிழ்' },
        { code: 'te', label: 'Telugu', flag: '🎥', native: 'తెలుగు' },
        { code: 'ml', label: 'Malayalam', flag: '🌴', native: 'മലയാളം' },
        { code: 'de', label: 'German', flag: '🇩🇪', native: 'Deutsch' },
        { code: 'pt', label: 'Portuguese', flag: '🇵🇹', native: 'Português' },
        { code: 'ru', label: 'Russian', flag: '🇷🇺', native: 'Русский' },
        { code: 'th', label: 'Thai', flag: '🇹🇭', native: 'ภาษาไทย' },
        { code: 'id', label: 'Indonesian', flag: '🇮🇩', native: 'Bahasa' },
        { code: 'ur', label: 'Urdu', flag: '🇵🇰', native: 'اردو' },
        { code: 'bn', label: 'Bengali', flag: '🇧🇩', native: 'বাংলা' }
    ];

    let activeLangIndex = 0;
    let activeType = 'all'; // 'all' | 'movie' | 'tv'

    document.addEventListener("DOMContentLoaded", () => {
        renderLangChips();
        loadLanguageData();
    });

    function renderLangChips() {
        const container = document.getElementById("langs-container");
        container.innerHTML = languages.map((l, idx) => `
            <button onclick="switchLanguage(${idx})" 
                    class="flex items-center gap-2 px-5 py-3 rounded-full text-xs font-bold border transition duration-250 select-none whitespace-nowrap
                    ${idx === activeLangIndex 
                        ? 'bg-emerald-600 border-emerald-500 text-white shadow-lg shadow-emerald-500/20' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}"
                    id="lang-chip-${idx}">
                <span>${l.flag}</span>
                <span>${l.label}</span>
                <span class="text-[10px] opacity-60 font-medium">(${l.native})</span>
            </button>
        `).join('');
    }

    async function loadLanguageData() {
        setLoadingState();
        
        const lang = languages[activeLangIndex];
        const grid = document.getElementById("results-grid");
        
        try {
            let items = [];
            const queryParams = new URLSearchParams({
                with_original_language: lang.code,
                sort_by: 'popularity.desc',
                include_adult: 'false',
                page: '1'
            });

            if (activeType === 'all') {
                // Fetch both movies and tv shows, then interleave
                const [moviesRes, tvRes] = await Promise.all([
                    fetch(`/api/tmdb/discover/movie?${queryParams}`).then(r => r.json()),
                    fetch(`/api/tmdb/discover/tv?${queryParams}`).then(r => r.json())
                ]);

                const movies = parseResults(moviesRes.results || [], 'movie');
                const tv = parseResults(tvRes.results || [], 'tv');
                
                // Interleave results
                const maxLength = Math.max(movies.length, tv.length);
                for (let i = 0; i < maxLength; i++) {
                    if (i < movies.length) items.push(movies[i]);
                    if (i < tv.length) items.push(tv[i]);
                }
            } else {
                // Fetch single type
                const res = await fetch(`/api/tmdb/discover/${activeType}?${queryParams}`).then(r => r.json());
                items = parseResults(res.results || [], activeType);
            }

            renderResults(items);

        } catch (err) {
            console.error("Error loading language results:", err);
            grid.innerHTML = `<div class="text-rose-500 text-sm col-span-full py-12 text-center">Failed to load content for selected language.</div>`;
        }
    }

    function parseResults(results, mediaType) {
        return results.map(r => {
            const dateRaw = r.release_date || r.first_air_date || '';
            const year = dateRaw.length >= 4 ? dateRaw.substring(0, 4) : '';
            return {
                id: r.id,
                title: r.title || r.name || 'Unknown',
                type: mediaType,
                posterUrl: r.poster_path ? `https://image.tmdb.org/t/p/w342${r.poster_path}` : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image',
                rating: r.vote_average ? parseFloat(r.vote_average.toFixed(1)) : 0.0,
                year: year
            };
        });
    }

    function renderResults(items) {
        const grid = document.getElementById("results-grid");
        if (items.length === 0) {
            grid.innerHTML = `<div class="text-slate-500 text-sm col-span-full py-12 text-center font-bold">No movies or TV shows found in this language</div>`;
            return;
        }

        grid.innerHTML = items.map(item => `
            <a href="/details/${item.type}/${item.id}" class="group flex flex-col gap-2 relative animate-fadeIn">
                <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-emerald-500/10">
                    <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 px-2 py-1 glass rounded-lg flex items-center gap-1 text-[11px] font-extrabold text-amber-400 select-none">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        <span>${item.rating}</span>
                    </div>
                </div>
                <div class="px-1 space-y-0.5 leading-tight">
                    <h3 class="text-sm font-bold text-white group-hover:text-emerald-400 transition truncate w-full">${item.title}</h3>
                    <div class="flex justify-between text-xs text-slate-450 mt-1">
                        <span>${item.year}</span>
                        <span class="uppercase text-[9px] font-extrabold bg-[#1E1E2E] px-1.5 py-0.5 rounded border border-white/5">${item.type}</span>
                    </div>
                </div>
            </a>
        `).join('');
    }

    function switchLanguage(idx) {
        if (idx === activeLangIndex) return;

        // Toggle Chip highlights
        document.getElementById(`lang-chip-${activeLangIndex}`).className = "flex items-center gap-2 px-5 py-3 rounded-full text-xs font-bold border transition duration-250 select-none whitespace-nowrap bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10";
        document.getElementById(`lang-chip-${idx}`).className = "flex items-center gap-2 px-5 py-3 rounded-full text-xs font-bold border transition duration-250 select-none whitespace-nowrap bg-emerald-600 border-emerald-500 text-white shadow-lg shadow-emerald-500/20";
        
        activeLangIndex = idx;
        loadLanguageData();
    }

    function switchType(type) {
        if (type === activeType) return;

        // Toggle buttons highlight
        document.querySelectorAll(".type-btn").forEach(btn => {
            btn.className = "type-btn pb-1 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
        });
        document.getElementById(`type-${type}`).className = "type-btn pb-1 text-sm font-extrabold border-b-2 border-emerald-500 text-white transition-all";

        activeType = type;
        loadLanguageData();
    }

    function setLoadingState() {
        const grid = document.getElementById("results-grid");
        grid.innerHTML = Array(10).fill(0).map(() => `
            <div class="aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
        `).join('');
    }
</script>
@endsection
