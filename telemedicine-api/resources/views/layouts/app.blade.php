<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'MediConnect') — Telemedicine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#E6F1FB', 100: '#B5D4F4', 200: '#85B7EB',
                            400: '#378ADD', 600: '#185FA5', 800: '#0C447C', 900: '#042C53',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="manifest" href="/manifest.json"/>
    <meta name="theme-color" content="#185FA5"/>
    @yield('head')
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">

{{-- Offline Banner --}}
<div id="offline-banner" class="hidden bg-amber-600 text-white text-xs font-medium text-center py-2 sticky top-0 z-50">
    ⚠️ Anda sedang offline — data akan disinkronisasi saat koneksi kembali
</div>

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside class="w-56 min-w-56 bg-brand-900 flex flex-col overflow-y-auto flex-shrink-0">

        <div class="px-5 py-5 border-b border-white/10">
            <div class="text-white font-bold text-lg tracking-tight">MediConnect</div>
            <div class="text-white/40 text-[10px] uppercase tracking-widest mt-0.5">Telemedicine Platform</div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-5">

            {{-- Utama --}}
            <div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-2 mb-1.5">Utama</div>
                <a href="/pasien" id="nav-link-dashboard"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors @yield('nav_dashboard')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="1" y="1" width="6" height="6" rx="1.5"/><rect x="9" y="1" width="6" height="6" rx="1.5"/>
                        <rect x="1" y="9" width="6" height="6" rx="1.5"/><rect x="9" y="9" width="6" height="6" rx="1.5"/>
                    </svg>
                    Dashboard
                </a>
                <a href="/konsultasi"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors mt-0.5 @yield('nav_konsultasi')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="1" y="1" width="14" height="10" rx="2"/><path d="M5 15l3-4 3 4"/>
                    </svg>
                    Konsultasi
                </a>
            </div>

            {{-- Dokter --}}
            <div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-2 mb-1.5">Dokter</div>
                <a href="/jadwal"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors @yield('nav_jadwal')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="1" y="2" width="14" height="13" rx="2"/><path d="M5 1v2M11 1v2M1 6h14"/>
                    </svg>
                    Jadwal Dokter
                </a>
                <a href="/tim"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors mt-0.5 @yield('nav_tim')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="6" cy="5" r="3"/><path d="M1 14a5 5 0 0110 0"/>
                        <circle cx="13" cy="5" r="2"/><path d="M13 12a3 3 0 012.5 3"/>
                    </svg>
                    Tim Dokter
                </a>
            </div>

            {{-- Pasien --}}
            <div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-2 mb-1.5">Pasien</div>
                <a href="/konsultasi/baru"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors @yield('nav_new')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="8" cy="8" r="7"/><path d="M8 5v6M5 8h6"/>
                    </svg>
                    Konsultasi Baru
                </a>
                <a href="/riwayat"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors mt-0.5 @yield('nav_riwayat')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="8" cy="8" r="7"/><path d="M8 5v3.5l2.5 1.5"/>
                    </svg>
                    Riwayat
                </a>
            </div>

            {{-- Admin (muncul hanya untuk admin) --}}
            <div id="nav-admin-section" class="hidden">
                <div class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-2 mb-1.5">Admin</div>
                <a href="/admin"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors @yield('nav_admin')">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 1l2 5h5l-4 3 1.5 5L8 11l-4.5 3L5 9 1 6h5z"/>
                    </svg>
                    Admin Panel
                </a>
                <a href="/admin/dokter/tambah"
                    class="nav-item flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors mt-0.5">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-80" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="7" cy="5" r="3"/><path d="M1 14a6 6 0 0110.5-4"/>
                        <path d="M13 10v4M11 12h4"/>
                    </svg>
                    Kelola Dokter
                </a>
            </div>
        </nav>

        {{-- User Footer --}}
        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-2.5">
                <div id="nav-avatar" class="w-8 h-8 rounded-full bg-brand-400 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">?</div>
                <div class="flex-1 min-w-0">
                    <div id="nav-name" class="text-white/90 text-[13px] font-medium truncate">—</div>
                    <div id="nav-role" class="text-white/40 text-[11px] mt-0.5">—</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="h-14 bg-white border-b border-slate-200 px-6 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-[15px] font-semibold text-slate-800">@yield('page_title', 'Dashboard')</h1>
                <p class="text-[12px] text-slate-400 mt-0.5">@yield('page_sub', '')</p>
            </div>
            <div class="flex items-center gap-2.5">
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 rounded-full text-[12px] font-semibold text-green-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
                </div>
                <a href="/konsultasi/baru" class="bg-brand-600 hover:bg-brand-800 text-white text-[13px] font-medium px-3.5 py-1.5 rounded-lg transition-colors">
                    + Konsultasi
                </a>
                <button onclick="logout()" class="text-slate-500 hover:text-red-500 border border-slate-200 hover:border-red-200 text-[12px] px-3 py-1.5 rounded-lg transition-colors">
                    Keluar
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-slate-100">
            @yield('content')
        </main>
    </div>
</div>

<style>
    .nav-item.active-nav { background: #185FA5; color: #fff; font-weight: 500; }
    .nav-item.active-nav svg { opacity: 1; }
</style>

<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');

    if (user) {
        var initials = user.name.split(' ').map(function(w){ return w[0]; }).join('').substring(0,2).toUpperCase();
        document.getElementById('nav-avatar').textContent = initials;
        document.getElementById('nav-name').textContent   = user.name;
        document.getElementById('nav-role').textContent   = user.role.charAt(0).toUpperCase() + user.role.slice(1);

        // Arahkan dashboard sesuai role
        var dashLink = document.getElementById('nav-link-dashboard');
        if (user.role === 'dokter' || user.role === 'admin') dashLink.href = '/dokter';
        if (user.role === 'admin') document.getElementById('nav-admin-section').classList.remove('hidden');
    }

    // Active nav highlight
    var path = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(function(el) {
        if (el.getAttribute('href') === path) el.classList.add('active-nav');
    });

    // Offline banner
    function updateOnlineStatus() {
        var b = document.getElementById('offline-banner');
        navigator.onLine ? b.classList.add('hidden') : b.classList.remove('hidden');
    }
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    async function logout() {
        if (token) {
            try { await fetch('/api/auth/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token } }); } catch(e) {}
        }
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        window.location.href = '/login';
    }

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(console.error);
        navigator.serviceWorker.addEventListener('message', function(e) {
            if (e.data.type === 'SYNC_SUCCESS' && typeof renderUI === 'function') renderUI();
        });
    }
</script>

@yield('scripts')
</body>
</html>