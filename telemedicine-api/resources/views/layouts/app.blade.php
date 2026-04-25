<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'CareMate') — Telemedicine</title>
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

        @media (min-width: 1024px) {
            #main-sidebar {
                transition: width 0.2s ease;
            }
            body.sidebar-collapsed #main-sidebar {
                width: 82px !important;
            }
            body.sidebar-collapsed #main-sidebar .nav-link {
                justify-content: center;
                padding-left: 8px;
                padding-right: 8px;
            }
            body.sidebar-collapsed #main-sidebar .nav-link-label,
            body.sidebar-collapsed #main-sidebar .nav-section-label,
            body.sidebar-collapsed #main-sidebar #jadwal-hari-badge,
            body.sidebar-collapsed #main-sidebar #nav-name,
            body.sidebar-collapsed #main-sidebar #nav-role {
                display: none !important;
            }
            body.sidebar-collapsed #main-sidebar nav {
                padding-left: 8px;
                padding-right: 8px;
            }
            body.sidebar-collapsed #main-sidebar .px-5.pt-6.pb-5 {
                padding-left: 10px;
                padding-right: 10px;
            }
            body.sidebar-collapsed #main-sidebar .px-5.pt-6.pb-5 > div {
                justify-content: center;
            }
            body.sidebar-collapsed #main-sidebar .px-5.pt-6.pb-5 > div > div:last-child {
                display: none;
            }
            body.sidebar-collapsed #main-sidebar #nav-footer-row {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            body.sidebar-collapsed #main-sidebar #nav-footer-meta {
                display: none;
            }
            body.sidebar-collapsed #main-sidebar #nav-avatar {
                width: 40px;
                height: 40px;
            }
            body.sidebar-collapsed #main-sidebar #nav-footer-actions {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }
            body.sidebar-collapsed #main-sidebar #nav-footer-actions button {
                width: 30px;
                height: 30px;
            }
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
                    <div class="text-white font-bold text-[15px] leading-tight">CareMate</div>
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
                    <a href="/konsultasi" class="nav-link" data-path="/konsultasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span class="nav-link-label">Konsultasi Aktif</span>
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
                        Konsultasi Aktif
                    </a>
                    <a href="/dokter/riwayat" class="nav-link" data-path="/dokter/riwayat">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Riwayat Konsultasi
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
            <div id="nav-footer-row" class="flex items-center gap-2.5">
                <div id="nav-avatar" class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">?</div>
                <div id="nav-footer-meta" class="flex-1 min-w-0">
                    <div id="nav-name" class="text-white/90 text-[12px] font-medium truncate">—</div>
                    <div id="nav-role" class="text-white/40 text-[10px] mt-0.5 capitalize">—</div>
                </div>

                <div id="nav-footer-actions" class="flex items-center gap-1.5">
                    <div class="relative">
                        <button onclick="toggleSettingsMenu(event)" title="Pengaturan"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-white/40 hover:text-white/80 hover:bg-white/10 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </button>

                        <div id="settings-menu" class="hidden absolute bottom-9 right-0 w-44 bg-white rounded-xl border border-slate-200 shadow-xl overflow-hidden z-50">
                            <button onclick="openProfileModal('profile')" class="w-full text-left px-3 py-2.5 text-[12px] text-slate-700 hover:bg-slate-50">Edit Profile</button>
                            <button onclick="openProfileModal('password')" class="w-full text-left px-3 py-2.5 text-[12px] text-slate-700 hover:bg-slate-50 border-t border-slate-100">Ubah Kata Sandi</button>
                        </div>
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

            <button id="desktop-sidebar-toggle" onclick="toggleDesktopSidebar()"
                class="hidden lg:flex p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors" title="Buka/Tutup Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 5h18M3 12h18M3 19h18"/>
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

                <button onclick="openProfileModal('profile')" title="Pengaturan"
                    class="hidden sm:flex items-center gap-1 text-slate-500 hover:text-brand-700 border border-slate-200 hover:border-brand-200 text-[11px] px-2.5 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    Pengaturan
                </button>

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

