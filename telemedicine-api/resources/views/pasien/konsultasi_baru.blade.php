@extends('layouts.app')

@section('title', 'Formulir Konsultasi')
@section('page_title', 'Formulir Konsultasi')
@section('page_sub', 'Pilih dokter dan konfirmasi keluhan Anda')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-[1fr_1.5fr] xl:grid-cols-[1fr_1.7fr] gap-5 lg:gap-6 items-start">

    {{-- KOLOM KIRI: FORMULIR --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6 order-2 lg:order-1">

        {{-- Info dari chatbot --}}
        <div id="chatbot-info" class="hidden bg-brand-50 border border-brand-100 rounded-xl px-4 py-3 mb-5 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-brand-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            <div>
                <div class="text-[12px] font-semibold text-brand-700 mb-0.5">Data dari asisten diisi otomatis</div>
                <div class="text-[11px] text-brand-600">Periksa dan lengkapi data sebelum mengirim.</div>
            </div>
        </div>

        <h2 class="text-[15px] font-semibold text-slate-800 mb-5">Detail Konsultasi</h2>

        <div id="success-box" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13px] px-4 py-3 rounded-xl mb-5"></div>
        <div id="error-box"   class="hidden bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3 rounded-xl mb-5"></div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Nama Lengkap</label>
                    <input id="nama" type="text" placeholder="Nama Anda"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 bg-slate-50 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tanggal Lahir</label>
                    <input id="tgl_lahir" type="date"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 bg-slate-50 transition"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Dokter</label>
                <select id="dokter_id"
                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 transition">
                    <option value="">— Memuat dokter... —</option>
                </select>
            </div>

            {{-- Info dokter terpilih --}}
            <div id="dokter-info" class="hidden bg-brand-50/50 border border-brand-100 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div id="dokter-avatar" class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 text-sm font-bold flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div id="dokter-nama-info" class="text-[13px] font-semibold text-slate-800 truncate"></div>
                        <div id="dokter-spesialis-info" class="text-[11px] text-slate-500"></div>
                    </div>
                    <div id="dokter-status-badge" class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"></div>
                </div>
                <div id="dokter-jadwal-info" class="mt-2 text-[11px] text-slate-500"></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Keluhan Utama</label>
                <textarea id="keluhan" rows="4" placeholder="Deskripsikan gejala atau keluhan Anda..."
                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 bg-slate-50 resize-none transition"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tingkat Urgensi</label>
                    <select id="urgensi"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 transition">
                        <option value="normal">🟢 Normal</option>
                        <option value="urgent">🟡 Urgent</option>
                        <option value="darurat">🔴 Darurat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Waktu Pilihan</label>
                    <select id="waktu"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 transition">
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
                class="w-full bg-brand-600 hover:bg-brand-800 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-sm disabled:opacity-50 flex justify-center items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                Kirim Konsultasi
            </button>

            <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Data disimpan lokal jika offline, otomatis sync saat online
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: PILIH DOKTER --}}
    <div class="order-1 lg:order-2">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 px-1">
            <h2 class="text-[14px] font-semibold text-slate-800">Pilih Dokter</h2>
            <div class="flex items-center gap-2">
                <span id="cache-label" class="hidden text-[10px] font-semibold bg-amber-100 text-amber-700 px-2 py-1 rounded-lg border border-amber-200">Data Offline</span>
                <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg" id="dokter-count">Memuat...</span>
            </div>
        </div>

        <div id="offline-badge" class="hidden bg-amber-50 border border-amber-200 text-amber-700 text-[13px] px-4 py-3 rounded-xl mb-4 flex items-start gap-2.5">
            <span>⚠️</span>
            <p>Anda <strong>offline</strong>. Menampilkan data dari cache. Konsultasi tetap bisa dikirim.</p>
        </div>

        <div id="dokter-cards" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="col-span-full bg-white rounded-2xl border border-slate-100 px-4 py-12 text-center text-sm text-slate-400 shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3 animate-pulse">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
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
    var DOKTER_CACHE_KEY = 'cache_dokter_list';
    var DOKTER_JADWAL_CACHE_KEY = 'cache_dokter_jadwal_mingguan';
    var HARI_BY_DAY_INDEX = ['minggu','senin','selasa','rabu','kamis','jumat','sabtu'];

    if (!token || !user) window.location.href = '/login';
    if (user) document.getElementById('nama').value = user.name;

    // ── Ambil draft dari chatbot ─────────────────────────────────────
    var draftKeluhan = localStorage.getItem('draft_keluhan') || '';
    var draftUrgensi = localStorage.getItem('draft_urgensi') || 'normal';

    if (draftKeluhan) {
        document.getElementById('keluhan').value = draftKeluhan;
        document.getElementById('urgensi').value = draftUrgensi;
        document.getElementById('chatbot-info').classList.remove('hidden');
        // Bersihkan draft setelah dipakai
        localStorage.removeItem('draft_keluhan');
        localStorage.removeItem('draft_urgensi');
    }

    const db = new Dexie('telemedicine');
    db.version(2).stores({ konsultasi: 'id, status, created_at', auth: 'key' });
    db.table('auth').put({ key: 'session', token, user });

    var dokterList = [];
    var jadwalMingguan = {};

    // ── Load dokter ──────────────────────────────────────────────────
    async function loadDokter() {
        if (navigator.onLine) {
            try {
                var dokterRes = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
                var jadwalRes = await fetch('/api/jadwal/mingguan', { headers: { 'Authorization': 'Bearer ' + token } });
                if (!dokterRes.ok || !jadwalRes.ok) throw new Error('error');

                dokterList = await dokterRes.json();
                jadwalMingguan = await jadwalRes.json();
                localStorage.setItem(DOKTER_CACHE_KEY, JSON.stringify(dokterList));
                localStorage.setItem(DOKTER_JADWAL_CACHE_KEY, JSON.stringify(jadwalMingguan));
                document.getElementById('offline-badge').classList.add('hidden');
                document.getElementById('cache-label').classList.add('hidden');
                renderAll();
            } catch(e) { loadDokterFromCache(); }
        } else { loadDokterFromCache(); }
    }

    async function syncPendingNow() {
        var pending = await db.konsultasi.where('status').equals('pending').toArray();
        var syncedCount = 0;

        for (var item of pending) {
            try {
                var res = await fetch('/api/konsultasi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ local_id: item.id, nama: item.nama, keluhan: item.keluhan, created_at: item.created_at })
                });

                if (res.ok || res.status === 409) {
                    var result = await res.json();
                    await db.konsultasi.update(item.id, {
                        status: 'synced',
                        server_id: result.server_id || null,
                        synced_at: new Date().toISOString()
                    });
                    syncedCount++;
                }
            } catch (e) {}
        }

        return syncedCount;
    }

    async function handleBackOnline() {
        await loadDokter();

        var syncedCount = await syncPendingNow();
        var pendingLeft = await db.konsultasi.where('status').equals('pending').count();

        if (syncedCount > 0 && pendingLeft === 0) {
            var sucBox = document.getElementById('success-box');
            sucBox.textContent = '✅ Sinkronisasi selesai. Mengarahkan ke dashboard...';
            sucBox.classList.remove('hidden');
            setTimeout(function() { window.location.href = '/pasien'; }, 900);
        }
    }

    function loadDokterFromCache() {
        var cached = localStorage.getItem(DOKTER_CACHE_KEY);
        var jadwalCached = localStorage.getItem(DOKTER_JADWAL_CACHE_KEY);
        if (cached) {
            dokterList = JSON.parse(cached);
            jadwalMingguan = jadwalCached ? JSON.parse(jadwalCached) : {};
            document.getElementById('offline-badge').classList.remove('hidden');
            document.getElementById('cache-label').classList.remove('hidden');
            renderAll();
        } else {
            document.getElementById('dokter-cards').innerHTML =
                '<div class="col-span-full bg-amber-50 rounded-2xl border border-amber-200 px-4 py-8 text-center shadow-sm">' +
                '<p class="text-sm font-semibold text-amber-800 mb-1">Koneksi Terputus</p>' +
                '<p class="text-xs text-amber-700">Tidak ada cache dokter. Anda tetap bisa mengirim konsultasi tanpa memilih dokter.</p></div>';
            document.getElementById('dokter-count').textContent = '0 dokter';
            document.getElementById('dokter_id').innerHTML = '<option value="">— Tidak tersedia (offline) —</option>';
            document.getElementById('offline-badge').classList.remove('hidden');
        }
    }

    function renderAll() {
        document.getElementById('dokter-count').textContent = dokterList.length + ' dokter terdaftar';
        renderDokterSelect();
        renderDokterCards();
        applyDoctorAvailability();
    }

    function renderDokterSelect() {
        var sel = document.getElementById('dokter_id');
        sel.innerHTML = '<option value="">— Pilih dokter (opsional) —</option>' +
            dokterList.map(function(d) {
                return '<option value="' + d.id + '">' + d.nama + ' — ' + d.spesialisasi + '</option>';
            }).join('');
        sel.onchange = onDokterChange;
    }

    function onDokterChange() {
        var id = document.getElementById('dokter_id').value;
        var info = document.getElementById('dokter-info');
        var selectedOption = document.querySelector('#dokter_id option[value="' + id + '"]');

        if (selectedOption && selectedOption.disabled) {
            document.getElementById('dokter_id').value = '';
            info.classList.add('hidden');
            return;
        }

        if (!id) { info.classList.add('hidden'); return; }
        var dr = dokterList.find(function(d) { return String(d.id) === String(id); });
        if (!dr) return;
        var isSibuk = dr.status === 'sibuk';
        document.getElementById('dokter-avatar').textContent = dr.inisial;
        document.getElementById('dokter-nama-info').textContent = dr.nama;
        document.getElementById('dokter-spesialis-info').textContent = dr.spesialisasi + (dr.no_str ? ' · STR: ' + dr.no_str : '');
        var badge = document.getElementById('dokter-status-badge');
        badge.textContent = isSibuk ? 'Sibuk' : 'Tersedia';
        badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0 ' +
            (isSibuk ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200');
        document.getElementById('dokter-jadwal-info').textContent =
            dr.hari_praktik && dr.hari_praktik.length ? 'Praktik: ' + dr.hari_praktik.join(', ') : 'Belum ada jadwal';
        info.classList.remove('hidden');
    }

    function renderDokterCards() {
        var container = document.getElementById('dokter-cards');
        if (!dokterList.length) return;
        container.innerHTML = dokterList.map(function(dr) {
            var isSibuk = dr.status === 'sibuk';
            return '<div class="bg-white rounded-2xl border-2 border-slate-100 hover:border-brand-400 p-4 cursor-pointer hover:shadow-md transition-all group" data-id="' + dr.id + '" onclick="pilihDokter(this,\'' + dr.id + '\')">' +
                '<div class="flex items-center gap-3 mb-3">' +
                '<div class="w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 ' + (isSibuk ? 'bg-red-50 text-red-700' : 'bg-brand-50 text-brand-700') + '">' + dr.inisial + '</div>' +
                '<div class="flex-1 min-w-0"><div class="text-[13px] font-semibold text-slate-800 truncate">' + dr.nama + '</div><div class="text-[11px] text-slate-400">' + dr.spesialisasi + '</div></div>' +
                '</div>' +
                '<div class="flex items-center justify-between mb-3 bg-slate-50 px-2.5 py-1.5 rounded-lg">' +
                '<span class="doctor-live-status text-[10px] font-bold ' + (isSibuk ? 'text-red-600' : 'text-emerald-600') + '">' + (isSibuk ? '● Sibuk' : '● Tersedia') + '</span>' +
                '<span class="text-[10px] text-slate-400">' + dr.pasien_aktif + ' Pasien</span>' +
                '</div>' +
                '<div class="flex flex-wrap gap-1">' +
                (dr.hari_praktik || []).slice(0,3).map(function(h) { return '<span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">' + h + '</span>'; }).join('') +
                (dr.hari_praktik && dr.hari_praktik.length > 3 ? '<span class="text-[10px] text-slate-400 self-center ml-1">+' + (dr.hari_praktik.length-3) + '</span>' : '') +
                '<span class="doctor-inactive-hint hidden text-[10px] text-slate-400 self-center ml-1">Tidak aktif di jam ini</span>' +
                '</div></div>';
        }).join('');
    }

    function normalizeName(name) {
        return String(name || '').toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function normalizeRange(text) {
        var m = String(text || '').match(/(\d{1,2}:\d{2})(?::\d{2})?\s*-\s*(\d{1,2}:\d{2})(?::\d{2})?/);
        return m ? (m[1] + ' - ' + m[2]) : String(text || '').trim();
    }

    function activeDoctorNamesForCurrentSlot() {
        var hariIni = HARI_BY_DAY_INDEX[new Date().getDay()];
        var selectedWaktu = normalizeRange(document.getElementById('waktu').value);
        var slotsHariIni = Array.isArray(jadwalMingguan[hariIni]) ? jadwalMingguan[hariIni] : [];

        var setNama = new Set();
        slotsHariIni.forEach(function(slot) {
            if (normalizeRange(slot.waktu) === selectedWaktu) {
                setNama.add(normalizeName(slot.dokter));
            }
        });

        return setNama;
    }

    function applyDoctorAvailability() {
        var activeNames = activeDoctorNamesForCurrentSlot();
        var activeCount = 0;
        var selectedId = document.getElementById('dokter_id').value;
        var selectedStillActive = !selectedId;

        dokterList.forEach(function(dr) {
            var active = activeNames.has(normalizeName(dr.nama));
            var option = document.querySelector('#dokter_id option[value="' + dr.id + '"]');
            var card = document.querySelector('#dokter-cards > div[data-id="' + dr.id + '"]');

            if (active) activeCount++;

            if (option) {
                option.disabled = !active;
                option.textContent = dr.nama + ' — ' + dr.spesialisasi + (active ? '' : ' (Tidak aktif di jam ini)');
            }

            if (card) {
                card.dataset.active = active ? '1' : '0';
                card.classList.toggle('opacity-50', !active);
                card.classList.toggle('grayscale', !active);
                card.classList.toggle('cursor-not-allowed', !active);
                card.classList.toggle('hover:border-brand-400', active);
                card.classList.toggle('hover:shadow-md', active);

                var liveStatus = card.querySelector('.doctor-live-status');
                var inactiveHint = card.querySelector('.doctor-inactive-hint');

                if (liveStatus) {
                    liveStatus.textContent = active ? '● Tersedia' : '● Tidak aktif';
                    liveStatus.className = 'doctor-live-status text-[10px] font-bold ' + (active ? 'text-emerald-600' : 'text-slate-500');
                }
                if (inactiveHint) {
                    inactiveHint.classList.toggle('hidden', active);
                }
            }

            if (String(dr.id) === String(selectedId) && active) {
                selectedStillActive = true;
            }
        });

        if (!selectedStillActive) {
            document.getElementById('dokter_id').value = '';
            document.getElementById('dokter-info').classList.add('hidden');
        }

        document.getElementById('dokter-count').textContent = activeCount + ' dokter aktif di jam ini';
    }

    function pilihDokter(el, id) {
        if (el.dataset.active === '0') return;
        document.getElementById('dokter_id').value = id;
        onDokterChange();
        document.querySelectorAll('#dokter-cards > div[data-id]').forEach(function(c) {
            c.classList.remove('border-brand-500','shadow-lg','bg-brand-50/10');
            c.classList.add('border-slate-100');
        });
        el.classList.remove('border-slate-100');
        el.classList.add('border-brand-500','shadow-lg','bg-brand-50/10');
        if (window.innerWidth < 1024) {
            document.getElementById('nama').scrollIntoView({ behavior:'smooth', block:'center' });
        }
    }

    // ── Kirim konsultasi ─────────────────────────────────────────────
    async function kirimKonsultasi() {
        var keluhan = document.getElementById('keluhan').value.trim();
        var nama    = document.getElementById('nama').value.trim();
        var errBox  = document.getElementById('error-box');
        var sucBox  = document.getElementById('success-box');
        errBox.classList.add('hidden'); sucBox.classList.add('hidden');

        if (!nama)    { errBox.textContent = 'Nama wajib diisi.'; errBox.classList.remove('hidden'); return; }
        if (!keluhan) { errBox.textContent = 'Keluhan wajib diisi.'; errBox.classList.remove('hidden'); return; }

        var btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim...';

        var data = {
            id: 'loc_' + Date.now() + '_' + Math.random().toString(36).slice(2,7),
            nama, keluhan, status: 'pending',
            created_at: new Date().toISOString(), server_id: null
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
                    sucBox.innerHTML = '✅ Konsultasi terkirim! ID: <strong>#KSL-' + String(result.server_id || '?').padStart(3,'0') + '</strong>. Dokter akan segera merespons.';
                } else { sucBox.textContent = '✓ Tersimpan lokal, menunggu sinkronisasi.'; }
            } catch(e) { sucBox.textContent = '✓ Offline — konsultasi tersimpan, dikirim saat online.'; }
        } else {
            sucBox.textContent = '✓ Offline — konsultasi tersimpan lokal, akan dikirim otomatis saat koneksi kembali.';
        }

        sucBox.classList.remove('hidden');
        document.getElementById('keluhan').value = '';
        document.getElementById('dokter_id').value = '';
        document.getElementById('dokter-info').classList.add('hidden');
        document.querySelectorAll('#dokter-cards > div[data-id]').forEach(function(c) {
            c.classList.remove('border-brand-500','shadow-lg','bg-brand-50/10');
            c.classList.add('border-slate-100');
        });
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg> Kirim Konsultasi';
        setTimeout(function() { sucBox.classList.add('hidden'); }, 8000);
    }

    window.addEventListener('online', handleBackOnline);
    document.getElementById('waktu').addEventListener('change', applyDoctorAvailability);

    if (navigator.onLine) {
        setTimeout(function() { handleBackOnline(); }, 300);
    }

    loadDokter();
</script>
@endsection