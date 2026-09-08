@extends('layouts.layout')
@section('title', 'Settings — ENGORA Admin')

@section('content')
<div class="px-6 py-8 max-w-4xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Global Settings</h1>
            <p class="text-slate-400 text-sm mt-1">Control app mode, ads, and mobile features remotely from production database.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="setSafeReviewMode()" id="btn-safe-mode" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-500 text-white font-bold px-4 py-2.5 rounded-2xl transition shadow-lg shadow-sky-500/20 text-sm">
                Safe Review Mode
            </button>
            <button onclick="setLiveMode()" id="btn-live-mode" class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-500 text-white font-bold px-4 py-2.5 rounded-2xl transition shadow-lg shadow-violet-500/20 text-sm">
                Live Mode
            </button>
            <button onclick="enableAllAds()" id="btn-enable-all" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2.5 rounded-2xl transition shadow-lg shadow-emerald-500/20 text-sm">
                Enable All Ads
            </button>
            <button onclick="disableAllAds()" id="btn-disable-all" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-500 text-white font-bold px-4 py-2.5 rounded-2xl transition shadow-lg shadow-rose-500/20 text-sm">
                Disable All Ads
            </button>
        </div>
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

    <!-- App Mode -->
    <div class="glass rounded-3xl overflow-hidden divide-y divide-white/5">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    App Mode
                </h3>
                <span id="app-mode-badge" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-300">Loading...</span>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <label class="relative cursor-pointer group">
                    <input type="radio" name="app_mode" value="safe_review" id="app_mode_safe_review" class="peer sr-only">
                    <div class="p-5 rounded-2xl border border-white/5 bg-white/2 peer-checked:border-sky-500/40 peer-checked:bg-sky-500/10 transition h-full">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-bold text-white">Safe Review Mode</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-500/20 text-sky-300">Store Safe</span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">Shows movie/TV details only — overview, cast, trailers, reviews. No watch or download features.</p>
                    </div>
                </label>
                <label class="relative cursor-pointer group">
                    <input type="radio" name="app_mode" value="live" id="app_mode_live" class="peer sr-only">
                    <div class="p-5 rounded-2xl border border-white/5 bg-white/2 peer-checked:border-violet-500/40 peer-checked:bg-violet-500/10 transition h-full">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-bold text-white">Live Mode</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-violet-500/20 text-violet-300">Full Features</span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">All features enabled — streaming, downloads, episode playback, and server selection.</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Ads Configuration -->
    <div class="glass rounded-3xl overflow-hidden divide-y divide-white/5">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    Mobile App Ads
                </h3>
                <span id="ads-status-badge" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-300">Loading...</span>
            </div>

            <form id="settings-form" onsubmit="saveSettings(event)" class="space-y-5">
                <!-- Master Toggle -->
                <div class="p-5 bg-white/2 rounded-2xl border border-white/5 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-white">Ads Enabled (Master Switch)</p>
                        <p class="text-xs text-slate-400 mt-1">Turns all ad placements on or off in the mobile app.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="ads_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- Google AdMob Section -->
                <div class="p-5 bg-white/2 rounded-2xl border border-white/5 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-white">Google AdMob (Test Ads)</p>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Official Sample Units</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Enables official Google test ads for safe testing on production & release builds.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="admob_enabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-white/5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-300">Banner Ad Unit ID</label>
                            <input type="text" id="admob_banner_id" placeholder="ca-app-pub-3940256099942544/6300978111" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3 py-2 font-mono">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-300">Interstitial Ad Unit ID</label>
                            <input type="text" id="admob_interstitial_id" placeholder="ca-app-pub-3940256099942544/1033173712" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3 py-2 font-mono">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-300">Rewarded Video Unit ID</label>
                            <input type="text" id="admob_rewarded_id" placeholder="ca-app-pub-3940256099942544/5224354917" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3 py-2 font-mono">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-300">App Open Unit ID</label>
                            <input type="text" id="admob_app_open_id" placeholder="ca-app-pub-3940256099942544/9257395921" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-xl px-3 py-2 font-mono">
                        </div>
                    </div>
                </div>

                <!-- WebView Ads Toggle -->
                <div class="p-5 bg-white/2 rounded-2xl border border-white/5 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-white">WebView Ads Enabled</p>
                        <p class="text-xs text-slate-400 mt-1">Enables banner, card, and interstitial WebView ads in the app.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="enable_webview_ads" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-500"></div>
                    </label>
                </div>

                <!-- WebView Ad URL -->
                <div class="p-5 bg-white/2 rounded-2xl border border-white/5 space-y-2">
                    <label for="webview_ad_url" class="text-sm font-bold text-white block">WebView Ad URL</label>
                    <p class="text-xs text-slate-400">The URL loaded in all WebView ad placements (banner, cards, interstitials).</p>
                    <input
                        type="url"
                        id="webview_ad_url"
                        placeholder="https://nazaarabox.com"
                        class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition font-mono"
                    />
                </div>

                <!-- WebView Ads Placements Overrides -->
                <div class="p-5 bg-white/2 rounded-2xl border border-white/5 space-y-4">
                    <button type="button" class="w-full flex items-center justify-between text-left focus:outline-none" onclick="togglePlacementSection()">
                        <div>
                            <p class="text-sm font-bold text-white">WebView Ads Placements Overrides</p>
                            <p class="text-xs text-slate-400 mt-1">Configure individual settings and custom URLs for each screen's ad webview.</p>
                        </div>
                        <span id="placement-chevron" class="text-slate-400 text-xs transition-transform duration-200">▼</span>
                    </button>
                    <div id="placement-section-content" class="hidden pt-4 border-t border-white/5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $placements = [
                                'home_banner' => 'Home Page Banner',
                                'home_inline' => 'Home Page Inline Cards',
                                'search_banner' => 'Search Page Banner',
                                'search_inline' => 'Search Page Inline Cards',
                                'detail_banner' => 'Detail Page Banner',
                                'detail_inline' => 'Detail Page Inline Cards',
                                'player_banner' => 'Player Screen Banner',
                                'browse_banner' => 'Browse Page Banner',
                                'browse_inline' => 'Browse Page Inline Cards',
                                'season_banner' => 'Season Page Banner',
                                'actor_banner' => 'Actor Page Banner',
                                'actor_inline' => 'Actor Page Inline Cards',
                                'category_banner' => 'Category Page Banner',
                                'seeall_banner' => 'SeeAll Page Banner',
                                'language_banner' => 'Language Page Banner',
                            ];
                        @endphp
                        
                        @foreach ($placements as $key => $label)
                            <div class="p-4 bg-white/2 rounded-xl border border-white/5 space-y-3">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="text-xs font-bold text-white">{{ $label }}</p>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="enable_ad_{{ $key }}" class="sr-only peer">
                                        <div class="w-8 h-4.5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-violet-500"></div>
                                    </label>
                                </div>
                                <input
                                    type="url"
                                    id="ad_url_{{ $key }}"
                                    placeholder="Use Global URL"
                                    class="w-full bg-[#1E1E2E] border border-white/5 text-white text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-violet-500/40 transition font-mono"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" id="btn-save" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-6 py-2.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Security & Credentials -->
    <div class="glass rounded-3xl overflow-hidden divide-y divide-white/5">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-extrabold text-white flex items-center gap-2">
                        <span>🔐</span> Admin Account &amp; Password Security
                    </h3>
                    <p class="text-slate-400 text-xs mt-1">Update administrator username and encrypted password with bcrypt verification.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#E50914]/20 text-[#FF2E3D] border border-[#E50914]/30">
                    Bcrypt Encrypted
                </span>
            </div>

            <form id="credentials-form" onsubmit="saveCredentials(event)" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Admin Display Name</label>
                        <input type="text" id="admin_name" value="{{ Auth::user()->name ?? 'Engora Admin' }}" required
                               class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E50914]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Username</label>
                        <input type="text" id="admin_username" value="{{ Auth::user()->username ?? 'admin' }}" required
                               class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E50914]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Admin Email</label>
                    <input type="email" id="admin_email" value="{{ Auth::user()->email ?? 'admin@engora.com' }}" required
                           class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E50914]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-white/5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">New Password (optional)</label>
                        <input type="password" id="new_password" placeholder="Leave blank to keep current" minlength="8"
                               class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E50914]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" placeholder="Repeat new password" minlength="8"
                               class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#E50914]">
                    </div>
                </div>

                <div class="pt-2 border-t border-white/5">
                    <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-2">Current Password (Required to confirm changes)</label>
                    <input type="password" id="current_password" required placeholder="Enter current password"
                           class="w-full bg-[#181828]/80 border border-amber-500/30 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-400">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" id="btn-save-credentials"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E50914] to-[#B81D24] text-white font-bold px-6 py-2.5 rounded-2xl hover:from-[#FF2E3D] hover:to-[#B81D24] transition shadow-lg shadow-[#E50914]/20 text-sm">
                        Update Credentials
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- API Preview -->
    <div class="glass rounded-3xl overflow-hidden">
        <div class="p-6 space-y-3">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Mobile API Response Preview</h3>
            <p class="text-xs text-slate-500">What the app receives from <code class="text-violet-400">GET /api/config/settings</code></p>
            <pre id="api-preview" class="bg-[#0D0D18] border border-white/5 rounded-2xl p-4 text-xs text-emerald-400 font-mono overflow-x-auto">Loading...</pre>
        </div>
    </div>

