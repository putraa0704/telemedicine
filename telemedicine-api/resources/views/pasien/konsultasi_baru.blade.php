@extends('layouts.app')

@section('title', 'Konsultasi Baru')
@section('page_title', 'Konsultasi Baru')
@section('page_sub', 'Buat permintaan konsultasi dengan dokter')
@section('nav_new', 'active')

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-[1fr_1.5fr] gap-4 lg:gap-5">

    {{-- FORM --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h2 class="text-[14px] font-semibold text-slate-800 mb-5">Formulir Konsultasi</h2>

        <div id="success-box" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13px] px-4 py-3 rounded-xl mb-4"></div>
        <div id="error-box"   class="hidden bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3 rounded-xl mb-4"></div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Nama Lengkap</label>
                    <input id="nama" type="text" placeholder="Nama Anda"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 bg-slate-50 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tanggal Lahir</label>
                    <input id="tgl_lahir" type="date"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 bg-slate-50 transition"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Dokter</label>
                <select id="dokter_id"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 transition">
                    <option value="">— Memuat dokter... —</option>
                </select>
            </div>

            {{-- Info Dokter Terpilih --}}
            <div id="dokter-info" class="hidden bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div id="dokter-avatar" class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 text-sm font-bold flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div id="dokter-nama-info" class="text-[13px] font-semibold text-slate-800"></div>
                        <div id="dokter-spesialis-info" class="text-[11px] text-slate-500"></div>
                    </div>
                    <div id="dokter-status-badge" class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"></div>
                </div>
                <div id="dokter-jadwal-info" class="mt-2 text-[11px] text-slate-400"></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Keluhan Utama</label>
                <textarea id="keluhan" rows="4" placeholder="Deskripsikan gejala atau keluhan Anda secara detail..."
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 bg-slate-50 resize-none transition"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tingkat Urgensi</label>
                    <select id="urgensi"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 transition">
                        <option value="normal">🟢 Normal</option>
                        <option value="urgent">🟡 Urgent</option>
                        <option value="darurat">🔴 Darurat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Waktu Pilihan</label>
                    <select id="waktu"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 transition">
                        <option>08:00 - 09:00</option>
                        <option>09:00 - 10:00</option>
                        <option>10:00 - 11:00</option>
                        <option>11:00 - 12:00</option>
                        <option>13:00 - 14:00</option>
                        <option>14:00 - 15:00</option>
                        <option>15:00 - 16:00</option>
                    </select>
                </div>
            </div>

            <button onclick="kirimKonsultasi()" id="submit-btn"
                class="w-full bg-brand-600 hover:bg-brand-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors disabled:opacity-50">
                Kirim Konsultasi
            </button>

            <p class="text-center text-[11px] text-slate-400">Data disimpan lokal jika offline, otomatis sync saat online</p>
        </div>
    </div>

    {{-- PILIH DOKTER --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-[13px] font-semibold text-slate-800">Pilih Dokter</h2>
            <span class="text-[11px] text-slate-400" id="dokter-count">Memuat...</span>
        </div>
        <div id="dokter-cards" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="col-span-2 bg-white rounded-2xl border border-slate-100 px-4 py-10 text-center text-sm text-slate-400 shadow-sm">
                <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    </svg>
                </div>
                Memuat daftar dokter...
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/dexie@3.2.4/dist/dexie.js"></script>
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user) document.getElementById('nama').value = user.name;

    const db = new Dexie('telemedicine');
    db.version(2).stores({ konsultasi: 'id, status, created_at', auth: 'key' });

    var dokterList = [];

    async function loadDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            dokterList = await res.json();
            document.getElementById('dokter-count').textContent = dokterList.length + ' dokter aktif';
            renderDokterSelect();
            renderDokterCards();
        } catch(e) {
            document.getElementById('dokter-cards').innerHTML = '<div class="col-span-2 bg-white rounded-2xl border border-slate-100 px-4 py-8 text-center text-sm text-slate-400">Gagal memuat</div>';
        }
    }

    function renderDokterSelect() {
        var sel = document.getElementById('dokter_id');
        sel.innerHTML = '<option value="">— Pilih dokter —</option>' +
            dokterList.map(d => '<option value="' + d.id + '">' + d.nama + ' — ' + d.spesialisasi + '</option>').join('');
        sel.addEventListener('change', onDokterChange);
    }

    function onDokterChange() {
        var id   = document.getElementById('dokter_id').value;
        var info = document.getElementById('dokter-info');
        if (!id) { info.classList.add('hidden'); return; }
        var dr = dokterList.find(d => String(d.id) === String(id));
        if (!dr) return;
        var isSibuk = dr.status === 'sibuk';
        document.getElementById('dokter-avatar').textContent = dr.inisial;
        document.getElementById('dokter-nama-info').textContent = dr.nama;
        document.getElementById('dokter-spesialis-info').textContent = dr.spesialisasi + (dr.no_str ? ' · STR: ' + dr.no_str : '');
        var badge = document.getElementById('dokter-status-badge');
        badge.textContent = isSibuk ? 'Sibuk' : 'Tersedia';
        badge.className = 'text-[10px] font-semibold px-2.5 py-1 rounded-full flex-shrink-0 ' +
            (isSibuk ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200');
        document.getElementById('dokter-jadwal-info').textContent =
            dr.hari_praktik && dr.hari_praktik.length ? 'Praktik: ' + dr.hari_praktik.join(', ') : 'Belum ada jadwal';
        info.classList.remove('hidden');
    }

    function renderDokterCards() {
        var container = document.getElementById('dokter-cards');
        if (!dokterList.length) {
            container.innerHTML = '<div class="col-span-2 text-sm text-slate-400 text-center py-8">Belum ada dokter</div>';
            return;
        }
        container.innerHTML = dokterList.map(dr => {
            var isSibuk = dr.status === 'sibuk';
            return `
            <div class="bg-white rounded-2xl border-2 border-slate-100 hover:border-brand-300 p-4 cursor-pointer hover:shadow-md transition-all group"
                 data-id="${dr.id}" onclick="pilihDokter(this, '${dr.id}')">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-11 h-11 rounded-full flex items-center justify-center text-[13px] font-bold flex-shrink-0
                        ${isSibuk ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-800 truncate">${dr.nama}</div>
                        <div class="text-[11px] text-slate-400">${dr.spesialisasi}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ${isSibuk ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                        ${isSibuk ? 'Sibuk' : 'Tersedia'}
                    </span>
                    <span class="text-[11px] text-slate-400">${dr.pasien_aktif} pasien</span>
                </div>
                <div class="flex flex-wrap gap-1">
                    ${(dr.hari_praktik || []).slice(0,3).map(h =>
                        `<span class="text-[9px] font-medium bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full">${h}</span>`
                    ).join('')}
                    ${dr.hari_praktik && dr.hari_praktik.length > 3 ? `<span class="text-[9px] text-slate-400">+${dr.hari_praktik.length - 3}</span>` : ''}
                </div>
            </div>
            `;
        }).join('');
    }

    function pilihDokter(el, id) {
        document.getElementById('dokter_id').value = id;
        onDokterChange();
        document.querySelectorAll('#dokter-cards > div[data-id]').forEach(function(card) {
            card.classList.remove('border-brand-600', 'shadow-lg');
            card.classList.add('border-slate-100');
        });
        el.classList.remove('border-slate-100');
        el.classList.add('border-brand-600', 'shadow-lg');
    }

    async function kirimKonsultasi() {
        var keluhan = document.getElementById('keluhan').value.trim();
        var nama    = document.getElementById('nama').value.trim();
        var errBox  = document.getElementById('error-box');
        var sucBox  = document.getElementById('success-box');
        errBox.classList.add('hidden'); sucBox.classList.add('hidden');

        if (!nama)    { errBox.textContent = 'Nama wajib diisi.'; errBox.classList.remove('hidden'); return; }
        if (!keluhan) { errBox.textContent = 'Keluhan wajib diisi.'; errBox.classList.remove('hidden'); return; }

        var btn = document.getElementById('submit-btn');
        btn.disabled = true; btn.textContent = 'Mengirim...';

        var data = {
            id: 'loc_' + Date.now() + '_' + Math.random().toString(36).slice(2,7),
            nama, keluhan, status: 'pending',
            created_at: new Date().toISOString(), server_id: null, synced_at: null
        };
        await db.konsultasi.add(data);

        if (navigator.onLine) {
            try {
                var res = await fetch('/api/konsultasi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ local_id: data.id, nama: data.nama, keluhan: data.keluhan, created_at: data.created_at })
                });
                if (res.ok || res.status === 409) {
                    var result = await res.json();
                    await db.konsultasi.update(data.id, { status: 'synced', server_id: result.server_id, synced_at: new Date().toISOString() });
                    sucBox.textContent = '✓ Konsultasi berhasil dikirim! ID: #KSL-' + String(result.server_id || '?').padStart(3,'0');
                } else { sucBox.textContent = '✓ Konsultasi tersimpan lokal.'; }
            } catch(e) { sucBox.textContent = '✓ Offline — konsultasi tersimpan, akan dikirim saat online.'; }
        } else {
            sucBox.textContent = '✓ Offline — konsultasi tersimpan lokal, akan dikirim saat koneksi kembali.';
        }

        sucBox.classList.remove('hidden');
        document.getElementById('keluhan').value = '';
        document.getElementById('dokter_id').value = '';
        document.getElementById('dokter-info').classList.add('hidden');
        document.querySelectorAll('#dokter-cards > div[data-id]').forEach(function(c) {
            c.classList.remove('border-brand-600', 'shadow-lg');
            c.classList.add('border-slate-100');
        });
        btn.disabled = false; btn.textContent = 'Kirim Konsultasi';
        setTimeout(() => sucBox.classList.add('hidden'), 6000);
    }

    loadDokter();
</script>
@endsection