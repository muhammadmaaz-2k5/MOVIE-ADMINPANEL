@extends('layouts.layout')
@section('title', 'Settings — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-4xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Global Settings</h1>
            <p class="text-slate-400 text-sm mt-1">Control mobile app ads remotely from production database.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="enableAllAds()" id="btn-enable-all" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2.5 rounded-2xl transition shadow-lg shadow-emerald-500/20 text-sm">
                Enable All Ads
            </button>
            <button onclick="disableAllAds()" id="btn-disable-all" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-500 text-white font-bold px-4 py-2.5 rounded-2xl transition shadow-lg shadow-rose-500/20 text-sm">
                Disable All Ads
            </button>
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

                <div class="flex justify-end pt-2">
                    <button type="submit" id="btn-save" class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-6 py-2.5 rounded-2xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
                        Save Settings
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

function applySettingsToForm(data) {
    document.getElementById('ads_enabled').checked = !!data.ads_enabled;
    document.getElementById('enable_webview_ads').checked = !!data.enable_webview_ads;
    document.getElementById('webview_ad_url').value = data.webview_ad_url || '';
}

function updateStatusBadge(data) {
    const badge = document.getElementById('ads-status-badge');
    if (data.ads_enabled && data.enable_webview_ads) {
        badge.textContent = 'All Ads ON';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
    } else if (data.ads_enabled) {
        badge.textContent = 'Ads ON (WebView OFF)';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30';
    } else {
        badge.textContent = 'All Ads OFF';
        badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-300';
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
            enable_webview_ads: document.getElementById('enable_webview_ads').checked,
            webview_ad_url: document.getElementById('webview_ad_url').value.trim(),
        };

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
