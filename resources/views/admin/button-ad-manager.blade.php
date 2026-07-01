@extends('layouts.layout')
@section('title', 'Button Ads — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Button Ads</h1>
            <p class="text-slate-400 text-sm mt-1">Configure dynamic promotional buttons shown on various screens.</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-5 py-2.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Button Ad
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
        <a href="{{ route('admin.home-section-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.home-section-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🔥 Home Sections
        </a>
        <a href="{{ route('admin.webview-ad-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.webview-ad-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            📺 WebView Ads
        </a>
        <a href="{{ route('admin.button-ad-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.button-ad-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🔘 Button Ads
        </a>
    </div>

    <!-- Button Ads List -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5 text-left">
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Sort</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Button Text</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Target Screen</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Link</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="ads-table-body" class="divide-y divide-white/5">
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading button ads...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add / Edit Modal -->
<div id="ad-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeModal(event)">
    <div id="ad-modal-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/8 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <h3 id="modal-title" class="text-lg font-extrabold text-white">Add Button Ad</h3>
            <button onclick="closeModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="ad-form" onsubmit="submitForm(event)" class="px-6 py-5 space-y-4">
            <input type="hidden" id="form-id" value=""/>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Name *</label>
                    <input id="form-name" type="text" required placeholder="e.g. Premium Content Button" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sort Order</label>
                    <input id="form-sort-order" type="number" value="0" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Button Text *</label>
                    <input id="form-button-text" type="text" required placeholder="e.g. Watch Now" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Button Icon</label>
                    <input id="form-button-icon" type="text" maxlength="4" placeholder="🎬 or 🔥" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Button Color *</label>
                    <input id="form-button-color" type="color" value="#6C5CE7" class="w-full h-10 bg-[#1E1E2E] border border-white/5 rounded-xl p-1 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Target Screen *</label>
                    <select id="form-target-screen" required class="w-full bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="home">🏠 Home</option>
                        <option value="movies">🎬 Movies</option>
                        <option value="tv">📺 TV Shows</option>
                        <option value="anime">⛩️ Anime</option>
                        <option value="downloads">⬇️ Downloads</option>
                        <option value="search">🔍 Search</option>
                    </select>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Button Link URL *</label>
                    <input id="form-button-link" type="url" required placeholder="https://example.com/premium-content" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition font-mono"/>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input id="form-is-enabled" type="checkbox" checked class="sr-only peer"/>
                    <div class="w-10 h-5 bg-[#1E1E2E] border border-white/10 rounded-full peer peer-checked:bg-violet-600 peer-checked:border-violet-500 transition-all after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
                <span class="text-sm font-semibold text-slate-300">Enabled (visible in app)</span>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" id="submit-btn" class="flex-1 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold py-3 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">Save Button Ad</button>
                <button type="button" onclick="closeModal()" class="px-5 py-3 bg-[#1E1E2E] border border-white/5 text-slate-300 font-bold rounded-2xl hover:bg-white/5 hover:text-white transition text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden">
    <div class="w-full max-w-sm bg-[#121220] rounded-3xl border border-white/8 shadow-2xl p-6 mx-4 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 2 0 00-1-1h-4a1 2 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Delete button ad?</h3>
                <p class="text-xs text-slate-400 mt-0.5">This will remove it from the app screens.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="confirmDelete()" class="flex-1 bg-rose-600 hover:bg-rose-500 text-white font-bold py-2.5 rounded-2xl transition text-sm">Delete</button>
            <button onclick="document.getElementById('delete-modal').classList.add('hidden')" class="flex-1 bg-[#1E1E2E] border border-white/5 text-slate-300 font-bold py-2.5 rounded-2xl hover:bg-white/5 hover:text-white transition text-sm">Cancel</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-8 right-6 z-[999] hidden">
    <div id="toast-inner" class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold text-white flex items-center gap-2 animate-slideUp"></div>
</div>

<script>
let deleteTargetId = null;

document.addEventListener('DOMContentLoaded', () => {
    loadAds();
});

async function loadAds() {
    const tbody = document.getElementById('ads-table-body');
    try {
        const data = await fetch('/admin/api/button-ads').then(r => r.json());
        renderTable(data);
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-12 text-center text-rose-450 text-xs">Failed to load button ads. Check console.</td></tr>`;
    }
}

function renderTable(ads) {
    const tbody = document.getElementById('ads-table-body');
    if (ads.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-16 text-center text-slate-500 text-sm">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>No button ads configured yet. Add one to get started.</span>
            </div>
        </td></tr>`;
        return;
    }

    const screenLabels = {
        'home': '🏠 Home',
        'movies': '🎬 Movies',
        'tv': '📺 TV',
        'anime': '⛩️ Anime',
        'downloads': '⬇️ Downloads',
        'search': '🔍 Search'
    };

    tbody.innerHTML = ads.map(ad => {
        const activeEl = ad.is_enabled
            ? `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2 py-0.5"><span class="w-1.5 h-1.5 bg-emerald-400 rounded-full inline-block"></span>Enabled</span>`
            : `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-white/5 border border-white/5 rounded-lg px-2 py-0.5">Disabled</span>`;

        const truncatedLink = ad.button_link.length > 40 ? ad.button_link.substring(0, 40) + '...' : ad.button_link;

        return `<tr class="hover:bg-white/1 transition group">
            <td class="px-5 py-3.5 text-xs font-bold text-slate-300">${ad.sort_order}</td>
            <td class="px-5 py-3.5 text-xs font-bold text-white">${ad.name}</td>
            <td class="px-5 py-3.5 text-xs text-white">${ad.button_icon || ''} ${ad.button_text}</td>
            <td class="px-5 py-3.5 text-xs text-slate-400">${screenLabels[ad.target_screen] || ad.target_screen}</td>
            <td class="px-5 py-3.5 text-[10px] font-mono text-slate-500 max-w-[200px] truncate" title="${ad.button_link.replace(/"/g, '&quot;')}">${truncatedLink}</td>
            <td class="px-5 py-3.5">${activeEl}</td>
            <td class="px-5 py-3.5">
                <div class="flex gap-2">
                    <button onclick="openEditModal(${JSON.stringify(ad).replace(/"/g,'&quot;')})" class="p-1.5 rounded-lg bg-violet-500/10 border border-violet-500/20 text-violet-400 hover:bg-violet-500/20 transition" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="openDeleteModal(${ad.id})" class="p-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition" title="Delete">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 2 0 00-1-1h-4a1 2 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function openAddModal() {
    document.getElementById('modal-title').innerText = 'Add Button Ad';
    document.getElementById('ad-form').reset();
    document.getElementById('form-id').value = '';
    document.getElementById('form-target-screen').value = 'home';
    document.getElementById('form-button-color').value = '#6C5CE7';
    document.getElementById('form-sort-order').value = '0';
    document.getElementById('form-is-enabled').checked = true;
    document.getElementById('ad-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(ad) {
    document.getElementById('modal-title').innerText = 'Edit Button Ad';
    document.getElementById('form-id').value = ad.id;
    document.getElementById('form-name').value = ad.name;
    document.getElementById('form-button-text').value = ad.button_text;
    document.getElementById('form-button-icon').value = ad.button_icon || '';
    document.getElementById('form-button-link').value = ad.button_link;
    document.getElementById('form-button-color').value = ad.button_color || '#6C5CE7';
    document.getElementById('form-target-screen').value = ad.target_screen;
    document.getElementById('form-sort-order').value = ad.sort_order || 0;
    document.getElementById('form-is-enabled').checked = !!ad.is_enabled;
    document.getElementById('ad-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(event) {
    if (event && event.target !== document.getElementById('ad-modal')) return;
    document.getElementById('ad-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

async function submitForm(event) {
    event.preventDefault();

    const id = document.getElementById('form-id').value;
    const payload = {
        name: document.getElementById('form-name').value,
        button_text: document.getElementById('form-button-text').value,
        button_icon: document.getElementById('form-button-icon').value || null,
        button_link: document.getElementById('form-button-link').value,
        button_color: document.getElementById('form-button-color').value,
        target_screen: document.getElementById('form-target-screen').value,
        sort_order: parseInt(document.getElementById('form-sort-order').value) || 0,
        is_enabled: document.getElementById('form-is-enabled').checked,
    };

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    try {
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/admin/api/button-ads/${id}` : '/admin/api/button-ads';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error(await res.text());
        closeModal();
        loadAds();
        showToast(id ? 'Button ad updated!' : 'Button ad created!', 'success');
    } catch(e) {
        showToast('Error: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Button Ad';
    }
}

function openDeleteModal(id) {
    deleteTargetId = id;
    document.getElementById('delete-modal').classList.remove('hidden');
}

async function confirmDelete() {
    if (!deleteTargetId) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        await fetch(`/admin/api/button-ads/${deleteTargetId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        document.getElementById('delete-modal').classList.add('hidden');
        loadAds();
        showToast('Button ad deleted.', 'success');
    } catch(e) {
        showToast('Delete failed.', 'error');
    }
    deleteTargetId = null;
}

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