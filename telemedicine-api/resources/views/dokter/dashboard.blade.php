@extends('layouts.app')

@section('title', 'Dashboard Dokter')
@section('page_title', 'Dashboard Dokter')
@section('page_sub', 'Daftar konsultasi masuk')
@section('nav_dashboard', 'active-nav')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Total Konsultasi</div>
        <div class="text-3xl font-bold text-slate-800 leading-none" id="stat-total">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Menunggu Jawaban</div>
        <div class="text-3xl font-bold text-amber-600 leading-none" id="stat-pending">—</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Selesai Hari Ini</div>
        <div class="text-3xl font-bold text-teal-600 leading-none" id="stat-done">—</div>
    </div>
</div>

{{-- Konsultasi List --}}
<div class="bg-white rounded-xl border border-slate-200">
    <div class="px-4 py-3 border-b border-slate-100">
        <span class="text-[13px] font-semibold text-slate-800">Konsultasi Masuk</span>
    </div>
    <div id="ksl-list">
        <p class="px-4 py-4 text-sm text-slate-400">Memuat data...</p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role === 'pasien') window.location.href = '/pasien';

    async function loadKonsultasi() {
        var list = document.getElementById('ksl-list');
        try {
            var res  = await fetch('/api/dokter/konsultasi', { headers: { 'Authorization': 'Bearer ' + token } });
            var data = await res.json();

            document.getElementById('stat-total').textContent   = data.length;
            document.getElementById('stat-pending').textContent = data.filter(d => d.status !== 'done').length;
            document.getElementById('stat-done').textContent    = data.filter(d => {
                return d.status === 'done' && d.dijawab_at && new Date(d.dijawab_at) > new Date(Date.now() - 86400000);
            }).length;

            if (!data.length) {
                list.innerHTML = '<p class="px-4 py-4 text-sm text-slate-400 italic">Belum ada konsultasi masuk</p>';
                return;
            }

            list.innerHTML = data.map(item => `
                <div class="px-4 py-3 border-b border-slate-100 last:border-0">
                    <div class="flex justify-between items-start mb-1.5">
                        <div>
                            <span class="text-[13px] font-semibold text-slate-800">${item.nama_pasien || '—'}</span>
                            <span class="text-[11px] text-slate-400 ml-1">#KSL-${String(item.id).padStart(3,'0')}</span>
                        </div>
                        <span class="text-[11px] text-slate-400">${new Date(item.created_at).toLocaleString('id-ID')}</span>
                    </div>
                    <p class="text-[12px] text-slate-600 mb-2">${item.keluhan}</p>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ${item.status === 'done' ? 'bg-teal-50 text-teal-700' : 'bg-amber-50 text-amber-700'}">${item.status === 'done' ? 'Selesai' : 'Menunggu'}</span>

                    ${item.jawaban
                        ? `<div class="mt-2.5 bg-blue-50 border-l-2 border-blue-400 rounded-r-lg px-3 py-2">
                               <p class="text-[11px] font-bold text-blue-600 mb-0.5">Jawaban Anda:</p>
                               <p class="text-[12px] text-slate-700">${item.jawaban}</p>
                           </div>`
                        : `<div class="mt-3 space-y-2">
                               <textarea id="jawaban-${item.id}" rows="3"
                                   placeholder="Tulis jawaban/saran medis..."
                                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 resize-none transition"></textarea>
                               <button onclick="kirimJawaban(${item.id})"
                                   class="w-full bg-blue-700 hover:bg-blue-900 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                                   Kirim Jawaban
                               </button>
                           </div>`
                    }
                </div>
            `).join('');
        } catch(e) {
            list.innerHTML = '<p class="px-4 py-4 text-sm text-red-400">Gagal memuat data</p>';
        }
    }

    async function kirimJawaban(id) {
        var jawaban = document.getElementById('jawaban-' + id).value.trim();
        if (!jawaban) return alert('Jawaban tidak boleh kosong');
        try {
            var res = await fetch('/api/dokter/konsultasi/' + id + '/jawab', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ jawaban })
            });
            if (res.ok) loadKonsultasi();
        } catch(e) { alert('Gagal mengirim jawaban'); }
    }

    loadKonsultasi();
    setInterval(loadKonsultasi, 30000);
</script>
@endsection