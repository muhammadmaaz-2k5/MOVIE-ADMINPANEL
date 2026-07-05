@extends('layouts.layout')

@section('title', 'Video Servers Manager — Admin Panel')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto select-none">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-[#1E1E2E]/40 p-6 rounded-3xl border border-white/5">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                <span>⚙️</span> Video Servers Manager
            </h1>
            <p class="text-xs text-slate-400 font-semibold mt-1">Configure default global video streaming embeds and custom servers for TMDB content.</p>
        </div>
        <button onclick="openServerModal()" 
                class="px-5 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-violet-600/10 flex items-center gap-2 transition duration-200">
            <span>+</span> Add Custom Server
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
        <a href="{{ route('admin.video-servers') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.video-servers') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            ⚙️ Video Servers
        </a>
        <a href="{{ route('admin.notification-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.notification-manager') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🔔 Notifications
        </a>
        <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.settings') ? 'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🛠️ Settings
        </a>
    </div>

    <!-- Servers List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="servers-grid">
        <!-- Dynamically loaded servers -->
        <div class="col-span-full py-12 flex flex-col items-center justify-center bg-[#1E1E2E]/20 rounded-3xl border border-dashed border-white/10 gap-3">
            <div class="w-8 h-8 border-2 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs text-slate-500 font-bold">Loading video servers list...</span>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div id="server-modal" class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center p-4 z-50 hidden transition-opacity duration-300">
    <div class="w-full max-w-lg bg-[#1E1E2E] border border-white/5 rounded-3xl overflow-hidden shadow-2xl animate-slideUp">
        <!-- Modal Header -->
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 id="modal-title" class="text-md font-bold text-white flex items-center gap-2">
                <span>🔗</span> Create Video Server
            </h3>
            <button onclick="closeServerModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Form Content -->
        <form id="server-form" onsubmit="saveServer(event)" class="p-6 space-y-4">
            <input type="hidden" id="server-id"/>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">System Name</label>
                    <input id="server-name" type="text" required placeholder="e.g. vidsrc" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Display Label</label>
                    <input id="server-label" type="text" required placeholder="e.g. VidSrc Server" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Icon / Emoji</label>
                <input id="server-icon" type="text" placeholder="e.g. ⚡ or ▶" 
                       class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Movie Stream URL Template</label>
                <input id="server-movie-tpl" type="text" required placeholder="https://vidsrc.to/embed/movie/{id}" 
                       class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                <p class="text-[10px] text-slate-500 font-medium">Use <code class="text-violet-400 font-bold">{id}</code> as the placeholder for the TMDB Movie ID.</p>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">TV Stream URL Template</label>
                <input id="server-tv-tpl" type="text" required placeholder="https://vidsrc.to/embed/tv/{id}/{season}/{episode}" 
                       class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                <p class="text-[10px] text-slate-500 font-medium">Use <code class="text-violet-400 font-bold">{id}</code>, <code class="text-violet-400 font-bold">{season}</code>, and <code class="text-violet-400 font-bold">{episode}</code> placeholders.</p>
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
                <button type="button" onclick="closeServerModal()" 
                        class="px-5 py-3 rounded-xl border border-white/5 hover:bg-white/5 text-slate-400 hover:text-white text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" id="save-btn" 
                        class="px-5 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold shadow-lg hover:shadow-violet-600/10 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notice -->
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden select-none pointer-events-none">
    <div id="toast-inner" class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold text-white flex items-center gap-2 animate-slideUp"></div>
</div>

<script>
let serversList = [];

document.addEventListener("DOMContentLoaded", () => {
    loadServers();
});

// ── CRUD - Load Servers ───────────────────────────────────────────────────────
async function loadServers() {
    try {
        const res = await fetch('/admin/api/video-servers');
        serversList = await res.json();
        renderServers();
    } catch (e) {
        showToast('Failed to load video servers.', 'error');
    }
}

