@extends('layouts.layout')

@section('title', 'Loading details… — CineMovie')

@section('content')
<div class="relative w-full min-h-screen text-slate-100 select-none pb-12" id="details-container">
    
    <!-- Hero Backdrop Banner -->
    <div class="relative w-full h-[350px] md:h-[550px] overflow-hidden">
        <div id="backdrop-img" class="absolute inset-0 bg-cover bg-center transition-all duration-700 blur-[2px] scale-[1.01]" style="background-image: url('https://placehold.co/1280x720/121220/FFF?text=Loading')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0B0B14] via-[#0B0B14]/40 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0B0B14] via-[#0B0B14]/30 to-transparent"></div>
        
        <!-- Back Button (Floating) -->
        <button onclick="window.history.back()" class="absolute top-6 left-6 p-3 rounded-xl glass hover:bg-white/10 text-white transition z-20">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </button>
    </div>

    <!-- Main Metadata Section (Overlapping) -->
    <div class="max-w-7xl mx-auto px-4 md:px-8 -mt-36 md:-mt-56 relative z-10 space-y-8">
        
        <!-- Main Panel: Poster + Core Details -->
        <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-start">
            <!-- Poster -->
            <div class="w-[180px] md:w-[260px] aspect-[2/3] rounded-3xl overflow-hidden shadow-2xl border border-white/5 self-center md:self-start bg-[#121220] flex-shrink-0">
                <img id="poster-img" src="" alt="Poster" class="w-full h-full object-cover opacity-0 transition-opacity duration-300">
            </div>

            <!-- Details Panel -->
            <div class="flex-1 space-y-4 text-center md:text-left self-end pb-2">
                <div class="flex flex-wrap gap-2 justify-center md:justify-start items-center">
                    <span id="meta-type" class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-violet-600/30 text-violet-400 border border-violet-500/20 rounded-md">Movie</span>
                    <span id="meta-status" class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-[#00B894]/20 text-[#00B894] border border-[#00B894]/20 rounded-md">Released</span>
                </div>
                <h1 id="details-title" class="text-3xl md:text-5xl font-extrabold tracking-tight drop-shadow">Loading Title...</h1>
                
                <div class="flex flex-wrap gap-x-4 gap-y-2 justify-center md:justify-start items-center text-slate-300 text-xs font-semibold">
                    <span id="meta-rating" class="flex items-center gap-1 text-amber-400 font-extrabold">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        <span>0.0</span>
                    </span>
                    <span>•</span>
                    <span id="meta-year">0000</span>
                    <span>•</span>
                    <span id="meta-runtime">00 min</span>
                    <span>•</span>
                    <span id="meta-language">English</span>
                </div>

                <!-- Genre Chips -->
                <div id="genres-row" class="flex flex-wrap gap-2 justify-center md:justify-start py-1"></div>

                <!-- Main Action Buttons -->
                <div class="flex flex-wrap gap-3 justify-center md:justify-start pt-3">
                    <a id="watch-now-btn" href="" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-extrabold px-8 py-3.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition duration-200 shadow-xl shadow-violet-500/20 text-sm">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <span>Watch Now</span>
                    </a>
                    
                    <button onclick="toggleFavorite()" id="favorite-btn" class="p-3.5 rounded-2xl bg-[#1E1E2E] border border-white/5 hover:border-violet-500/20 hover:text-rose-500 transition duration-200 text-slate-300">
                        <svg id="fav-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </button>

                    <button id="download-btn" onclick="openDownloadModal()" class="inline-flex items-center gap-2 bg-[#1E1E2E] border border-white/5 text-slate-300 font-extrabold px-6 py-3.5 rounded-2xl hover:bg-white/5 hover:text-white hover:border-violet-500/20 transition duration-200 text-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Download Links</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Tabs Section -->
        <div class="space-y-6">
            <div class="border-b border-[#1E1E2E] flex gap-6 overflow-x-auto no-scrollbar scroll-smooth">
                <button onclick="switchTab('overview')" id="tab-overview" class="tab-btn pb-3 text-sm font-extrabold border-b-2 border-violet-500 text-white transition-all">Overview</button>
                <button onclick="switchTab('cast')" id="tab-cast" class="tab-btn pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">Cast & Crew</button>
                <button onclick="switchTab('trailers')" id="tab-trailers" class="tab-btn pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">Trailers</button>
                <button onclick="switchTab('reviews')" id="tab-reviews" class="tab-btn pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">Reviews</button>
                <button onclick="switchTab('similar')" id="tab-similar" class="tab-btn pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">Recommendations</button>
            </div>

            <!-- Tab Contents -->
            <div class="relative w-full">
                <!-- Overview Tab Content -->
                <div id="content-overview" class="tab-content space-y-6">
                    <div class="glass p-6 rounded-3xl space-y-4">
                        <h3 class="text-md font-bold text-white">Storyline</h3>
                        <p id="plot-text" class="text-slate-300 text-sm leading-relaxed"></p>
                    </div>

                    <!-- Statistics grid (budget, revenue, etc.) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="stats-grid">
                        <div class="glass p-5 rounded-2xl flex flex-col justify-center">
                            <span class="text-xs font-semibold text-slate-400">Budget</span>
                            <span id="stat-budget" class="text-md font-extrabold text-white mt-1">$0</span>
                        </div>
                        <div class="glass p-5 rounded-2xl flex flex-col justify-center">
                            <span class="text-xs font-semibold text-slate-400">Revenue</span>
                            <span id="stat-revenue" class="text-md font-extrabold text-white mt-1">$0</span>
                        </div>
                        <div class="glass p-5 rounded-2xl flex flex-col justify-center">
                            <span class="text-xs font-semibold text-slate-400">Director</span>
                            <span id="stat-director" class="text-md font-extrabold text-white mt-1">Unknown</span>
                        </div>
                        <div class="glass p-5 rounded-2xl flex flex-col justify-center">
                            <span class="text-xs font-semibold text-slate-400">Ratings Count</span>
                            <span id="stat-votes" class="text-md font-extrabold text-white mt-1">0</span>
                        </div>
                    </div>

                    <!-- Seasons Section (TV Only) -->
                    <div id="seasons-section" class="space-y-4 hidden">
                        <h2 class="text-xl font-bold tracking-tight text-white">Seasons</h2>
                        <div id="seasons-row" class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-1 px-0.5"></div>
                    </div>
                </div>

                <!-- Cast Tab Content -->
                <div id="content-cast" class="tab-content hidden">
                    <div id="cast-list" class="flex gap-4 overflow-x-auto no-scrollbar py-2"></div>
                </div>

                <!-- Trailers Tab Content -->
                <div id="content-trailers" class="tab-content hidden">
                    <div id="trailers-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"></div>
                </div>

                <!-- Reviews Tab Content -->
                <div id="content-reviews" class="tab-content hidden space-y-4">
                    <div id="reviews-list" class="space-y-4"></div>
                </div>

                <!-- Similar Tab Content -->
                <div id="content-similar" class="tab-content hidden">
                    <div id="similar-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Download Links Modal — Dynamic from DB -->
<div id="download-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-end sm:items-center justify-center hidden select-none" onclick="closeDownloadModal(event)">
    <div id="download-modal-panel" class="w-full max-w-lg bg-[#121220] rounded-t-3xl sm:rounded-3xl border border-white/8 shadow-2xl shadow-slate-950/60 overflow-hidden animate-slideUp">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 flex justify-between items-start border-b border-white/5">
            <div>
                <h3 class="text-lg font-extrabold text-white tracking-tight">Download Links</h3>
                <p id="dl-modal-title" class="text-slate-400 text-xs mt-0.5 truncate max-w-[280px]"></p>
            </div>
            <button onclick="closeDownloadModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Quality Filter Tabs (populated dynamically) -->
        <div id="dl-quality-tabs" class="px-6 py-3 border-b border-white/5 flex gap-2 overflow-x-auto no-scrollbar hidden">
            <!-- filled by JS -->
        </div>

        <!-- Links List -->
        <div id="dl-servers-list" class="px-6 py-4 space-y-3 max-h-80 overflow-y-auto scrollbar-thin">
            <div class="flex items-center justify-center py-10">
                <div class="w-5 h-5 border-2 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <!-- Manage / Disclaimer footer -->
        <div class="px-6 py-3.5 border-t border-white/5 flex items-center justify-between gap-3">
            <p class="text-[10px] text-slate-500 leading-relaxed flex-1">
                ⚠️ CineMovie does not host copyrighted content. Links are sourced externally.
            </p>
            <a href="/admin/download-manager" target="_blank" class="flex-shrink-0 text-[10px] font-bold text-violet-400 hover:text-violet-300 transition flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Manage
            </a>
        </div>
    </div>
</div>

<!-- Season Episodes Modal Drawer -->
<div id="episodes-drawer" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex justify-end hidden select-none">
    <div class="w-full max-w-lg bg-[#121220] h-full flex flex-col border-l border-white/5 animate-slideLeft">
        <!-- Drawer Header -->
        <div class="px-6 py-5 border-b border-[#1E1E2E] flex justify-between items-center bg-[#1E1E2E]/20">
            <div>
                <h3 id="drawer-title" class="text-lg font-extrabold text-white">Season 1</h3>
                <p id="drawer-subtitle" class="text-slate-400 text-xs mt-0.5">0 Episodes</p>
            </div>
            <button onclick="closeEpisodes()" class="p-2 rounded-xl bg-[#1E1E2E] border border-white/5 hover:bg-white/5 transition">
                <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <!-- Episode List Scrollable Area -->
        <div id="episodes-list" class="flex-1 overflow-y-auto px-6 py-4 space-y-4 scrollbar-thin"></div>
    </div>
</div>

<script>
    const type = "{{ $type }}";
    const id = {{ $id }};
    let targetType = type;
    let targetTmdbId = id;
    let isFavorite = false;
    let detailData = {};
    let downloadProgressInterval;
    let isDownloading = false;

    document.addEventListener("DOMContentLoaded", () => {
        loadDetails();
        checkFavoriteStatus();
        checkDownloadStatus();
    });

    // ── Download Modal — Dynamic from DB ─────────────────────────────────────
    let dlAllLinks      = [];   // all links for this content
    let dlActiveQuality = null; // currently selected quality tab

    async function openDownloadModal() {
        const title = detailData.title || detailData.name || 'Unknown Title';
        const year  = (detailData.release_date || detailData.first_air_date || '').substring(0, 4);

        document.getElementById('dl-modal-title').innerText = `${title}${year ? ' (' + year + ')' : ''}`;
        document.getElementById('dl-servers-list').innerHTML = `
            <div class="flex items-center justify-center py-10">
                <div class="w-5 h-5 border-2 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
            </div>`;
        document.getElementById('dl-quality-tabs').classList.add('hidden');
        document.getElementById('download-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        try {
            const links = await fetch(`/api/download-links/${type}/${id}`).then(r => r.json());
            dlAllLinks = links;

            if (links.length === 0) {
                renderNoLinksState(title, year);
                return;
            }

            // Build unique quality tabs from available links
            const qualities = [...new Set(links.map(l => l.quality))];
            const qualityTabsEl = document.getElementById('dl-quality-tabs');
            qualityTabsEl.innerHTML = qualities.map((q, i) => `
                <button onclick="selectDlQuality('${q}')" id="dq-${q.replace(/\s/g,'_')}"
                    class="dl-q-btn flex-shrink-0 px-4 py-1.5 rounded-xl text-xs font-bold border transition ${i === 0 ? 'border-violet-500/30 bg-violet-500/10 text-violet-300' : 'border-white/5 bg-[#1E1E2E] text-slate-300 hover:border-violet-500/30 hover:text-violet-300'}">
                    ${q}
                </button>
            `).join('');
            qualityTabsEl.classList.remove('hidden');

            // Show first quality
            selectDlQuality(qualities[0]);

        } catch(e) {
            document.getElementById('dl-servers-list').innerHTML = `
                <div class="py-10 text-center">
                    <p class="text-rose-400 text-sm font-bold">Failed to load download links</p>
                    <p class="text-slate-500 text-xs mt-1">Check connection or try again later</p>
                </div>`;
        }
    }

    function selectDlQuality(quality) {
        dlActiveQuality = quality;

        // Highlight active tab
        document.querySelectorAll('.dl-q-btn').forEach(btn => {
            btn.className = 'dl-q-btn flex-shrink-0 px-4 py-1.5 rounded-xl text-xs font-bold border transition border-white/5 bg-[#1E1E2E] text-slate-300 hover:border-violet-500/30 hover:text-violet-300';
        });
        const activeBtn = document.getElementById(`dq-${quality.replace(/\s/g,'_')}`);
        if (activeBtn) activeBtn.className = 'dl-q-btn flex-shrink-0 px-4 py-1.5 rounded-xl text-xs font-bold border transition border-violet-500/30 bg-violet-500/10 text-violet-300';

        // Filter links by quality
        const filtered = dlAllLinks.filter(l => l.quality === quality);
        renderDlLinks(filtered);
    }

    function renderDlLinks(links) {
        const container = document.getElementById('dl-servers-list');
        if (links.length === 0) {
            container.innerHTML = `<div class="py-8 text-center text-slate-500 text-sm">No servers for this quality.</div>`;
            return;
        }

        container.innerHTML = links.map((link, index) => {
            const icon = link.server_icon || '🔗';
            const colors = [
                { border:'border-emerald-500/30', bg:'bg-emerald-500/5', hover:'hover:border-emerald-500/30 hover:bg-emerald-500/5', iconBg:'bg-emerald-500/10 border-emerald-500/20', textHover:'group-hover:text-emerald-400', arrowHover:'group-hover:text-emerald-400' },
                { border:'border-violet-500/30',  bg:'bg-violet-500/5',  hover:'hover:border-violet-500/30 hover:bg-violet-500/5',  iconBg:'bg-violet-500/10 border-violet-500/20',  textHover:'group-hover:text-violet-400',  arrowHover:'group-hover:text-violet-400' },
                { border:'border-sky-500/30',     bg:'bg-sky-500/5',     hover:'hover:border-sky-500/30 hover:bg-sky-500/5',         iconBg:'bg-sky-500/10 border-sky-500/20',         textHover:'group-hover:text-sky-400',     arrowHover:'group-hover:text-sky-400' },
                { border:'border-amber-500/30',   bg:'bg-amber-500/5',   hover:'hover:border-amber-500/30 hover:bg-amber-500/5',     iconBg:'bg-amber-500/10 border-amber-500/20',     textHover:'group-hover:text-amber-400',   arrowHover:'group-hover:text-amber-400' },
                { border:'border-fuchsia-500/30', bg:'bg-fuchsia-500/5', hover:'hover:border-fuchsia-500/30 hover:bg-fuchsia-500/5', iconBg:'bg-fuchsia-500/10 border-fuchsia-500/20', textHover:'group-hover:text-fuchsia-400', arrowHover:'group-hover:text-fuchsia-400' },
                { border:'border-rose-500/30',    bg:'bg-rose-500/5',    hover:'hover:border-rose-500/30 hover:bg-rose-500/5',       iconBg:'bg-rose-500/10 border-rose-500/20',       textHover:'group-hover:text-rose-400',    arrowHover:'group-hover:text-rose-400' },
            ];
            const c = colors[index % colors.length];

            return `
                <a href="${link.download_url}" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-3 p-3.5 rounded-2xl bg-[#1E1E2E] border border-white/5 ${c.hover} transition group cursor-pointer">
                    <div class="w-9 h-9 rounded-xl ${c.iconBg} flex items-center justify-center flex-shrink-0 text-base">
                        ${icon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="block text-sm font-bold text-white ${c.textHover} transition truncate">
                            ${link.server_name}
                            <span class="text-[10px] font-semibold text-slate-500 ml-1">Server ${index + 1}</span>
                        </span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] font-bold text-violet-400 bg-violet-500/10 rounded-md px-1.5 py-0.5">${link.quality}</span>
                            <span class="text-[10px] text-slate-400">${link.language || 'English'}</span>
                            ${link.notes ? `<span class="text-[10px] text-slate-500">· ${link.notes}</span>` : ''}
                        </div>
                    </div>
                    ${link.file_size ? `<span class="text-[10px] font-bold text-slate-500 bg-white/5 rounded-lg px-2 py-1 flex-shrink-0">${link.file_size}</span>` : ''}
                    <svg class="w-4 h-4 text-slate-500 ${c.arrowHover} transition flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>`;
        }).join('');
    }

    function renderNoLinksState(title, year) {
        const q = encodeURIComponent(`${title} ${year}`);
        document.getElementById('dl-servers-list').innerHTML = `
            <div class="py-6 space-y-4">
                <div class="text-center py-4">
                    <p class="text-slate-400 text-sm font-bold">No download links added yet</p>
                    <p class="text-slate-500 text-xs mt-1">An admin can add links via the Download Manager</p>
                    <a href="/admin/download-manager" target="_blank" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-violet-400 hover:text-violet-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Open Download Manager
                    </a>
                </div>
                <div class="border-t border-white/5 pt-4 space-y-2">
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">External Search</p>
                    <a href="https://yts.mx/movies/${encodeURIComponent((title||'').toLowerCase().replace(/[^a-z0-9]+/g,'-'))}-${year}" target="_blank"
                        class="flex items-center gap-3 p-3 rounded-2xl bg-[#1E1E2E] border border-white/5 hover:border-emerald-500/30 hover:bg-emerald-500/5 transition group">
                        <span class="text-base">🎬</span>
                        <div class="flex-1"><span class="text-xs font-bold text-white group-hover:text-emerald-400 transition">Search on YTS</span><br><span class="text-[10px] text-slate-400">Best movie torrents</span></div>
                        <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-emerald-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <a href="https://1337x.to/search/${q}/1/" target="_blank"
                        class="flex items-center gap-3 p-3 rounded-2xl bg-[#1E1E2E] border border-white/5 hover:border-violet-500/30 hover:bg-violet-500/5 transition group">
                        <span class="text-base">⚡</span>
                        <div class="flex-1"><span class="text-xs font-bold text-white group-hover:text-violet-400 transition">Search on 1337x</span><br><span class="text-[10px] text-slate-400">Multiple mirrors & servers</span></div>
                        <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-violet-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>`;
    }

    function closeDownloadModal(event) {
        if (event && event.target !== document.getElementById('download-modal')) return;
        document.getElementById('download-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function checkDownloadStatus() {
        // no-op — download is now handled by the dynamic modal
    }


    async function loadDetails() {
        let customMovieData = null;

        try {
            if (type === 'custom') {
                customMovieData = await fetch(`/api/custom-movie/${id}`).then(r => r.json());
                targetTmdbId = customMovieData.tmdb_id;
                targetType = customMovieData.type;
            }

            const endpoint = targetType === 'tv' ? 'tv' : 'movie';
            const [details, credits, videos, similar, reviews] = await Promise.all([
                fetch(`/api/tmdb/${endpoint}/${targetTmdbId}`).then(r => r.json()),
                fetch(`/api/tmdb/${endpoint}/${targetTmdbId}/credits`).then(r => r.json()),
                fetch(`/api/tmdb/${endpoint}/${targetTmdbId}/videos`).then(r => r.json()),
                fetch(`/api/tmdb/${endpoint}/${targetTmdbId}/similar`).then(r => r.json()),
                fetch(`/api/tmdb/${endpoint}/${targetTmdbId}/reviews`).then(r => r.json())
            ]);

            detailData = details;
            
            // Set Page Title
            const displayTitle = customMovieData ? customMovieData.title : (details.title || details.name);
            document.title = `${displayTitle} — CineMovie`;

            // Images
            const backdropPath = customMovieData && customMovieData.backdrop_path ? customMovieData.backdrop_path : details.backdrop_path;
            const fullBackdropUrl = backdropPath 
                ? (backdropPath.startsWith('http') ? backdropPath : `https://image.tmdb.org/t/p/w1280${backdropPath}`)
                : "https://placehold.co/1280x720/121220/FFF?text=No+Backdrop";
            document.getElementById("backdrop-img").style.backgroundImage = `url('${fullBackdropUrl}')`;
            
            const poster = document.getElementById("poster-img");
            const posterPath = customMovieData && customMovieData.poster_path ? customMovieData.poster_path : details.poster_path;
            poster.src = posterPath 
                ? (posterPath.startsWith('http') ? posterPath : `https://image.tmdb.org/t/p/w500${posterPath}`)
                : 'https://placehold.co/500x750/1E1E2E/FFF?text=No+Poster';
            poster.classList.remove("opacity-0");

            // Meta Info
            document.getElementById("meta-type").innerText = targetType === 'tv' ? 'TV Series' : 'Movie';
            document.getElementById("meta-status").innerText = details.status || 'Released';
            document.getElementById("details-title").innerText = displayTitle;
            
            const voteAverage = customMovieData ? customMovieData.rating : details.vote_average;
            document.getElementById("meta-rating").querySelector("span").innerText = voteAverage ? voteAverage.toFixed(1) : '0.0';
            
            const releaseDate = details.release_date || details.first_air_date || '';
            const yearStr = customMovieData && customMovieData.year ? customMovieData.year : (releaseDate ? releaseDate.substring(0, 4) : 'Unknown');
            document.getElementById("meta-year").innerText = yearStr;
            
            const runtime = customMovieData && customMovieData.runtime ? customMovieData.runtime : (details.runtime ? `${details.runtime} min` : null);
            const episodeRuntime = details.episode_run_time && details.episode_run_time.length > 0 ? details.episode_run_time[0] : null;
            document.getElementById("meta-runtime").innerText = runtime 
                ? (runtime.includes('min') ? runtime : `${runtime} min`)
                : (episodeRuntime ? `${episodeRuntime} min/ep` : '-- min');

            const spokenLang = customMovieData ? customMovieData.language : (details.spoken_languages && details.spoken_languages.length > 0 ? details.spoken_languages[0].english_name : 'English');
            document.getElementById("meta-language").innerText = spokenLang;

            // Watch Now Link
            document.getElementById("watch-now-btn").href = type === 'custom' ? `/play/custom/${id}` : `/play/${type}/${id}`;

            // Genres
            document.getElementById("genres-row").innerHTML = (details.genres || []).map(g => `
                <span class="px-3.5 py-1.5 text-xs font-bold bg-[#1E1E2E] border border-white/5 text-slate-300 rounded-full select-none">${g.name}</span>
            `).join('');

            // Overview Tab
            document.getElementById("plot-text").innerText = (customMovieData && customMovieData.overview) ? customMovieData.overview : (details.overview || 'No storyline description is currently available for this title.');
            document.getElementById("stat-budget").innerText = details.budget ? formatCurrency(details.budget) : '$0';
            document.getElementById("stat-revenue").innerText = details.revenue ? formatCurrency(details.revenue) : '$0';
            
            const director = (credits.crew || []).find(c => c.job === 'Director');
            document.getElementById("stat-director").innerText = director ? director.name : 'N/A';
            document.getElementById("stat-votes").innerText = details.vote_count ? details.vote_count.toLocaleString() : '0';

            // Seasons Section (TV Only)
            if (targetType === 'tv' && details.seasons) {
                document.getElementById("seasons-section").classList.remove("hidden");
                const cleanSeasons = details.seasons.filter(s => s.season_number > 0);
                document.getElementById("seasons-row").innerHTML = cleanSeasons.map(s => `
                    <button onclick="openEpisodes(${s.season_number}, '${s.name.replace(/'/g, "\\'")}')" class="min-w-[125px] md:min-w-[155px] text-left group flex flex-col gap-2 relative">
                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                            <img src="${s.poster_path ? 'https://image.tmdb.org/t/p/w342' + s.poster_path : 'https://placehold.co/342x513/1E1E2E/FFF?text=Season+' + s.season_number}" alt="${s.name}" class="w-full h-full object-cover">
                        </div>
                        <div class="px-1">
                            <h4 class="text-xs font-bold text-white group-hover:text-violet-400 transition truncate">${s.name}</h4>
                            <span class="text-[10px] text-slate-400 font-semibold">${s.episode_count} Episodes</span>
                        </div>
                    </button>
                `).join('');
            }

            // Cast Tab
            const castItems = (credits.cast || []).slice(0, 15);
            const castContainer = document.getElementById("cast-list");
            if (castItems.length === 0) {
                castContainer.innerHTML = `<div class="text-slate-500 text-sm py-4">Cast members information not available</div>`;
            } else {
                castContainer.innerHTML = castItems.map(c => `
                    <a href="/actor/${c.id}" class="min-w-[95px] flex flex-col items-center text-center gap-2 group">
                        <div class="w-16 h-16 rounded-full overflow-hidden border border-white/10 group-hover:border-violet-500 bg-[#1E1E2E] transition">
                            <img src="${c.profile_path ? 'https://image.tmdb.org/t/p/w185' + c.profile_path : 'https://placehold.co/185x185/1E1E2E/FFF?text=' + encodeURIComponent(c.name.substring(0,2))}" alt="${c.name}" class="w-full h-full object-cover">
                        </div>
                        <div class="px-1 flex flex-col leading-tight">
                            <span class="text-[11px] font-bold text-white truncate max-w-[85px]">${c.name}</span>
                            <span class="text-[9px] text-slate-400 truncate max-w-[85px] mt-0.5">${c.character}</span>
                        </div>
                    </a>
                `).join('');
            }

            // Trailers Tab
            const trailers = (videos.results || []).filter(v => v.site === 'YouTube' && (v.type === 'Trailer' || v.type === 'Teaser')).slice(0, 6);
            const trailersContainer = document.getElementById("trailers-grid");
            if (trailers.length === 0) {
                trailersContainer.innerHTML = `<div class="text-slate-500 text-sm col-span-full py-8 text-center">No official trailers or teasers found</div>`;
            } else {
                trailersContainer.innerHTML = trailers.map(v => `
                    <div class="glass overflow-hidden rounded-2xl border border-white/5 space-y-3 p-3">
                        <div class="aspect-video relative rounded-xl overflow-hidden">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/${v.key}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <h4 class="text-xs font-bold text-white px-1 line-clamp-1">${v.name}</h4>
                    </div>
                `).join('');
            }

            // Reviews Tab
            const reviewItems = (reviews.results || []).slice(0, 5);
            const reviewsContainer = document.getElementById("reviews-list");
            if (reviewItems.length === 0) {
                reviewsContainer.innerHTML = `<div class="text-slate-500 text-sm py-8 text-center">No reviews have been written for this title</div>`;
            } else {
                reviewsContainer.innerHTML = reviewItems.map(r => {
                    const ratingStr = r.author_details && r.author_details.rating ? `
                        <span class="flex items-center gap-1 text-amber-500 text-xs font-extrabold bg-amber-500/10 px-2 py-0.5 rounded-lg border border-amber-500/20">
                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            ${r.author_details.rating}
                        </span>
                    ` : '';
                    return `
                        <div class="glass p-5 rounded-2xl space-y-3">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full overflow-hidden border border-white/10 bg-[#1E1E2E]">
                                        <img src="https://placehold.co/32x32/1E1E2E/FFF?text=${r.author.substring(0,1)}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-xs font-bold text-white">${r.author}</span>
                                </div>
                                ${ratingStr}
                            </div>
                            <p class="text-slate-300 text-xs leading-relaxed line-clamp-3 hover:line-clamp-none transition-all cursor-pointer">${r.content}</p>
                        </div>
                    `;
                }).join('');
            }

            // Similar Tab
            const similarItems = (similar.results || []).slice(0, 10);
            const similarContainer = document.getElementById("similar-grid");
            if (similarItems.length === 0) {
                similarContainer.innerHTML = `<div class="text-slate-500 text-sm col-span-full py-8 text-center">No recommendation matches found</div>`;
            } else {
                similarContainer.innerHTML = similarItems.map(s => {
                    const sReleaseDate = s.release_date || s.first_air_date || '';
                    const sYear = sReleaseDate ? sReleaseDate.substring(0, 4) : '';
                    return `
                        <a href="/details/${type}/${s.id}" class="group flex flex-col gap-2 relative">
                            <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl">
                                <img src="${s.poster_path ? 'https://image.tmdb.org/t/p/w342' + s.poster_path : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image'}" alt="${s.title || s.name}" class="w-full h-full object-cover">
                            </div>
                            <div class="px-1">
                                <h4 class="text-xs font-bold text-white group-hover:text-violet-400 transition truncate">${s.title || s.name}</h4>
                                <span class="text-[10px] text-slate-400 font-semibold">${sYear}</span>
                            </div>
                        </a>
                    `;
                }).join('');
            }

        } catch (err) {
            console.error("Error loading movie/tv show details:", err);
        }
    }

    function switchTab(tabId) {
        // Toggle tab highlights
        document.querySelectorAll(".tab-btn").forEach(btn => {
            btn.className = "tab-btn pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
        });
        document.getElementById(`tab-${tabId}`).className = "tab-btn pb-3 text-sm font-extrabold border-b-2 border-violet-500 text-white transition-all";

        // Toggle contents
        document.querySelectorAll(".tab-content").forEach(content => {
            content.classList.add("hidden");
        });
        document.getElementById(`content-${tabId}`).classList.remove("hidden");
    }

    // Interactive Season Episodes Drawer
    async function openEpisodes(seasonNum, seasonName) {
        document.getElementById("drawer-title").innerText = seasonName;
        const list = document.getElementById("episodes-list");
        list.innerHTML = `<div class="text-slate-500 text-sm py-12 text-center animate-pulse">Loading episodes...</div>`;
        document.getElementById("episodes-drawer").classList.remove("hidden");

        try {
            const res = await fetch(`/api/tmdb/tv/${targetTmdbId}/season/${seasonNum}`).then(r => r.json());
            const eps = res.episodes || [];
            
            document.getElementById("drawer-subtitle").innerText = `${eps.length} Episodes`;
            
            list.innerHTML = eps.map(e => {
                const playUrl = type === 'custom'
                    ? `/play/custom/${id}?season=${seasonNum}&episode=${e.episode_number}`
                    : `/play/tv/${id}?season=${seasonNum}&episode=${e.episode_number}`;
                return `
                <div class="glass p-4 rounded-2xl flex flex-col sm:flex-row gap-4 hover:border-violet-500/20 transition-all duration-200">
                    <div class="w-full sm:w-[130px] aspect-video rounded-xl overflow-hidden bg-[#1E1E2E] border border-white/5 flex-shrink-0 relative">
                        <img src="${e.still_path ? 'https://image.tmdb.org/t/p/w300' + e.still_path : 'https://placehold.co/300x169/1E1E2E/FFF?text=Episode+' + e.episode_number}" class="w-full h-full object-cover">
                        <!-- Play Icon overlay -->
                        <a href="${playUrl}" class="absolute inset-0 flex items-center justify-center bg-slate-950/30 hover:bg-slate-950/60 transition group">
                            <span class="w-8 h-8 rounded-full bg-violet-600 flex items-center justify-center text-white shadow-lg shadow-violet-500/20 group-hover:scale-110 transition">
                                <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                        </a>
                    </div>
                    <div class="flex-1 space-y-1.5 flex flex-col justify-center leading-tight">
                        <div class="flex justify-between items-start gap-2">
                            <h4 class="text-xs font-bold text-white line-clamp-1">Ep ${e.episode_number}: ${e.name}</h4>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-2">${e.overview || 'No description available.'}</p>
                        <div class="flex items-center gap-3 text-[9px] font-semibold text-slate-500 pt-1">
                            <span>${e.air_date ? e.air_date : ''}</span>
                            <span>•</span>
                            <span>${e.runtime ? e.runtime + ' min' : '45 min'}</span>
                        </div>
                    </div>
                </div>
            `; }).join('');

        } catch (err) {
            console.error("Error loading season episodes:", err);
            list.innerHTML = `<div class="text-rose-500 text-sm py-12 text-center">Failed to load episodes. Please try again.</div>`;
        }
    }

    function closeEpisodes() {
        document.getElementById("episodes-drawer").classList.add("hidden");
    }

    // Favorite Status
    function checkFavoriteStatus() {
        const key = `fav_${type}_${id}`;
        isFavorite = localStorage.getItem(key) === 'true';
        updateFavoriteButton();
    }

    function toggleFavorite() {
        isFavorite = !isFavorite;
        const key = `fav_${type}_${id}`;
        localStorage.setItem(key, isFavorite ? 'true' : 'false');
        updateFavoriteButton();
    }

    function updateFavoriteButton() {
        const btn = document.getElementById("favorite-btn");
        const icon = document.getElementById("fav-icon");
        if (isFavorite) {
            btn.className = "p-3.5 rounded-2xl bg-rose-600/10 border border-rose-500/20 text-rose-500 hover:bg-rose-600/20 transition duration-200";
            icon.setAttribute("fill", "currentColor");
        } else {
            btn.className = "p-3.5 rounded-2xl bg-[#1E1E2E] border border-white/5 hover:border-violet-500/20 hover:text-rose-500 transition duration-200 text-slate-300";
            icon.setAttribute("fill", "none");
        }
    }

    function formatCurrency(num) {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(num);
    }
</script>
@endsection
