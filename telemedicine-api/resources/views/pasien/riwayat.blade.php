@extends('layouts.app')

@section('title', 'Riwayat')
@section('page_title', 'Riwayat')
@section('page_sub', 'Konsultasi yang sudah selesai')
@section('nav_riwayat', 'active-nav')

@section('content')

<h2 class="text-[13px] font-semibold text-slate-800 mb-4">Riwayat Konsultasi</h2>
<div id="riwayat-list">
    <p class="text-sm text-slate-400">Memuat...</p>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';

    async function load() {
        var list = document.getElementById('riwayat-list');
        try {
            var res  = await fetch('/api/konsultasi/saya', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();
            var done = data.filter(d => d.status === 'done');

            if (!done.length) {
                list.innerHTML = '<p class="text-sm text-slate-400 italic">Belum ada riwayat konsultasi selesai</p>';
                return;
            }

            list.innerHTML = done.map(item => `
                <div class="bg-white rounded-xl border border-slate-200 mb-3 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <div class="flex justify-between items-start mb-1.5">
                            <div>
                                <span class="text-[13px] font-semibold text-slate-800">${item.nama || user.name}</span>
                                <span class="text-[11px] text-slate-400 ml-1">#KSL-${String(item.id).padStart(3,'0')}</span>
                            </div>
                            <span class="text-[11px] text-slate-400">${new Date(item.created_at).toLocaleString('id-ID')}</span>
                        </div>
                        <p class="text-[12px] text-slate-600 mb-2">${item.keluhan}</p>
                        <span class="text-[10px] font-semibold bg-teal-50 text-teal-700 px-2 py-0.5 rounded-full">Selesai</span>
                    </div>
                    ${item.jawaban_dokter ? `
                    <div class="px-4 py-3 bg-blue-50 border-l-4 border-blue-400">
                        <p class="text-[11px] font-bold text-blue-600 mb-1">Jawaban Dokter:</p>
                        <p class="text-[12px] text-slate-700 leading-relaxed">${item.jawaban_dokter}</p>
                    </div>` : ''}
                </div>
            `).join('');
        } catch(e) {
            list.innerHTML = '<p class="text-sm text-slate-400">Gagal memuat data</p>';
        }
    }
    load();
</script>
@endsection