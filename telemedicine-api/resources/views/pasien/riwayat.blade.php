@extends('layouts.app')

@section('title', 'Riwayat Konsultasi')
@section('page_title', 'Riwayat')
@section('page_sub', 'Konsultasi yang sudah selesai')
@section('nav_riwayat', 'active-nav')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Total Konsultasi</div>
        <div class="text-3xl font-bold text-slate-800 leading-none" id="stat-total">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Selesai</div>
        <div class="text-3xl font-bold text-teal-600 leading-none" id="stat-done">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Menunggu Jawaban</div>
        <div class="text-3xl font-bold text-amber-600 leading-none" id="stat-pending">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Sudah Dijawab</div>
        <div class="text-3xl font-bold text-blue-700 leading-none" id="stat-answered">—</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="flex items-center gap-3 mb-4">
    <span class="text-[13px] font-semibold text-slate-800 mr-1">Riwayat Konsultasi</span>
    <div class="flex gap-1.5 ml-auto">
        <button onclick="setFilter('all')" id="f-all"
            class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 transition-colors bg-blue-700 text-white border-blue-700">
            Semua
        </button>
        <button onclick="setFilter('done')" id="f-done"
            class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 transition-colors text-slate-600 hover:bg-slate-50">
            Selesai
        </button>
        <button onclick="setFilter('received')" id="f-received"
            class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 transition-colors text-slate-600 hover:bg-slate-50">
            Menunggu
        </button>
        <button onclick="setFilter('in_review')" id="f-in_review"
            class="filter-btn text-[11px] font-semibold px-3 py-1.5 rounded-lg border border-slate-200 transition-colors text-slate-600 hover:bg-slate-50">
            Ditinjau
        </button>
    </div>
    <button onclick="loadData()" class="text-[11px] font-semibold text-blue-700 border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
        ↻ Refresh
    </button>
</div>

{{-- List --}}
<div id="riwayat-list" class="space-y-3">
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-8 text-center text-sm text-slate-400">
        Memuat riwayat konsultasi...
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    var allData    = [];
    var activeFilter = 'all';

    function setFilter(f) {
        activeFilter = f;
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            btn.classList.remove('bg-blue-700', 'text-white', 'border-blue-700');
            btn.classList.add('text-slate-600', 'hover:bg-slate-50');
        });
        var active = document.getElementById('f-' + f);
        if (active) {
            active.classList.add('bg-blue-700', 'text-white', 'border-blue-700');
            active.classList.remove('text-slate-600', 'hover:bg-slate-50');
        }
        renderList();
    }

    function badgeClass(s) {
        if (s === 'done')      return 'bg-teal-50 text-teal-700 border border-teal-200';
        if (s === 'in_review') return 'bg-blue-50 text-blue-700 border border-blue-200';
        return 'bg-amber-50 text-amber-700 border border-amber-200';
    }
    function badgeLabel(s) {
        if (s === 'done')      return 'Selesai';
        if (s === 'in_review') return 'Ditinjau';
        return 'Menunggu';
    }

    function renderList() {
        var list = document.getElementById('riwayat-list');
        var filtered = activeFilter === 'all' ? allData : allData.filter(function(d) { return d.status === activeFilter; });

        if (!filtered.length) {
            list.innerHTML = '<div class="bg-white rounded-xl border border-slate-200 px-4 py-10 text-center">' +
                '<p class="text-sm text-slate-400 italic">Belum ada konsultasi' +
                (activeFilter !== 'all' ? ' dengan status ini' : '') + '</p>' +
                '<a href="/konsultasi/baru" class="inline-block mt-3 text-xs font-semibold text-blue-700 border border-blue-200 hover:bg-blue-50 px-4 py-2 rounded-lg transition-colors">+ Buat Konsultasi Baru</a>' +
                '</div>';
            return;
        }

        list.innerHTML = filtered.map(function(item) {
            var dijawab = item.dijawab_at ? new Date(item.dijawab_at).toLocaleString('id-ID') : null;
            var dibuat  = new Date(item.created_at).toLocaleString('id-ID');
            var hasJawaban = item.jawaban_dokter || item.jawaban;
            var jawaban    = item.jawaban_dokter || item.jawaban || '';

            return '<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">' +

                // Header
                '<div class="px-4 py-3 border-b border-slate-100">' +
                    '<div class="flex justify-between items-start mb-1.5">' +
                        '<div class="flex items-center gap-2">' +
                            '<span class="text-[13px] font-semibold text-slate-800">' + (item.nama || item.nama_pasien || user.name) + '</span>' +
                            '<span class="text-[11px] text-slate-400">#KSL-' + String(item.id).padStart(3,'0') + '</span>' +
                        '</div>' +
                        '<span class="text-[11px] text-slate-400">' + dibuat + '</span>' +
                    '</div>' +
                    '<p class="text-[12px] text-slate-600 mb-2 leading-relaxed">' + item.keluhan + '</p>' +
                    '<div class="flex items-center gap-2">' +
                        '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ' + badgeClass(item.status) + '">' + badgeLabel(item.status) + '</span>' +
                        (item.dokter ? '<span class="text-[11px] text-slate-400">· ' + (item.dokter.name || item.dokter) + '</span>' : '') +
                        (dijawab ? '<span class="text-[11px] text-slate-400 ml-auto">Dijawab: ' + dijawab + '</span>' : '') +
                    '</div>' +
                '</div>' +

                // Jawaban Dokter
                (hasJawaban
                    ? '<div class="px-4 py-3 bg-blue-50 border-l-4 border-blue-400">' +
                          '<div class="flex items-center gap-1.5 mb-1.5">' +
                              '<span class="text-[11px] font-bold text-blue-700">Jawaban Dokter</span>' +
                              (item.dokter ? '<span class="text-[11px] text-blue-500">· ' + (item.dokter.name || item.dokter) + '</span>' : '') +
                          '</div>' +
                          '<p class="text-[12px] text-slate-700 leading-relaxed">' + jawaban + '</p>' +
                      '</div>'
                    : '<div class="px-4 py-3 bg-slate-50 border-l-4 border-slate-200">' +
                          '<p class="text-[12px] text-slate-400 italic">Menunggu jawaban dokter...</p>' +
                      '</div>'
                ) +
            '</div>';
        }).join('');
    }

    async function loadData() {
        var list = document.getElementById('riwayat-list');
        list.innerHTML = '<div class="bg-white rounded-xl border border-slate-200 px-4 py-8 text-center text-sm text-slate-400">Memuat...</div>';

        try {
            var res  = await fetch('/api/konsultasi/saya', { headers: { 'Authorization': 'Bearer ' + token } });
            allData  = await res.json();

            // Update stats
            document.getElementById('stat-total').textContent    = allData.length;
            document.getElementById('stat-done').textContent     = allData.filter(function(d) { return d.status === 'done'; }).length;
            document.getElementById('stat-pending').textContent  = allData.filter(function(d) { return d.status === 'received'; }).length;
            document.getElementById('stat-answered').textContent = allData.filter(function(d) { return d.jawaban_dokter || d.jawaban; }).length;

            renderList();
        } catch(e) {
            list.innerHTML = '<div class="bg-white rounded-xl border border-slate-200 px-4 py-8 text-center text-sm text-red-400">Gagal memuat data. Periksa koneksi internet Anda.</div>';
        }
    }

    loadData();
</script>
@endsection