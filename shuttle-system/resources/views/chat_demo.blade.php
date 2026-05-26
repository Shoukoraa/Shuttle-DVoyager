<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Chatbot Demo — Shuttle System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Libs -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

        body {
            background: #0f172a;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }

        /* ─── PHONE FRAME ─── */
        .phone-frame {
            background: #f8fafc;
            border-radius: 36px;
            border: 8px solid #1e293b;
            box-shadow: 0 0 0 2px #334155, 0 30px 60px rgba(0,0,0,0.5);
            overflow: hidden;
            flex: 1;
            width: 370px;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        /* ─── CHAT BUBBLES ─── */
        .bubble-bot {
            background: #fff;
            color: #1e293b;
            border-radius: 4px 18px 18px 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .bubble-user {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #fff;
            border-radius: 18px 4px 18px 18px;
        }
        .bubble-admin {
            background: linear-gradient(135deg, #059669, #047857);
            color: #fff;
            border-radius: 4px 18px 18px 18px;
        }
        .bubble-system {
            background: #fef3c7;
            color: #92400e;
            border-radius: 12px;
            border: 1px solid #fde68a;
            font-size: 11px;
            text-align: center;
        }

        /* ─── QUICK REPLY PILLS (TRAVELOKA STYLE) ─── */
        .quick-reply-pill {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            display: block;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: all 0.15s ease;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .quick-reply-pill:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            box-shadow: 0 3px 6px rgba(0,0,0,0.08);
            transform: translateY(-0.5px);
        }
        .quick-reply-pill:active {
            background: #e2e8f0;
            transform: translateY(0);
        }
        .quick-reply-pill.disabled {
            color: #94a3b8;
            background: #f8fafc;
            border-color: #e2e8f0;
            box-shadow: none;
            cursor: default;
            pointer-events: none;
            opacity: 0.65;
        }

        /* ─── SOLUTION CARD ─── */
        .solution-card {
            background: #fff;
            border: 1.5px solid #bfdbfe;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(59,130,246,0.08);
        }

        /* ─── TYPING INDICATOR ─── */
        .typing-dots span {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #94a3b8;
            animation: typing 1.4s infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* ─── ANIMATIONS ─── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .msg-anim { animation: fadeSlideUp 0.3s ease both; }

        /* ─── PULSE ─── */
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.5} }
        .pulse { animation: pulse 2s infinite; }

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

        /* ─── MARKDOWN-LIKE RENDERING ─── */
        .solution-text strong, .solution-text b { font-weight: 700; }
        .solution-text { white-space: pre-wrap; line-height: 1.65; }
    </style>
</head>
<body>

    <!-- ═══════════ TOP HEADER BAR ═══════════ -->
    <div style="background:#0f172a; border-bottom:1px solid #1e293b; padding:10px 20px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="background: linear-gradient(135deg,#3b82f6,#1d4ed8); width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-bus-simple" style="color:#fff; font-size:16px;"></i>
            </div>
            <div>
                <div style="color:#f8fafc; font-size:15px; font-weight:800; line-height:1.2;">Shuttle System CS Chatbot</div>
                <div style="color:#64748b; font-size:11px; font-weight:500;">Demo Tugas Akhir — Sistem Customer Service Realtime</div>
            </div>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <div id="reverb-indicator" style="background:#1e293b; border:1px solid #334155; border-radius:999px; padding:5px 12px; font-size:11px; font-weight:600; color:#64748b; display:flex; align-items:center; gap:6px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;" class="pulse"></span>
                Reverb: Menghubungkan...
            </div>
            <div style="background:#1e293b; border:1px solid #334155; border-radius:999px; padding:5px 12px; font-size:11px; color:#64748b;">
                <i class="fa-solid fa-database" style="color:#3b82f6; margin-right:4px;"></i> SQLite
            </div>
        </div>
    </div>

    <!-- ═══════════ MAIN WORKSPACE ═══════════ -->
    <div style="flex:1; display:flex; gap:16px; padding:16px; overflow:hidden; min-height:0;">

        <!-- ══════════════════════════════════════
             LEFT — SIMULATED CUSTOMER MOBILE APP
             ══════════════════════════════════════ -->
        <div style="display:flex; flex-direction:column; align-items:center; gap:8px; flex-shrink:0; height:100%; min-height:0;">
            <div style="color:#475569; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em;">
                Aplikasi Customer (Mobile)
            </div>

            <div class="phone-frame">
                <!-- Status Bar -->
                <div style="background:#0f172a; color:#fff; padding:8px 20px; display:flex; justify-content:space-between; align-items:center; font-size:11px; flex-shrink:0; position:relative;">
                    <span style="font-weight:700;">14:30</span>
                    <div style="width:60px;height:14px;background:#0f172a;border-radius:999px;position:absolute;left:50%;transform:translateX(-50%);"></div>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <i class="fa-solid fa-signal" style="font-size:10px;"></i>
                        <i class="fa-solid fa-wifi" style="font-size:10px;"></i>
                        <i class="fa-solid fa-battery-three-quarters" style="font-size:10px;"></i>
                    </div>
                </div>

                <!-- App Header -->
                <div style="background:#fff; padding:10px 14px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f1f5f9; flex-shrink:0; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="position:relative;">
                            <img src="https://api.dicebear.com/7.x/bottts-neutral/svg?seed=shuttle&backgroundColor=b6e3f4" style="width:38px;height:38px;border-radius:50%;border:2px solid #dbeafe;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4712/4712035.png'">
                            <span style="position:absolute;bottom:1px;right:1px;width:9px;height:9px;background:#10b981;border-radius:50%;border:2px solid #fff;" id="client-online-dot"></span>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:800;color:#0f172a;">Virtual Assistant</div>
                            <div id="client-status-text" style="font-size:10px;font-weight:600;color:#10b981;">● Online — Siap Membantu</div>
                        </div>
                    </div>
                    <button id="btn-end-session" onclick="clientEndSession()" style="display:none; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:999px; padding:5px 12px; font-size:11px; font-weight:700; cursor:pointer;">
                        <i class="fa-solid fa-xmark" style="margin-right:3px;"></i>Akhiri
                    </button>
                </div>

                <!-- Chat Body -->
                <div id="client-chat-box" style="flex:1; overflow-y:auto; padding:14px; display:flex; flex-direction:column; gap:12px; background:#f8fafc; min-height:0;">
                </div>

                <!-- Live Chat Input Bar -->
                <div id="client-input-bar" style="display:none; background:#fff; border-top:1px solid #f1f5f9; padding:10px 12px; flex-shrink:0;">
                    <div style="display:flex; align-items:center; gap:8px; background:#f1f5f9; border-radius:999px; padding:6px 14px;">
                        <input id="client-msg-input" type="text" placeholder="Tulis pesan..." style="flex:1; background:transparent; border:none; outline:none; font-size:13px; font-family:inherit; color:#0f172a;"
                            onkeydown="if(event.key==='Enter') clientSendLive()">
                        <button onclick="clientSendLive()" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8); color:#fff; width:32px; height:32px; border-radius:50%; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:transform .15s;" onmousedown="this.style.transform='scale(0.9)'" onmouseup="this.style.transform=''">
                            <i class="fa-solid fa-paper-plane" style="font-size:12px; margin-left:1px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════
             RIGHT — ADMIN CS DASHBOARD PORTAL
             ══════════════════════════════════════ -->
        <div style="flex:1; background:#fff; border-radius:20px; border:1px solid #e2e8f0; display:flex; flex-direction:column; overflow:hidden; min-width:0;">

            <!-- Admin Header -->
            <div style="background:#0f172a; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; border-radius:20px 20px 0 0;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="background:linear-gradient(135deg,#10b981,#059669); width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-headset" style="color:#fff; font-size:14px;"></i>
                    </div>
                    <div>
                        <div style="color:#f8fafc; font-weight:800; font-size:14px;">Portal Admin CS</div>
                        <div style="color:#475569; font-size:11px;">Live Chat Dashboard</div>
                    </div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <div id="admin-queue-badge" style="background:#ef4444; color:#fff; border-radius:999px; padding:3px 10px; font-size:11px; font-weight:800; display:none;">
                        <span id="admin-queue-count">0</span> Antrean Baru
                    </div>
                    <button onclick="refreshAdminQueue()" style="background:#1e293b; border:1px solid #334155; color:#94a3b8; border-radius:8px; padding:6px 12px; font-size:11px; cursor:pointer; font-family:inherit;">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Admin Body -->
            <div style="flex:1; display:flex; overflow:hidden; min-height:0;">

                <!-- Sidebar Queue -->
                <div style="width:260px; flex-shrink:0; border-right:1px solid #f1f5f9; display:flex; flex-direction:column; overflow:hidden;">
                    <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.08em;">Antrean Chat Aktif</span>
                    </div>
                    <div id="admin-session-list" style="flex:1; overflow-y:auto; padding:10px;">
                        <div id="admin-empty-msg" style="text-align:center; padding:32px 16px; color:#94a3b8;">
                            <i class="fa-regular fa-folder-open" style="font-size:36px; display:block; margin-bottom:10px; color:#e2e8f0;"></i>
                            <div style="font-size:12px; font-weight:600;">Belum ada antrean</div>
                            <div style="font-size:11px; margin-top:4px;">Menunggu customer menghubungi admin...</div>
                        </div>
                    </div>
                </div>

                <!-- Chat Console -->
                <div style="flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0;">

                    <!-- Splash (no session selected) -->
                    <div id="admin-splash" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#94a3b8; padding:32px;">
                        <i class="fa-regular fa-comments" style="font-size:64px; margin-bottom:16px; color:#e2e8f0;"></i>
                        <div style="font-size:16px; font-weight:700; color:#64748b;">Pilih Antrean Chat</div>
                        <div style="font-size:13px; margin-top:6px; text-align:center; max-width:300px; line-height:1.6;">Pilih salah satu sesi dari panel kiri untuk mulai membalas chat pelanggan secara realtime.</div>
                    </div>

                    <!-- Active Chat View -->
                    <div id="admin-active-view" style="display:none; flex-direction:column; height:100%;">
                        <!-- Session Info Bar -->
                        <div style="padding:12px 16px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; background:#f8fafc; flex-shrink:0;">
                            <div>
                                <div id="admin-client-name" style="font-size:14px; font-weight:800; color:#0f172a;">—</div>
                                <div id="admin-session-meta" style="font-size:11px; color:#64748b; margin-top:1px;">—</div>
                            </div>
                            <button onclick="adminResolveSession()" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:999px; padding:7px 16px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; transition:all .15s;">
                                <i class="fa-solid fa-circle-check" style="margin-right:4px;"></i>Selesaikan Sesi
                            </button>
                        </div>
                        <!-- Messages -->
                        <div id="admin-chat-box" style="flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px; background:#f8fafc; min-height:0;">
                        </div>
                        <!-- Reply Bar -->
                        <div style="padding:12px 16px; background:#fff; border-top:1px solid #f1f5f9; flex-shrink:0;">
                            <div style="display:flex; gap:10px;">
                                <input id="admin-msg-input" type="text" placeholder="Tulis balasan sebagai Admin CS..." style="flex:1; border:1.5px solid #e2e8f0; border-radius:12px; padding:10px 14px; font-size:13px; outline:none; font-family:inherit; color:#0f172a; transition:border .15s;"
                                    onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"
                                    onkeydown="if(event.key==='Enter') adminSendMessage()">
                                <button onclick="adminSendMessage()" style="background:linear-gradient(135deg,#10b981,#059669); color:#fff; border:none; border-radius:12px; padding:10px 20px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; white-space:nowrap; transition:all .15s;" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform=''">
                                    <i class="fa-solid fa-paper-plane" style="margin-right:5px;"></i>Kirim
                                </button>
                            </div>
                            <div style="margin-top:8px; display:flex; gap:6px; flex-wrap:wrap;">
                                <button onclick="adminQuickReply('Halo! Ada yang bisa kami bantu? 😊')" style="background:#f1f5f9; border:none; border-radius:999px; padding:5px 12px; font-size:11px; color:#475569; cursor:pointer; font-family:inherit;">Halo 👋</button>
                                <button onclick="adminQuickReply('Mohon ditunggu sebentar ya, kami sedang proses.')" style="background:#f1f5f9; border:none; border-radius:999px; padding:5px 12px; font-size:11px; color:#475569; cursor:pointer; font-family:inherit;">Mohon tunggu</button>
                                <button onclick="adminQuickReply('Terima kasih sudah menghubungi kami! Masalah Anda sudah kami catat. 🙏')" style="background:#f1f5f9; border:none; border-radius:999px; padding:5px 12px; font-size:11px; color:#475569; cursor:pointer; font-family:inherit;">Terima kasih</button>
                                <button onclick="adminQuickReply('Masalah sudah kami selesaikan. Ada yang bisa kami bantu lagi?')" style="background:#f1f5f9; border:none; border-radius:999px; padding:5px 12px; font-size:11px; color:#475569; cursor:pointer; font-family:inherit;">Selesai ✅</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div><!-- end workspace -->

    <!-- ═══════════════ JAVASCRIPT ═══════════════ -->
    <script>
    // ─── GLOBALS ─────────────────────────────────────────────
    let devToken = '';
    let clientEcho = null;
    let adminEcho  = null;
    let clientSessionId  = null;
    let activeAdminSessionId = null;

    let categories = [];
    let problems   = [];
    let selectedCategory = null;
    let selectedProblem  = null;

    const WA_NUMBER  = '+62895324354052'; // ← Ganti dengan nomor WA admin Anda
    const WA_MESSAGE = 'Halo Admin, saya butuh bantuan terkait aplikasi Shuttle System.';

    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // ─── INIT ────────────────────────────────────────────────
    window.addEventListener('DOMContentLoaded', () => {
        devLogin();
    });

    function devLogin() {
        axios.post('/api/login-dev')
            .then(r => {
                devToken = r.data.token;
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + devToken;
                initEcho();
                startBot();
            })
            .catch(() => {
                appendBotMsg('<i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;"></i> <b>Gagal terhubung ke API.</b> Pastikan <code>php artisan serve</code> sudah berjalan di terminal.', true);
            });
    }

    // ─── ECHO / REVERB ───────────────────────────────────────
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
            authEndpoint: '/api/broadcasting/auth',
            auth: { headers: { Authorization: 'Bearer ' + devToken } }
        };

        clientEcho = new Echo(echoConfig);
        adminEcho  = new Echo(echoConfig);

        // Monitor connection state
        clientEcho.connector.pusher.connection.bind('connected', () => {
            document.getElementById('reverb-indicator').innerHTML =
                '<span style="width:7px;height:7px;border-radius:50%;background:#10b981;"></span> Reverb: Terhubung ✅';
            document.getElementById('reverb-indicator').style.color = '#10b981';
        });
        clientEcho.connector.pusher.connection.bind('disconnected', () => {
            document.getElementById('reverb-indicator').innerHTML =
                '<span style="width:7px;height:7px;border-radius:50%;background:#ef4444;" class="pulse"></span> Reverb: Terputus';
            document.getElementById('reverb-indicator').style.color = '#ef4444';
        });

        // Admin listens to new session events
        adminEcho.private('admin.chat')
            .listen('SessionStatusChanged', () => { refreshAdminQueue(); });

        refreshAdminQueue();
    }

    // ─── BOT FLOW ────────────────────────────────────────────
    function startBot() {
        clearClientChat();
        clientSessionId  = null;
        selectedCategory = null;
        selectedProblem  = null;

        document.getElementById('client-input-bar').style.display = 'none';
        document.getElementById('btn-end-session').style.display  = 'none';
        document.getElementById('client-status-text').textContent = '● Online — Siap Membantu';
        document.getElementById('client-status-text').style.color = '#10b981';

        appendBotMsg('Halo! Selamat datang di <b>Customer Service Shuttle System</b> 👋<br><br>Saya adalah asisten virtual Anda. Saya siap membantu menyelesaikan kendala seputar layanan shuttle Anda!');

        showTyping(() => {
            appendBotMsg('Silakan pilih <b>kategori bantuan</b> yang sesuai dengan kendala Anda:');
            loadCategories();
        }, 900);
    }

    function loadCategories() {
        axios.get('/api/chatbot/categories')
            .then(r => {
                categories = r.data;
                renderCategoryList(categories);
            })
            .catch(() => appendBotMsg('⚠️ Gagal memuat kategori. Cek koneksi ke server.'));
    }

    function renderCategoryList(cats) {
        const container = document.createElement('div');
        container.id = 'category-options';
        container.style.cssText = 'display:flex; flex-direction:column; gap:2px; width:100%;';

        cats.forEach(cat => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-pill msg-anim';
            btn.textContent = cat.name;
            btn.onclick = () => selectCategory(cat);
            container.appendChild(btn);
        });

        const wrapper = document.createElement('div');
        wrapper.className = 'msg-anim';
        wrapper.appendChild(container);
        document.getElementById('client-chat-box').appendChild(wrapper);
        scrollClientChat();
        // Extra scroll guarantee after category pills animate in
        setTimeout(() => {
            const box = document.getElementById('client-chat-box');
            box.scrollTop = box.scrollHeight;
        }, 500);
    }

    function selectCategory(cat) {
        selectedCategory = cat;
        appendUserMsg(cat.name);
        disableOptions('category-options');

        showTyping(() => {
            appendBotMsg(`Baik! Saya akan membantu untuk kategori <b>"${cat.name}"</b>.<br>Pilih masalah yang paling sesuai:`);
            axios.get(`/api/chatbot/problems?category_id=${cat.id}`)
                .then(r => {
                    problems = r.data;
                    if (!problems.length) {
                        appendBotMsg('Maaf, belum ada data masalah untuk kategori ini.');
                        showRestartOption();
                        return;
                    }
                    renderProblemList(problems);
                })
                .catch(() => appendBotMsg('⚠️ Gagal memuat data. Silakan coba lagi.'));
        }, 700);
    }

    function renderProblemList(probs) {
        const container = document.createElement('div');
        container.id = 'problem-options';
        container.style.cssText = 'display:flex; flex-direction:column; gap:2px; width:100%;';

        probs.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-pill msg-anim';
            btn.style.animationDelay = '0ms';
            btn.textContent = p.title;
            btn.onclick = () => selectProblem(p);
            container.appendChild(btn);
        });

        const wrapper = document.createElement('div');
        wrapper.className = 'msg-anim';
        wrapper.appendChild(container);
        document.getElementById('client-chat-box').appendChild(wrapper);
        scrollClientChat();
        // Extra scroll to guarantee visibility after animation completes
        setTimeout(() => {
            const box = document.getElementById('client-chat-box');
            box.scrollTop = box.scrollHeight;
        }, 500);
    }

    function selectProblem(p) {
        selectedProblem = p;
        appendUserMsg(p.title);
        disableOptions('problem-options');

        showTyping(() => {
            renderSolutionCard(p);
            showTyping(() => {
                renderFeedbackCard();
            }, 600);
        }, 900);
    }

    function renderSolutionCard(p) {
        const card = document.createElement('div');
        card.className = 'solution-card msg-anim';
        card.innerHTML = `
            <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe); padding:10px 14px; display:flex; align-items:center; gap:8px; border-bottom:1.5px solid #bfdbfe;">
                <i class="fa-regular fa-lightbulb" style="color:#2563eb; font-size:15px;"></i>
                <span style="font-size:12px; font-weight:800; color:#1e40af;">Solusi Rekomendasi</span>
            </div>
            <div style="padding:12px 14px;">
                <div class="solution-text" style="font-size:12.5px; color:#1e293b;">${renderMarkdown(p.solution_text)}</div>
            </div>
        `;
        document.getElementById('client-chat-box').appendChild(card);
        scrollClientChat();
    }

    function renderFeedbackCard() {
        appendBotMsg("apakah anda sudah puas dengan jawaban ini ?");

        const container = document.createElement('div');
        container.id = 'feedback-options';
        container.style.cssText = 'display:flex; flex-direction:column; gap:2px; width:100%;';

        const btnYes = document.createElement('button');
        btnYes.className = 'quick-reply-pill msg-anim';
        btnYes.textContent = "Ya, saya sudah puas";
        btnYes.onclick = () => handleFeedback('yes');

        const btnNo = document.createElement('button');
        btnNo.className = 'quick-reply-pill msg-anim';
        btnNo.textContent = "Belum puas";
        btnNo.onclick = () => handleFeedback('no');

        container.appendChild(btnYes);
        container.appendChild(btnNo);

        const wrapper = document.createElement('div');
        wrapper.className = 'msg-anim';
        wrapper.appendChild(container);
        document.getElementById('client-chat-box').appendChild(wrapper);
        scrollClientChat();
    }

    function handleFeedback(choice) {
        disableOptions('feedback-options');

        if (choice === 'yes') {
            appendUserMsg('Ya, saya sudah puas ✅');
            showTyping(() => {
                appendBotMsg('Alhamdulillah, senang bisa membantu Anda! Terima kasih telah menggunakan layanan CS Shuttle System. 😊');
                showTyping(() => {
                    appendBotMsg('Apa ada hal yang lain bisa kami bantu?');
                    loadCategories();
                }, 800);
            }, 700);
        } else {
            appendUserMsg('Belum puas ❌');
            showTyping(() => {
                appendBotMsg('Mohon maaf asisten belum dapat menyelesaikan kendala Anda secara otomatis. 🙏');
                showTyping(() => {
                    renderEscalationOptions();
                }, 800);
            }, 700);
        }
    }

    function renderEscalationOptions() {
        const container = document.createElement('div');
        container.id = 'escalation-options';
        container.style.cssText = 'display:flex; flex-direction:column; gap:2px; width:100%;';

        appendBotMsg('Anda dapat menghubungkan langsung ke Admin CS atau menghubungi kami via WhatsApp:');

        // Live Chat with Admin button
        const liveBtn = document.createElement('button');
        liveBtn.className = 'quick-reply-pill msg-anim';
        liveBtn.textContent = 'Hubungkan ke Admin CS (Live Chat)';
        liveBtn.onclick = () => connectToAdmin();

        // WhatsApp button
        const waBtn = document.createElement('button');
        waBtn.className = 'quick-reply-pill msg-anim';
        waBtn.textContent = 'Hubungi via WhatsApp';
        waBtn.onclick = () => {
            const waUrl = `https://wa.me/${WA_NUMBER.replace(/\D/g,'')}?text=${encodeURIComponent(WA_MESSAGE)}`;
            window.open(waUrl, '_blank');
        };

        // Back to menu
        const backBtn = document.createElement('button');
        backBtn.className = 'quick-reply-pill msg-anim';
        backBtn.textContent = 'Kembali ke Menu Utama';
        backBtn.onclick = () => startBot();

        container.appendChild(liveBtn);
        container.appendChild(waBtn);
        container.appendChild(backBtn);

        const wrapper = document.createElement('div');
        wrapper.className = 'msg-anim';
        wrapper.appendChild(container);
        document.getElementById('client-chat-box').appendChild(wrapper);
        scrollClientChat();

        // Display operational hours below choices
        showTyping(() => {
            appendBotMsg(`💬 <b>WhatsApp CS Shuttle System</b>: <b>${WA_NUMBER}</b><br>Jam operasional: <b>08.00 – 21.00 WIB</b>`);
        }, 650);
    }

    function showRestartOption() {
        const container = document.createElement('div');
        container.id = 'restart-options';
        container.style.cssText = 'display:flex; flex-direction:column; gap:2px; width:100%;';

        const homeBtn = document.createElement('button');
        homeBtn.className = 'quick-reply-pill msg-anim';
        homeBtn.textContent = 'Kembali ke Menu Utama';
        homeBtn.onclick = () => startBot();

        container.appendChild(homeBtn);

        const wrapper = document.createElement('div');
        wrapper.className = 'msg-anim';
        wrapper.appendChild(container);
        document.getElementById('client-chat-box').appendChild(wrapper);
        scrollClientChat();
    }

    // ─── LIVE CHAT ───────────────────────────────────────────
    function connectToAdmin() {
        const opts = document.getElementById('escalation-options');
        if (opts) opts.remove();

        appendUserMsg('Chat Langsung dengan Admin CS');

        showTyping(() => {
            appendBotMsg('<i class="fa-solid fa-circle-notch fa-spin" style="color:#3b82f6;margin-right:6px;"></i>Sedang menghubungkan ke Admin CS... Mohon tunggu sebentar.');

            axios.post('/api/chat/connect-admin', {
                last_category_id: selectedCategory ? selectedCategory.id : null,
                last_problem_id:  selectedProblem  ? selectedProblem.id  : null,
            })
            .then(r => {
                clientSessionId = r.data.session.id;

                document.getElementById('client-input-bar').style.display = 'block';
                document.getElementById('btn-end-session').style.display  = 'inline-block';
                document.getElementById('client-status-text').innerHTML   = '<span style="color:#10b981;">● Terhubung ke Admin CS</span>';

                appendSystemMsg('🎧 Anda terhubung dengan Admin CS. Silakan ceritakan masalah Anda secara lengkap.');

                // Listen for admin replies
                clientEcho.private(`chat.${clientSessionId}`)
                    .listen('MessageSent', e => {
                        if (e.sender_type !== 'user') {
                            appendAdminMsg(e.message_content);
                        }
                    })
                    .listen('SessionStatusChanged', e => {
                        if (e.status === 'resolved') {
                            handleSessionResolvedByAdmin();
                        }
                    });

                refreshAdminQueue();
            })
            .catch(() => {
                appendBotMsg('⚠️ Gagal menghubungkan ke admin. Silakan coba lagi atau hubungi via WhatsApp.');
            });
        }, 800);
    }

    function clientSendLive() {
        const input = document.getElementById('client-msg-input');
        const msg = input.value.trim();
        if (!msg || !clientSessionId) return;
        input.value = '';

        appendUserMsg(msg);
        axios.post(`/api/chat/${clientSessionId}/messages`, { message: msg })
            .catch(() => appendBotMsg('⚠️ Pesan gagal terkirim.'));
    }

    function clientEndSession() {
        if (!clientSessionId) return;
        axios.post(`/api/chat/${clientSessionId}/resolve`).then(() => {
            handleSessionEnd();
        });
    }

    function handleSessionResolvedByAdmin() {
        appendSystemMsg('✅ Admin CS telah menandai sesi ini selesai.');
        handleSessionEnd();
    }

    function handleSessionEnd() {
        clientEcho && clientSessionId && clientEcho.leave(`chat.${clientSessionId}`);
        clientSessionId = null;

        document.getElementById('client-input-bar').style.display = 'none';
        document.getElementById('btn-end-session').style.display  = 'none';
        document.getElementById('client-status-text').innerHTML   = '● Online — Siap Membantu';
        document.getElementById('client-status-text').style.color = '#10b981';

        appendBotMsg('Terima kasih sudah menghubungi kami! Sesi Live Chat telah selesai. Semoga masalah Anda terselesaikan 🙏');
        showRestartOption();
        refreshAdminQueue();
    }

    // ─── ADMIN PANEL ─────────────────────────────────────────
    function refreshAdminQueue() {
        axios.get('/admin/active-sessions')
            .then(r => {
                const sessions = r.data;
                const list  = document.getElementById('admin-session-list');
                const empty = document.getElementById('admin-empty-msg');
                const badge = document.getElementById('admin-queue-badge');

                list.innerHTML = '';

                if (!sessions.length) {
                    empty.style.display = 'block';
                    list.appendChild(empty);
                    badge.style.display = 'none';
                    return;
                }

                badge.style.display = 'block';
                document.getElementById('admin-queue-count').textContent = sessions.length;

                sessions.forEach(s => {
                    const isActive = s.id === activeAdminSessionId;
                    const item = document.createElement('div');
                    item.className = 'admin-session-item' + (isActive ? ' active' : '');
                    const time = new Date(s.created_at).toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
                    item.innerHTML = `
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:4px;">
                            <span style="font-size:13px;font-weight:800;color:#0f172a;">${s.user ? s.user.name : 'Pelanggan'}</span>
                            <span style="font-size:10px;color:#94a3b8;">${time}</span>
                        </div>
                        <div style="font-size:11px;color:#64748b;margin-bottom:6px;">
                            <i class="fa-solid fa-tag" style="font-size:9px;margin-right:3px;color:#94a3b8;"></i>
                            ${s.last_category ? s.last_category.name : 'Umum'}
                        </div>
                        <div style="display:flex;gap:5px;align-items:center;">
                            <span style="background:#dcfce7;color:#166534;border-radius:999px;padding:2px 8px;font-size:10px;font-weight:700;">● Aktif</span>
                            <span style="color:#94a3b8;font-size:10px;margin-left:auto;">Klik untuk buka</span>
                        </div>
                    `;
                    item.onclick = () => openAdminSession(s);
                    list.appendChild(item);
                });
            });
    }

    function openAdminSession(session) {
        // Leave previous channel
        if (activeAdminSessionId && adminEcho) {
            adminEcho.leave(`chat.${activeAdminSessionId}`);
        }
        activeAdminSessionId = session.id;

        document.getElementById('admin-splash').style.display       = 'none';
        document.getElementById('admin-active-view').style.display  = 'flex';
        document.getElementById('admin-client-name').textContent    = session.user ? session.user.name : 'Pelanggan';
        document.getElementById('admin-session-meta').textContent   = `Sesi ID: ${session.id.substring(0,8)}... | Kategori: ${session.last_category ? session.last_category.name : 'Umum'}`;

        // Load message history
        axios.get(`/api/chat/${session.id}/history`)
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

        // Subscribe Reverb for new messages
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
                        document.getElementById('admin-splash').style.display      = 'flex';
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

        axios.post(`/api/chat/${activeAdminSessionId}/messages`, { message: msg })
            .catch(() => alert('Gagal mengirim pesan.'));
    }

    // Quick replies helper
    function adminQuickReply(text) {
        document.getElementById('admin-msg-input').value = text;
        document.getElementById('admin-msg-input').focus();
    }

    function adminResolveSession() {
        if (!activeAdminSessionId) return;
        axios.post(`/api/chat/${activeAdminSessionId}/resolve`).then(() => {
            adminEcho && adminEcho.leave(`chat.${activeAdminSessionId}`);
            activeAdminSessionId = null;
            document.getElementById('admin-active-view').style.display = 'none';
            document.getElementById('admin-splash').style.display      = 'flex';
            refreshAdminQueue();
        });
    }

    // ─── RENDER HELPERS ──────────────────────────────────────
    function renderMarkdown(text) {
        if (!text) return '';
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
    }

    function appendBotMsg(html, isError = false) {
        const wrap = document.createElement('div');
        wrap.className = 'msg-anim';
        wrap.style.cssText = 'display:flex; align-items:flex-start; gap:8px; max-width:90%;';
        wrap.innerHTML = `
            <img src="https://api.dicebear.com/7.x/bottts-neutral/svg?seed=shuttle&backgroundColor=b6e3f4" style="width:26px;height:26px;border-radius:50%;border:1px solid #dbeafe;flex-shrink:0;margin-top:2px;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4712/4712035.png'">
            <div class="bubble-bot" style="padding:10px 13px; font-size:12.5px; line-height:1.6; ${isError ? 'background:#fef2f2;border-color:#fecaca;' : ''}">${html}</div>
        `;
        document.getElementById('client-chat-box').appendChild(wrap);
        scrollClientChat();
    }

    function appendUserMsg(text) {
        const wrap = document.createElement('div');
        wrap.className = 'msg-anim';
        wrap.style.cssText = 'display:flex; justify-content:flex-end; max-width:90%; align-self:flex-end;';
        wrap.innerHTML = `<div class="bubble-user" style="padding:9px 13px; font-size:12.5px; line-height:1.5;">${text}</div>`;
        document.getElementById('client-chat-box').appendChild(wrap);
        scrollClientChat();
    }

    function appendAdminMsg(text) {
        const wrap = document.createElement('div');
        wrap.className = 'msg-anim';
        wrap.style.cssText = 'display:flex; align-items:flex-start; gap:8px; max-width:90%;';
        wrap.innerHTML = `
            <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-headset" style="color:#fff;font-size:11px;"></i>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#059669;margin-bottom:3px;">Admin CS</div>
                <div class="bubble-admin" style="padding:9px 13px;font-size:12.5px;line-height:1.5;">${text}</div>
            </div>
        `;
        document.getElementById('client-chat-box').appendChild(wrap);
        scrollClientChat();
    }

    function appendSystemMsg(text) {
        const el = document.createElement('div');
        el.className = 'bubble-system msg-anim';
        el.style.cssText = 'padding:7px 12px; font-size:11px; text-align:center; align-self:center; max-width:85%;';
        el.innerHTML = text;
        document.getElementById('client-chat-box').appendChild(el);
        scrollClientChat();
    }

    // Admin Console Appenders
    function appendAdminBoxUserMsg(text) {
        const el = document.createElement('div');
        el.className = 'msg-anim';
        el.style.cssText = 'display:flex; align-items:flex-end; gap:8px; max-width:75%;';
        el.innerHTML = `
            <div style="width:26px;height:26px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-user" style="color:#64748b;font-size:11px;"></i>
            </div>
            <div style="background:#f1f5f9; color:#1e293b; border-radius:4px 14px 14px 14px; padding:9px 13px; font-size:13px; line-height:1.5;">${text}</div>
        `;
        document.getElementById('admin-chat-box').appendChild(el);
    }

    function appendAdminBoxAdminMsg(text, isBot = false) {
        const el = document.createElement('div');
        el.className = 'msg-anim';
        el.style.cssText = 'display:flex; justify-content:flex-end; align-self:flex-end; max-width:75%;';
        const bg = isBot
            ? 'background:linear-gradient(135deg,#3b82f6,#1d4ed8);'
            : 'background:linear-gradient(135deg,#10b981,#059669);';
        const label = isBot ? 'Bot' : 'Admin CS (Anda)';
        el.innerHTML = `
            <div style="text-align:right;">
                <div style="font-size:10px; font-weight:700; color:#64748b; margin-bottom:3px;">${label}</div>
                <div style="${bg} color:#fff; border-radius:14px 4px 14px 14px; padding:9px 13px; font-size:13px; line-height:1.5;">${text}</div>
            </div>
        `;
        document.getElementById('admin-chat-box').appendChild(el);
    }

    function appendAdminSystemMsg(text) {
        const el = document.createElement('div');
        el.className = 'bubble-system msg-anim';
        el.style.cssText = 'padding:6px 12px; font-size:11px; text-align:center; align-self:center;';
        el.textContent = text;
        document.getElementById('admin-chat-box').appendChild(el);
    }

    // ─── TYPING INDICATOR ────────────────────────────────────
    function showTyping(callback, delay = 800) {
        const indicator = document.createElement('div');
        indicator.id = 'typing-' + Date.now() + Math.random().toString(36).slice(2);
        indicator.className = 'msg-anim';
        indicator.style.cssText = 'display:flex; align-items:center; gap:8px;';
        indicator.innerHTML = `
            <img src="https://api.dicebear.com/7.x/bottts-neutral/svg?seed=shuttle&backgroundColor=b6e3f4" style="width:26px;height:26px;border-radius:50%;border:1px solid #dbeafe;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4712/4712035.png'">
            <div class="bubble-bot" style="padding:10px 16px;">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        const id = indicator.id;
        document.getElementById('client-chat-box').appendChild(indicator);
        scrollClientChat();
        setTimeout(() => {
            const el = document.getElementById(id);
            if (el) el.remove();
            callback();
            // Scroll again after callback adds new content
            scrollClientChat();
        }, delay);
    }

    // ─── UTILITIES ───────────────────────────────────────────
    function disableGrid() {
        const grid = document.getElementById('category-grid');
        if (grid) {
            grid.querySelectorAll('button').forEach(b => {
                b.onclick = null;
                b.style.opacity = '0.45';
                b.style.cursor  = 'default';
                b.style.pointerEvents = 'none';
            });
        }
    }

    function disableOptions(id) {
        const el = document.getElementById(id);
        if (el) {
            el.querySelectorAll('button').forEach(b => {
                b.onclick = null;
                b.classList.add('disabled');
            });
        }
    }

    function clearClientChat() {
        document.getElementById('client-chat-box').innerHTML = '';
    }

    function scrollClientChat() {
        const box = document.getElementById('client-chat-box');
        // Delay must be longer than fadeSlideUp animation (300ms)
        setTimeout(() => {
            box.scrollTop = box.scrollHeight;
        }, 100);
        setTimeout(() => {
            box.scrollTop = box.scrollHeight;
        }, 420);
    }

    function scrollAdminChat() {
        const box = document.getElementById('admin-chat-box');
        setTimeout(() => { box.scrollTop = box.scrollHeight; }, 80);
    }
    </script>

</body>
</html>
