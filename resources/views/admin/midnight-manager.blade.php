@extends('layouts.layout')
@section('title', 'Midnight 18+ Manager — ENGORA Admin')

@section('content')
<div class="px-6 py-8 max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-white tracking-tight">🍸 Midnight 18+ Manager</h1>
                <span class="px-2 py-0.5 text-[11px] font-black bg-[#FF1A75]/20 text-[#FF1A75] border border-[#FF1A75]/40 rounded-md">MANUAL 18+ CURATION</span>
            </div>
            <p class="text-slate-400 text-sm mt-1">Manage manual late-night nightclub categories and content streams. Disconnected from TMDB — 100% curated custom content.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openAddContentModal()" class="inline-flex items-center gap-2 bg-[#1E1E2E] hover:bg-white/10 text-slate-200 font-bold px-4 py-2.5 rounded-2xl border border-white/10 transition text-sm">
                <svg class="w-4 h-4 text-[#FF1A75]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Content to Midnight
            </button>
            <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white font-bold px-5 py-2.5 rounded-2xl hover:brightness-110 transition shadow-lg shadow-[#FF1A75]/25 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Category
            </button>
        </div>
    </div>

    <!-- Admin Modules Navigation Tabs -->
    <div class="flex flex-wrap gap-2.5 pb-2">
        <a href="{{ route('admin.movie-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.movie-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🎬 Movie Manager
        </a>
        <a href="{{ route('admin.tv-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.tv-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            📺 TV Shows Manager
        </a>
        <a href="{{ route('admin.anime-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.anime-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            ⛩️ Anime Manager
        </a>
        <a href="{{ route('admin.home-section-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.home-section-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🔥 Home Sections
        </a>
        <a href="{{ route('admin.midnight-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.midnight-manager') ? 'bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white shadow-lg shadow-[#FF1A75]/20 border border-[#FF1A75]/30' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🍸 Midnight 18+ Manager
        </a>
        <a href="{{ route('admin.video-servers') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.video-servers') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            ⚙️ Video Servers
        </a>
        <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.settings') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🛠️ Settings
        </a>
    </div>

    <!-- ══════════════════════════════════════════════════════
         1. Manual Categories Section
    ══════════════════════════════════════════════════════ -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-white">Manual Midnight Categories (Shelves)</h2>
                <p class="text-xs text-slate-400">Each active category appears as a curated row and chip on the mobile 18+ Midnight screen.</p>
            </div>
            <span id="categories-count-badge" class="px-2.5 py-1 text-xs font-bold bg-white/5 rounded-xl text-slate-300 border border-white/10">0 Categories</span>
        </div>

        <div class="glass rounded-3xl overflow-hidden border border-white/8 bg-[#121220]">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/5 text-left bg-white/[0.02]">
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Sort</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Emoji</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Category Title & Tagline</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Streams</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sections-table-body" class="divide-y divide-white/5 text-slate-200">
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading Midnight categories...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════
         2. Midnight 18+ Content Roster (Movies, TV & Anime)
    ══════════════════════════════════════════════════════ -->
    <div class="space-y-4 pt-4 border-t border-white/5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-white flex items-center gap-2">
                    <span>🌙 Midnight 18+ Content Roster</span>
                    <span id="content-count-badge" class="px-2 py-0.5 text-[11px] font-black bg-[#FF1A75]/20 text-[#FF1A75] border border-[#FF1A75]/40 rounded-md">0 Streams</span>
                </h2>
                <p class="text-xs text-slate-400">All custom Movies, TV Shows, and Anime tagged with <strong class="text-white">"Include in Midnight"</strong>.</p>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <input id="content-search" type="text" placeholder="Search Midnight streams..." oninput="filterContent()" class="bg-[#181826] border border-white/10 text-white text-xs rounded-xl px-3.5 py-2 placeholder-slate-500 focus:outline-none focus:border-[#FF1A75] transition">
                <select id="content-category-filter" onchange="filterContent()" class="bg-[#181826] border border-white/10 text-slate-300 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-[#FF1A75] transition">
                    <option value="">All Categories</option>
                </select>
                <select id="content-type-filter" onchange="filterContent()" class="bg-[#181826] border border-white/10 text-slate-300 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-[#FF1A75] transition">
                    <option value="">All Types</option>
                    <option value="movie">🎬 Movies</option>
                    <option value="tv">📺 TV Shows</option>
                </select>
            </div>
        </div>

        <div class="glass rounded-3xl overflow-hidden border border-white/8 bg-[#121220]">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/5 text-left bg-white/[0.02]">
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Stream / TMDB</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Type</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Midnight Category</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Rating</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="content-table-body" class="divide-y divide-white/5 text-slate-200">
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading Midnight content roster...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     Add / Edit Category Modal
══════════════════════════════════════════════════════ -->
<div id="section-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeModal(event)">
    <div id="section-modal-panel" class="w-full max-w-xl bg-[#121220] rounded-3xl border border-white/10 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <div class="flex items-center gap-2">
                <span class="text-xl">🍸</span>
                <h3 id="modal-title" class="text-lg font-black text-white">Add Midnight Category</h3>
            </div>
            <button onclick="closeModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="section-form" onsubmit="saveSection(event)" class="p-6 space-y-5">
            <input type="hidden" id="section-id">

            <!-- Presets Section -->
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2">QUICK CATEGORY PRESETS</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="applyPreset('vip')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-[#FF1A75]/20 hover:text-[#FF1A75] border border-white/5 hover:border-[#FF1A75]/40 transition text-slate-300 font-semibold">🍾 VIP Exclusives</button>
                    <button type="button" onclick="applyPreset('noir')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-[#FF1A75]/20 hover:text-[#FF1A75] border border-white/5 hover:border-[#FF1A75]/40 transition text-slate-300 font-semibold">🌙 Neon Noir</button>
                    <button type="button" onclick="applyPreset('passion')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-[#FF1A75]/20 hover:text-[#FF1A75] border border-white/5 hover:border-[#FF1A75]/40 transition text-slate-300 font-semibold">💋 After Hours Passion</button>
                    <button type="button" onclick="applyPreset('horror')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-[#FF1A75]/20 hover:text-[#FF1A75] border border-white/5 hover:border-[#FF1A75]/40 transition text-slate-300 font-semibold">💀 Midnight Horror</button>
                    <button type="button" onclick="applyPreset('psych')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-[#FF1A75]/20 hover:text-[#FF1A75] border border-white/5 hover:border-[#FF1A75]/40 transition text-slate-300 font-semibold">🔮 Mindbenders</button>
                    <button type="button" onclick="applyPreset('club')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-[#FF1A75]/20 hover:text-[#FF1A75] border border-white/5 hover:border-[#FF1A75]/40 transition text-slate-300 font-semibold">🎧 Nightclub Beats</button>
                </div>
            </div>

            <!-- Emoji & Title -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Emoji</label>
                    <input type="text" id="sec-emoji" placeholder="🍸" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-center text-lg focus:outline-none focus:border-[#FF1A75] transition">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Category Title *</label>
                    <input type="text" id="sec-title" required placeholder="e.g. Neon Noir & Nightlife Crime" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
                </div>
            </div>

            <!-- Tagline -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Tagline / Subtitle</label>
                <input type="text" id="sec-tagline" placeholder="e.g. Dark alleys, gritty undergrounds, and midnight heists" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
            </div>

            <!-- Sort Order & Active -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Sort Order</label>
                    <input type="number" id="sec-sort-order" value="0" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="sec-is-active" checked class="w-4 h-4 rounded bg-[#181826] border-white/10 text-[#FF1A75] focus:ring-[#FF1A75] accent-[#FF1A75]">
                        <span class="text-sm font-semibold text-slate-200">Active (Visible in Midnight)</span>
                    </label>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="pt-4 border-t border-white/5 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition text-sm font-semibold">Cancel</button>
                <button type="submit" id="save-btn" class="bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white font-bold px-6 py-2.5 rounded-xl hover:brightness-110 transition shadow-lg shadow-[#FF1A75]/25 text-sm">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     Quick Add Content to Midnight Modal
══════════════════════════════════════════════════════ -->
<div id="add-content-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeAddContentModal(event)">
    <div id="add-content-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/10 shadow-2xl overflow-hidden mx-4 max-h-[90vh] flex flex-col">
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center bg-[#121220]">
            <div>
                <h3 class="text-lg font-black text-white flex items-center gap-2">
                    <span>🌙 Add Content to Midnight</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Search your custom movies, TV shows, and anime to flag them for Midnight.</p>
            </div>
            <button onclick="closeAddContentModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 border-b border-white/5">
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="modal-content-search" type="text" placeholder="Type title to search library..." oninput="searchLibraryContent()" class="w-full bg-[#181826] border border-white/10 text-white text-sm rounded-xl pl-10 pr-4 py-3 placeholder-slate-500 focus:outline-none focus:border-[#FF1A75] transition">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-3 scrollbar-thin max-h-96" id="available-content-list">
            <div class="text-center text-slate-500 py-12 text-sm">Type in the search bar above to find content from your library.</div>
        </div>
    </div>
</div>

<script>
    let sectionsData = [];
    let contentData = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const presets = {
        vip: {
            emoji: '🍾',
            title: 'VIP Lounge Exclusives',
            tagline: 'Hand-curated adult late-night streams'
        },
        noir: {
            emoji: '🌙',
            title: 'Neon Noir & Nightlife Crime',
            tagline: 'Dark alleys, gritty undergrounds, and midnight heists'
        },
        passion: {
            emoji: '💋',
            title: 'After Hours & Passion',
            tagline: 'Intense, sensual, and mature late-night romance'
        },
        horror: {
            emoji: '💀',
            title: 'Midnight Madness & Horror',
            tagline: 'Sinister chills and screams for the dead of night'
        },
        psych: {
            emoji: '🔮',
            title: 'Late Night Mindbenders',
            tagline: 'Twisted psychological thrillers that keep you awake'
        },
        club: {
            emoji: '🎧',
            title: 'Electronic Beats & Nightclub',
            tagline: 'High-energy dance, electronic nightlife, and music'
        }
    };

    function applyPreset(key) {
        const p = presets[key];
        if (!p) return;
        document.getElementById('sec-emoji').value = p.emoji;
        document.getElementById('sec-title').value = p.title;
        document.getElementById('sec-tagline').value = p.tagline;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadSections();
        loadContent();
    });

    // ── Load Categories ──────────────────────────────────────────────────────────
    async function loadSections() {
        try {
            const res = await fetch('/admin/api/midnight-sections');
            sectionsData = await res.json();
            renderCategoriesTable();
            updateCategoryFilterDropdown();
        } catch (e) {
            console.error('Failed to load categories', e);
            document.getElementById('sections-table-body').innerHTML = `<tr><td colspan="6" class="px-5 py-8 text-center text-rose-400">Failed to load Midnight categories.</td></tr>`;
        }
    }

    function renderCategoriesTable() {
        const tbody = document.getElementById('sections-table-body');
        document.getElementById('categories-count-badge').textContent = `${sectionsData.length} Categories`;

        if (!sectionsData || sectionsData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">No Midnight categories configured yet. Click "New Category" to create one.</td></tr>`;
            return;
        }

        tbody.innerHTML = sectionsData.map(sec => `
            <tr class="hover:bg-white/[0.02] transition group">
                <td class="px-5 py-4 font-bold text-slate-400 text-xs">${sec.sort_order ?? 0}</td>
                <td class="px-5 py-4 text-xl">${sec.emoji || '🍸'}</td>
                <td class="px-5 py-4">
                    <div class="font-bold text-white text-sm group-hover:text-[#FF1A75] transition">${sec.title}</div>
                    <div class="text-xs text-slate-400 mt-0.5">${sec.tagline || '—'}</div>
                </td>
                <td class="px-5 py-4">
                    <span class="px-2.5 py-1 text-xs font-bold bg-[#FF1A75]/15 text-[#FF1A75] border border-[#FF1A75]/30 rounded-xl">
                        🎬 ${sec.custom_movies_count ?? 0} Streams
                    </span>
                </td>
                <td class="px-5 py-4">
                    <button onclick="toggleActive(${sec.id}, ${sec.is_active ? 0 : 1})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${sec.is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700'}">
                        <span class="w-1.5 h-1.5 rounded-full ${sec.is_active ? 'bg-emerald-400' : 'bg-slate-500'}"></span>
                        ${sec.is_active ? 'Active' : 'Hidden'}
                    </button>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <button onclick="openEditModal(${sec.id})" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button onclick="deleteSection(${sec.id})" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function updateCategoryFilterDropdown() {
        const filterSel = document.getElementById('content-category-filter');
        const currentVal = filterSel.value;
        filterSel.innerHTML = '<option value="">All Categories</option>' +
            sectionsData.map(s => `<option value="${s.id}">${s.emoji || '🍸'} ${s.title}</option>`).join('');
        filterSel.value = currentVal;
    }

    // ── Load Content Roster ──────────────────────────────────────────────────────
    async function loadContent() {
        const q = document.getElementById('content-search')?.value || '';
        const catId = document.getElementById('content-category-filter')?.value || '';
        const type = document.getElementById('content-type-filter')?.value || '';

        let url = `/admin/api/midnight-content?1=1`;
        if (q) url += `&search=${encodeURIComponent(q)}`;
        if (catId) url += `&category_id=${catId}`;
        if (type) url += `&type=${type}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            contentData = data.data || [];
            renderContentTable();
        } catch(e) {
            console.error('Failed to load content roster', e);
        }
    }

    function filterContent() {
        loadContent();
    }

    function renderContentTable() {
        const tbody = document.getElementById('content-table-body');
        document.getElementById('content-count-badge').textContent = `${contentData.length} Streams`;

        if (!contentData || contentData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">No Midnight streams found. Click "Add Content to Midnight" to include movies, TV shows, or anime.</td></tr>`;
            return;
        }

        tbody.innerHTML = contentData.map(movie => {
            const poster = movie.poster_path 
                ? (movie.poster_path.startsWith('/') ? 'https://image.tmdb.org/t/p/w92' + movie.poster_path : movie.poster_path)
                : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(movie.title.substring(0,2))}`;

            const typeBadge = movie.type === 'tv'
                ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#00B894]/20 text-[#00B894] rounded-md">TV</span>`
                : (movie.genre_ids && movie.genre_ids.includes(16)
                    ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#FF6B9D]/20 text-[#FF6B9D] rounded-md">ANIME</span>`
                    : `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#0984E3]/20 text-[#0984E3] rounded-md">MOVIE</span>`);

            // Category options dropdown
            const categoryOptions = `<option value="">🍸 General / All Midnight</option>` +
                sectionsData.map(s => `<option value="${s.id}" ${movie.midnight_section_id === s.id ? 'selected' : ''}>${s.emoji || '🍸'} ${s.title}</option>`).join('');

            return `
                <tr class="hover:bg-white/[0.02] transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <img src="${poster}" class="w-8 h-11 rounded-lg object-cover bg-[#1E1E2E] flex-shrink-0"/>
                            <div class="min-w-0">
                                <a href="/details/custom/${movie.id}" target="_blank" class="text-xs font-bold text-white hover:text-[#FF1A75] transition line-clamp-1">${movie.title}</a>
                                <p class="text-[10px] text-slate-500 mt-0.5">TMDB ID: ${movie.tmdb_id} · Year: ${movie.year || '—'}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">${typeBadge}</td>
                    <td class="px-5 py-3.5">
                        <select onchange="updateMovieCategory(${movie.id}, this.value)" class="bg-[#181826] border border-[#FF1A75]/30 text-white text-xs rounded-xl px-3 py-1.5 focus:outline-none focus:border-[#FF1A75] transition">
                            ${categoryOptions}
                        </select>
                    </td>
                    <td class="px-5 py-3.5"><span class="text-xs text-amber-400 font-extrabold">⭐ ${movie.rating || '0.0'}</span></td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#FF1A75] bg-[#FF1A75]/10 border border-[#FF1A75]/25 rounded-lg px-2 py-0.5">
                            🌙 18+ VIP
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <button onclick="removeFromMidnight(${movie.id})" class="px-3 py-1 text-xs font-bold rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition" title="Remove from Midnight">
                            Remove
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function updateMovieCategory(movieId, categoryId) {
        try {
            await fetch(`/admin/api/midnight-content/toggle/${movieId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ midnight_section_id: categoryId || null })
            });
            await loadSections(); // refresh counts
        } catch(e) {
            alert('Failed to update category: ' + e.message);
        }
    }

    async function removeFromMidnight(movieId) {
        if (!confirm('Remove this stream from Midnight 18+? It will remain in your main library.')) return;
        try {
            await fetch(`/admin/api/midnight-content/toggle/${movieId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ is_midnight: false, midnight_section_id: null })
            });
            await loadSections();
            await loadContent();
        } catch(e) {
            alert('Failed to remove: ' + e.message);
        }
    }

    // ── Quick Add Content Modal ──────────────────────────────────────────────────
    let availableSearchTimer = null;

    function openAddContentModal() {
        document.getElementById('modal-content-search').value = '';
        document.getElementById('available-content-list').innerHTML = `<div class="text-center text-slate-500 py-12 text-sm">Type in the search bar above to find content from your library.</div>`;
        document.getElementById('add-content-modal').classList.remove('hidden');
        document.getElementById('modal-content-search').focus();
    }

    function closeAddContentModal(e) {
        if (!e || e.target.id === 'add-content-modal' || e.target.closest('button')) {
            document.getElementById('add-content-modal').classList.add('hidden');
        }
    }

    function searchLibraryContent() {
        clearTimeout(availableSearchTimer);
        const q = document.getElementById('modal-content-search').value.trim();
        availableSearchTimer = setTimeout(async () => {
            try {
                const res = await fetch(`/admin/api/midnight-content/search-available?q=${encodeURIComponent(q)}`);
                const items = await res.json();
                renderAvailableList(items);
            } catch(e) {
                console.error(e);
            }
        }, 250);
    }

    function renderAvailableList(items) {
        const container = document.getElementById('available-content-list');
        if (!items || items.length === 0) {
            container.innerHTML = `<div class="text-center text-slate-500 py-12 text-sm">No content found matching your search.</div>`;
            return;
        }

        container.innerHTML = items.map(m => {
            const poster = m.poster_path 
                ? (m.poster_path.startsWith('/') ? 'https://image.tmdb.org/t/p/w92' + m.poster_path : m.poster_path)
                : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(m.title.substring(0,2))}`;

            const isAlreadyMidnight = !!m.is_midnight;
            const categorySelectId = `add-cat-sel-${m.id}`;

            const catOptions = `<option value="">🍸 General / All Midnight</option>` +
                sectionsData.map(s => `<option value="${s.id}" ${m.midnight_section_id === s.id ? 'selected' : ''}>${s.emoji || '🍸'} ${s.title}</option>`).join('');

            return `
                <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-[#FF1A75]/30 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="${poster}" class="w-9 h-12 rounded-xl object-cover bg-[#1E1E2E] flex-shrink-0"/>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">${m.title}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 uppercase tracking-wider">${m.type} · ${m.year || '—'}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <select id="${categorySelectId}" class="bg-[#181826] border border-white/10 text-white text-xs rounded-xl px-2.5 py-1.5 focus:outline-none focus:border-[#FF1A75] transition">
                            ${catOptions}
                        </select>
                        <button onclick="addMovieToMidnight(${m.id}, document.getElementById('${categorySelectId}').value)" 
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition ${isAlreadyMidnight ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white hover:brightness-110 shadow-lg shadow-[#FF1A75]/20'}">
                            ${isAlreadyMidnight ? '✓ In Midnight' : '+ Add to Midnight'}
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function addMovieToMidnight(movieId, categoryId) {
        try {
            await fetch(`/admin/api/midnight-content/toggle/${movieId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ is_midnight: true, midnight_section_id: categoryId || null })
            });
            await loadSections();
            await loadContent();
            searchLibraryContent(); // refresh search list
        } catch(e) {
            alert('Failed to add: ' + e.message);
        }
    }

    // ── Category Modal Logic ─────────────────────────────────────────────────────
    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Add Midnight Category';
        document.getElementById('section-id').value = '';
        document.getElementById('sec-emoji').value = '🍸';
        document.getElementById('sec-title').value = '';
        document.getElementById('sec-tagline').value = '';
        document.getElementById('sec-sort-order').value = sectionsData.length;
        document.getElementById('sec-is-active').checked = true;

        document.getElementById('section-modal').classList.remove('hidden');
    }

    function openEditModal(id) {
        const sec = sectionsData.find(s => s.id === id);
        if (!sec) return;

        document.getElementById('modal-title').textContent = 'Edit Midnight Category';
        document.getElementById('section-id').value = sec.id;
        document.getElementById('sec-emoji').value = sec.emoji || '🍸';
        document.getElementById('sec-title').value = sec.title || '';
        document.getElementById('sec-tagline').value = sec.tagline || '';
        document.getElementById('sec-sort-order').value = sec.sort_order ?? 0;
        document.getElementById('sec-is-active').checked = !!sec.is_active;

        document.getElementById('section-modal').classList.remove('hidden');
    }

    function closeModal(e) {
        if (!e || e.target.id === 'section-modal' || e.target.closest('button')) {
            document.getElementById('section-modal').classList.add('hidden');
        }
    }

    async function saveSection(e) {
        e.preventDefault();
        const id = document.getElementById('section-id').value;

        const payload = {
            emoji: document.getElementById('sec-emoji').value.trim(),
            title: document.getElementById('sec-title').value.trim(),
            tagline: document.getElementById('sec-tagline').value.trim(),
            sort_order: parseInt(document.getElementById('sec-sort-order').value) || 0,
            is_active: document.getElementById('sec-is-active').checked,
        };

        const saveBtn = document.getElementById('save-btn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const url = id ? `/admin/api/midnight-sections/${id}` : '/admin/api/midnight-sections';
            const method = id ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.message || 'Failed to save category');
            }

            document.getElementById('section-modal').classList.add('hidden');
            await loadSections();
            await loadContent();
        } catch (err) {
            alert(err.message);
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Category';
        }
    }

    async function toggleActive(id, newState) {
        try {
            const res = await fetch(`/admin/api/midnight-sections/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ is_active: !!newState, title: sectionsData.find(s => s.id === id)?.title }),
            });

            if (!res.ok) throw new Error('Failed to toggle status');
            await loadSections();
        } catch (err) {
            alert(err.message);
        }
    }

    async function deleteSection(id) {
        if (!confirm('Are you sure you want to delete this Midnight category? Content in this category will become general Midnight content.')) return;

        try {
            const res = await fetch(`/admin/api/midnight-sections/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!res.ok) throw new Error('Failed to delete category');
            await loadSections();
            await loadContent();
        } catch (err) {
            alert(err.message);
        }
    }
</script>
@endsection
