@extends('layouts.layout')

@section('title', 'Actor Biography — CineMovie')

@section('content')
<div class="px-4 md:px-8 py-6 space-y-8 max-w-7xl mx-auto select-none" id="actor-container">
    
    <!-- Navigation Back Button -->
    <div class="flex items-center">
        <button onclick="window.history.back()" class="p-3.5 rounded-xl bg-[#1E1E2E] border border-white/5 text-slate-300 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </button>
    </div>

    <!-- Core Actor Profile Panel -->
    <div class="flex flex-col md:flex-row gap-6 md:gap-10 items-start">
        
        <!-- Profile Picture Column -->
        <div class="w-[180px] md:w-[280px] aspect-[2/3] rounded-3xl overflow-hidden shadow-2xl border border-white/5 bg-[#1E1E2E] flex-shrink-0 self-center md:self-start">
            <img id="actor-profile-img" src="" alt="Profile Image" class="w-full h-full object-cover opacity-0 transition-opacity duration-300">
        </div>

        <!-- Biography Column -->
        <div class="flex-1 space-y-5">
            <h1 id="actor-name" class="text-3xl md:text-5xl font-extrabold tracking-tight text-white">Loading Artist...</h1>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="bio-metadata">
                <div class="glass px-4 py-3.5 rounded-2xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Born</span>
                    <p id="actor-birthday" class="text-xs font-semibold text-white mt-0.5">--</p>
                </div>
                <div class="glass px-4 py-3.5 rounded-2xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Place of Birth</span>
                    <p id="actor-birthplace" class="text-xs font-semibold text-white mt-0.5">--</p>
                </div>
                <div class="glass px-4 py-3.5 rounded-2xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Known For</span>
                    <p id="actor-dept" class="text-xs font-semibold text-white mt-0.5">Acting</p>
                </div>
            </div>

            <div class="glass p-6 rounded-3xl space-y-4">
                <h3 class="text-md font-bold text-white">Biography</h3>
                <p id="actor-biography" class="text-slate-300 text-xs md:text-sm leading-relaxed whitespace-pre-line line-clamp-6 hover:line-clamp-none cursor-pointer transition-all duration-300">Loading biography details...</p>
            </div>
        </div>

    </div>

    <!-- Tabs Selector -->
    <div class="flex gap-4 border-b border-[#1E1E2E] pt-4">
        <button onclick="switchActorTab('known')" id="actor-tab-known" class="pb-3 text-sm font-extrabold border-b-2 border-violet-500 text-white transition-all">Known For</button>
        <button onclick="switchActorTab('filmography')" id="actor-tab-filmography" class="pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all">Filmography</button>
    </div>

    <!-- Tab Contents -->
    <div>
        <!-- Known For Grid -->
        <div id="known-container" class="space-y-6">
            <div id="known-for-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                <!-- Skeletons -->
                @for ($i = 0; $i < 10; $i++)
                <div class="aspect-[2/3] bg-slate-900/60 rounded-2xl animate-pulse"></div>
                @endfor
            </div>
        </div>

        <!-- Filmography List -->
        <div id="filmography-container" class="hidden space-y-3 max-w-4xl">
            <!-- Skeletons -->
            @for ($i = 0; $i < 6; $i++)
            <div class="p-3 bg-[#1E1E2E]/30 rounded-2xl border border-white/5 flex gap-4 items-center">
                <div class="w-12 h-16 bg-slate-900 rounded-xl animate-pulse"></div>
                <div class="flex-1 space-y-2 py-1">
                    <div class="h-3.5 bg-slate-900 rounded w-1/3 animate-pulse"></div>
                    <div class="h-3 bg-slate-900 rounded w-1/4 animate-pulse"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

</div>

