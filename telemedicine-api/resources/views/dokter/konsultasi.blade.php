@extends('layouts.app')

@section('title', 'Chat Konsultasi Pasien')
@section('page_title', 'Chat Konsultasi Pasien')
@section('page_sub', 'Pilih pasien dan balas konsultasi dalam format chat')

@section('content')

<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-4">
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
        <div class="text-[10px] text-slate-400 mt-1">Percakapan</div>
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
        <div class="text-[10px] text-slate-400 mt-1">Belum dibalas</div>
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
        <div class="text-[10px] text-slate-400 mt-1">Sudah dibalas</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3.5 border-b border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[13px] font-semibold text-slate-800">Daftar Pasien</span>
                <button onclick="loadKonsultasi()" class="text-[11px] text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1 rounded-lg transition-colors">↻</button>
            </div>
            <input id="search-chat" type="text" placeholder="Cari nama pasien..."
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50" oninput="renderChatList()" />
        </div>
        <div id="chat-list" class="max-h-[66vh] overflow-y-auto">
            <div class="px-4 py-8 text-center text-[12px] text-slate-400">Memuat percakapan...</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col min-h-130">
        <div id="chat-header" class="px-5 py-3.5 border-b border-slate-100">
            <span class="text-[13px] font-semibold text-slate-800">Pilih pasien untuk memulai chat</span>
        </div>
        <div id="chat-thread" class="flex-1 p-4 space-y-3 overflow-y-auto bg-slate-50/40">
            <div class="h-full flex items-center justify-center text-[12px] text-slate-400">Belum ada percakapan dipilih</div>
        </div>
        <div id="chat-input" class="border-t border-slate-100 p-3 hidden">
            <textarea id="jawaban-input" rows="3" placeholder="Tulis balasan untuk pasien..."
                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 resize-none"></textarea>
            <div class="mt-2 flex justify-end">
                <button onclick="kirimJawabanAktif()" id="btn-kirim"
                    class="bg-brand-600 hover:bg-brand-800 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">Kirim Balasan</button>
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
    if (user && (user.role !== 'dokter' && user.role !== 'admin')) window.location.href = '/pasien';

    var allData = [];
    var activeId = null;

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDate(s) {
        if (!s) return '-';
        return new Date(s).toLocaleString('id-ID');
    }

    function updateStats() {
        document.getElementById('stat-total').textContent = allData.length;
        document.getElementById('stat-pending').textContent = allData.filter(function(d) { return !d.jawaban; }).length;
        document.getElementById('stat-done').textContent = allData.filter(function(d) { return !!d.jawaban; }).length;
    }

    function renderChatList() {
        var q = (document.getElementById('search-chat').value || '').toLowerCase();
        var items = allData.filter(function(item) {
            var name = (item.nama_pasien || item.nama || '').toLowerCase();
            return !q || name.indexOf(q) !== -1;
        });

        var listEl = document.getElementById('chat-list');
        if (!items.length) {
            listEl.innerHTML = '<div class="px-4 py-8 text-center text-[12px] text-slate-400">Data konsultasi tidak ditemukan</div>';
            return;
        }

        listEl.innerHTML = items.map(function(item) {
            var activeClass = String(item.id) === String(activeId)
                ? 'bg-brand-50 border-brand-200'
                : 'bg-white border-transparent hover:bg-slate-50';
            var status = item.jawaban
                ? '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Dibalas</span>'
                : '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Menunggu</span>';
            return '<button onclick="pilihChat(' + item.id + ')" class="w-full text-left px-4 py-3 border-b border-slate-100 ' + activeClass + '">' +
                '<div class="flex items-center justify-between mb-1">' +
                '<div class="text-[12px] font-semibold text-slate-800 truncate pr-2">' + escapeHtml(item.nama_pasien || item.nama || '-') + '</div>' +
                status +
                '</div>' +
                '<div class="text-[11px] text-slate-500 line-clamp-2">' + escapeHtml(item.keluhan || '-') + '</div>' +
                '<div class="text-[10px] text-slate-400 mt-1">' + formatDate(item.created_at) + '</div>' +
                '</button>';
        }).join('');
    }

    function pilihChat(id) {
        activeId = id;
        renderChatList();
        renderThread();
    }

    function renderThread() {
        var item = allData.find(function(d) { return String(d.id) === String(activeId); });
        var header = document.getElementById('chat-header');
        var thread = document.getElementById('chat-thread');
        var inputWrap = document.getElementById('chat-input');

        if (!item) {
            header.innerHTML = '<span class="text-[13px] font-semibold text-slate-800">Pilih pasien untuk memulai chat</span>';
            thread.innerHTML = '<div class="h-full flex items-center justify-center text-[12px] text-slate-400">Belum ada percakapan dipilih</div>';
            inputWrap.classList.add('hidden');
            return;
        }

        header.innerHTML = '<div class="flex items-center justify-between gap-2">' +
            '<div><div class="text-[13px] font-semibold text-slate-800">' + escapeHtml(item.nama_pasien || item.nama || '-') + '</div>' +
            '<div class="text-[11px] text-slate-500">#KSL-' + String(item.id).padStart(3, '0') + '</div></div>' +
            '<div class="text-[10px] text-slate-400">' + formatDate(item.created_at) + '</div>' +
            '</div>';

        var pasienBubble =
            '<div class="flex justify-start">' +
                '<div class="max-w-[85%] bg-white border border-slate-200 rounded-2xl rounded-tl-md px-3.5 py-2.5 shadow-sm">' +
                    '<div class="text-[10px] font-semibold text-slate-400 mb-1">Pasien</div>' +
                    '<div class="text-[13px] text-slate-700 leading-relaxed">' + escapeHtml(item.keluhan || '-') + '</div>' +
                '</div>' +
            '</div>';

        var jawabanBubble = item.jawaban
            ? '<div class="flex justify-end">' +
                '<div class="max-w-[85%] bg-brand-600 text-white rounded-2xl rounded-tr-md px-3.5 py-2.5 shadow-sm">' +
                    '<div class="text-[10px] font-semibold text-white/80 mb-1">Dokter</div>' +
                    '<div class="text-[13px] leading-relaxed">' + escapeHtml(item.jawaban) + '</div>' +
                '</div>' +
              '</div>'
            : '<div class="text-center text-[11px] text-slate-400 py-1">Belum ada balasan dari dokter</div>';

        thread.innerHTML = pasienBubble + jawabanBubble;
        thread.scrollTop = thread.scrollHeight;
        inputWrap.classList.remove('hidden');
        document.getElementById('jawaban-input').value = item.jawaban || '';
    }

    async function loadKonsultasi() {
        try {
            var res  = await fetch('/api/dokter/konsultasi', { headers: { 'Authorization': 'Bearer ' + token } });
            allData = await res.json();
            updateStats();
            renderChatList();

            if (!allData.length) {
                activeId = null;
                renderThread();
                return;
            }

            if (!activeId || !allData.some(function(d) { return String(d.id) === String(activeId); })) {
                activeId = allData[0].id;
            }
            renderThread();
        } catch(e) {
            document.getElementById('chat-list').innerHTML = '<div class="px-4 py-8 text-center text-[12px] text-red-400">Gagal memuat percakapan</div>';
        }
    }

    async function kirimJawabanAktif() {
        if (!activeId) return;
        var jawaban = document.getElementById('jawaban-input').value.trim();
        if (!jawaban) return alert('Jawaban tidak boleh kosong');

        var btn = document.getElementById('btn-kirim');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        try {
            var res = await fetch('/api/dokter/konsultasi/' + activeId + '/jawab', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ jawaban })
            });

            if (res.ok) {
                try {
                    await fetch('/api/dokter/konsultasi/' + activeId + '/status', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                        body: JSON.stringify({ status: 'done' })
                    });
                } catch(e) {}

                await loadKonsultasi();
            }
        } catch(e) { alert('Gagal mengirim jawaban'); }
        btn.disabled = false;
        btn.textContent = 'Kirim Balasan';
    }

    loadKonsultasi();
    setInterval(loadKonsultasi, 30000);
</script>
@endsection
