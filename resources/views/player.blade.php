@extends('layouts.layout')

@section('title', 'Playing Video — CineMovie')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-6 max-w-5xl mx-auto select-none">
    
    <!-- Top Back Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/details/{{ $type }}/{{ $id }}" class="p-3.5 rounded-xl bg-[#1E1E2E] border border-white/5 text-slate-300 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h1 id="player-title" class="text-md md:text-lg font-bold text-white leading-tight">Loading Stream...</h1>
                <p id="player-subtitle" class="text-xs text-slate-400 font-semibold mt-0.5"></p>
            </div>
        </div>
        
        <!-- Quick Reload -->
        <button onclick="reloadPlayer()" class="p-3.5 rounded-xl bg-[#1E1E2E] border border-white/5 text-slate-300 hover:text-white hover:border-violet-500/20 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3" /></svg>
        </button>
    </div>

    <!-- 16:9 Video Frame Container -->
    <div class="relative w-full aspect-video rounded-3xl overflow-hidden shadow-2xl border border-white/5 bg-black">
        <!-- Iframe loader spinner -->
        <div id="player-spinner" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 z-10 gap-3">
            <div class="w-8 h-8 border-2 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs text-slate-400 font-bold">Connecting to streaming server...</span>
        </div>
        
        <iframe id="video-iframe" class="w-full h-full" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media; picture-in-picture"></iframe>
    </div>

    <!-- TV Episode Navigation (Only shown for TV shows) -->
    <div id="tv-navigation" class="hidden glass p-4 rounded-2xl flex justify-between items-center gap-4">
        <button id="prev-ep-btn" onclick="navigateEpisode(-1)" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#121220] border border-white/5 text-xs font-bold text-slate-300 hover:text-white disabled:opacity-40 disabled:pointer-events-none transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            <span>Previous Episode</span>
        </button>
        <span id="current-ep-label" class="text-xs font-extrabold text-violet-400">Season 1 : Episode 1</span>
        <button id="next-ep-btn" onclick="navigateEpisode(1)" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#121220] border border-white/5 text-xs font-bold text-slate-300 hover:text-white disabled:opacity-40 disabled:pointer-events-none transition">
            <span>Next Episode</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        </button>
    </div>

    <!-- Server Pickers & Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Servers list -->
        <div class="md:col-span-2 space-y-3">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <span>Select Stream Server</span>
            </h3>
            <div id="servers-container" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
        </div>

        <!-- Utilities -->
        <div class="space-y-3">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Stream Controls</h3>
            <div class="flex flex-col gap-2">
                <button onclick="openExternal()" class="flex items-center justify-between p-4 bg-[#1E1E2E] border border-white/5 rounded-2xl text-xs font-semibold text-slate-300 hover:text-white hover:border-violet-500/20 transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        <span>Open in Browser</span>
                    </span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>

                <button onclick="shareLink()" class="flex items-center justify-between p-4 bg-[#1E1E2E] border border-white/5 rounded-2xl text-xs font-semibold text-slate-300 hover:text-white hover:border-violet-500/20 transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 10.742l4.022-2.011m0 0A3.002 3.002 0 1118 7.5a3.002 3.002 0 01-5.294 2.011m0 0L8.684 13.258m0 0A3.002 3.002 0 113 12.5a3.002 3.002 0 015.684.758z" /></svg>
                        <span>Share Stream URL</span>
                    </span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>

                <button onclick="reportBroken()" class="flex items-center gap-2 text-[11px] text-slate-500 hover:text-slate-400 transition select-none pt-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Report Broken Link</span>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Simple Toast Message -->
<div id="toast" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 glass px-5 py-3 rounded-xl text-xs font-bold text-white shadow-xl pointer-events-none opacity-0 transition-opacity duration-300 z-50"></div>

<script>
    const type = "{{ $type }}";
    const id = {{ $id }};
    let targetType = type;
    let targetTmdbId = id;
    let activeSeason = {{ $season ?? 'null' }};
    let activeEpisode = {{ $episode ?? 'null' }};
    let activeServer = 0;
    let tvDetails = null;
    let servers = [];
    let customMovieData = null;

    document.addEventListener("DOMContentLoaded", () => {
        fetchServers();
        initIframeListener();
    });

    async function fetchServers() {
        try {
            if (type === 'custom' || id >= 1000000000) {
                const dbId = id >= 1000000000 ? id - 1000000000 : id;
                customMovieData = await fetch(`/api/custom-movie/${dbId}`).then(r => r.json());
                targetType = customMovieData.type;
                targetTmdbId = customMovieData.tmdb_id;
                
                // Set initial season/episode if TV
                if (targetType === 'tv') {
                    if (!activeSeason) activeSeason = 1;
                    if (!activeEpisode) activeEpisode = 1;
                }
                
                // Get custom stream servers
                resolveCustomStreams();
            } else {
                const res = await fetch('/api/config/servers');
                servers = await res.json();
            }

            renderServersList();
            await loadMetadata();
        } catch (err) {
            console.error("Error loading dynamic servers configuration:", err);
        }
    }

    function resolveCustomStreams() {
        if (!customMovieData) return;
        const allStreams = customMovieData.streams || [];
        
        if (targetType === 'tv') {
            // Filter stream servers for current season/episode
            const matches = allStreams.filter(s => s.season_number === activeSeason && s.episode_number === activeEpisode);
            if (matches.length > 0) {
                servers = matches.map(s => ({ name: s.server_name, icon: s.server_icon || '🔗', stream_url: s.stream_url }));
            } else {
                // Try fallback to streams with null season/episode or list all
                const generic = allStreams.filter(s => !s.season_number);
                servers = (generic.length > 0 ? generic : allStreams).map(s => ({ name: s.server_name, icon: s.server_icon || '🔗', stream_url: s.stream_url }));
            }
        } else {
            // Movie - list all streams
            servers = allStreams.map(s => ({ name: s.server_name, icon: s.server_icon || '🔗', stream_url: s.stream_url }));
        }

        // Default servers fallback if empty
        if (servers.length === 0) {
            servers = [{ name: 'Default Server', icon: '🔗', stream_url: `https://vidsrc.to/embed/${targetType}/${targetTmdbId}${targetType === 'tv' ? '/' + activeSeason + '/' + activeEpisode : ''}` }];
        }
    }

    function buildServerUrl(server, tmdbId, s, e) {
        if (type === 'custom' || id >= 1000000000) {
            return server.stream_url;
        }
        if (type === 'tv' && s && e) {
            return server.tv_url_template
                .replaceAll('{id}', tmdbId)
                .replaceAll('{season}', s)
                .replaceAll('{episode}', e);
        }
        return server.movie_url_template
            .replaceAll('{id}', tmdbId);
    }

    async function loadMetadata() {
        const endpoint = targetType === 'tv' ? 'tv' : 'movie';
        try {
            const data = await fetch(`/api/tmdb/${endpoint}/${targetTmdbId}`).then(r => r.json());
            tvDetails = data;
            
            const title = customMovieData ? customMovieData.title : (data.title || data.name);
            document.title = `Playing: ${title} — CineMovie`;
            document.getElementById("player-title").innerText = title;

            if (targetType === 'tv') {
                document.getElementById("tv-navigation").classList.remove("hidden");
                updateTvNavLabel();
                verifyNavigationButtons();
            } else {
                const releaseYear = customMovieData && customMovieData.year ? customMovieData.year : (data.release_date ? data.release_date.substring(0, 4) : '');
                document.getElementById("player-subtitle").innerText = `${releaseYear} • ${data.runtime ? data.runtime + ' min' : ''}`;
            }

            // Load iframe URL
            loadIframeSource();

        } catch (err) {
            console.error("Error loading stream metadata:", err);
        }
    }

    function renderServersList() {
        const container = document.getElementById("servers-container");
        if (servers.length === 0) {
            container.innerHTML = `<div class="text-xs text-slate-500 py-3">No streaming servers configured.</div>`;
            return;
        }
        
        // Reset activeServer index if out of range
        if (activeServer >= servers.length) {
            activeServer = 0;
        }

        container.innerHTML = servers.map((s, idx) => `
            <button onclick="switchServer(${idx})" 
                    class="flex items-center gap-3 p-4 rounded-2xl text-left transition select-none border
                    ${idx === activeServer 
                        ? 'bg-violet-600/10 border-violet-500 text-violet-400' 
                        : 'bg-[#1E1E2E] border-white/5 text-slate-300 hover:text-white hover:border-white/10'}"
                    id="server-${idx}">
                <span class="text-lg">${s.icon}</span>
                <div>
                    <h4 class="text-xs font-bold">${s.name}</h4>
                    <span class="text-[9px] text-slate-550 font-semibold">${idx === activeServer ? 'Connected' : 'Click to connect'}</span>
                </div>
            </button>
        `).join('');
    }

    function loadIframeSource() {
        if (servers.length === 0) return;
        const spinner = document.getElementById("player-spinner");
        spinner.classList.remove("hidden");

        const iframe = document.getElementById("video-iframe");
        const url = buildServerUrl(servers[activeServer], targetTmdbId, activeSeason, activeEpisode);
        iframe.src = url;
    }

    function initIframeListener() {
        const iframe = document.getElementById("video-iframe");
        const spinner = document.getElementById("player-spinner");
        iframe.addEventListener("load", () => {
            spinner.classList.add("hidden");
        });
    }

    function switchServer(idx) {
        if (idx === activeServer) return;
        
        // update active style
        const oldBtn = document.getElementById(`server-${activeServer}`);
        if (oldBtn) {
            oldBtn.className = "flex items-center gap-3 p-4 rounded-2xl text-left transition select-none border bg-[#1E1E2E] border-white/5 text-slate-300 hover:text-white hover:border-white/10";
            const oldStatus = oldBtn.querySelector("span.text-slate-500, span.text-slate-550");
            if (oldStatus) oldStatus.innerText = "Click to connect";
        }
        
        activeServer = idx;
        
        const newBtn = document.getElementById(`server-${idx}`);
        if (newBtn) {
            newBtn.className = "flex items-center gap-3 p-4 rounded-2xl text-left transition select-none border bg-violet-600/10 border-violet-500 text-violet-400";
            const newStatus = newBtn.querySelector("span.text-slate-500, span.text-slate-550");
            if (newStatus) newStatus.innerText = "Connected";
        }

        loadIframeSource();
        showToast(`Switched to server: ${servers[idx].name}`);
    }

    function reloadPlayer() {
        loadIframeSource();
        showToast("Reloading media stream...");
    }

    function openExternal() {
        if (servers.length === 0) return;
        const url = buildServerUrl(servers[activeServer], targetTmdbId, activeSeason, activeEpisode);
        window.open(url, '_blank');
    }

    function shareLink() {
        const shareUrl = window.location.href;
        navigator.clipboard.writeText(shareUrl).then(() => {
            showToast("Copied page stream link to clipboard!");
        });
    }

    function reportBroken() {
        showToast("Broken link reported! Thank you for helping keep links active.");
    }

    function updateTvNavLabel() {
        document.getElementById("current-ep-label").innerText = `Season ${activeSeason} : Episode ${activeEpisode}`;
        document.getElementById("player-subtitle").innerText = `S${activeSeason} E${activeEpisode}`;
    }

    async function verifyNavigationButtons() {
        if (!tvDetails) return;
        
        const prevBtn = document.getElementById("prev-ep-btn");
        const nextBtn = document.getElementById("next-ep-btn");
        
        prevBtn.disabled = activeEpisode === 1 && activeSeason === 1;

        // Check if there is a next episode by pulling next season if needed
        try {
            const currentSeasonData = tvDetails.seasons.find(s => s.season_number === activeSeason);
            if (currentSeasonData) {
                nextBtn.disabled = activeEpisode >= currentSeasonData.episode_count && activeSeason >= tvDetails.seasons.filter(s => s.season_number > 0).length;
            }
        } catch (_) {
            nextBtn.disabled = false; // default to enable fallback
        }
    }

    function navigateEpisode(direction) {
        if (!tvDetails) return;

        const currentSeasonData = tvDetails.seasons.find(s => s.season_number === activeSeason);
        
        if (direction === 1) { // Next
            if (activeEpisode < currentSeasonData.episode_count) {
                activeEpisode++;
            } else {
                const totalSeasons = tvDetails.seasons.filter(s => s.season_number > 0).length;
                if (activeSeason < totalSeasons) {
                    activeSeason++;
                    activeEpisode = 1;
                }
            }
        } else { // Previous
            if (activeEpisode > 1) {
                activeEpisode--;
            } else if (activeSeason > 1) {
                activeSeason--;
                const prevSeasonData = tvDetails.seasons.find(s => s.season_number === activeSeason);
                activeEpisode = prevSeasonData ? prevSeasonData.episode_count : 1;
            }
        }

        updateTvNavLabel();
        verifyNavigationButtons();
        
        // Reload custom streams list if type is custom
        if (type === 'custom' || id >= 1000000000) {
            resolveCustomStreams();
            renderServersList();
        }
        
        loadIframeSource();
        
        // Push state to browser history to keep URL updated
        const newUrl = (type === 'custom' || id >= 1000000000)
            ? `/play/${targetType}/${id}?season=${activeSeason}&episode=${activeEpisode}`
            : `/play/tv/${id}?season=${activeSeason}&episode=${activeEpisode}`;
        window.history.pushState({ path: newUrl }, '', newUrl);
        showToast(`Now playing Episode ${activeEpisode}`);
    }

    function showToast(message) {
        const toast = document.getElementById("toast");
        toast.innerText = message;
        toast.style.opacity = "1";
        setTimeout(() => {
            toast.style.opacity = "0";
        }, 3000);
    }
</script>
@endsection
