@extends('layouts.layout')
@section('title', 'Midnight 18+ Manager — ENGORA Admin')

@section('content')
<div class="px-6 py-8 max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-white tracking-tight">🍸 Midnight 18+ Manager</h1>
                <span class="px-2 py-0.5 text-[11px] font-black bg-[#FF1A75]/20 text-[#FF1A75] border border-[#FF1A75]/40 rounded-md">ADULT VIP</span>
            </div>
            <p class="text-slate-400 text-sm mt-1">Dynamically manage and curate late-night cinema sections and adult streams.</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white font-bold px-5 py-2.5 rounded-2xl hover:brightness-110 transition shadow-lg shadow-[#FF1A75]/25 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Midnight Section
        </button>
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

    <!-- Sections List Card -->
    <div class="glass rounded-3xl overflow-hidden border border-white/8 bg-[#121220]">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5 text-left bg-white/[0.02]">
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Sort</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Emoji</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Title & Tagline</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Endpoint</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Params</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="sections-table-body" class="divide-y divide-white/5">
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading Midnight sections...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add / Edit Modal -->
<div id="section-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeModal(event)">
    <div id="section-modal-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/10 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <div class="flex items-center gap-2">
                <span class="text-xl">🍸</span>
                <h3 id="modal-title" class="text-lg font-black text-white">Add Midnight Section</h3>
            </div>
            <button onclick="closeModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="section-form" onsubmit="saveSection(event)" class="p-6 space-y-5">
            <input type="hidden" id="section-id">

            <!-- Presets Section -->
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2">QUICK NIGHTCLUB PRESETS</label>
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
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Section Title *</label>
                    <input type="text" id="sec-title" required placeholder="e.g. Neon Noir & Nightlife Crime" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
                </div>
            </div>

            <!-- Tagline -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Tagline / Subtitle</label>
                <input type="text" id="sec-tagline" placeholder="e.g. Dark alleys, gritty undergrounds, and midnight heists" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
            </div>

            <!-- Endpoint & Media Type -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Endpoint *</label>
                    <input type="text" id="sec-endpoint" required placeholder="discover/movie or custom" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
                    <span class="text-[10px] text-slate-500 mt-1 block">Use 'custom' for your Custom Content, or TMDB paths like 'discover/movie'.</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Media Type</label>
                    <select id="sec-media-type" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
                        <option value="movie">Movie</option>
                        <option value="tv">TV Show</option>
                    </select>
                </div>
            </div>

            <!-- Parameters (JSON) -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-xs font-bold text-slate-300">TMDB Parameters (JSON)</label>
                    <span class="text-[11px] text-slate-500">Must be valid JSON object</span>
                </div>
                <textarea id="sec-params" rows="3" placeholder='{"with_genres": "80,53", "sort_by": "popularity.desc", "include_adult": "true"}' class="w-full bg-[#181826] border border-white/8 rounded-xl p-3 text-white font-mono text-xs focus:outline-none focus:border-[#FF1A75] transition"></textarea>
            </div>

            <!-- Sort Order & Active -->
            <div class="grid grid-cols-2 gap-4 items-center">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Sort Order</label>
                    <input type="number" id="sec-sort-order" value="0" min="0" class="w-full bg-[#181826] border border-white/8 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#FF1A75] transition">
                </div>
                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="sec-is-active" checked class="w-4 h-4 rounded bg-[#181826] border-white/10 text-[#FF1A75] focus:ring-[#FF1A75] accent-[#FF1A75]">
                        <span class="text-sm font-semibold text-slate-200">Active (Visible in Midnight)</span>
                    </label>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="pt-4 border-t border-white/5 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition text-sm font-semibold">Cancel</button>
                <button type="submit" id="save-btn" class="bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white font-bold px-6 py-2.5 rounded-xl hover:brightness-110 transition shadow-lg shadow-[#FF1A75]/25 text-sm">Save Section</button>
            </div>
        </form>
    </div>
</div>

<script>
    let sectionsData = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const presets = {
        vip: {
            emoji: '🍾',
            title: 'VIP Nightclub Exclusives',
            tagline: 'Hand-curated adult late-night streams',
            endpoint: 'custom',
            media_type: 'movie',
            params: '{}'
        },
        noir: {
            emoji: '🌙',
            title: 'Neon Noir & Nightlife Crime',
            tagline: 'Dark alleys, gritty undergrounds, and midnight heists',
            endpoint: 'discover/movie',
            media_type: 'movie',
            params: JSON.stringify({ with_genres: '80,53', sort_by: 'popularity.desc', include_adult: 'true' }, null, 2)
        },
        passion: {
            emoji: '💋',
            title: 'After Hours & Passion',
            tagline: 'Intense, sensual, and mature late-night romance',
            endpoint: 'discover/movie',
            media_type: 'movie',
            params: JSON.stringify({ with_genres: '10749,18', sort_by: 'popularity.desc', include_adult: 'true' }, null, 2)
        },
        horror: {
            emoji: '💀',
            title: 'Midnight Madness & Horror',
            tagline: 'Sinister chills and screams for the dead of night',
            endpoint: 'discover/movie',
            media_type: 'movie',
            params: JSON.stringify({ with_genres: '27,53', sort_by: 'popularity.desc', include_adult: 'true' }, null, 2)
        },
        psych: {
            emoji: '🔮',
            title: 'Late Night Mindbenders',
            tagline: 'Twisted psychological thrillers that keep you awake',
            endpoint: 'discover/movie',
            media_type: 'movie',
            params: JSON.stringify({ with_genres: '9648,53', sort_by: 'vote_average.desc', 'vote_count.gte': '100', include_adult: 'true' }, null, 2)
        },
        club: {
            emoji: '🎧',
            title: 'Electronic Beats & Nightclub',
            tagline: 'High-energy dance, electronic nightlife, and music',
            endpoint: 'discover/movie',
            media_type: 'movie',
            params: JSON.stringify({ with_genres: '10402,28', sort_by: 'popularity.desc', include_adult: 'true' }, null, 2)
        }
    };

    function applyPreset(key) {
        const p = presets[key];
        if (!p) return;
        document.getElementById('sec-emoji').value = p.emoji;
        document.getElementById('sec-title').value = p.title;
        document.getElementById('sec-tagline').value = p.tagline;
        document.getElementById('sec-endpoint').value = p.endpoint;
        document.getElementById('sec-media-type').value = p.media_type;
        document.getElementById('sec-params').value = p.params;
    }

    document.addEventListener('DOMContentLoaded', loadSections);

    async function loadSections() {
        try {
            const res = await fetch('/admin/api/midnight-sections');
            sectionsData = await res.json();
            renderTable();
        } catch (e) {
            console.error('Failed to load sections', e);
            document.getElementById('sections-table-body').innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-rose-400">Failed to load Midnight sections.</td></tr>`;
        }
    }

    function renderTable() {
        const tbody = document.getElementById('sections-table-body');
        if (!sectionsData || sectionsData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-12 text-center text-slate-500">No Midnight sections configured yet. Click "Add Midnight Section" to create one.</td></tr>`;
            return;
        }

        tbody.innerHTML = sectionsData.map((sec) => `
            <tr class="hover:bg-white/[0.02] transition">
                <td class="px-5 py-4 font-mono text-slate-400 text-xs">${sec.sort_order ?? 0}</td>
                <td class="px-5 py-4 text-xl">${sec.emoji || '🍸'}</td>
                <td class="px-5 py-4">
                    <div class="font-bold text-white text-sm">${sec.title}</div>
                    ${sec.tagline ? `<div class="text-xs text-slate-400 mt-0.5">${sec.tagline}</div>` : ''}
                </td>
                <td class="px-5 py-4 font-mono text-xs text-[#FF1A75]">${sec.endpoint}</td>
                <td class="px-5 py-4 font-mono text-xs text-slate-400 max-w-xs truncate" title='${JSON.stringify(sec.params || {})}'>
                    ${JSON.stringify(sec.params || {})}
                </td>
                <td class="px-5 py-4">
                    <button onclick="toggleActive(${sec.id})" class="px-2.5 py-1 rounded-full text-xs font-bold transition ${sec.is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700/30 text-slate-400 border border-white/5'}">
                        ${sec.is_active ? 'Active' : 'Disabled'}
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

    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Add Midnight Section';
        document.getElementById('section-id').value = '';
        document.getElementById('sec-emoji').value = '🍸';
        document.getElementById('sec-title').value = '';
        document.getElementById('sec-tagline').value = '';
        document.getElementById('sec-endpoint').value = 'discover/movie';
        document.getElementById('sec-params').value = '{\n  "with_genres": "80,53",\n  "sort_by": "popularity.desc",\n  "include_adult": "true"\n}';
        document.getElementById('sec-media-type').value = 'movie';
        document.getElementById('sec-sort-order').value = sectionsData.length;
        document.getElementById('sec-is-active').checked = true;

        document.getElementById('section-modal').classList.remove('hidden');
    }

    function openEditModal(id) {
        const sec = sectionsData.find(s => s.id === id);
        if (!sec) return;

        document.getElementById('modal-title').textContent = 'Edit Midnight Section';
        document.getElementById('section-id').value = sec.id;
        document.getElementById('sec-emoji').value = sec.emoji || '🍸';
        document.getElementById('sec-title').value = sec.title || '';
        document.getElementById('sec-tagline').value = sec.tagline || '';
        document.getElementById('sec-endpoint').value = sec.endpoint || 'discover/movie';
        document.getElementById('sec-params').value = JSON.stringify(sec.params || {}, null, 2);
        document.getElementById('sec-media-type').value = sec.media_type || 'movie';
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

        let parsedParams = {};
        const rawParams = document.getElementById('sec-params').value.trim();
        if (rawParams) {
            try {
                parsedParams = JSON.parse(rawParams);
            } catch (err) {
                alert('Invalid JSON in TMDB Parameters. Please format as valid JSON.');
                return;
            }
        }

        const payload = {
            emoji: document.getElementById('sec-emoji').value.trim(),
            title: document.getElementById('sec-title').value.trim(),
            tagline: document.getElementById('sec-tagline').value.trim(),
            endpoint: document.getElementById('sec-endpoint').value.trim(),
            params: parsedParams,
            media_type: document.getElementById('sec-media-type').value,
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
                throw new Error(data.message || 'Failed to save section');
            }

            document.getElementById('section-modal').classList.add('hidden');
            await loadSections();
        } catch (err) {
            alert(err.message);
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Section';
        }
    }

    async function toggleActive(id) {
        const sec = sectionsData.find(s => s.id === id);
        if (!sec) return;

        try {
            const res = await fetch(`/admin/api/midnight-sections/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    ...sec,
                    is_active: !sec.is_active,
                }),
            });

            if (!res.ok) throw new Error('Failed to toggle status');
            await loadSections();
        } catch (err) {
            alert(err.message);
        }
    }

    async function deleteSection(id) {
        if (!confirm('Are you sure you want to delete this Midnight section?')) return;

        try {
            const res = await fetch(`/admin/api/midnight-sections/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!res.ok) throw new Error('Failed to delete section');
            await loadSections();
        } catch (err) {
            alert(err.message);
        }
    }
</script>
@endsection
