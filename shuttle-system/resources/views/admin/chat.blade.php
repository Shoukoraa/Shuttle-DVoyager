@extends('admin.layout')

@section('content')
<!-- Include dependencies for chat -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
    /* ─── SCROLLBAR ─── */
    .chat-scroll::-webkit-scrollbar { width: 4px; }
    .chat-scroll::-webkit-scrollbar-track { background: transparent; }
    .chat-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }

    /* ─── CHAT BUBBLES ─── */
    .bubble-user {
        background: #f1f5f9;
        color: #1e293b;
        border-radius: 4px 14px 14px 14px;
        padding: 9px 13px;
        font-size: 13px;
        line-height: 1.5;
    }
    .bubble-admin {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border-radius: 14px 4px 14px 14px;
        padding: 9px 13px;
        font-size: 13px;
        line-height: 1.5;
    }
    .bubble-bot {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #fff;
        border-radius: 14px 4px 14px 14px;
        padding: 9px 13px;
        font-size: 13px;
        line-height: 1.5;
    }
    .bubble-system {
        background: #fef3c7;
        color: #92400e;
        border-radius: 12px;
        border: 1px solid #fde68a;
        font-size: 11px;
        text-align: center;
        padding: 6px 12px;
        align-self: center;
    }

    /* ─── ANIMATIONS ─── */
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .msg-anim { animation: fadeSlideUp 0.3s ease both; }

    /* ─── ADMIN PANEL ─── */
    .admin-session-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.15s;
        margin-bottom: 8px;
    }
    .admin-session-item:hover { border-color: #3b82f6; background: #eff6ff; }
    .admin-session-item.active { border-color: #3b82f6; background: #eff6ff; }
    
    @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.5} }
    .pulse-dot { animation: pulse-dot 2s infinite; }
</style>

