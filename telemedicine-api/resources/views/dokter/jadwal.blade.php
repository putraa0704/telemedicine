@extends('layouts.app')

@section('title', 'Jadwal Dokter')
@section('page_title', 'Jadwal Dokter')
@section('page_sub', 'Lihat dan pesan jadwal dokter tersedia')
@section('nav_jadwal', 'active-nav')

@section('content')

{{-- Date picker --}}
<div class="flex items-center gap-3 mb-5">
    <label class="text-xs font-medium text-slate-500">Tanggal:</label>
    <input type="date" id="tanggal-picker"
        class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white"
        value="{{ date('Y-m-d') }}">
    <button onclick="loadJadwal()"
        class="px-4 py-1.5 bg-blue-700 hover:bg-blue-900 text-white text-xs font-semibold rounded-lg transition-colors">
        Cari
    </button>
    <span id="hari-label" class="text-xs text-slate-400"></span>
</div>

<div class="grid grid-cols-2 gap-5">

    {{-- KOLOM KIRI: Slot Hari Ini --}}
    <div>
        <h2 class="text-[13px] font-semibold text-slate-800 mb-3">Jadwal Tersedia</h2>
        <div id="jadwal-list" class="space-y-2">
            <div class="text-sm text-slate-400 py-4 text-center">Memuat jadwal...</div>
        </div>

        {{-- Booking saya --}}
        <h2 class="text-[13px] font-semibold text-slate-800 mt-6 mb-3">Booking Saya</h2>
        <div id="booking-saya" class="space-y-2">
            <div class="text-sm text-slate-400 italic">Memuat...</div>
        </div>
    </div>

    {{-- KOLOM KANAN: Tim Dokter + Tabel Mingguan --}}
    <div>
        <h2 class="text-[13px] font-semibold text-slate-800 mb-3">Tim Dokter Aktif</h2>
        <div id="dokter-list" class="space-y-2 mb-5">
            <div class="text-sm text-slate-400 py-4 text-center">Memuat...</div>
        </div>

        <h2 class="text-[13px] font-semibold text-slate-800 mb-3">Jadwal Minggu Ini</h2>
        <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
            <table class="w-full text-[12px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2.5 text-left font-semibold text-slate-600">Waktu</th>
                        <th class="px-3 py-2.5 text-center font-semibold text-slate-600">Senin</th>
                        <th class="px-3 py-2.5 text-center font-semibold text-slate-600">Selasa</th>
                        <th class="px-3 py-2.5 text-center font-semibold text-slate-600">Rabu</th>
                        <th class="px-3 py-2.5 text-center font-semibold text-slate-600">Kamis</th>
                        <th class="px-3 py-2.5 text-center font-semibold text-slate-600">Jumat</th>
                    </tr>
                </thead>
                <tbody id="tabel-mingguan" class="divide-y divide-slate-100">
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-400 text-xs">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Pesan --}}
<div id="modal-overlay" class="hidden fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 w-full max-w-sm">
        <h3 class="text-[14px] font-semibold text-slate-800 mb-1">Konfirmasi Pemesanan</h3>
        <p id="modal-desc" class="text-[12px] text-slate-400 mb-4">—</p>
        <div class="mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Keluhan</label>
            <textarea id="modal-keluhan" rows="3" placeholder="Deskripsikan keluhan Anda..."
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 resize-none transition"></textarea>
        </div>
        <div id="modal-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-2 rounded-lg mb-3"></div>
        <div class="flex gap-2">
            <button onclick="tutupModal()" class="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium py-2 rounded-lg transition-colors">Batal</button>
            <button onclick="konfirmasiPesan()" class="flex-1 bg-blue-700 hover:bg-blue-900 text-white text-sm font-semibold py-2 rounded-lg transition-colors">Pesan</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    var selectedJadwalId = null;
    var selectedTanggal  = null;

    const HARI = ['minggu','senin','selasa','rabu','kamis','jumat','sabtu'];
    const HARI_LABEL = { senin:'Senin', selasa:'Selasa', rabu:'Rabu', kamis:'Kamis', jumat:'Jumat', sabtu:'Sabtu', minggu:'Minggu' };

    // ── Load jadwal dari API ──
    async function loadJadwal() {
        var tanggal = document.getElementById('tanggal-picker').value;
        if (!tanggal) return;

        var hariIdx = new Date(tanggal).getDay();
        document.getElementById('hari-label').textContent = '(' + (HARI_LABEL[HARI[hariIdx]] || '') + ')';

        document.getElementById('jadwal-list').innerHTML = '<div class="text-sm text-slate-400 text-center py-4">Memuat...</div>';

        try {
            var res  = await fetch('/api/jadwal?tanggal=' + tanggal, { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            renderJadwal(data.jadwal || [], tanggal);
        } catch(e) {
            document.getElementById('jadwal-list').innerHTML = '<div class="text-sm text-red-400 py-2">Gagal memuat jadwal</div>';
        }
    }

    function renderJadwal(list, tanggal) {
        var el = document.getElementById('jadwal-list');
        if (!list.length) {
            el.innerHTML = '<div class="text-sm text-slate-400 italic py-4 text-center">Tidak ada jadwal untuk hari ini</div>';
            return;
        }
        el.innerHTML = list.map(slot => `
            <div class="flex items-center justify-between px-3 py-2.5 rounded-xl border
                ${slot.terisi ? 'bg-red-50 border-red-100' : 'bg-white border-slate-200'}">
                <div>
                    <div class="text-[13px] font-semibold ${slot.terisi ? 'text-red-500' : 'text-slate-800'}">${slot.waktu}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">${slot.dokter} · ${slot.spesialisasi || ''}</div>
                </div>
                ${slot.terisi
                    ? '<span class="text-[11px] font-semibold text-red-500 bg-red-50 border border-red-200 px-3 py-1 rounded-lg">Terisi</span>'
                    : `<button onclick="bukaPesan(${slot.id}, '${slot.waktu}', '${slot.dokter}', '${tanggal}')"
                           class="text-[11px] font-semibold text-white bg-blue-700 hover:bg-blue-900 px-3 py-1 rounded-lg transition-colors">
                           Pesan
                       </button>`
                }
            </div>
        `).join('');
    }

    // ── Load tim dokter ──
    async function loadTimDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            document.getElementById('dokter-list').innerHTML = data.slice(0,4).map(dr => `
                <div class="flex items-center gap-3 px-3 py-2.5 bg-white rounded-xl border border-slate-200">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-800 truncate">${dr.nama}</div>
                        <div class="text-[11px] text-slate-400">${dr.spesialisasi} · ${dr.pasien_aktif} pasien</div>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-teal-50 text-teal-700 border border-teal-200'}">
                        ${dr.status === 'sibuk' ? 'Sibuk' : 'Tersedia'}
                    </span>
                </div>
            `).join('');
        } catch(e) {
            document.getElementById('dokter-list').innerHTML = '<div class="text-sm text-slate-400">Gagal memuat</div>';
        }
    }

    // ── Load tabel mingguan ──
    async function loadMingguan() {
        try {
            var res  = await fetch('/api/jadwal/mingguan', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            var hariList = ['senin','selasa','rabu','kamis','jumat'];

            // Kumpulkan semua waktu unik
            var waktuSet = new Set();
            hariList.forEach(h => (data[h] || []).forEach(s => waktuSet.add(s.waktu)));
            var waktuList = [...waktuSet].sort();

            if (!waktuList.length) {
                document.getElementById('tabel-mingguan').innerHTML =
                    '<tr><td colspan="6" class="px-3 py-4 text-center text-slate-400 text-xs">Belum ada jadwal</td></tr>';
                return;
            }

            document.getElementById('tabel-mingguan').innerHTML = waktuList.map(waktu => `
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2.5 font-semibold text-slate-700 whitespace-nowrap">${waktu}</td>
                    ${hariList.map(h => {
                        var slot = (data[h] || []).find(s => s.waktu === waktu);
                        return `<td class="px-3 py-2.5 text-center text-slate-600 text-[11px]">${slot ? slot.dokter.replace('Dr. ','Dr.') : '<span class="text-slate-300">—</span>'}</td>`;
                    }).join('')}
                </tr>
            `).join('');
        } catch(e) {
            document.getElementById('tabel-mingguan').innerHTML =
                '<tr><td colspan="6" class="px-3 py-4 text-center text-slate-400 text-xs">Gagal memuat</td></tr>';
        }
    }

    // ── Load booking milik pasien ──
    async function loadBookingSaya() {
        try {
            var res  = await fetch('/api/jadwal/booking-saya', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            var el   = document.getElementById('booking-saya');

            if (!data.length) {
                el.innerHTML = '<div class="text-xs text-slate-400 italic">Belum ada booking</div>';
                return;
            }
            el.innerHTML = data.slice(0,3).map(b => `
                <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5">
                    <div>
                        <div class="text-[12px] font-semibold text-slate-800">${b.dokter}</div>
                        <div class="text-[11px] text-slate-400">${b.tanggal} · ${b.waktu}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                            ${b.status === 'booked' ? 'bg-blue-50 text-blue-700' : b.status === 'selesai' ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-500'}">
                            ${b.status}
                        </span>
                        ${b.status === 'booked'
                            ? `<button onclick="batalBooking(${b.id})" class="text-[10px] text-red-500 hover:underline">Batal</button>`
                            : ''}
                    </div>
                </div>
            `).join('');
        } catch(e) {}
    }

    // ── Modal ──
    function bukaPesan(jadwalId, waktu, dokter, tanggal) {
        selectedJadwalId = jadwalId;
        selectedTanggal  = tanggal;
        document.getElementById('modal-desc').textContent = waktu + ' — ' + dokter + ' (' + tanggal + ')';
        document.getElementById('modal-keluhan').value    = '';
        document.getElementById('modal-error').classList.add('hidden');
        document.getElementById('modal-overlay').classList.remove('hidden');
    }

    function tutupModal() {
        document.getElementById('modal-overlay').classList.add('hidden');
        selectedJadwalId = null; selectedTanggal = null;
    }

    async function konfirmasiPesan() {
        var keluhan  = document.getElementById('modal-keluhan').value.trim();
        var errEl    = document.getElementById('modal-error');
        errEl.classList.add('hidden');

        if (!keluhan) { errEl.textContent = 'Keluhan tidak boleh kosong'; errEl.classList.remove('hidden'); return; }

        try {
            var res  = await fetch('/api/jadwal/' + selectedJadwalId + '/booking', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ tanggal: selectedTanggal, keluhan })
            });
            var data = await res.json();

            if (!data.success) {
                errEl.textContent = data.message || 'Gagal memesan'; errEl.classList.remove('hidden'); return;
            }

            tutupModal();
            await loadJadwal();
            await loadBookingSaya();
            alert('✅ Jadwal berhasil dipesan!\n' + data.jadwal.waktu + ' — ' + data.jadwal.dokter);
        } catch(e) {
            errEl.textContent = 'Gagal terhubung ke server'; errEl.classList.remove('hidden');
        }
    }

    async function batalBooking(id) {
        if (!confirm('Batalkan booking ini?')) return;
        try {
            await fetch('/api/jadwal/booking/' + id, {
                method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token }
            });
            await loadBookingSaya();
        } catch(e) {}
    }

    document.getElementById('modal-overlay').addEventListener('click', e => { if (e.target === e.currentTarget) tutupModal(); });

    // ── Init ──
    loadJadwal();
    loadTimDokter();
    loadMingguan();
    loadBookingSaya();
</script>
@endsection