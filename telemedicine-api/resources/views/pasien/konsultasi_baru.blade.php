@extends('layouts.app')

@section('title', 'Konsultasi Baru')
@section('page_title', 'Konsultasi Baru')
@section('page_sub', 'Buat permintaan konsultasi baru')
@section('nav_new', 'active-nav')

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl">
    <h2 class="text-sm font-semibold text-slate-800 mb-5">Formulir Konsultasi</h2>

    <div id="success-box" class="hidden bg-teal-50 border border-teal-300 text-teal-700 text-sm px-3 py-2.5 rounded-lg mb-4">
        ✓ Konsultasi berhasil dikirim!
    </div>
    <div id="error-box" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>

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
            <select id="dokter"
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition bg-white">
                <option>Dr. Hendra — Umum</option>
                <option>Dr. Sari — Anak</option>
                <option>Dr. Ahmad — Kardiologi</option>
                <option>Dr. Maya — Dermatologi</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Keluhan Utama</label>
            <textarea id="keluhan" rows="4" placeholder="Deskripsikan gejala atau keluhan anda..."
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 resize-none transition"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Tingkat Urgensi</label>
                <select id="urgensi"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 bg-white transition">
                    <option>Normal</option>
                    <option>Urgent</option>
                    <option>Darurat</option>
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

        <button onclick="kirimKonsultasi()"
            class="w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
            Kirim Konsultasi
        </button>
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

    async function kirimKonsultasi() {
        var keluhan = document.getElementById('keluhan').value.trim();
        var nama    = document.getElementById('nama').value.trim();
        var errBox  = document.getElementById('error-box');
        var sucBox  = document.getElementById('success-box');
        errBox.classList.add('hidden'); sucBox.classList.add('hidden');

        if (!keluhan || !nama) {
            errBox.textContent = 'Nama dan keluhan wajib diisi.';
            errBox.classList.remove('hidden'); return;
        }

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
                }
            } catch(e) {}
        }

        sucBox.classList.remove('hidden');
        document.getElementById('keluhan').value = '';
        setTimeout(() => sucBox.classList.add('hidden'), 3000);
    }
</script>
@endsection