<div class="h-[calc(100vh-140px)] flex bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">

    <!-- Sidebar Queue -->
    <div class="w-72 flex-shrink-0 border-r border-gray-100 flex flex-col bg-gray-50/50">
        <div class="p-4 border-b border-gray-100 bg-white flex items-center justify-between">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Antrean Chat Aktif</span>
            <div id="reverb-indicator" class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-red-400 pulse-dot"></span> Reverb...
            </div>
        </div>
        <div id="admin-session-list" class="flex-1 overflow-y-auto p-3 chat-scroll">
            <div id="admin-empty-msg" class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <div class="text-sm font-semibold">Belum ada antrean</div>
                <div class="text-xs mt-1">Menunggu customer menghubungi admin...</div>
            </div>
        </div>
    </div>

    <!-- Chat Console -->
    <div class="flex-1 flex flex-col min-w-0 bg-white">
        
        <!-- Splash (no session selected) -->
        <div id="admin-splash" class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8">
            <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            <div class="text-lg font-bold text-gray-500">Pilih Antrean Chat</div>
            <div class="text-sm mt-2 text-center max-w-xs leading-relaxed">Pilih salah satu sesi dari panel kiri untuk mulai membalas chat pelanggan secara realtime.</div>
        </div>

        <!-- Active Chat View -->
        <div id="admin-active-view" class="hidden flex-col h-full">
            <!-- Session Info Bar -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white flex-shrink-0 shadow-sm z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-500/10 text-brand-600 flex items-center justify-center font-bold text-lg border border-brand-500/20">
                        <span id="admin-client-initial">C</span>
                    </div>
                    <div>
                        <div id="admin-client-name" class="text-sm font-bold text-gray-900">—</div>
                        <div id="admin-session-meta" class="text-xs text-gray-500 mt-0.5">—</div>
                    </div>
                </div>
                <button onclick="adminResolveSession()" class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-4 py-2 text-xs font-bold hover:bg-emerald-100 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Selesaikan Sesi
                </button>
            </div>
            
            <!-- Messages -->
            <div id="admin-chat-box" class="flex-1 overflow-y-auto p-5 flex flex-col gap-4 bg-[#f8fafc] chat-scroll relative">
                <!-- Messages will be injected here -->
            </div>
            
            <!-- Reply Bar -->
            <div class="p-4 bg-white border-t border-gray-100 flex-shrink-0">
                <div class="flex gap-2">
                    <input id="admin-msg-input" type="text" placeholder="Tulis balasan sebagai Admin CS..." class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-brand-500 transition-colors text-gray-800"
                        onkeydown="if(event.key==='Enter') adminSendMessage()">
                    <button onclick="adminSendMessage()" class="bg-gradient-to-br from-emerald-500 to-emerald-700 text-white border-none rounded-xl px-6 py-2.5 text-sm font-bold hover:opacity-90 active:scale-95 transition-all shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim
                    </button>
                </div>
                <div class="mt-3 flex gap-2 flex-wrap">
                    <button onclick="adminQuickReply('Halo! Ada yang bisa kami bantu? 😊')" class="bg-gray-100 border-none rounded-full px-3 py-1 text-xs text-gray-600 hover:bg-gray-200 transition-colors">Halo 👋</button>
                    <button onclick="adminQuickReply('Mohon ditunggu sebentar ya, kami sedang mengeceknya.')" class="bg-gray-100 border-none rounded-full px-3 py-1 text-xs text-gray-600 hover:bg-gray-200 transition-colors">Mohon tunggu</button>
                    <button onclick="adminQuickReply('Terima kasih sudah menghubungi kami! Masalah Anda sudah kami catat. 🙏')" class="bg-gray-100 border-none rounded-full px-3 py-1 text-xs text-gray-600 hover:bg-gray-200 transition-colors">Terima kasih</button>
                    <button onclick="adminQuickReply('Masalah sudah kami selesaikan. Ada hal lain yang bisa dibantu?')" class="bg-gray-100 border-none rounded-full px-3 py-1 text-xs text-gray-600 hover:bg-gray-200 transition-colors">Selesai ✅</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let devToken = '{{ csrf_token() }}'; // Or properly set up authentication if needed for API
    let adminEcho = null;
    let activeAdminSessionId = null;

    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    window.addEventListener('DOMContentLoaded', () => {
        initEcho();
    });

    function initEcho() {
        window.Pusher = Pusher;
        const echoConfig = {
            broadcaster: 'reverb',
            key: '{{ env("REVERB_APP_KEY") }}',
            wsHost: '{{ env("REVERB_HOST", "localhost") }}',
            wsPort: {{ env("REVERB_PORT", 8080) }},
            wssPort: {{ env("REVERB_PORT", 8080) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/admin/broadcasting/auth'
        };

        adminEcho = new Echo(echoConfig);

        adminEcho.connector.pusher.connection.bind('connected', () => {
            const ind = document.getElementById('reverb-indicator');
            ind.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Reverb OK';
            ind.className = 'flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100';
        });
        
        adminEcho.connector.pusher.connection.bind('disconnected', () => {
            const ind = document.getElementById('reverb-indicator');
            ind.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-500 pulse-dot"></span> Terputus';
            ind.className = 'flex items-center gap-1.5 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-100';
        });

        adminEcho.private('admin.chat')
            .listen('SessionStatusChanged', () => { refreshAdminQueue(); });

        refreshAdminQueue();
    }

    function refreshAdminQueue() {
        axios.get('/admin/chat/active-sessions')
            .then(r => {
                const sessions = r.data;
                const list = document.getElementById('admin-session-list');
                const empty = document.getElementById('admin-empty-msg');

                list.innerHTML = '';

                if (!sessions.length) {
                    empty.style.display = 'block';
                    list.appendChild(empty);
                    return;
                }

                sessions.forEach(s => {
                    const isActive = s.id === activeAdminSessionId;
                    const item = document.createElement('div');
                    item.className = 'admin-session-item' + (isActive ? ' active' : '');
                    const time = new Date(s.created_at).toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
                    const clientName = s.user ? s.user.name : 'Pelanggan';
                    item.innerHTML = `
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm font-bold text-gray-900 truncate pr-2">${clientName}</span>
                            <span class="text-[10px] text-gray-400 flex-shrink-0">${time}</span>
                        </div>
                        <div class="text-[11px] text-gray-500 mb-2 truncate">
                            <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            ${s.last_category ? s.last_category.name : 'Umum'}
                        </div>
                        <div class="flex gap-1.5 items-center">
                            <span class="bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider">Aktif</span>
                        </div>
                    `;
                    item.onclick = () => openAdminSession(s);
                    list.appendChild(item);
                });
            });
    }

    function openAdminSession(session) {
        if (activeAdminSessionId && adminEcho) {
            adminEcho.leave(`chat.${activeAdminSessionId}`);
        }
        activeAdminSessionId = session.id;

        document.getElementById('admin-splash').style.display = 'none';
        document.getElementById('admin-active-view').style.display = 'flex';
        
        const clientName = session.user ? session.user.name : 'Pelanggan';
        document.getElementById('admin-client-name').textContent = clientName;
        document.getElementById('admin-client-initial').textContent = clientName.charAt(0).toUpperCase();
        document.getElementById('admin-session-meta').textContent = `Sesi ID: ${session.id.substring(0,8)}... | Kategori: ${session.last_category ? session.last_category.name : 'Umum'}`;

        axios.get(`/admin/chat/${session.id}/history`)
            .then(r => {
                const box = document.getElementById('admin-chat-box');
                box.innerHTML = '';
                (r.data.messages || []).forEach(m => {
                    if (m.sender_type === 'user') {
                        appendAdminBoxUserMsg(m.message_content);
                    } else {
                        appendAdminBoxAdminMsg(m.message_content, m.sender_type === 'bot');
                    }
                });
                scrollAdminChat();
            });

        adminEcho.private(`chat.${session.id}`)
            .listen('MessageSent', e => {
                if (e.sender_type === 'user') {
                    appendAdminBoxUserMsg(e.message_content);
                    scrollAdminChat();
                }
            })
            .listen('SessionStatusChanged', e => {
                if (e.status === 'resolved') {
                    appendAdminSystemMsg('Sesi ini telah diselesaikan.');
                    activeAdminSessionId = null;
                    setTimeout(() => {
                        document.getElementById('admin-active-view').style.display = 'none';
                        document.getElementById('admin-splash').style.display = 'flex';
                        refreshAdminQueue();
                    }, 2000);
                }
            });

        refreshAdminQueue();
    }

    function adminSendMessage() {
        const input = document.getElementById('admin-msg-input');
        const msg = input.value.trim();
        if (!msg || !activeAdminSessionId) return;
        input.value = '';

        appendAdminBoxAdminMsg(msg, false);
        scrollAdminChat();

        axios.post(`/admin/chat/${activeAdminSessionId}/messages`, { message: msg })
            .catch(() => alert('Gagal mengirim pesan.'));
    }

    function adminQuickReply(text) {
        document.getElementById('admin-msg-input').value = text;
        document.getElementById('admin-msg-input').focus();
    }

    function adminResolveSession() {
        if (!activeAdminSessionId) return;
        axios.post(`/admin/chat/${activeAdminSessionId}/resolve`).then(() => {
            adminEcho && adminEcho.leave(`chat.${activeAdminSessionId}`);
            activeAdminSessionId = null;
            document.getElementById('admin-active-view').style.display = 'none';
            document.getElementById('admin-splash').style.display = 'flex';
            refreshAdminQueue();
        });
    }

    function appendAdminBoxUserMsg(text) {
        const el = document.createElement('div');
        el.className = 'msg-anim flex items-end gap-2 max-w-[75%]';
        el.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div class="bubble-user shadow-sm">${text}</div>
        `;
        document.getElementById('admin-chat-box').appendChild(el);
    }

    function appendAdminBoxAdminMsg(text, isBot = false) {
        const el = document.createElement('div');
        el.className = 'msg-anim flex justify-end self-end max-w-[75%]';
        const label = isBot ? 'Bot' : 'Admin CS (Anda)';
        const bubbleClass = isBot ? 'bubble-bot' : 'bubble-admin';
        el.innerHTML = `
            <div class="text-right w-full">
                <div class="text-[10px] font-bold text-gray-400 mb-1">${label}</div>
                <div class="${bubbleClass} shadow-sm inline-block text-left">${text}</div>
            </div>
        `;
        document.getElementById('admin-chat-box').appendChild(el);
    }

    function appendAdminSystemMsg(text) {
        const el = document.createElement('div');
        el.className = 'msg-anim bubble-system my-2';
        el.textContent = text;
        document.getElementById('admin-chat-box').appendChild(el);
    }

    function scrollAdminChat() {
        const box = document.getElementById('admin-chat-box');
        setTimeout(() => { box.scrollTop = box.scrollHeight; }, 50);
    }
</script>
@endsection
