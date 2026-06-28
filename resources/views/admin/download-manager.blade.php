@extends('layouts.layout')
@section('title', 'Download Manager — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Download Manager</h1>
            <p class="text-slate-400 text-sm mt-1">Add and manage download server links for every movie & TV show.</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-5 py-2.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Download Link
        </button>
    </div>

    <!-- Admin Modules Navigation Tabs -->
    <div class="flex flex-wrap gap-2.5 pb-2">
        <a href="{{ route('admin.movie-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.movie-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🎬 Movie Manager
        </a>
        <a href="{{ route('admin.tv-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.tv-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            📺 TV Shows Manager
        </a>
        <a href="{{ route('admin.anime-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.anime-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            ⛩️ Anime Manager
        </a>
        <a href="{{ route('admin.download-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.download-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            📂 Download Manager
        </a>
        <a href="{{ route('admin.home-section-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.home-section-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🔥 Home Sections
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="glass p-4 rounded-2xl flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input id="search-input" type="text" placeholder="Search by title..." oninput="filterLinks()" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
        </div>
        <select id="type-filter" onchange="filterLinks()" class="bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
            <option value="">All Types</option>
            <option value="movie">🎬 Movies</option>
            <option value="tv">📺 TV Shows</option>
        </select>
        <select id="quality-filter" onchange="filterLinks()" class="bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
            <option value="">All Qualities</option>
            <option value="360p">360p</option>
            <option value="480p">480p</option>
            <option value="720p">720p HD</option>
            <option value="1080p">1080p FHD</option>
            <option value="2160p">2160p 4K</option>
            <option value="4K">4K</option>
            <option value="Blu-ray">Blu-ray</option>
            <option value="CAM">CAM</option>
        </select>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="stats-row">
        <div class="glass p-4 rounded-2xl flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400">Total Links</span>
            <span id="stat-total" class="text-2xl font-extrabold text-white">—</span>
        </div>
        <div class="glass p-4 rounded-2xl flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400">Active</span>
            <span id="stat-active" class="text-2xl font-extrabold text-emerald-400">—</span>
        </div>
        <div class="glass p-4 rounded-2xl flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400">Movies</span>
            <span id="stat-movies" class="text-2xl font-extrabold text-[#0984E3]">—</span>
        </div>
        <div class="glass p-4 rounded-2xl flex flex-col gap-1">
            <span class="text-xs font-semibold text-slate-400">TV Shows</span>
            <span id="stat-tv" class="text-2xl font-extrabold text-[#00B894]">—</span>
        </div>
    </div>

    <!-- Links Table -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5 text-left">
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Content</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Server</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Quality</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Language</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Size</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="links-table-body" class="divide-y divide-white/5">
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading download links...</td></tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="pagination-bar" class="px-5 py-4 border-t border-white/5 flex items-center justify-between hidden">
            <span id="pagination-info" class="text-xs text-slate-400"></span>
            <div class="flex gap-2">
                <button id="prev-btn" onclick="prevPage()" class="px-3 py-1.5 text-xs font-bold bg-[#1E1E2E] border border-white/5 rounded-xl text-slate-300 hover:text-white hover:border-violet-500/20 transition disabled:opacity-40" disabled>← Prev</button>
                <button id="next-btn" onclick="nextPage()" class="px-3 py-1.5 text-xs font-bold bg-[#1E1E2E] border border-white/5 rounded-xl text-slate-300 hover:text-white hover:border-violet-500/20 transition disabled:opacity-40" disabled>Next →</button>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     Add / Edit Modal
══════════════════════════════════════════════════════ -->
<div id="link-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeLinkModal(event)">
    <div id="link-modal-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/8 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <h3 id="modal-title" class="text-lg font-extrabold text-white">Add Download Link</h3>
            <button onclick="closeLinkModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- TMDB Search (only shown in add mode) -->
        <div id="tmdb-search-section" class="px-6 py-4 border-b border-white/5 space-y-3">
            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Search Movie / TV Show</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="tmdb-search" type="text" placeholder="Type to search TMDB..." oninput="searchTmdb()" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
            </div>
            <div id="tmdb-results" class="space-y-2 max-h-52 overflow-y-auto scrollbar-thin hidden"></div>
            <!-- Selected content preview -->
            <div id="selected-content" class="hidden flex items-center gap-3 p-3 bg-violet-600/10 border border-violet-500/20 rounded-2xl">
                <img id="sel-poster" src="" class="w-10 h-14 rounded-lg object-cover bg-[#1E1E2E]"/>
                <div>
                    <p id="sel-title" class="text-sm font-bold text-white"></p>
                    <p id="sel-meta" class="text-xs text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="clearSelectedContent()" class="ml-auto p-1.5 rounded-lg bg-white/5 hover:bg-white/10 transition text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <form id="link-form" onsubmit="submitLinkForm(event)" class="px-6 py-5 space-y-4">
            <input type="hidden" id="form-id" value=""/>
            <input type="hidden" id="form-content-type" value=""/>
            <input type="hidden" id="form-content-id" value=""/>
            <input type="hidden" id="form-content-title" value=""/>
            <input type="hidden" id="form-content-poster" value=""/>

            <!-- Season & Episode (TV Only) -->
            <div id="season-episode-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Season Number</label>
                    <input id="form-season-number" type="number" min="1" placeholder="e.g. 1" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Episode Number</label>
                    <input id="form-episode-number" type="number" min="1" placeholder="e.g. 3" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Server Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Server Name *</label>
                    <input id="form-server-name" type="text" required placeholder="e.g. Google Drive, Mega.nz, Mediafire" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Server Icon -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Server Icon (emoji)</label>
                    <input id="form-server-icon" type="text" placeholder="🔗" maxlength="4" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Quality -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Quality *</label>
                    <select id="form-quality" required class="w-full bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="">Select Quality</option>
                        <option value="360p">360p SD</option>
                        <option value="480p">480p SD</option>
                        <option value="720p">720p HD</option>
                        <option value="1080p">1080p FHD</option>
                        <option value="2160p">2160p 4K UHD</option>
                        <option value="4K">4K</option>
                        <option value="Blu-ray">Blu-ray</option>
                        <option value="CAM">CAM</option>
                    </select>
                </div>

                <!-- Language -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Language</label>
                    <input id="form-language" type="text" placeholder="English" value="English" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- File Size -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">File Size</label>
                    <input id="form-file-size" type="text" placeholder="e.g. 2.1 GB" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Notes -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Notes</label>
                    <input id="form-notes" type="text" placeholder="Optional notes..." class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
            </div>

            <!-- Download URL -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Download URL *</label>
                <input id="form-download-url" type="url" required placeholder="https://drive.google.com/..." class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition font-mono"/>
            </div>

            <!-- Active Toggle -->
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input id="form-is-active" type="checkbox" checked class="sr-only peer"/>
                    <div class="w-10 h-5 bg-[#1E1E2E] border border-white/10 rounded-full peer peer-checked:bg-violet-600 peer-checked:border-violet-500 transition-all after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
                <span class="text-sm font-semibold text-slate-300">Active (visible to users)</span>
            </div>

            <!-- Submit Row -->
            <div class="flex gap-3 pt-2">
                <button type="submit" id="submit-btn" class="flex-1 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold py-3 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
                    Save Download Link
                </button>
                <button type="button" onclick="closeLinkModal()" class="px-5 py-3 bg-[#1E1E2E] border border-white/5 text-slate-300 font-bold rounded-2xl hover:bg-white/5 hover:text-white transition text-sm">
                    Cancel
                </button>
            </div>
        </form>
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
                <h3 class="text-base font-extrabold text-white">Delete Link?</h3>
                <p class="text-xs text-slate-400 mt-0.5">This action cannot be undone.</p>
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
let allLinks    = [];
let currentPage = 1;
let totalPages  = 1;
let deleteTargetId = null;
let tmdbSearchTimer = null;
let editingId   = null;

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadLinks();
});

