<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'MediConnect') — Telemedicine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:  '#E6F1FB', 100: '#B5D4F4', 200: '#85B7EB',
                            400: '#378ADD', 600: '#185FA5', 800: '#0C447C', 900: '#042C53',
                        }
                    },
                    screens: {
                        'xs': '475px',
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root { --sidebar-w: 220px; }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 10px;
            font-size: 13px; color: rgba(255,255,255,0.55);
            text-decoration: none; transition: all 0.15s;
        }
        .nav-link:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.9); }
        .nav-link.active { background: #185FA5; color: #fff; font-weight: 500; }
        .nav-link svg { flex-shrink: 0; opacity: 0.75; }
        .nav-link.active svg { opacity: 1; }

        #sidebar-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 40; backdrop-filter: blur(2px);
        }
        #sidebar-overlay.show { display: block; }

        @media (max-width: 1023px) {
            #main-sidebar {
                position: fixed; left: -100%; top: 0; bottom: 0; z-index: 50;
                transition: left 0.25s cubic-bezier(0.4,0,0.2,1);
            }
            #main-sidebar.open { left: 0; }
        }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }

        #offline-banner {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            color: white; font-size: 12px; font-weight: 500;
            text-align: center; padding: 7px;
            position: sticky; top: 0; z-index: 100;
            display: none;
        }
        #offline-banner.show { display: block; }

        main { animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }

        #bottom-nav { display: none; }
        @media (max-width: 767px) {
            #bottom-nav { display: flex; }
            #main-content { padding-bottom: 80px !important; }
        }
    </style>
    <link rel="manifest" href="/manifest.json"/>
    @yield('head')
</head>
<body class="bg-slate-100 antialiased">

{{-- Offline Banner --}}
<div id="offline-banner">⚠ Anda sedang offline — data akan disinkronisasi saat koneksi kembali</div>

