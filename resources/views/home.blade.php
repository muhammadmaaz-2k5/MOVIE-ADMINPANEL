@extends('layouts.layout')

@section('title', 'CineMovie — Stream Movies & TV Shows')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-8 max-w-7xl mx-auto select-none" id="home-view">
    
    <!-- Hero Slider Section -->
    <div class="relative w-full h-[300px] md:h-[450px] rounded-3xl overflow-hidden shadow-2xl bg-slate-900/40 border border-white/5">
        <div id="hero-slider" class="w-full h-full relative">
            <!-- Skeletons while loading -->
            <div id="hero-skeleton" class="absolute inset-0 flex items-center justify-center bg-slate-950 animate-pulse">
                <div class="space-y-4 text-center">
                    <div class="w-32 h-6 bg-slate-800 rounded-md mx-auto"></div>
                    <div class="w-64 h-10 bg-slate-800 rounded-md mx-auto"></div>
                </div>
            </div>
            <!-- Slides Container -->
            <div id="hero-slides" class="w-full h-full relative opacity-0 transition-opacity duration-500"></div>
        </div>
        
        <!-- Controls & Dots -->
        <div class="absolute bottom-6 left-6 right-6 flex items-center justify-between z-10">
            <div id="hero-dots" class="flex gap-2"></div>
            <div class="flex gap-2">
                <button onclick="prevSlide()" class="p-2.5 rounded-full glass hover:bg-white/10 transition text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button onclick="nextSlide()" class="p-2.5 rounded-full glass hover:bg-white/10 transition text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Chips Scroll Row -->
    <div class="w-full">
        <div id="category-chips" class="flex gap-3 overflow-x-auto no-scrollbar py-2"></div>
    </div>

    <!-- Trending Row -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 id="trending-label" class="text-xl md:text-2xl font-extrabold tracking-tight">Trending This Week</h2>
        </div>
        <div class="relative">
            <!-- Scroll indicators -->
            <div id="trending-row" class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-1 px-0.5">
                <!-- Skeletons -->
                @for ($i = 0; $i < 6; $i++)
                <div class="min-w-[150px] md:min-w-[180px] aspect-[2/3] bg-slate-900/80 rounded-2xl animate-pulse"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Popular Grid -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 id="popular-label" class="text-xl md:text-2xl font-extrabold tracking-tight">Popular This Month</h2>
        </div>
        <div id="popular-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Skeletons -->
            @for ($i = 0; $i < 8; $i++)
            <div class="aspect-[2/3] bg-slate-900/80 rounded-2xl animate-pulse"></div>
            @endfor
        </div>
    </div>

    <!-- Custom Exclusives (Custom Manager Contents) -->
    <div id="custom-exclusives-section" class="space-y-4 hidden">
        <div class="flex justify-between items-center">
            <h2 class="text-xl md:text-2xl font-extrabold tracking-tight">💎 Custom Exclusives</h2>
        </div>
        <div class="relative">
            <div id="custom-exclusives-row" class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-1 px-0.5"></div>
        </div>
    </div>

    <!-- Dynamic Additional Sections (Similar to Mobile App) -->
    <div id="additional-sections-container" class="space-y-8 pt-4">
        <!-- Rendered dynamically via JavaScript -->
    </div>

</div>