<div id="profile-modal" class="hidden fixed inset-0 bg-slate-900/45 z-[80] items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <div class="text-[14px] font-semibold text-slate-800">Pengaturan Akun</div>
            <button onclick="closeProfileModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">✕</button>
        </div>

        <div class="px-5 pt-4 pb-1 flex gap-2">
            <button id="tab-profile" onclick="switchProfileTab('profile')" class="text-[12px] font-semibold px-3 py-1.5 rounded-lg border">Edit Profile</button>
            <button id="tab-password" onclick="switchProfileTab('password')" class="text-[12px] font-semibold px-3 py-1.5 rounded-lg border">Ubah Kata Sandi</button>
        </div>

        <div id="profile-msg" class="hidden mx-5 mt-3 text-[12px] px-3 py-2 rounded-lg"></div>

        <form id="form-edit-profile" class="px-5 py-4 space-y-3" onsubmit="submitEditProfile(event)">
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Nama (default)</label>
                <input id="profile-name" type="text" readonly class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-100 text-slate-500"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Nomor Ponsel</label>
                <input id="profile-phone" type="text" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Foto Profil</label>
                <input id="profile-photo" type="file" accept="image/png,image/jpeg,image/webp" class="w-full text-[12px] text-slate-600" onchange="previewProfilePhoto(event)"/>
                <img id="profile-photo-preview" class="hidden mt-2 w-14 h-14 rounded-full object-cover border border-slate-200" alt="Preview foto"/>
                <button id="btn-remove-photo" type="button" onclick="markRemoveProfilePhoto()" class="hidden mt-2 text-[11px] font-semibold text-red-600 hover:text-red-700">Hapus Foto</button>
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-800 text-white text-[12px] font-semibold py-2.5 rounded-xl">Simpan Perubahan</button>
        </form>

        <form id="form-change-password" class="hidden px-5 py-4 space-y-3" onsubmit="submitChangePassword(event)">
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Kata Sandi Saat Ini</label>
                <input id="pwd-current" type="password" required class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Kata Sandi Baru</label>
                <input id="pwd-new" type="password" required minlength="8" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50"/>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <input id="pwd-confirm" type="password" required minlength="8" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50"/>
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-800 text-white text-[12px] font-semibold py-2.5 rounded-xl">Ubah Kata Sandi</button>
        </form>
    </div>
</div>

