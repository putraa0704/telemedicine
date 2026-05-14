@extends('layouts.app')

@section('title', 'Riwayat Konsultasi')
@section('page_title', 'Riwayat Konsultasi')
@section('page_sub', 'Arsip percakapan konsultasi Anda yang sudah selesai')
@section('nav_riwayat', 'active')

@section('content')

<div class="max-w-4xl mx-auto py-2">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-slate-800">Riwayat Selesai</h2>
        <div class="flex gap-2">
            <input id="search-chat" type="text" placeholder="Cari dokter..."
                class="px-4 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-white" oninput="renderTimeline()" />
            <button onclick="loadKonsultasi()" class="px-3 py-2 text-brand-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            </button>
        </div>
    </div>

    <div id="timeline-container" class="relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:ml-[2.25rem] md:before:translate-x-0 before:h-full before:w-0.5 before:bg-slate-200 space-y-6">
        <div class="py-12 text-center text-sm text-slate-400">Memuat riwayat...</div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    var allData = [];
    var lastHTML = '';

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateInfo(s) {
        if (!s) return '-';
        var d = new Date(s);
        var datePart = d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        var timePart = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        return datePart + ' • ' + timePart;
    }

    function renderTimeline() {
        var q = (document.getElementById('search-chat').value || '').toLowerCase();
        
        var items = allData.filter(function(item) {
            if (item.status !== 'done') return false;
            var searchTxt = ((item.dokter ? item.dokter.name : '') + ' ' + (item.keluhan || '')).toLowerCase();
            return !q || searchTxt.indexOf(q) !== -1;
        });

        var container = document.getElementById('timeline-container');
        
        if (!items.length) {
            var emptyHTML = '<div class="py-12 text-center text-sm text-slate-400">Tidak ada riwayat konsultasi selesai</div>';
            if (lastHTML !== emptyHTML) {
                container.innerHTML = emptyHTML;
                lastHTML = emptyHTML;
            }
            return;
        }

        var newHTML = items.map(function(item) {
            var drName = item.dokter ? escapeHtml(item.dokter.name) : 'Belum ada dokter';
            var drInisial = drName.replace(/[^A-Z]/g, '').slice(0,2);
            if(!drInisial && item.dokter && item.dokter.name) drInisial = item.dokter.name.substring(0,2).toUpperCase();
            var drSpesialisasi = item.dokter ? escapeHtml(item.dokter.spesialisasi || 'UMUM') : 'UMUM';

            return '<div class="relative flex items-start gap-4 md:gap-6">' +
                '<div class="absolute left-0 md:relative z-10 w-10 h-10 md:w-14 md:h-14 rounded-full bg-blue-50 border-4 border-white flex items-center justify-center flex-shrink-0 shadow-sm">' +
                    '<svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>' +
                '</div>' +
                '<div class="flex-1 ml-12 md:ml-0 bg-white border border-slate-200 rounded-2xl shadow-sm p-5 md:p-6 hover:shadow-md transition-shadow">' +
                    '<div class="flex items-start justify-between mb-4">' +
                        '<div class="flex items-center gap-4">' +
                            '<div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-500 overflow-hidden text-lg">' +
                                drInisial +
                            '</div>' +
                            '<div>' +
                                '<div class="text-[10px] md:text-xs font-bold text-brand-600 tracking-wider uppercase mb-1">' +
                                    drSpesialisasi + ' • KONSULTASI' +
                                '</div>' +
                                '<h3 class="text-base md:text-lg font-bold text-slate-800">' + drName + '</h3>' +
                                '<div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">' +
                                    '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' +
                                    formatDateInfo(item.created_at) +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="flex flex-col items-end gap-2">' +
                            '<span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold tracking-wide">Selesai</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="bg-slate-50 rounded-xl p-4 mb-5 border border-slate-100">' +
                        '<h4 class="text-sm font-semibold text-brand-700 mb-2">Ringkasan Keluhan</h4>' +
                        '<p class="text-sm text-slate-600 leading-relaxed">' + escapeHtml(item.keluhan || '-') + '</p>' +
                    '</div>' +
                    '<div class="flex items-center gap-3">' +
                        '<a href="/konsultasi/' + item.id + '/print" target="_blank" class="flex items-center gap-2 px-4 py-2 border border-slate-200 hover:bg-slate-50 hover:border-brand-300 text-brand-600 font-semibold text-sm rounded-xl transition-all">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>' +
                            'Download Report' +
                        '</a>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');

        if (lastHTML !== newHTML) {
            container.innerHTML = newHTML;
            lastHTML = newHTML;
        }
    }

    async function loadKonsultasi() {
        try {
            var res  = await fetch('/api/konsultasi/saya?_t=' + new Date().getTime(), { headers: { 'Authorization': 'Bearer ' + token } });
            allData = await res.json();
            renderTimeline();
        } catch(e) {
            document.getElementById('timeline-container').innerHTML = '<div class="py-12 text-center text-[12px] text-red-400">Gagal memuat riwayat</div>';
        }
    }

    loadKonsultasi();
    setInterval(loadKonsultasi, 15000); // refresh list 15s
</script>
@endsection
