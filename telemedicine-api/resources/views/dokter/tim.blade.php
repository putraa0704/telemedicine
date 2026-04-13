@extends('layouts.app')

@section('title', 'Tim Dokter')
@section('page_title', 'Tim Dokter')
@section('page_sub', 'Daftar dokter dan keahliannya')
@section('nav_tim', 'active-nav')

@section('content')

<div class="grid grid-cols-2 gap-4" id="tim-grid">
    <div class="col-span-2 text-sm text-slate-400 text-center py-8">Memuat data dokter...</div>
</div>

{{-- Modal Detail --}}
<div id="modal-overlay" class="hidden fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 w-full max-w-sm">

        <div class="flex items-center gap-3 mb-5">
            <div id="modal-avatar" class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 text-lg font-bold flex-shrink-0"></div>
            <div>
                <div id="modal-nama" class="text-[15px] font-bold text-slate-800"></div>
                <div id="modal-spesialis" class="text-[12px] text-slate-400 mt-0.5"></div>
            </div>
        </div>

        <div class="divide-y divide-slate-100 mb-5 text-[12px]">
            <div class="flex justify-between py-2">
                <span class="text-slate-500">Status</span>
                <span id="modal-status" class="font-semibold"></span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500">Pasien Aktif</span>
                <span id="modal-pasien" class="font-semibold text-slate-800"></span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500">No. STR</span>
                <span id="modal-str" class="font-semibold text-slate-800"></span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500">No. HP</span>
                <span id="modal-hp" class="font-semibold text-slate-800"></span>
            </div>
            <div class="py-2">
                <span class="text-slate-500 block mb-1.5">Hari Praktik</span>
                <div id="modal-hari" class="flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="flex gap-2">
            <button onclick="tutupModal()" class="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium py-2 rounded-lg transition-colors">Tutup</button>
            <button onclick="pesanKonsultasi()" class="flex-1 bg-blue-700 hover:bg-blue-900 text-white text-sm font-semibold py-2 rounded-lg transition-colors">Konsultasi</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    var selectedDokter = null;

    async function loadTimDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            renderTim(data);
        } catch(e) {
            document.getElementById('tim-grid').innerHTML =
                '<div class="col-span-2 text-sm text-red-400 text-center py-8">Gagal memuat data dokter</div>';
        }
    }

    function renderTim(list) {
        var grid = document.getElementById('tim-grid');
        if (!list.length) {
            grid.innerHTML = '<div class="col-span-2 text-sm text-slate-400 italic text-center py-8">Belum ada dokter terdaftar</div>';
            return;
        }

        grid.innerHTML = list.map(dr => `
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition-shadow cursor-default">

                {{-- Header --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-[14px] font-bold flex-shrink-0
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                        ${dr.inisial}
                    </div>
                    <div>
                        <div class="text-[14px] font-semibold text-slate-800">${dr.nama}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">${dr.spesialisasi}</div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-2 mb-4">
                    <div class="mr-1">
                        <div class="text-[22px] font-bold text-slate-800 leading-none">${dr.pasien_aktif}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Pasien Aktif</div>
                    </div>
                    <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full
                        ${dr.status === 'sibuk' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-teal-50 text-teal-700 border border-teal-200'}">
                        ${dr.status === 'sibuk' ? 'Sibuk' : 'Tersedia'}
                    </span>
                    <button onclick='bukaModal(${JSON.stringify(dr).replace(/'/g,"&#39;")})'
                        class="ml-auto text-[11px] font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                        Lihat Jadwal
                    </button>
                </div>

                {{-- Hari praktik --}}
                <div class="flex flex-wrap gap-1.5">
                    ${(dr.hari_praktik || []).map(h =>
                        `<span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">${h}</span>`
                    ).join('')}
                </div>
            </div>
        `).join('');
    }

    function bukaModal(dr) {
        selectedDokter = dr;

        var isSibuk = dr.status === 'sibuk';
        document.getElementById('modal-avatar').textContent = dr.inisial;
        document.getElementById('modal-avatar').className   =
            'w-14 h-14 rounded-full flex items-center justify-center text-lg font-bold flex-shrink-0 ' +
            (isSibuk ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800');
        document.getElementById('modal-nama').textContent     = dr.nama;
        document.getElementById('modal-spesialis').textContent= dr.spesialisasi;
        document.getElementById('modal-pasien').textContent   = dr.pasien_aktif + ' pasien';
        document.getElementById('modal-str').textContent      = dr.no_str || '—';
        document.getElementById('modal-hp').textContent       = dr.no_hp || '—';

        var statusEl = document.getElementById('modal-status');
        statusEl.textContent = isSibuk ? 'Sibuk' : 'Tersedia';
        statusEl.className   = 'font-semibold ' + (isSibuk ? 'text-red-500' : 'text-teal-600');

        document.getElementById('modal-hari').innerHTML = (dr.hari_praktik || []).map(h =>
            `<span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">${h}</span>`
        ).join('');

        document.getElementById('modal-overlay').classList.remove('hidden');
    }

    function tutupModal() {
        document.getElementById('modal-overlay').classList.add('hidden');
        selectedDokter = null;
    }

    function pesanKonsultasi() {
        tutupModal();
        window.location.href = '/konsultasi/baru';
    }

    document.getElementById('modal-overlay').addEventListener('click', e => { if (e.target === e.currentTarget) tutupModal(); });

    loadTimDokter();
</script>
@endsection