@extends('layouts.layout')
@section('title', 'Home Sections — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Home Sections</h1>
            <p class="text-slate-400 text-sm mt-1">Configure dynamic sections shown on the home page.</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-5 py-2.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Section
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
    </div>

    <!-- Sections List -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5 text-left">
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Sort</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Emoji</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Title</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Endpoint</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Params</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="sections-table-body" class="divide-y divide-white/5">
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading sections...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add / Edit Modal -->
<div id="section-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeModal(event)">
    <div id="section-modal-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/8 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <h3 id="modal-title" class="text-lg font-extrabold text-white">Add Section</h3>
            <button onclick="closeModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="section-form" onsubmit="submitForm(event)" class="px-6 py-5 space-y-4">
            <input type="hidden" id="form-id" value=""/>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sort Order</label>
                    <input id="form-sort-order" type="number" value="0" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Emoji</label>
                    <input id="form-emoji" type="text" maxlength="4" value="🎬" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Title *</label>
                    <input id="form-title" type="text" required placeholder="e.g. Trending Movies" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Endpoint *</label>
                    <input id="form-endpoint" type="text" required value="discover/movie" placeholder="e.g. discover/movie or discover/tv" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition font-mono"/>
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Params (JSON)</label>
                    <textarea id="form-params" rows="4" placeholder='{"sort_by": "popularity.desc"}' class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl p-4 focus:outline-none focus:border-violet-500/40 transition resize-none font-mono"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input id="form-is-active" type="checkbox" checked class="sr-only peer"/>
                    <div class="w-10 h-5 bg-[#1E1E2E] border border-white/10 rounded-full peer peer-checked:bg-violet-600 peer-checked:border-violet-500 transition-all after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
                <span class="text-sm font-semibold text-slate-300">Active (visible on home page)</span>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" id="submit-btn" class="flex-1 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold py-3 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">Save Section</button>
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
                <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Delete section?</h3>
                <p class="text-xs text-slate-400 mt-0.5">This will remove it from the home page.</p>
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
    loadSections();
});

async function loadSections() {
    const tbody = document.getElementById('sections-table-body');
    try {
        const data = await fetch('/admin/api/home-sections').then(r => r.json());
        renderTable(data);
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-12 text-center text-rose-450 text-xs">Failed to load sections. Check console.</td></tr>`;
    }
}

function renderTable(sections) {
    const tbody = document.getElementById('sections-table-body');
    if (sections.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-16 text-center text-slate-500 text-sm">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>No sections configured yet. Add one to get started.</span>
            </div>
        </td></tr>`;
        return;
    }

    tbody.innerHTML = sections.map(sec => {
        const activeEl = sec.is_active
            ? `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2 py-0.5"><span class="w-1.5 h-1.5 bg-emerald-400 rounded-full inline-block"></span>Active</span>`
            : `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-white/5 border border-white/5 rounded-lg px-2 py-0.5">Inactive</span>`;

        const paramsStr = sec.params ? JSON.stringify(sec.params) : '{}';
        const truncatedParams = paramsStr.length > 30 ? paramsStr.substring(0, 30) + '...' : paramsStr;

        return `<tr class="hover:bg-white/1 transition group">
            <td class="px-5 py-3.5 text-xs font-bold text-slate-300">${sec.sort_order}</td>
            <td class="px-5 py-3.5 text-base">${sec.emoji}</td>
            <td class="px-5 py-3.5 text-xs font-bold text-white">${sec.title}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-slate-400">${sec.endpoint}</td>
            <td class="px-5 py-3.5 text-[10px] font-mono text-slate-500 max-w-[200px] truncate" title="${paramsStr.replace(/"/g, '&quot;')}">${truncatedParams}</td>
            <td class="px-5 py-3.5">${activeEl}</td>
            <td class="px-5 py-3.5">
                <div class="flex gap-2">
                    <button onclick="openEditModal(${JSON.stringify(sec).replace(/"/g,'&quot;')})" class="p-1.5 rounded-lg bg-violet-500/10 border border-violet-500/20 text-violet-400 hover:bg-violet-500/20 transition" title="Edit Section">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="openDeleteModal(${sec.id})" class="p-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition" title="Delete Section">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function openAddModal() {
    document.getElementById('modal-title').innerText = 'Add Section';
    document.getElementById('section-form').reset();
    document.getElementById('form-id').value = '';
    document.getElementById('form-emoji').value = '🎬';
    document.getElementById('form-endpoint').value = 'discover/movie';
    document.getElementById('form-params').value = '{}';
    document.getElementById('form-is-active').checked = true;
    document.getElementById('section-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(section) {
    document.getElementById('modal-title').innerText = 'Edit Section';
    document.getElementById('form-id').value = section.id;
    document.getElementById('form-sort-order').value = section.sort_order || 0;
    document.getElementById('form-emoji').value = section.emoji || '🎬';
    document.getElementById('form-title').value = section.title;
    document.getElementById('form-endpoint').value = section.endpoint;
    document.getElementById('form-params').value = section.params ? JSON.stringify(section.params, null, 2) : '{}';
    document.getElementById('form-is-active').checked = !!section.is_active;
    document.getElementById('section-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(event) {
    if (event && event.target !== document.getElementById('section-modal')) return;
    document.getElementById('section-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

async function submitForm(event) {
    event.preventDefault();

    const id = document.getElementById('form-id').value;
    let paramsRaw = document.getElementById('form-params').value.trim();
    let paramsObj = {};
    try {
        if (paramsRaw) paramsObj = JSON.parse(paramsRaw);
    } catch (e) {
        showToast('Invalid JSON in Params field.', 'error');
        return;
    }

    const payload = {
        sort_order: parseInt(document.getElementById('form-sort-order').value) || 0,
        emoji: document.getElementById('form-emoji').value || '🎬',
        title: document.getElementById('form-title').value,
        endpoint: document.getElementById('form-endpoint').value,
        params: paramsObj,
        is_active: document.getElementById('form-is-active').checked,
    };

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    try {
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/admin/api/home-sections/${id}` : '/admin/api/home-sections';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error(await res.text());
        closeModal();
        loadSections();
        showToast(id ? 'Section updated successfully!' : 'Section created!', 'success');
    } catch(e) {
        showToast('Error saving: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Section';
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
        await fetch(`/admin/api/home-sections/${deleteTargetId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        document.getElementById('delete-modal').classList.add('hidden');
        loadSections();
        showToast('Section deleted.', 'success');
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
