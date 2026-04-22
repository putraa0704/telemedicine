@extends('layouts.app')

@section('title', 'Riwayat Konsultasi')
@section('page_title', 'Riwayat')
@section('page_sub', 'Daftar semua konsultasi Anda')
@section('nav_riwayat', 'active')

@section('content')

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Total</div>
        <div class="text-2xl sm:text-3xl font-bold text-slate-800" id="stat-total">—</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Selesai</div>
        <div class="text-2xl sm:text-3xl font-bold text-emerald-600" id="stat-done">—</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Menunggu</div>
        <div class="text-2xl sm:text-3xl font-bold text-amber-600" id="stat-pending">—</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Dijawab</div>
        <div class="text-2xl sm:text-3xl font-bold text-blue-600" id="stat-answered">—</div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center gap-2">
        <span class="text-[13px] font-semibold text-slate-800 mr-auto">Riwayat Konsultasi</span>
        <div class="flex gap-1.5 flex-wrap">
            <button onclick="setFilter('all')" id="f-all" class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border transition-colors bg-brand-600 text-white border-brand-600">Semua</button>
            <button onclick="setFilter('done')" id="f-done" class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Selesai</button>
            <button onclick="setFilter('received')" id="f-received" class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Menunggu</button>
            <button onclick="setFilter('in_review')" id="f-in_review" class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">Ditinjau</button>
        </div>
        <button onclick="loadData()" class="text-[11px] font-semibold text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition-colors">↻</button>
    </div>
    <div id="riwayat-list">
        <div class="px-4 py-10 text-center text-[12px] text-slate-400">Memuat riwayat...</div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    var allData = [];
    var activeFilter = 'all';
    const db = new Dexie('telemedicine');
    db.version(2).stores({ konsultasi: 'id, status, created_at', auth: 'key' });

    async function loadLocalPending() {
        try {
            var pending = await db.konsultasi.where('status').equals('pending').reverse().sortBy('created_at');
            return pending.map(function(item) {
                return {
                    id: item.id,
                    local_id: item.id,
                    server_id: item.server_id || null,
                    nama: item.nama,
                    keluhan: item.keluhan,
                    status: 'received',
                    created_at: item.created_at,
                    is_local_pending: true,
                };
            });
        } catch (e) {
            return [];
        }
    }

    function formatKslId(item) {
        if (item.server_id) return '#KSL-' + String(item.server_id).padStart(3, '0');
        if (typeof item.id === 'number') return '#KSL-' + String(item.id).padStart(3, '0');
        return '#LOCAL';
    }

    function setFilter(f) {
        activeFilter = f;
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            btn.classList.remove('bg-brand-600','text-white','border-brand-600');
            btn.classList.add('text-slate-600','border-slate-200','hover:bg-slate-50');
        });
        var active = document.getElementById('f-' + f);
        if (active) {
            active.classList.add('bg-brand-600','text-white','border-brand-600');
            active.classList.remove('text-slate-600','border-slate-200','hover:bg-slate-50');
        }
        renderList();
    }

    function statusBadge(s) {
        if (s === 'done') return '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>';
        if (s === 'in_review') return '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">Ditinjau</span>';
        return '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Menunggu</span>';
    }

    function renderList() {
        var list = document.getElementById('riwayat-list');
        var filtered = activeFilter === 'all' ? allData : allData.filter(function(d) { return d.status === activeFilter; });

        if (!filtered.length) {
            list.innerHTML = '<div class="px-4 py-10 text-center"><p class="text-sm text-slate-400 mb-3">Belum ada konsultasi' + (activeFilter !== 'all' ? ' dengan status ini' : '') + '</p><a href="/konsultasi/baru" class="inline-block text-[12px] font-semibold text-brand-600 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-colors">+ Buat Konsultasi Baru</a></div>';
            return;
        }

        list.innerHTML = filtered.map(function(item) {
            var hasJawaban = item.jawaban_dokter || item.jawaban;
            var jawaban    = item.jawaban_dokter || item.jawaban || '';
            var dibuat     = new Date(item.created_at).toLocaleString('id-ID');
            var dokterName = item.dokter ? (item.dokter.name || item.dokter) : null;

            var jawabanHtml = hasJawaban
                ? '<div class="mx-4 sm:mx-5 mb-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-xl px-4 py-3"><p class="text-[11px] font-bold text-blue-700 mb-1">Jawaban Dokter' + (dokterName ? ' (' + dokterName + ')' : '') + ':</p><p class="text-[12px] text-slate-700 leading-relaxed">' + jawaban + '</p></div>'
                : '<div class="mx-4 sm:mx-5 mb-4 bg-slate-50 border-l-4 border-slate-200 rounded-r-xl px-4 py-2.5"><p class="text-[12px] text-slate-400 italic">Menunggu jawaban dokter...</p></div>';

            return '<div class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">' +
                '<div class="px-4 sm:px-5 py-4">' +
                    '<div class="flex flex-wrap items-start justify-between gap-2 mb-2">' +
                        '<div class="flex items-center gap-2 flex-wrap">' +
                            '<span class="text-[13px] font-semibold text-slate-800">' + (item.nama || item.nama_pasien || (user ? user.name : '')) + '</span>' +
                            '<span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">' + formatKslId(item) + '</span>' +
                            (item.is_local_pending ? '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">Offline</span>' : '') +
                        '</div>' +
                        '<span class="text-[10px] text-slate-400">' + dibuat + '</span>' +
                    '</div>' +
                    '<p class="text-[13px] text-slate-600 mb-3 leading-relaxed">' + item.keluhan + '</p>' +
                    '<div class="flex flex-wrap items-center gap-2">' + statusBadge(item.status) +
                        (dokterName ? '<span class="text-[11px] text-slate-400">· ' + dokterName + '</span>' : '') +
                    '</div>' +
                '</div>' +
                jawabanHtml + '</div>';
        }).join('');
    }

    async function loadData() {
        document.getElementById('riwayat-list').innerHTML = '<div class="px-4 py-10 text-center text-[12px] text-slate-400">Memuat...</div>';
        var localPending = await loadLocalPending();
        try {
            var res  = await fetch('/api/konsultasi/saya', { headers: { 'Authorization': 'Bearer ' + token } });
            var remoteData = await res.json();
            allData  = localPending.concat(remoteData || []);
            document.getElementById('stat-total').textContent    = allData.length;
            document.getElementById('stat-done').textContent     = allData.filter(function(d) { return d.status === 'done'; }).length;
            document.getElementById('stat-pending').textContent  = allData.filter(function(d) { return d.status === 'received'; }).length;
            document.getElementById('stat-answered').textContent = allData.filter(function(d) { return d.jawaban_dokter || d.jawaban; }).length;
            renderList();
        } catch(e) {
            allData = localPending;
            document.getElementById('stat-total').textContent    = allData.length;
            document.getElementById('stat-done').textContent     = 0;
            document.getElementById('stat-pending').textContent  = allData.length;
            document.getElementById('stat-answered').textContent = 0;
            if (allData.length) {
                renderList();
            } else {
                document.getElementById('riwayat-list').innerHTML = '<div class="px-4 py-10 text-center text-[12px] text-red-400">Gagal memuat data</div>';
            }
        }
    }
    window.addEventListener('online', loadData);
    loadData();
</script>
@endsection