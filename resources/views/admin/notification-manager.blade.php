@extends('layouts.layout')
@section('title', 'Notification Manager — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-6xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Notification Manager</h1>
            <p class="text-slate-400 text-sm mt-1">Send immediate push notifications or manage a library of templates for random recommendation alerts.</p>
        </div>
    </div>

    <!-- Tab Switcher -->
    <div class="flex border-b border-white/5 pb-px gap-4">
        <button onclick="switchTab('direct')" id="tab-btn-direct" class="px-4 py-2 text-sm font-semibold border-b-2 border-violet-500 text-violet-400 focus:outline-none transition-all duration-200">
            ✉️ Direct Broadcast
        </button>
        <button onclick="switchTab('library')" id="tab-btn-library" class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-200 focus:outline-none transition-all duration-200">
            📚 Template Library
        </button>
    </div>

    <!-- ══════════════════════════════════════════════════════
         Direct Broadcast Tab
         ══════════════════════════════════════════════════════ -->
    <div id="tab-direct" class="space-y-6">
        <div class="glass p-6 rounded-3xl space-y-6">
            <h2 class="text-base font-bold text-white">Direct Push Broadcast</h2>
            <form id="notification-form" onsubmit="sendDirectNotification(event)">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Notification Title *</label>
                        <input id="notif-title" type="text" required placeholder="e.g. New Episode Available!" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Notification Body *</label>
                        <textarea id="notif-body" required rows="3" placeholder="e.g. Watch the latest episode of your favorite K-Drama now." class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Image Type -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Image Type</label>
                            <select id="notif-image-type" onchange="toggleDirectImageInput()" class="w-full bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition">
                                <option value="url">🔗 Image URL / TMDB Path</option>
                                <option value="manual">📁 Manual Upload (Auto WebP)</option>
                            </select>
                        </div>

                        <!-- Image URL / TMDB Path Input -->
                        <div id="direct-image-path-wrapper" class="space-y-1.5 sm:col-span-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Image URL or TMDB Path</label>
                            <input id="notif-image" type="text" placeholder="https://example.com/image.jpg or /backdrop.jpg" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                        </div>

                        <!-- Manual Upload File Input -->
                        <div id="direct-image-file-wrapper" class="space-y-1.5 sm:col-span-2 hidden">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Upload Local Image</label>
                            <input id="notif-image-file" type="file" accept="image/*" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                        </div>
                    </div>

                    <div class="border-t border-white/5 pt-4 mt-4">
                        <h3 class="text-sm font-semibold text-white mb-4">Deep Link Routing (Optional)</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Target Screen</label>
                                <select id="notif-screen" class="w-full bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition">
                                    <option value="">None (Default)</option>
                                    <option value="home">Home Screen</option>
                                    <option value="search">Search Screen</option>
                                    <option value="watch">Watch / Player Screen</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Content Type</label>
                                <select id="notif-item-type" class="w-full bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-violet-500/40 transition">
                                    <option value="movie">🎬 Movie</option>
                                    <option value="tv">📺 TV Show</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Drama ID / Slug</label>
                                <input id="notif-slug" type="text" placeholder="e.g. 693134 or 1" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Episode Number</label>
                                <input id="notif-episode" type="text" placeholder="e.g. 1" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-white/5 flex justify-end">
                        <button id="direct-submit-btn" type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-8 py-3 rounded-xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send Push Notification
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════
         Template Library Tab
         ══════════════════════════════════════════════════════ -->
    <div id="tab-library" class="space-y-6 hidden">
        
        <!-- Controls & Send Random -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Movie Random Dispatcher Card -->
            <div class="glass p-5 rounded-2xl flex flex-col justify-between border border-violet-500/10 hover:border-violet-500/20 transition duration-300">
                <div>
                    <h3 class="font-bold text-white text-sm">🎬 Movie Recommendations</h3>
                    <p class="text-xs text-slate-400 mt-1">Dispatches a random movie template from the library below via FCM topic push.</p>
                </div>
                <button onclick="sendRandomNotification('movie', this)" class="mt-4 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-violet-600/20 border border-violet-500/30 text-violet-400 rounded-xl hover:bg-violet-600/30 text-xs font-bold transition">
                    🎲 Send Random Movie Push
                </button>
            </div>

            <!-- TV Random Dispatcher Card -->
            <div class="glass p-5 rounded-2xl flex flex-col justify-between border border-fuchsia-500/10 hover:border-fuchsia-500/20 transition duration-300">
                <div>
                    <h3 class="font-bold text-white text-sm">📺 TV Show Recommendations</h3>
                    <p class="text-xs text-slate-400 mt-1">Dispatches a random TV template from the library below via FCM topic push.</p>
                </div>
                <button onclick="sendRandomNotification('tv', this)" class="mt-4 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-fuchsia-600/20 border border-fuchsia-500/30 text-fuchsia-400 rounded-xl hover:bg-fuchsia-600/30 text-xs font-bold transition">
                    🎲 Send Random TV Show Push
                </button>
            </div>

            <!-- Create New Template Card -->
            <div class="glass p-5 rounded-2xl flex flex-col justify-center items-center border border-white/5 hover:border-white/10 transition duration-300 text-center">
                <p class="text-xs text-slate-400 max-w-[200px] mb-4">Create a library of notifications that can be scheduled or dispatched randomly.</p>
                <button onclick="openTemplateModal()" class="w-full inline-flex items-center justify-center gap-1.5 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-4 py-2.5 rounded-xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Create Template
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="glass p-4 rounded-2xl flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="library-search" type="text" placeholder="Search template library by title or body..." oninput="filterTemplates()" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
            </div>
            <select id="library-type-filter" onchange="filterTemplates()" class="bg-[#1E1E2E] border border-white/5 text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                <option value="">All Types</option>
                <option value="movie">🎬 Movies</option>
                <option value="tv">📺 TV Shows</option>
            </select>
        </div>

        <!-- Templates List -->
        <div class="glass rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/5 text-left text-slate-400">
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Template Title / Body</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Type</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Image Source</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Deep Linking</th>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="library-table-body" class="divide-y divide-white/5 text-slate-200">
                        <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500 animate-pulse">Loading templates library...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     Template CRUD Modal (Add / Edit)
     ══════════════════════════════════════════════════════ -->
<div id="template-modal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="closeTemplateModal(event)">
    <div id="template-modal-panel" class="w-full max-w-2xl bg-[#121220] rounded-3xl border border-white/8 shadow-2xl overflow-hidden mx-4 max-h-[90vh] overflow-y-auto scrollbar-thin">
        
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-white/5 flex justify-between items-center sticky top-0 bg-[#121220] z-10">
            <h3 id="modal-title" class="text-lg font-extrabold text-white">Create Template</h3>
            <button onclick="closeTemplateModal()" class="p-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- TMDB Search (Optional prefill helper) -->
        <div id="tmdb-search-section" class="px-6 py-4 border-b border-white/5 space-y-3">
            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Prefill with TMDB Data (Optional)</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="tmdb-search" type="text" placeholder="Type movie/show title to search TMDB..." oninput="searchTmdb()" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
            </div>
            <div id="tmdb-results" class="space-y-2 max-h-52 overflow-y-auto scrollbar-thin hidden"></div>
        </div>

        <!-- CRUD Form -->
        <form id="template-form" onsubmit="submitTemplateForm(event)" class="px-6 py-5 space-y-4" enctype="multipart/form-data">
            <input type="hidden" id="form-id" value=""/>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Type -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Template Type *</label>
                    <select id="form-type" required class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="movie">🎬 Movie</option>
                        <option value="tv">📺 TV Show</option>
                    </select>
                </div>

                <!-- TMDB ID -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">TMDB ID (Optional)</label>
                    <input id="form-tmdb-id" type="number" placeholder="e.g. 693134" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Title -->
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Notification Title *</label>
                    <input id="form-title" type="text" required placeholder="e.g. Special Content Alert!" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Body -->
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Notification Body *</label>
                    <textarea id="form-body" required rows="3" placeholder="Write description or preview details..." class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"></textarea>
                </div>

                <!-- Image Type -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Image Type *</label>
                    <select id="form-image-type" onchange="toggleImageInput()" required class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                        <option value="tmdb">🎬 TMDB CDN Path</option>
                        <option value="manual">📁 Manual Upload (Auto WebP)</option>
                    </select>
                </div>

                <!-- TMDB Image Path Input -->
                <div id="image-path-wrapper" class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">TMDB Backdrop/Poster Path</label>
                    <input id="form-image-path" type="text" placeholder="e.g. /xJHokMbljvjrrclvST6U6.jpg" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <!-- Manual Upload Input -->
                <div id="image-file-wrapper" class="space-y-1.5 hidden">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Upload Local Image</label>
                    <input id="form-image" type="file" accept="image/*" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-3 py-1.5 focus:outline-none focus:border-violet-500/40 transition"/>
                    <p class="text-[10px] text-violet-400 italic">Images convert automatically to WebP & overwrite old ones.</p>
                </div>
            </div>

            <!-- Deep Link Routing -->
            <div class="border-t border-white/5 pt-4 mt-2">
                <h4 class="text-sm font-bold text-white mb-3">Deep Linking Routing Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Target Screen</label>
                        <select id="form-screen" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition">
                            <option value="">None (Default)</option>
                            <option value="home">Home Screen</option>
                            <option value="search">Search Screen</option>
                            <option value="watch">Watch / Player Screen</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Drama Slug / ID</label>
                        <input id="form-drama-slug" type="text" placeholder="e.g. stranger-things" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Episode Number</label>
                        <input id="form-episode-number" type="text" placeholder="e.g. 5" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-violet-500/40 transition"/>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/5 flex justify-end gap-3">
                <button type="button" onclick="closeTemplateModal()" class="px-6 py-2.5 text-sm font-semibold bg-[#1E1E2E] border border-white/5 rounded-xl text-slate-300 hover:text-white transition">Cancel</button>
                <button id="modal-submit-btn" type="submit" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-6 py-2.5 rounded-xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg">Save Template</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     Delete Confirmation Modal
     ══════════════════════════════════════════════════════ -->
<div id="delete-modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden" onclick="closeDeleteModal(event)">
    <div class="w-full max-w-md bg-[#121220] rounded-3xl border border-white/8 p-6 shadow-2xl text-center space-y-5 mx-4">
        <div class="w-12 h-12 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-2xl flex items-center justify-center mx-auto text-xl font-bold">⚠️</div>
        <div>
            <h3 class="text-base font-extrabold text-white">Delete Template</h3>
            <p class="text-slate-400 text-xs mt-2">Are you sure you want to delete this notification template? If it has a manual image upload, the file will be removed permanently.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-2.5 text-xs font-bold bg-[#1E1E2E] border border-white/5 rounded-xl text-slate-300 hover:text-white transition">Cancel</button>
            <button onclick="confirmDeleteTemplate()" class="flex-1 py-2.5 text-xs font-bold bg-rose-600 rounded-xl text-white hover:bg-rose-500 transition">Delete</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-8 right-6 z-[999] hidden">
    <div id="toast-inner" class="px-5 py-3 rounded-2xl shadow-2xl text-sm font-bold text-white flex items-center gap-2 animate-slideUp"></div>
</div>

<script>
let activeTab = 'direct';
let templatesList = [];
let deleteTargetId = null;
let tmdbSearchTimer = null;

document.addEventListener('DOMContentLoaded', () => {
    loadTemplates();
});

// ── Tab Management ────────────────────────────────────────────────────────────
function switchTab(tab) {
    activeTab = tab;
    const directBtn = document.getElementById('tab-btn-direct');
    const libraryBtn = document.getElementById('tab-btn-library');
    const directPanel = document.getElementById('tab-direct');
    const libraryPanel = document.getElementById('tab-library');

    if (tab === 'direct') {
        directBtn.className = "px-4 py-2 text-sm font-semibold border-b-2 border-violet-500 text-violet-400 focus:outline-none transition-all duration-200";
        libraryBtn.className = "px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-200 focus:outline-none transition-all duration-200";
        directPanel.classList.remove('hidden');
        libraryPanel.classList.add('hidden');
    } else {
        directBtn.className = "px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-200 focus:outline-none transition-all duration-200";
        libraryBtn.className = "px-4 py-2 text-sm font-semibold border-b-2 border-violet-500 text-violet-400 focus:outline-none transition-all duration-200";
        directPanel.classList.add('hidden');
        libraryPanel.classList.remove('hidden');
        loadTemplates();
    }
}

// ── Direct Push Dispatch ──────────────────────────────────────────────────────
async function sendDirectNotification(e) {
    e.preventDefault();
    
    const btn = document.getElementById('direct-submit-btn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-4 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...`;

    const formData = new FormData();
    formData.append('title', document.getElementById('notif-title').value);
    formData.append('body', document.getElementById('notif-body').value);
    formData.append('screen', document.getElementById('notif-screen').value);
    formData.append('drama_slug', document.getElementById('notif-slug').value);
    formData.append('episode_number', document.getElementById('notif-episode').value);
    formData.append('item_type', document.getElementById('notif-item-type').value);
    formData.append('image_type', document.getElementById('notif-image-type').value);
    formData.append('_token', '{{ csrf_token() }}');

    const imageType = document.getElementById('notif-image-type').value;
    if (imageType === 'manual') {
        const fileInput = document.getElementById('notif-image-file');
        if (fileInput.files[0]) {
            formData.append('image', fileInput.files[0]);
        }
    } else {
        formData.append('image_url', document.getElementById('notif-image').value);
    }

    try {
        const res = await fetch('/admin/api/notifications/send', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
        });
        
        const data = await res.json();
        
        if (res.ok && data.success) {
            showToast(data.message);
            document.getElementById('notification-form').reset();
            toggleDirectImageInput();
        } else {
            showToast(data.message || 'Unknown error occurred', 'error');
        }
    } catch(err) {
        showToast('Network error or server error.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function toggleDirectImageInput() {
    const type = document.getElementById('notif-image-type').value;
    const pathWrapper = document.getElementById('direct-image-path-wrapper');
    const fileWrapper = document.getElementById('direct-image-file-wrapper');

    if (type === 'manual') {
        pathWrapper.classList.add('hidden');
        fileWrapper.classList.remove('hidden');
    } else {
        pathWrapper.classList.remove('hidden');
        fileWrapper.classList.add('hidden');
    }
}

// ── CRUD - Load Templates ─────────────────────────────────────────────────────
async function loadTemplates() {
    const tbody = document.getElementById('library-table-body');
    try {
        const res = await fetch('/admin/api/scheduled-notifications');
        const data = await res.json();
        templatesList = data;
        renderTemplatesTable(templatesList);
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-5 py-12 text-center text-rose-400 text-xs">Failed to load templates.</td></tr>`;
    }
}

function renderTemplatesTable(templates) {
    const tbody = document.getElementById('library-table-body');
    if (templates.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-5 py-16 text-center text-slate-500 text-xs">No notification templates found. Create one above!</td></tr>`;
        return;
    }

    tbody.innerHTML = templates.map(t => {
        const typeBadge = t.type === 'movie'
            ? `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#0984E3]/20 text-[#0984E3] rounded-md border border-[#0984E3]/20">🎬 MOVIE</span>`
            : `<span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#00B894]/20 text-[#00B894] rounded-md border border-[#00B894]/20">📺 TV</span>`;

        let imgHtml = `<span class="text-[10px] text-slate-500 italic">None</span>`;
        if (t.image_path) {
            const previewUrl = t.image_type === 'tmdb'
                ? `https://image.tmdb.org/t/p/w92${t.image_path}`
                : t.image_path;
            imgHtml = `<div class="flex items-center gap-2">
                <img src="${previewUrl}" class="w-10 h-7 rounded object-cover border border-white/10 bg-[#1E1E2E] flex-shrink-0" onerror="this.src='https://placehold.co/40x28/1E1E2E/FFF?text=N/A'"/>
                <span class="text-[9px] text-slate-400 capitalize">${t.image_type}</span>
            </div>`;
        }

        const deepLinkStr = t.screen 
            ? `<span class="text-xs font-bold text-violet-400 capitalize">${t.screen}</span>${t.drama_slug ? `<br/><span class="text-[10px] text-slate-500">Slug: ${t.drama_slug}</span>` : ''}`
            : `<span class="text-[10px] text-slate-500">—</span>`;

        return `
        <tr class="hover:bg-white/1 transition duration-200">
            <td class="px-5 py-3">
                <div class="font-bold text-white text-xs">${t.title}</div>
                <div class="text-[10px] text-slate-400 line-clamp-1 max-w-sm mt-0.5">${t.body}</div>
            </td>
            <td class="px-5 py-3">${typeBadge}</td>
            <td class="px-5 py-3">${imgHtml}</td>
            <td class="px-5 py-3">${deepLinkStr}</td>
            <td class="px-5 py-3">
                <div class="flex gap-2">
                    <button onclick="sendSpecificNotification(${t.id}, this)" class="p-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition" title="Send Notification Immediately">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                    <button onclick="editTemplate(${JSON.stringify(t).replace(/"/g,'&quot;')})" class="p-1.5 rounded-lg bg-violet-500/10 border border-violet-500/20 text-violet-400 hover:bg-violet-500/20 transition" title="Edit Template">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="openDeleteModal(${t.id})" class="p-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition" title="Delete Template">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function filterTemplates() {
    const q = document.getElementById('library-search').value.toLowerCase();
    const type = document.getElementById('library-type-filter').value;
    
    const filtered = templatesList.filter(t => {
        const matchesQuery = t.title.toLowerCase().includes(q) || t.body.toLowerCase().includes(q);
        const matchesType = !type || t.type === type;
        return matchesQuery && matchesType;
    });

    renderTemplatesTable(filtered);
}

// ── Image Input Toggle ────────────────────────────────────────────────────────
function toggleImageInput() {
    const type = document.getElementById('form-image-type').value;
    const pathWrapper = document.getElementById('image-path-wrapper');
    const fileWrapper = document.getElementById('image-file-wrapper');

    if (type === 'manual') {
        pathWrapper.classList.add('hidden');
        fileWrapper.classList.remove('hidden');
    } else {
        pathWrapper.classList.remove('hidden');
        fileWrapper.classList.add('hidden');
    }
}

// ── Modals Logic ──────────────────────────────────────────────────────────────
function openTemplateModal() {
    document.getElementById('modal-title').innerText = 'Create Template';
    document.getElementById('template-form').reset();
    document.getElementById('form-id').value = '';
    document.getElementById('tmdb-search-section').classList.remove('hidden');
    document.getElementById('template-modal').classList.remove('hidden');
    toggleImageInput();
    document.body.style.overflow = 'hidden';
}

function editTemplate(t) {
    document.getElementById('modal-title').innerText = 'Edit Template';
    document.getElementById('tmdb-search-section').classList.add('hidden');

    document.getElementById('form-id').value = t.id;
    document.getElementById('form-type').value = t.type;
    document.getElementById('form-title').value = t.title;
    document.getElementById('form-body').value = t.body;
    document.getElementById('form-image-type').value = t.image_type;
    document.getElementById('form-tmdb-id').value = t.tmdb_id || '';
    document.getElementById('form-screen').value = t.screen || '';
    document.getElementById('form-drama-slug').value = t.drama_slug || '';
    document.getElementById('form-episode-number').value = t.episode_number || '';

    toggleImageInput();

    if (t.image_type === 'tmdb') {
        document.getElementById('form-image-path').value = t.image_path || '';
    } else {
        document.getElementById('form-image-path').value = '';
    }

    document.getElementById('template-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTemplateModal(event) {
    if (event && event.target !== document.getElementById('template-modal')) return;
    document.getElementById('template-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── Submit CRUD Form ──────────────────────────────────────────────────────────
async function submitTemplateForm(event) {
    event.preventDefault();

    const btn = document.getElementById('modal-submit-btn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const id = document.getElementById('form-id').value;
    const isEdit = !!id;

    // Use FormData for file uploads
    const formData = new FormData();
    formData.append('title', document.getElementById('form-title').value);
    formData.append('body', document.getElementById('form-body').value);
    formData.append('type', document.getElementById('form-type').value);
    formData.append('image_type', document.getElementById('form-image-type').value);
    formData.append('tmdb_id', document.getElementById('form-tmdb-id').value);
    formData.append('screen', document.getElementById('form-screen').value);
    formData.append('drama_slug', document.getElementById('form-drama-slug').value);
    formData.append('episode_number', document.getElementById('form-episode-number').value);
    formData.append('_token', '{{ csrf_token() }}');

    const imageType = document.getElementById('form-image-type').value;
    if (imageType === 'manual') {
        const fileInput = document.getElementById('form-image');
        if (fileInput.files[0]) {
            formData.append('image', fileInput.files[0]);
        }
    } else {
        formData.append('image_path', document.getElementById('form-image-path').value);
    }

    const url = isEdit 
        ? `/admin/api/scheduled-notifications/${id}`
        : '/admin/api/scheduled-notifications';

    try {
        const res = await fetch(url, {
            method: 'POST', // We use POST for update as well to natively support multipart file uploads
            headers: { 'Accept': 'application/json' },
            body: formData
        });

        const data = await res.json();

        if (res.ok) {
            showToast(isEdit ? 'Template updated successfully.' : 'Template created successfully.');
            closeTemplateModal();
            loadTemplates();
        } else {
            showToast(data.message || 'Validation or database error.', 'error');
        }
    } catch(e) {
        showToast('Network error or server error.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

// ── Delete Template ───────────────────────────────────────────────────────────
function openDeleteModal(id) {
    deleteTargetId = id;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal(event) {
    if (event && event.target !== document.getElementById('delete-modal')) return;
    document.getElementById('delete-modal').classList.add('hidden');
    deleteTargetId = null;
}

async function confirmDeleteTemplate() {
    if (!deleteTargetId) return;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(`/admin/api/scheduled-notifications/${deleteTargetId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });

        if (res.ok) {
            showToast('Template deleted successfully.');
            closeDeleteModal();
            loadTemplates();
        } else {
            showToast('Failed to delete template.', 'error');
        }
    } catch(e) {
        showToast('Network error.', 'error');
    }
}

// ── Send Specific Notification ────────────────────────────────────────────────
async function sendSpecificNotification(id, button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `<svg class="animate-spin h-3.5 w-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(`/admin/api/scheduled-notifications/${id}/send`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
        } else {
            showToast(data.message || 'Failed to send notification.', 'error');
        }
    } catch(e) {
        showToast('Network error.', 'error');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// ── Send Random Notification ──────────────────────────────────────────────────
async function sendRandomNotification(type, button) {
    const originalText = button.textContent;
    button.disabled = true;
    button.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...`;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch('/admin/api/scheduled-notifications/send-random', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type })
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
        } else {
            showToast(data.message || 'Failed to send random notification.', 'error');
        }
    } catch(e) {
        showToast('Network error.', 'error');
    } finally {
        button.disabled = false;
        button.textContent = originalText;
    }
}

// ── TMDB Inline Search & Prefill ──────────────────────────────────────────────
function searchTmdb() {
    clearTimeout(tmdbSearchTimer);
    const q = document.getElementById('tmdb-search').value.trim();
    if (q.length < 2) {
        document.getElementById('tmdb-results').classList.add('hidden');
        return;
    }

    const type = document.getElementById('form-type').value;

    tmdbSearchTimer = setTimeout(async () => {
        try {
            // Search movies or TV shows depending on selected type
            const endpoint = `/api/tmdb/search/${type}?query=${encodeURIComponent(q)}`;
            const data = await fetch(endpoint).then(r => r.json());
            const results = (data.results || []).slice(0, 6);
            const container = document.getElementById('tmdb-results');

            if (results.length === 0) {
                container.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');
            container.innerHTML = results.map(r => {
                const title = r.title || r.name;
                const year = (r.release_date || r.first_air_date || '').substring(0,4);
                const poster = r.poster_path 
                    ? `https://image.tmdb.org/t/p/w92${r.poster_path}` 
                    : `https://placehold.co/46x69/1E1E2E/FFF?text=${encodeURIComponent(title.substring(0,2))}`;
                const typeIcon = type === 'movie' ? '🎬' : '📺';
                
                return `<button type="button" onclick='selectTmdbContent(${JSON.stringify({id:r.id,title:title,poster:r.poster_path||"",backdrop:r.backdrop_path||"",overview:r.overview||""}).replace(/'/g,"\\'")})'
                    class="flex items-center gap-3 w-full p-2 rounded-xl hover:bg-white/5 text-left border border-white/0 hover:border-violet-500/20 transition">
                    <img src="${poster}" class="w-8 h-11 rounded-lg object-cover bg-[#1E1E2E] flex-shrink-0"/>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">${typeIcon} ${title}</p>
                        <p class="text-[10px] text-slate-400">${year} · ID ${r.id}</p>
                    </div>
                </button>`;
            }).join('');
        } catch(e) {
            console.error(e);
        }
    }, 350);
}

function selectTmdbContent(c) {
    document.getElementById('form-tmdb-id').value = c.id;
    document.getElementById('form-title').value = c.title;
    document.getElementById('form-body').value = c.overview || '';
    
    // Choose TMDB image type and prefill path
    document.getElementById('form-image-type').value = 'tmdb';
    toggleImageInput();

    // Use backdrop if available, otherwise poster
    const imgPath = c.backdrop || c.poster || '';
    document.getElementById('form-image-path').value = imgPath;

    // Prefill linking details
    document.getElementById('form-screen').value = 'watch';
    
    // Prefill with TMDB ID since the app routes using TMDB ID
    document.getElementById('form-drama-slug').value = c.id;

    // Hide search results
    document.getElementById('tmdb-results').classList.add('hidden');
    document.getElementById('tmdb-search').value = '';
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
