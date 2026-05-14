@extends('layouts.app')

@section('title', 'Riwayat Konsultasi Pasien')
@section('page_title', 'Riwayat Konsultasi')
@section('page_sub', 'Arsip percakapan konsultasi pasien yang sudah selesai')
@section('nav_riwayat', 'active')

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
                <span class="text-[13px] font-semibold text-slate-800">Riwayat Selesai</span>
                <button onclick="loadKonsultasi()" class="text-[11px] text-brand-600 border border-brand-200 hover:bg-blue-50 px-2.5 py-1 rounded-lg transition-colors">↻</button>
            </div>
            <input id="search-chat" type="text" placeholder="Cari nama pasien..."
                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 bg-slate-50" oninput="renderChatList()" />
        </div>
        <div id="chat-list" class="flex-1 overflow-y-auto">
            <div class="px-4 py-8 text-center text-[12px] text-slate-400">Memuat riwayat...</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-[70vh]">
        <div id="chat-header" class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center">
            <span class="text-[13px] font-semibold text-slate-800">Pilih riwayat untuk dibaca</span>
        </div>
        <div id="chat-thread" class="flex-1 p-4 space-y-3 overflow-y-auto bg-slate-50/40">
            <div class="h-full flex items-center justify-center text-[12px] text-slate-400">Belum ada riwayat dipilih</div>
        </div>
        <div id="chat-input" class="border-t border-slate-100 p-3 hidden flex-shrink-0 bg-slate-50">
            <div class="text-center text-[11px] text-slate-400 py-1 font-semibold flex items-center justify-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Konsultasi ini sudah selesai dan tidak dapat dibalas.
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
    var lastChatListHTML = '';
    var lastThreadHTML = '';

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
        // Hanya tampilkan yang done di riwayat
        var items = allData.filter(function(item) {
            if (item.status !== 'done') return false;
            var name = (item.nama_pasien || item.nama || '').toLowerCase();
            return !q || name.indexOf(q) !== -1;
        });

        var listEl = document.getElementById('chat-list');
        if (!items.length) {
            var emptyHTML = '<div class="px-4 py-8 text-center text-[12px] text-slate-400">Tidak ada riwayat konsultasi selesai</div>';
            if (lastChatListHTML !== emptyHTML) {
                listEl.innerHTML = emptyHTML;
                lastChatListHTML = emptyHTML;
            }
            return;
        }

        var newHTML = items.map(function(item) {
            var activeClass = String(item.id) === String(activeId)
                ? 'bg-brand-50 border-brand-200'
                : 'bg-white border-transparent hover:bg-slate-50';
            var status = '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>';
            return '<button onclick="pilihChat(' + item.id + ')" class="w-full text-left px-4 py-3 border-b border-slate-100 ' + activeClass + '">' +
                '<div class="flex items-center justify-between mb-1">' +
                '<div class="text-[12px] font-semibold text-slate-800 truncate pr-2">' + escapeHtml(item.nama_pasien || item.nama || '-') + '</div>' +
                status +
                '</div>' +
                '<div class="text-[11px] text-slate-500 line-clamp-2">' + escapeHtml(item.keluhan || '-') + '</div>' +
                '<div class="text-[10px] text-slate-400 mt-1">' + formatDate(item.created_at) + '</div>' +
                '</button>';
        }).join('');

        if (lastChatListHTML !== newHTML) {
            listEl.innerHTML = newHTML;
            lastChatListHTML = newHTML;
        }
    }

    async function pilihChat(id) {
        if (activeId === id) return;
        activeId = id;
        lastThreadHTML = '';
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
            header.innerHTML = '<span class="text-[13px] font-semibold text-slate-800">Pilih riwayat untuk dibaca</span>';
            var emptyHTML = '<div class="h-full flex items-center justify-center text-[12px] text-slate-400">Belum ada riwayat dipilih</div>';
            if (lastThreadHTML !== emptyHTML) {
                thread.innerHTML = emptyHTML;
                lastThreadHTML = emptyHTML;
            }
            inputWrap.classList.add('hidden');
            return;
        }

        header.innerHTML = '<div class="flex items-center justify-between gap-2 w-full">' +
            '<div><div class="text-[13px] font-semibold text-slate-800">' + escapeHtml(item.nama_pasien || item.nama || '-') + '</div>' +
            '<div class="text-[11px] text-slate-500">#KSL-' + String(item.id).padStart(3, '0') + ' <span class="bg-emerald-50 text-emerald-600 px-1.5 rounded text-[9px] font-bold ml-1">SELESAI</span></div></div>' +
            '<div class="flex items-center gap-2">' +
                '<div class="text-[10px] text-slate-400">' + formatDate(item.created_at) + '</div>' +
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
                    '<div class="max-w-[85%] bg-slate-400 text-white rounded-2xl rounded-tr-md px-3.5 py-2.5 shadow-sm">' +
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

        if (lastThreadHTML !== html) {
            thread.innerHTML = html;
            thread.scrollTop = thread.scrollHeight;
            lastThreadHTML = html;
        }
        inputWrap.classList.remove('hidden');
    }

    async function loadMessages(id) {
        try {
            var res = await fetch('/api/konsultasi/' + id + '/messages?_t=' + new Date().getTime(), { headers: { 'Authorization': 'Bearer ' + token } });
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
            var res  = await fetch('/api/dokter/konsultasi?_t=' + new Date().getTime(), { headers: { 'Authorization': 'Bearer ' + token } });
            allData = await res.json();
            updateStats();
            
            if (activeId) {
                var activeItem = allData.find(d => String(d.id) === String(activeId));
                if (!activeItem || activeItem.status !== 'done') {
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
            document.getElementById('chat-list').innerHTML = '<div class="px-4 py-8 text-center text-[12px] text-red-400">Gagal memuat riwayat</div>';
        }
    }

    loadKonsultasi();
    setInterval(loadKonsultasi, 15000); // refresh list 15s
</script>
@endsection
