@extends('layouts.app')

@section('title', 'Konsultasi Baru')
@section('page_title', 'Konsultasi')
@section('page_sub', 'Ceritakan keluhan Anda kepada asisten kami')

@section('head')
<style>
    .chat-bubble-in {
        animation: slideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        opacity: 0;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .typing-dot { animation: blink 1.4s infinite; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes blink {
        0%, 60%, 100% { opacity: 0.2; transform: scale(0.8); }
        30% { opacity: 1; transform: scale(1); }
    }
    .choice-btn { transition: all 0.18s; }
    .choice-btn:hover { transform: translateY(-1px); }
    .choice-btn:active { transform: scale(0.98); }
    #chat-container::-webkit-scrollbar { width: 4px; }
    #chat-container::-webkit-scrollbar-track { background: transparent; }
    #chat-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
</style>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Chat Window --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Chat Header --}}
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                </svg>
            </div>
            <div>
                <div class="text-[13px] font-semibold text-slate-800">Asisten MediConnect</div>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] text-slate-400">Online — siap membantu</span>
                </div>
            </div>
            <a href="/pasien" class="ml-auto text-[11px] text-slate-400 hover:text-slate-600 border border-slate-200 px-2.5 py-1.5 rounded-lg transition-colors">
                ← Dashboard
            </a>
        </div>

        {{-- Chat Messages --}}
        <div id="chat-container" class="p-5 space-y-3 overflow-y-auto" style="min-height:380px; max-height:460px;">
            {{-- JS akan render messages di sini --}}
        </div>

        {{-- Input Area --}}
        <div id="input-area" class="hidden border-t border-slate-100 px-4 py-3">
            <div class="flex gap-2">
                <textarea id="user-input" rows="2" placeholder="Ketik keluhan Anda di sini..."
                    class="flex-1 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 bg-slate-50 resize-none transition"></textarea>
                <button onclick="sendUserInput()"
                    class="w-10 h-10 self-end bg-brand-600 hover:bg-brand-800 rounded-xl flex items-center justify-center transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Choice Buttons --}}
        <div id="choice-area" class="hidden border-t border-slate-100 px-4 py-3 space-y-2">
            {{-- JS render --}}
        </div>
    </div>

    {{-- Info hint --}}
    <p class="text-center text-[11px] text-slate-400 mt-3">Asisten ini akan mengarahkan Anda ke formulir konsultasi dokter</p>
</div>

@endsection