function renderServers() {
    const grid = document.getElementById('servers-grid');
    if (serversList.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full py-12 flex flex-col items-center justify-center bg-[#1E1E2E]/20 rounded-3xl border border-dashed border-white/10 gap-2">
                <span class="text-slate-500 text-sm font-bold">No custom servers configured yet</span>
                <span class="text-slate-600 text-xs">Click "Add Custom Server" to configure one.</span>
            </div>`;
        return;
    }

    grid.innerHTML = serversList.map(s => {
        return `
            <div class="bg-[#1E1E2E] border border-white/5 rounded-3xl p-6 flex flex-col justify-between space-y-4 hover:border-violet-500/20 transition duration-300">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl px-2.5 py-1.5 bg-[#121220] rounded-xl border border-white/5">${s.icon || '🔗'}</span>
                            <div>
                                <h4 class="text-sm font-bold text-white leading-tight">${s.label}</h4>
                                <span class="text-[9px] font-semibold text-slate-500 uppercase tracking-wide">Slug: ${s.name}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2">
                        <div class="space-y-1">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-500 block">Movie Endpoint</span>
                            <span class="text-[11px] text-slate-400 bg-[#121220]/60 p-2 rounded-lg block truncate font-mono">${s.movie_url_template}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-500 block">TV Show Endpoint</span>
                            <span class="text-[11px] text-slate-400 bg-[#121220]/60 p-2 rounded-lg block truncate font-mono">${s.tv_url_template}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2.5 border-t border-white/5 pt-4 mt-2">
                    <button onclick='editServer(${JSON.stringify(s)})' 
                            class="flex-1 py-2.5 text-center bg-[#121220] border border-white/5 hover:border-violet-500/20 rounded-xl text-xs font-bold text-slate-300 hover:text-white transition">
                        Edit Server
                    </button>
                    <button onclick="deleteServer(${s.id})" 
                            class="py-2.5 px-3 bg-rose-600/10 border border-rose-500/20 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold transition">
                        ✕
                    </button>
                </div>
            </div>`;
    }).join('');
}

// ── CRUD - Save Server ────────────────────────────────────────────────────────
async function saveServer(e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-4 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;

    const id = document.getElementById('server-id').value;
    const isEdit = !!id;

    const payload = {
        name: document.getElementById('server-name').value,
        label: document.getElementById('server-label').value,
        icon: document.getElementById('server-icon').value,
        movie_url_template: document.getElementById('server-movie-tpl').value,
        tv_url_template: document.getElementById('server-tv-tpl').value,
        _token: '{{ csrf_token() }}'
    };

    const url = isEdit 
        ? `/admin/api/video-servers/${id}`
        : '/admin/api/video-servers';

    try {
        const res = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            closeServerModal();
            loadServers();
        } else {
            showToast(data.message || 'Error occurred while saving.', 'error');
        }
    } catch (err) {
        showToast('Network error or server error.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// ── CRUD - Delete Server ──────────────────────────────────────────────────────
async function deleteServer(id) {
    if (!confirm('Are you sure you want to delete this video server?')) return;

    try {
        const res = await fetch(`/admin/api/video-servers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            loadServers();
        } else {
            showToast(data.message || 'Error deleting server.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    }
}

// ── Modals Logic ──────────────────────────────────────────────────────────────
function openServerModal() {
    document.getElementById('modal-title').innerHTML = '<span>🔗</span> Create Video Server';
    document.getElementById('server-form').reset();
    document.getElementById('server-id').value = '';
    document.getElementById('server-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function editServer(s) {
    document.getElementById('modal-title').innerHTML = '<span>🔗</span> Edit Video Server';
    document.getElementById('server-id').value = s.id;
    document.getElementById('server-name').value = s.name;
    document.getElementById('server-label').value = s.label;
    document.getElementById('server-icon').value = s.icon || '';
    document.getElementById('server-movie-tpl').value = s.movie_url_template;
    document.getElementById('server-tv-tpl').value = s.tv_url_template;
    document.getElementById('server-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeServerModal() {
    document.getElementById('server-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Toast Helper ──────────────────────────────────────────────────────────────
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
