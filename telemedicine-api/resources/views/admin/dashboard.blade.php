@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')
@section('page_sub', 'Kelola seluruh sistem CareMate')
@section('nav_admin', 'active')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
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
<div class="grid grid-cols-1 gap-4">

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
            var res = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            var dr  = await res.json();
            document.getElementById('s-dokter').textContent  = dr.filter(d => d.status === 'tersedia').length;
        } catch(e) {}
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

    loadStats(); loadTimDokter();
    setInterval(() => { loadStats(); loadTimDokter(); }, 30000);
</script>
@endsection