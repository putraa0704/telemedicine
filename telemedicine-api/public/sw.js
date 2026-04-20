const CACHE_NAME = 'telemedicine-v4';
const API_BASE   = 'http://localhost:8000';

// Hanya cache aset lokal — JANGAN masukkan URL CDN eksternal
const ASSETS_TO_CACHE = [
    '/',
    '/login',
    '/pasien',
    '/register',
    '/welcome',
    '/konsultasi/baru',  // ← tambah ini
    '/konsultasi',
    '/riwayat',
    '/jadwal',
    '/tim',
];

// Domain yang di-skip (tidak di-cache, langsung network)
const SKIP_CACHE_DOMAINS = [
    'cdn.tailwindcss.com',
    'unpkg.com',
    'fonts.googleapis.com',
    'fonts.gstatic.com',
    'cdnjs.cloudflare.com',
    'placehold.co',
];

// ─── 1. INSTALL ───────────────────────────────────────────
self.addEventListener('install', event => {
    console.log('[SW] Installing v4...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            // Gunakan individual add + catch supaya 1 URL gagal tidak block semua
            return Promise.allSettled(
                ASSETS_TO_CACHE.map(url =>
                    cache.add(url).catch(err =>
                        console.warn('[SW] Failed to cache:', url, err.message)
                    )
                )
            );
        })
    );
    self.skipWaiting();
});

// ─── 2. ACTIVATE ──────────────────────────────────────────
self.addEventListener('activate', event => {
    console.log('[SW] Activating v4...');
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => {
                        console.log('[SW] Deleting old cache:', key);
                        return caches.delete(key);
                    })
            )
        )
    );
    self.clients.claim();
});

// ─── 3. FETCH ─────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Skip non-GET
    if (event.request.method !== 'GET') return;

    // Skip non-http(s)
    if (!url.protocol.startsWith('http')) return;

    // Skip CDN eksternal — biarkan browser handle langsung (no cache)
    if (SKIP_CACHE_DOMAINS.some(domain => url.hostname.includes(domain))) {
        return; // tidak intercept, browser fetch normal
    }

    // API Laravel (localhost:8000/api/*) → Network-First, tidak di-cache
    if (url.hostname === 'localhost' && url.port === '8000' && url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirst(event.request));
        return;
    }

    // Navigasi halaman HTML → utamakan network agar update view terbaru langsung terlihat.
    if (event.request.mode === 'navigate') {
        event.respondWith(networkFirstPage(event.request));
        return;
    }

    // Halaman & aset lokal → Cache-First dengan Network fallback
    event.respondWith(cacheFirst(event.request));
});

// ── Cache-First ──
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        console.warn('[SW] Offline, no cache for:', request.url);
        // Fallback ke halaman login
        const fallback = await caches.match('/pasien') || await caches.match('/login');
        return fallback || new Response(
            '<h1>Offline</h1><p>Sambungkan internet untuk melanjutkan.</p>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

// ── Network-First (untuk API) ──
async function networkFirst(request) {
    try {
        return await fetch(request);
    } catch (err) {
        // API tidak di-cache, kembalikan error JSON
        return new Response(
            JSON.stringify({ error: 'Offline', message: 'Tidak ada koneksi internet' }),
            {
                status: 503,
                headers: { 'Content-Type': 'application/json' }
            }
        );
    }
}

// ─── 4. BACKGROUND SYNC ───────────────────────────────────
self.addEventListener('sync', event => {
    console.log('[SW] Background sync triggered:', event.tag);
    if (event.tag === 'sync-konsultasi') {
        event.waitUntil(syncKonsultasi());
    }
});

async function syncKonsultasi() {
    let db;
    try {
        db = await openDB();
    } catch (err) {
        console.error('[SW] Gagal buka IndexedDB:', err);
        return;
    }

    const pendingData = await getAllPending(db);
    console.log('[SW] Pending items to sync:', pendingData.length);

    if (!pendingData.length) return;

    const authData = await getAuthData(db);
    const token    = authData?.token;

    if (!token) {
        console.warn('[SW] Tidak ada token auth, skip sync');
        return;
    }

    for (const item of pendingData) {
        try {
            const response = await fetch(`${API_BASE}/api/konsultasi`, {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    local_id:   item.id,
                    nama:       item.nama,
                    keluhan:    item.keluhan,
                    created_at: item.created_at,
                }),
            });

            if (response.ok || response.status === 409) {
                const result = await response.json();
                await updateStatus(db, item.id, 'synced', result.server_id);

                // Beritahu semua tab yang terbuka
                notifyClients({
                    type:      'SYNC_SUCCESS',
                    local_id:  item.id,
                    server_id: result.server_id,
                });

                console.log('[SW] Synced item:', item.id, '→ server_id:', result.server_id);
            } else {
                console.warn('[SW] Sync HTTP error', response.status, 'for item:', item.id);
            }
        } catch (err) {
            console.error('[SW] Sync failed for item:', item.id, err.message);
            // Lanjut ke item berikutnya
        }
    }
}

async function notifyClients(message) {
    const clients = await self.clients.matchAll({ includeUncontrolled: true });
    clients.forEach(client => client.postMessage(message));
}

// ─── IndexedDB Helpers ────────────────────────────────────
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('telemedicine', 2);
        request.onsuccess = () => resolve(request.result);
        request.onerror   = () => reject(request.error);
        request.onupgradeneeded = e => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('konsultasi')) {
                const store = db.createObjectStore('konsultasi', { keyPath: 'id' });
                store.createIndex('status', 'status', { unique: false });
            }
            if (!db.objectStoreNames.contains('auth')) {
                db.createObjectStore('auth', { keyPath: 'key' });
            }
        };
    });
}

function getAllPending(db) {
    return new Promise((resolve, reject) => {
        const tx      = db.transaction('konsultasi', 'readonly');
        const store   = tx.objectStore('konsultasi');
        const index   = store.index('status');
        const request = index.getAll('pending');
        request.onsuccess = () => resolve(request.result || []);
        request.onerror   = () => reject(request.error);
    });
}

function getAuthData(db) {
    return new Promise((resolve, reject) => {
        const tx      = db.transaction('auth', 'readonly');
        const store   = tx.objectStore('auth');
        const request = store.get('session');
        request.onsuccess = () => resolve(request.result || null);
        request.onerror   = () => reject(request.error);
    });
}

function updateStatus(db, id, status, server_id = null) {
    return new Promise((resolve, reject) => {
        const tx    = db.transaction('konsultasi', 'readwrite');
        const store = tx.objectStore('konsultasi');
        const req   = store.get(id);
        req.onsuccess = () => {
            const item     = req.result;
            if (!item) { resolve(); return; }
            item.status    = status;
            if (server_id) item.server_id = server_id;
            item.synced_at = new Date().toISOString();
            store.put(item);
            resolve();
        };
        req.onerror = () => reject(req.error);
    });
}

// ── Network-First khusus halaman HTML ──
async function networkFirstPage(request) {
    try {
        const response = await fetch(request);
        if (response && response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        const cached = await caches.match(request);
        if (cached) return cached;

        const fallback = await caches.match('/login');
        return fallback || new Response(
            '<h1>Offline</h1><p>Sambungkan internet untuk melanjutkan.</p>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}