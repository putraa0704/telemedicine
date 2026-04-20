@extends('layouts.app')

@section('title', 'Dashboard Pasien')
@section('page_title', 'Dashboard')
@section('page_sub', 'Selamat datang, pantau aktivitas kesehatan Anda')
@section('nav_dashboard', 'active')

@section('head')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total</div>
            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-slate-800 leading-none" id="stat-total">—</div>
        <div class="text-[11px] font-medium text-blue-500 mt-1" id="stat-today"></div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pending</div>
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-amber-600 leading-none" id="stat-pending">—</div>
        <div class="text-[11px] font-medium text-amber-500 mt-1">Belum dijawab</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Selesai</div>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-emerald-600 leading-none" id="stat-done">—</div>
        <div class="text-[11px] font-medium text-emerald-500 mt-1" id="stat-pct"></div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Offline</div>
            <div class="w-8 h-8 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                    <line x1="12" y1="20" x2="12.01" y2="20"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-violet-600 leading-none" id="stat-pending-badge">0</div>
        <div class="text-[11px] font-medium text-violet-500 mt-1">Data pending sync</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_1.6fr] gap-4">

    {{-- Antrian Sinkronisasi --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-4 py-3.5 border-b border-slate-100">
            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
            <span class="text-[13px] font-semibold text-slate-800">Antrian Sinkronisasi</span>
            <span id="pending-badge" class="ml-auto bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200">0</span>
        </div>
        <div class="p-3 max-h-60 overflow-y-auto" id="pending-list">
            <div class="flex flex-col items-center py-6 text-center">
                <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </div>
                <p class="text-[12px] text-slate-400">Semua data tersinkronisasi</p>
            </div>
        </div>
    </div>

    {{-- Konsultasi Terbaru --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Konsultasi Terbaru</span>
            <a href="/riwayat" class="text-[11px] font-semibold text-brand-600 hover:underline">Lihat semua →</a>
        </div>
        <div id="history-list" class="divide-y divide-slate-50 max-h-[360px] overflow-y-auto">
            <div class="px-4 py-8 text-center text-[12px] text-slate-400">Memuat riwayat...</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="/konsultasi/baru"
        class="flex items-center gap-3 bg-brand-600 hover:bg-brand-800 text-white rounded-2xl p-4 transition-colors">
        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
        </div>
        <div><div class="text-[12px] font-semibold">Konsultasi Baru</div><div class="text-[10px] text-white/70">Buat permintaan</div></div>
    </a>
    <a href="/jadwal"
        class="flex items-center gap-3 bg-white hover:bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl p-4 transition-colors shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
        </div>
        <div><div class="text-[12px] font-semibold">Jadwal</div><div class="text-[10px] text-slate-400">Lihat jadwal dokter</div></div>
    </a>
    <a href="/tim"
        class="flex items-center gap-3 bg-white hover:bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl p-4 transition-colors shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div><div class="text-[12px] font-semibold">Tim Dokter</div><div class="text-[10px] text-slate-400">Pilih dokter</div></div>
    </a>
    <a href="/riwayat"
        class="flex items-center gap-3 bg-white hover:bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl p-4 transition-colors shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div><div class="text-[12px] font-semibold">Riwayat</div><div class="text-[10px] text-slate-400">Konsultasi lalu</div></div>
    </a>
</div>

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

    async function syncAndRenderNow() {
        await syncPending();
        await renderUI();
    }

    window.addEventListener('online', async () => {
        // Jalankan beberapa kali agar UI cepat konsisten saat jaringan baru kembali.
        await syncAndRenderNow();
        setTimeout(syncAndRenderNow, 1200);
        setTimeout(syncAndRenderNow, 3200);
    });

    window.addEventListener('focus', async () => {
        if (navigator.onLine) await syncAndRenderNow();
    });

    document.addEventListener('visibilitychange', async () => {
        if (!document.hidden && navigator.onLine) await syncAndRenderNow();
    });
    setInterval(async () => {
        if (navigator.onLine) {
            var p = await db.konsultasi.where('status').equals('pending').toArray();
            if (p.length) { await syncAndRenderNow(); }
        }
    }, 30000);

    document.addEventListener('DOMContentLoaded', async () => {
        if (navigator.onLine) await syncAndRenderNow();
        else await renderUI();
    });

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
            } catch(e) {}
        }
    }

    async function renderUI() {
        var all     = await db.konsultasi.orderBy('created_at').reverse().toArray();
        var pending = all.filter(d => d.status === 'pending');
        var synced  = all.filter(d => d.status === 'synced');

        document.getElementById('stat-total').textContent   = all.length;
        document.getElementById('stat-pending').textContent = pending.length;
        document.getElementById('stat-done').textContent    = synced.length;
        document.getElementById('stat-pct').textContent     = all.length ? Math.round(synced.length / all.length * 100) + '% selesai' : '—';
        document.getElementById('stat-today').textContent   = '+' + all.filter(d => new Date(d.created_at) > new Date(Date.now() - 86400000)).length + ' hari ini';
        document.getElementById('stat-pending-badge').textContent = pending.length;
        document.getElementById('pending-badge').textContent = pending.length;

        // Pending list
        var pendingList = document.getElementById('pending-list');
        if (pending.length === 0) {
            pendingList.innerHTML = '<div class="flex flex-col items-center py-6 text-center"><div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center mb-2"><svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div><p class="text-[12px] text-slate-400">Semua data tersinkronisasi</p></div>';
        } else {
            pendingList.innerHTML = pending.map(item => `
                <div class="flex items-start justify-between bg-amber-50 rounded-xl px-3 py-2.5 mb-2 border border-amber-100">
                    <div class="flex-1 min-w-0 mr-2">
                        <p class="text-[12px] text-slate-700 truncate">${item.keluhan.substring(0,60)}${item.keluhan.length > 60 ? '…' : ''}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                    </div>
                    <span class="text-[10px] font-semibold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full flex-shrink-0">pending</span>
                </div>
            `).join('');
        }

        // History
        var histList = document.getElementById('history-list');
        if (navigator.onLine) {
            try {
                var res  = await fetch('/api/konsultasi/saya', { headers: { 'Authorization': 'Bearer ' + token } });
                var data = await res.json();
                if (!data.length) {
                    histList.innerHTML = '<div class="px-4 py-8 text-center"><p class="text-[12px] text-slate-400 mb-3">Belum ada konsultasi</p><a href="/konsultasi/baru" class="inline-block text-[12px] font-semibold text-brand-600 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition-colors">Mulai Konsultasi</a></div>';
                    return;
                }
                histList.innerHTML = data.slice(0,5).map(item => `
                    <div class="px-4 py-3 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-[12px] font-semibold text-slate-800">${item.nama || user.name}</span>
                            <span class="text-[10px] text-slate-400 flex-shrink-0 ml-2">${new Date(item.created_at).toLocaleDateString('id-ID')}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-1.5 line-clamp-1">${item.keluhan}</p>
                        <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full ${item.status === 'done' ? 'bg-emerald-50 text-emerald-700' : item.status === 'in_review' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700'}">${item.status === 'done' ? '✓ Selesai' : item.status === 'in_review' ? '⟳ Ditinjau' : '· Menunggu'}</span>
                        ${item.jawaban_dokter ? `<div class="mt-2 bg-blue-50 border-l-2 border-blue-400 rounded-r-lg px-2.5 py-1.5"><p class="text-[11px] text-blue-600 font-semibold mb-0.5">Jawaban Dokter:</p><p class="text-[11px] text-slate-600 line-clamp-2">${item.jawaban_dokter}</p></div>` : ''}
                    </div>
                `).join('');
            } catch(e) {
                histList.innerHTML = '<div class="px-4 py-4 text-[12px] text-red-400 text-center">Gagal memuat dari server</div>';
            }
        } else {
            histList.innerHTML = !synced.length
                ? '<div class="px-4 py-8 text-center text-[12px] text-slate-400">Offline — sambungkan internet untuk lihat riwayat</div>'
                : synced.slice(0,5).map(item => `
                    <div class="px-4 py-3 border-b border-slate-50 last:border-0">
                        <p class="text-[12px] text-slate-700 mb-1">${item.keluhan}</p>
                        <span class="text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">tersimpan lokal</span>
                    </div>
                `).join('');
        }
    }
    window.renderUI = renderUI;
</script>
@endsection