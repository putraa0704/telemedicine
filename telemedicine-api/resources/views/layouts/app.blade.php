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
                    screens: { 'xs': '475px' }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 10px;
            font-size: 13px; color: rgba(255,255,255,0.55);
            text-decoration: none; transition: all 0.15s;
            width: 100%;
            min-width: 0;
        }
        .nav-link:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.9); }
        .nav-link.active { background: #185FA5; color: #fff; font-weight: 500; }
        .nav-link svg { flex-shrink: 0; opacity: 0.75; }
        .nav-link.active svg { opacity: 1; }
        .nav-link-label {
            flex: 1;
            min-width: 0;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #nav-pasien {
            width: 100%;
            min-width: 0;
            overflow-x: hidden;
        }
        #nav-pasien > div {
            width: 100%;
            min-width: 0;
        }

        .nav-section-label {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.25);
            padding: 0 12px; margin-bottom: 4px;
        }

        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 40;
            backdrop-filter: blur(2px);
        }
        #sidebar-overlay.show { display: block; }

        @media (max-width: 1023px) {
            #main-sidebar {
                position: fixed; left: -100%; top: 0; bottom: 0; z-index: 50;
                width: min(92vw, 320px);
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

        /* Jadwal hari indicator */
        .hari-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 20px;
            font-size: 10px; font-weight: 600;
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.6);
        }
        .hari-badge.active-day {
            background: rgba(24,95,165,0.4);
            color: #90c8ff;
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
        class="w-60 shrink-0 bg-brand-900 flex flex-col h-full overflow-y-auto shadow-xl">

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

        {{-- ──────── PASIEN NAV ──────── --}}
        <nav id="nav-pasien" class="hidden flex-1 flex-col px-3 py-4 space-y-5">

            <div>
                <div class="nav-section-label mb-2">Menu Utama</div>
                <div class="space-y-0.5">
                    <a href="/pasien" class="nav-link" data-path="/pasien">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/>
                            <rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>
                        </svg>
                        <span class="nav-link-label">Dashboard</span>
                    </a>
                    <a href="/jadwal" class="nav-link" data-path="/jadwal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        <span class="nav-link-label">Jadwal Dokter</span>
                        <span id="jadwal-hari-badge" class="ml-auto hari-badge text-[9px]">—</span>
                    </a>
                </div>
            </div>

            <div>
                <div class="nav-section-label mb-2">Konsultasi</div>
                <div class="space-y-0.5">
                    <a href="/welcome" class="nav-link" data-path="/welcome">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <path d="M12 8v4M10 10h4"/>
                        </svg>
                        <span class="nav-link-label">Konsultasi Baru</span>
                    </a>
                    <a href="/riwayat" class="nav-link" data-path="/riwayat">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span class="nav-link-label">Riwayat Konsultasi</span>
                    </a>
                </div>
            </div>

        </nav>

        {{-- ──────── DOKTER NAV ──────── --}}
        <nav id="nav-dokter" class="hidden flex-1 flex-col px-3 py-4 space-y-5">

            <div>
                <div class="nav-section-label mb-2">Menu Dokter</div>
                <div class="space-y-0.5">
                    <a href="/dokter" class="nav-link" data-path="/dokter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/>
                            <rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="/dokter/jadwal-saya" class="nav-link" data-path="/dokter/jadwal-saya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        Jadwal Saya
                    </a>
                    <a href="/tim" class="nav-link" data-path="/tim">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Jadwal Dokter
                    </a>
                    <a href="/dokter/konsultasi" class="nav-link" data-path="/dokter/konsultasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Konsultasi Pasien
                    </a>
                </div>
            </div>

        </nav>

        {{-- ──────── ADMIN NAV ──────── --}}
        <nav id="nav-admin" class="hidden flex-1 flex-col px-3 py-4 space-y-5">

            <div>
                <div class="nav-section-label mb-2">Admin Panel</div>
                <div class="space-y-0.5">
                    <a href="/admin" class="nav-link" data-path="/admin">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Dashboard Admin
                    </a>
                    <a href="/admin/dokter/tambah" class="nav-link" data-path="/admin/dokter/tambah">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                        Kelola Dokter
                    </a>
                    <a href="/konsultasi" class="nav-link" data-path="/konsultasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Semua Konsultasi
                    </a>
                    <a href="/jadwal" class="nav-link" data-path="/jadwal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        Jadwal Dokter
                    </a>
                    <a href="/tim" class="nav-link" data-path="/tim">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Tim Dokter
                    </a>
                </div>
            </div>

        </nav>

        {{-- User Footer --}}
        <div class="px-4 py-4 border-t border-white/10 flex-shrink-0">
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

    {{-- ══════════════ MAIN CONTENT ══════════════ --}}
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

                {{-- CTA button berdasarkan role (diisi JS) --}}
                <div id="header-cta"></div>

                <button onclick="logout()"
                    class="hidden sm:flex items-center gap-1 text-slate-500 hover:text-red-500 border border-slate-200 hover:border-red-200 text-[11px] px-2.5 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>

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

{{-- ══ MOBILE BOTTOM NAV (diisi JS berdasarkan role) ══ --}}
<nav id="bottom-nav"
    class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-30 items-center justify-around px-2 py-2">
    {{-- Diisi oleh JS --}}
</nav>

<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');

    // ─── Referensi fetch asli ───────────────────────────────────────
    const originalFetch = window.fetch;

    // ─── Idle Timeout ───────────────────────────────────────────────
    const IDLE_TIMEOUT_MS = 30 * 60 * 1000;
    const WARN_BEFORE_MS  = 30 * 1000;

    var idleTimer    = null;
    var warnTimer    = null;
    var warnBannerEl = null;
    var sessionEnded = false;

    function resetIdleTimer() {
        if (sessionEnded) return;
        clearTimeout(idleTimer);
        clearTimeout(warnTimer);
        hideWarnBanner();
        warnTimer = setTimeout(showWarnBanner,       IDLE_TIMEOUT_MS - WARN_BEFORE_MS);
        idleTimer = setTimeout(handleSessionExpired, IDLE_TIMEOUT_MS);
    }

    function showWarnBanner() {
        if (sessionEnded) return;
        if (!warnBannerEl) {
            warnBannerEl = document.createElement('div');
            warnBannerEl.style.cssText = 'position:fixed;top:0;left:0;right:0;background:#f59e0b;color:white;font-size:13px;font-weight:600;text-align:center;padding:10px 16px;z-index:9998;display:flex;align-items:center;justify-content:center;gap:12px;';
            warnBannerEl.innerHTML = '⚠️ Sesi akan berakhir dalam 30 detik karena tidak aktif. <button onclick="keepAlive()" style="background:white;color:#92400e;border:none;border-radius:6px;padding:4px 12px;font-size:12px;font-weight:700;cursor:pointer;">Tetap Login</button>';
            document.body.prepend(warnBannerEl);
        }
        warnBannerEl.style.display = 'flex';
    }
    function hideWarnBanner() {
        if (warnBannerEl) warnBannerEl.style.display = 'none';
    }
    async function keepAlive() {
        var t = localStorage.getItem('auth_token');
        if (!t) return;
        try { await originalFetch('/api/auth/me', { headers: { 'Authorization': 'Bearer ' + t, 'Accept': 'application/json' } }); } catch(e) {}
        resetIdleTimer();
    }

    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function(evt) {
        document.addEventListener(evt, resetIdleTimer, { passive: true });
    });

    // ─── Fetch interceptor untuk 401 ────────────────────────────────
    window.fetch = async function(...args) {
        let response;
        try { response = await originalFetch(...args); } catch(e) { throw e; }
        if (response.status === 401) { handleSessionExpired(); return response; }
        return response;
    };

    function handleSessionExpired() {
        if (sessionEnded) return;
        sessionEnded = true;
        clearTimeout(idleTimer); clearTimeout(warnTimer);
        if (warnBannerEl) warnBannerEl.style.display = 'none';
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        var notif = document.createElement('div');
        notif.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#ef4444;color:white;padding:14px 24px;border-radius:12px;font-size:14px;font-weight:600;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,0.3);text-align:center;';
        notif.innerHTML = '⏱️ Sesi berakhir karena tidak aktif.<br>Mengalihkan ke halaman login...';
        document.body.appendChild(notif);
        setTimeout(function() { window.location.href = '/login'; }, 2000);
    }

    // ─── Render Sidebar & Bottom Nav berdasarkan role ────────────────
    var role = user ? user.role : null;

    if (user) {
        var initials = user.name.split(' ').map(function(w) { return w[0] || ''; }).join('').substring(0,2).toUpperCase();
        var navAvatar = document.getElementById('nav-avatar');
        var mobileAvat = document.getElementById('mobile-avatar');
        var navName = document.getElementById('nav-name');
        var navRole = document.getElementById('nav-role');
        if (navAvatar) navAvatar.textContent = initials;
        if (mobileAvat) mobileAvat.textContent = initials;
        if (navName) navName.textContent = user.name;
        if (navRole) navRole.textContent = user.role === 'dokter' ? 'Dokter' : user.role === 'admin' ? 'Administrator' : 'Pasien';

        // Show role-based sidebar nav
        if (role === 'pasien') {
            document.getElementById('nav-pasien').classList.remove('hidden');
            document.getElementById('nav-pasien').classList.add('flex');
            renderBottomNavPasien();
            renderHeaderCtaPasien();
            updateJadwalHariBadge();
        } else if (role === 'dokter') {
            document.getElementById('nav-dokter').classList.remove('hidden');
            document.getElementById('nav-dokter').classList.add('flex');
            renderBottomNavDokter();
            renderHeaderCtaDokter();
        } else if (role === 'admin') {
            document.getElementById('nav-admin').classList.remove('hidden');
            document.getElementById('nav-admin').classList.add('flex');
            renderBottomNavAdmin();
        }
    }

    // ─── Active nav highlight ────────────────────────────────────────
    var path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(function(el) {
        var href = el.getAttribute('href') || el.getAttribute('data-path');
        if (href === path) el.classList.add('active');
    });

    // ─── Jadwal hari badge (pasien sidebar) ──────────────────────────
    function updateJadwalHariBadge() {
        var badge = document.getElementById('jadwal-hari-badge');
        if (!badge) return;
        var hariList = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        var today = new Date().getDay();
        badge.textContent = hariList[today];
        badge.classList.add('active-day');
    }

    // ─── Bottom nav renderers ─────────────────────────────────────────
    function renderBottomNavPasien() {
        document.getElementById('bottom-nav').innerHTML = [
            '<a href="/pasien" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg><span class="text-[10px] font-medium">Home</span></a>',
            '<a href="/jadwal" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><span class="text-[10px] font-medium">Jadwal</span></a>',
            '<a href="/welcome" class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl bg-brand-600 text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span class="text-[10px] font-semibold">Konsul</span></a>',
            '<a href="/riwayat" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span class="text-[10px] font-medium">Riwayat</span></a>',
        ].join('');
    }

    function renderBottomNavDokter() {
        document.getElementById('bottom-nav').innerHTML = [
            '<a href="/dokter" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg><span class="text-[10px] font-medium">Dashboard</span></a>',
            '<a href="/dokter/jadwal-saya" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><span class="text-[10px] font-medium">Jadwal Saya</span></a>',
            '<a href="/tim" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg><span class="text-[10px] font-medium">Jadwal Dokter</span></a>',
            '<a href="/dokter/konsultasi" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span class="text-[10px] font-medium">Chat</span></a>',
        ].join('');
    }

    function renderBottomNavAdmin() {
        document.getElementById('bottom-nav').innerHTML = [
            '<a href="/admin" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg><span class="text-[10px] font-medium">Dashboard</span></a>',
            '<a href="/konsultasi" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span class="text-[10px] font-medium">Konsultasi</span></a>',
            '<a href="/admin/dokter/tambah" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg><span class="text-[10px] font-medium">Dokter</span></a>',
            '<a href="/jadwal" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 hover:text-brand-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><span class="text-[10px] font-medium">Jadwal</span></a>',
        ].join('');
    }

    // ─── Header CTA renderers ─────────────────────────────────────────
    function renderHeaderCtaPasien() {
        document.getElementById('header-cta').innerHTML =
            '<a href="/welcome" class="hidden sm:flex items-center gap-1.5 bg-brand-600 hover:bg-brand-800 text-white text-[12px] font-medium px-3 py-1.5 rounded-lg transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span>Konsultasi</span></a>';
    }
    function renderHeaderCtaDokter() {
        document.getElementById('header-cta').innerHTML =
            '<a href="/dokter/konsultasi" class="hidden sm:flex items-center gap-1.5 bg-brand-600 hover:bg-brand-800 text-white text-[12px] font-medium px-3 py-1.5 rounded-lg transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span>Lihat Antrian</span></a>';
    }

    // ─── Highlight bottom nav active ─────────────────────────────────
    setTimeout(function() {
        document.querySelectorAll('#bottom-nav a').forEach(function(el) {
            if (el.getAttribute('href') === path) {
                el.classList.add('text-brand-600');
                el.classList.remove('text-slate-400');
            }
        });
    }, 50);

    // ─── Sidebar toggle ───────────────────────────────────────────────
    function toggleSidebar() {
        document.getElementById('main-sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('main-sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }

    // ─── Offline banner ───────────────────────────────────────────────
    function updateOnlineStatus() {
        var b = document.getElementById('offline-banner');
        if (!b) return;
        navigator.onLine ? b.classList.remove('show') : b.classList.add('show');
    }
    window.addEventListener('online',  updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    // ─── Logout ───────────────────────────────────────────────────────
    async function logout() {
        var t = localStorage.getItem('auth_token');
        if (t) {
            try {
                await originalFetch('/api/auth/logout', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + t, 'Accept': 'application/json' }
                });
            } catch(e) {}
        }
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        window.location.href = '/login';
    }

    // ─── Service Worker ───────────────────────────────────────────────
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(console.error);
        navigator.serviceWorker.addEventListener('message', function(e) {
            if (e.data.type === 'SYNC_SUCCESS' && typeof renderUI === 'function') renderUI();
        });
    }

    // ─── Start idle timer ─────────────────────────────────────────────
    if (token && user) resetIdleTimer();
</script>

@yield('scripts')
</body>
</html>