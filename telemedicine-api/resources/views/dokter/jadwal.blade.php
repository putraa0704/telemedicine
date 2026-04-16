@extends('layouts.app')

@section('title', 'Jadwal Dokter')
@section('page_title', 'Jadwal Dokter')
@section('page_sub', 'Lihat jadwal dokter yang tersedia')
@section('nav_jadwal', 'active')

@section('content')

{{-- Date Picker --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-5">
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Pilih Tanggal</label>
                <input type="date" id="tanggal-picker" value="{{ date('Y-m-d') }}"
                    class="text-sm font-medium text-slate-800 border-none outline-none bg-transparent p-0 mt-0.5"/>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span id="hari-label" class="text-[12px] text-slate-400 font-medium"></span>
            <button onclick="loadJadwal()"
                class="px-4 py-2 bg-brand-600 hover:bg-brand-800 text-white text-[12px] font-semibold rounded-xl transition-colors">
                Cari Jadwal
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-4">

    {{-- KIRI: Slot Hari Ini --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3.5 border-b border-slate-100">
                <h2 class="text-[13px] font-semibold text-slate-800">Jadwal Tersedia</h2>
            </div>
            <div id="jadwal-list" class="p-3 space-y-2">
                <div class="py-6 text-center text-[12px] text-slate-400">Memuat jadwal...</div>
            </div>
        </div>

        {{-- Tim Dokter --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3.5 border-b border-slate-100">
                <h2 class="text-[13px] font-semibold text-slate-800">Tim Dokter Aktif</h2>
            </div>
            <div id="dokter-list" class="p-3 space-y-2">
                <div class="py-4 text-center text-[12px] text-slate-400">Memuat...</div>
            </div>
        </div>
    </div>

    {{-- KANAN: Tabel Mingguan --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3.5 border-b border-slate-100">
            <h2 class="text-[13px] font-semibold text-slate-800">Jadwal Minggu Ini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[11px]" style="min-width:420px;">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold text-slate-500">Waktu</th>
                        <th class="px-2 py-3 text-center font-semibold text-slate-500">Senin</th>
                        <th class="px-2 py-3 text-center font-semibold text-slate-500">Selasa</th>
                        <th class="px-2 py-3 text-center font-semibold text-slate-500">Rabu</th>
                        <th class="px-2 py-3 text-center font-semibold text-slate-500">Kamis</th>
                        <th class="px-2 py-3 text-center font-semibold text-slate-500">Jumat</th>
                    </tr>
                </thead>
                <tbody id="tabel-mingguan" class="divide-y divide-slate-50">
                    <tr><td colspan="6" class="px-3 py-6 text-center text-slate-400">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    const HARI      = ['minggu','senin','selasa','rabu','kamis','jumat','sabtu'];
    const HARI_LABEL = { senin:'Senin', selasa:'Selasa', rabu:'Rabu', kamis:'Kamis', jumat:'Jumat', sabtu:'Sabtu', minggu:'Minggu' };

    async function loadJadwal() {
        var tanggal = document.getElementById('tanggal-picker').value;
        if (!tanggal) return;
        var hariIdx = new Date(tanggal + 'T12:00:00').getDay();
        document.getElementById('hari-label').textContent = HARI_LABEL[HARI[hariIdx]] || '';
        document.getElementById('jadwal-list').innerHTML = '<div class="py-4 text-center text-[12px] text-slate-400">Memuat...</div>';
        try {
            var res  = await fetch('/api/jadwal?tanggal=' + tanggal, { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            renderJadwal(data.jadwal || []);
        } catch(e) {
            document.getElementById('jadwal-list').innerHTML = '<div class="py-4 text-center text-[12px] text-red-400">Gagal memuat jadwal</div>';
        }
    }

    function renderJadwal(list) {
        var el = document.getElementById('jadwal-list');
        if (!list.length) {
            el.innerHTML = '<div class="py-8 text-center"><div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><p class="text-[12px] text-slate-400">Tidak ada jadwal hari ini</p></div>';
            return;
        }
        el.innerHTML = list.map(slot => `
            <div class="flex items-center justify-between px-3.5 py-3 rounded-xl border border-slate-100 hover:border-brand-200 hover:bg-blue-50/30 transition-all">
                <div>
                    <div class="text-[13px] font-semibold text-slate-800">${slot.waktu}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">${slot.dokter} · ${slot.spesialisasi || ''}</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">Tersedia</span>
                    <a href="/konsultasi/baru" class="text-[10px] font-semibold text-brand-600 hover:underline">Pesan</a>
                </div>
            </div>
        `).join('');
    }

    async function loadTimDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            document.getElementById('dokter-list').innerHTML = data.slice(0,4).map(dr => `
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-100 hover:border-brand-200 transition-all">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-[11px] font-bold flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[12px] font-semibold text-slate-800 truncate">${dr.nama}</div>
                        <div class="text-[10px] text-slate-400">${dr.spesialisasi} · ${dr.pasien_aktif} pasien</div>
                    </div>
                    <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                        ${dr.status === 'sibuk' ? 'Sibuk' : 'OK'}
                    </span>
                </div>
            `).join('');
        } catch(e) {
            document.getElementById('dokter-list').innerHTML = '<div class="py-4 text-center text-[12px] text-slate-400">Gagal memuat</div>';
        }
    }

    async function loadMingguan() {
        try {
            var res  = await fetch('/api/jadwal/mingguan', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            var hariList = ['senin','selasa','rabu','kamis','jumat'];
            var waktuSet = new Set();
            hariList.forEach(h => (data[h] || []).forEach(s => waktuSet.add(s.waktu)));
            var waktuList = [...waktuSet].sort();
            if (!waktuList.length) {
                document.getElementById('tabel-mingguan').innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-slate-400">Belum ada jadwal</td></tr>';
                return;
            }
            document.getElementById('tabel-mingguan').innerHTML = waktuList.map(waktu => `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-2.5 font-semibold text-slate-700 whitespace-nowrap">${waktu}</td>
                    ${hariList.map(h => {
                        var slot = (data[h] || []).find(s => s.waktu === waktu);
                        return '<td class="px-2 py-2.5 text-center">' +
                            (slot ? '<span class="text-[10px] font-medium text-slate-700 bg-blue-50 px-1.5 py-0.5 rounded-lg whitespace-nowrap">' + slot.dokter.replace('Dr. ','Dr.') + '</span>' : '<span class="text-slate-200">—</span>') +
                            '</td>';
                    }).join('')}
                </tr>
            `).join('');
        } catch(e) {
            document.getElementById('tabel-mingguan').innerHTML = '<tr><td colspan="6" class="px-3 py-4 text-center text-slate-400">Gagal memuat</td></tr>';
        }
    }

    loadJadwal();
    loadTimDokter();
    loadMingguan();
</script>
@endsection