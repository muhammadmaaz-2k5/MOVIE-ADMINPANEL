@extends('layouts.layout')
@section('title', 'Anime Manager — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>Anime Manager</span>
                <span class="text-lg">⛩️</span>
            </h1>
            <p class="text-slate-400 text-sm mt-1">Manage customized Anime Movies & TV series, and configure specific stream/download servers.</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-5 py-2.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Create Custom Anime
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="glass p-4 rounded-2xl flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input id="search-input" type="text" placeholder="Search custom Anime library..." oninput="filterMovies()" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
        </div>
        <select id="type-filter" onchange="filterMovies()" class="bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
            <option value="">All Anime Formats</option>
            <option value="movie">🎬 Anime Movies</option>
            <option value="tv">📺 Anime TV Shows</option>
        </select>
    </div>

    <!-- Anime List Table -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5 text-left text-slate-400">
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Title / TMDB ID</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Format</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Language</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Rating</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Streams</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="movies-table-body" class="divide-y divide-white/5 text-slate-200">
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading custom Anime library...</td></tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="pagination-bar" class="px-5 py-4 border-t border-white/5 flex items-center justify-between hidden">
            <span id="pagination-info" class="text-xs text-slate-400"></span>
            <div class="flex gap-2">
                <button id="prev-btn" onclick="prevPage()" class="px-3 py-1.5 text-xs font-bold bg-[#1E1E2E] border border-white/5 rounded-xl text-slate-300 hover:text-white transition disabled:opacity-40" disabled>← Prev</button>
                <button id="next-btn" onclick="nextPage()" class="px-3 py-1.5 text-xs font-bold bg-[#1E1E2E] border border-white/5 rounded-xl text-slate-300 hover:text-white transition disabled:opacity-40" disabled>Next →</button>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     Add / Edit Custom Anime Modal
 ══════════════════════════════════════════════════════ -->
<div id="movie-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeMovieModal(event)">
    <div id="movie-modal-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/8 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <h3 id="modal-title" class="text-lg font-extrabold text-white">Create Custom Anime</h3>
            <button onclick="closeMovieModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- TMDB Search (Prefill) -->
        <div id="tmdb-search-section" class="px-6 py-4 border-b border-white/5 space-y-3">
            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Find & Import from TMDB</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="tmdb-search" type="text" placeholder="Type Anime title to import metadata..." oninput="searchTmdb()" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
            </div>
            <div id="tmdb-results" class="space-y-2 max-h-52 overflow-y-auto scrollbar-thin hidden"></div>
        </div>

        <!-- Form -->
        <form id="movie-form" onsubmit="submitMovieForm(event)" class="px-6 py-5 space-y-4">
            <input type="hidden" id="form-id" value=""/>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- TMDB ID -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">TMDB ID *</label>
                    <input id="form-tmdb-id" type="number" required placeholder="e.g. 94605" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Format -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Format *</label>
                    <select id="form-type" required class="w-full bg-[#1E1E2E] border border-white/5 text-slate-355 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="movie">🎬 Anime Movie</option>
                        <option value="tv">📺 Anime TV Show</option>
                    </select>
                </div>

                <!-- Custom Title -->
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Custom Title *</label>
                    <input id="form-title" type="text" required placeholder="e.g. Solo Leveling [Hindi]" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                    <input id="form-genre-ids" type="hidden" value="[16]"/>
                </div>

                <!-- Poster URL -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Poster Path (TMDB or HTTP URL)</label>
                    <input id="form-poster-path" type="text" placeholder="/gE8S01v7V8nZ72uQ3v9T3Jm.jpg" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Backdrop URL -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Backdrop Path (TMDB or HTTP URL)</label>
                    <input id="form-backdrop-path" type="text" placeholder="/xJHokMbljvjrrclvST6U6.jpg" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Language -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Language *</label>
                    <select id="form-language" required class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="Hindi">Hindi</option>
                        <option value="English">English</option>
                        <option value="Punjabi">Punjabi</option>
                        <option value="Bengali">Bengali</option>
                        <option value="Urdu">Urdu</option>
                        <option value="Tamil">Tamil</option>
                        <option value="Telugu">Telugu</option>
                        <option value="Arabic">Arabic</option>
                        <option value="French">French</option>
                        <option value="Spanish">Spanish</option>
                        <option value="Russian">Russian</option>
                        <option value="Indonesian">Indonesian</option>
                        <option value="Dual Audio">Dual Audio</option>
                    </select>
                </div>

                <!-- Release Year -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Year</label>
                    <input id="form-year" type="text" placeholder="2024" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Runtime -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Runtime</label>
                    <input id="form-runtime" type="text" placeholder="24 min" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Rating -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rating</label>
                    <input id="form-rating" type="number" step="0.1" max="10" placeholder="8.5" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Storyline Description -->
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Overview / Storyline</label>
                    <textarea id="form-overview" rows="3" placeholder="Enter localized description..." class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition scrollbar-thin"></textarea>
                </div>

                <!-- Active Status Toggle -->
                <div class="flex items-center gap-3 sm:col-span-2 pt-2">
                    <input id="form-is-active" type="checkbox" class="w-4 h-4 accent-violet-600 rounded border-white/5 bg-[#1E1E2E]"/>
                    <label for="form-is-active" class="text-xs font-semibold text-slate-300 uppercase tracking-wider select-none cursor-pointer">Publish Custom Anime</label>
                </div>
            </div>

            <!-- Footer Save Buttons -->
            <div class="border-t border-white/5 pt-5 flex gap-3">
                <button type="submit" id="submit-btn" class="flex-1 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold py-3 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm font-extrabold">Save Custom Anime</button>
                <button type="button" onclick="closeMovieModal()" class="flex-1 bg-[#1E1E2E] border border-white/5 text-slate-300 font-bold py-3 rounded-2xl hover:bg-white/5 transition text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     Stream Management Drawer
 ══════════════════════════════════════════════════════ -->
<div id="streams-drawer" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex justify-end hidden" onclick="handleDrawerBackdropClick(event)">
    <div id="streams-drawer-content" class="w-full max-w-md bg-[#121220] h-full border-l border-white/5 shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-white/5 flex justify-between items-center bg-[#121220]/60 backdrop-blur">
            <div>
                <h3 id="drawer-movie-title" class="text-lg font-extrabold text-white">Manage Streams</h3>
                <p id="drawer-movie-meta" class="text-slate-400 text-xs mt-0.5"></p>
            </div>
            <button onclick="closeStreamsDrawer()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Add Stream Form -->
        <div class="px-6 py-4 border-b border-white/5 bg-white/1">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Add Custom Stream link</h4>
            <form id="stream-form" onsubmit="submitStreamForm(event)" class="space-y-3">
                <input type="hidden" id="stream-form-id" value=""/>
                
                <div class="grid grid-cols-2 gap-3">
                    <input id="stream-server" type="text" required placeholder="Server Name (e.g. Server HD [Hindi])" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                    <input id="stream-icon" type="text" placeholder="Icon (e.g. 🇮🇳 or 🔗)" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="grid grid-cols-2 gap-3" id="tv-fields">
                    <input id="stream-season" type="number" placeholder="Season Number" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                    <input id="stream-episode" type="number" placeholder="Episode Number" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <input id="stream-url" type="url" required placeholder="Stream Embed Player URL (e.g. https://server.com/embed/...)" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-violet-500/40 transition font-mono"/>

                <div class="flex gap-2">
                    <button type="submit" id="stream-submit-btn" class="flex-1 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold py-2 rounded-xl text-xs hover:from-violet-500 hover:to-fuchsia-500 transition">Add Stream</button>
                    <button type="button" id="stream-cancel-btn" onclick="resetStreamForm()" class="px-4 py-2 bg-[#1E1E2E] border border-white/5 text-slate-400 text-xs font-bold rounded-xl hover:text-white hidden">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Streams List -->
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3 scrollbar-thin">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Configured Streams</h4>
            <div id="drawer-streams-list" class="space-y-3">
                <div class="text-center text-slate-500 py-12 text-xs">No streams configured yet.</div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden">
    <div class="w-full max-w-sm bg-[#121220] rounded-3xl border border-white/8 shadow-2xl p-6 mx-4 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Delete customized Anime?</h3>
                <p class="text-xs text-slate-400 mt-0.5">This removes all custom metadata and related streams.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="confirmDelete()" class="flex-1 bg-rose-600 hover:bg-rose-500 text-white font-bold py-2.5 rounded-2xl transition text-sm">Delete</button>
            <button onclick="document.getElementById('delete-modal').classList.add('hidden')" class="flex-1 bg-[#1E1E2E] border border-white/5 text-slate-300 font-bold py-2.5 rounded-2xl hover:bg-white/5 transition text-sm">Cancel</button>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="fixed bottom-8 right-6 z-[999] hidden">
    <div id="toast-inner" class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold text-white flex items-center gap-2 animate-slideUp"></div>
</div>

<script>
// ── State ─────────────────────────────────────────────────────────────────────
let currentPage = 1;
let totalPages  = 1;
let deleteTargetId = null;
let tmdbSearchTimer = null;
let activeMovieIdForStreams = null;
let activeMovieTypeForStreams = 'movie';

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadCustomLibrary();
});

