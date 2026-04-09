const CACHE_NAME = 'telemedicine-v2';
const API_BASE   = 'http://localhost:8000';

// Aset yang di-cache saat install
const ASSETS_TO_CACHE = [
    '/',
    '/login',
    '/pasien',
    '/register',
    'https://cdn.tailwindcss.com',
    'https://unpkg.com/dexie@3.2.4/dist/dexie.js',
];

// ─── 1. INSTALL ───────────────────────────────────────────
self.addEventListener('install', event => {
    console.log('[SW] Installing v2...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            // addAll gagal total kalau satu URL error
            // Gunakan individual add dengan catch
            return Promise.allSettled(
                ASSETS_TO_CACHE.map(url =>
                    cache.add(url).catch(err =>
                        console.warn('[SW] Failed to cache:', url, err)
                    )
                )
            );
        })
    );
    self.skipWaiting();
});

// ─── 2. ACTIVATE ──────────────────────────────────────────
self.addEventListener('activate', event => {
    console.log('[SW] Activating v2...');
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

// ─── 3. FETCH ─────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Skip browser extension requests
    if (!url.protocol.startsWith('http')) return;

    // API Laravel → Network-First
    if (url.hostname === 'localhost' && url.port === '8000') {
        event.respondWith(networkFirst(event.request));
        return;
    }

    // Halaman & aset → Cache-First dengan Network fallback
    event.respondWith(cacheFirst(event.request));
});

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        console.log('[SW] Cache hit:', request.url);
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
        console.warn('[SW] Offline, no cache:', request.url);
        // Fallback ke halaman login kalau halaman tidak ada di cache
        const fallback = await caches.match('/login');
        return fallback || new Response(
            '<h1>Offline</h1><p>Sambungkan internet untuk melanjutkan.</p>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

async function networkFirst(request) {
    try {
        return await fetch(request);
    } catch (err) {
        const cached = await caches.match(request);
        if (cached) return cached;
        return new Response(
            JSON.stringify({ error: 'Offline' }),
            { status: 503, headers: { 'Content-Type': 'application/json' } }
        );
    }
}

// ─── 4. BACKGROUND SYNC ───────────────────────────────────
self.addEventListener('sync', event => {
    console.log('[SW] Sync triggered:', event.tag);
    if (event.tag === 'sync-konsultasi') {
        event.waitUntil(syncKonsultasi());
    }
});

async function syncKonsultasi() {
    const db          = await openDB();
    const pendingData = await getAllPending(db);

    console.log('[SW] Pending items:', pendingData.length);

    // Ambil token dari IndexedDB
    const authData = await getAuthData(db);
    const token    = authData?.token;

    if (!token) {
        console.warn('[SW] Tidak ada token, skip sync');
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
                notifyClients({ type: 'SYNC_SUCCESS', local_id: item.id });
                console.log('[SW] Synced:', item.id);
            }
        } catch (err) {
            console.error('[SW] Sync failed:', item.id, err);
        }
    }
}

async function notifyClients(message) {
    const clients = await self.clients.matchAll();
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
            // Store untuk auth token
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
        request.onsuccess = () => resolve(request.result);
        request.onerror   = () => reject(request.error);
    });
}

function getAuthData(db) {
    return new Promise((resolve, reject) => {
        const tx      = db.transaction('auth', 'readonly');
        const store   = tx.objectStore('auth');
        const request = store.get('session');
        request.onsuccess = () => resolve(request.result);
        request.onerror   = () => reject(request.error);
    });
}

function updateStatus(db, id, status, server_id = null) {
    return new Promise((resolve, reject) => {
        const tx    = db.transaction('konsultasi', 'readwrite');
        const store = tx.objectStore('konsultasi');
        const req   = store.get(id);
        req.onsuccess = () => {
            const item    = req.result;
            item.status   = status;
            if (server_id) item.server_id = server_id;
            item.synced_at = new Date().toISOString();
            store.put(item);
            resolve();
        };
        req.onerror = () => reject(req.error);
    });
}