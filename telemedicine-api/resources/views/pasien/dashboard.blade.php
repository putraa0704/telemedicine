@extends('layouts.app')
@section('title', 'Dashboard Pasien')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="card p-6" style="background:linear-gradient(135deg,#0d9488,#0284c7)">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white text-xl font-bold" id="avatar-initial">Z</div>
            <div>
                <p class="text-white/80 text-sm">Selamat datang,</p>
                <h1 class="text-white text-xl font-bold" id="user-name">Pasien</h1>
            </div>
            <div class="ml-auto text-right">
                <p class="text-white/70 text-xs">Status</p>
                <p class="text-white text-sm font-medium" id="conn-status">Online</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 bg-white rounded-xl p-1 card">
        <button onclick="showTab('konsultasi')" id="tab-konsultasi"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition tab-active">
            Konsultasi Baru
        </button>
        <button onclick="showTab('riwayat')" id="tab-riwayat"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition text-slate-500 hover:text-slate-700">
            Riwayat & Jawaban
        </button>
        <button onclick="showTab('pending')" id="tab-pending"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition text-slate-500 hover:text-slate-700 relative">
            Antrian
            <span id="pending-badge" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-amber-400 text-white text-xs rounded-full flex items-center justify-center"></span>
        </button>
    </div>

    {{-- Tab: Konsultasi Baru --}}
    <div id="panel-konsultasi" class="card p-6">
        <h2 class="font-semibold text-slate-700 mb-1">Form Konsultasi</h2>
        <p class="text-slate-400 text-xs mb-4">Ceritakan keluhan Anda secara detail agar dokter dapat memberikan saran yang tepat</p>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Keluhan Anda</label>
                <textarea id="keluhan" rows="5"
                    placeholder="Contoh: Saya mengalami sakit kepala sejak 2 hari yang lalu, disertai demam dan mual..."
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition resize-none"></textarea>
            </div>

            <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3 flex items-start gap-3">
                <span class="text-teal-500 mt-0.5">ℹ</span>
                <p class="text-xs text-teal-700">Form ini dapat diisi meskipun Anda sedang offline. Data akan otomatis terkirim saat koneksi kembali.</p>
            </div>

            <button onclick="submitKonsultasi()" id="submit-btn"
                class="w-full text-white font-medium py-3 rounded-xl text-sm transition hover:opacity-90 active:scale-95 flex items-center justify-center gap-2"
                style="background:linear-gradient(135deg,#0d9488,#0284c7)">
                <span>Kirim Konsultasi</span>
            </button>
        </div>
    </div>

    {{-- Tab: Riwayat --}}
    <div id="panel-riwayat" class="hidden space-y-3">
        <div id="history-list">
            <div class="card p-8 text-center text-slate-400 text-sm">Memuat riwayat...</div>
        </div>
    </div>

    {{-- Tab: Pending --}}
    <div id="panel-pending" class="hidden">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-slate-700">Antrian Sinkronisasi</h2>
                <span id="pending-count" class="text-xs bg-amber-100 text-amber-700 px-3 py-1 rounded-full font-medium">0 item</span>
            </div>
            <ul id="pending-list">
                <li class="text-sm text-slate-400 italic text-center py-4">Tidak ada data pending</li>
            </ul>
        </div>
    </div>

</div>

<style>
.tab-active { background: linear-gradient(135deg,#0d9488,#0284c7); color: white; }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
<script>
if (!token || !user) window.location.href = '/login';
if (user && user.role !== 'pasien') window.location.href = '/dokter';

// Update header
if (user) {
    document.getElementById('user-name').textContent = user.name;
    document.getElementById('avatar-initial').textContent = user.name.charAt(0).toUpperCase();
}

const API = 'http://localhost:8000/api';

const db = new Dexie('telemedicine');
db.version(2).stores({
    konsultasi: 'id, status, created_at',
    auth: 'key'
});

async function saveAuthToIndexedDB() {
    await db.table('auth').put({ key: 'session', token, user });
}
saveAuthToIndexedDB();

// Tab management
function showTab(name) {
    ['konsultasi','riwayat','pending'].forEach(t => {
        document.getElementById(`panel-${t}`).classList.add('hidden');
        const btn = document.getElementById(`tab-${t}`);
        btn.classList.remove('tab-active');
        btn.classList.add('text-slate-500');
    });
    document.getElementById(`panel-${name}`).classList.remove('hidden');
    const activeBtn = document.getElementById(`tab-${name}`);
    activeBtn.classList.add('tab-active');
    activeBtn.classList.remove('text-slate-500');

    if (name === 'riwayat') loadRiwayat();
    if (name === 'pending') loadPending();
}

// Online/offline
function updateConnStatus() {
    const el = document.getElementById('conn-status');
    el.textContent = navigator.onLine ? '🟢 Online' : '🔴 Offline';
}
window.addEventListener('online', async () => {
    updateConnStatus();
    await syncPending();
    await renderUI();
});
window.addEventListener('offline', updateConnStatus);
updateConnStatus();

// Polling
setInterval(async () => {
    if (navigator.onLine) {
        const pending = await db.konsultasi.where('status').equals('pending').toArray();
        if (pending.length > 0) {
            await syncPending();
            await renderUI();
        }
    }
}, 30000);

async function submitKonsultasi() {
    const keluhan = document.getElementById('keluhan').value.trim();
    if (!keluhan) {
        alert('Keluhan tidak boleh kosong');
        return;
    }

    const btn = document.getElementById('submit-btn');
    btn.innerHTML = '<span>Menyimpan...</span>';
    btn.disabled = true;

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
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            const reg = await navigator.serviceWorker.ready;
            await reg.sync.register('sync-konsultasi');
        }
    }

    btn.innerHTML = '<span>Kirim Konsultasi</span>';
    btn.disabled = false;

    await renderUI();
    showTab('riwayat');
}