// ── Load Custom Library ───────────────────────────────────────────────────────
async function loadCustomLibrary(page = 1) {
    currentPage = page;
    const search  = document.getElementById('search-input').value;
    const type    = document.getElementById('type-filter').value;
    let url = `/admin/api/custom-movies?page=${page}&genre=16`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (type)   url += `&type=${type}`;

    try {
        const data = await fetch(url).then(r => r.json());
        renderTable(data.data || []);
        updatePagination(data);
    } catch(e) {
        document.getElementById('movies-table-body').innerHTML = `<tr><td colspan="7" class="px-5 py-12 text-center text-rose-455 text-xs">Failed to load customized library. Check console or try again.</td></tr>`;
    }
}

function filterMovies() {
    clearTimeout(tmdbSearchTimer);
    tmdbSearchTimer = setTimeout(() => loadCustomLibrary(1), 300);
}

// ── Render Anime List ─────────────────────────────────────────────────────────
function renderTable(movies) {
    const tbody = document.getElementById('movies-table-body');
    if (movies.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-16 text-center text-slate-500 text-sm">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                <span>No custom Anime created yet. Add one to get started.</span>
            </div>
        </td></tr>`;
        return;
    }

    tbody.innerHTML = movies.map(movie => {
        const poster = movie.poster_path 
            ? (movie.poster_path.startsWith('/') ? 'https://image.tmdb.org/t/p/w92' + movie.poster_path : movie.poster_path)
            : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(movie.title.substring(0,2))}`;
        
        const typeBadge = movie.type === 'movie'
            ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#FF6B9D]/20 text-[#FF6B9D] rounded-md">ANIME MOVIE</span>`
            : `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#00B894]/20 text-[#00B894] rounded-md">ANIME TV</span>`;
        
        const activeEl  = movie.is_active
            ? `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2 py-0.5"><span class="w-1.5 h-1.5 bg-emerald-400 rounded-full inline-block"></span>Active</span>`
            : `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-white/5 border border-white/5 rounded-lg px-2 py-0.5">Inactive</span>`;

        return `<tr class="hover:bg-white/1 transition group">
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <img src="${poster}" class="w-8 h-11 rounded-lg object-cover bg-[#1E1E2E] flex-shrink-0"/>
                    <div class="min-w-0">
                        <a href="/details/custom/${movie.id}" target="_blank" class="text-xs font-bold text-white hover:text-violet-400 transition line-clamp-1">${movie.title}</a>
                        <p class="text-[10px] text-slate-550 mt-0.5">TMDB ID: ${movie.tmdb_id} · Year: ${movie.year || '—'}</p>
                    </div>
                </div>
            </td>
            <td class="px-5 py-3.5">${typeBadge}</td>
            <td class="px-5 py-3.5"><span class="text-xs font-bold text-slate-300">${movie.language}</span></td>
            <td class="px-5 py-3.5"><span class="text-xs text-amber-400 font-extrabold">⭐ ${movie.rating || '0.0'}</span></td>
            <td class="px-5 py-3.5">
                <button onclick="openStreamsDrawer(${movie.id}, '${movie.title.replace(/'/g, "\\'")}', '${movie.type}')" 
                    class="px-2.5 py-1 text-[10px] font-bold bg-violet-600/10 border border-violet-500/20 text-violet-400 rounded-lg hover:bg-violet-600/20 transition flex items-center gap-1">
                    🔗 ${movie.streams_count || 0} Streams
                </button>
            </td>
            <td class="px-5 py-3.5">${activeEl}</td>
            <td class="px-5 py-3.5">
                <div class="flex gap-2">
                    <button onclick="openEditModal(${JSON.stringify(movie).replace(/"/g,'&quot;')})" class="p-1.5 rounded-lg bg-violet-500/10 border border-violet-500/20 text-violet-400 hover:bg-violet-500/20 transition" title="Edit Metadata">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="openDeleteModal(${movie.id})" class="p-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition" title="Delete Customized Anime">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

// ── Pagination ────────────────────────────────────────────────────────────────
function updatePagination(data) {
    const bar = document.getElementById('pagination-bar');
    totalPages = data.last_page || 1;
    if (totalPages <= 1) { bar.classList.add('hidden'); return; }
    bar.classList.remove('hidden');

    document.getElementById('pagination-info').innerText = `Page ${data.current_page} of ${data.last_page} — ${data.total} items`;
    document.getElementById('prev-btn').disabled = data.current_page <= 1;
    document.getElementById('next-btn').disabled = data.current_page >= data.last_page;
}

function prevPage() { if (currentPage > 1) loadCustomLibrary(currentPage - 1); }
function nextPage() { if (currentPage < totalPages) loadCustomLibrary(currentPage + 1); }

// ── TMDB Search ───────────────────────────────────────────────────────────────
function searchTmdb() {
    clearTimeout(tmdbSearchTimer);
    const q = document.getElementById('tmdb-search').value.trim();
    if (q.length < 2) { document.getElementById('tmdb-results').classList.add('hidden'); return; }
    tmdbSearchTimer = setTimeout(async () => {
        try {
            // Fetch TMDB results matching animation genre 16
            const data = await fetch(`/api/tmdb/search/multi?query=${encodeURIComponent(q)}`).then(r => r.json());
            const results = (data.results || []).filter(r => (r.media_type === 'movie' || r.media_type === 'tv')).slice(0, 8);
            const container = document.getElementById('tmdb-results');
            if (results.length === 0) { container.classList.add('hidden'); return; }
            container.classList.remove('hidden');
            container.innerHTML = results.map(r => {
                const title = r.title || r.name;
                const year  = (r.release_date || r.first_air_date || '').substring(0,4);
                const poster = r.poster_path ? `https://image.tmdb.org/t/p/w92${r.poster_path}` : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(title.substring(0,2))}`;
                const typeLabel = r.media_type === 'movie' ? '🎬 Movie' : '📺 TV Show';
                return `<button type="button" onclick='selectTmdbContent(${JSON.stringify({id:r.id,type:r.media_type,title:title,year:year,poster:r.poster_path||"",backdrop:r.backdrop_path||"",rating:r.vote_average||0.0,overview:r.overview||"",genre_ids:r.genre_ids||[],original_language:r.original_language||"en"}).replace(/'/g,"\\'")})'
                    class="flex items-center gap-3 w-full p-2.5 rounded-xl hover:bg-white/5 transition text-left border border-white/0 hover:border-violet-500/20">
                    <img src="${poster}" class="w-8 h-11 rounded-lg object-cover bg-[#1E1E2E] flex-shrink-0"/>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">${typeLabel} · ${title}</p>
                        <p class="text-[10px] text-slate-400">${year} · ID ${r.id}</p>
                    </div>
                </button>`;
            }).join('');
        } catch(e) { console.error(e); }
    }, 350);
}

