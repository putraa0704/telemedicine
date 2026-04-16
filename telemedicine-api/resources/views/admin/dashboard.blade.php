@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')
@section('page_sub', 'Kelola seluruh sistem MediConnect')
@section('nav_admin', 'active')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total</div>
            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-slate-800" id="s-total">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Konsultasi</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Menunggu</div>
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-amber-600" id="s-pending">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Belum dijawab</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Selesai</div>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-emerald-600" id="s-done">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Konsultasi</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-2">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Dokter</div>
            <div class="w-8 h-8 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-violet-600" id="s-dokter">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Dokter aktif</div>
    </div>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-[1.6fr_1fr] gap-4">

    {{-- Konsultasi --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center gap-2 px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800 mr-auto">Semua Konsultasi</span>
            <select id="filter-status" onchange="loadKonsultasi()"
                class="text-[11px] border border-slate-200 rounded-lg px-2.5 py-1.5 outline-none bg-white">
                <option value="">Semua Status</option>
                <option value="received">Menunggu</option>
                <option value="in_review">Ditinjau</option>
                <option value="done">Selesai</option>
            </select>
            <button onclick="loadKonsultasi(); loadStats();" class="text-[11px] text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition-colors">↻</button>
        </div>
        <div id="konsultasi-list" class="divide-y divide-slate-50 max-h-[500px] overflow-y-auto">
            <div class="px-4 py-8 text-center text-[12px] text-slate-400">Memuat...</div>
        </div>
    </div>

    {{-- Tim Dokter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Tim Dokter</span>
            <a href="/admin/dokter/tambah" class="text-[11px] font-semibold text-brand-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg transition-colors">+ Tambah</a>
        </div>
        <div id="tim-list" class="divide-y divide-slate-50 max-h-[500px] overflow-y-auto">
            <div class="px-4 py-8 text-center text-[12px] text-slate-400">Memuat...</div>
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
            document.getElementById('s-total').textContent   = ksl.length;
            document.getElementById('s-pending').textContent = ksl.filter(k => k.status === 'received').length;
            document.getElementById('s-done').textContent    = ksl.filter(k => k.status === 'done').length;
            document.getElementById('s-dokter').textContent  = dr.filter(d => d.status === 'tersedia').length;
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
                list.innerHTML = '<div class="px-4 py-8 text-center text-[12px] text-slate-400 italic">Tidak ada konsultasi</div>';
                return;
            }
            list.innerHTML = data.map(item => `
                <div class="px-4 py-3.5 hover:bg-slate-50 transition-colors">
                    <div class="flex flex-wrap items-start justify-between gap-1 mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-[12px] font-semibold text-slate-800">${item.nama_pasien}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full">#${String(item.id).padStart(3,'0')}</span>
                        </div>
                        <span class="text-[10px] text-slate-400">${new Date(item.created_at).toLocaleDateString('id-ID')}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-2 line-clamp-1">${item.keluhan}</p>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ${item.status === 'done' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : item.status === 'in_review' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200'}">
                            ${item.status === 'done' ? 'Selesai' : item.status === 'in_review' ? 'Ditinjau' : 'Menunggu'}
                        </span>
                        ${item.dokter ? '<span class="text-[10px] text-slate-400">· ' + item.dokter + '</span>' : ''}
                        ${item.status !== 'done' ? '<button onclick="updateStatus(' + item.id + ',\'in_review\')" class="ml-auto text-[10px] font-semibold text-brand-600 hover:underline">Tinjau</button>' : ''}
                    </div>
                </div>
            `).join('');
        } catch(e) {
            list.innerHTML = '<div class="px-4 py-4 text-center text-[12px] text-red-400">Gagal memuat</div>';
        }
    }

    async function loadTimDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            var list = document.getElementById('tim-list');
            list.innerHTML = data.map(dr => `
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-[11px] font-bold flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[12px] font-semibold text-slate-800 truncate">${dr.nama}</div>
                        <div class="text-[10px] text-slate-400">${dr.spesialisasi} · ${dr.pasien_aktif} pasien</div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full ${dr.status === 'sibuk' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                            ${dr.status === 'sibuk' ? 'Sibuk' : 'OK'}
                        </span>
                        <a href="/admin/dokter/${dr.id}" class="text-[10px] text-brand-600 hover:underline">Detail</a>
                    </div>
                </div>
            `).join('');
        } catch(e) {}
    }

    async function updateStatus(id, status) {
        try {
            await fetch('/api/dokter/konsultasi/' + id + '/status', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ status })
            });
            loadKonsultasi(); loadStats();
        } catch(e) {}
    }

    loadStats(); loadKonsultasi(); loadTimDokter();
    setInterval(() => { loadStats(); loadKonsultasi(); }, 30000);
</script>
@endsection