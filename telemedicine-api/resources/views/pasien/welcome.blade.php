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
                <div class="text-[13px] font-semibold text-slate-800">Asisten CareMate</div>
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

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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

    function analisaKeluhan(text) {
        var lower = (text || '').toLowerCase();
        var rules = [
            {
                patterns: /demam|panas|menggigil|suhu tinggi|meriang/,
                message: '🌡️ Gejala <strong>demam</strong> terdeteksi. Bisa berkaitan dengan infeksi dan perlu dipantau suhunya secara berkala.'
            },
            {
                patterns: /batuk|pilek|flu|tenggorokan|bersin|hidung tersumbat|ingus/,
                message: '🤧 Keluhan <strong>saluran pernapasan atas</strong> terdeteksi. Dokter dapat membantu memastikan apakah ini infeksi virus, alergi, atau penyebab lain.'
            },
            {
                patterns: /sesak|sulit napas|napas pendek|asma|mengi|bengek/,
                message: '🫁 Keluhan <strong>pernapasan</strong> terdeteksi. Bila sesak memberat, ini perlu perhatian cepat dari dokter.'
            },
            {
                patterns: /nyeri dada|dada sakit|berdebar|jantung|detak cepat|detak tidak teratur/,
                message: '❤️ Keluhan pada <strong>dada atau jantung</strong> terdeteksi. Ini perlu evaluasi medis lebih lanjut untuk menyingkirkan kondisi serius.'
            },
            {
                patterns: /mual|muntah|diare|perut|lambung|maag|kembung|sembelit|konstipasi|bab/,
                message: '🫃 Masalah <strong>pencernaan</strong> terdeteksi. Dokter akan bantu menilai penyebab dan terapi yang sesuai.'
            },
            {
                patterns: /pusing|kepala|migrain|vertigo|sakit kepala|kunang-kunang/,
                message: '🧠 Keluhan <strong>sakit kepala atau pusing</strong> terdeteksi. Perlu dilihat pola, durasi, dan faktor pemicunya.'
            },
            {
                patterns: /nyeri|sakit|ngilu|linu|pegal|otot|sendi|pinggang|punggung|leher/,
                message: '💢 Keluhan <strong>nyeri otot atau sendi</strong> terdeteksi. Dokter akan mengevaluasi kemungkinan peradangan, cedera, atau faktor lain.'
            },
            {
                patterns: /gatal|ruam|bintik|kulit merah|jerawat parah|eksim|alergi kulit|biduran/,
                message: '🧴 Keluhan <strong>kulit</strong> terdeteksi. Perlu evaluasi apakah berkaitan dengan alergi, iritasi, atau infeksi.'
            },
            {
                patterns: /mata merah|mata perih|mata buram|penglihatan kabur|belekan|mata gatal/,
                message: '👁️ Keluhan <strong>mata</strong> terdeteksi. Dokter dapat membantu menilai apakah ini iritasi, infeksi, atau gangguan penglihatan lain.'
            },
            {
                patterns: /telinga|berdenging|pendengaran|sakit telinga|keluar cairan telinga/,
                message: '👂 Keluhan <strong>telinga</strong> terdeteksi. Perlu pemeriksaan untuk menilai infeksi atau gangguan pendengaran.'
            },
            {
                patterns: /gigi|gusi|sariawan|mulut bau|mulut sakit|rahang/,
                message: '🦷 Keluhan <strong>gigi atau mulut</strong> terdeteksi. Dokter dapat memberi penanganan awal dan rujukan bila diperlukan.'
            },
            {
                patterns: /kencing|anyang-anyangan|nyeri saat kencing|urin|air seni|beser/,
                message: '🚻 Keluhan <strong>saluran kemih</strong> terdeteksi. Ini perlu dievaluasi untuk menyingkirkan infeksi atau iritasi.'
            },
            {
                patterns: /haid|menstruasi|nyeri haid|keputihan|siklus haid|telat haid/,
                message: '🩺 Keluhan <strong>kesehatan reproduksi wanita</strong> terdeteksi. Dokter akan membantu menilai kondisi hormonal atau infeksi.'
            },
            {
                patterns: /hamil|kehamilan|mual hamil|kontraksi|kandungan|janin/,
                message: '🤰 Keluhan terkait <strong>kehamilan</strong> terdeteksi. Penting untuk pemantauan ibu dan janin secara tepat waktu.'
            },
            {
                patterns: /gula darah|diabetes|kencing manis|hiperglikemi|hipoglikemi/,
                message: '🩸 Keluhan terkait <strong>gula darah</strong> terdeteksi. Dokter akan membantu evaluasi kontrol metabolik Anda.'
            },
            {
                patterns: /darah tinggi|hipertensi|tekanan darah|tensi tinggi|tensi rendah/,
                message: '🫀 Keluhan terkait <strong>tekanan darah</strong> terdeteksi. Perlu pemantauan rutin untuk mencegah komplikasi.'
            },
            {
                patterns: /susah tidur|sulit tidur|tidak bisa tidur|tak bisa tidur|ga bisa tidur|gak bisa tidur|gk bisa tidur|nggak bisa tidur|ngga bisa tidur|gabisa tidur|insomnia|sering terbangun|tidur tidak nyenyak|kualitas tidur buruk/,
                message: '😴 Keluhan <strong>gangguan tidur (insomnia)</strong> terdeteksi. Dokter akan membantu mencari pemicu seperti stres, kebiasaan tidur, atau kondisi medis lain.'
            },
            {
                patterns: /cemas|ansietas|panik|stres|sulit tidur|tidak bisa tidur|ga bisa tidur|nggak bisa tidur|insomnia|begadang|sering terbangun|gelisah|sedih berkepanjangan/,
                message: '🧘 Keluhan <strong>kesehatan mental</strong> terdeteksi. Dokter dapat membantu skrining awal dan saran penanganan lanjutan.'
            },
            {
                patterns: /luka|bernanah|bengkak|infeksi|memar|jatuh|terbentur|keseleo/,
                message: '🩹 Keluhan <strong>cedera atau infeksi lokal</strong> terdeteksi. Penanganan dini penting untuk mencegah kondisi memburuk.'
            },
            {
                patterns: /anak|bayi|balita|demam anak|batuk anak|pilek anak/,
                message: '🧒 Keluhan pada <strong>anak</strong> terdeteksi. Dokter akan menilai sesuai usia dan kondisi tumbuh kembangnya.'
            }
        ];

        var matched = rules.filter(function(rule) { return rule.patterns.test(lower); }).map(function(rule) { return rule.message; });

        if (!matched.length) {
            return '📋 Keluhan Anda sudah saya catat. Dokter kami siap membantu mengevaluasi kondisi Anda secara menyeluruh.';
        }

        return matched.slice(0, 2).join('<br><br>');
    }

    // ── Message flow (opening) ───────────────────────────────────────
    async function runOpening() {
        var t = showTyping();
        await sleep(900);
        removeTyping();
        await appendSystem('Halo <strong>' + userName + '</strong> 👋 Selamat datang di CareMate!', 0);

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

        if (opt.action === 'go_form') {
            appendUser('Lanjutkan ke formulir konsultasi');
            await sleep(300);
            appendSystem('Menyiapkan formulir konsultasi dengan data Anda...', 0);
            await sleep(800);
            // Simpan state ke localStorage agar dipakai form konsultasi
            localStorage.setItem('draft_keluhan', state.keluhan);
            window.location.href = '/konsultasi/baru';
            return;
        }

        if (opt.action === 'ulang') {
            appendUser('Ulangi dari awal');
            await sleep(300);
            container.innerHTML = '';
            state = { keluhan: '' };
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

            // Analisis keluhan dengan cakupan gejala lebih luas.
            var saran = analisaKeluhan(val);

            await appendSystem(saran, 0);
            await sleep(600);
            await showPostKeluhanChoices();
        }
    };

    async function showPostKeluhanChoices() {
        await sleep(400);
        var t = showTyping();
        await sleep(900);
        removeTyping();

        // Ringkasan
        var summary = '📋 <strong>Ringkasan keluhan Anda:</strong><br>' +
            '<span class="text-slate-600">• Keluhan: ' + escapeHtml(state.keluhan) + '</span>';

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