function selectTmdbContent(c) {
    document.getElementById('form-tmdb-id').value       = c.id;
    document.getElementById('form-type').value          = c.type;
    document.getElementById('form-title').value         = c.title;
    document.getElementById('form-poster-path').value   = c.poster;
    document.getElementById('form-backdrop-path').value = c.backdrop;
    document.getElementById('form-year').value          = c.year;
    document.getElementById('form-rating').value        = c.rating.toFixed(1);
    document.getElementById('form-overview').value      = c.overview;
    
    // Automatically force Animation genre 16
    let genres = c.genre_ids || [];
    if (!genres.includes(16)) genres.push(16);
    document.getElementById('form-genre-ids').value     = JSON.stringify(genres);

    const langMap = {
        'hi': 'Hindi',
        'en': 'English',
        'pa': 'Punjabi',
        'te': 'Telugu',
        'ta': 'Tamil',
        'ur': 'Urdu',
        'ar': 'Arabic',
        'es': 'Spanish',
        'fr': 'French',
        'ru': 'Russian',
        'id': 'Indonesian'
    };
    const dbLang = langMap[c.original_language] || 'Hindi';
    document.getElementById('form-language').value = dbLang;

    document.getElementById('tmdb-results').classList.add('hidden');
    document.getElementById('tmdb-search').value = '';
}