@section('scripts')
<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (!token || !user) window.location.href = '/login';
    if (user && user.role !== 'pasien') window.location.href = '/dokter';

    var userName = user ? user.name.split(' ')[0] : 'Pengguna';
    var container = document.getElementById('chat-container');
    var choiceEl  = document.getElementById('choice-area');
    var inputEl   = document.getElementById('input-area');

    // ── State ───────────────────────────────────────────────────────
    var state = {
        keluhan: '',
        urgensi: 'normal',
    };

    // ── Chat helpers ────────────────────────────────────────────────
    function appendSystem(text, delay) {
        return new Promise(function(resolve) {
            setTimeout(function() {
                var wrap = document.createElement('div');
                wrap.className = 'flex items-start gap-2.5 chat-bubble-in';
                wrap.innerHTML =
                    '<div class="w-8 h-8 rounded-xl bg-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg></div>' +
                    '<div class="bg-slate-100 text-slate-800 text-sm px-4 py-2.5 rounded-2xl rounded-tl-md max-w-[85%] leading-relaxed">' + text + '</div>';
                container.appendChild(wrap);
                scrollBottom();
                resolve();
            }, delay || 0);
        });
    }

    function appendUser(text) {
        var wrap = document.createElement('div');
        wrap.className = 'flex justify-end chat-bubble-in';
        wrap.innerHTML = '<div class="bg-brand-600 text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-md max-w-[80%] leading-relaxed">' + text + '</div>';
        container.appendChild(wrap);
        scrollBottom();
    }

    function showTyping() {
        var t = document.createElement('div');
        t.id = 'typing';
        t.className = 'flex items-start gap-2.5 chat-bubble-in';
        t.innerHTML =
            '<div class="w-8 h-8 rounded-xl bg-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg></div>' +
            '<div class="bg-slate-100 px-4 py-3 rounded-2xl rounded-tl-md flex gap-1"><span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span><span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span><span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span></div>';
        container.appendChild(t);
        scrollBottom();
        return t;
    }

    function removeTyping() { var t = document.getElementById('typing'); if (t) t.remove(); }

    function showChoices(opts) {
        choiceEl.innerHTML = '';
        choiceEl.classList.remove('hidden');
        inputEl.classList.add('hidden');
        opts.forEach(function(opt) {
            var btn = document.createElement('button');
            btn.className = 'choice-btn w-full text-left px-4 py-3 bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-300 text-slate-700 hover:text-brand-800 text-sm rounded-xl transition-all';
            btn.innerHTML = opt.label;
            btn.onclick = function() { handleChoice(opt); };
            choiceEl.appendChild(btn);
        });
    }

    function hideChoices() { choiceEl.classList.add('hidden'); choiceEl.innerHTML = ''; }

    function showInput() {
        choiceEl.classList.add('hidden');
        inputEl.classList.remove('hidden');
        var inp = document.getElementById('user-input');
        inp.value = '';
        setTimeout(function() { inp.focus(); }, 100);
    }

    function scrollBottom() { container.scrollTop = container.scrollHeight; }

    // ── Message flow (opening) ───────────────────────────────────────
    async function runOpening() {
        var t = showTyping();
        await sleep(900);
        removeTyping();
        await appendSystem('Halo <strong>' + userName + '</strong> 👋 Selamat datang di MediConnect!', 0);

        t = showTyping();
        await sleep(1200);
        removeTyping();
        await appendSystem('Saya asisten kesehatan digital Anda. Saya akan membantu mengumpulkan informasi keluhan Anda sebelum diteruskan ke dokter.', 0);

        await sleep(500);
        showChoices([
            { label: '🩺  Ya, saya ingin konsultasi dengan dokter', action: 'start' },
            { label: '📋  Lihat riwayat konsultasi saya', action: 'riwayat' },
            { label: '📅  Lihat jadwal dokter', action: 'jadwal' },
        ]);
    }

    // ── Handle pilihan ───────────────────────────────────────────────
    async function handleChoice(opt) {
        hideChoices();

        if (opt.action === 'riwayat') {
            appendUser('Lihat riwayat konsultasi saya');
            await sleep(300);
            appendSystem('Baik, mengarahkan ke halaman riwayat...', 0);
            await sleep(900);
            window.location.href = '/riwayat';
            return;
        }

        if (opt.action === 'jadwal') {
            appendUser('Lihat jadwal dokter');
            await sleep(300);
            appendSystem('Menampilkan jadwal dokter yang tersedia...', 0);
            await sleep(900);
            window.location.href = '/jadwal';
            return;
        }

        if (opt.action === 'start') {
            appendUser('Ya, saya ingin konsultasi dengan dokter');
            await sleep(400);
            var t = showTyping();
            await sleep(1000);
            removeTyping();
            await appendSystem('Baik! Ceritakan <strong>keluhan utama</strong> yang Anda rasakan saat ini. Semakin detail, semakin baik.', 0);
            await sleep(200);
            showInput();
            window._inputMode = 'keluhan';
            return;
        }

        if (opt.action === 'urgensi_normal') {
            state.urgensi = 'normal';
            appendUser('🟢 Normal — Masih bisa ditangani biasa');
            await processUrgensi();
            return;
        }
        if (opt.action === 'urgensi_urgent') {
            state.urgensi = 'urgent';
            appendUser('🟡 Agak mendesak');
            await processUrgensi();
            return;
        }
        if (opt.action === 'urgensi_darurat') {
            state.urgensi = 'darurat';
            appendUser('🔴 Darurat / sangat mendesak');
            await processUrgensi();
            return;
        }

        if (opt.action === 'go_form') {
            appendUser('Lanjutkan ke formulir konsultasi');
            await sleep(300);
            appendSystem('Menyiapkan formulir konsultasi dengan data Anda...', 0);
            await sleep(800);
            // Simpan state ke localStorage agar dipakai form konsultasi
            localStorage.setItem('draft_keluhan', state.keluhan);
            localStorage.setItem('draft_urgensi', state.urgensi);
            window.location.href = '/konsultasi/baru';
            return;
        }

        if (opt.action === 'ulang') {
            appendUser('Ulangi dari awal');
            await sleep(300);
            container.innerHTML = '';
            state = { keluhan: '', urgensi: 'normal' };
            await runOpening();
            return;
        }
    }

    // ── Handle input teks user ───────────────────────────────────────
    window.sendUserInput = async function() {
        var inp = document.getElementById('user-input');
        var val = inp.value.trim();
        if (!val) return;
        inp.value = '';
        inputEl.classList.add('hidden');
        appendUser(val);

        if (window._inputMode === 'keluhan') {
            state.keluhan = val;
            await sleep(400);
            var t = showTyping();
            await sleep(1400);
            removeTyping();

            // Analisis sederhana keluhan
            var lower = val.toLowerCase();
            var saran = '';
            if (lower.match(/demam|panas|suhu/)) {
                saran = '🌡️ Gejala <strong>demam</strong> terdeteksi. Bisa jadi tanda infeksi. Penting untuk diperiksa lebih lanjut.';
            } else if (lower.match(/batuk|pilek|flu|tenggorokan|bersin/)) {
                saran = '🤧 Keluhan <strong>saluran pernapasan</strong> terdeteksi. Dokter akan membantu menentukan penyebab dan penanganan yang tepat.';
            } else if (lower.match(/mual|muntah|diare|perut|lambung|maag/)) {
                saran = '🫃 Masalah <strong>pencernaan</strong> terdeteksi. Kondisi ini perlu dievaluasi agar tidak semakin parah.';
            } else if (lower.match(/pusing|kepala|migrain|vertigo/)) {
                saran = '🧠 <strong>Sakit kepala</strong> bisa memiliki berbagai penyebab. Dokter akan membantu menelusuri sumber masalahnya.';
            } else if (lower.match(/nyeri|sakit|ngilu|linu|pegal/)) {
                saran = '💢 Keluhan <strong>nyeri</strong> perlu dievaluasi untuk menentukan penyebab dan penanganan terbaik.';
            } else {
                saran = '📋 Keluhan Anda sudah saya catat. Dokter kami siap membantu mengevaluasi kondisi Anda.';
            }

            await appendSystem(saran, 0);
            await sleep(600);
            await appendSystem('Seberapa mendesak kondisi Anda saat ini?', 0);
            await sleep(200);
            showChoices([
                { label: '🟢  Normal — Bisa jadwal biasa', action: 'urgensi_normal' },
                { label: '🟡  Agak mendesak — Perlu cepat ditangani', action: 'urgensi_urgent' },
                { label: '🔴  Darurat — Sangat mendesak', action: 'urgensi_darurat' },
            ]);
        }
    };

    async function processUrgensi() {
        await sleep(400);
        var t = showTyping();
        await sleep(1000);
        removeTyping();

        var urgensiMsg = '';
        if (state.urgensi === 'darurat') {
            urgensiMsg = '⚠️ Kondisi darurat! Jika sangat serius, pertimbangkan untuk segera ke UGD. Namun Anda tetap bisa konsultasi online sekarang.';
        } else if (state.urgensi === 'urgent') {
            urgensiMsg = '⏰ Kami tandai sebagai <strong>prioritas</strong>. Dokter akan merespons lebih cepat.';
        } else {
            urgensiMsg = '✅ Baik, konsultasi akan dijadwalkan secara normal.';
        }

        await appendSystem(urgensiMsg, 0);
        await sleep(600);

        // Ringkasan
        var summary = '📋 <strong>Ringkasan keluhan Anda:</strong><br>' +
            '<span class="text-slate-600">• Keluhan: ' + state.keluhan + '</span><br>' +
            '<span class="text-slate-600">• Urgensi: ' + (state.urgensi === 'darurat' ? '🔴 Darurat' : state.urgensi === 'urgent' ? '🟡 Mendesak' : '🟢 Normal') + '</span>';

        await appendSystem(summary, 0);
        await sleep(500);
        await appendSystem('Lanjutkan ke <strong>formulir konsultasi</strong> untuk memilih dokter dan konfirmasi data Anda.', 0);
        await sleep(300);
        showChoices([
            { label: '✅  Lanjut ke formulir konsultasi dokter', action: 'go_form' },
            { label: '↩️  Ubah keluhan (ulangi dari awal)', action: 'ulang' },
        ]);
    }

    function sleep(ms) { return new Promise(function(r) { setTimeout(r, ms); }); }

    // ── Keyboard shortcut ────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey && !inputEl.classList.contains('hidden')) {
            e.preventDefault();
            sendUserInput();
        }
    });

    // ── Start ────────────────────────────────────────────────────────
    runOpening();
</script>
@endsection 