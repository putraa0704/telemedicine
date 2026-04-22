@extends('layouts.app')

@section('title', 'Konsultasi')
@section('page_title', 'Konsultasi')
@section('page_sub', 'Daftar semua konsultasi')
@section('nav_konsultasi', 'active-nav')

@section('content')

<div class="flex justify-between items-center mb-4">
    <span class="text-[13px] font-semibold text-slate-800">Semua Konsultasi</span>
    <select id="filter-status" onchange="filterList()"
        class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white">
        <option value="">Semua Status</option>
        <option value="received">Menunggu</option>
        <option value="done">Selesai</option>
    </select>
</div>

<div class="bg-white rounded-xl border border-slate-200" id="ksl-list">
    <p class="px-4 py-4 text-sm text-slate-400">Memuat...</p>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role === 'dokter') window.location.href = '/dokter/konsultasi';

    var allData = [];
    const db = new Dexie('telemedicine');
    db.version(2).stores({ konsultasi: 'id, status, created_at', auth: 'key' });

    function formatKslId(item) {
        if (item.server_id) return '#KSL-' + String(item.server_id).padStart(3, '0');
        if (typeof item.id === 'number') return '#KSL-' + String(item.id).padStart(3, '0');
        return '#LOCAL';
    }

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

    function badgeClass(s) {
        if (s === 'done')      return 'bg-teal-50 text-teal-700';
        if (s === 'in_review') return 'bg-blue-50 text-blue-700';
        return 'bg-amber-50 text-amber-700';
    }
    function badgeLabel(s) {
        if (s === 'done')      return 'Selesai';
        if (s === 'in_review') return 'Ditinjau';
        return 'Menunggu';
    }

    function renderList(data) {
        var list = document.getElementById('ksl-list');
        if (!data.length) {
            list.innerHTML = '<p class="px-4 py-4 text-sm text-slate-400 italic">Tidak ada konsultasi</p>';
            return;
        }
        list.innerHTML = data.map(item => `
            <div class="px-4 py-3 border-b border-slate-100 last:border-0">
                <div class="flex justify-between items-start mb-1.5">
                    <div>
                        <span class="text-[13px] font-semibold text-slate-800">${item.nama_pasien || item.nama || '—'}</span>
                        <span class="text-[11px] text-slate-400 ml-1">${formatKslId(item)}</span>
                    </div>
                    <span class="text-[11px] text-slate-400">${new Date(item.created_at).toLocaleString('id-ID')}</span>
                </div>
                <p class="text-[12px] text-slate-600 mb-2">${item.keluhan}</p>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ${badgeClass(item.status)}">${badgeLabel(item.status)}</span>
                ${item.is_local_pending ? '<span class="ml-2 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">Offline</span>' : ''}
                ${item.jawaban ? `
                <div class="mt-2.5 bg-blue-50 border-l-2 border-blue-400 rounded-r-lg px-3 py-2">
                    <p class="text-[11px] font-bold text-blue-600 mb-0.5">Jawaban Dokter:</p>
                    <p class="text-[12px] text-slate-700">${item.jawaban}</p>
                </div>` : ''}
            </div>
        `).join('');
    }

    function filterList() {
        var val = document.getElementById('filter-status').value;
        renderList(val ? allData.filter(d => d.status === val) : allData);
    }

    async function load() {
        var localPending = await loadLocalPending();
        try {
            var url  = user.role === 'pasien' ? '/api/konsultasi/saya' : '/api/dokter/konsultasi';
            var res  = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
            var remoteData = await res.json();
            allData = localPending.concat(remoteData || []);
            renderList(allData);
        } catch(e) {
            allData = localPending;
            if (allData.length) {
                renderList(allData);
            } else {
                document.getElementById('ksl-list').innerHTML = '<p class="px-4 py-4 text-sm text-slate-400">Gagal memuat data</p>';
            }
        }
    }
    window.addEventListener('online', load);
    load();
</script>
@endsection