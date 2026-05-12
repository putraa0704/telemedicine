@extends('layouts.app')

@section('title', 'Chat Konsultasi Pasien')
@section('page_title', 'Chat Konsultasi Pasien')
@section('page_sub', 'Pilih pasien dan balas konsultasi dalam format chat')

@section('content')

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total</div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-slate-800" id="stat-total">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Percakapan</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Menunggu</div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-amber-600" id="stat-pending">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Belum dibalas</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Aktif</div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-blue-600" id="stat-active">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Ditinjau</div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Selesai</div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-emerald-600" id="stat-done">—</div>
        <div class="text-[10px] text-slate-400 mt-1">Selesai</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-[70vh]">
        <div class="px-4 py-3.5 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[13px] font-semibold text-slate-800">Daftar Pasien (Aktif)</span>
                <button onclick="loadKonsultasi()" class="text-[11px] text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1 rounded-lg transition-colors">↻</button>
            </div>
            <input id="search-chat" type="text" placeholder="Cari nama pasien..."
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50" oninput="renderChatList()" />
        </div>
        <div id="chat-list" class="flex-1 overflow-y-auto">
            <div class="px-4 py-8 text-center text-[12px] text-slate-400">Memuat percakapan...</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-[70vh]">
        <div id="chat-header" class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center">
            <span class="text-[13px] font-semibold text-slate-800">Pilih pasien untuk memulai chat</span>
        </div>
        <div id="chat-thread" class="flex-1 p-4 space-y-3 overflow-y-auto bg-slate-50/40">
            <div class="h-full flex items-center justify-center text-[12px] text-slate-400">Belum ada percakapan dipilih</div>
        </div>
        <div id="chat-input" class="border-t border-slate-100 p-3 hidden flex-shrink-0">
            <textarea id="jawaban-input" rows="2" placeholder="Tulis balasan untuk pasien..."
                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50 resize-none"></textarea>
            <div class="mt-2 flex justify-end">
                <button onclick="kirimPesanAktif()" id="btn-kirim"
                    class="bg-brand-600 hover:bg-brand-800 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">Kirim Pesan</button>
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
    var currentMessages = [];

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
        document.getElementById('stat-pending').textContent = allData.filter(function(d) { return d.status === 'received'; }).length;
        document.getElementById('stat-active').textContent = allData.filter(function(d) { return d.status === 'in_review'; }).length;
        document.getElementById('stat-done').textContent = allData.filter(function(d) { return d.status === 'done'; }).length;
    }

    function renderChatList() {
        var q = (document.getElementById('search-chat').value || '').toLowerCase();
        // Hanya tampilkan yang aktif (bukan done) di chat list ini
        var items = allData.filter(function(item) {
            if (item.status === 'done') return false;
            var name = (item.nama_pasien || item.nama || '').toLowerCase();
            return !q || name.indexOf(q) !== -1;
        });

        var listEl = document.getElementById('chat-list');
        if (!items.length) {
            listEl.innerHTML = '<div class="px-4 py-8 text-center text-[12px] text-slate-400">Tidak ada konsultasi aktif</div>';
            return;
        }

        listEl.innerHTML = items.map(function(item) {
            var activeClass = String(item.id) === String(activeId)
                ? 'bg-brand-50 border-brand-200'
                : 'bg-white border-transparent hover:bg-slate-50';
            var status = item.status === 'in_review'
                ? '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Ditinjau</span>'
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

    async function pilihChat(id) {
        activeId = id;
        renderChatList();
        renderThreadLoading();
        await loadMessages(id);
    }

    function renderThreadLoading() {
        var thread = document.getElementById('chat-thread');
        thread.innerHTML = '<div class="h-full flex items-center justify-center text-[12px] text-slate-400">Memuat pesan...</div>';
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

        header.innerHTML = '<div class="flex items-center justify-between gap-2 w-full">' +
            '<div><div class="text-[13px] font-semibold text-slate-800">' + escapeHtml(item.nama_pasien || item.nama || '-') + '</div>' +
            '<div class="text-[11px] text-slate-500">#KSL-' + String(item.id).padStart(3, '0') + '</div></div>' +
            '<div class="flex items-center gap-2">' +
                '<div class="text-[10px] text-slate-400">' + formatDate(item.created_at) + '</div>' +
                '<button onclick="akhiriChat()" class="text-[11px] font-semibold px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg">Akhiri Chat</button>' +
            '</div></div>';

        // Tampilkan keluhan awal
        var html = '<div class="flex justify-start mb-3">' +
            '<div class="max-w-[85%] bg-white border border-slate-200 rounded-2xl rounded-tl-md px-3.5 py-2.5 shadow-sm">' +
                '<div class="text-[10px] font-semibold text-slate-400 mb-1">Pasien (Keluhan)</div>' +
                '<div class="text-[13px] text-slate-700 leading-relaxed">' + escapeHtml(item.keluhan || '-') + '</div>' +
                '<div class="text-[9px] text-slate-400 mt-1 text-right">' + formatDate(item.created_at) + '</div>' +
            '</div></div>';

        // Tampilkan pesan
        currentMessages.forEach(function(msg) {
            var isDokter = msg.sender_role === 'dokter';
            if (isDokter) {
                html += '<div class="flex justify-end mb-3">' +
                    '<div class="max-w-[85%] bg-brand-600 text-white rounded-2xl rounded-tr-md px-3.5 py-2.5 shadow-sm">' +
                        '<div class="text-[10px] font-semibold text-white/80 mb-1">Anda</div>' +
                        '<div class="text-[13px] leading-relaxed">' + escapeHtml(msg.message) + '</div>' +
                        '<div class="text-[9px] text-white/60 mt-1 text-right">' + formatDate(msg.created_at) + '</div>' +
                    '</div></div>';
            } else {
                html += '<div class="flex justify-start mb-3">' +
                    '<div class="max-w-[85%] bg-white border border-slate-200 rounded-2xl rounded-tl-md px-3.5 py-2.5 shadow-sm">' +
                        '<div class="text-[10px] font-semibold text-slate-400 mb-1">Pasien</div>' +
                        '<div class="text-[13px] text-slate-700 leading-relaxed">' + escapeHtml(msg.message) + '</div>' +
                        '<div class="text-[9px] text-slate-400 mt-1 text-right">' + formatDate(msg.created_at) + '</div>' +
                    '</div></div>';
            }
        });

        thread.innerHTML = html;
        thread.scrollTop = thread.scrollHeight;
        inputWrap.classList.remove('hidden');
    }

    async function loadMessages(id) {
        try {
            var res = await fetch('/api/konsultasi/' + id + '/messages', { headers: { 'Authorization': 'Bearer ' + token }, cache: 'no-store' });
            if(res.ok) {
                currentMessages = await res.json();
            } else {
                currentMessages = [];
            }
            renderThread();
        } catch(e) {
            console.error(e);
        }
    }

    async function loadKonsultasi() {
        try {
            var res  = await fetch('/api/dokter/konsultasi', { headers: { 'Authorization': 'Bearer ' + token }, cache: 'no-store' });
            allData = await res.json();
            updateStats();
            
            // Check if activeId is still valid and not 'done'
            if (activeId) {
                var activeItem = allData.find(d => String(d.id) === String(activeId));
                if (!activeItem || activeItem.status === 'done') {
                    activeId = null;
                }
            }
            
            renderChatList();

            if (!activeId) {
                renderThread();
            } else {
                await loadMessages(activeId);
            }
        } catch(e) {
            document.getElementById('chat-list').innerHTML = '<div class="px-4 py-8 text-center text-[12px] text-red-400">Gagal memuat percakapan</div>';
        }
    }

    async function kirimPesanAktif() {
        if (!activeId) return;
        var inputEl = document.getElementById('jawaban-input');
        var pesan = inputEl.value.trim();
        if (!pesan) return alert('Pesan tidak boleh kosong');

        var btn = document.getElementById('btn-kirim');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        try {
            var res = await fetch('/api/konsultasi/' + activeId + '/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ message: pesan })
            });

            if (res.ok) {
                inputEl.value = '';
                await loadMessages(activeId);
                // Also refresh list without changing activeId to update status tags
                var resList = await fetch('/api/dokter/konsultasi', { headers: { 'Authorization': 'Bearer ' + token }, cache: 'no-store' });
                allData = await resList.json();
                updateStats();
                renderChatList();
            }
        } catch(e) { alert('Gagal mengirim pesan'); }
        btn.disabled = false;
        btn.textContent = 'Kirim Pesan';
    }

    async function akhiriChat() {
        if (!activeId) return;
        if (!confirm('Akhiri chat ini? Pasien tidak akan bisa membalas lagi dan percakapan akan dipindah ke riwayat.')) return;

        try {
            var res = await fetch('/api/dokter/konsultasi/' + activeId + '/status', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ status: 'done' })
            });

            if (res.ok) {
                activeId = null;
                await loadKonsultasi();
            }
        } catch(e) { alert('Gagal mengakhiri chat'); }
    }

    loadKonsultasi();
    setInterval(loadKonsultasi, 30000); // refresh list 30s
</script>
@endsection
