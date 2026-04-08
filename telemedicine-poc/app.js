// ─── Setup Dexie.js (IndexedDB wrapper) ───────────────────
const db = new Dexie("telemedicine");

db.version(1).stores({
  // id = primary key, status dan created_at bisa di-query
  konsultasi: "id, status, created_at",
});

// ─── Register Service Worker ───────────────────────────────
if ("serviceWorker" in navigator) {
  navigator.serviceWorker
    .register("/sw.js")
    .then((reg) => {
      console.log("[App] SW registered:", reg.scope);
    })
    .catch((err) => {
      console.error("[App] SW registration failed:", err);
    });

  // Dengarkan pesan dari SW (saat sync berhasil)
  navigator.serviceWorker.addEventListener("message", (event) => {
    if (event.data.type === "SYNC_SUCCESS") {
      console.log("[App] Sync success notif dari SW:", event.data.local_id);
      renderUI(); // Refresh tampilan
    }
  });
}

// ─── Monitor status online/offline ────────────────────────
function updateStatusBar() {
  const bar = document.getElementById("status-bar");
  const text = document.getElementById("status-text");

  if (navigator.onLine) {
    bar.className = "status-online";
    text.textContent = "🟢 Online";
  } else {
    bar.className = "status-offline";
    text.textContent = "🔴 Offline — data disimpan lokal";
  }
}

window.addEventListener('online', async () => {
  updateStatusBar();
  console.log('[App] Kembali online, mulai sync...');

  // Langsung sync semua pending tanpa tunggu Background Sync
  const pending = await db.konsultasi
    .where('status').equals('pending')
    .toArray();

  console.log('[App] Pending items:', pending.length);

  for (const item of pending) {
    await trySendDirect(item);
  }

  renderUI();

  // Tetap daftarkan Background Sync sebagai backup
  triggerBackgroundSync();
});
window.addEventListener("offline", updateStatusBar);
updateStatusBar(); // Cek status awal

// ─── Submit form ───────────────────────────────────────────
document
  .getElementById("konsultasi-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = {
      // Gunakan UUID sederhana sebagai local ID
      id: "loc_" + Date.now() + "_" + Math.random().toString(36).slice(2, 7),
      nama: document.getElementById("nama").value,
      keluhan: document.getElementById("keluhan").value,
      status: "pending", // pending | synced | failed
      created_at: new Date().toISOString(),
      server_id: null,
      synced_at: null,
    };

    // Simpan ke IndexedDB dulu (selalu, online maupun offline)
    await db.konsultasi.add(data);
    console.log("[App] Saved to IndexedDB:", data.id);

    // Reset form
    e.target.reset();

    // Coba kirim ke server kalau online
    if (navigator.onLine) {
      await trySendDirect(data);
    } else {
      // Daftarkan Background Sync
      await triggerBackgroundSync();
    }

    renderUI();
  });

document.getElementById("sync-btn").addEventListener("click", async () => {
  console.log("[App] Manual sync triggered");
  await triggerBackgroundSync();
  // Fallback langsung
  const pending = await db.konsultasi
    .where("status")
    .equals("pending")
    .toArray();
  for (const item of pending) {
    await trySendDirect(item);
  }
  renderUI();
});

// ─── Kirim langsung ke server (saat online) ───────────────
async function trySendDirect(data) {
  try {
    const response = await fetch("/api/konsultasi", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (response.ok) {
      const result = await response.json();
      // Update status di IndexedDB
      await db.konsultasi.update(data.id, {
        status: "synced",
        server_id: result.server_id,
        synced_at: new Date().toISOString(),
      });
      console.log("[App] Direct send success:", result.server_id);
    }
  } catch (err) {
    console.warn("[App] Direct send failed, akan dicoba via Background Sync");
    await triggerBackgroundSync();
  }
}

// ─── Daftarkan Background Sync ke SW ──────────────────────
async function triggerBackgroundSync() {
  if ("serviceWorker" in navigator && "SyncManager" in window) {
    const reg = await navigator.serviceWorker.ready;
    await reg.sync.register("sync-konsultasi");
    console.log("[App] Background Sync registered");
  } else {
    // Fallback: polling setiap 10 detik
    console.warn(
      "[App] Background Sync tidak didukung, pakai fallback polling",
    );
    setTimeout(pollSync, 10000);
  }
}

// Fallback untuk browser yang tidak support Background Sync (Safari)
async function pollSync() {
  if (!navigator.onLine) return;
  const pending = await db.konsultasi
    .where("status")
    .equals("pending")
    .toArray();
  for (const item of pending) {
    await trySendDirect(item);
  }
  renderUI();
}

// ─── Render UI dari IndexedDB ──────────────────────────────
async function renderUI() {
  const allData = await db.konsultasi.orderBy("created_at").reverse().toArray();

  const pending = allData.filter((d) => d.status === "pending");
  const synced = allData.filter((d) => d.status === "synced");

  // Render antrian pending
  const queueList = document.getElementById("queue-list");
  if (pending.length === 0) {
    queueList.innerHTML = '<li class="empty">Tidak ada data pending</li>';
  } else {
    queueList.innerHTML = pending
      .map(
        (item) => `
      <li>
        <strong>${item.nama}</strong> — ${item.keluhan.slice(0, 40)}...
        <span class="badge-pending">pending</span>
        <br><small>${new Date(item.created_at).toLocaleString("id-ID")}</small>
      </li>
    `,
      )
      .join("");
  }

  // Render riwayat yang sudah tersinkronisasi
  const historyList = document.getElementById("history-list");
  if (synced.length === 0) {
    historyList.innerHTML = '<li class="empty">Belum ada riwayat</li>';
  } else {
    historyList.innerHTML = synced
      .map(
        (item) => `
      <li>
        <strong>${item.nama}</strong> — ${item.keluhan.slice(0, 40)}...
        <span class="badge-synced">tersinkronisasi</span>
        <br><small>Server ID: ${item.server_id} | ${new Date(item.synced_at).toLocaleString("id-ID")}</small>
      </li>
    `,
      )
      .join("");
  }
}

// Render pertama kali
renderUI();
