@extends('layouts.app')

@section('title', 'Kelola Jadwal Dokter')
@section('page_title', 'Kelola Jadwal Dokter')
@section('page_sub', 'Halaman pengaturan jadwal khusus admin')

@section('content')

{{-- Hari Ini Card --}}
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
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Hari Praktik</div>
        <div class="text-2xl font-bold text-slate-800" id="stat-hari-praktik">—</div>
        <div class="text-[11px] text-slate-400 mt-1">hari/minggu</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Jam Hari Ini</div>
        <div class="text-[13px] font-bold text-amber-600" id="stat-jam-hari-ini">—</div>
    </div>
</div>

{{-- Jadwal Per Hari --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="space-y-3" id="jadwal-list-left">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 text-center text-slate-400 text-sm">
            Memuat jadwal...
        </div>
    </div>
    <div class="space-y-3" id="jadwal-list-right"></div>
</div>

{{-- Tambah Jadwal --}}
<div class="mt-5 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-slate-100">
        <span class="text-[13px] font-semibold text-slate-800">Tambah Jadwal Baru</span>
    </div>
    <div class="p-5">
        <div id="jadwal-success" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl mb-4"></div>
        <div id="jadwal-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4"></div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Hari</label>
                <select id="f-hari" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50">
                    <option value="senin">Senin</option>
                    <option value="selasa">Selasa</option>
                    <option value="rabu">Rabu</option>
                    <option value="kamis">Kamis</option>
                    <option value="jumat">Jumat</option>
                    <option value="sabtu">Sabtu</option>
                    <option value="minggu">Minggu</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Jam Mulai</label>
                <input id="f-mulai" type="time" value="08:00" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Jam Selesai</label>
                <input id="f-selesai" type="time" value="09:00" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50"/>
            </div>
        </div>
        <button onclick="tambahJadwal()" class="mt-4 bg-brand-600 hover:bg-brand-800 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">
            + Tambah Jadwal
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role === 'pasien') window.location.href = '/pasien';
    if (user && user.role === 'dokter') window.location.href = '/dokter/jadwal-saya';
    if (user && user.role !== 'admin') window.location.href = '/login';

    const HARI_LIST  = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
    const HARI_LABEL = { senin:'Senin', selasa:'Selasa', rabu:'Rabu', kamis:'Kamis', jumat:'Jumat', sabtu:'Sabtu', minggu:'Minggu' };
    const DAY_TO_NAMA = ['minggu','senin','selasa','rabu','kamis','jumat','sabtu'];

    var todayNama = DAY_TO_NAMA[new Date().getDay()];
    var todayFull = new Date().toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

    // Update stat cards
    document.getElementById('stat-hari').textContent = HARI_LABEL[todayNama] || '—';
    document.getElementById('stat-tanggal').textContent = todayFull;

    var allJadwal = [];

    async function loadJadwal() {
        try {
            var res = await fetch('/api/jadwal/mingguan', { headers: { 'Authorization': 'Bearer ' + token } });
            var mingguan = await res.json();

            // Filter hanya jadwal dokter ini dari mingguan
            // Kita juga perlu fetch detail dokter untuk filter by dokter_id
            var resDetail = await fetch('/api/tim-dokter/' + user.id, { headers: { 'Authorization': 'Bearer ' + token } });
            var dokterDetail = await resDetail.json();
            var myJadwal = dokterDetail.jadwal || [];

            // Group my jadwal per hari
            var grouped = {};
            myJadwal.forEach(function(hari_data) {
                grouped[hari_data.hari ? hari_data.hari.toLowerCase() : ''] = hari_data.slots || [];
            });

            // Hitung stats
            var totalSlots = 0;
            var hariAktif = 0;
            var jamHariIni = [];

            HARI_LIST.forEach(function(hari) {
                var slots = grouped[hari] || [];
                if (slots.length) {
                    totalSlots += slots.length;
                    hariAktif++;
                }
                if (hari === todayNama) {
                    jamHariIni = slots;
                }
            });

            document.getElementById('stat-aktif').textContent = totalSlots;
            document.getElementById('stat-hari-praktik').textContent = hariAktif;
            document.getElementById('stat-jam-hari-ini').textContent = jamHariIni.length
                ? jamHariIni.map(function(s) { return s.waktu; }).join(', ')
                : 'Tidak praktik';

            renderJadwalCards(grouped);
        } catch(e) {
            // Fallback: load from /api/jadwal/mingguan dan filter by dokter name
            loadJadwalFallback();
        }
    }

    async function loadJadwalFallback() {
        try {
            var res = await fetch('/api/jadwal/mingguan', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();

            var grouped = {};
            var totalSlots = 0, hariAktif = 0;
            var jamHariIni = [];

            HARI_LIST.forEach(function(hari) {
                var slots = (data[hari] || []).filter(function(s) {
                    return s.dokter && s.dokter.toLowerCase().includes(user.name.split(' ')[1] ? user.name.split(' ')[1].toLowerCase() : user.name.toLowerCase());
                });
                if (!slots.length) {
                    // If can't filter by name, show all for this day
                    slots = data[hari] || [];
                }
                grouped[hari] = slots;
                if (slots.length) { totalSlots += slots.length; hariAktif++; }
                if (hari === todayNama) jamHariIni = slots;
            });

            document.getElementById('stat-aktif').textContent = totalSlots;
            document.getElementById('stat-hari-praktik').textContent = hariAktif;
            document.getElementById('stat-jam-hari-ini').textContent = jamHariIni.length
                ? jamHariIni.map(function(s) { return s.waktu; }).join(', ')
                : 'Tidak praktik';

            renderJadwalCards(grouped);
        } catch(e) {
            document.getElementById('jadwal-list-left').innerHTML = '<div class="bg-white rounded-2xl border border-slate-100 p-6 text-center text-red-400 text-sm">Gagal memuat jadwal</div>';
        }
    }

    function renderJadwalCards(grouped) {
        var leftHtml = '', rightHtml = '';

        HARI_LIST.forEach(function(hari, idx) {
            var slots = grouped[hari] || [];
            var isToday = hari === todayNama;
            var hasPraktik = slots.length > 0;

            var card = '<div class="bg-white rounded-2xl border ' +
                (isToday ? 'border-brand-300 shadow-md ring-2 ring-brand-100' : 'border-slate-100 shadow-sm') +
                ' overflow-hidden">' +
                '<div class="px-4 py-3 flex items-center justify-between ' +
                (isToday ? 'bg-brand-600' : hasPraktik ? 'bg-slate-50' : 'bg-slate-50') + '">' +
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

            if (hasPraktik) {
                card += '<span class="text-[10px] font-semibold ' + (isToday ? 'text-white/70' : 'text-emerald-600') + '">' +
                    slots.length + ' slot</span>';
            } else {
                card += '<span class="text-[10px] text-slate-300 italic">Libur</span>';
            }

            card += '</div>'; // end header

            if (hasPraktik) {
                card += '<div class="px-4 py-3 space-y-2">';
                slots.forEach(function(slot) {
                    card += '<div class="flex items-center justify-between px-3 py-2 bg-slate-50 rounded-xl border border-slate-100">' +
                        '<div class="flex items-center gap-2">' +
                        '<svg class="w-3.5 h-3.5 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
                        '<span class="text-[12px] font-semibold text-slate-700">' + slot.waktu + '</span>' +
                        '</div>' +
                        '<button onclick="hapusJadwal(' + slot.id + ')" class="text-[10px] text-red-400 hover:text-red-600 hover:bg-red-50 px-2 py-0.5 rounded-lg transition-colors">Hapus</button>' +
                        '</div>';
                });
                card += '</div>';
            } else {
                card += '<div class="px-4 py-4 text-center text-[12px] text-slate-300 italic">Tidak ada jadwal</div>';
            }

            card += '</div>'; // end card

            if (idx % 2 === 0) { leftHtml += card; }
            else { rightHtml += card; }
        });

        document.getElementById('jadwal-list-left').innerHTML = leftHtml || '<div class="bg-white rounded-2xl border border-slate-100 p-6 text-center text-slate-400 text-sm">Tidak ada jadwal</div>';
        document.getElementById('jadwal-list-right').innerHTML = rightHtml;
    }

    async function tambahJadwal() {
        var sucEl = document.getElementById('jadwal-success');
        var errEl = document.getElementById('jadwal-error');
        sucEl.classList.add('hidden'); errEl.classList.add('hidden');

        var hari     = document.getElementById('f-hari').value;
        var mulai    = document.getElementById('f-mulai').value;
        var selesai  = document.getElementById('f-selesai').value;

        if (!mulai || !selesai) { errEl.textContent = 'Jam mulai dan selesai wajib diisi'; errEl.classList.remove('hidden'); return; }
        if (mulai >= selesai)   { errEl.textContent = 'Jam selesai harus setelah jam mulai'; errEl.classList.remove('hidden'); return; }

        try {
            var res = await fetch('/api/jadwal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ hari, jam_mulai: mulai, jam_selesai: selesai })
            });
            var data = await res.json();

            if (data.success) {
                sucEl.textContent = '✓ Jadwal ' + HARI_LABEL[hari] + ' ' + mulai + '–' + selesai + ' berhasil ditambahkan!';
                sucEl.classList.remove('hidden');
                await loadJadwal();
                // Refresh sidebar jadwal
                if (typeof loadJadwalDokterSidebar === 'function') loadJadwalDokterSidebar();
                setTimeout(function() { sucEl.classList.add('hidden'); }, 4000);
            } else {
                errEl.textContent = data.message || 'Gagal menambahkan jadwal';
                errEl.classList.remove('hidden');
            }
        } catch(e) {
            errEl.textContent = 'Gagal terhubung ke server';
            errEl.classList.remove('hidden');
        }
    }

    async function hapusJadwal(id) {
        if (!confirm('Hapus jadwal ini?')) return;
        try {
            var res = await fetch('/api/jadwal/' + id, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            var data = await res.json();
            if (data.success) {
                await loadJadwal();
                if (typeof loadJadwalDokterSidebar === 'function') loadJadwalDokterSidebar();
            }
        } catch(e) { alert('Gagal menghapus jadwal'); }
    }

    loadJadwal();
</script>
@endsection