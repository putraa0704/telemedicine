@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')
@section('page_sub', 'Kelola seluruh sistem MediConnect')
@section('nav_dashboard', 'active-nav')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Total Konsultasi</div>
        <div class="text-3xl font-bold text-slate-800 leading-none" id="s-total">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Menunggu</div>
        <div class="text-3xl font-bold text-amber-600 leading-none" id="s-pending">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Selesai</div>
        <div class="text-3xl font-bold text-teal-600 leading-none" id="s-done">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Dokter Aktif</div>
        <div class="text-3xl font-bold text-blue-700 leading-none" id="s-dokter">—</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-[1.5fr_1fr] gap-4">

    {{-- Semua Konsultasi --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Semua Konsultasi</span>
            <select id="filter-status" onchange="loadKonsultasi()"
                class="text-xs border border-slate-200 rounded-lg px-2 py-1 outline-none bg-white">
                <option value="">Semua</option>
                <option value="received">Menunggu</option>
                <option value="in_review">Ditinjau</option>
                <option value="done">Selesai</option>
            </select>
        </div>
        <div id="konsultasi-list" class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
            <div class="px-4 py-4 text-sm text-slate-400">Memuat...</div>
        </div>
    </div>

    {{-- Tim Dokter --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Tim Dokter</span>
            <a href="/admin/dokter/tambah" class="text-[11px] font-semibold text-blue-700 hover:underline">+ Tambah</a>
        </div>
        <div id="tim-list" class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
            <div class="px-4 py-4 text-sm text-slate-400">Memuat...</div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role !== 'admin') window.location.href = '/pasien';

    async function loadStats() {
        try {
            var [kslRes, drRes] = await Promise.all([
                fetch('/api/konsultasi', { headers: { 'Authorization': 'Bearer ' + token } }),
                fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } }),
            ]);
            var ksl = await kslRes.json();
            var dr  = await drRes.json();

            document.getElementById('s-total').textContent  = ksl.length;
            document.getElementById('s-pending').textContent= ksl.filter(k => k.status === 'received').length;
            document.getElementById('s-done').textContent   = ksl.filter(k => k.status === 'done').length;
            document.getElementById('s-dokter').textContent = dr.filter(d => d.status === 'tersedia').length;
        } catch(e) {}
    }

    async function loadKonsultasi() {
        var status = document.getElementById('filter-status').value;
        var url    = '/api/dokter/konsultasi' + (status ? '?status=' + status : '');
        var list   = document.getElementById('konsultasi-list');

        try {
            var res  = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();

            if (!data.length) {
                list.innerHTML = '<div class="px-4 py-4 text-sm text-slate-400 italic">Tidak ada konsultasi</div>';
                return;
            }

            list.innerHTML = data.map(item => `
                <div class="px-4 py-3 hover:bg-slate-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <div>
                            <span class="text-[13px] font-semibold text-slate-800">${item.nama_pasien}</span>
                            <span class="text-[11px] text-slate-400 ml-1">#${String(item.id).padStart(3,'0')}</span>
                        </div>
                        <span class="text-[11px] text-slate-400">${new Date(item.created_at).toLocaleDateString('id-ID')}</span>
                    </div>
                    <p class="text-[12px] text-slate-600 mb-2 line-clamp-1">${item.keluhan}</p>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                            ${item.status === 'done' ? 'bg-teal-50 text-teal-700' : item.status === 'in_review' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700'}">
                            ${item.status === 'done' ? 'Selesai' : item.status === 'in_review' ? 'Ditinjau' : 'Menunggu'}
                        </span>
                        ${item.dokter ? `<span class="text-[11px] text-slate-400">${item.dokter}</span>` : ''}
                        ${item.status !== 'done'
                            ? `<button onclick="updateStatus(${item.id}, 'in_review')"
                                   class="ml-auto text-[10px] text-blue-600 hover:underline">Tinjau</button>`
                            : ''
                        }
                    </div>
                </div>
            `).join('');
        } catch(e) {
            list.innerHTML = '<div class="px-4 py-4 text-sm text-red-400">Gagal memuat</div>';
        }
    }

    async function loadTimDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            var list = document.getElementById('tim-list');

            list.innerHTML = data.map(dr => `
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-800 truncate">${dr.nama}</div>
                        <div class="text-[11px] text-slate-400">${dr.spesialisasi} · ${dr.pasien_aktif} pasien</div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                            ${dr.status === 'sibuk' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-teal-50 text-teal-700 border border-teal-200'}">
                            ${dr.status === 'sibuk' ? 'Sibuk' : 'Tersedia'}
                        </span>
                        <a href="/admin/dokter/${dr.id}" class="text-[10px] text-blue-600 hover:underline">Detail</a>
                    </div>
                </div>
            `).join('');
        } catch(e) {
            document.getElementById('tim-list').innerHTML = '<div class="px-4 py-4 text-sm text-red-400">Gagal memuat</div>';
        }
    }

    async function updateStatus(id, status) {
        try {
            await fetch('/api/dokter/konsultasi/' + id + '/status', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ status })
            });
            loadKonsultasi();
            loadStats();
        } catch(e) {}
    }

    // Init & auto refresh 30s
    loadStats();
    loadKonsultasi();
    loadTimDokter();
    setInterval(() => { loadStats(); loadKonsultasi(); }, 30000);
</script>
@endsection