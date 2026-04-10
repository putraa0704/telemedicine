<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Telemedicine')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        medical: {
                            50:  '#f0fdf9',
                            100: '#ccfbef',
                            200: '#99f6e0',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="manifest" href="/manifest.json"/>
    <meta name="theme-color" content="#0d9488"/>
    <style>
        * { font-family: 'Segoe UI', system-ui, sans-serif; }
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04); }
        .badge-pending  { background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
        .badge-synced   { background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
        .badge-received { background:#dbeafe; color:#1e40af; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
        .badge-done     { background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
        .badge-pasien   { background:#ccfbef; color:#0f766e; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-dokter   { background:#dbeafe; color:#1e40af; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- Offline banner --}}
    <div id="offline-banner" class="hidden bg-amber-500 text-white text-center text-xs py-2 font-medium">
        ⚠️ Anda sedang offline — data akan disinkronisasi saat koneksi kembali
    </div>

    {{-- Navbar --}}
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50" style="box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#0d9488,#0284c7)">T</div>
                <span class="font-semibold text-slate-700">Telemedicine</span>
            </a>
            <div id="nav-user" class="flex items-center gap-3 text-sm"></div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <script>
        var token = localStorage.getItem('auth_token');
        var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');

        // Offline banner
        function updateStatus() {
            const banner = document.getElementById('offline-banner');
            if (!navigator.onLine) {
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }
        }
        window.addEventListener('online',  updateStatus);
        window.addEventListener('offline', updateStatus);
        updateStatus();

        // Navbar user info
        if (user) {
            document.getElementById('nav-user').innerHTML = `
                <span class="text-slate-600">${user.name}</span>
                <span class="badge-${user.role}">${user.role}</span>
                <button onclick="logout()"
                    class="text-slate-400 hover:text-red-500 transition text-xs border border-slate-200 px-3 py-1 rounded-lg hover:border-red-200">
                    Keluar
                </button>
            `;
        }

        async function logout() {
            if (token) {
                await fetch('http://localhost:8000/api/auth/logout', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` }
                }).catch(() => {});
            }
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            window.location.href = '/login';
        }

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(console.error);
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data.type === 'SYNC_SUCCESS') {
                    if (typeof renderUI === 'function') renderUI();
                }
            });
        }
    </script>

    @yield('scripts')
</body>
</html>