@extends('layouts.layout')

@section('title', 'Smart Search — CineMovie')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-6 max-w-7xl mx-auto select-none" id="search-view">
    
    <!-- Page Header and Search input -->
    <div class="space-y-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>Smart Search</span>
                <span class="text-xl">🔍</span>
            </h1>
            <p class="text-slate-400 text-sm">Find movies, TV series, actors, directors, and more in our global catalog.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-450">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="search-input" placeholder="Search across millions of movies, TV shows, and people..." 
                       class="w-full bg-[#1E1E2E] border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-[14px] text-white focus:outline-none focus:border-[#FDAA07]/50 focus:ring-1 focus:ring-[#FDAA07]/50 transition-all placeholder-slate-500">
            </div>
            
            <!-- Filters button -->
            <button onclick="openFilters()" class="flex items-center justify-center gap-2 px-6 py-4 bg-[#1E1E2E] border border-white/5 hover:border-[#FDAA07]/20 rounded-2xl font-bold text-[14px] text-slate-350 hover:text-white transition select-none">
                <svg class="w-5 h-5 text-[#FDAA07]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span id="filter-btn-label">Filters</span>
            </button>
        </div>
    </div>

    <!-- Active Filters Strip -->
    <div id="active-filters-strip" class="flex flex-wrap items-center gap-2 bg-[#FDAA07]/10 border border-[#FDAA07]/10 p-3 rounded-2xl hidden">
        <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#FDAA07] mr-1">Active:</span>
        <div id="active-chips" class="flex flex-wrap gap-1.5"></div>
        <button onclick="resetFilters(true)" class="text-[11px] font-extrabold text-[#FDAA07] hover:text-[#e59805] transition ml-auto">Clear All</button>
    </div>

    <!-- Type Selection Horizontal Scroll -->
    <div class="flex gap-3 overflow-x-auto no-scrollbar py-1" id="type-selector-container">
        <!-- Loaded dynamically -->
    </div>

    <!-- Search Results / Popular Searches Area -->
    <div class="space-y-4">
        <h2 id="results-label" class="text-md font-bold text-slate-300">Popular Searches</h2>
        
        <!-- Results List -->
        <div id="results-list" class="space-y-3 max-w-4xl">
            <!-- Loader Skeletons by default -->
            @for ($i = 0; $i < 6; $i++)
            <div class="p-3 bg-[#1E1E2E]/30 rounded-2xl border border-white/5 flex gap-4 items-center">
                <div class="w-14 h-18 bg-slate-900 rounded-xl animate-pulse"></div>
                <div class="flex-1 space-y-2 py-1">
                    <div class="h-3.5 bg-slate-900 rounded w-1/3 animate-pulse"></div>
                    <div class="h-3 bg-slate-900 rounded w-1/4 animate-pulse"></div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-20 space-y-2 animate-fadeIn">
            <span class="text-3xl">🏜️</span>
            <h3 class="text-md font-bold text-white">No Results Found</h3>
            <p class="text-slate-500 text-xs">Try double-checking your spelling or adjusting filters.</p>
        </div>
    </div>

</div>