<script>
    const actorId = {{ $id }};
    let activeTab = 'known';
    let fullCredits = [];

    document.addEventListener("DOMContentLoaded", () => {
        loadActorDetails();
    });

    function switchActorTab(tab) {
        if (tab === activeTab) return;

        // Toggle button classes
        document.getElementById("actor-tab-known").className = "pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
        document.getElementById("actor-tab-filmography").className = "pb-3 text-sm font-semibold text-slate-400 border-b-2 border-transparent hover:text-slate-200 transition-all";
        
        document.getElementById(`actor-tab-${tab}`).className = "pb-3 text-sm font-extrabold border-b-2 border-violet-500 text-white transition-all";

        // Toggle container visibility
        if (tab === 'known') {
            document.getElementById("known-container").classList.remove("hidden");
            document.getElementById("filmography-container").classList.add("hidden");
        } else {
            document.getElementById("known-container").classList.add("hidden");
            document.getElementById("filmography-container").classList.remove("hidden");
            renderFilmographyList();
        }

        activeTab = tab;
    }

    async function loadActorDetails() {
        try {
            const [actor, credits] = await Promise.all([
                fetch(`/api/tmdb/person/${actorId}`).then(r => r.json()),
                fetch(`/api/tmdb/person/${actorId}/combined_credits`).then(r => r.json())
            ]);

            // Set Title & Basic Details
            document.title = `${actor.name} — CineMovie`;
            document.getElementById("actor-name").innerText = actor.name;
            
            const profile = document.getElementById("actor-profile-img");
            profile.src = actor.profile_path 
                ? `https://image.tmdb.org/t/p/h632${actor.profile_path}` 
                : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image';
            profile.classList.remove("opacity-0");

            // Bio data
            document.getElementById("actor-birthday").innerText = actor.birthday 
                ? `${actor.birthday} ${actor.deathday ? '(Deceased: ' + actor.deathday + ')' : ''}` 
                : 'Unknown';
            document.getElementById("actor-birthplace").innerText = actor.place_of_birth || 'N/A';
            document.getElementById("actor-dept").innerText = actor.known_for_department || 'Acting';
            
            document.getElementById("actor-biography").innerText = actor.biography || `We don't have a biography for ${actor.name} yet.`;

            // Known For: top 10 items (sorted by popularity)
            const knownForCredits = (credits.cast || [])
                .filter(c => c.poster_path)
                .sort((a, b) => (b.popularity || 0) - (a.popularity || 0))
                .slice(0, 10);

            renderKnownFor(knownForCredits);

            // Filmography: all items with dates sorted descending
            fullCredits = (credits.cast || [])
                .filter(c => c.release_date || c.first_air_date)
                .sort((a, b) => {
                    const dateA = a.release_date || a.first_air_date || '';
                    const dateB = b.release_date || b.first_air_date || '';
                    return dateB.localeCompare(dateA);
                })
                .slice(0, 30); // Show top 30 casting credits

        } catch (err) {
            console.error("Error loading actor details:", err);
        }
    }

    function renderKnownFor(items) {
        const grid = document.getElementById("known-for-grid");
        if (items.length === 0) {
            grid.innerHTML = `<div class="text-slate-500 text-sm col-span-full py-8 text-center">No filmography listings found</div>`;
            return;
        }
        grid.innerHTML = items.map(item => {
            const mediaType = item.media_type || 'movie';
            const dateRaw = item.release_date || item.first_air_date || '';
            const year = dateRaw ? dateRaw.substring(0, 4) : '';
            const displayTitle = item.title || item.name || 'Unknown Title';
            return `
                <a href="/details/${mediaType}/${item.id}" class="group flex flex-col gap-2 relative">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-[#1E1E2E] border border-white/5 transition duration-300 group-hover:scale-[1.03] group-hover:shadow-xl group-hover:shadow-violet-500/10">
                        <img src="https://image.tmdb.org/t/p/w342${item.poster_path}" alt="${displayTitle}" class="w-full h-full object-cover">
                    </div>
                    <div class="px-1 space-y-0.5 leading-tight">
                        <h4 class="text-xs font-bold text-white group-hover:text-violet-400 transition truncate w-full">${displayTitle}</h4>
                        <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
                            <span>${year}</span>
                            <span class="uppercase font-extrabold text-[8px] bg-[#1E1E2E] px-1.5 py-0.5 rounded border border-white/5">${mediaType}</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
    }

    function renderFilmographyList() {
        const container = document.getElementById("filmography-container");
        if (fullCredits.length === 0) {
            container.innerHTML = `<div class="text-slate-500 text-sm py-12 text-center">No filmography records found.</div>`;
            return;
        }

        container.innerHTML = fullCredits.map(item => {
            const mediaType = item.media_type || 'movie';
            const dateRaw = item.release_date || item.first_air_date || '';
            const year = dateRaw ? dateRaw.substring(0, 4) : 'N/A';
            const displayTitle = item.title || item.name || 'Unknown Title';
            const posterUrl = item.poster_path 
                ? `https://image.tmdb.org/t/p/w185${item.poster_path}` 
                : 'https://placehold.co/185x278/1E1E2E/FFF?text=No+Image';

            let badgeBg = 'bg-violet-600/10 text-violet-400 border-violet-500/10';
            let badgeText = 'Movie';
            if (mediaType === 'tv') {
                badgeBg = 'bg-emerald-600/10 text-emerald-400 border-emerald-500/10';
                badgeText = 'TV Show';
            }

            return `
                <a href="/details/${mediaType}/${item.id}" class="group p-3 bg-[#1E1E2E]/20 hover:bg-[#1E1E2E]/40 border border-white/5 rounded-2xl flex items-center justify-between gap-4 transition duration-200 hover:scale-[1.01] hover:border-violet-500/20 select-none animate-fadeIn">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-16 rounded-xl overflow-hidden bg-[#1E1E2E] border border-white/5 flex-shrink-0">
                            <img src="${posterUrl}" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-white group-hover:text-violet-400 transition line-clamp-1 leading-tight">${displayTitle}</h3>
                            <div class="flex items-center gap-2">
                                <span class="uppercase text-[9px] font-extrabold px-1.5 py-0.5 rounded border ${badgeBg}">${badgeText}</span>
                                <span class="text-xs text-slate-450">${year}</span>
                            </div>
                            ${item.character ? `<p class="text-xs text-slate-450 line-clamp-1">as <span class="text-slate-300 font-semibold">${item.character}</span></p>` : ''}
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-500 group-hover:text-white transition-all mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            `;
        }).join('');
    }
</script>
@endsection
