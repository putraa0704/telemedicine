@extends('layouts.app')
@section('title', 'Dashboard Pasien')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-semibold text-gray-800">Dashboard Pasien</h1>
        <p class="text-gray-500 text-sm">Konsultasi kesehatan Anda</p>
    </div>

    {{-- Form Konsultasi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-medium text-gray-700 mb-4">Form Konsultasi Baru</h2>
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                <textarea id="keluhan" rows="4" placeholder="Deskripsikan keluhan Anda secara detail..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <button onclick="submitKonsultasi()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Kirim Konsultasi
            </button>
        </div>
    </div>

    {{-- Antrian Pending --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-medium text-gray-700 mb-3">
            Antrian Sinkronisasi
            <span id="pending-count" class="ml-2 bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">0</span>
        </h2>
        <ul id="pending-list" class="space-y-2">
            <li class="text-sm text-gray-400 italic">Tidak ada data pending</li>
        </ul>
    </div>

    {{-- Riwayat Konsultasi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-medium text-gray-700 mb-3">Riwayat Konsultasi</h2>
        <ul id="history-list" class="space-y-3">
            <li class="text-sm text-gray-400 italic">Memuat riwayat...</li>
        </ul>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
<script>
// Auth guard
// const token = localStorage.getItem('auth_token');
// const user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
if (!token || !user) window.location.href = '/login';
if (user && user.role !== 'pasien') window.location.href = '/dokter';

const API = 'http://localhost:8000/api';

// IndexedDB
const db = new Dexie('telemedicine');
db.version(2).stores({
    konsultasi: 'id, status, created_at',
    auth: 'key'
});

// Simpan token ke IndexedDB supaya SW bisa akses
async function saveAuthToIndexedDB() {
    await db.table('auth').put({
        key:   'session',
        token: token,
        user:  user
    });
}
saveAuthToIndexedDB();

// Register SW
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(console.error);
    navigator.serviceWorker.addEventListener('message', e => {
        if (e.data.type === 'SYNC_SUCCESS') renderUI();
    });
}

// Online/offline sync
window.addEventListener('online', async () => {
    console.log('[App] Kembali online, sync semua pending...');
    await syncPending();
    await renderUI();
});

// Polling fallback setiap 30 detik
setInterval(async () => {
    if (navigator.onLine) {
        const pending = await db.konsultasi
            .where('status').equals('pending').toArray();
        if (pending.length > 0) {
            console.log('[App] Polling sync:', pending.length, 'items');
            await syncPending();
            await renderUI();
        }
    }
}, 30000);

// Coba sync saat halaman pertama kali dibuka
document.addEventListener('DOMContentLoaded', async () => {
    if (navigator.onLine) {
        await syncPending();
        await renderUI();
    }
});

async function submitKonsultasi() {
    const keluhan = document.getElementById('keluhan').value.trim();
    if (!keluhan) return alert('Keluhan tidak boleh kosong');

    const data = {
        id:         'loc_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7),
        nama:       user.name,
        keluhan,
        status:     'pending',
        created_at: new Date().toISOString(),
        server_id:  null,
        synced_at:  null
    };

    await db.konsultasi.add(data);
    document.getElementById('keluhan').value = '';

    if (navigator.onLine) {
        await syncPending();
    } else {
        // Daftarkan background sync
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            const reg = await navigator.serviceWorker.ready;
            await reg.sync.register('sync-konsultasi');
        }
    }

    renderUI();
}

async function syncPending() {
    const pending = await db.konsultasi.where('status').equals('pending').toArray();
    for (const item of pending) {
        try {
            const res = await fetch(`${API}/konsultasi`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    local_id:   item.id,
                    nama:       item.nama,
                    keluhan:    item.keluhan,
                    created_at: item.created_at
                })
            });

            if (res.ok || res.status === 409) {
                const result = await res.json();
                await db.konsultasi.update(item.id, {
                    status:    'synced',
                    server_id: result.server_id,
                    synced_at: new Date().toISOString()
                });
            }
        } catch (err) {
            console.warn('[App] Sync gagal untuk:', item.id);
        }
    }
}

async function renderUI() {
    const all     = await db.konsultasi.orderBy('created_at').reverse().toArray();
    const pending = all.filter(d => d.status === 'pending');
    const synced  = all.filter(d => d.status === 'synced');

    // Pending count badge
    document.getElementById('pending-count').textContent = pending.length;

    // Pending list
    const pendingList = document.getElementById('pending-list');
    pendingList.innerHTML = pending.length === 0
        ? '<li class="text-sm text-gray-400 italic">Tidak ada data pending</li>'
        : pending.map(item => `
            <li class="flex items-start justify-between bg-yellow-50 rounded-lg px-4 py-3">
                <div>
                    <p class="text-sm text-gray-700">${item.keluhan.slice(0, 60)}...</p>
                    <p class="text-xs text-gray-400 mt-1">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                </div>
                <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded-full ml-3 shrink-0">pending</span>
            </li>
        `).join('');

    // History — ambil dari server kalau online, dari IndexedDB kalau offline
    const historyList = document.getElementById('history-list');
    if (navigator.onLine) {
        try {
            const res  = await fetch(`${API}/konsultasi/saya`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            historyList.innerHTML = data.length === 0
                ? '<li class="text-sm text-gray-400 italic">Belum ada riwayat</li>'
                : data.map(item => `
                    <li class="border border-gray-100 rounded-xl p-4">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-medium text-gray-700">${item.keluhan}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full ml-3 shrink-0 ${
                                item.status === 'done'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-blue-100 text-blue-700'
                            }">${item.status}</span>
                        </div>
                        ${item.jawaban_dokter
                            ? `<div class="bg-green-50 rounded-lg px-3 py-2 mt-2">
                                <p class="text-xs text-green-600 font-medium mb-1">Jawaban Dokter:</p>
                                <p class="text-sm text-gray-700">${item.jawaban_dokter}</p>
                               </div>`
                            : '<p class="text-xs text-gray-400 mt-1">Menunggu jawaban dokter...</p>'
                        }
                        <p class="text-xs text-gray-400 mt-2">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                    </li>
                `).join('');
        } catch (err) {
            historyList.innerHTML = '<li class="text-sm text-gray-400 italic">Gagal memuat dari server</li>';
        }
    } else {
        historyList.innerHTML = synced.length === 0
            ? '<li class="text-sm text-gray-400 italic">Data offline — sambungkan internet untuk lihat riwayat lengkap</li>'
            : synced.map(item => `
                <li class="border border-gray-100 rounded-xl p-4">
                    <p class="text-sm text-gray-700">${item.keluhan}</p>
                    <p class="text-xs text-gray-400 mt-1">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">tersimpan lokal</span>
                </li>
            `).join('');
    }
}

renderUI();
</script>
@endsection