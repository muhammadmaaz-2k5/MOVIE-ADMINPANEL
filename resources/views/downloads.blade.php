@extends('layouts.layout')

@section('title', 'My Downloads — CineMovie')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-8 max-w-7xl mx-auto select-none">
    
    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <span>Offline Downloads</span>
            <span class="text-xl">📥</span>
        </h1>
        <p class="text-slate-400 text-sm">Access your saved offline streams, downloaded movies, and TV series episodes.</p>
    </div>

    <!-- Toggle Selector -->
    <div class="flex gap-4 border-b border-[#1E1E2E]">
        <button onclick="switchMode('downloads')" id="mode-downloads" class="pb-3 text-sm font-extrabold border-b-2 border-violet-500 text-white transition-all">Downloads</button>
        <button onclick="switchMode('favorites')" id="mode-favorites" class="pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">My Watchlist</button>
    </div>

    <!-- Downloads List Area -->
    <div id="downloads-section" class="space-y-8">
        <div id="downloads-container" class="space-y-4">
            <!-- Loaded dynamically from localStorage -->
        </div>

        <!-- External Download Sources (matching Mobile App) -->
        <div class="space-y-4 pt-6 border-t border-[#1E1E2E]">
            <div>
                <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <span class="text-violet-500">🌍</span>
                    <span>External Download Sources</span>
                </h2>
                <p class="text-slate-400 text-xs mt-1">Need torrent magnets or subtitles? Explore standard public indexing portals. Links open externally.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="external-sources-container">
                <!-- Sources items -->
            </div>
        </div>
    </div>

    <!-- Favorites / Watchlist Area -->
    <div id="favorites-container" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
        <!-- Will be loaded dynamically from localStorage -->
        <div class="text-slate-500 text-sm col-span-full py-12 text-center" id="no-favorites-msg">No bookmarked watchlist items found. Explore movies and tap the heart icon to save them here.</div>
    </div>

</div>

