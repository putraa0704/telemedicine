@extends('layouts.app')

@section('title', 'Jadwal Saya')
@section('page_title', 'Jadwal Saya')
@section('page_sub', 'Lihat jadwal praktik Anda (hanya baca)')

@section('content')

<div class="bg-amber-50 border border-amber-200 text-amber-800 text-[12px] px-4 py-3 rounded-xl mb-4">
    Jadwal praktik hanya dapat diatur oleh admin.
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5" id="stat-cards">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm col-span-2 sm:col-span-1">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Hari Ini</div>
        <div class="text-xl font-bold text-brand-600" id="stat-hari">—</div>
        <div class="text-[11px] text-slate-400 mt-1" id="stat-tanggal">—</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Jadwal Aktif</div>
        <div class="text-2xl font-bold text-emerald-600" id="stat-aktif">—</div>
        <div class="text-[11px] text-slate-400 mt-1">slot/minggu</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">status online dokter</div>
        <div class="text-2xl font-bold text-slate-800" id="stat-hari-praktik">—</div>
        <div class="text-[11px] text-slate-400 mt-1">hari/minggu</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Jam Hari Ini</div>
        <div class="text-[13px] font-bold text-amber-600" id="stat-jam-hari-ini">—</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="space-y-3" id="jadwal-list-left">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 text-center text-slate-400 text-sm">
            Memuat jadwal...
        </div>
    </div>
    <div class="space-y-3" id="jadwal-list-right"></div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role === 'pasien') window.location.href = '/pasien';

    const HARI_LIST = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
    const HARI_LABEL = { senin:'Senin', selasa:'Selasa', rabu:'Rabu', kamis:'Kamis', jumat:'Jumat', sabtu:'Sabtu', minggu:'Minggu' };
    const DAY_TO_NAMA = ['minggu','senin','selasa','rabu','kamis','jumat','sabtu'];

    var todayNama = DAY_TO_NAMA[new Date().getDay()];
    var todayFull = new Date().toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    document.getElementById('stat-hari').textContent = HARI_LABEL[todayNama] || '—';
    document.getElementById('stat-tanggal').textContent = todayFull;

    function renderJadwalCards(grouped) {
        var leftHtml = '';
        var rightHtml = '';

        HARI_LIST.forEach(function(hari, idx) {
            var slots = grouped[hari] || [];
            var isToday = hari === todayNama;
            var hasPraktik = slots.length > 0;

            var card = '<div class="bg-white rounded-2xl border ' +
                (isToday ? 'border-brand-300 shadow-md ring-2 ring-brand-100' : 'border-slate-100 shadow-sm') +
                ' overflow-hidden">' +
                '<div class="px-4 py-3 flex items-center justify-between ' +
                (isToday ? 'bg-brand-600' : 'bg-slate-50') + '">' +
                '<div class="flex items-center gap-2">';

            if (isToday) {
                card += '<span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>';
                card += '<span class="text-[13px] font-bold text-white">' + HARI_LABEL[hari] + '</span>';
                card += '<span class="text-[10px] bg-white/20 text-white px-2 py-0.5 rounded-full font-semibold">Hari ini</span>';
            } else {
                card += '<span class="w-2 h-2 rounded-full ' + (hasPraktik ? 'bg-emerald-400' : 'bg-slate-300') + '"></span>';
                card += '<span class="text-[13px] font-semibold ' + (hasPraktik ? 'text-slate-800' : 'text-slate-400') + '">' + HARI_LABEL[hari] + '</span>';
            }

            card += '</div>';
            card += hasPraktik
                ? '<span class="text-[10px] font-semibold ' + (isToday ? 'text-white/70' : 'text-emerald-600') + '">' + slots.length + ' slot</span>'
                : '<span class="text-[10px] text-slate-300 italic">Libur</span>';
            card += '</div>';

            if (hasPraktik) {
                card += '<div class="px-4 py-3 space-y-2">';
                slots.forEach(function(slot) {
                    card += '<div class="flex items-center justify-between px-3 py-2 bg-slate-50 rounded-xl border border-slate-100">' +
                        '<div class="flex items-center gap-2">' +
                        '<svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
                        '<span class="text-[12px] font-semibold text-slate-700">' + slot.waktu + '</span>' +
                        '</div>' +
                        '</div>';
                });
                card += '</div>';
            } else {
                card += '<div class="px-4 py-4 text-center text-[12px] text-slate-300 italic">Tidak ada jadwal</div>';
            }

            card += '</div>';
            if (idx % 2 === 0) leftHtml += card;
            else rightHtml += card;
        });

        document.getElementById('jadwal-list-left').innerHTML = leftHtml || '<div class="bg-white rounded-2xl border border-slate-100 p-6 text-center text-slate-400 text-sm">Tidak ada jadwal</div>';
        document.getElementById('jadwal-list-right').innerHTML = rightHtml;
    }

    async function loadJadwalSaya() {
        try {
            var resDetail = await fetch('/api/tim-dokter/' + user.id, { headers: { 'Authorization': 'Bearer ' + token } });
            var dokterDetail = await resDetail.json();
            var myJadwal = dokterDetail.jadwal || [];

            var grouped = {};
            myJadwal.forEach(function(hariData) {
                grouped[hariData.hari ? hariData.hari.toLowerCase() : ''] = hariData.slots || [];
            });

            var totalSlots = 0;
            var hariAktif = 0;
            var jamHariIni = [];

            HARI_LIST.forEach(function(hari) {
                var slots = grouped[hari] || [];
                if (slots.length) {
                    totalSlots += slots.length;
                    hariAktif++;
                }
                if (hari === todayNama) jamHariIni = slots;
            });

            document.getElementById('stat-aktif').textContent = totalSlots;
            document.getElementById('stat-hari-praktik').textContent = hariAktif;
            document.getElementById('stat-jam-hari-ini').textContent = jamHariIni.length
                ? jamHariIni.map(function(s) { return s.waktu; }).join(', ')
                : 'Tidak praktik';

            renderJadwalCards(grouped);
        } catch (e) {
            document.getElementById('jadwal-list-left').innerHTML = '<div class="bg-white rounded-2xl border border-slate-100 p-6 text-center text-red-400 text-sm">Gagal memuat jadwal</div>';
        }
    }

    loadJadwalSaya();
</script>
@endsection
