<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar — CareMate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#185FA5',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .form-input {
            transition: all 0.2s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(24, 95, 165, 0.15);
        }
        .btn-primary {
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(24, 95, 165, 0.2);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .error-box {
            display: none;
        }
        .error-box.show {
            display: flex;
        }
        .tab-btn {
            transition: all 0.2s ease;
        }
        .tab-btn.active {
            background-color: white;
            color: #185FA5;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-primary-500 selection:text-white min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        
        <!-- Left Side: Brand/Image -->
        <div class="md:w-5/12 bg-primary-600 text-white p-10 flex flex-col justify-center relative hidden md:flex">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-md">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" class="text-primary-600" viewBox="0 0 24 24">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">CareMate</span>
                </div>
                
                <h1 class="text-3xl font-bold leading-tight mb-4">
                    Bergabunglah Bersama Kami!
                </h1>
                <p class="text-primary-100 text-base leading-relaxed">
                    Buat akun sekarang dan nikmati kemudahan konsultasi dengan dokter spesialis, serta kelola kesehatan Anda dari satu aplikasi.
                </p>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
            <!-- Mobile Logo -->
            <div class="md:hidden flex items-center gap-3 mb-8 justify-center">
                <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-slate-800">CareMate</span>
            </div>

            <div class="mb-8 text-center md:text-left">
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Buat Akun Baru</h2>
                <p class="text-slate-500 text-sm">Lengkapi data diri di bawah ini untuk mendaftar.</p>
            </div>

            <div id="error-msg" class="error-box bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 mb-6 items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="error-text" class="text-sm font-medium"></span>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input id="name" type="text" placeholder="Nama lengkap Anda" autocomplete="name" 
                        class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400"/>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <div class="bg-slate-100 p-1 rounded-lg flex gap-1 mb-2">
                            <button class="tab-btn active flex-1 py-1.5 text-xs font-semibold text-slate-600 rounded-md" id="tab-email" onclick="switchTab('email')">Email</button>
                            <button class="tab-btn flex-1 py-1.5 text-xs font-semibold text-slate-600 rounded-md" id="tab-nohp" onclick="switchTab('nohp')">No. HP</button>
                        </div>
                        
                        <div id="field-email">
                            <input id="email" type="email" placeholder="email@contoh.com"
                                class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400"/>
                        </div>
                        <div id="field-nohp" style="display:none;">
                            <input id="no_hp" type="tel" placeholder="08xxxxxxxxxx"
                                class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400"/>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input id="tanggal_lahir" type="date" max="<?php echo date('Y-m-d'); ?>"
                            class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm text-slate-600"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                        <input id="password" type="password" placeholder="Min. 8 karakter" oninput="checkStrength()"
                            class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400"/>
                        <div class="flex items-center mt-2 gap-2">
                            <div class="flex-1 bg-slate-200 h-1 rounded-full overflow-hidden">
                                <div id="strength-bar" class="h-full bg-slate-200 transition-all duration-300 w-0"></div>
                            </div>
                            <span id="strength-text" class="text-[10px] font-bold text-slate-400 min-w-[40px] text-right"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" placeholder="Ulangi password"
                            class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400"/>
                    </div>
                </div>

                <button id="reg-btn" onclick="doRegister()" class="btn-primary w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg flex items-center justify-center gap-2 mt-4 disabled:bg-slate-400 disabled:cursor-not-allowed">
                    <span>Buat Akun Sekarang</span>
                </button>
            </div>

            <div class="mt-8 text-center text-sm">
                <p class="text-slate-500">
                    Sudah punya akun? 
                    <a href="/login" class="text-primary-600 font-semibold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>

<script>
    function checkStrength() {
        var pwd = document.getElementById('password').value;
        var bar = document.getElementById('strength-bar');
        var txt = document.getElementById('strength-text');
        var score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        var configs = [
            {w:'0%',c:'#e2e8f0',t:''},
            {w:'25%',c:'#ef4444',t:'LEMAH'},
            {w:'50%',c:'#f59e0b',t:'SEDANG'},
            {w:'75%',c:'#3b82f6',t:'BAIK'},
            {w:'100%',c:'#22c55e',t:'KUAT'},
        ];
        var cfg = configs[score] || configs[0];
        bar.style.width = cfg.w; 
        bar.style.backgroundColor = cfg.c;
        txt.textContent = cfg.t; 
        txt.style.color = cfg.c;
    }

    var currentMethod = 'email';
    function switchTab(method) {
        currentMethod = method;
        if (method === 'email') {
            document.getElementById('tab-email').classList.add('active');
            document.getElementById('tab-nohp').classList.remove('active');
            document.getElementById('field-email').style.display = 'block';
            document.getElementById('field-nohp').style.display = 'none';
        } else {
            document.getElementById('tab-nohp').classList.add('active');
            document.getElementById('tab-email').classList.remove('active');
            document.getElementById('field-nohp').style.display = 'block';
            document.getElementById('field-email').style.display = 'none';
        }
    }

    async function doRegister() {
        var errDiv  = document.getElementById('error-msg');
        var errText = document.getElementById('error-text');
        var btn     = document.getElementById('reg-btn');
        errDiv.classList.remove('show');

        var payload = {
            name: document.getElementById('name').value.trim(),
            email: currentMethod === 'email' ? document.getElementById('email').value.trim() : null,
            no_hp: currentMethod === 'nohp' ? document.getElementById('no_hp').value.trim() : null,
            tanggal_lahir: document.getElementById('tanggal_lahir').value,
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
        };

        if (!payload.name || !payload.password) {
            errText.textContent = 'Nama dan password wajib diisi.';
            errDiv.classList.add('show'); return;
        }
        if (currentMethod === 'email' && !payload.email) {
            errText.textContent = 'Email wajib diisi.';
            errDiv.classList.add('show'); return;
        }
        if (currentMethod === 'nohp' && !payload.no_hp) {
            errText.textContent = 'No. HP wajib diisi.';
            errDiv.classList.add('show'); return;
        }
        if (!payload.tanggal_lahir) {
            errText.textContent = 'Tanggal lahir wajib diisi.';
            errDiv.classList.add('show'); return;
        }
        if (payload.password !== payload.password_confirmation) {
            errText.textContent = 'Password dan konfirmasi tidak cocok.';
            errDiv.classList.add('show'); return;
        }
        if (payload.password.length < 8) {
            errText.textContent = 'Password minimal 8 karakter.';
            errDiv.classList.add('show'); return;
        }

        btn.disabled = true; 
        btn.innerHTML = '<span>Memproses...</span>';

        try {
            async function submitRegister(url) {
                return fetch(url, {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
            }

            var res = await submitRegister('/api/auth/register');
            if (res.status === 404) {
                res = await submitRegister('/auth/register');
            }

            var data = await res.json();
            if (!data.success) {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Registrasi gagal.');
                errText.textContent = msgs; errDiv.classList.add('show');
                btn.disabled = false; btn.innerHTML = '<span>Buat Akun Sekarang</span>'; return;
            }
            
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('auth_user', JSON.stringify(data.user));
            
            window.location.href = '/welcome';
        } catch (e) {
            errText.textContent = 'Gagal terhubung ke server. Silakan coba lagi.';
            errDiv.classList.add('show');
            btn.disabled = false; btn.innerHTML = '<span>Buat Akun Sekarang</span>';
        }
    }
</script>
</body>
</html>