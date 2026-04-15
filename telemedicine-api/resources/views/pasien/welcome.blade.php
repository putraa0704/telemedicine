<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Selamat Datang — MediConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
        }

        .chat-bubble-in {
            animation: slideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            opacity: 0;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(12px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .typing-dot {
            animation: blink 1.4s infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes blink {
            0%, 60%, 100% { opacity: 0.2; transform: scale(0.8); }
            30% { opacity: 1; transform: scale(1); }
        }

        .choice-btn {
            transition: all 0.2s;
        }
        .choice-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
        }
        .choice-btn:active { transform: scale(0.97); }

        .pulse-ring {
            animation: pulse-ring 2s ease-out infinite;
        }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
            70%  { box-shadow: 0 0 0 12px rgba(99, 102, 241, 0); }
            100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }

        #chat-container::-webkit-scrollbar { width: 4px; }
        #chat-container::-webkit-scrollbar-track { background: transparent; }
        #chat-container::-webkit-scrollbar-thumb { background: #334155; border-radius: 2px; }
    </style>
</head>
<body class="flex items-center justify-center p-4">

<div class="w-full max-w-md">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg pulse-ring">M</div>
        <div>
            <div class="text-white font-semibold text-sm">MediConnect</div>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                <span class="text-slate-400 text-xs">Sistem Aktif</span>
            </div>
        </div>
    </div>

    {{-- Chat Window --}}
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-slate-700/50 overflow-hidden shadow-2xl">

        <div id="chat-container" class="p-5 space-y-4 min-h-[420px] max-h-[480px] overflow-y-auto flex flex-col justify-end">
            {{-- Messages rendered by JS --}}
        </div>

        {{-- Input area (shown for text input if needed) --}}
        <div id="input-area" class="hidden px-5 pb-5">
            <div class="flex gap-2">
                <input id="user-input" type="text" placeholder="Ketik pesan..."
                    class="flex-1 bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-indigo-500 transition"/>
                <button onclick="sendUserInput()"
                    class="w-10 h-10 bg-indigo-600 hover:bg-indigo-500 rounded-xl flex items-center justify-center transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Choice buttons --}}
        <div id="choice-area" class="hidden px-5 pb-5 space-y-2"></div>
    </div>

    {{-- Nama pasien (auto filled) --}}
    <p id="user-hint" class="text-center text-slate-500 text-xs mt-4"></p>
</div>

<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || 'null');

    if (!token || !user) { window.location.href = '/login'; }
    if (user && user.role !== 'pasien') { window.location.href = '/dokter'; }

    var userName  = user ? user.name.split(' ')[0] : 'Pengguna';
    var container = document.getElementById('chat-container');
    var choiceEl  = document.getElementById('choice-area');

    document.getElementById('user-hint').textContent = 'Masuk sebagai ' + (user ? user.name : '');

    // ── Chat Script ──────────────────────────────────────────────
    var script = [
        {
            type: 'system',
            delay: 600,
            text: 'Halo <strong>' + userName + '</strong> 👋 Selamat datang di MediConnect!'
        },
        {
            type: 'system',
            delay: 1800,
            text: 'Saya adalah asisten kesehatan digital Anda. Ada yang bisa saya bantu hari ini?'
        },
        {
            type: 'choices',
            delay: 2800,
            options: [
                { label: '🏥  Saya ingin konsultasi dengan dokter', action: 'konsultasi' },
                { label: '📋  Lihat riwayat konsultasi saya', action: 'riwayat' },
                { label: '📅  Lihat jadwal dokter', action: 'jadwal' },
                { label: '🏠  Ke dashboard utama', action: 'dashboard' },
            ]
        }
    ];

    // ── User answers symptom check ──
    var symptomScript = [
        {
            type: 'system',
            delay: 400,
            text: 'Baik, sebelum ke dokter, boleh saya tanya dulu — <strong>apa keluhan utama Anda?</strong>'
        },
        {
            type: 'input',
            delay: 1200,
            placeholder: 'Contoh: demam, pusing, batuk...'
        }
    ];

    var currentStep = 0;
    var choicesShown = false;

    function appendSystemMessage(text) {
        var wrap = document.createElement('div');
        wrap.className = 'flex items-start gap-2.5 chat-bubble-in';

        var avatar = document.createElement('div');
        avatar.className = 'w-7 h-7 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5';
        avatar.textContent = 'M';

        var bubble = document.createElement('div');
        bubble.className = 'bg-slate-800 border border-slate-700/50 text-slate-200 text-sm px-4 py-2.5 rounded-2xl rounded-tl-md max-w-[85%] leading-relaxed';
        bubble.innerHTML = text;

        wrap.appendChild(avatar);
        wrap.appendChild(bubble);
        container.appendChild(wrap);
        container.scrollTop = container.scrollHeight;
        return wrap;
    }

    function appendUserMessage(text) {
        var wrap = document.createElement('div');
        wrap.className = 'flex justify-end chat-bubble-in';

        var bubble = document.createElement('div');
        bubble.className = 'bg-indigo-600 text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-md max-w-[80%] leading-relaxed';
        bubble.textContent = text;

        wrap.appendChild(bubble);
        container.appendChild(wrap);
        container.scrollTop = container.scrollHeight;
    }

    function showTyping() {
        var wrap = document.createElement('div');
        wrap.id = 'typing-indicator';
        wrap.className = 'flex items-start gap-2.5 chat-bubble-in';

        var avatar = document.createElement('div');
        avatar.className = 'w-7 h-7 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5';
        avatar.textContent = 'M';

        var bubble = document.createElement('div');
        bubble.className = 'bg-slate-800 border border-slate-700/50 px-4 py-3 rounded-2xl rounded-tl-md flex gap-1';
        bubble.innerHTML = '<span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span><span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span><span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span>';

        wrap.appendChild(avatar);
        wrap.appendChild(bubble);
        container.appendChild(wrap);
        container.scrollTop = container.scrollHeight;
        return wrap;
    }

    function removeTyping() {
        var t = document.getElementById('typing-indicator');
        if (t) t.remove();
    }

    function showChoices(options) {
        choiceEl.innerHTML = '';
        choiceEl.classList.remove('hidden');

        options.forEach(function(opt) {
            var btn = document.createElement('button');
            btn.className = 'choice-btn w-full text-left px-4 py-3 bg-slate-800 hover:bg-indigo-600/20 border border-slate-700 hover:border-indigo-500 text-slate-200 hover:text-white text-sm rounded-xl transition';
            btn.textContent = opt.label;
            btn.onclick = function() { handleChoice(opt); };
            choiceEl.appendChild(btn);
        });
    }

    function hideChoices() {
        choiceEl.classList.add('hidden');
        choiceEl.innerHTML = '';
    }

    function handleChoice(opt) {
        hideChoices();
        appendUserMessage(opt.label);

        if (opt.action === 'dashboard') {
            setTimeout(function() {
                appendSystemMessage('Baik! Mengarahkan ke dashboard utama...');
                setTimeout(function() { window.location.href = '/pasien'; }, 1000);
            }, 400);
        }
        else if (opt.action === 'riwayat') {
            setTimeout(function() {
                appendSystemMessage('Membuka riwayat konsultasi Anda...');
                setTimeout(function() { window.location.href = '/riwayat'; }, 1000);
            }, 400);
        }
        else if (opt.action === 'jadwal') {
            setTimeout(function() {
                appendSystemMessage('Menampilkan jadwal dokter yang tersedia...');
                setTimeout(function() { window.location.href = '/jadwal'; }, 1000);
            }, 400);
        }
        else if (opt.action === 'konsultasi') {
            runSymptomFlow();
        }
        else if (opt.action === 'kirim_konsultasi') {
            // Ambil keluhan dari state
            window.location.href = '/konsultasi/baru';
        }
        else if (opt.action === 'langsung_dokter') {
            setTimeout(function() {
                appendSystemMessage('Mengarahkan ke halaman konsultasi dokter...');
                setTimeout(function() { window.location.href = '/konsultasi/baru'; }, 900);
            }, 400);
        }
        else if (opt.action === 'kembali') {
            setTimeout(function() {
                appendSystemMessage('Tentu! Ada lagi yang bisa saya bantu?');
                setTimeout(function() {
                    showChoices([
                        { label: '🏥  Saya ingin konsultasi dengan dokter', action: 'konsultasi' },
                        { label: '📋  Lihat riwayat konsultasi saya', action: 'riwayat' },
                        { label: '📅  Lihat jadwal dokter', action: 'jadwal' },
                        { label: '🏠  Ke dashboard utama', action: 'dashboard' },
                    ]);
                }, 800);
            }, 400);
        }
    }

    function runSymptomFlow() {
        setTimeout(function() {
            var t = showTyping();
            setTimeout(function() {
                removeTyping();
                appendSystemMessage('Baik! Sebelum saya arahkan ke dokter, boleh ceritakan <strong>keluhan utama</strong> Anda?');
                setTimeout(function() {
                    // Show input
                    var inputArea = document.getElementById('input-area');
                    inputArea.classList.remove('hidden');
                    document.getElementById('user-input').focus();
                    window._inputMode = 'keluhan';
                }, 600);
            }, 1200);
        }, 300);
    }

    window.sendUserInput = function() {
        var input = document.getElementById('user-input');
        var val   = input.value.trim();
        if (!val) return;

        input.value = '';
        document.getElementById('input-area').classList.add('hidden');
        appendUserMessage(val);

        if (window._inputMode === 'keluhan') {
            window._keluhan = val;
            // Simpan ke localStorage untuk dipakai di halaman konsultasi
            localStorage.setItem('draft_keluhan', val);

            setTimeout(function() {
                var t = showTyping();
                setTimeout(function() {
                    removeTyping();
                    // Simple symptom classification
                    var lowerVal = val.toLowerCase();
                    var saran = '';
                    if (lowerVal.match(/demam|panas|fever/)) {
                        saran = '🌡️ Keluhan <strong>demam</strong> bisa disebabkan infeksi virus atau bakteri. Disarankan istirahat cukup dan minum banyak air. ';
                    } else if (lowerVal.match(/batuk|pilek|flu|bersin/)) {
                        saran = '🤧 Gejala <strong>ISPA</strong> seperti batuk/pilek umumnya memerlukan istirahat dan hidrasi. ';
                    } else if (lowerVal.match(/mual|muntah|diare|perut/)) {
                        saran = '🫃 Keluhan <strong>pencernaan</strong> perlu dievaluasi. Hindari makanan berat dan jaga cairan tubuh. ';
                    } else if (lowerVal.match(/pusing|kepala|migrain/)) {
                        saran = '🧠 <strong>Pusing/sakit kepala</strong> bisa memiliki banyak penyebab. Istirahat dan hindari layar sebentar. ';
                    } else {
                        saran = '📋 Keluhan Anda telah saya catat. ';
                    }

                    appendSystemMessage(saran + 'Saya sarankan Anda untuk berkonsultasi dengan dokter agar mendapat penanganan yang tepat.');

                    setTimeout(function() {
                        showChoices([
                            { label: '✅  Lanjut konsultasi dengan dokter', action: 'langsung_dokter' },
                            { label: '↩️  Kembali ke menu utama', action: 'kembali' },
                        ]);
                    }, 800);
                }, 1800);
            }, 400);
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !document.getElementById('input-area').classList.contains('hidden')) {
            sendUserInput();
        }
    });

    // ── Run opening script ──
    function runScript(steps, idx) {
        if (idx >= steps.length) return;
        var step = steps[idx];

        setTimeout(function() {
            if (step.type === 'system') {
                var t = showTyping();
                setTimeout(function() {
                    removeTyping();
                    appendSystemMessage(step.text);
                    runScript(steps, idx + 1);
                }, 800);
            } else if (step.type === 'choices') {
                showChoices(step.options);
            } else if (step.type === 'input') {
                var inputArea = document.getElementById('input-area');
                inputArea.classList.remove('hidden');
                document.getElementById('user-input').placeholder = step.placeholder || 'Ketik pesan...';
                document.getElementById('user-input').focus();
            }
        }, step.delay || 0);
    }

    runScript(script, 0);
</script>
</body>
</html>