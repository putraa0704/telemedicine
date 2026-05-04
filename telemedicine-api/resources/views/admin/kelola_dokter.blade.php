@extends('layouts.app')

@section('title', 'Kelola Dokter')
@section('page_title', 'Kelola Dokter')
@section('page_sub', 'Tambah dan kelola akun dokter')

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-[minmax(300px,380px)_1fr] gap-4 lg:gap-5">

    {{-- Form Tambah Dokter --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="text-[13px] font-semibold text-slate-800 mb-4">Tambah Dokter Baru</h2>

        <div id="form-success" class="hidden bg-teal-50 border border-teal-300 text-teal-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>
        <div id="form-error"   class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Nama Lengkap</label>
                <input id="f-name" type="text" placeholder="Dr. Nama Dokter"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Username</label>
                <input id="f-username" type="text" placeholder="username_dokter"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition"/>
                <p class="text-[10px] text-slate-400 mt-1">Akan disimpan sebagai akun login dengan format <span class="font-semibold">username@caremate.id</span>.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Password</label>
                    <input id="f-password" type="password" placeholder="Min. 8 karakter"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">No. HP</label>
                    <input id="f-hp" type="tel" placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Tanggal Lahir</label>
                <input id="f-tgl-lahir" type="date" max="<?php echo date('Y-m-d'); ?>"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Spesialisasi</label>
                <select id="f-spesialis"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white">
                    <option value="Dokter Umum">Dokter Umum</option>
                    <option value="Dokter Anak">Dokter Anak</option>
                    <option value="Kardiologi">Kardiologi</option>
                    <option value="Dermatologi">Dermatologi</option>
                    <option value="Neurologi">Neurologi</option>
                    <option value="Ortopedi">Ortopedi</option>
                    <option value="Psikiatri">Psikiatri</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">No. STR</label>
                <input id="f-str" type="text" placeholder="STR-2024-XXX"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
            </div>
            <button onclick="tambahDokter()"
                class="w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                Tambah Dokter
            </button>
        </div>

        {{-- Form Tambah Jadwal --}}
        <div class="mt-6 pt-5 border-t border-slate-100">
            <h2 class="text-[13px] font-semibold text-slate-800 mb-3">Tambah Jadwal Dokter</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Spesialisasi</label>
                    <select id="j-spesialisasi" onchange="filterDokterJadwalSelect()"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white">
                        <option value="all">— Semua Spesialisasi —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Pilih Dokter</label>
                    <select id="j-dokter"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white">
                        <option value="">— Pilih Dokter —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Hari</label>
                    <select id="j-hari"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white">
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Jam Mulai</label>
                        <input id="j-mulai" type="time" value="08:00"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Jam Selesai</label>
                        <input id="j-selesai" type="time" value="09:00"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
                    </div>
                </div>
                <div id="jadwal-success" class="hidden bg-teal-50 border border-teal-300 text-teal-700 text-sm px-3 py-2.5 rounded-lg"></div>
                <div id="jadwal-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg"></div>

                <button id="btn-tambah-jadwal" onclick="tambahJadwal()"
                    class="w-full bg-teal-600 hover:bg-teal-800 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    Tambah Jadwal
                </button>
            </div>
        </div>
    </div>

    {{-- Daftar Dokter --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
            <span class="text-[13px] font-semibold text-slate-800">Daftar Dokter</span>
            <select id="filter-spesialisasi" onchange="setSpesialisasiFilter(this.value)"
                class="px-2.5 py-1.5 text-[12px] border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white text-slate-700">
                <option value="all">Semua Spesialisasi</option>
            </select>
        </div>
        <div id="dokter-list" class="divide-y divide-slate-100">
            <div class="px-4 py-4 text-sm text-slate-400">Memuat...</div>
        </div>
    </div>
</div>

<div id="edit-jadwal-modal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-slate-900/45" onclick="tutupEditJadwal()"></div>
    <div class="relative h-full w-full flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl border border-slate-200 shadow-xl p-5">
            <div class="flex items-center justify-between mb-4">
                 <h3 class="text-sm font-semibold text-slate-800">Edit Jam Kerja Dokter</h3>
                <button type="button" onclick="tutupEditJadwal()" class="text-slate-400 hover:text-slate-700 text-sm">Tutup</button>
            </div>

            <div id="edit-jadwal-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-3"></div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Dokter</label>
                    <input id="e-dokter" type="text" readonly
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-700" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Hari</label>
                    <select id="e-hari"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white">
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                        <option value="minggu">Minggu</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Jam Mulai</label>
                        <input id="e-mulai" type="time"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Jam Selesai</label>
                        <input id="e-selesai" type="time"
                            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 transition"/>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button id="btn-simpan-jadwal" onclick="simpanEditJadwal()"
                        class="w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                        Simpan Perubahan
                    </button>
                    <button id="btn-hapus-jadwal" onclick="hapusJadwal()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                        Hapus Jadwal
                    </button>
                </div>
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
    if (user && user.role !== 'admin') window.location.href = '/pasien';

    var dokterData = [];
    var editJadwalId = null;
    var dokterAutoRefreshTimer = null;
    var activeSpesialisasiFilter = 'all';
    var HARI_ORDER = {
        senin: 1,
        selasa: 2,
        rabu: 3,
        kamis: 4,
        jumat: 5,
        sabtu: 6,
        minggu: 7,
    };

    function escapeHtml(val) {
        return String(val || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function hariRank(hariKey) {
        return HARI_ORDER[String(hariKey || '').toLowerCase()] || 99;
    }

    function sortedHariPraktik(days) {
        return (Array.isArray(days) ? days.slice() : []).sort(function(a, b) {
            return hariRank(a) - hariRank(b);
        });
    }

    function normalizeSpesialisasi(value) {
        return String(value || '').trim();
    }

    function sortedGroups(groups) {
        return (Array.isArray(groups) ? groups.slice() : [])
            .map(function(g) {
                var slots = (Array.isArray(g.slots) ? g.slots.slice() : []).sort(function(x, y) {
                    return String(x.jam_mulai || '').localeCompare(String(y.jam_mulai || ''));
                });
                return Object.assign({}, g, { slots: slots });
            })
            .sort(function(a, b) {
                return hariRank(a.hari_key) - hariRank(b.hari_key);
            });
    }

    async function loadDokter() {
        try {
            var headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };
            var bust = '_=' + Date.now();
            var res = await fetch('/api/supabase/tim-dokter?' + bust, {
                headers: headers,
                cache: 'no-store'
            });

            if (!res.ok) {
                res = await fetch('/api/tim-dokter?' + bust, {
                    headers: headers,
                    cache: 'no-store'
                });
            }

            dokterData = await res.json();
            if (!Array.isArray(dokterData)) {
                throw new Error('Format data dokter tidak valid');
            }
            populateSpesialisasiFilter();
            populateJadwalSpesialisFilter();
            renderDokter();
            filterDokterJadwalSelect();
        } catch(e) {
            document.getElementById('dokter-list').innerHTML = '<div class="px-4 py-4 text-sm text-red-400">Gagal memuat</div>';
        }
    }

    function setupDokterAutoRefresh() {
        if (dokterAutoRefreshTimer) clearInterval(dokterAutoRefreshTimer);

        // Pull data berkala agar perubahan dari tab/perangkat lain ikut muncul.
        dokterAutoRefreshTimer = setInterval(function() {
            if (document.hidden) return;
            if (!navigator.onLine) return;
            loadDokter();
        }, 10000);

        window.addEventListener('focus', function() {
            if (navigator.onLine) loadDokter();
        });

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && navigator.onLine) loadDokter();
        });

        window.addEventListener('online', loadDokter);

        // Hook agar jika layout menerima event sinkronisasi dari SW, halaman ikut refresh.
        window.renderUI = loadDokter;
    }

    function populateJadwalSpesialisFilter() {
        var sel = document.getElementById('j-spesialisasi');
        if (!sel) return;

        var unique = Array.from(new Set(
            dokterData
                .map(function(d) { return normalizeSpesialisasi(d.spesialisasi); })
                .filter(Boolean)
        ));

        unique.sort(function(a, b) {
            if (a === 'Dokter Umum') return -1;
            if (b === 'Dokter Umum') return 1;
            return a.localeCompare(b, 'id');
        });

        var currentVal = sel.value;

        sel.innerHTML = '<option value="all">— Semua Spesialisasi —</option>' +
            unique.map(function(s) {
                return '<option value="' + escapeHtml(s) + '">' + escapeHtml(s) + '</option>';
            }).join('');
            
        if (currentVal && unique.includes(currentVal)) {
            sel.value = currentVal;
        } else {
            sel.value = 'all';
        }
    }

    function filterDokterJadwalSelect() {
        var selSpesialisasi = document.getElementById('j-spesialisasi');
        var spesialisasi = selSpesialisasi ? selSpesialisasi.value : 'all';
        populateDokterSelect(spesialisasi);
    }

    function populateDokterSelect(spesialisasi) {
        if (!spesialisasi) spesialisasi = 'all';
        var sel = document.getElementById('j-dokter');
        if (!sel) return;

        var filtered = dokterData;
        if (spesialisasi !== 'all') {
            filtered = dokterData.filter(function(d) {
                return normalizeSpesialisasi(d.spesialisasi) === spesialisasi;
            });
        }

        sel.innerHTML = '<option value="">— Pilih Dokter —</option>' +
            filtered.map(d => `<option value="${d.id}">${d.nama}</option>`).join('');
    }

    function populateSpesialisasiFilter() {
        var sel = document.getElementById('filter-spesialisasi');
        if (!sel) return;

        var unique = Array.from(new Set(
            dokterData
                .map(function(d) { return normalizeSpesialisasi(d.spesialisasi); })
                .filter(Boolean)
        ));

        unique.sort(function(a, b) {
            if (a === 'Dokter Umum') return -1;
            if (b === 'Dokter Umum') return 1;
            return a.localeCompare(b, 'id');
        });

        sel.innerHTML = '<option value="all">Semua Spesialisasi</option>' +
            unique.map(function(s) {
                return '<option value="' + escapeHtml(s) + '">' + escapeHtml(s) + '</option>';
            }).join('');

        sel.value = activeSpesialisasiFilter;
        if (sel.value !== activeSpesialisasiFilter) {
            activeSpesialisasiFilter = 'all';
            sel.value = 'all';
        }
    }

    function setSpesialisasiFilter(value) {
        activeSpesialisasiFilter = value || 'all';
        renderDokter();
    }

    function renderDokterCard(dr) {
        var hariPraktik = sortedHariPraktik(dr.hari_praktik || []);
        var groups = sortedGroups(dr.jadwal || []);

        return `
            <div class="px-4 py-3">
                <div class="flex items-start sm:items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div class="flex-1">
                        <div class="text-[13px] font-semibold text-slate-800">${dr.nama}</div>
                        <div class="text-[11px] text-slate-400">${dr.spesialisasi} · STR: ${dr.no_str || '—'}</div>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-teal-50 text-teal-700 border border-teal-200'}">
                        ${dr.status === 'sibuk' ? 'Sibuk' : 'Tersedia'}
                    </span>
                </div>
                <div class="flex flex-wrap gap-1 ml-0 sm:ml-12">
                    ${hariPraktik.map(h =>
                        `<span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">${h}</span>`
                    ).join('')}
                    ${!hariPraktik.length ? '<span class="text-[11px] text-slate-400 italic">Belum ada jadwal</span>' : ''}
                </div>
                <div class="ml-0 sm:ml-12 mt-2 space-y-2">
                    ${groups.map(group => `
                        <div>
                            <div class="text-[11px] font-semibold text-slate-500 mb-1">${group.hari}</div>
                            <div class="flex flex-wrap gap-1.5">
                                ${(group.slots || []).map(slot => `
                                    <button type="button"
                                        onclick="bukaEditJadwal(${slot.id}, '${escapeHtml(dr.nama)}', '${group.hari_key}', '${slot.jam_mulai}', '${slot.jam_selesai}')"
                                        class="text-[10px] px-2 py-1 rounded-md border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                        ${slot.waktu} · Edit
                                    </button>
                                `).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function renderDokter() {
        var list = document.getElementById('dokter-list');
        if (!dokterData.length) {
            list.innerHTML = '<div class="px-4 py-4 text-sm text-slate-400 italic">Belum ada dokter</div>';
            return;
        }

        var filteredDokter = dokterData.filter(function(dr) {
            if (activeSpesialisasiFilter === 'all') return true;
            return normalizeSpesialisasi(dr.spesialisasi) === activeSpesialisasiFilter;
        });

        if (!filteredDokter.length) {
            list.innerHTML = '<div class="px-4 py-4 text-sm text-slate-400 italic">Tidak ada dokter untuk spesialisasi ini</div>';
            return;
        }

        var grouped = {};
        filteredDokter.forEach(function(dr) {
            var key = (dr.spesialisasi || 'Lainnya').trim() || 'Lainnya';
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(dr);
        });

        var kategori = Object.keys(grouped).sort(function(a, b) {
            if (a === 'Dokter Umum') return -1;
            if (b === 'Dokter Umum') return 1;
            return a.localeCompare(b, 'id');
        });

        list.innerHTML = kategori.map(function(kat) {
            var doctors = grouped[kat];
            return `
                <div class="border-b border-slate-100 last:border-0">
                    <div class="px-4 py-2.5 bg-slate-50 flex items-center justify-between">
                        <span class="text-[12px] font-semibold text-slate-700">${kat}</span>
                        <span class="text-[10px] font-semibold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full">${doctors.length} dokter</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        ${doctors.map(function(dr) { return renderDokterCard(dr); }).join('')}
                    </div>
                </div>
            `;
        }).join('');
    }

    function bukaEditJadwal(id, namaDokter, hari, jamMulai, jamSelesai) {
        editJadwalId = id;
        document.getElementById('edit-jadwal-error').classList.add('hidden');
        document.getElementById('e-dokter').value = namaDokter;
        document.getElementById('e-hari').value = hari;
        document.getElementById('e-mulai').value = jamMulai ? jamMulai.substring(0, 5) : '';
        document.getElementById('e-selesai').value = jamSelesai ? jamSelesai.substring(0, 5) : '';
        document.getElementById('edit-jadwal-modal').classList.remove('hidden');
    }

    function tutupEditJadwal() {
        editJadwalId = null;
        document.getElementById('edit-jadwal-modal').classList.add('hidden');
    }

    async function tambahDokter() {
        var sucEl  = document.getElementById('form-success');
        var errEl  = document.getElementById('form-error');
        sucEl.classList.add('hidden'); errEl.classList.add('hidden');

        var username = (document.getElementById('f-username').value || '').trim().toLowerCase();
        var usernameSanitized = username.replace(/[^a-z0-9._-]/g, '');

        if (!usernameSanitized) {
            errEl.textContent = 'Username wajib diisi.';
            errEl.classList.remove('hidden');
            return;
        }

        if (usernameSanitized.length < 3) {
            errEl.textContent = 'Username minimal 3 karakter.';
            errEl.classList.remove('hidden');
            return;
        }

        var generatedEmail = usernameSanitized + '@caremate.id';

        var payload = {
            name:                  document.getElementById('f-name').value.trim(),
            email:                 generatedEmail,
            password:              document.getElementById('f-password').value,
            password_confirmation: document.getElementById('f-password').value,
            no_hp:                 document.getElementById('f-hp').value.trim(),
            tanggal_lahir:         document.getElementById('f-tgl-lahir').value,
            spesialisasi:          document.getElementById('f-spesialis').value,
            no_str:                document.getElementById('f-str').value.trim(),
            role:                  'dokter',
        };

        if (!payload.name || !payload.password || !payload.tanggal_lahir) {
            errEl.textContent = 'Nama, username, tanggal lahir, dan password wajib diisi.';
            errEl.classList.remove('hidden'); return;
        }

        try {
            // Daftarkan via endpoint admin khusus dokter
            var res  = await fetch('/api/auth/register-dokter', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            });
            var data = await res.json();

            if (!data.success) {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Gagal menambahkan dokter');
                errEl.textContent = msgs; errEl.classList.remove('hidden'); return;
            }

            sucEl.textContent = '✓ Dokter ' + payload.name + ' berhasil ditambahkan!';
            sucEl.classList.remove('hidden');
            ['f-name','f-username','f-password','f-hp','f-tgl-lahir','f-str'].forEach(id => document.getElementById(id).value = '');
            await loadDokter();
        } catch(e) {
            errEl.textContent = 'Gagal terhubung ke server'; errEl.classList.remove('hidden');
        }
    }

    async function tambahJadwal() {
        var sucEl = document.getElementById('jadwal-success');
        var errEl = document.getElementById('jadwal-error');
        var btnEl = document.getElementById('btn-tambah-jadwal');
        sucEl.classList.add('hidden');
        errEl.classList.add('hidden');

        var dokterId = document.getElementById('j-dokter').value;
        var hari     = document.getElementById('j-hari').value;
        var mulai    = document.getElementById('j-mulai').value;
        var selesai  = document.getElementById('j-selesai').value;

        if (!dokterId) {
            errEl.textContent = 'Pilih dokter terlebih dahulu.';
            errEl.classList.remove('hidden');
            return;
        }
        if (!mulai || !selesai) {
            errEl.textContent = 'Jam mulai dan selesai wajib diisi.';
            errEl.classList.remove('hidden');
            return;
        }

        btnEl.disabled = true;
        btnEl.textContent = 'Menyimpan...';

        try {
            var res  = await fetch('/api/jadwal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ dokter_id: dokterId, hari, jam_mulai: mulai, jam_selesai: selesai })
            });

            var text = await res.text();
            var data = {};
            try { data = text ? JSON.parse(text) : {}; } catch (e) {}

            if (res.ok && data.success) {
                sucEl.textContent = '✓ Jadwal berhasil ditambahkan.';
                sucEl.classList.remove('hidden');
                await loadDokter();
                document.getElementById('j-mulai').value = '08:00';
                document.getElementById('j-selesai').value = '09:00';
            } else {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Gagal menambahkan jadwal');
                errEl.textContent = msgs;
                errEl.classList.remove('hidden');
            }
        } catch(e) {
            errEl.textContent = 'Gagal terhubung ke server';
            errEl.classList.remove('hidden');
        } finally {
            btnEl.disabled = false;
            btnEl.textContent = 'Tambah Jadwal';
        }
    }

    async function simpanEditJadwal() {
        if (!editJadwalId) return;

        var errEl = document.getElementById('edit-jadwal-error');
        var btnEl = document.getElementById('btn-simpan-jadwal');
        errEl.classList.add('hidden');

        var hari = document.getElementById('e-hari').value;
        var mulai = document.getElementById('e-mulai').value;
        var selesai = document.getElementById('e-selesai').value;

        if (!hari || !mulai || !selesai) {
            errEl.textContent = 'Hari, jam mulai, dan jam selesai wajib diisi.';
            errEl.classList.remove('hidden');
            return;
        }

        btnEl.disabled = true;
        btnEl.textContent = 'Menyimpan...';

        try {
            var res = await fetch('/api/jadwal/' + editJadwalId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ hari: hari, jam_mulai: mulai, jam_selesai: selesai })
            });
            var data = await res.json();

            if (!res.ok || !data.success) {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Gagal menyimpan perubahan jadwal');
                errEl.textContent = msgs;
                errEl.classList.remove('hidden');
                return;
            }

            tutupEditJadwal();
            await loadDokter();
            alert('✅ Jadwal dokter berhasil diperbarui.');
        } catch (e) {
            errEl.textContent = 'Gagal terhubung ke server';
            errEl.classList.remove('hidden');
        } finally {
            btnEl.disabled = false;
            btnEl.textContent = 'Simpan Perubahan';
        }
    }

    async function hapusJadwal() {
        if (!editJadwalId) return;

        var errEl = document.getElementById('edit-jadwal-error');
        var btnHapus = document.getElementById('btn-hapus-jadwal');
        var btnSimpan = document.getElementById('btn-simpan-jadwal');
        errEl.classList.add('hidden');

        var ok = confirm('Yakin ingin menghapus jadwal ini?');
        if (!ok) return;

        btnHapus.disabled = true;
        btnSimpan.disabled = true;
        btnHapus.textContent = 'Menghapus...';

        try {
            var res = await fetch('/api/jadwal/' + editJadwalId, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
            });
            var data = await res.json();

            if (!res.ok || !data.success) {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Gagal menghapus jadwal');
                errEl.textContent = msgs;
                errEl.classList.remove('hidden');
                return;
            }

            tutupEditJadwal();
            await loadDokter();
            alert('✅ Jadwal dokter berhasil dihapus.');
        } catch (e) {
            errEl.textContent = 'Gagal terhubung ke server';
            errEl.classList.remove('hidden');
        } finally {
            btnHapus.disabled = false;
            btnSimpan.disabled = false;
            btnHapus.textContent = 'Hapus Jadwal';
        }
    }

    loadDokter();
    setupDokterAutoRefresh();
</script>
@endsection