// ── Load Links ────────────────────────────────────────────────────────────────
async function loadLinks(page = 1) {
    currentPage = page;
    const search  = document.getElementById('search-input').value;
    const type    = document.getElementById('type-filter').value;
    let url = `/admin/api/download-links?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (type)   url += `&type=${type}`;

    try {
        const data = await fetch(url).then(r => r.json());
        renderTable(data.data || []);
        updatePagination(data);
        updateStats(data);
    } catch(e) {
        document.getElementById('links-table-body').innerHTML = `<tr><td colspan="7" class="px-5 py-12 text-center text-rose-400 text-sm">Failed to load links. Make sure the database is migrated.</td></tr>`;
    }
}

function filterLinks() {
    clearTimeout(tmdbSearchTimer);
    tmdbSearchTimer = setTimeout(() => loadLinks(1), 300);
}

// ── Render Table ──────────────────────────────────────────────────────────────
function renderTable(links) {
    const tbody = document.getElementById('links-table-body');
    if (links.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-16 text-center text-slate-500 text-sm">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>No download links found. Add one to get started.</span>
            </div>
        </td></tr>`;
        return;
    }

    const qualityColors = { '360p':'text-slate-400', '480p':'text-slate-300', '720p':'text-sky-400', '1080p':'text-violet-400', '2160p':'text-fuchsia-400', '4K':'text-fuchsia-400', 'Blu-ray':'text-amber-400', 'CAM':'text-rose-400' };

    tbody.innerHTML = links.map(link => {
        const poster = link.content_poster ? `https://image.tmdb.org/t/p/w92${link.content_poster}` : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(link.content_title.substring(0,2))}`;
        const typeBadge = link.content_type === 'movie'
            ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#0984E3]/20 text-[#0984E3] rounded-md">MOVIE</span>`
            : `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#00B894]/20 text-[#00B894] rounded-md">TV</span>`;
        const epBadge = (link.content_type === 'tv' && link.season_number !== null && link.episode_number !== null)
            ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-violet-500/20 text-violet-400 rounded-md">S${link.season_number} E${link.episode_number}</span>`
            : (link.content_type === 'tv' ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-slate-500/20 text-slate-400 rounded-md">Show Level</span>` : '');
        const qualColor = qualityColors[link.quality] || 'text-slate-300';
        const activeEl  = link.is_active
            ? `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2 py-0.5"><span class="w-1.5 h-1.5 bg-emerald-400 rounded-full inline-block"></span>Active</span>`
            : `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-white/5 border border-white/5 rounded-lg px-2 py-0.5">Inactive</span>`;

        return `<tr class="hover:bg-white/2 transition group">
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <img src="${poster}" class="w-8 h-11 rounded-lg object-cover bg-[#1E1E2E] flex-shrink-0"/>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            ${typeBadge}
                            ${epBadge}
                        </div>
                        <a href="/details/${link.content_type}/${link.content_id}" target="_blank" class="text-xs font-bold text-white hover:text-violet-400 transition line-clamp-1">${link.content_title}</a>
                        <p class="text-[10px] text-slate-500">ID: ${link.content_id}</p>
                    </div>
                </div>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-2">
                    <span class="text-base">${link.server_icon || '🔗'}</span>
                    <span class="text-sm font-semibold text-white">${link.server_name}</span>
                </div>
                ${link.notes ? `<p class="text-[10px] text-slate-500 mt-0.5">${link.notes}</p>` : ''}
            </td>
            <td class="px-5 py-3.5">
                <span class="text-xs font-extrabold ${qualColor} bg-white/5 rounded-lg px-2.5 py-1">${link.quality}</span>
            </td>
            <td class="px-5 py-3.5">
                <span class="text-xs text-slate-300">${link.language || 'English'}</span>
            </td>
            <td class="px-5 py-3.5">
                <span class="text-xs text-slate-400">${link.file_size || '—'}</span>
            </td>
            <td class="px-5 py-3.5">${activeEl}</td>
            <td class="px-5 py-3.5">
                <div class="flex gap-2">
                    <a href="${link.download_url}" target="_blank" class="p-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition" title="Test link">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <button onclick="openEditModal(${JSON.stringify(link).replace(/"/g,'&quot;')})" class="p-1.5 rounded-lg bg-violet-500/10 border border-violet-500/20 text-violet-400 hover:bg-violet-500/20 transition" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="openDeleteModal(${link.id})" class="p-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition" title="Delete">
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
    document.getElementById('pagination-info').innerText = `Page ${data.current_page} of ${data.last_page} — ${data.total} links`;
    document.getElementById('prev-btn').disabled = data.current_page <= 1;
    document.getElementById('next-btn').disabled = data.current_page >= data.last_page;
}

function updateStats(data) {
    document.getElementById('stat-total').innerText  = data.total ?? 0;
    const all = data.data || [];
    document.getElementById('stat-active').innerText = all.filter(l => l.is_active).length;
    document.getElementById('stat-movies').innerText = all.filter(l => l.content_type === 'movie').length;
    document.getElementById('stat-tv').innerText     = all.filter(l => l.content_type === 'tv').length;
}

function prevPage() { if (currentPage > 1) loadLinks(currentPage - 1); }
function nextPage() { if (currentPage < totalPages) loadLinks(currentPage + 1); }

// ── TMDB Search ───────────────────────────────────────────────────────────────
function searchTmdb() {
    clearTimeout(tmdbSearchTimer);
    const q = document.getElementById('tmdb-search').value.trim();
    if (q.length < 2) { document.getElementById('tmdb-results').classList.add('hidden'); return; }
    tmdbSearchTimer = setTimeout(async () => {
        try {
            const data = await fetch(`/api/tmdb/search/multi?query=${encodeURIComponent(q)}`).then(r => r.json());
            const results = (data.results || []).filter(r => r.media_type === 'movie' || r.media_type === 'tv').slice(0, 8);
            const container = document.getElementById('tmdb-results');
            if (results.length === 0) { container.classList.add('hidden'); return; }
            container.classList.remove('hidden');
            container.innerHTML = results.map(r => {
                const title = r.title || r.name;
                const year  = (r.release_date || r.first_air_date || '').substring(0,4);
                const poster = r.poster_path ? `https://image.tmdb.org/t/p/w92${r.poster_path}` : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(title.substring(0,2))}`;
                const typeBadge = r.media_type === 'movie' ? '🎬' : '📺';
                return `<button type="button" onclick='selectContent(${JSON.stringify({id:r.id,type:r.media_type,title:title,year:year,poster:r.poster_path||""}).replace(/'/g,"\\'")})'
                    class="flex items-center gap-3 w-full p-2.5 rounded-xl hover:bg-white/5 transition text-left border border-white/0 hover:border-violet-500/20">
                    <img src="${poster}" class="w-8 h-11 rounded-lg object-cover bg-[#1E1E2E] flex-shrink-0"/>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">${typeBadge} ${title}</p>
                        <p class="text-[10px] text-slate-400">${year} · ID ${r.id}</p>
                    </div>
                </button>`;
            }).join('');
        } catch(e) { console.error(e); }
    }, 350);
}

