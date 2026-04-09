<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Telemedicine')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json"/>
    <meta name="theme-color" content="#3b82f6"/>
</head>

<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <span class="font-semibold text-blue-600">🏥 Telemedicine</span>
            <div id="nav-user" class="text-sm text-gray-500"></div>
        </div>
    </nav>

    {{-- Status bar --}}
    <div id="status-bar" class="hidden text-center text-xs py-1 font-medium"></div>

    {{-- Content --}}
    <main class="max-w-4xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Base JS --}}
    <script>
        // Online/offline indicator
        const bar = document.getElementById('status-bar');
        function updateStatus() {
            if (navigator.onLine) {
                bar.className = 'text-center text-xs py-1 font-medium bg-green-100 text-green-700';
                bar.textContent = '🟢 Online';
                setTimeout(() => bar.classList.add('hidden'), 2000);
            } else {
                bar.className = 'text-center text-xs py-1 font-medium bg-red-100 text-red-700';
                bar.textContent = '🔴 Offline — data disimpan lokal';
                bar.classList.remove('hidden');
            }
        }
        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);

        // Tampilkan user di navbar
        var token = localStorage.getItem('auth_token');
        var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
        if (user) {
            document.getElementById('nav-user').innerHTML = `
                <span class="mr-3">${user.name} <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">${user.role}</span></span>
                <button onclick="logout()" class="text-red-500 hover:text-red-700">Keluar</button>
            `;
        }

        async function logout() {
            const token = localStorage.getItem('auth_token');
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
    </script>

    @yield('scripts')

{{-- Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('[SW] Registered:', reg.scope))
                .catch(err => console.error('[SW] Failed:', err));

            // Dengarkan pesan dari SW
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data.type === 'SYNC_SUCCESS') {
                    console.log('[App] Sync sukses:', event.data.local_id);
                    if (typeof renderUI === 'function') renderUI();
                }
            });
        }
    </script>
</body>
</html>