<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    const SIDEBAR_STATE_KEY = 'desktop_sidebar_collapsed';

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
    var removeProfilePhoto = false;

    function getInitials(name) {
        return String(name || '')
            .split(' ')
            .map(function(w) { return w[0] || ''; })
            .join('')
            .substring(0, 2)
            .toUpperCase() || '?';
    }

    function photoUrl(path) {
        if (!path) return '';
        if (/^https?:\/\//.test(path)) return path;
        return '/storage/' + String(path).replace(/^\/+/, '');
    }

    function applyAvatar(el, currentUser) {
        if (!el || !currentUser) return;
        var initials = getInitials(currentUser.name);
        var p = photoUrl(currentUser.foto_profil);
        if (p) {
            el.textContent = '';
            el.style.backgroundImage = 'url(' + p + ')';
            el.style.backgroundSize = 'cover';
            el.style.backgroundPosition = 'center';
            el.style.backgroundRepeat = 'no-repeat';
            el.style.backgroundColor = 'transparent';
        } else {
            el.textContent = initials;
            el.style.backgroundImage = '';
            el.style.backgroundSize = '';
            el.style.backgroundPosition = '';
            el.style.backgroundRepeat = '';
            el.style.backgroundColor = '';
        }
    }

    function refreshIdentityUI() {
        if (!user) return;
        var navName = document.getElementById('nav-name');
        var navRole = document.getElementById('nav-role');
        if (navName) navName.textContent = user.name;
        if (navRole) navRole.textContent = user.role === 'dokter' ? 'Dokter' : user.role === 'admin' ? 'Administrator' : 'Pasien';
        applyAvatar(document.getElementById('nav-avatar'), user);
        applyAvatar(document.getElementById('mobile-avatar'), user);
    }

    if (user) {
        refreshIdentityUI();

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

    function toggleSettingsMenu(event) {
        if (event) event.stopPropagation();
        var menu = document.getElementById('settings-menu');
        if (!menu) return;
        menu.classList.toggle('hidden');
    }

    function closeSettingsMenu() {
        var menu = document.getElementById('settings-menu');
        if (menu) menu.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        var menu = document.getElementById('settings-menu');
        if (!menu) return;
        if (!menu.contains(e.target)) menu.classList.add('hidden');
    });

    function showProfileMessage(text, ok) {
        var box = document.getElementById('profile-msg');
        if (!box) return;
        box.className = 'mx-5 mt-3 text-[12px] px-3 py-2 rounded-lg ' +
            (ok
                ? 'bg-emerald-50 border border-emerald-200 text-emerald-700'
                : 'bg-red-50 border border-red-200 text-red-700');
        box.textContent = text;
        box.classList.remove('hidden');
    }

    function clearProfileMessage() {
        var box = document.getElementById('profile-msg');
        if (!box) return;
        box.classList.add('hidden');
        box.textContent = '';
    }

    function switchProfileTab(tab) {
        var isProfile = tab === 'profile';
        var pTab = document.getElementById('tab-profile');
        var sTab = document.getElementById('tab-password');
        var pForm = document.getElementById('form-edit-profile');
        var sForm = document.getElementById('form-change-password');

        if (pForm) pForm.classList.toggle('hidden', !isProfile);
        if (sForm) sForm.classList.toggle('hidden', isProfile);

        if (pTab) {
            pTab.classList.toggle('bg-brand-600', isProfile);
            pTab.classList.toggle('text-white', isProfile);
            pTab.classList.toggle('border-brand-600', isProfile);
            pTab.classList.toggle('border-slate-200', !isProfile);
            pTab.classList.toggle('text-slate-600', !isProfile);
        }
        if (sTab) {
            sTab.classList.toggle('bg-brand-600', !isProfile);
            sTab.classList.toggle('text-white', !isProfile);
            sTab.classList.toggle('border-brand-600', !isProfile);
            sTab.classList.toggle('border-slate-200', isProfile);
            sTab.classList.toggle('text-slate-600', isProfile);
        }
    }

    function openProfileModal(tab) {
        closeSettingsMenu();
        if (!user) return;
        var modal = document.getElementById('profile-modal');
        if (!modal) return;
        removeProfilePhoto = false;

        document.getElementById('profile-name').value = user.name || '';
        document.getElementById('profile-phone').value = user.no_hp || '';
        document.getElementById('profile-photo').value = '';

        var prev = document.getElementById('profile-photo-preview');
        var removeBtn = document.getElementById('btn-remove-photo');
        var url = photoUrl(user.foto_profil);
        if (prev) {
            if (url) {
                prev.src = url;
                prev.classList.remove('hidden');
                if (removeBtn) removeBtn.classList.remove('hidden');
            } else {
                prev.src = '';
                prev.classList.add('hidden');
                if (removeBtn) removeBtn.classList.add('hidden');
            }
        }

        clearProfileMessage();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        switchProfileTab(tab || 'profile');
    }

    function closeProfileModal() {
        var modal = document.getElementById('profile-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        clearProfileMessage();
    }

    function previewProfilePhoto(event) {
        removeProfilePhoto = false;
        var file = event.target && event.target.files ? event.target.files[0] : null;
        var prev = document.getElementById('profile-photo-preview');
        var removeBtn = document.getElementById('btn-remove-photo');
        if (!prev) return;
        if (!file) {
            var current = removeProfilePhoto ? '' : photoUrl(user ? user.foto_profil : '');
            if (current) {
                prev.src = current;
                prev.classList.remove('hidden');
                if (removeBtn) removeBtn.classList.remove('hidden');
            } else {
                prev.classList.add('hidden');
                prev.src = '';
                if (removeBtn) removeBtn.classList.add('hidden');
            }
            return;
        }
        prev.src = URL.createObjectURL(file);
        prev.classList.remove('hidden');
        if (removeBtn) removeBtn.classList.remove('hidden');
    }

    function markRemoveProfilePhoto() {
        removeProfilePhoto = true;
        var prev = document.getElementById('profile-photo-preview');
        var photoInput = document.getElementById('profile-photo');
        var removeBtn = document.getElementById('btn-remove-photo');

        if (photoInput) photoInput.value = '';
        if (prev) {
            prev.src = '';
            prev.classList.add('hidden');
        }
        if (removeBtn) removeBtn.classList.add('hidden');
    }

    async function submitEditProfile(event) {
        event.preventDefault();
        if (!token || !user) return;

        clearProfileMessage();
        var formData = new FormData();
        formData.append('no_hp', document.getElementById('profile-phone').value.trim());
        formData.append('remove_foto', removeProfilePhoto ? '1' : '0');

        var photoInput = document.getElementById('profile-photo');
        if (photoInput && photoInput.files && photoInput.files[0]) {
            formData.append('foto_profil', photoInput.files[0]);
        }

        try {
            var res = await originalFetch('/api/auth/profile', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                body: formData,
            });
            var data = await res.json();

            if (!res.ok || !data.success) {
                var msg = data && data.message ? data.message : 'Gagal memperbarui profil.';
                if (data && data.errors) {
                    var firstKey = Object.keys(data.errors)[0];
                    if (firstKey && data.errors[firstKey] && data.errors[firstKey][0]) msg = data.errors[firstKey][0];
                }
                showProfileMessage(msg, false);
                return;
            }

            user = data.user || user;
            localStorage.setItem('auth_user', JSON.stringify(user));
            refreshIdentityUI();
            showProfileMessage('Profil berhasil diperbarui.', true);
        } catch (e) {
            showProfileMessage('Tidak dapat terhubung ke server.', false);
        }
    }

    async function submitChangePassword(event) {
        event.preventDefault();
        if (!token) return;
        clearProfileMessage();

        var current = document.getElementById('pwd-current').value;
        var next = document.getElementById('pwd-new').value;
        var confirmNext = document.getElementById('pwd-confirm').value;

        if (next !== confirmNext) {
            showProfileMessage('Konfirmasi kata sandi baru tidak sama.', false);
            return;
        }

        try {
            var res = await originalFetch('/api/auth/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_password: current,
                    new_password: next,
                    new_password_confirmation: confirmNext,
                }),
            });
            var data = await res.json();

            if (!res.ok || !data.success) {
                var msg = data && data.message ? data.message : 'Gagal mengubah kata sandi.';
                if (data && data.errors) {
                    var first = Object.keys(data.errors)[0];
                    if (first && data.errors[first] && data.errors[first][0]) msg = data.errors[first][0];
                }
                showProfileMessage(msg, false);
                return;
            }

            document.getElementById('pwd-current').value = '';
            document.getElementById('pwd-new').value = '';
            document.getElementById('pwd-confirm').value = '';
            showProfileMessage('Kata sandi berhasil diubah.', true);
        } catch (e) {
            showProfileMessage('Tidak dapat terhubung ke server.', false);
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
    function isDesktopViewport() {
        return window.innerWidth >= 1024;
    }

    function applyDesktopSidebarState() {
        if (!isDesktopViewport()) {
            document.body.classList.remove('sidebar-collapsed');
            return;
        }
        var collapsed = localStorage.getItem(SIDEBAR_STATE_KEY) === '1';
        document.body.classList.toggle('sidebar-collapsed', collapsed);
    }

    function toggleDesktopSidebar() {
        if (!isDesktopViewport()) return;
        var collapsed = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(SIDEBAR_STATE_KEY, collapsed ? '1' : '0');
    }

    function toggleSidebar() {
        if (isDesktopViewport()) {
            toggleDesktopSidebar();
            return;
        }
        document.getElementById('main-sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('main-sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }

    window.addEventListener('resize', function() {
        if (isDesktopViewport()) {
            closeSidebar();
            applyDesktopSidebarState();
        }
    });

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
    applyDesktopSidebarState();
    if (token && user) resetIdleTimer();
</script>

@yield('scripts')
</body>
</html>