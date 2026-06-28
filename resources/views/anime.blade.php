@extends('layouts.layout')

@section('title', 'Explore Anime — CineMovie')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-6 max-w-7xl mx-auto select-none" id="anime-view">
    
    <!-- Page Header and Filter Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>Anime Portal</span>
                <span class="text-xl">⛩️</span>
            </h1>
            <p class="text-slate-400 text-sm">Discover Japanese anime series, popular releases, and trending animations.</p>
        </div>
        
        <!-- Filter Button -->
        <button onclick="openFilters()" class="flex items-center gap-2 px-4 py-2.5 bg-[#1E1E2E] border border-white/5 hover:border-[#FF6B9D]/20 text-xs font-extrabold text-slate-300 hover:text-white rounded-xl transition duration-200">
            <svg class="w-4 h-4 text-[#FF6B9D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            <span id="filter-btn-label">Filters</span>
        </button>
    </div>

    <!-- Active Filters Strip -->
    <div id="active-filters-strip" class="flex flex-wrap items-center gap-2 bg-[#FF6B9D]/10 border border-[#FF6B9D]/10 p-3 rounded-2xl hidden">
        <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#FF6B9D] mr-1">Active:</span>
        <div id="active-chips" class="flex flex-wrap gap-1.5"></div>
        <button onclick="resetFilters(true)" class="text-[11px] font-extrabold text-[#FF6B9D] hover:text-[#ff558f] transition ml-auto">Clear All</button>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-[#1E1E2E] flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-1" id="anime-tabs-container"></div>

    <!-- Anime Grid -->
    <div class="space-y-6">
        <div id="anime-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
            <!-- Skeletons initial loading -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-20 space-y-2 animate-fadeIn">
            <span class="text-3xl">🏜️</span>
            <h3 class="text-md font-bold text-white">No Anime Found</h3>
            <p class="text-slate-500 text-xs">Try clearing filters or changing tabs</p>
        </div>

        <!-- Grid Loading More Spinner -->
        <div id="load-more-indicator" class="hidden flex justify-center py-6">
            <div class="w-6 h-6 border-2 border-[#FF6B9D] border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>

</div>

<!-- Filters Drawer -->
<div id="filter-drawer" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex justify-end hidden select-none" onclick="handleBackdropClick(event)">
    <div id="filter-drawer-content" class="w-full max-w-md bg-[#121220] h-full flex flex-col border-l border-white/5 transform translate-x-full transition-transform duration-300">
        <!-- Drawer Header -->
        <div class="px-6 py-5 border-b border-[#1E1E2E] flex justify-between items-center bg-[#1E1E2E]/20">
            <div>
                <h3 class="text-lg font-extrabold text-white">Refine Anime</h3>
                <p class="text-slate-400 text-xs mt-0.5">Filter by release year, origin country and sorting options</p>
            </div>
            <button onclick="closeFilters()" class="p-2 rounded-xl bg-[#1E1E2E] border border-white/5 hover:bg-white/5 transition">
                <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Drawer Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 scrollbar-thin">
            <!-- Sort options -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Sort By</h4>
                <div class="flex flex-wrap gap-2" id="filter-sort-options"></div>
            </div>

            <!-- Years -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Release Year</h4>
                <div class="flex flex-wrap gap-2" id="filter-years"></div>
            </div>

            <!-- Countries -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Country</h4>
                <div class="flex flex-wrap gap-2" id="filter-countries"></div>
            </div>
        </div>

        <!-- Drawer Footer -->
        <div class="p-6 border-t border-[#1E1E2E] flex gap-3 bg-[#1E1E2E]/10">
            <button onclick="resetFilters(false)" class="flex-1 py-3.5 rounded-xl bg-[#1E1E2E] border border-white/5 text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition">
                Reset All
            </button>
            <button onclick="applyFilters()" class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-pink-600 to-[#FF6B9D] text-white text-xs font-bold shadow-lg shadow-[#FF6B9D]/20 hover:from-pink-500 hover:to-[#ff558f] transition">
                Apply Filters
            </button>
        </div>
    </div>
</div>

<script>
    // Tab and filter configurations
    const animeTabs = [
        { label: '🎬 Movies', type: 'movie' },
        { label: '📺 Shows',  type: 'tv' }
    ];

    const countries = [
        'All', 'United States', 'United Kingdom', 'France', 'Japan', 'China', 'Korea'
    ];

    const countryCodes = {
        'United States': 'US', 'United Kingdom': 'GB', 'France': 'FR',
        'Japan': 'JP', 'China': 'CN', 'Korea': 'KR'
    };

    const years = [
        'All', '2026', '2025', '2024', '2023', '2022', '2021', '2020',
        '2010s', '2000s', '1990s', '1980s'
    ];

    const sortOptions = ['ForYou', 'Hottest', 'Latest', 'Rating'];

    // State Variables
    let currentTabIndex = 0;
    let currentPage = 1;
    let totalPages = 1;
    let isLoading = false;
    let isLoadingMore = false;
    let animeList = [];
    const animeCache = {};

    function getCacheKey(tabIdx, page) {
        return `${tabIdx}_${page}_${JSON.stringify(activeFilters)}`;
    }

    // Filter Objects
    let activeFilters = {
        country: 'All',
        year: 'All',
        sortBy: 'ForYou'
    };

    let tempFilters = { ...activeFilters };

    document.addEventListener("DOMContentLoaded", () => {
        renderTabs();
        loadTabContent(0, 1);
        setupInfiniteScroll();
    });

    function renderTabs() {
        const container = document.getElementById("anime-tabs-container");
        container.innerHTML = animeTabs.map((tab, idx) => `
            <button onclick="switchTab(${idx})" 
                    id="tab-btn-${idx}"
                    class="tab-btn pb-3 text-sm font-semibold transition-all whitespace-nowrap border-b-2
                    ${idx === currentTabIndex 
                        ? 'border-[#FF6B9D] text-white font-extrabold' 
                        : 'border-transparent text-slate-400 hover:text-slate-200'}">
                ${tab.label}
            </button>
        `).join('');
    }

    function switchTab(idx) {
        if (idx === currentTabIndex && !isLoading) return;
        
        // Remove active styling from current tab
        document.getElementById(`tab-btn-${currentTabIndex}`).className = "tab-btn pb-3 text-sm font-semibold transition-all whitespace-nowrap border-b-2 border-transparent text-slate-400 hover:text-slate-200";
        // Add active styling to new tab
        document.getElementById(`tab-btn-${idx}`).className = "tab-btn pb-3 text-sm font-semibold transition-all whitespace-nowrap border-b-2 border-[#FF6B9D] text-white font-extrabold";

        currentTabIndex = idx;
        currentPage = 1;
        animeList = [];

        loadTabContent(idx, 1);
    }

    function buildParams(tabIdx, page) {
        const type = animeTabs[tabIdx].type;
        const params = {
            page: page,
            include_adult: 'false',
            with_genres: '16',
            with_keywords: '210024'
        };

        // 1. Sort By
        switch (activeFilters.sortBy) {
            case 'Rating':
                params['sort_by'] = 'vote_average.desc';
                params['vote_count.gte'] = 100;
                break;
            case 'Latest':
                params['sort_by'] = type === 'movie' ? 'release_date.desc' : 'first_air_date.desc';
                break;
            case 'Hottest':
            case 'ForYou':
            default:
                params['sort_by'] = 'popularity.desc';
                break;
        }

        // 2. Country
        if (activeFilters.country !== 'All' && activeFilters.country !== 'Other') {
            const code = countryCodes[activeFilters.country];
            if (code) params['with_origin_country'] = code;
        }

        // 3. Year
        const yr = activeFilters.year;
        if (yr !== 'All' && yr !== 'Other') {
            const dateField = type === 'movie' ? 'release_date' : 'first_air_date';
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

        return params;
    }

    async function loadTabContent(tabIdx, page) {
        const cacheKey = getCacheKey(tabIdx, page);
        if (animeCache[cacheKey]) {
            const cached = animeCache[cacheKey];
            totalPages = cached.totalPages;
            if (page === 1) {
                animeList = cached.items;
                renderGrid(animeList);
                if (animeList.length === 0) {
                    document.getElementById("empty-state").classList.remove("hidden");
                }
                isLoading = false;
            } else {
                animeList = [...animeList, ...cached.items];
                appendItemsToGrid(cached.items);
                isLoadingMore = false;
                document.getElementById("load-more-indicator").classList.add("hidden");
            }
            return;
        }

        if (page === 1) {
            isLoading = true;
            renderSkeletons();
            document.getElementById("empty-state").classList.add("hidden");
        } else {
            isLoadingMore = true;
            document.getElementById("load-more-indicator").classList.remove("hidden");
        }

        const type = animeTabs[tabIdx].type;
        const params = buildParams(tabIdx, page);

        try {
            // Fetch custom content in parallel for page 1. Since it's anime, filter by genre = 16 (Animation) and matching type
            let customItems = [];
            if (page === 1) {
                const customParams = { type: type, genre: 16 };
                customItems = await fetch(`/api/custom-content?${new URLSearchParams(customParams)}`).then(r => r.json()).catch(() => []);
            }

            const res = await fetch(`/api/tmdb/discover/${type}?${new URLSearchParams(params)}`).then(r => r.json());
            let items = parseItems(res.results || [], type);
            totalPages = res.total_pages || 1;

            if (page === 1 && customItems.length > 0) {
                const customTmdbIds = customItems.map(c => c.tmdb_id);
                items = items.filter(item => !customTmdbIds.includes(item.id));
                items = [...customItems, ...items];
            }

            // Cache result
            animeCache[cacheKey] = {
                items: items,
                totalPages: totalPages
            };

            if (page === 1) {
                animeList = items;
                renderGrid(animeList);
                if (animeList.length === 0) {
                    document.getElementById("empty-state").classList.remove("hidden");
                }
                isLoading = false;
            } else {
                animeList = [...animeList, ...items];
                appendItemsToGrid(items);
                isLoadingMore = false;
                document.getElementById("load-more-indicator").classList.add("hidden");
            }
        } catch (err) {
            console.error("Error loading anime page content:", err);
            isLoading = false;
            isLoadingMore = false;
            document.getElementById("load-more-indicator").classList.add("hidden");
        }
    }

    function parseItems(results, type) {
        return results.map(r => {
            const dateRaw = r.release_date || r.first_air_date || '';
            const year = dateRaw.length >= 4 ? dateRaw.substring(0, 4) : '';
            return {
                id: r.id,
                title: r.title || r.name || 'Unknown Title',
                type: type,
                posterUrl: r.poster_path ? `https://image.tmdb.org/t/p/w342${r.poster_path}` : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image',
                rating: r.vote_average ? parseFloat(r.vote_average.toFixed(1)) : 0.0,
                year: year
            };
        });
    }

    function renderSkeletons() {
        const grid = document.getElementById("anime-grid");
        grid.innerHTML = Array(10).fill(0).map(() => `
            <div class="aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
        `).join('');
    }

    function renderGrid(items) {
        const grid = document.getElementById("anime-grid");
        if (items.length === 0) {
            grid.innerHTML = '';
            return;
        }
        grid.innerHTML = items.map(item => buildCardHtml(item)).join('');
    }

    function appendItemsToGrid(items) {
        const grid = document.getElementById("anime-grid");
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = items.map(item => buildCardHtml(item)).join('');
        while (tempDiv.firstChild) {
            grid.appendChild(tempDiv.firstChild);
        }
    }

    function buildCardHtml(item) {
        const detailsUrl = item.is_custom ? `/details/custom/${item.custom_id}` : `/details/${item.type}/${item.id}`;
        return `
            <a href="${detailsUrl}" class="group flex flex-col gap-2 relative">
                <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-[#FF6B9D]/10">
                    <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                    <!-- Rating pill top-right -->
                    <div class="absolute top-3 right-3 px-2 py-1 glass rounded-lg flex items-center gap-1 text-[11px] font-extrabold text-amber-400 select-none">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        <span>${item.rating}</span>
                    </div>
                </div>
                <div class="px-1 space-y-0.5">
                    <h3 class="text-sm font-bold text-white group-hover:text-[#FF6B9D] transition truncate w-full">${item.title}</h3>
                    <div class="flex justify-between text-xs text-slate-400">
                        <span>${item.year}</span>
                    </div>
                </div>
            </a>
        `;
    }

    function setupInfiniteScroll() {
        window.addEventListener("scroll", () => {
            if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 500) {
                if (!isLoading && !isLoadingMore && currentPage < totalPages) {
                    currentPage++;
                    loadTabContent(currentTabIndex, currentPage);
                }
            }
        });
    }

    // Filters logic
    function isFiltersDefault() {
        return activeFilters.country === 'All' &&
               activeFilters.year === 'All' &&
               activeFilters.sortBy === 'ForYou';
    }

    function openFilters() {
        tempFilters = { ...activeFilters };
        renderFilterOptions();
        
        const drawer = document.getElementById("filter-drawer");
        const content = document.getElementById("filter-drawer-content");
        
        drawer.classList.remove("hidden");
        // Trigger reflow then transition
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
        // Render Sort By
        document.getElementById("filter-sort-options").innerHTML = sortOptions.map(opt => `
            <button onclick="setTempFilter('sortBy', '${opt}')"
                    class="px-4 py-2 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.sortBy === opt 
                        ? 'bg-[#FF6B9D] border-[#ff558f] text-white shadow-md shadow-[#FF6B9D]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt === 'ForYou' ? 'For You' : opt}
            </button>
        `).join('');

        // Render Years
        document.getElementById("filter-years").innerHTML = years.map(opt => `
            <button onclick="setTempFilter('year', '${opt}')"
                    class="px-4 py-2 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.year === opt 
                        ? 'bg-[#FF6B9D] border-[#ff558f] text-white shadow-md shadow-[#FF6B9D]/10' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}">
                ${opt}
            </button>
        `).join('');

        // Render Countries
        document.getElementById("filter-countries").innerHTML = countries.map(opt => `
            <button onclick="setTempFilter('country', '${opt}')"
                    class="px-4 py-2 text-xs font-bold rounded-xl border select-none transition duration-200
                    ${tempFilters.country === opt 
                        ? 'bg-[#FF6B9D] border-[#ff558f] text-white shadow-md shadow-[#FF6B9D]/10' 
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
        
        currentPage = 1;
        animeList = [];
        loadTabContent(currentTabIndex, 1);
    }

    function resetFilters(applyImmediately = false) {
        tempFilters = {
            country: 'All',
            year: 'All',
            sortBy: 'ForYou'
        };
        
        if (applyImmediately) {
            activeFilters = { ...tempFilters };
            updateActiveFiltersStrip();
            currentPage = 1;
            animeList = [];
            loadTabContent(currentTabIndex, 1);
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

        // Check each filter to see if it is custom
        if (activeFilters.country !== 'All') {
            activeCount++;
            chipsHtml.push(buildChipHtml('country', activeFilters.country));
        }
        if (activeFilters.year !== 'All') {
            activeCount++;
            chipsHtml.push(buildChipHtml('year', activeFilters.year));
        }
        if (activeFilters.sortBy !== 'ForYou') {
            activeCount++;
            chipsHtml.push(buildChipHtml('sortBy', activeFilters.sortBy));
        }

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
            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-extrabold bg-[#1E1E2E] text-slate-300 border border-white/5 rounded-lg select-none">
                <span>${label === 'ForYou' ? 'For You' : label}</span>
                <button onclick="removeFilter('${key}')" class="text-slate-500 hover:text-slate-300 transition">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </span>
        `;
    }

    function removeFilter(key) {
        activeFilters[key] = key === 'sortBy' ? 'ForYou' : 'All';
        tempFilters = { ...activeFilters };
        updateActiveFiltersStrip();
        currentPage = 1;
        animeList = [];
        loadTabContent(currentTabIndex, 1);
    }
</script>
@endsection