// ── Modals Logic ──────────────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('modal-title').innerText = 'Create Custom Anime';
    document.getElementById('movie-form').reset();
    document.getElementById('form-id').value = '';
    document.getElementById('form-genre-ids').value = '[16]';
    document.getElementById('form-is-active').checked = true;
    document.getElementById('tmdb-search-section').classList.remove('hidden');
    document.getElementById('movie-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(movie) {
    document.getElementById('modal-title').innerText = 'Edit Custom Anime';
    document.getElementById('tmdb-search-section').classList.add('hidden');

    document.getElementById('form-id').value            = movie.id;
    document.getElementById('form-tmdb-id').value       = movie.tmdb_id;
    document.getElementById('form-type').value          = movie.type;
    document.getElementById('form-title').value         = movie.title;
    document.getElementById('form-poster-path').value   = movie.poster_path || '';
    document.getElementById('form-backdrop-path').value = movie.backdrop_path || '';
    document.getElementById('form-language').value      = movie.language;
    document.getElementById('form-year').value          = movie.year || '';
    document.getElementById('form-runtime').value       = movie.runtime || '';
    document.getElementById('form-rating').value        = movie.rating || '';
    document.getElementById('form-overview').value      = movie.overview || '';
    
    // Maintain Animation genre 16
    let genres = movie.genre_ids || [];
    if (!genres.includes(16)) genres.push(16);
    document.getElementById('form-genre-ids').value     = JSON.stringify(genres);
    document.getElementById('form-is-active').checked   = !!movie.is_active;

    document.getElementById('movie-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMovieModal(event) {
    if (event && event.target !== document.getElementById('movie-modal')) return;
    document.getElementById('movie-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Submit Movie Form ─────────────────────────────────────────────────────────
async function submitMovieForm(event) {
    event.preventDefault();

    const id = document.getElementById('form-id').value;
    
    // Double check that animation genre 16 is appended
    let genreIds = JSON.parse(document.getElementById('form-genre-ids').value || '[16]');
    if (!genreIds.includes(16)) genreIds.push(16);

    const payload = {
        tmdb_id:       parseInt(document.getElementById('form-tmdb-id').value),
        title:         document.getElementById('form-title').value,
        type:          document.getElementById('form-type').value,
        genre_ids:     genreIds,
        poster_path:   document.getElementById('form-poster-path').value,
        backdrop_path: document.getElementById('form-backdrop-path').value,
        language:      document.getElementById('form-language').value,
        year:          document.getElementById('form-year').value,
        runtime:       document.getElementById('form-runtime').value,
        rating:        parseFloat(document.getElementById('form-rating').value || 0.0),
        overview:      document.getElementById('form-overview').value,
        is_active:     document.getElementById('form-is-active').checked,
    };

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    try {
        const method = id ? 'PUT' : 'POST';
        const url    = id ? `/admin/api/custom-movies/${id}` : '/admin/api/custom-movies';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error(await res.text());
        closeMovieModal();
        loadCustomLibrary(currentPage);
        showToast(id ? 'Anime updated successfully!' : 'Custom Anime created!', 'success');
    } catch(e) {
        showToast('Error saving: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Custom Anime';
    }
}

// ── Delete Anime ──────────────────────────────────────────────────────────────
function openDeleteModal(id) {
    deleteTargetId = id;
    document.getElementById('delete-modal').classList.remove('hidden');
}

async function confirmDelete() {
    if (!deleteTargetId) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        await fetch(`/admin/api/custom-movies/${deleteTargetId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        document.getElementById('delete-modal').classList.add('hidden');
        loadCustomLibrary(currentPage);
        showToast('Custom Anime deleted.', 'success');
    } catch(e) {
        showToast('Delete failed.', 'error');
    }
    deleteTargetId = null;
}

// ── Manage Streams Drawer ─────────────────────────────────────────────────────
async function openStreamsDrawer(id, title, type) {
    activeMovieIdForStreams = id;
    activeMovieTypeForStreams = type;
    document.getElementById('drawer-movie-title').innerText = title;
    document.getElementById('drawer-movie-meta').innerText = `Add video player servers for this customized anime ${type === 'movie' ? 'movie' : 'series'}`;
    
    // Toggle TV fields (Season/Episode selection)
    if (type === 'tv') {
        document.getElementById('tv-fields').classList.remove('hidden');
    } else {
        document.getElementById('tv-fields').classList.add('hidden');
    }
    
    resetStreamForm();
    await loadMovieStreams();

    const drawer = document.getElementById('streams-drawer');
    const content = document.getElementById('streams-drawer-content');
    drawer.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeStreamsDrawer() {
    const drawer = document.getElementById('streams-drawer');
    const content = document.getElementById('streams-drawer-content');
    content.classList.add('translate-x-full');
    setTimeout(() => {
        drawer.classList.add('hidden');
    }, 300);
    document.body.style.overflow = '';
}

function handleDrawerBackdropClick(event) {
    if (event.target.id === 'streams-drawer') {
        closeStreamsDrawer();
    }
}

async function loadMovieStreams() {
    const list = document.getElementById('drawer-streams-list');
    list.innerHTML = `<div class="text-center text-slate-500 py-12 text-xs animate-pulse">Loading streams...</div>`;
    
    try {
        const streams = await fetch(`/admin/api/custom-movies/${activeMovieIdForStreams}/streams`).then(r => r.json());
        if (streams.length === 0) {
            list.innerHTML = `<div class="text-center text-slate-500 py-12 text-xs">No streams configured yet. Create one above!</div>`;
            return;
        }

        list.innerHTML = streams.map(s => {
            const metaInfo = activeMovieTypeForStreams === 'tv'
                ? `<span class="px-1.5 py-0.5 bg-violet-600/10 text-violet-400 border border-violet-500/20 text-[9px] font-bold rounded">S${s.season_number} E${s.episode_number}</span>`
                : '';
            return `
            <div class="glass p-3 rounded-2xl flex items-center justify-between gap-3 border border-white/5 hover:border-violet-500/20 transition">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-base">${s.server_icon || '🔗'}</span>
                    <div class="min-w-0">
                        <span class="block text-xs font-bold text-white truncate">${s.server_name}</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            ${metaInfo}
                            <span class="text-[9px] text-slate-555 truncate font-mono">${s.stream_url}</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-1.5 flex-shrink-0">
                    <button onclick="editStream(${JSON.stringify(s).replace(/"/g,'&quot;')})" class="p-1 rounded-lg bg-violet-500/10 border border-violet-500/20 text-violet-400 hover:bg-violet-500/20 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="deleteStream(${s.id})" class="p-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>`;
        }).join('');
    } catch(e) {
        list.innerHTML = `<div class="text-center text-rose-400 py-12 text-xs">Failed to load streams.</div>`;
    }
}