<script>
    let activeMode = 'downloads';
    
    const externalSources = [
        { name: 'YTS', emoji: '🎬', desc: 'Best quality movies in extremely compressed file sizes', url: 'https://yts.mx' },
        { name: 'EZTV', emoji: '📺', desc: 'TV show torrent links updated daily in multiple qualities', url: 'https://eztv.re' },
        { name: '1337x', emoji: '🔍', desc: 'Large public search index of movies, TV shows, and anime', url: 'https://1337x.to' },
        { name: 'Archive.org', emoji: '📦', desc: 'Millions of free public domain movies, books, and videos', url: 'https://archive.org/details/movies' },
        { name: 'Open Subtitles', emoji: '📝', desc: 'Download multi-language subtitles for any movie or TV series', url: 'https://www.opensubtitles.org' }
    ];

    document.addEventListener("DOMContentLoaded", () => {
        loadOfflineDownloads();
        renderExternalSources();
    });

    function switchMode(mode) {
        if (mode === activeMode) return;

        // Toggle Buttons Highlight
        document.querySelectorAll("[id^='mode-']").forEach(btn => {
            btn.className = "pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
        });
        document.getElementById(`mode-${mode}`).className = "pb-3 text-sm font-extrabold border-b-2 border-violet-500 text-white transition-all";

        // Toggle Containers
        if (mode === 'downloads') {
            document.getElementById("downloads-section").classList.remove("hidden");
            document.getElementById("favorites-container").classList.add("hidden");
            loadOfflineDownloads();
        } else {
            document.getElementById("downloads-section").classList.add("hidden");
            document.getElementById("favorites-container").classList.remove("hidden");
            loadFavorites();
        }

        activeMode = mode;
    }

    function loadOfflineDownloads() {
        const container = document.getElementById("downloads-container");
        const listItems = [];

        // Scan local storage keys
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith("download_")) {
                try {
                    const val = JSON.parse(localStorage.getItem(key));
                    listItems.push(val);
                } catch (e) {
                    console.error("Error parsing download metadata", e);
                }
            }
        }

        if (listItems.length === 0) {
            container.innerHTML = `
                <div class="glass p-8 text-center rounded-2xl border border-white/5 space-y-3">
                    <span class="text-3xl inline-block animate-bounce">📥</span>
                    <h3 class="text-sm font-bold text-white leading-tight">Your Offline downloads list is empty</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">Navigate to any Movie or TV Show details page, and tap the "Download Offline" button to save files here.</p>
                </div>
            `;
            return;
        }

        // Sort by date downloaded
        listItems.sort((a, b) => new Date(b.downloadedAt) - new Date(a.downloadedAt));

        container.innerHTML = listItems.map(item => `
            <div class="glass p-5 rounded-2xl flex flex-col sm:flex-row gap-4 items-center justify-between animate-fadeIn" id="dl-card-${item.type}-${item.id}">
                <div class="flex gap-4 items-center w-full sm:w-auto">
                    <div class="w-16 h-20 bg-slate-900 rounded-xl overflow-hidden flex-shrink-0 border border-white/5">
                        <img src="${item.posterUrl}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white leading-tight">${item.title}</h3>
                        <p class="text-xs text-slate-400 mt-1">${item.size} • ${item.type === 'tv' ? 'TV Series' : 'Movie'} • ${item.quality}</p>
                        <span class="inline-block mt-2 text-[9px] font-extrabold uppercase bg-emerald-600/20 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded-md">COMPLETED</span>
                    </div>
                </div>
                <div class="flex gap-3 w-full sm:w-auto pt-4 sm:pt-0">
                    <a href="/play/${item.type}/${item.id}?offline=true" class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-bold px-5 py-2.5 rounded-xl transition text-xs shadow-lg shadow-violet-500/10">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <span>Play Offline</span>
                    </a>
                    <button onclick="deleteDownload('${item.type}', ${item.id})" class="p-2.5 rounded-xl bg-slate-900/50 border border-white/5 text-slate-400 hover:text-rose-500 transition hover:border-rose-500/20">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
        `).join('');
    }

    function deleteDownload(type, id) {
        if (confirm("Delete this offline download?")) {
            const key = `download_${type}_${id}`;
            localStorage.removeItem(key);
            
            const card = document.getElementById(`dl-card-${type}-${id}`);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    loadOfflineDownloads();
                }, 300);
            }
        }
    }

    function renderExternalSources() {
        const container = document.getElementById("external-sources-container");
        container.innerHTML = externalSources.map(s => `
            <div class="glass p-5 rounded-2xl flex items-center justify-between gap-4 border border-white/5">
                <div class="flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-[#121220] rounded-xl border border-white/5">${s.emoji}</span>
                    <div>
                        <h4 class="text-sm font-bold text-white leading-snug">${s.name}</h4>
                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">${s.desc}</p>
                    </div>
                </div>
                <a href="${s.url}" target="_blank" class="p-2.5 rounded-xl bg-violet-600/10 hover:bg-violet-600/20 text-violet-400 hover:text-violet-300 transition duration-200 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                </a>
            </div>
        `).join('');
    }

    async function loadFavorites() {
        const container = document.getElementById("favorites-container");
        const listItems = [];

        // Scan local storage keys
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith("fav_")) {
                const val = localStorage.getItem(key);
                if (val === 'true') {
                    const parts = key.split("_");
                    const type = parts[1];
                    const id = parseInt(parts[2]);
                    listItems.push({ type, id });
                }
            }
        }

        if (listItems.length === 0) {
            container.innerHTML = `<div class="text-slate-500 text-sm col-span-full py-12 text-center">Your Watchlist is empty. Add titles to favorites to display them here!</div>`;
            return;
        }

        container.innerHTML = Array(listItems.length).fill(0).map(() => `
            <div class="aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
        `).join('');

        try {
            const favoritesData = await Promise.all(listItems.map(item => {
                const endpoint = item.type === 'tv' ? 'tv' : 'movie';
                return fetch(`/api/tmdb/${endpoint}/${item.id}`)
                    .then(r => r.json())
                    .then(data => ({
                        id: item.id,
                        type: item.type,
                        title: data.title || data.name,
                        posterUrl: data.poster_path ? `https://image.tmdb.org/t/p/w342${data.poster_path}` : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image',
                        rating: data.vote_average ? parseFloat(data.vote_average.toFixed(1)) : 0.0,
                        year: (data.release_date || data.first_air_date || '').substring(0, 4)
                    }));
            }));

            container.innerHTML = container.innerHTML = favoritesData.map(item => `
                <a href="/details/${item.type}/${item.id}" class="group flex flex-col gap-2 relative">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                        <img src="${item.posterUrl}" alt="${item.title}" class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3 px-2 py-1 glass rounded-lg flex items-center gap-1 text-[11px] font-extrabold text-amber-400 select-none">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            <span>${item.rating}</span>
                        </div>
                    </div>
                    <div class="px-1 space-y-0.5 leading-tight">
                        <h3 class="text-sm font-bold text-white group-hover:text-violet-400 transition truncate w-full">${item.title}</h3>
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>${item.year}</span>
                            <span class="uppercase text-[9px] font-extrabold bg-[#1E1E2E] px-1.5 py-0.5 rounded border border-white/5">${item.type}</span>
                        </div>
                    </div>
                </a>
            `).join('');

        } catch (err) {
            console.error("Error loading watchlist items:", err);
            container.innerHTML = `<div class="text-rose-500 text-sm col-span-full py-12 text-center">Failed to load Watchlist. Check connection.</div>`;
        }
    }
</script>
@endsection
