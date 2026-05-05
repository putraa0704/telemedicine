<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Masuk — CareMate</title>
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
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-primary-500 selection:text-white min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">
        
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
                    Selamat Datang Kembali!
                </h1>
                <p class="text-primary-100 text-base leading-relaxed">
                    Masuk ke akun Anda untuk mengakses layanan kesehatan terpadu dan mengelola jadwal konsultasi dengan mudah.
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
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Masuk ke Akun</h2>
                <p class="text-slate-500 text-sm">Silakan masukkan detail login Anda.</p>
            </div>

            <div id="error-msg" class="error-box bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 mb-6 items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="error-text" class="text-sm font-medium"></span>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email / No. HP</label>
                    <input id="identifier" type="text" placeholder="Masukkan email atau no. hp" autocomplete="email" 
                        class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400"/>
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Password</label>
                        <a href="#" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <input id="password" type="password" placeholder="••••••••" autocomplete="current-password" 
                            class="form-input w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-primary-500 outline-none text-slate-800 text-sm placeholder:text-slate-400 pr-10"/>
                        <button type="button" onclick="togglePwd()" id="pwd-toggle" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1.5 rounded-md">
                            <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button id="login-btn" onclick="doLogin()" class="btn-primary w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg flex items-center justify-center gap-2 mt-2 disabled:bg-slate-400 disabled:cursor-not-allowed">
                    <span>Masuk</span>
                </button>
            </div>

            <div class="mt-8 text-center text-sm">
                <p class="text-slate-500">
                    Belum punya akun? 
                    <a href="/register" class="text-primary-600 font-semibold hover:underline">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </div>

<script>
    var t = localStorage.getItem('auth_token');
    var u = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (t && u) {
        if (u.role === 'dokter') { window.location.href = '/dokter'; }
        else if (u.role === 'admin') { window.location.href = '/admin'; }
        else { window.location.href = '/welcome'; } 
    }

    function togglePwd() {
        var inp = document.getElementById('password');
        var ico = document.getElementById('eye-icon');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            inp.type = 'password';
            ico.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    async function doLogin() {
        var identifier = document.getElementById('identifier').value.trim();
        var password = document.getElementById('password').value;
        var errDiv   = document.getElementById('error-msg');
        var errText  = document.getElementById('error-text');
        var btn      = document.getElementById('login-btn');
        errDiv.classList.remove('show');

        if (!identifier || !password) {
            errText.textContent = 'Email/No. HP dan password wajib diisi.';
            errDiv.classList.add('show'); return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span>Memproses...</span>';

        try {
            async function submitLogin(url) {
                return fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ identifier, password })
                });
            }

            var endpoints = ['/api/auth/login', '/auth/login'];
            var res = null;
            var lastErr = null;

            for (var i = 0; i < endpoints.length; i++) {
                try {
                    var candidate = await submitLogin(endpoints[i]);
                    if (candidate.status === 404) continue;
                    res = candidate;
                    break;
                } catch (err) {
                    lastErr = err;
                }
            }

            if (!res) throw (lastErr || new Error('Login endpoint tidak tersedia'));

            var data = await res.json();

            if (!data.success) {
                errText.textContent = data.message || 'Email atau password salah.';
                errDiv.classList.add('show');
                btn.disabled = false; btn.innerHTML = '<span>Masuk</span>'; return;
            }

            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('auth_user',  JSON.stringify(data.user));

            if (data.role === 'admin') {
                window.location.href = '/admin';
            } else if (data.role === 'dokter') {
                window.location.href = '/dokter';
            } else {
                window.location.href = '/welcome';
            }
        } catch (e) {
            errText.textContent = 'Gagal terhubung ke server. Silakan coba lagi.';
            errDiv.classList.add('show');
            btn.disabled = false; btn.innerHTML = '<span>Masuk</span>';
        }
    }

    document.addEventListener('keydown', function(e) { if (e.key === 'Enter') doLogin(); });
</script>
</body>
</html>