// ── Submit Stream Form ────────────────────────────────────────────────────────
async function submitStreamForm(event) {
    event.preventDefault();

    const streamId = document.getElementById('stream-form-id').value;
    const payload = {
        server_name:    document.getElementById('stream-server').value,
        server_icon:    document.getElementById('stream-icon').value || '🔗',
        stream_url:     document.getElementById('stream-url').value,
        season_number:  document.getElementById('stream-season').value ? parseInt(document.getElementById('stream-season').value) : null,
        episode_number: document.getElementById('stream-episode').value ? parseInt(document.getElementById('stream-episode').value) : null,
    };

    const btn = document.getElementById('stream-submit-btn');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    try {
        const method = streamId ? 'PUT' : 'POST';
        const url    = streamId ? `/admin/api/custom-streams/${streamId}` : `/admin/api/custom-movies/${activeMovieIdForStreams}/streams`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        });
        if (!res.ok) throw new Error(await res.text());
        
        resetStreamForm();
        await loadMovieStreams();
        loadCustomLibrary(currentPage); // update stream count badges
        showToast(streamId ? 'Stream server updated!' : 'Stream server added!', 'success');
    } catch(e) {
        showToast('Error saving stream: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = streamId ? 'Update Stream' : 'Add Stream';
    }
}

