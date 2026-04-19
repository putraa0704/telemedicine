@extends('layouts.app')

@section('title', 'Dashboard Dokter')
@section('page_title', 'Dashboard Dokter')
@section('page_sub', 'Ringkasan aktivitas praktik Anda')
@section('nav_dashboard', 'active')

@section('content')

<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total</div>
            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-slate-800" id="stat-total">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Total konsultasi</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Menunggu</div>
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-amber-600" id="stat-pending">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Perlu ditindaklanjuti</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Selesai</div>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-emerald-600" id="stat-done">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Hari ini</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Tindakan Cepat</span>
        </div>
        <div class="p-4 space-y-3">
            <a href="/dokter/konsultasi"
                class="flex items-center justify-between gap-3 bg-brand-600 hover:bg-brand-800 text-white rounded-xl px-4 py-3 transition-colors">
                <div>
                    <div class="text-[13px] font-semibold">Buka Konsultasi Pasien</div>
                    <div class="text-[11px] text-white/75">Lihat daftar konsultasi dan balas pasien</div>
                </div>
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 12h14M13 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="/tim"
                class="flex items-center justify-between gap-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl px-4 py-3 border border-slate-200 transition-colors">
                <div>
                    <div class="text-[13px] font-semibold">Lihat Jadwal Dokter</div>
                    <div class="text-[11px] text-slate-500">Pantau jadwal praktik tim dokter</div>
                </div>
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 12h14M13 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <span class="text-[13px] font-semibold text-slate-800">Status Cepat</span>
            <button onclick="loadRingkasan()" class="text-[11px] text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition-colors">↻ Refresh</button>
        </div>
        <div class="p-4 space-y-2.5 text-[12px]">
            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2">
                <span class="text-slate-500">Menunggu jawaban</span>
                <span class="font-semibold text-amber-600" id="quick-pending">—</span>
            </div>
            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2">
                <span class="text-slate-500">Selesai hari ini</span>
                <span class="font-semibold text-emerald-600" id="quick-done">—</span>
            </div>
            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2">
                <span class="text-slate-500">Total konsultasi</span>
                <span class="font-semibold text-slate-700" id="quick-total">—</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role === 'pasien') window.location.href = '/pasien';

    async function loadRingkasan() {
        try {
            var res  = await fetch('/api/dokter/konsultasi', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();

            document.getElementById('stat-total').textContent   = data.length;
            document.getElementById('stat-pending').textContent = data.filter(d => d.status !== 'done').length;
            document.getElementById('stat-done').textContent    = data.filter(d => {
                return d.status === 'done' && d.dijawab_at && new Date(d.dijawab_at) > new Date(Date.now() - 86400000);
            }).length;
            document.getElementById('quick-total').textContent = data.length;
            document.getElementById('quick-pending').textContent = data.filter(d => d.status !== 'done').length;
            document.getElementById('quick-done').textContent = data.filter(d => {
                return d.status === 'done' && d.dijawab_at && new Date(d.dijawab_at) > new Date(Date.now() - 86400000);
            }).length;
        } catch(e) {
            document.getElementById('quick-total').textContent = 'Gagal';
            document.getElementById('quick-pending').textContent = 'Gagal';
            document.getElementById('quick-done').textContent = 'Gagal';
        }
    }

    loadRingkasan();
    setInterval(loadRingkasan, 30000);
</script>
@endsection