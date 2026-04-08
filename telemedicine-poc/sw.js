const CACHE_NAME = "telemedicine-v1";

// File yang di-cache saat SW diinstall
const ASSETS_TO_CACHE = [
  "/",
  "/index.html",
  "/app.js",
  "/style.css",
  "/manifest.json",
];

// ─── 1. INSTALL ───────────────────────────────────────────
// Dipanggil sekali saat SW pertama kali didaftarkan
self.addEventListener("install", (event) => {
  console.log("[SW] Installing...");

  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("[SW] Caching assets");
      return cache.addAll(ASSETS_TO_CACHE);
    }),
  );

  // Paksa SW aktif tanpa tunggu tab lama ditutup
  self.skipWaiting();
});

// ─── 2. ACTIVATE ──────────────────────────────────────────
// Dipanggil setelah install, bersihkan cache lama
self.addEventListener("activate", (event) => {
  console.log("[SW] Activating...");

  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => {
            console.log("[SW] Deleting old cache:", key);
            return caches.delete(key);
          }),
      ),
    ),
  );

  // Ambil kontrol semua tab yang ada sekarang
  self.clients.claim();
});

// ─── 3. FETCH (intercept semua request) ───────────────────
// Strategi: Cache-First untuk aset, Network-First untuk API
self.addEventListener("fetch", (event) => {
  const url = new URL(event.request.url);

  // Request ke Laravel API (localhost:8000) → Network-First
  if (url.hostname === "localhost" && url.port === "8000") {
    event.respondWith(networkFirst(event.request));
    return;
  }

  // Request ke /api lokal (localhost:3000) → Network-First
  if (url.pathname.startsWith("/api")) {
    event.respondWith(networkFirst(event.request));
    return;
  }

  // Aset statis → Cache-First
  event.respondWith(cacheFirst(event.request));
});
// Cache-First: coba cache dulu, kalau tidak ada baru fetch
async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) {
    console.log("[SW] Cache hit:", request.url);
    return cached;
  }
  try {
    const response = await fetch(request);
    const cache = await caches.open(CACHE_NAME);
    cache.put(request, response.clone());
    return response;
  } catch (err) {
    console.warn("[SW] cacheFirst failed, no cache available:", request.url);
    // Return kosong agar tidak crash
    return new Response("", { status: 408 });
  }
}

// Network-First: coba network dulu, fallback ke cache
async function networkFirst(request) {
  try {
    const response = await fetch(request);
    return response;
  } catch (err) {
    console.log("[SW] Network failed, trying cache:", request.url);
    const cached = await caches.match(request);
    if (cached) return cached;
    // Tidak ada di cache → return error response
    return new Response(
      JSON.stringify({ error: "Offline dan tidak ada cache" }),
      { status: 503, headers: { "Content-Type": "application/json" } },
    );
  }
}

// ─── 4. BACKGROUND SYNC ───────────────────────────────────
// Dipanggil browser saat koneksi kembali
self.addEventListener("sync", (event) => {
  console.log("[SW] Background sync triggered:", event.tag);

  if (event.tag === "sync-konsultasi") {
    event.waitUntil(syncKonsultasi());
  }
});

// Fungsi sync: ambil data pending dari IndexedDB → kirim ke server
async function syncKonsultasi() {
  // Buka IndexedDB langsung dari SW context
  const db = await openDB();
  const pendingData = await getAllPending(db);

  console.log("[SW] Pending items to sync:", pendingData.length);

  for (const item of pendingData) {
    try {
      const response = await fetch("http://localhost:8000/api/konsultasi", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          local_id: item.id,
          nama: item.nama,
          keluhan: item.keluhan,
          created_at: item.created_at,
        }),
      });

      if (response.ok) {
        const result = await response.json();
        console.log("[SW] Sync success for:", item.id);
        // Update status di IndexedDB
        await updateStatus(db, item.id, "synced", result.server_id);
        // Notify client (tab yang terbuka)
        notifyClients({ type: "SYNC_SUCCESS", local_id: item.id });
      }
    } catch (err) {
      console.error("[SW] Sync failed for:", item.id, err);
      // Biarkan, akan dicoba lagi di sync berikutnya
    }
  }
}

// Notify semua tab yang terbuka
async function notifyClients(message) {
  const clients = await self.clients.matchAll();
  clients.forEach((client) => client.postMessage(message));
}

// ─── IndexedDB helpers (dari dalam SW) ────────────────────
function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open("telemedicine", 1);
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
    request.onupgradeneeded = (e) => {
      const db = e.target.result;
      if (!db.objectStoreNames.contains("konsultasi")) {
        const store = db.createObjectStore("konsultasi", { keyPath: "id" });
        store.createIndex("status", "status", { unique: false });
      }
    };
  });
}

function getAllPending(db) {
  return new Promise((resolve, reject) => {
    const tx = db.transaction("konsultasi", "readonly");
    const store = tx.objectStore("konsultasi");
    const index = store.index("status");
    const request = index.getAll("pending");
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

function updateStatus(db, id, status, server_id = null) {
  return new Promise((resolve, reject) => {
    const tx = db.transaction("konsultasi", "readwrite");
    const store = tx.objectStore("konsultasi");
    const getReq = store.get(id);
    getReq.onsuccess = () => {
      const item = getReq.result;
      item.status = status;
      if (server_id) item.server_id = server_id;
      item.synced_at = new Date().toISOString();
      store.put(item);
      resolve();
    };
    getReq.onerror = () => reject(getReq.error);
  });
}