async function syncPending() {
    const pending = await db.konsultasi.where('status').equals('pending').toArray();
    for (const item of pending) {
        try {
            const res = await fetch(`${API}/konsultasi`, {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
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
            console.warn('[App] Sync gagal:', item.id);
        }
    }
}

async function loadRiwayat() {
    const list = document.getElementById('history-list');
    if (navigator.onLine) {
        try {
            const res  = await fetch(`${API}/konsultasi/saya`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();

            if (data.length === 0) {
                list.innerHTML = `
                    <div class="card p-8 text-center">
                        <p class="text-4xl mb-3">📋</p>
                        <p class="text-slate-500 text-sm">Belum ada riwayat konsultasi</p>
                        <p class="text-slate-400 text-xs mt-1">Mulai konsultasi pertama Anda</p>
                    </div>`;
                return;
            }

            list.innerHTML = data.map(item => `
                <div class="card p-5 mb-3">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-xs text-slate-400">${new Date(item.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'})}</p>
                        </div>
                        <span class="badge-${item.status}">${item.status === 'done' ? 'Selesai' : 'Menunggu'}</span>
                    </div>

                    <div class="bg-slate-50 rounded-xl px-4 py-3 mb-3">
                        <p class="text-xs text-slate-400 mb-1 font-medium">KELUHAN</p>
                        <p class="text-sm text-slate-700">${item.keluhan}</p>
                    </div>

                    ${item.jawaban_dokter ? `
                        <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-teal-600 mb-1 font-medium">💊 JAWABAN DOKTER</p>
                            <p class="text-sm text-slate-700">${item.jawaban_dokter}</p>
                        </div>
                    ` : `
                        <div class="flex items-center gap-2 text-slate-400">
                            <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                            <p class="text-xs">Menunggu jawaban dokter...</p>
                        </div>
                    `}
                </div>
            `).join('');
        } catch (err) {
            list.innerHTML = '<div class="card p-6 text-center text-slate-400 text-sm">Gagal memuat data</div>';
        }
    } else {
        list.innerHTML = `
            <div class="card p-8 text-center">
                <p class="text-4xl mb-3">📵</p>
                <p class="text-slate-500 text-sm">Sedang offline</p>
                <p class="text-slate-400 text-xs mt-1">Sambungkan internet untuk melihat riwayat</p>
            </div>`;
    }
}

async function loadPending() {
    const pending = await db.konsultasi.where('status').equals('pending').orderBy('created_at').reverse().toArray();
    const list    = document.getElementById('pending-list');

    if (pending.length === 0) {
        list.innerHTML = '<li class="text-sm text-slate-400 italic text-center py-6">✅ Semua data sudah tersinkronisasi</li>';
    } else {
        list.innerHTML = pending.map(item => `
            <li class="flex items-start justify-between bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 mb-2">
                <div class="flex-1 mr-3">
                    <p class="text-sm text-slate-700">${item.keluhan.slice(0, 80)}${item.keluhan.length > 80 ? '...' : ''}</p>
                    <p class="text-xs text-slate-400 mt-1">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                </div>
                <span class="badge-pending shrink-0">pending</span>
            </li>
        `).join('');
    }
}

async function renderUI() {
    const pending = await db.konsultasi.where('status').equals('pending').toArray();
    const count   = pending.length;

    document.getElementById('pending-count').textContent = `${count} item`;

    const badge = document.getElementById('pending-badge');
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }

    // Refresh panel yang sedang aktif
    const riwayatPanel = document.getElementById('panel-riwayat');
    if (!riwayatPanel.classList.contains('hidden')) loadRiwayat();

    const pendingPanel = document.getElementById('panel-pending');
    if (!pendingPanel.classList.contains('hidden')) loadPending();
}

// Init
if (navigator.onLine) syncPending();
renderUI();
</script>
@endsection