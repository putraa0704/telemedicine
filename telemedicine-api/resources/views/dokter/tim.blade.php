@extends('layouts.app')

@section('title', 'Tim Dokter')
@section('page_title', 'Tim Dokter')
@section('page_sub', 'Daftar dokter dan keahliannya')
@section('nav_tim', 'active')

@section('content')

{{-- Search & Filter --}}
<div class="flex flex-col sm:flex-row gap-3 mb-5">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="search-input" oninput="filterDokter()" placeholder="Cari dokter..."
            class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 bg-white"/>
    </div>
    <select id="filter-spesialis" onchange="filterDokter()"
        class="px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-white min-w-[160px]">
        <option value="">Semua Spesialisasi</option>
    </select>
</div>

{{-- Grid --}}
<div id="tim-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    <div class="col-span-full text-center py-12 text-slate-400 text-sm">Memuat data dokter...</div>
</div>

{{-- Modal Detail --}}
<div id="modal-overlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) tutupModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 pt-6 pb-5">
            <div class="flex items-center gap-4 mb-5">
                <div id="modal-avatar" class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-800 text-lg font-bold flex-shrink-0"></div>
                <div>
                    <div id="modal-nama" class="text-[15px] font-bold text-slate-800"></div>
                    <div id="modal-spesialis" class="text-[12px] text-slate-400 mt-0.5"></div>
                    <div id="modal-status" class="mt-1.5"></div>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-3 mb-4 space-y-2.5">
                <div class="flex justify-between items-center text-[12px]">
                    <span class="text-slate-500">Pasien Aktif</span>
                    <span class="font-semibold text-slate-800" id="modal-pasien"></span>
                </div>
                <div class="flex justify-between items-center text-[12px]">
                    <span class="text-slate-500">No. STR</span>
                    <span class="font-semibold text-slate-700" id="modal-str"></span>
                </div>
                <div class="flex justify-between items-center text-[12px]">
                    <span class="text-slate-500">No. HP</span>
                    <span class="font-semibold text-slate-700" id="modal-hp"></span>
                </div>
            </div>

            <div class="mb-5">
                <p class="text-[11px] font-semibold text-slate-500 mb-2">Hari Praktik</p>
                <div id="modal-hari" class="flex flex-wrap gap-1.5"></div>
            </div>

            <div class="flex gap-2">
                <button onclick="tutupModal()" class="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium py-2.5 rounded-xl transition-colors">Tutup</button>
                <button onclick="pesanKonsultasi()" class="flex-1 bg-brand-600 hover:bg-brand-800 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">Konsultasi</button>
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

    var allDokter = [];

    async function loadTimDokter() {
        try {
            var res  = await fetch('/api/tim-dokter', { headers: { 'Authorization': 'Bearer ' + token } });
            allDokter = await res.json();

            // Populate spesialis filter
            var spesialisSet = [...new Set(allDokter.map(d => d.spesialisasi))];
            var sel = document.getElementById('filter-spesialis');
            sel.innerHTML = '<option value="">Semua Spesialisasi</option>' +
                spesialisSet.map(s => '<option value="' + s + '">' + s + '</option>').join('');

            filterDokter();
        } catch(e) {
            document.getElementById('tim-grid').innerHTML = '<div class="col-span-full text-center py-12 text-slate-400 text-sm">Gagal memuat data</div>';
        }
    }

    function filterDokter() {
        var query  = document.getElementById('search-input').value.toLowerCase();
        var spesial = document.getElementById('filter-spesialis').value;
        var filtered = allDokter.filter(d => {
            var matchQ = !query || d.nama.toLowerCase().includes(query) || d.spesialisasi.toLowerCase().includes(query);
            var matchS = !spesial || d.spesialisasi === spesial;
            return matchQ && matchS;
        });
        renderTim(filtered);
    }

    function renderTim(list) {
        var grid = document.getElementById('tim-grid');
        if (!list.length) {
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400 text-sm">Tidak ada dokter ditemukan</div>';
            return;
        }
        grid.innerHTML = list.map(dr => {
            var isSibuk = dr.status === 'sibuk';
            return `
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-brand-200 transition-all p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-[14px] font-bold flex-shrink-0
                            ${isSibuk ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800'}">
                            ${dr.inisial}
                        </div>
                        <div>
                            <div class="text-[14px] font-semibold text-slate-800">${dr.nama}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">${dr.spesialisasi}</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-lg flex-shrink-0
                        ${isSibuk ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                        ${isSibuk ? 'Sibuk' : 'Tersedia'}
                    </span>
                </div>

                <div class="flex items-center gap-4 mb-4 py-3 border-y border-slate-50">
                    <div class="text-center">
                        <div class="text-xl font-bold text-slate-800">${dr.pasien_aktif}</div>
                        <div class="text-[10px] text-slate-400">Pasien Aktif</div>
                    </div>
                    <div class="h-8 w-px bg-slate-100"></div>
                    <div class="flex flex-wrap gap-1">
                        ${(dr.hari_praktik || []).slice(0,4).map(h =>
                            '<span class="text-[9px] font-medium bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full">' + h + '</span>'
                        ).join('')}
                        ${dr.hari_praktik && dr.hari_praktik.length > 4 ? '<span class="text-[9px] text-slate-400">+' + (dr.hari_praktik.length-4) + '</span>' : ''}
                        ${!dr.hari_praktik || !dr.hari_praktik.length ? '<span class="text-[10px] text-slate-400 italic">Belum ada jadwal</span>' : ''}
                    </div>
                </div>

                <button onclick='bukaModal(${JSON.stringify(dr).replace(/'/g,"&#39;")})'
                    class="w-full text-center text-[12px] font-semibold text-brand-600 bg-blue-50 hover:bg-blue-100 py-2 rounded-xl transition-colors">
                    Lihat Detail & Jadwal
                </button>
            </div>
            `;
        }).join('');
    }

    function bukaModal(dr) {
        var isSibuk = dr.status === 'sibuk';
        document.getElementById('modal-avatar').textContent = dr.inisial;
        document.getElementById('modal-avatar').className  = 'w-14 h-14 rounded-2xl flex items-center justify-center text-lg font-bold flex-shrink-0 ' + (isSibuk ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800');
        document.getElementById('modal-nama').textContent     = dr.nama;
        document.getElementById('modal-spesialis').textContent= dr.spesialisasi;
        document.getElementById('modal-pasien').textContent   = dr.pasien_aktif + ' pasien';
        document.getElementById('modal-str').textContent      = dr.no_str || '—';
        document.getElementById('modal-hp').textContent       = dr.no_hp || '—';
        document.getElementById('modal-status').innerHTML     = isSibuk
            ? '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200">Sibuk</span>'
            : '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia</span>';
        document.getElementById('modal-hari').innerHTML = (dr.hari_praktik || []).length
            ? dr.hari_praktik.map(h => '<span class="text-[10px] font-medium bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg">' + h + '</span>').join('')
            : '<span class="text-[11px] text-slate-400 italic">Belum ada jadwal</span>';
        document.getElementById('modal-overlay').classList.remove('hidden');
    }

    function tutupModal() {
        document.getElementById('modal-overlay').classList.add('hidden');
    }

    function pesanKonsultasi() {
        tutupModal();
        window.location.href = '/konsultasi/baru';
    }

    loadTimDokter();
</script>
@endsection