</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-8 right-6 z-[999] hidden">
    <div id="toast-inner" class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold text-white flex items-center gap-2 animate-slideUp"></div>
</div>

<script>
const API_BASE = '/admin/api/settings';

document.addEventListener('DOMContentLoaded', () => {
    loadSettings();
});

async function loadSettings() {
    try {
        const res = await fetch(API_BASE);
        if (!res.ok) throw new Error('Failed to load settings');
        const data = await res.json();
        applySettingsToForm(data);
        updateStatusBadge(data);
        updateApiPreview(data);
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function togglePlacementSection() {
    const content = document.getElementById('placement-section-content');
    const chevron = document.getElementById('placement-chevron');
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}

function applySettingsToForm(data) {
    document.getElementById('ads_enabled').checked = !!data.ads_enabled;
    document.getElementById('admob_enabled').checked = data.admob_enabled !== false;
    document.getElementById('admob_banner_id').value = data.admob_banner_id || 'ca-app-pub-3940256099942544/6300978111';
    document.getElementById('admob_interstitial_id').value = data.admob_interstitial_id || 'ca-app-pub-3940256099942544/1033173712';
    document.getElementById('admob_rewarded_id').value = data.admob_rewarded_id || 'ca-app-pub-3940256099942544/5224354917';
    document.getElementById('admob_app_open_id').value = data.admob_app_open_id || 'ca-app-pub-3940256099942544/9257395921';
    document.getElementById('enable_webview_ads').checked = !!data.enable_webview_ads;
    document.getElementById('webview_ad_url').value = data.webview_ad_url || '';
    const mode = data.app_mode === 'safe_review' ? 'safe_review' : 'live';
    document.getElementById('app_mode_safe_review').checked = mode === 'safe_review';
    document.getElementById('app_mode_live').checked = mode === 'live';

    const placements = [
        'home_banner', 'home_inline', 'search_banner', 'search_inline',
        'detail_banner', 'detail_inline', 'player_banner', 'browse_banner',
        'browse_inline', 'season_banner', 'actor_banner', 'actor_inline',
        'category_banner', 'seeall_banner', 'language_banner'
    ];
    placements.forEach(p => {
        const toggle = document.getElementById(`enable_ad_${p}`);
        const urlInput = document.getElementById(`ad_url_${p}`);
        if (toggle) toggle.checked = data[`enable_ad_${p}`] !== false; // default true
        if (urlInput) urlInput.value = data[`ad_url_${p}`] || '';
    });
}

function updateStatusBadge(data) {
    updateAppModeBadge(data);

    const badge = document.getElementById('ads-status-badge');
    if (data.ads_enabled && data.admob_enabled) {
        badge.textContent = 'AdMob Test Ads ON';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30';
    } else if (data.ads_enabled && data.enable_webview_ads) {
        badge.textContent = 'All Ads ON';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
    } else if (data.ads_enabled) {
        badge.textContent = 'Ads ON';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30';
    } else {
        badge.textContent = 'All Ads OFF';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-300';
    }
}

function updateAppModeBadge(data) {
    const badge = document.getElementById('app-mode-badge');
    if (data.app_mode === 'safe_review') {
        badge.textContent = 'Safe Review';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30';
    } else {
        badge.textContent = 'Live Mode';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-violet-500/20 text-violet-400 border border-violet-500/30';
    }
}

function updateApiPreview(data) {
    document.getElementById('api-preview').textContent = JSON.stringify(data, null, 2);
}

async function saveSettings(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    try {
        const payload = {
            ads_enabled: document.getElementById('ads_enabled').checked,
            admob_enabled: document.getElementById('admob_enabled').checked,
            admob_banner_id: document.getElementById('admob_banner_id').value.trim(),
            admob_interstitial_id: document.getElementById('admob_interstitial_id').value.trim(),
            admob_rewarded_id: document.getElementById('admob_rewarded_id').value.trim(),
            admob_app_open_id: document.getElementById('admob_app_open_id').value.trim(),
            enable_webview_ads: document.getElementById('enable_webview_ads').checked,
            webview_ad_url: document.getElementById('webview_ad_url').value.trim(),
            app_mode: document.getElementById('app_mode_safe_review').checked ? 'safe_review' : 'live',
        };

        const placements = [
            'home_banner', 'home_inline', 'search_banner', 'search_inline',
            'detail_banner', 'detail_inline', 'player_banner', 'browse_banner',
            'browse_inline', 'season_banner', 'actor_banner', 'actor_inline',
            'category_banner', 'seeall_banner', 'language_banner'
        ];
        placements.forEach(p => {
            const toggle = document.getElementById(`enable_ad_${p}`);
            const urlInput = document.getElementById(`ad_url_${p}`);
            if (toggle) payload[`enable_ad_${p}`] = toggle.checked;
            if (urlInput) payload[`ad_url_${p}`] = urlInput.value.trim();
        });

        const res = await fetch(API_BASE, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(payload),
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Failed to save settings');
        }

        const data = await res.json();
        applySettingsToForm(data);
        updateStatusBadge(data);
        updateApiPreview(data);
        showToast('Settings saved successfully');
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Settings';
    }
}

async function setSafeReviewMode() {
    await bulkToggle('/set-safe-review', 'Safe Review Mode enabled');
}

async function setLiveMode() {
    await bulkToggle('/set-live-mode', 'Live Mode enabled');
}

async function enableAllAds() {
    await bulkToggle('/enable-all-ads', 'All ads enabled');
}

async function disableAllAds() {
    await bulkToggle('/disable-all-ads', 'All ads disabled');
}

async function bulkToggle(endpoint, successMsg) {
    try {
        const res = await fetch(API_BASE + endpoint, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        });

        if (!res.ok) throw new Error('Request failed');

        const data = await res.json();
        const settings = data.settings || data;
        applySettingsToForm(settings);
        updateStatusBadge(settings);
        updateApiPreview(settings);
        showToast(data.message || successMsg);
    } catch (err) {
        showToast(err.message || 'Action failed', 'error');
    }
}

async function saveCredentials(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-credentials');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>Encrypting & Saving...</span>
    `;

    const payload = {
        name: document.getElementById('admin_name').value.trim(),
        username: document.getElementById('admin_username').value.trim(),
        email: document.getElementById('admin_email').value.trim(),
        current_password: document.getElementById('current_password').value,
    };

    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('new_password_confirmation').value;
    if (newPass) {
        if (newPass !== confirmPass) {
            showToast('New passwords do not match', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
            return;
        }
        if (newPass.length < 8) {
            showToast('New password must be at least 8 characters', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
            return;
        }
        payload.new_password = newPass;
        payload.new_password_confirmation = confirmPass;
    }

    try {
        const res = await fetch('/admin/api/settings/update-credentials', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();
        if (!res.ok) {
            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to update credentials');
            throw new Error(errorMsg);
        }

        showToast(data.message || 'Credentials updated and encrypted successfully!');
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('new_password_confirmation').value = '';
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
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
