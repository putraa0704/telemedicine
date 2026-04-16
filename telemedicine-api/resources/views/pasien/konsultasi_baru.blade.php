@extends('layouts.app')

@section('title', 'Konsultasi Baru')
@section('page_title', 'Konsultasi Baru')
@section('page_sub', 'Buat permintaan konsultasi baru')
@section('nav_new', 'active-nav')

@section('content')

<div class="grid grid-cols-[1fr_1.6fr] gap-5">

    {{-- Kolom Kiri: Form --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="text-sm font-semibold text-slate-800 mb-5">Formulir Konsultasi</h2>

        <div id="success-box" class="hidden bg-teal-50 border border-teal-300 text-teal-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>
        <div id="error-box"   class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>

        <div class="space-y-4">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input id="nama" type="text" placeholder="Nama anda"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Tanggal Lahir</label>
                    <input id="tgl_lahir" type="date"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Pilih Dokter</label>
                <select id="dokter_id"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition bg-white">
                    <option value="">— Memuat daftar dokter... —</option>
                </select>
            </div>

            {{-- Info dokter terpilih --}}
            <div id="dokter-info" class="hidden bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5">
                <div class="flex items-center gap-2.5">
                    <div id="dokter-avatar" class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 text-xs font-bold flex-shrink-0"></div>
                    <div>
                        <div id="dokter-nama-info" class="text-[13px] font-semibold text-slate-800"></div>
                        <div id="dokter-spesialis-info" class="text-[11px] text-slate-500"></div>
                    </div>
                    <div id="dokter-status-badge" class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full"></div>
                </div>
                <div id="dokter-jadwal-info" class="mt-2 text-[11px] text-slate-500"></div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Keluhan Utama</label>
                <textarea id="keluhan" rows="4" placeholder="Deskripsikan gejala atau keluhan anda secara detail..."
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 resize-none transition"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Tingkat Urgensi</label>
                    <select id="urgensi"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white transition">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="darurat">Darurat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Waktu Pilihan</label>
                    <select id="waktu"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white transition">
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
                class="w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Kirim Konsultasi
            </button>
        </div>
    </div>

    {{-- Kolom Kanan: Daftar Dokter --}}
    <div>
        <h2 class="text-[13px] font-semibold text-slate-800 mb-3">Pilih Dokter</h2>
        <div id="dokter-cards" class="grid grid-cols-2 gap-3">
            <div class="col-span-2 bg-white rounded-xl border border-slate-200 px-4 py-8 text-center text-sm text-slate-400">
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
            renderDokterSelect();
            renderDokterCards();
        } catch(e) {
            document.getElementById('dokter-cards').innerHTML =
                '<div class="col-span-2 bg-white rounded-xl border border-slate-200 px-4 py-8 text-center text-sm text-slate-400">Gagal memuat daftar dokter</div>';
        }
    }

    function renderDokterSelect() {
        var sel = document.getElementById('dokter_id');
        sel.innerHTML = '<option value="">— Pilih dokter —</option>' +
            dokterList.map(d =>
                '<option value="' + d.id + '">' + d.nama + ' — ' + d.spesialisasi + '</option>'
            ).join('');
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
        badge.className = 'ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full ' +
            (isSibuk ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-teal-50 text-teal-700 border border-teal-200');

        var jadwalText = dr.hari_praktik && dr.hari_praktik.length
            ? 'Praktik: ' + dr.hari_praktik.join(', ')
            : 'Jadwal belum tersedia';
        document.getElementById('dokter-jadwal-info').textContent = jadwalText;
        info.classList.remove('hidden');
    }

    function renderDokterCards() {
        var container = document.getElementById('dokter-cards');
        if (!dokterList.length) {
            container.innerHTML = '<div class="col-span-2 text-sm text-slate-400 italic py-4 text-center">Belum ada dokter tersedia</div>';
            return;
        }
        container.innerHTML = dokterList.map(dr => {
            var isSibuk = dr.status === 'sibuk';
            return `
            <div class="bg-white rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-blue-300 hover:shadow-sm transition-all"
                 onclick="pilihDokter('${dr.id}')">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-[13px] font-bold flex-shrink-0
                        ${isSibuk ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-800 truncate">${dr.nama}</div>
                        <div class="text-[11px] text-slate-400">${dr.spesialisasi}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                        ${isSibuk ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-teal-50 text-teal-700 border border-teal-200'}">
                        ${isSibuk ? 'Sibuk' : 'Tersedia'}
                    </span>
                    <span class="text-[11px] text-slate-400">${dr.pasien_aktif} pasien</span>
                </div>
                <div class="mt-2.5 flex flex-wrap gap-1">
                    ${(dr.hari_praktik || []).slice(0,3).map(h =>
                        `<span class="text-[9px] font-medium bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full">${h}</span>`
                    ).join('')}
                    ${dr.hari_praktik && dr.hari_praktik.length > 3
                        ? `<span class="text-[9px] text-slate-400">+${dr.hari_praktik.length - 3}</span>` : ''}
                </div>
            </div>
            `;
        }).join('');
    }

    function pilihDokter(id) {
        document.getElementById('dokter_id').value = id;
        onDokterChange();
        // Highlight card terpilih
        document.querySelectorAll('#dokter-cards > div').forEach(el => {
            el.classList.remove('border-blue-400', 'ring-2', 'ring-blue-100');
        });
        event.currentTarget.classList.add('border-blue-400', 'ring-2', 'ring-blue-100');
    }

    async function kirimKonsultasi() {
        var keluhan = document.getElementById('keluhan').value.trim();
        var nama    = document.getElementById('nama').value.trim();
        var errBox  = document.getElementById('error-box');
        var sucBox  = document.getElementById('success-box');
        errBox.classList.add('hidden'); sucBox.classList.add('hidden');

        if (!nama)    { errBox.textContent = 'Nama wajib diisi.';    errBox.classList.remove('hidden'); return; }
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
                    body: JSON.stringify({
                        local_id: data.id, nama: data.nama,
                        keluhan: data.keluhan, created_at: data.created_at
                    })
                });
                if (res.ok || res.status === 409) {
                    var result = await res.json();
                    await db.konsultasi.update(data.id, {
                        status: 'synced', server_id: result.server_id,
                        synced_at: new Date().toISOString()
                    });
                    sucBox.textContent = '✓ Konsultasi berhasil dikirim! ID: #KSL-' + String(result.server_id).padStart(3,'0');
                } else {
                    sucBox.textContent = '✓ Konsultasi tersimpan. Akan disinkronisasi saat online.';
                }
            } catch(e) {
                sucBox.textContent = '✓ Konsultasi tersimpan secara lokal, akan disinkronisasi saat koneksi tersedia.';
            }
        } else {
            sucBox.textContent = '✓ Konsultasi tersimpan secara lokal (offline). Akan dikirim saat koneksi kembali.';
        }

        sucBox.classList.remove('hidden');
        document.getElementById('keluhan').value = '';
        document.getElementById('dokter_id').value = '';
        document.getElementById('dokter-info').classList.add('hidden');
        document.querySelectorAll('#dokter-cards > div').forEach(el => {
            el.classList.remove('border-blue-400', 'ring-2', 'ring-blue-100');
        });
        btn.disabled = false; btn.textContent = 'Kirim Konsultasi';

        setTimeout(() => sucBox.classList.add('hidden'), 5000);
    }

    loadDokter();
</script>
@endsection