function toggleSeasonEpisodeFields(type) {
    const fields = document.getElementById('season-episode-fields');
    if (type === 'tv') {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
        document.getElementById('form-season-number').value = '';
        document.getElementById('form-episode-number').value = '';
    }
}

function selectContent(content) {
    document.getElementById('form-content-type').value  = content.type;
    document.getElementById('form-content-id').value    = content.id;
    document.getElementById('form-content-title').value = content.title;
    document.getElementById('form-content-poster').value = content.poster;

    document.getElementById('sel-poster').src = content.poster
        ? `https://image.tmdb.org/t/p/w92${content.poster}`
        : `https://placehold.co/40x56/1E1E2E/FFF?text=${encodeURIComponent(content.title.substring(0,2))}`;
    document.getElementById('sel-title').innerText = content.title;
    document.getElementById('sel-meta').innerText  = `${content.type === 'movie' ? '🎬 Movie' : '📺 TV Show'} · ${content.year}`;

    document.getElementById('selected-content').classList.remove('hidden');
    document.getElementById('tmdb-results').classList.add('hidden');
    document.getElementById('tmdb-search').value = '';

    toggleSeasonEpisodeFields(content.type);
}

function clearSelectedContent() {
    ['form-content-type','form-content-id','form-content-title','form-content-poster'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('selected-content').classList.add('hidden');
    toggleSeasonEpisodeFields(null);
}

// ── Add Modal ─────────────────────────────────────────────────────────────────
function openAddModal() {
    editingId = null;
    document.getElementById('modal-title').innerText = 'Add Download Link';
    document.getElementById('link-form').reset();
    document.getElementById('form-id').value = '';
    document.getElementById('form-is-active').checked = true;
    clearSelectedContent();
    document.getElementById('tmdb-search-section').classList.remove('hidden');
    document.getElementById('tmdb-results').classList.add('hidden');
    document.getElementById('link-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    toggleSeasonEpisodeFields(null);
}

function openEditModal(link) {
    editingId = link.id;
    document.getElementById('modal-title').innerText = 'Edit Download Link';
    document.getElementById('tmdb-search-section').classList.add('hidden');

    document.getElementById('form-id').value            = link.id;
    document.getElementById('form-content-type').value  = link.content_type;
    document.getElementById('form-content-id').value    = link.content_id;
    document.getElementById('form-content-title').value = link.content_title;
    document.getElementById('form-content-poster').value = link.content_poster || '';
    document.getElementById('form-server-name').value   = link.server_name;
    document.getElementById('form-server-icon').value   = link.server_icon || '🔗';
    document.getElementById('form-quality').value       = link.quality;
    document.getElementById('form-language').value      = link.language || 'English';
    document.getElementById('form-file-size').value     = link.file_size || '';
    document.getElementById('form-download-url').value  = link.download_url;
    document.getElementById('form-notes').value         = link.notes || '';
    document.getElementById('form-is-active').checked   = !!link.is_active;

    toggleSeasonEpisodeFields(link.content_type);
    document.getElementById('form-season-number').value = link.season_number || '';
    document.getElementById('form-episode-number').value = link.episode_number || '';

    document.getElementById('link-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLinkModal(event) {
    if (event && event.target !== document.getElementById('link-modal')) return;
    document.getElementById('link-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Submit Form ───────────────────────────────────────────────────────────────
async function submitLinkForm(event) {
    event.preventDefault();

    const contentType = document.getElementById('form-content-type').value;
    const contentId   = document.getElementById('form-content-id').value;
    if (!editingId && (!contentType || !contentId)) {
        showToast('Please select a movie or TV show first.', 'error');
        return;
    }

    const seasonVal = document.getElementById('form-season-number').value;
    const episodeVal = document.getElementById('form-episode-number').value;

    const payload = {
        content_type:   contentType,
        content_id:     parseInt(contentId),
        content_title:  document.getElementById('form-content-title').value,
        content_poster: document.getElementById('form-content-poster').value,
        season_number:  (contentType === 'tv' && seasonVal) ? parseInt(seasonVal) : null,
        episode_number: (contentType === 'tv' && episodeVal) ? parseInt(episodeVal) : null,
        server_name:    document.getElementById('form-server-name').value,
        server_icon:    document.getElementById('form-server-icon').value || '🔗',
        quality:        document.getElementById('form-quality').value,
        language:       document.getElementById('form-language').value || 'English',
        file_size:      document.getElementById('form-file-size').value,
        download_url:   document.getElementById('form-download-url').value,
        notes:          document.getElementById('form-notes').value,
        is_active:      document.getElementById('form-is-active').checked,
    };

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    try {
        const method = editingId ? 'PUT' : 'POST';
        const url    = editingId ? `/admin/api/download-links/${editingId}` : '/admin/api/download-links';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error(await res.text());
        closeLinkModal();
        loadLinks(currentPage);
        showToast(editingId ? 'Link updated successfully!' : 'Download link added!', 'success');
    } catch(e) {
        showToast('Error saving: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Download Link';
    }
}

// ── Delete ────────────────────────────────────────────────────────────────────
function openDeleteModal(id) {
    deleteTargetId = id;
    document.getElementById('delete-modal').classList.remove('hidden');
}

async function confirmDelete() {
    if (!deleteTargetId) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        await fetch(`/admin/api/download-links/${deleteTargetId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        document.getElementById('delete-modal').classList.add('hidden');
        loadLinks(currentPage);
        showToast('Link deleted.', 'success');
    } catch(e) {
        showToast('Delete failed.', 'error');
    }
    deleteTargetId = null;
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
