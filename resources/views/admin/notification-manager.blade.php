@extends('layouts.layout')
@section('title', 'Notification Manager — CineMovie Admin')

@section('content')
<div class="px-6 py-8 max-w-4xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Notification Manager</h1>
            <p class="text-slate-400 text-sm mt-1">Send push notifications to all users via Firebase Cloud Messaging.</p>
        </div>
    </div>

    <!-- Notification Composer -->
    <div class="glass p-6 rounded-3xl space-y-6">
        <form id="notification-form" onsubmit="sendNotification(event)">
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Notification Title *</label>
                    <input id="notif-title" type="text" required placeholder="e.g. New Episode Available!" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Notification Body *</label>
                    <textarea id="notif-body" required rows="3" placeholder="e.g. Watch the latest episode of your favorite K-Drama now." class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"></textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Image URL (Optional)</label>
                    <input id="notif-image" type="url" placeholder="https://example.com/image.jpg" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                </div>

                <div class="border-t border-white/5 pt-4 mt-4">
                    <h3 class="text-sm font-semibold text-white mb-4">Deep Link Routing (Optional)</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Drama Slug / ID</label>
                            <input id="notif-slug" type="text" placeholder="e.g. squid-game" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Episode Number</label>
                            <input id="notif-episode" type="text" placeholder="e.g. 1" class="w-full bg-[#1E1E2E] border border-white/5 text-white text-sm rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:border-violet-500/40 transition"/>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-white/5">
                    <button id="submit-btn" type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold px-8 py-3 rounded-xl hover:from-violet-500 hover:to-fuchsia-500 transition shadow-lg shadow-violet-500/20 text-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Send Push Notification
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
async function sendNotification(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submit-btn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...`;

    const payload = {
        title: document.getElementById('notif-title').value,
        body: document.getElementById('notif-body').value,
        image_url: document.getElementById('notif-image').value,
        screen: document.getElementById('notif-screen').value,
        drama_slug: document.getElementById('notif-slug').value,
        episode_number: document.getElementById('notif-episode').value,
        _token: '{{ csrf_token() }}'
    };

    try {
        const res = await fetch('{{ url("/admin/api/notifications/send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        
        const data = await res.json();
        
        if(res.ok && data.success) {
            alert('Success: ' + data.message);
            document.getElementById('notification-form').reset();
        } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
        }
    } catch(err) {
        alert('Error: Network or server error. Check console.');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
</script>
@endsection
