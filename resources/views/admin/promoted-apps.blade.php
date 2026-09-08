@extends('layouts.layout')

@section('title', 'Promoted Apps Manager — Admin Panel')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto select-none">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-[#1E1E2E]/40 p-6 rounded-3xl border border-white/5 shadow-xl">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-3">
                <span class="p-2 rounded-xl bg-violet-600/20 text-violet-400 border border-violet-500/30">📱</span>
                <span>Promoted Apps & Games</span>
            </h1>
            <p class="text-xs text-slate-400 font-semibold mt-1">Manage companion partner apps, utilities, anime streamers, and direct Play Store installations promoted to mobile users.</p>
        </div>
        <button onclick="openAppModal()" 
                class="px-5 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-violet-600/20 flex items-center gap-2 transition duration-200">
            <span>+</span> Add Promoted App
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
        <a href="{{ route('admin.midnight-manager') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.midnight-manager') ? 'bg-gradient-to-r from-[#FF1A75] to-[#9D4EDD] text-white shadow-lg shadow-[#FF1A75]/20 border border-[#FF1A75]/30' : 'bg-[#1E1E2E] border border-white/5 text-slate-300 hover:bg-white/5 hover:text-white' }}">
            🍸 Midnight 18+ Manager
        </a>
        <a href="{{ route('admin.promoted-apps') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white shadow-lg shadow-violet-500/10 border border-violet-500/30">
            📱 Promoted Apps
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

    <!-- Apps Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="apps-grid">
        @forelse($apps as $app)
        <div id="app-card-{{ $app->id }}" class="bg-[#1E1E2E]/60 border {{ $app->is_featured ? 'border-amber-500/40 shadow-amber-500/5' : 'border-white/5' }} rounded-3xl p-5 flex flex-col justify-between space-y-4 hover:border-violet-500/30 transition duration-200 shadow-xl relative overflow-hidden group">
            
            @if($app->is_featured)
            <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 to-orange-500 text-black font-black text-[10px] px-3 py-1 rounded-bl-xl tracking-wider uppercase shadow-md flex items-center gap-1">
                <span>★</span> Featured Spotlight
            </div>
            @endif

            <div class="space-y-3">
                <div class="flex items-start gap-3.5">
                    <!-- App Icon -->
                    <div class="w-14 h-14 rounded-2xl bg-[#121220] border border-white/10 flex-shrink-0 overflow-hidden shadow-inner flex items-center justify-center">
                        @if($app->icon_url)
                            <img src="{{ $app->icon_url }}" alt="{{ $app->name }}" class="w-full h-full object-cover"/>
                        @else
                            <span class="text-2xl">📱</span>
                        @endif
                    </div>
                    
                    <!-- App Title & Category -->
                    <div class="flex-1 min-w-0 pr-8">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-bold text-white truncate">{{ $app->name }}</h3>
                            @if($app->badge)
                                <span class="px-2 py-0.5 text-[9px] font-black rounded-md uppercase tracking-wider bg-violet-600/30 text-violet-300 border border-violet-500/30">{{ $app->badge }}</span>
                            @endif
                        </div>
                        <span class="inline-block mt-1 text-[11px] font-semibold text-slate-400 bg-white/5 px-2 py-0.5 rounded-lg border border-white/5">
                            {{ $app->category }}
                        </span>
                    </div>
                </div>

                <!-- Tagline -->
                <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed">
                    {{ $app->tagline ?: ($app->description ?: 'No tagline provided.') }}
                </p>

                <!-- Stats & Metadata -->
                <div class="flex items-center justify-between text-[11px] text-slate-400 bg-[#121220]/60 p-2.5 rounded-xl border border-white/5">
                    <span class="flex items-center gap-1 text-amber-400 font-bold">
                        ★ {{ number_format($app->rating, 1) }}
                    </span>
                    <span class="text-slate-400 font-semibold">
                        📥 {{ $app->downloads }}
                    </span>
                    <span class="font-mono text-slate-500 text-[10px] truncate max-w-[120px]" title="{{ $app->package_name }}">
                        {{ $app->package_name }}
                    </span>
                </div>
            </div>

            <!-- Actions Row -->
            <div class="pt-3 border-t border-white/5 flex items-center justify-between gap-2">
                <!-- Toggles -->
                <div class="flex items-center gap-2">
                    <!-- Active status toggle -->
                    <button onclick="toggleAppStatus({{ $app->id }})" 
                            id="status-btn-{{ $app->id }}"
                            class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition border {{ $app->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30' }}">
                        {{ $app->is_active ? 'Active' : 'Inactive' }}
                    </button>

                    <!-- Featured toggle -->
                    <button onclick="toggleAppFeatured({{ $app->id }})" 
                            id="featured-btn-{{ $app->id }}"
                            title="Toggle Spotlight Feature"
                            class="p-1.5 rounded-lg transition border {{ $app->is_featured ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'bg-white/5 text-slate-500 border-white/5 hover:text-amber-400' }}">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-1.5">
                    @if($app->play_store_url)
                    <a href="{{ $app->play_store_url }}" target="_blank" rel="noopener" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/5 transition" title="Open Store Page">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                    @endif
                    <button onclick="editApp({{ json_encode($app) }})" class="p-2 text-slate-400 hover:text-violet-400 rounded-lg hover:bg-violet-500/10 transition" title="Edit App">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button onclick="deleteApp({{ $app->id }})" class="p-2 text-slate-400 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition" title="Delete App">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 flex flex-col items-center justify-center bg-[#1E1E2E]/20 rounded-3xl border border-dashed border-white/10 gap-3">
            <span class="text-4xl">📱</span>
            <span class="text-sm text-slate-400 font-bold">No promoted apps configured yet.</span>
            <p class="text-xs text-slate-600">Click "+ Add Promoted App" to spotlight partner apps and companion media tools.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Add / Edit Modal -->
<div id="app-modal" class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center p-4 z-50 hidden transition-opacity duration-300">
    <div class="w-full max-w-xl bg-[#1E1E2E] border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 id="modal-title" class="text-md font-bold text-white flex items-center gap-2">
                <span>📱</span> Create Promoted App
            </h3>
            <button onclick="closeAppModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Form Content -->
        <form id="app-form" onsubmit="saveApp(event)" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            <input type="hidden" id="app-id"/>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">App Name *</label>
                    <input id="app-name" type="text" required placeholder="e.g. CinePlay Ultra 4K" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Category *</label>
                    <select id="app-category" required class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="Entertainment">Entertainment</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Anime">Anime</option>
                        <option value="Streaming">Streaming</option>
                        <option value="Media Tools">Media Tools</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tagline</label>
                <input id="app-tagline" type="text" placeholder="e.g. Hardware accelerated 4K HDR & subtitle player" 
                       class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Description</label>
                <textarea id="app-description" rows="2" placeholder="Detailed app overview..." 
                          class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Package Name</label>
                    <input id="app-package-name" type="text" placeholder="e.g. com.cineplay.ultraplayer" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition font-mono text-xs"/>
                </div>

                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Play Store URL (Optional)</label>
                    <input id="app-play-store-url" type="url" placeholder="https://play.google.com/store/apps/details?id=..." 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Icon URL</label>
                    <input id="app-icon-url" type="url" placeholder="https://.../icon.png" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Banner URL</label>
                    <input id="app-banner-url" type="url" placeholder="https://.../banner.jpg" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-600 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rating (1-5)</label>
                    <input id="app-rating" type="number" step="0.1" min="1" max="5" value="4.8" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Downloads</label>
                    <input id="app-downloads" type="text" value="100K+" placeholder="e.g. 500K+" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Badge Tag</label>
                    <input id="app-badge" type="text" placeholder="FEATURED, HOT" 
                           class="w-full bg-[#121220] border border-white/5 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-300">
                    <input type="checkbox" id="app-is-featured" class="w-4 h-4 rounded text-violet-600 focus:ring-0 bg-[#121220] border-white/10"/>
                    <span>Featured Spotlight Hero</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-300">
                    <input type="checkbox" id="app-is-active" checked class="w-4 h-4 rounded text-violet-600 focus:ring-0 bg-[#121220] border-white/10"/>
                    <span>Active in Mobile Feed</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end gap-3 border-t border-white/5">
                <button type="button" onclick="closeAppModal()" class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-xs font-bold transition">
                    Cancel
                </button>
                <button type="submit" id="save-app-btn" class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition">
                    Save App
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';

    function openAppModal() {
        document.getElementById('modal-title').innerHTML = '<span>📱</span> Create Promoted App';
        document.getElementById('app-id').value = '';
        document.getElementById('app-form').reset();
        document.getElementById('app-rating').value = '4.8';
        document.getElementById('app-downloads').value = '100K+';
        document.getElementById('app-is-active').checked = true;
        document.getElementById('app-modal').classList.remove('hidden');
    }

    function closeAppModal() {
        document.getElementById('app-modal').classList.add('hidden');
    }

    function editApp(app) {
        document.getElementById('modal-title').innerHTML = '<span>✏️</span> Edit Promoted App';
        document.getElementById('app-id').value = app.id;
        document.getElementById('app-name').value = app.name || '';
        document.getElementById('app-category').value = app.category || 'Entertainment';
        document.getElementById('app-tagline').value = app.tagline || '';
        document.getElementById('app-description').value = app.description || '';
        document.getElementById('app-package-name').value = app.package_name || '';
        document.getElementById('app-play-store-url').value = app.play_store_url || '';
        document.getElementById('app-icon-url').value = app.icon_url || '';
        document.getElementById('app-banner-url').value = app.banner_url || '';
        document.getElementById('app-rating').value = app.rating || 4.8;
        document.getElementById('app-downloads').value = app.downloads || '100K+';
        document.getElementById('app-badge').value = app.badge || '';
        document.getElementById('app-is-featured').checked = !!app.is_featured;
        document.getElementById('app-is-active').checked = !!app.is_active;
        document.getElementById('app-modal').classList.remove('hidden');
    }

    async function saveApp(e) {
        e.preventDefault();
        const id = document.getElementById('app-id').value;
        const url = id ? `/admin/api/promoted-apps/${id}` : '/admin/api/promoted-apps';
        const method = id ? 'PUT' : 'POST';

        const payload = {
            name: document.getElementById('app-name').value,
            category: document.getElementById('app-category').value,
            tagline: document.getElementById('app-tagline').value,
            description: document.getElementById('app-description').value,
            package_name: document.getElementById('app-package-name').value,
            play_store_url: document.getElementById('app-play-store-url').value,
            icon_url: document.getElementById('app-icon-url').value,
            banner_url: document.getElementById('app-banner-url').value,
            rating: parseFloat(document.getElementById('app-rating').value) || 4.8,
            downloads: document.getElementById('app-downloads').value,
            badge: document.getElementById('app-badge').value,
            is_featured: document.getElementById('app-is-featured').checked,
            is_active: document.getElementById('app-is-active').checked,
        };

        const btn = document.getElementById('save-app-btn');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok) {
                closeAppModal();
                window.location.reload();
            } else {
                alert(data.message || 'Validation failed');
            }
        } catch (err) {
            console.error(err);
            alert('An unexpected error occurred while saving app.');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save App';
        }
    }

    async function toggleAppStatus(id) {
        try {
            const res = await fetch(`/admin/api/promoted-apps/${id}/toggle-status`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (res.ok) {
                const btn = document.getElementById(`status-btn-${id}`);
                if (data.is_active) {
                    btn.className = 'px-2.5 py-1 text-[11px] font-bold rounded-lg transition border bg-emerald-500/20 text-emerald-400 border-emerald-500/30';
                    btn.innerText = 'Active';
                } else {
                    btn.className = 'px-2.5 py-1 text-[11px] font-bold rounded-lg transition border bg-red-500/20 text-red-400 border-red-500/30';
                    btn.innerText = 'Inactive';
                }
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function toggleAppFeatured(id) {
        try {
            const res = await fetch(`/admin/api/promoted-apps/${id}/toggle-featured`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (res.ok) {
                window.location.reload();
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function deleteApp(id) {
        if (!confirm('Are you sure you want to remove this promoted app?')) return;
        try {
            const res = await fetch(`/admin/api/promoted-apps/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const card = document.getElementById(`app-card-${id}`);
                if (card) card.remove();
            } else {
                alert('Failed to delete app.');
            }
        } catch (err) {
            console.error(err);
        }
    }
</script>
@endsection