<!-- Filters Drawer -->
<div id="filter-drawer" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex justify-end hidden select-none" onclick="handleBackdropClick(event)">
    <div id="filter-drawer-content" class="w-full max-w-md bg-[#121220] h-full flex flex-col border-l border-white/5 transform translate-x-full transition-transform duration-300">
        <!-- Drawer Header -->
        <div class="px-6 py-5 border-b border-[#1E1E2E] flex justify-between items-center bg-[#1E1E2E]/20">
            <div>
                <h3 class="text-lg font-extrabold text-white">Refine Search</h3>
                <p class="text-slate-400 text-xs mt-0.5">Apply category, country, year, and language filters</p>
            </div>
            <button onclick="closeFilters()" class="p-2 rounded-xl bg-[#1E1E2E] border border-white/5 hover:bg-white/5 transition">
                <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Drawer Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 scrollbar-thin">
            <!-- Sort options -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Sort Results By</h4>
                <div class="flex flex-wrap gap-2" id="filter-sort-options"></div>
            </div>

            <!-- Genres -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Genre</h4>
                <div class="flex flex-wrap gap-2 max-h-[160px] overflow-y-auto pr-1 scrollbar-thin" id="filter-genres"></div>
            </div>

            <!-- Countries -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Origin Country</h4>
                <div class="flex flex-wrap gap-2 max-h-[160px] overflow-y-auto pr-1 scrollbar-thin" id="filter-countries"></div>
            </div>

            <!-- Languages -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Original Language</h4>
                <div class="flex flex-wrap gap-2 max-h-[160px] overflow-y-auto pr-1 scrollbar-thin" id="filter-languages"></div>
            </div>

            <!-- Years -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Release Year</h4>
                <div class="flex flex-wrap gap-2" id="filter-years"></div>
            </div>
        </div>

        <!-- Drawer Footer -->
        <div class="p-6 border-t border-[#1E1E2E] flex gap-3 bg-[#1E1E2E]/10">
            <button onclick="resetFilters(false)" class="flex-1 py-3.5 rounded-xl bg-[#1E1E2E] border border-white/5 text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition">
                Reset All
            </button>
            <button onclick="applyFilters()" class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-amber-600 to-[#FDAA07] text-white text-xs font-bold shadow-lg shadow-[#FDAA07]/20 hover:from-amber-500 hover:to-[#f0a30b] transition">
                Apply Filters
            </button>
        </div>
    </div>
</div>

<script>
    // Config items matching search_filters.dart
    const mediaTypes = [
        { label: 'All Results', value: 'all' },
        { label: '🎬 Movies', value: 'movie' },
        { label: '📺 TV Shows', value: 'tv' },
        { label: '👤 People', value: 'person' }
    ];

    const sortOptions = ['Hottest', 'Latest', 'Rating'];

    const genres = [
        'All', 'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 
        'Drama', 'Family', 'Fantasy', 'History', 'Horror', 'Music', 'Mystery', 
        'Romance', 'Sci-Fi', 'Thriller', 'War', 'Western'
    ];

    const genreIds = {
        'Action': 28, 'Adventure': 12, 'Animation': 16, 'Comedy': 35,
        'Crime': 80, 'Documentary': 99, 'Drama': 18, 'Family': 10751,
        'Fantasy': 14, 'History': 36, 'Horror': 27, 'Music': 10402,
        'Mystery': 9648, 'Romance': 10749, 'Sci-Fi': 878, 'Thriller': 53,
        'War': 10752, 'Western': 37
    };

    const countries = [
        'All', 'United States', 'United Kingdom', 'Korea', 'Japan', 'India', 
        'China', 'France', 'Germany', 'Spain', 'Italy', 'Nigeria', 'Pakistan', 'Turkey'
    ];

    const countryCodes = {
        'United States': 'US', 'United Kingdom': 'GB', 'Korea': 'KR',
        'Japan': 'JP', 'China': 'CN', 'France': 'FR', 'Germany': 'DE', 
        'India': 'IN', 'Italy': 'IT', 'Nigeria': 'NG', 'Pakistan': 'PK', 
        'Spain': 'ES', 'Turkey': 'TR'
    };

    const languages = [
        'All', 'English dub', 'French dub', 'Hindi dub', 'Bengali dub',
        'Urdu dub', 'Punjabi dub', 'Tamil dub', 'Telugu dub', 'Malayalam dub',
        'Arabic dub', 'Indonesian dub', 'Russian dub', 'Spanish dub'
    ];

    const languageCodes = {
        'English dub': 'en', 'French dub': 'fr', 'Hindi dub': 'hi',
        'Bengali dub': 'bn', 'Urdu dub': 'ur', 'Punjabi dub': 'pa',
        'Tamil dub': 'ta', 'Telugu dub': 'te', 'Malayalam dub': 'ml',
        'Arabic dub': 'ar', 'Indonesian dub': 'id', 'Russian dub': 'ru', 
        'Spanish dub': 'es'
    };

    const years = [
        'All', '2026', '2025', '2024', '2023', '2022', '2021', '2020',
        '2010s', '2000s', '1990s', '1980s'
    ];

    // State
    let selectedMediaType = 'all';
    let searchQuery = '';
    let searchTimeout;
    let resultsList = [];
    let isLoading = false;

    // Filters
    let activeFilters = {
        genre: 'All',
        country: 'All',
        year: 'All',
        language: 'All',
        sortBy: 'Hottest'
    };

    let tempFilters = { ...activeFilters };

    document.addEventListener("DOMContentLoaded", () => {
        renderMediaTypeTabs();
        triggerSearch();

        const input = document.getElementById("search-input");
        input.addEventListener("input", () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchQuery = input.value.trim();
                triggerSearch();
            }, 400);
        });

        // Set focus
        input.focus();
    });

    function renderMediaTypeTabs() {
        const container = document.getElementById("type-selector-container");
        container.innerHTML = mediaTypes.map(t => `
            <button onclick="switchMediaType('${t.value}')"
                    id="type-tab-${t.value}"
                    class="px-4 py-2 text-xs font-bold rounded-2xl border select-none transition duration-200 whitespace-nowrap
                    ${selectedMediaType === t.value 
                        ? 'bg-[#FDAA07] border-[#e59805] text-white shadow-md shadow-[#FDAA07]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${t.label}
            </button>
        `).join('');
    }

    function switchMediaType(type) {
        if (type === selectedMediaType) return;
        selectedMediaType = type;
        renderMediaTypeTabs();
        triggerSearch();
    }

    function buildQueryParams() {
        const params = {
            include_adult: 'false',
            page: '1'
        };

        if (searchQuery.length > 0) {
            params.query = searchQuery;
        }

        // Apply filters only if not Person tab
        if (selectedMediaType !== 'person') {
            // Genre
            if (activeFilters.genre !== 'All') {
                const gid = genreIds[activeFilters.genre];
                if (gid) params.with_genres = gid;
            }
            // Country
            if (activeFilters.country !== 'All') {
                const code = countryCodes[activeFilters.country];
                if (code) {
                    if (searchQuery.length > 0) {
                        params.region = code;
                    } else {
                        params.with_origin_country = code;
                    }
                }
            }
            // Language
            if (activeFilters.language !== 'All') {
                const code = languageCodes[activeFilters.language];
                if (code) {
                    if (searchQuery.length > 0) {
                        params.language = code;
                    } else {
                        params.with_original_language = code;
                    }
                }
            }
            // Year
            if (activeFilters.year !== 'All') {
                const yr = activeFilters.year;
                if (searchQuery.length > 0) {
                    if (!yr.endsWith('s')) params.year = yr;
                } else {
                    const dateField = selectedMediaType === 'tv' ? 'first_air_date' : 'release_date';
                    if (yr.endsWith('s')) {
                        const decade = parseInt(yr.replace('s', ''));
                        if (!isNaN(decade)) {
                            params[`${dateField}.gte`] = `${decade}-01-01`;
                            params[`${dateField}.lte`] = `${decade + 9}-12-31`;
                        }
                    } else {
                        params[`${dateField}.gte`] = `${yr}-01-01`;
                        params[`${dateField}.lte`] = `${yr}-12-31`;
                    }
                }
            }
            // Sort (only works in discover)
            if (searchQuery.length === 0) {
                switch (activeFilters.sortBy) {
                    case 'Rating':
                        params.sort_by = 'vote_average.desc';
                        params['vote_count.gte'] = 100;
                        break;
                    case 'Latest':
                        params.sort_by = selectedMediaType === 'tv' ? 'first_air_date.desc' : 'release_date.desc';
                        break;
                    case 'Hottest':
                    default:
                        params.sort_by = 'popularity.desc';
                        break;
                }
            }
        }

        return params;
    }

    async function triggerSearch() {
        setLoadingState();
        document.getElementById("empty-state").classList.add("hidden");

        const label = document.getElementById("results-label");
        const list = document.getElementById("results-list");
        const params = buildQueryParams();

        try {
            let fetchedItems = [];
            if (searchQuery.length > 0) {
                // Text Search Flow
                label.innerText = `Search Results for "${searchQuery}"`;
                
                let endpoint = '';
                if (selectedMediaType === 'all') {
                    endpoint = 'search/multi';
                } else {
                    endpoint = `search/${selectedMediaType}`;
                }

                // Parallel fetch TMDB + local custom movies database
                const customUrl = `/api/search/custom?query=${encodeURIComponent(searchQuery)}`;
                const [res, customRes] = await Promise.all([
                    fetch(`/api/tmdb/${endpoint}?${new URLSearchParams(params)}`).then(r => r.json()),
                    fetch(customUrl).then(r => r.json())
                ]);

                // Parse custom movies
                const parsedCustom = customRes
                    .filter(m => selectedMediaType === 'all' || m.type === selectedMediaType)
                    .map(m => ({
                        id: m.id,
                        title: m.title,
                        type: 'custom',
                        posterUrl: m.poster_path 
                            ? (m.poster_path.startsWith('http') ? m.poster_path : 'https://image.tmdb.org/t/p/w342' + m.poster_path)
                            : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image',
                        rating: m.rating || 0.0,
                        year: m.year || '',
                        language: m.language || 'Hindi',
                        isCustomMovie: true
                    }));

                const parsedTmdb = parseResults(res.results || [], selectedMediaType);
                fetchedItems = [...parsedCustom, ...parsedTmdb];
            } else {
                // Discover/Popular flow (default)
                label.innerText = "Popular Discoveries";
                
                let endpoint = '';
                if (selectedMediaType === 'all') {
                    // Fetch both movies and tv shows popularity-based and merge/interleave
                    const movieParams = { ...params, sort_by: 'popularity.desc' };
                    const tvParams = { ...params, sort_by: 'popularity.desc' };
                    
                    const [moviesRes, tvRes] = await Promise.all([
                        fetch(`/api/tmdb/discover/movie?${new URLSearchParams(movieParams)}`).then(r => r.json()),
                        fetch(`/api/tmdb/discover/tv?${new URLSearchParams(tvParams)}`).then(r => r.json())
                    ]);

                    const movies = parseResults(moviesRes.results || [], 'movie');
                    const tv = parseResults(tvRes.results || [], 'tv');
                    
                    // Interleave
                    const max = Math.max(movies.length, tv.length);
                    for (let i = 0; i < max; i++) {
                        if (i < movies.length) fetchedItems.push(movies[i]);
                        if (i < tv.length) fetchedItems.push(tv[i]);
                    }
                } else if (selectedMediaType === 'person') {
                    // Popular actors
                    const res = await fetch(`/api/tmdb/person/popular?page=1`).then(r => r.json());
                    fetchedItems = parseResults(res.results || [], 'person');
                } else {
                    const res = await fetch(`/api/tmdb/discover/${selectedMediaType}?${new URLSearchParams(params)}`).then(r => r.json());
                    fetchedItems = parseResults(res.results || [], selectedMediaType);
                }
            }

            // Client-side filtering/sorting for search results if applicable
            if (searchQuery.length > 0 && selectedMediaType !== 'person') {
                if (activeFilters.sortBy === 'Rating') {
                    fetchedItems.sort((a, b) => b.rating - a.rating);
                } else if (activeFilters.sortBy === 'Latest') {
                    fetchedItems.sort((a, b) => b.year.localeCompare(a.year));
                }
            }

            resultsList = fetchedItems;
            renderResultsList(resultsList);
            
            if (resultsList.length === 0) {
                document.getElementById("empty-state").classList.remove("hidden");
            }

        } catch (err) {
            console.error("Error running search query:", err);
            list.innerHTML = `<div class="text-rose-500 text-xs py-10 text-center font-bold">Failed to load searches. Check TMDB proxy status.</div>`;
        }
    }

    function parseResults(results, defaultType) {
        return results.map(r => {
            const mediaType = r.media_type || defaultType;
            const isPerson = mediaType === 'person';
            
            const title = r.title || r.name || 'Unknown';
            const posterPath = isPerson ? r.profile_path : r.poster_path;
            const posterUrl = posterPath 
                ? `https://image.tmdb.org/t/p/w342${posterPath}` 
                : (isPerson 
                    ? 'https://placehold.co/185x185/1E1E2E/FFF?text=' + encodeURIComponent(title.substring(0, 2))
                    : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image');
            
            const dateRaw = r.release_date || r.first_air_date || '';
            const year = dateRaw.length >= 4 ? dateRaw.substring(0, 4) : '';

            return {
                id: r.id,
                title: title,
                type: mediaType,
                posterUrl: posterUrl,
                rating: r.vote_average ? parseFloat(r.vote_average.toFixed(1)) : 0.0,
                year: year,
                department: r.known_for_department || 'Acting'
            };
        });
    }

    function renderResultsList(items) {
        const list = document.getElementById("results-list");
        if (items.length === 0) {
            list.innerHTML = '';
            return;
        }

        list.innerHTML = items.map(item => {
            const isPerson = item.type === 'person';
            const targetUrl = isPerson ? `/actor/${item.id}` : (item.type === 'custom' ? `/details/custom/${item.id}` : `/details/${item.type}/${item.id}`);
            
            // Badges
            let badgeBg = 'bg-[#1E1E2E] text-slate-400 border-white/5';
            let badgeText = 'Unknown';
            
            if (item.type === 'movie') {
                badgeBg = 'bg-violet-600/10 text-violet-400 border-violet-500/10';
                badgeText = 'Movie';
            } else if (item.type === 'tv') {
                badgeBg = 'bg-emerald-600/10 text-emerald-400 border-emerald-500/10';
                badgeText = 'TV Show';
            } else if (item.type === 'person') {
                badgeBg = 'bg-[#FDAA07]/10 text-[#FDAA07] border-[#FDAA07]/10';
                badgeText = 'Person';
            } else if (item.type === 'custom') {
                badgeBg = 'bg-violet-600/20 text-violet-400 border-violet-500/20';
                badgeText = item.language ? item.language.toUpperCase() : 'Custom';
            }

            return `
                <a href="${targetUrl}" class="group p-3 bg-[#1E1E2E]/20 hover:bg-[#1E1E2E]/40 border border-white/5 rounded-2xl flex items-center justify-between gap-4 transition duration-200 hover:scale-[1.01] hover:border-[#FDAA07]/20 select-none">
                    <div class="flex items-center gap-4">
                        <div class="overflow-hidden bg-[#1E1E2E] border border-white/5 flex-shrink-0
                            ${isPerson ? 'w-14 h-14 rounded-full' : 'w-13 h-18 rounded-xl'}">
                            <img src="${item.posterUrl}" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-white group-hover:text-[#FDAA07] transition line-clamp-1 leading-tight">${item.title}</h3>
                            <div class="flex items-center gap-2">
                                <span class="uppercase text-[9px] font-extrabold px-1.5 py-0.5 rounded border ${badgeBg}">${badgeText}</span>
                                ${item.year ? `<span class="text-xs text-slate-450">${item.year}</span>` : ''}
                            </div>
                            
                            ${!isPerson && item.rating > 0 ? `
                                <div class="flex items-center gap-1 text-xs text-[#FDAA07] font-bold">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <span>${item.rating}</span>
                                </div>
                            ` : ''}

                            ${isPerson ? `
                                <p class="text-xs text-slate-450">${item.department}</p>
                            ` : ''}
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-500 group-hover:text-white transition-all mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            `;
        }).join('');
    }

    function setLoadingState() {
        const list = document.getElementById("results-list");
        list.innerHTML = Array(6).fill(0).map(() => `
            <div class="p-3 bg-[#1E1E2E]/30 rounded-2xl border border-white/5 flex gap-4 items-center">
                <div class="w-13 h-18 bg-slate-900 rounded-xl animate-pulse"></div>
                <div class="flex-1 space-y-2 py-1">
                    <div class="h-3.5 bg-slate-900 rounded w-1/3 animate-pulse"></div>
                    <div class="h-3 bg-slate-900 rounded w-1/4 animate-pulse"></div>
                </div>
            </div>
        `).join('');
    }

    // Filter drawer controls
    function isFiltersDefault() {
        return activeFilters.genre === 'All' &&
               activeFilters.country === 'All' &&
               activeFilters.year === 'All' &&
               activeFilters.language === 'All' &&
               activeFilters.sortBy === 'Hottest';
    }

    function openFilters() {
        tempFilters = { ...activeFilters };
        renderFilterOptions();
        
        const drawer = document.getElementById("filter-drawer");
        const content = document.getElementById("filter-drawer-content");
        
        drawer.classList.remove("hidden");
        setTimeout(() => {
            content.classList.remove("translate-x-full");
        }, 10);
    }

    function closeFilters() {
        const drawer = document.getElementById("filter-drawer");
        const content = document.getElementById("filter-drawer-content");
        
        content.classList.add("translate-x-full");
        setTimeout(() => {
            drawer.classList.add("hidden");
        }, 300);
    }

    function handleBackdropClick(event) {
        if (event.target.id === "filter-drawer") {
            closeFilters();
        }
    }

    function renderFilterOptions() {
        // Sort By
        document.getElementById("filter-sort-options").innerHTML = sortOptions.map(opt => `
            <button onclick="setTempFilter('sortBy', '${opt}')"
                    class="px-4 py-2 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.sortBy === opt 
                        ? 'bg-[#FDAA07] border-[#e59805] text-white shadow-md shadow-[#FDAA07]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt}
            </button>
        `).join('');

        // Genres
        document.getElementById("filter-genres").innerHTML = genres.map(opt => `
            <button onclick="setTempFilter('genre', '${opt}')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.genre === opt 
                        ? 'bg-[#FDAA07] border-[#e59805] text-white shadow-md shadow-[#FDAA07]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt}
            </button>
        `).join('');

        // Countries
        document.getElementById("filter-countries").innerHTML = countries.map(opt => `
            <button onclick="setTempFilter('country', '${opt}')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.country === opt 
                        ? 'bg-[#FDAA07] border-[#e59805] text-white shadow-md shadow-[#FDAA07]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt}
            </button>
        `).join('');

        // Languages
        document.getElementById("filter-languages").innerHTML = languages.map(opt => `
            <button onclick="setTempFilter('language', '${opt}')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.language === opt 
                        ? 'bg-[#FDAA07] border-[#e59805] text-white shadow-md shadow-[#FDAA07]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt}
            </button>
        `).join('');

        // Years
        document.getElementById("filter-years").innerHTML = years.map(opt => `
            <button onclick="setTempFilter('year', '${opt}')"
                    class="px-4 py-2 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.year === opt 
                        ? 'bg-[#FDAA07] border-[#e59805] text-white shadow-md shadow-[#FDAA07]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt}
            </button>
        `).join('');
    }

    function setTempFilter(key, value) {
        tempFilters[key] = value;
        renderFilterOptions();
    }

    function applyFilters() {
        activeFilters = { ...tempFilters };
        closeFilters();
        updateActiveFiltersStrip();
        triggerSearch();
    }

    function resetFilters(applyImmediately = false) {
        tempFilters = {
            genre: 'All',
            country: 'All',
            year: 'All',
            language: 'All',
            sortBy: 'Hottest'
        };

        if (applyImmediately) {
            activeFilters = { ...tempFilters };
            updateActiveFiltersStrip();
            triggerSearch();
            closeFilters();
        } else {
            renderFilterOptions();
        }
    }

    function updateActiveFiltersStrip() {
        const strip = document.getElementById("active-filters-strip");
        const chipsContainer = document.getElementById("active-chips");
        const btnLabel = document.getElementById("filter-btn-label");

        if (isFiltersDefault()) {
            strip.classList.add("hidden");
            btnLabel.innerText = "Filters";
            return;
        }

        let activeCount = 0;
        let chipsHtml = [];

        if (activeFilters.genre !== 'All') { activeCount++; chipsHtml.push(buildChipHtml('genre', activeFilters.genre)); }
        if (activeFilters.country !== 'All') { activeCount++; chipsHtml.push(buildChipHtml('country', activeFilters.country)); }
        if (activeFilters.year !== 'All') { activeCount++; chipsHtml.push(buildChipHtml('year', activeFilters.year)); }
        if (activeFilters.language !== 'All') { activeCount++; chipsHtml.push(buildChipHtml('language', activeFilters.language)); }
        if (activeFilters.sortBy !== 'Hottest') { activeCount++; chipsHtml.push(buildChipHtml('sortBy', 'Sort: ' + activeFilters.sortBy)); }

        if (activeCount === 0) {
            strip.classList.add("hidden");
            btnLabel.innerText = "Filters";
            return;
        }

        btnLabel.innerText = `Filters (${activeCount})`;
        chipsContainer.innerHTML = chipsHtml.join('');
        strip.classList.remove("hidden");
    }

    function buildChipHtml(key, label) {
        return `
            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-extrabold bg-[#1E1E2E] text-slate-300 border border-white/5 rounded-lg select-none animate-fadeIn">
                <span>${label}</span>
                <button onclick="removeFilter('${key}')" class="text-slate-500 hover:text-slate-300 transition">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </span>
        `;
    }

    function removeFilter(key) {
        activeFilters[key] = key === 'sortBy' ? 'Hottest' : 'All';
        tempFilters = { ...activeFilters };
        updateActiveFiltersStrip();
        triggerSearch();
    }
</script>
@endsection