<script>
    let homeSections = [];
    const cacheData = {};
    const customExclusivesCache = {};

    let categories = [];
    let currentCategory = 0;
    let featuredList = [];
    let currentSlide = 0;
    let slideInterval;

    document.addEventListener("DOMContentLoaded", async () => {
        fetchCategories();
        await loadHomeSections();
        loadCustomExclusives(0);
        loadAdditionalSections(0);
    });

    async function loadHomeSections() {
        try {
            const res = await fetch('/api/config/home-sections');
            homeSections = await res.json();
        } catch (err) {
            console.error("Error loading home sections configuration:", err);
            homeSections = [];
        }
    }

    async function fetchCategories() {
        try {
            const res = await fetch('/api/config/categories');
            categories = await res.json();
            renderCategoryChips();
            loadAllData(0); // load 'All' initially
        } catch (err) {
            console.error("Error loading dynamic categories configuration:", err);
        }
    }

    function renderCategoryChips() {
        const chipsContainer = document.getElementById("category-chips");
        chipsContainer.innerHTML = categories.map((cat, idx) => `
            <button onclick="switchCategory(${idx})" 
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold border transition duration-250 select-none whitespace-nowrap
                    ${idx === currentCategory 
                        ? 'bg-violet-600 border-violet-500 text-white shadow-lg shadow-violet-500/20' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10'}"
                    id="chip-${idx}">
                <span>${cat.emoji}</span>
                <span>${cat.label}</span>
            </button>
        `).join('');
    }

    async function loadAllData(idx) {
        if (cacheData[idx]) {
            featuredList = cacheData[idx].featuredList || [];
            renderHeroSlider();
            renderTrendingRow(cacheData[idx].trendingItems || []);
            renderPopularGrid(cacheData[idx].popularItems || []);
            return;
        }

        setLoadingState();
        
        const cat = categories[idx];
        const monthStart = getMonthStartDate();
        const monthEnd = getMonthEndDate();

        // 1. Fetch Trending
        let trendingUrl = '';
        let trendingParams = { page: 1 };
        
        if (cat.mediaType === 'all') {
            trendingUrl = '/api/tmdb/trending/all/week';
        } else {
            trendingUrl = `/api/tmdb/discover/${cat.mediaType}`;
            trendingParams = { ...trendingParams, ...cat.trendingParams, sort_by: 'popularity.desc', include_adult: false };
        }

        // 2. Fetch Popular
        let popularUrl = `/api/tmdb/discover/${cat.mediaType === 'all' ? 'movie' : cat.mediaType}`;
        let popularParams = { 
            page: 1, 
            sort_by: 'popularity.desc', 
            include_adult: false,
            ...cat.popularParams 
        };
        
        if (cat.mediaType === 'all') {
            popularParams['release_date.gte'] = monthStart;
            popularParams['release_date.lte'] = monthEnd;
        }

        try {
            // Fetch custom exclusives for this category first (or in parallel) to merge them
            const customParams = {};
            if (cat.mediaType && cat.mediaType !== 'all') {
                customParams.type = cat.mediaType;
            }
            if (cat.trendingParams && cat.trendingParams.with_genres) {
                customParams.genre = cat.trendingParams.with_genres;
            }
            const customUrlParams = new URLSearchParams(customParams);

            const [trendRes, popRes, customRes] = await Promise.all([
                fetch(`${trendingUrl}?${new URLSearchParams(trendingParams)}`).then(r => r.json()),
                fetch(`${popularUrl}?${new URLSearchParams(popularParams)}`).then(r => r.json()),
                fetch(`/api/custom-content?${customUrlParams}`).then(r => r.json()).catch(() => [])
            ]);

            let trendingItems = parseItems(trendRes.results || [], cat.mediaType);
            let popularItems = parseItems(popRes.results || [], cat.mediaType);

            // Fallback for popular if date constraints returned sparse list
            if (cat.mediaType === 'all' && popularItems.length < 4) {
                delete popularParams['release_date.gte'];
                delete popularParams['release_date.lte'];
                const popFallback = await fetch(`${popularUrl}?${new URLSearchParams(popularParams)}`).then(r => r.json());
                popularItems = parseItems(popFallback.results || [], 'movie');
            }

            // --- ALGORITHM: Custom Exclusives Merge & Promotion ---
            if (customRes && customRes.length > 0) {
                const customTmdbIds = customRes.map(c => c.tmdb_id);
                trendingItems = trendingItems.filter(item => !customTmdbIds.includes(item.id));
                popularItems = popularItems.filter(item => !customTmdbIds.includes(item.id));

                // Prepend custom exclusives to give them maximum visibility
                trendingItems = [...customRes, ...trendingItems];
                popularItems = [...customRes, ...popularItems];
            }

            featuredList = trendingItems.slice(0, 3);
            renderHeroSlider();

            renderTrendingRow(trendingItems);
            renderPopularGrid(popularItems.slice(0, 8));

            // Cache results
            cacheData[idx] = {
                featuredList: featuredList,
                trendingItems: trendingItems,
                popularItems: popularItems
            };

        } catch (err) {
            console.error("Error loading home page content:", err);
        }
    }

    function switchCategory(idx) {
        if (idx === currentCategory) return;
        
        // Update active class immediately in UI
        document.getElementById(`chip-${currentCategory}`).className = "flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold border transition duration-250 select-none whitespace-nowrap bg-[#1E1E2E] border-white/5 text-slate-400 hover:text-slate-200 hover:border-white/10";
        document.getElementById(`chip-${idx}`).className = "flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold border transition duration-250 select-none whitespace-nowrap bg-violet-600 border-violet-500 text-white shadow-lg shadow-violet-500/20";
        
        currentCategory = idx;

        // Update titles
        const cat = categories[idx];
        document.getElementById("trending-label").innerText = cat.label === 'All' ? 'Trending This Week' : `Trending ${cat.label}s`;
        document.getElementById("popular-label").innerText = cat.label === 'All' ? 'Popular This Month' : `Popular ${cat.label}s`;

        loadAllData(idx);
        loadCustomExclusives(idx);
        loadAdditionalSections(idx);
    }

    function parseItems(results, defaultType) {
        return results.map(r => {
            const mediaType = r.media_type || (defaultType === 'all' ? 'movie' : defaultType);
            const dateRaw = r.release_date || r.first_air_date || '';
            const year = dateRaw.length >= 4 ? dateRaw.substring(0, 4) : '';
            return {
                id: r.id,
                title: r.title || r.name || 'Unknown Title',
                type: mediaType === 'tv' ? 'tv' : 'movie',
                posterUrl: r.poster_path ? `https://image.tmdb.org/t/p/w342${r.poster_path}` : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image',
                backdropUrl: r.backdrop_path ? `https://image.tmdb.org/t/p/w780${r.backdrop_path}` : '',
                rating: r.vote_average ? parseFloat(r.vote_average.toFixed(1)) : 0.0,
                year: year
            };
        });
    }

    function setLoadingState() {
        // Shimmer template for row
        const row = document.getElementById("trending-row");
        row.innerHTML = Array(6).fill(0).map(() => `
            <div class="min-w-[150px] md:min-w-[180px] aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
        `).join('');

        // Shimmer template for grid
        const grid = document.getElementById("popular-grid");
        grid.innerHTML = Array(8).fill(0).map(() => `
            <div class="aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
        `).join('');
    }

    function renderHeroSlider() {
        const slider = document.getElementById("hero-slides");
        const dots = document.getElementById("hero-dots");
        const skeleton = document.getElementById("hero-skeleton");

        if (featuredList.length === 0) return;

        slider.innerHTML = featuredList.map((item, idx) => `
            <div class="absolute inset-0 transition-all duration-700 ease-in-out opacity-0 select-none hero-slide" id="slide-${idx}">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://image.tmdb.org/t/p/w1280${item.backdropUrl ? item.backdropUrl.substring(item.backdropUrl.lastIndexOf('/')) : ''}')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0B0B14] via-[#0B0B14]/30 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#0B0B14] via-[#0B0B14]/20 to-transparent"></div>
                
                <!-- Details -->
                <div class="absolute bottom-16 left-6 md:left-12 max-w-[90%] md:max-w-[50%] space-y-4">
                    <span class="px-3 py-1 text-[11px] font-extrabold uppercase bg-violet-600 text-white rounded-md tracking-wider">FEATURED</span>
                    <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight drop-shadow-md line-clamp-2">${item.title}</h1>
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-300">
                        <span class="flex items-center gap-1 text-amber-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            ${item.rating}
                        </span>
                        <span>•</span>
                        <span>${item.year}</span>
                        <span>•</span>
                        <span class="uppercase">${item.type}</span>
                    </div>
                    <p class="hidden md:block text-slate-400 text-[14px] leading-relaxed line-clamp-2">Click to view details, reviews, trailers and full interactive streaming.</p>
                    <a href="/details/${item.type}/${item.id}" class="inline-flex items-center gap-2 bg-white text-slate-950 font-bold px-6 py-3 rounded-xl hover:bg-slate-200 transition text-[13px] shadow-lg shadow-white/5">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <span>Watch Now</span>
                    </a>
                </div>
            </div>
        `).join('');

        dots.innerHTML = featuredList.map((_, idx) => `
            <button onclick="goToSlide(${idx})" class="w-2 h-2 rounded-full transition-all duration-300 ${idx === 0 ? 'bg-violet-500 w-6' : 'bg-slate-600 hover:bg-slate-400'}" id="dot-${idx}"></button>
        `).join('');

        if (skeleton) skeleton.classList.add("hidden");
        slider.classList.remove("opacity-0");

        currentSlide = 0;
        showSlide(0);
        startSlideTimer();
    }

    function showSlide(idx) {
        const slides = document.querySelectorAll(".hero-slide");
        slides.forEach((slide, i) => {
            if (i === idx) {
                slide.classList.remove("opacity-0");
                slide.classList.add("opacity-100", "z-10");
                document.getElementById(`dot-${i}`).className = "w-2 h-2 rounded-full transition-all duration-300 bg-violet-500 w-6";
            } else {
                slide.classList.remove("opacity-100", "z-10");
                slide.classList.add("opacity-0");
                document.getElementById(`dot-${i}`).className = "w-2 h-2 rounded-full transition-all duration-300 bg-slate-600 hover:bg-slate-400";
            }
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % featuredList.length;
        showSlide(currentSlide);
        resetSlideTimer();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + featuredList.length) % featuredList.length;
        showSlide(currentSlide);
        resetSlideTimer();
    }

    function goToSlide(idx) {
        currentSlide = idx;
        showSlide(currentSlide);
        resetSlideTimer();
    }

    function startSlideTimer() {
        slideInterval = setInterval(nextSlide, 6000);
    }

    function resetSlideTimer() {
        clearInterval(slideInterval);
        startSlideTimer();
    }

    function renderTrendingRow(items) {
        const row = document.getElementById("trending-row");
        row.innerHTML = items.map(item => {
            const detailsUrl = item.is_custom ? `/details/custom/${item.custom_id}` : `/details/${item.type}/${item.id}`;
            return `
                <a href="${detailsUrl}" class="min-w-[145px] md:min-w-[170px] group flex flex-col gap-2 relative">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                        <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                            <span class="text-white text-[11px] font-bold truncate w-full">${item.title}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs px-1 select-none">
                        <span class="text-slate-400 font-bold">${item.year}</span>
                        <span class="flex items-center gap-1 font-bold text-amber-500">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            ${item.rating}
                        </span>
                    </div>
                </a>
            `;
        }).join('');
    }

    function renderPopularGrid(items) {
        const grid = document.getElementById("popular-grid");
        grid.innerHTML = items.map(item => {
            const detailsUrl = item.is_custom ? `/details/custom/${item.custom_id}` : `/details/${item.type}/${item.id}`;
            return `
                <a href="${detailsUrl}" class="group flex flex-col gap-2 relative">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                        <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                        <!-- Rating pill top-right -->
                        <div class="absolute top-3 right-3 px-2 py-1 glass rounded-lg flex items-center gap-1 text-[11px] font-extrabold text-amber-400 select-none">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            <span>${item.rating}</span>
                        </div>
                    </div>
                    <div class="px-1 space-y-0.5">
                        <h3 class="text-sm font-bold text-white group-hover:text-violet-400 transition truncate w-full">${item.title}</h3>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>${item.year}</span>
                            <span class="uppercase text-[10px] font-extrabold bg-[#1E1E2E] px-1.5 py-0.5 rounded border border-white/5">${item.type}</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
    }

    async function loadCustomExclusives(catIdx = 0) {
        const section = document.getElementById("custom-exclusives-section");
        const row = document.getElementById("custom-exclusives-row");
        if (!section || !row) return;

        if (customExclusivesCache[catIdx]) {
            const cachedRes = customExclusivesCache[catIdx];
            if (cachedRes.length === 0) {
                section.classList.add("hidden");
                return;
            }
            section.classList.remove("hidden");
            row.innerHTML = cachedRes.map(item => `
                <a href="/details/custom/${item.custom_id}" class="min-w-[145px] md:min-w-[170px] group flex flex-col gap-2 relative">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                        <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                            <span class="text-white text-[11px] font-bold truncate w-full">${item.title}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs px-1 select-none">
                        <span class="text-slate-400 font-bold">${item.year}</span>
                        <span class="flex items-center gap-1 font-bold text-amber-500">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            ${item.rating}
                        </span>
                    </div>
                </a>
            `).join('');
            return;
        }

        const cat = categories[catIdx];
        const params = {};
        
        if (cat) {
            if (cat.mediaType && cat.mediaType !== 'all') {
                params.type = cat.mediaType;
            }
            if (cat.trendingParams && cat.trendingParams.with_genres) {
                params.genre = cat.trendingParams.with_genres;
            }
        }

        try {
            const urlParams = new URLSearchParams(params);
            const res = await fetch(`/api/custom-content?${urlParams}`).then(r => r.json());
            
            customExclusivesCache[catIdx] = res;

            if (res.length === 0) {
                section.classList.add("hidden");
                return;
            }

            section.classList.remove("hidden");
            row.innerHTML = res.map(item => `
                <a href="/details/custom/${item.custom_id}" class="min-w-[145px] md:min-w-[170px] group flex flex-col gap-2 relative">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                        <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                            <span class="text-white text-[11px] font-bold truncate w-full">${item.title}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs px-1 select-none">
                        <span class="text-slate-400 font-bold">${item.year}</span>
                        <span class="flex items-center gap-1 font-bold text-amber-500">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            ${item.rating}
                        </span>
                    </div>
                </a>
            `).join('');
        } catch (err) {
            console.error("Error loading custom exclusives:", err);
            section.classList.add("hidden");
        }
    }

    const sectionCache = {};
    let sectionObservers = [];

    async function loadAdditionalSections(catIdx = 0) {
        const container = document.getElementById("additional-sections-container");
        if (!container) return;

        // Disconnect any existing observers to avoid duplication
        sectionObservers.forEach(obs => obs.disconnect());
        sectionObservers = [];

        const cat = categories[catIdx];

        // Render skeleton loaders for each section
        container.innerHTML = homeSections.map((sec, idx) => `
            <div class="space-y-4 min-h-[220px]" id="section-${idx}">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">${sec.emoji}</span>
                        <h2 class="text-xl md:text-2xl font-extrabold tracking-tight">${sec.title}</h2>
                    </div>
                </div>
                <div class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-1 px-0.5">
                    ${Array(6).fill(0).map(() => `
                        <div class="min-w-[150px] md:min-w-[180px] aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
                    `).join('')}
                </div>
            </div>
        `).join('');

        // Setup observer for each section wrapper
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const sectionIdx = parseInt(entry.target.dataset.sectionIdx);
                    loadSectionData(sectionIdx, catIdx);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '200px 0px', // trigger fetch when 200px from viewport
            threshold: 0.01
        });

        sectionObservers.push(observer);

        homeSections.forEach((sec, idx) => {
            const sectionDiv = document.getElementById(`section-${idx}`);
            if (sectionDiv) {
                sectionDiv.dataset.sectionIdx = idx;
                observer.observe(sectionDiv);
            }
        });
    }

    async function loadSectionData(i, catIdx) {
        const cacheKey = `${catIdx}_${i}`;
        const sectionDiv = document.getElementById(`section-${i}`);
        if (!sectionDiv) return;

        const sec = homeSections[i];
        const cat = categories[catIdx];

        if (sectionCache[cacheKey]) {
            renderSectionHTML(sectionDiv, sectionCache[cacheKey], sec);
            return;
        }

        try {
            const mergedParams = {
                ...(sec.params || {}),
                ...(cat ? (cat.trendingParams || {}) : {}),
                page: 1,
                include_adult: 'false'
            };

            if (sec.params && sec.params.with_genres && cat && cat.trendingParams && cat.trendingParams.with_genres) {
                const secGenres = String(sec.params.with_genres).split(',');
                const catGenres = String(cat.trendingParams.with_genres).split(',');
                const uniqueGenres = Array.from(new Set([...secGenres, ...catGenres]));
                mergedParams.with_genres = uniqueGenres.join(',');
            }

            const queryParams = new URLSearchParams(mergedParams);
            const res = await fetch(`/api/tmdb/${sec.endpoint}?${queryParams}`).then(r => r.json());
            
            const defaultMediaType = sec.endpoint.includes('/tv') || sec.endpoint.includes('tv/') ? 'tv' : 'movie';
            const items = parseItems(res.results || [], defaultMediaType);

            if (items.length === 0) {
                sectionDiv.innerHTML = '';
                sectionDiv.style.display = 'none';
                return;
            }

            sectionCache[cacheKey] = items;
            renderSectionHTML(sectionDiv, items, sec);
        } catch (err) {
            console.error(`Error loading section ${sec.title}:`, err);
            sectionDiv.innerHTML = `<div class="text-slate-500 text-sm py-4">Failed to load ${sec.title}</div>`;
        }
    }

    function renderSectionHTML(sectionDiv, items, sec) {
        sectionDiv.style.display = 'block';
        sectionDiv.innerHTML = `
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-xl">${sec.emoji}</span>
                    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight">${sec.title}</h2>
                </div>
            </div>
            <div class="relative">
                <div class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-1 px-0.5">
                    ${items.map(item => {
                        const detailsUrl = item.is_custom ? `/details/custom/${item.custom_id}` : `/details/${item.type}/${item.id}`;
                        return `
                            <a href="${detailsUrl}" class="min-w-[145px] md:min-w-[170px] group flex flex-col gap-2 relative">
                                <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                                    <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                                        <span class="text-white text-[11px] font-bold truncate w-full">${item.title}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-xs px-1 select-none">
                                    <span class="text-slate-400 font-bold">${item.year}</span>
                                    <span class="flex items-center gap-1 font-bold text-amber-500">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                        ${item.rating}
                                    </span>
                                </div>
                            </a>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    function getMonthStartDate() {
        const now = new Date();
        return `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-01`;
    }

    function getMonthEndDate() {
        const now = new Date();
        const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        return `${last.getFullYear()}-${(last.getMonth() + 1).toString().padStart(2, '0')}-${last.getDate().toString().padStart(2, '0')}`;
    }
</script>
@endsection