function editStream(s) {
    document.getElementById('stream-form-id').value = s.id;
    document.getElementById('stream-server').value = s.server_name;
    document.getElementById('stream-icon').value = s.server_icon || '🔗';
    document.getElementById('stream-url').value = s.stream_url;
    document.getElementById('stream-season').value = s.season_number || '';
    document.getElementById('stream-episode').value = s.episode_number || '';

    document.getElementById('stream-submit-btn').innerText = 'Update Stream';
    document.getElementById('stream-cancel-btn').classList.remove('hidden');
}

function resetStreamForm() {
    document.getElementById('stream-form').reset();
    document.getElementById('stream-form-id').value = '';
    document.getElementById('stream-submit-btn').innerText = 'Add Stream';
    document.getElementById('stream-cancel-btn').classList.add('hidden');
}

async function deleteStream(id) {
    if (!confirm('Are you sure you want to remove this stream link?')) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        await fetch(`/admin/api/custom-streams/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        await loadMovieStreams();
        loadCustomLibrary(currentPage); // update badges
        showToast('Stream removed.', 'success');
    } catch(e) {
        showToast('Failed to delete stream.', 'error');
    }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    inner.className = `px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold text-white flex items-center gap-2 animate-slideUp ${type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'}`;
    inner.innerHTML = `${type === 'error' ? '✕' : '✓'} ${msg}`;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3500);
}
</script>
@endsection
