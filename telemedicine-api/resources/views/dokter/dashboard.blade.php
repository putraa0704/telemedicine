@extends('layouts.app')

@section('title', 'Dashboard Dokter')
@section('page_title', 'Dashboard Dokter')
@section('page_sub', 'Kelola konsultasi pasien masuk')
@section('nav_dashboard', 'active')

@section('content')

<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total</div>
            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-slate-800" id="stat-total">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Konsultasi</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Menunggu</div>
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-amber-600" id="stat-pending">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Belum dijawab</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Selesai</div>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-emerald-600" id="stat-done">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Hari ini</div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
        <span class="text-[13px] font-semibold text-slate-800">Konsultasi Masuk</span>
        <button onclick="loadKonsultasi()" class="text-[11px] text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition-colors">↻ Refresh</button>
    </div>
    <div id="ksl-list">
        <div class="px-4 py-10 text-center text-[12px] text-slate-400">Memuat data...</div>
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
                list.innerHTML = '<div class="px-4 py-10 text-center"><div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3"><svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div><p class="text-sm text-slate-400">Belum ada konsultasi masuk</p></div>';
                return;
            }

            list.innerHTML = data.map(item => `
                <div class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors px-4 sm:px-5 py-4">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[13px] font-semibold text-slate-800">${item.nama_pasien || '—'}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">#KSL-${String(item.id).padStart(3,'0')}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full ${item.status === 'done' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : item.status === 'in_review' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200'}">
                                ${item.status === 'done' ? 'Selesai' : item.status === 'in_review' ? 'Ditinjau' : 'Menunggu'}
                            </span>
                            <span class="text-[10px] text-slate-400">${new Date(item.created_at).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                    <p class="text-[13px] text-slate-600 mb-3 leading-relaxed">${item.keluhan}</p>

                    ${item.jawaban
                        ? `<div class="bg-blue-50 border-l-4 border-blue-400 rounded-r-xl px-4 py-3">
                               <p class="text-[11px] font-bold text-blue-700 mb-1">Jawaban Anda:</p>
                               <p class="text-[12px] text-slate-700">${item.jawaban}</p>
                           </div>`
                        : `<div class="mt-2 space-y-2">
                               <textarea id="jawaban-${item.id}" rows="3"
                                   placeholder="Tulis jawaban/saran medis..."
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 resize-none bg-slate-50 transition"></textarea>
                               <div class="flex gap-2">
                                   <button onclick="kirimJawaban(${item.id})"
                                       class="flex-1 bg-brand-600 hover:bg-brand-800 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                                       Kirim Jawaban
                                   </button>
                                   ${item.status !== 'in_review' ? `<button onclick="updateStatus(${item.id},'in_review')" class="px-4 py-2.5 text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Tinjau</button>` : ''}
                               </div>
                           </div>`
                    }
                </div>
            `).join('');
        } catch(e) {
            list.innerHTML = '<div class="px-4 py-4 text-center text-[12px] text-red-400">Gagal memuat data</div>';
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

    async function updateStatus(id, status) {
        try {
            await fetch('/api/dokter/konsultasi/' + id + '/status', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ status })
            });
            loadKonsultasi();
        } catch(e) {}
    }

    loadKonsultasi();
    setInterval(loadKonsultasi, 30000);
</script>
@endsection