{{-- Mobile Overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside id="main-sidebar"
        class="w-[220px] flex-shrink-0 bg-brand-900 flex flex-col h-full overflow-y-auto shadow-xl">

        {{-- Logo --}}
        <div class="px-5 pt-6 pb-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold text-base flex-shrink-0">M</div>
                <div>
                    <div class="text-white font-bold text-[15px] leading-tight">MediConnect</div>
                    <div class="text-white/35 text-[10px] uppercase tracking-widest mt-0.5">Telemedicine</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-6">

            {{-- Utama --}}
            <div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-3 mb-2">Utama</div>
                <div class="space-y-0.5">
                    <a href="/pasien" id="nav-dashboard" class="nav-link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/>
                            <rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="/konsultasi" class="nav-link @yield('nav_konsultasi')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Konsultasi
                    </a>
                </div>
            </div>

            {{-- Dokter --}}
            <div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-3 mb-2">Dokter</div>
                <div class="space-y-0.5">
                    <a href="/jadwal" class="nav-link @yield('nav_jadwal')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        Jadwal Saya
                    </a>
                    <a href="/tim" class="nav-link @yield('nav_tim')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Jadwal Dokter Lainnya
                    </a>
                </div>
            </div>

            {{-- Pasien --}}
            <div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-3 mb-2">Pasien</div>
                <div class="space-y-0.5">
                    <a href="/konsultasi/baru" class="nav-link @yield('nav_new')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                        </svg>
                        Konsultasi Baru
                    </a>
                    <a href="/riwayat" class="nav-link @yield('nav_riwayat')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Riwayat
                    </a>
                </div>
            </div>

            {{-- Admin (disembunyikan, dishow via JS jika role admin) --}}
            <div id="nav-admin-section" class="hidden">
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-3 mb-2">Admin</div>
                <div class="space-y-0.5">
                    <a href="/admin" class="nav-link @yield('nav_admin')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Admin Panel
                    </a>
                    <a href="/admin/dokter/tambah" class="nav-link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                        Kelola Dokter
                    </a>
                </div>
            </div>
        </nav>

        {{-- User Footer --}}
        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-2.5">
                <div id="nav-avatar" class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">?</div>
                <div class="flex-1 min-w-0">
                    <div id="nav-name" class="text-white/90 text-[12px] font-medium truncate">—</div>
                    <div id="nav-role" class="text-white/40 text-[10px] mt-0.5 capitalize">—</div>
                </div>
                <button onclick="logout()" title="Keluar"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-white/40 hover:text-white/80 hover:bg-white/10 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    {{-- ══════════════ MAIN ══════════════ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top Header --}}
        <header class="bg-white border-b border-slate-200/80 h-14 flex items-center px-4 sm:px-6 gap-3 flex-shrink-0">

            {{-- Hamburger (mobile) --}}
            <button onclick="toggleSidebar()"
                class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <div class="flex-1 min-w-0">
                <h1 class="text-[14px] sm:text-[15px] font-semibold text-slate-800 truncate">@yield('page_title', 'Dashboard')</h1>
                <p class="text-[11px] text-slate-400 hidden sm:block">@yield('page_sub', '')</p>
            </div>

            {{-- Status + Actions --}}
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 rounded-full text-[11px] font-semibold text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live</span>
                </div>
                <a href="/konsultasi/baru"
                    class="hidden sm:flex items-center gap-1.5 bg-brand-600 hover:bg-brand-800 text-white text-[12px] font-medium px-3 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                    </svg>
                    <span>Konsultasi</span>
                </a>
                <button onclick="logout()"
                    class="hidden sm:flex items-center gap-1 text-slate-500 hover:text-red-500 border border-slate-200 hover:border-red-200 text-[11px] px-2.5 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>

                {{-- Mobile avatar --}}
                <div id="mobile-avatar"
                    class="lg:hidden w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold">?</div>
            </div>
        </header>

        {{-- Content --}}
        <main id="main-content" class="flex-1 overflow-y-auto p-4 sm:p-5 lg:p-6 bg-slate-50">
            @yield('content')
        </main>
    </div>
</div>

{{-- ══ MOBILE BOTTOM NAV ══ --}}
<nav id="bottom-nav"
    class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-30 items-center justify-around px-2 py-2">
    <a href="/pasien" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>
        <span class="text-[10px] font-medium">Home</span>
    </a>
    <a href="/jadwal" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        <span class="text-[10px] font-medium">Jadwal</span>
    </a>
    <a href="/konsultasi/baru" class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl bg-brand-600 text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
        </svg>
        <span class="text-[10px] font-semibold">Baru</span>
    </a>
    <a href="/riwayat" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        <span class="text-[10px] font-medium">Riwayat</span>
    </a>
    <a href="/tim" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <span class="text-[10px] font-medium">Tim</span>
    </a>
</nav>

<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');

    // ─────────────────────────────────────────────────────────────
    // 1. Simpan referensi fetch asli SEBELUM apapun menggunakannya
    // ─────────────────────────────────────────────────────────────
    const originalFetch = window.fetch;

    // ─────────────────────────────────────────────────────────────
    // 2. Idle Timeout
    //    Harus sinkron dengan IDLE_MINUTES di CheckTokenExpiration.php
    // ─────────────────────────────────────────────────────────────
    const IDLE_TIMEOUT_MS = 30 * 60 * 1000; // 30 menit
    const WARN_BEFORE_MS  =  2 * 60 * 1000; //  2 menit sebelum timeout

    var idleTimer    = null;
    var warnTimer    = null;
    var warnBannerEl = null;
    var sessionEnded = false; // guard agar handleSessionExpired tidak dipanggil 2x

    function resetIdleTimer() {
        if (sessionEnded) return;
        clearTimeout(idleTimer);
        clearTimeout(warnTimer);
        hideWarnBanner();

        warnTimer = setTimeout(showWarnBanner,    IDLE_TIMEOUT_MS - WARN_BEFORE_MS);
        idleTimer = setTimeout(handleSessionExpired, IDLE_TIMEOUT_MS);
    }

    function showWarnBanner() {
        if (sessionEnded) return;
        if (!warnBannerEl) {
            warnBannerEl = document.createElement('div');
            warnBannerEl.id = 'idle-warn-banner';
            warnBannerEl.style.cssText = [
                'position:fixed;top:0;left:0;right:0',
                'background:#f59e0b;color:white',
                'font-size:13px;font-weight:600',
                'text-align:center;padding:10px 16px',
                'z-index:9998',
                'display:flex;align-items:center;justify-content:center;gap:12px',
            ].join(';');
            warnBannerEl.innerHTML =
                '⚠️ Sesi Anda akan berakhir dalam 2 menit karena tidak aktif. ' +
                '<button onclick="keepAlive()" style="background:white;color:#92400e;' +
                'border:none;border-radius:6px;padding:4px 12px;font-size:12px;' +
                'font-weight:700;cursor:pointer;">Tetap Login</button>';
            document.body.prepend(warnBannerEl);
        }
        warnBannerEl.style.display = 'flex';
    }

    function hideWarnBanner() {
        if (warnBannerEl) warnBannerEl.style.display = 'none';
    }

    // Ping server → perpanjang token dari sisi server juga
    async function keepAlive() {
        var t = localStorage.getItem('auth_token');
        if (!t) return;
        try {
            await originalFetch('/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + t, 'Accept': 'application/json' }
            });
        } catch(e) { /* offline — timer sudah di-reset di bawah */ }
        resetIdleTimer();
    }

    // Reset timer pada setiap interaksi pengguna (tanpa ping server)
    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function(evt) {
        document.addEventListener(evt, resetIdleTimer, { passive: true });
    });

    // ─────────────────────────────────────────────────────────────
    // 3. Interceptor fetch — tangkap 401 dari server
    // ─────────────────────────────────────────────────────────────
    window.fetch = async function(...args) {
        let response;
        try {
            response = await originalFetch(...args);
        } catch(e) {
            throw e;
        }

        if (response.status === 401) {
            const clone = response.clone();
            try {
                const data = await clone.json();
                if (
                    data.message === 'Sesi telah berakhir, silakan login kembali.' ||
                    data.message === 'Unauthenticated.'
                ) {
                    handleSessionExpired();
                    return response;
                }
            } catch(e) {
                handleSessionExpired();
                return response;
            }
        }

        return response;
    };

    // ─────────────────────────────────────────────────────────────
    // 4. Handler sesi berakhir (dipanggil oleh timer ATAU interceptor)
    // ─────────────────────────────────────────────────────────────
    function handleSessionExpired() {
        if (sessionEnded) return; // cegah double call
        sessionEnded = true;

        clearTimeout(idleTimer);
        clearTimeout(warnTimer);
        if (warnBannerEl) warnBannerEl.style.display = 'none';

        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');

        // Tampilkan notifikasi
        var existing = document.getElementById('session-expired-notif');
        if (existing) existing.remove();

        var notif = document.createElement('div');
        notif.id = 'session-expired-notif';
        notif.style.cssText = [
            'position:fixed;top:20px;left:50%;transform:translateX(-50%)',
            'background:#ef4444;color:white',
            'padding:14px 24px;border-radius:12px',
            'font-size:14px;font-weight:600',
            'z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,0.3)',
            'text-align:center;white-space:nowrap',
        ].join(';');
        notif.innerHTML = '⏱️ Sesi berakhir karena tidak aktif.<br>Mengalihkan ke halaman login...';
        document.body.appendChild(notif);

        setTimeout(function() { window.location.href = '/login'; }, 2000);
    }

    // ─────────────────────────────────────────────────────────────
    // 5. Setup navbar user info
    // ─────────────────────────────────────────────────────────────
    if (user) {
        var initials = user.name
            .split(' ')
            .map(function(w) { return w[0] || ''; })
            .join('')
            .substring(0, 2)
            .toUpperCase();

        var navAvatar   = document.getElementById('nav-avatar');
        var mobileAvat  = document.getElementById('mobile-avatar');
        var navName     = document.getElementById('nav-name');
        var navRole     = document.getElementById('nav-role');
        var navDashLink = document.getElementById('nav-dashboard');

        if (navAvatar)   navAvatar.textContent  = initials;
        if (mobileAvat)  mobileAvat.textContent  = initials;
        if (navName)     navName.textContent      = user.name;
        if (navRole)     navRole.textContent      = user.role;

        // Ubah link dashboard sesuai role
        if (navDashLink && (user.role === 'dokter' || user.role === 'admin')) {
            navDashLink.href = '/dokter';
        }

        // Tampilkan menu admin
        if (user.role === 'admin') {
            var adminSection = document.getElementById('nav-admin-section');
            if (adminSection) adminSection.classList.remove('hidden');
        }
    }

    // ─────────────────────────────────────────────────────────────
    // 6. Active nav highlight
    // ─────────────────────────────────────────────────────────────
    var path = window.location.pathname;

    document.querySelectorAll('.nav-link').forEach(function(el) {
        if (el.getAttribute('href') === path) el.classList.add('active');
    });

    document.querySelectorAll('#bottom-nav a').forEach(function(el) {
        if (el.getAttribute('href') === path) {
            el.classList.add('text-brand-600');
            el.classList.remove('text-slate-400');
        }
    });

    // ─────────────────────────────────────────────────────────────
    // 7. Sidebar toggle
    // ─────────────────────────────────────────────────────────────
    function toggleSidebar() {
        document.getElementById('main-sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('main-sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }

    // ─────────────────────────────────────────────────────────────
    // 8. Offline banner
    // ─────────────────────────────────────────────────────────────
    function updateOnlineStatus() {
        var b = document.getElementById('offline-banner');
        if (!b) return;
        navigator.onLine ? b.classList.remove('show') : b.classList.add('show');
    }
    window.addEventListener('online',  updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    // ─────────────────────────────────────────────────────────────
    // 9. Logout
    // ─────────────────────────────────────────────────────────────
    async function logout() {
        var t = localStorage.getItem('auth_token');
        if (t) {
            try {
                await originalFetch('/api/auth/logout', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + t, 'Accept': 'application/json' }
                });
            } catch(e) { /* abaikan error jaringan */ }
        }
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        window.location.href = '/login';
    }

    // ─────────────────────────────────────────────────────────────
    // 10. Service Worker
    // ─────────────────────────────────────────────────────────────
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(console.error);
        navigator.serviceWorker.addEventListener('message', function(e) {
            if (e.data.type === 'SYNC_SUCCESS' && typeof renderUI === 'function') renderUI();
        });
    }

    // ─────────────────────────────────────────────────────────────
    // 11. Mulai idle timer (hanya jika user sudah login)
    // ─────────────────────────────────────────────────────────────
    if (token && user) {
        resetIdleTimer();
    }
</script>

@yield('scripts')
</body>
</html>