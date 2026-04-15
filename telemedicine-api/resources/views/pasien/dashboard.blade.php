@extends('layouts.app')

@section('title', 'Dashboard Pasien')
@section('page_title', 'Dashboard')
@section('page_sub', 'Ringkasan aktivitas hari ini')
@section('nav_dashboard', 'active-nav')

@section('head')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Total Konsultasi</div>
        <div class="text-3xl font-bold text-slate-800 leading-none mb-1" id="stat-total">—</div>
        <div class="text-[11px] font-medium text-teal-500" id="stat-today"></div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Menunggu Jawaban</div>
        <div class="text-3xl font-bold text-amber-600 leading-none mb-1" id="stat-pending">—</div>
        <div class="text-[11px] font-medium text-amber-500">Belum dijawab</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Selesai</div>
        <div class="text-3xl font-bold text-slate-800 leading-none mb-1" id="stat-done">—</div>
        <div class="text-[11px] font-medium text-blue-600" id="stat-pct"></div>
    </div>
</div>

{{-- Grid 2 col --}}
<div class="grid grid-cols-[1fr_1.4fr] gap-4 mb-4">

    {{-- Pending Queue --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100">
            <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
            <span class="text-[13px] font-semibold text-slate-800">Antrian Sinkronisasi</span>
            <span id="pending-badge" class="ml-auto bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-full">0</span>
        </div>
        <div class="p-3" id="pending-list">
            <p class="text-[12px] text-slate-400 italic py-2">Tidak ada data pending</p>
        </div>
    </div>

    {{-- Recent Konsultasi --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-4 py-3 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Konsultasi Terbaru</span>
        </div>
        <div id="history-list">
            <p class="px-4 py-4 text-[12px] text-slate-400 italic">Memuat riwayat...</p>
        </div>
    </div>
</div>

{{-- Form Konsultasi Baru --}}
{{-- <div class="bg-white rounded-xl border border-slate-200 p-5 max-w-xl">
    <h2 class="text-[13px] font-semibold text-slate-800 mb-4">Konsultasi Baru</h2>
    <div class="space-y-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Keluhan Utama</label>
            <textarea id="keluhan" rows="4" placeholder="Deskripsikan gejala atau keluhan Anda secara detail..."
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 resize-none transition"></textarea>
        </div>
        <button onclick="submitKonsultasi()"
            class="w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
            Kirim Konsultasi
        </button>
    </div>
</div> --}}

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role !== 'pasien') window.location.href = '/dokter';

    const db = new Dexie('telemedicine');
    db.version(2).stores({ konsultasi: 'id, status, created_at', auth: 'key' });

    db.table('auth').put({ key: 'session', token, user });

    window.addEventListener('online', async () => { await syncPending(); await renderUI(); });

    setInterval(async () => {
        if (navigator.onLine) {
            var p = await db.konsultasi.where('status').equals('pending').toArray();
            if (p.length) { await syncPending(); await renderUI(); }
        }
    }, 30000);

    document.addEventListener('DOMContentLoaded', async () => {
        if (navigator.onLine) await syncPending();
        await renderUI();
    });

    async function submitKonsultasi() {
        var keluhan = document.getElementById('keluhan').value.trim();
        if (!keluhan) return alert('Keluhan tidak boleh kosong');

        var data = {
            id: 'loc_' + Date.now() + '_' + Math.random().toString(36).slice(2,7),
            nama: user.name, keluhan, status: 'pending',
            created_at: new Date().toISOString(), server_id: null, synced_at: null
        };
        await db.konsultasi.add(data);
        document.getElementById('keluhan').value = '';

        if (navigator.onLine) {
            await syncPending();
        } else if ('serviceWorker' in navigator && 'SyncManager' in window) {
            var reg = await navigator.serviceWorker.ready;
            await reg.sync.register('sync-konsultasi');
        }
        renderUI();
    }

    async function syncPending() {
        var pending = await db.konsultasi.where('status').equals('pending').toArray();
        for (var item of pending) {
            try {
                var res = await fetch('/api/konsultasi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ local_id: item.id, nama: item.nama, keluhan: item.keluhan, created_at: item.created_at })
                });
                if (res.ok || res.status === 409) {
                    var result = await res.json();
                    await db.konsultasi.update(item.id, { status: 'synced', server_id: result.server_id, synced_at: new Date().toISOString() });
                }
            } catch(e) { console.warn('[App] Sync failed:', item.id); }
        }
    }

    async function renderUI() {
        var all     = await db.konsultasi.orderBy('created_at').reverse().toArray();
        var pending = all.filter(d => d.status === 'pending');
        var synced  = all.filter(d => d.status === 'synced');

        document.getElementById('stat-total').textContent   = all.length;
        document.getElementById('stat-pending').textContent = pending.length;
        document.getElementById('stat-done').textContent    = synced.length;
        document.getElementById('stat-pct').textContent     = all.length ? Math.round(synced.length / all.length * 100) + '% tingkat penyelesaian' : '—';
        document.getElementById('stat-today').textContent   = '+' + all.filter(d => new Date(d.created_at) > new Date(Date.now() - 86400000)).length + ' hari ini';
        document.getElementById('pending-badge').textContent = pending.length;

        // Pending list
        var pendingList = document.getElementById('pending-list');
        pendingList.innerHTML = pending.length === 0
            ? '<p class="text-[12px] text-slate-400 italic py-2">Tidak ada data pending</p>'
            : pending.map(item => `
                <div class="flex items-start justify-between bg-amber-50 rounded-lg px-3 py-2.5 mb-2">
                    <div>
                        <p class="text-[12px] text-slate-700">${item.keluhan.substring(0,55)}${item.keluhan.length > 55 ? '…' : ''}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                    </div>
                    <span class="text-[10px] font-semibold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full ml-2 flex-shrink-0">pending</span>
                </div>
            `).join('');

        // History
        var histList = document.getElementById('history-list');
        if (navigator.onLine) {
            try {
                var res  = await fetch('/api/konsultasi/saya', { headers: { 'Authorization': 'Bearer ' + token } });
                var data = await res.json();
                histList.innerHTML = !data.length
                    ? '<p class="px-4 py-4 text-[12px] text-slate-400 italic">Belum ada riwayat konsultasi</p>'
                    : data.slice(0,5).map(item => `
                        <div class="px-4 py-3 border-b border-slate-100 last:border-0">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[13px] font-semibold text-slate-800">${item.nama || user.name}</span>
                                <span class="text-[11px] text-slate-400">${new Date(item.created_at).toLocaleString('id-ID')}</span>
                            </div>
                            <p class="text-[12px] text-slate-600 mb-2">${item.keluhan}</p>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ${item.status === 'done' ? 'bg-teal-50 text-teal-700' : 'bg-amber-50 text-amber-700'}">${item.status === 'done' ? 'Selesai' : 'Menunggu'}</span>
                            ${item.jawaban_dokter ? `
                            <div class="mt-2 bg-blue-50 border-l-2 border-blue-400 rounded-r-lg px-3 py-2">
                                <p class="text-[11px] font-bold text-blue-600 mb-0.5">Jawaban Dokter:</p>
                                <p class="text-[12px] text-slate-700">${item.jawaban_dokter}</p>
                            </div>` : ''}
                        </div>
                    `).join('');
            } catch(e) {
                histList.innerHTML = '<p class="px-4 py-4 text-[12px] text-slate-400">Gagal memuat dari server</p>';
            }
        } else {
            histList.innerHTML = !synced.length
                ? '<p class="px-4 py-4 text-[12px] text-slate-400 italic">Offline — sambungkan internet untuk lihat riwayat</p>'
                : synced.slice(0,5).map(item => `
                    <div class="px-4 py-3 border-b border-slate-100 last:border-0">
                        <p class="text-[12px] text-slate-700 mb-1">${item.keluhan}</p>
                        <span class="text-[10px] font-semibold bg-teal-50 text-teal-700 px-2 py-0.5 rounded-full">tersimpan lokal</span>
                    </div>
                `).join('');
        }
    }

    window.renderUI = renderUI;
</script>
@endsection