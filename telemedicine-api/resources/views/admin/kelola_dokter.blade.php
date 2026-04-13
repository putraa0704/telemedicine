@extends('layouts.app')

@section('title', 'Kelola Dokter')
@section('page_title', 'Kelola Dokter')
@section('page_sub', 'Tambah dan kelola akun dokter')

@section('content')

<div class="grid grid-cols-[1fr_1.4fr] gap-5">

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
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                <input id="f-email" type="email" placeholder="dokter@mediconnect.id"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition"/>
            </div>
            <div class="grid grid-cols-2 gap-2">
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
                <div class="grid grid-cols-2 gap-2">
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
                <button onclick="tambahJadwal()"
                    class="w-full bg-teal-600 hover:bg-teal-800 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    Tambah Jadwal
                </button>
            </div>
        </div>
    </div>

    {{-- Daftar Dokter --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-4 py-3 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Daftar Dokter</span>
        </div>
        <div id="dokter-list" class="divide-y divide-slate-100">
            <div class="px-4 py-4 text-sm text-slate-400">Memuat...</div>
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

    async function loadDokter() {
        try {
            var res   = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            dokterData = await res.json();
            renderDokter();
            populateDokterSelect();
        } catch(e) {
            document.getElementById('dokter-list').innerHTML = '<div class="px-4 py-4 text-sm text-red-400">Gagal memuat</div>';
        }
    }

    function populateDokterSelect() {
        var sel = document.getElementById('j-dokter');
        sel.innerHTML = '<option value="">— Pilih Dokter —</option>' +
            dokterData.map(d => `<option value="${d.id}">${d.nama}</option>`).join('');
    }

    function renderDokter() {
        var list = document.getElementById('dokter-list');
        if (!dokterData.length) {
            list.innerHTML = '<div class="px-4 py-4 text-sm text-slate-400 italic">Belum ada dokter</div>';
            return;
        }
        list.innerHTML = dokterData.map(dr => `
            <div class="px-4 py-3">
                <div class="flex items-center gap-3 mb-2">
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
                {{-- Hari praktik --}}
                <div class="flex flex-wrap gap-1 ml-12">
                    ${(dr.hari_praktik || []).map(h =>
                        `<span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">${h}</span>`
                    ).join('')}
                    ${!dr.hari_praktik?.length ? '<span class="text-[11px] text-slate-400 italic">Belum ada jadwal</span>' : ''}
                </div>
            </div>
        `).join('');
    }

    async function tambahDokter() {
        var sucEl  = document.getElementById('form-success');
        var errEl  = document.getElementById('form-error');
        sucEl.classList.add('hidden'); errEl.classList.add('hidden');

        var payload = {
            name:                  document.getElementById('f-name').value.trim(),
            email:                 document.getElementById('f-email').value.trim(),
            password:              document.getElementById('f-password').value,
            password_confirmation: document.getElementById('f-password').value,
            no_hp:                 document.getElementById('f-hp').value.trim(),
            spesialisasi:          document.getElementById('f-spesialis').value,
            no_str:                document.getElementById('f-str').value.trim(),
            role:                  'dokter',
        };

        if (!payload.name || !payload.email || !payload.password) {
            errEl.textContent = 'Nama, email, dan password wajib diisi.';
            errEl.classList.remove('hidden'); return;
        }

        try {
            // Daftarkan via endpoint register (hanya admin yang bisa tambah dokter secara langsung)
            var res  = await fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            });
            var data = await res.json();

            if (!data.success) {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Gagal menambahkan dokter');
                errEl.textContent = msgs; errEl.classList.remove('hidden'); return;
            }

            sucEl.textContent = '✓ Dokter ' + payload.name + ' berhasil ditambahkan!';
            sucEl.classList.remove('hidden');
            ['f-name','f-email','f-password','f-hp','f-str'].forEach(id => document.getElementById(id).value = '');
            await loadDokter();
        } catch(e) {
            errEl.textContent = 'Gagal terhubung ke server'; errEl.classList.remove('hidden');
        }
    }

    async function tambahJadwal() {
        var dokterId = document.getElementById('j-dokter').value;
        var hari     = document.getElementById('j-hari').value;
        var mulai    = document.getElementById('j-mulai').value;
        var selesai  = document.getElementById('j-selesai').value;

        if (!dokterId) { alert('Pilih dokter terlebih dahulu'); return; }
        if (!mulai || !selesai) { alert('Jam mulai dan selesai wajib diisi'); return; }

        try {
            var res  = await fetch('/api/jadwal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ dokter_id: dokterId, hari, jam_mulai: mulai, jam_selesai: selesai })
            });
            var data = await res.json();

            if (data.success) {
                alert('✅ Jadwal berhasil ditambahkan!');
                await loadDokter();
            } else {
                alert(data.message || 'Gagal menambahkan jadwal');
            }
        } catch(e) {
            alert('Gagal terhubung ke server');
        }
    }

    loadDokter();
</script>
@endsection