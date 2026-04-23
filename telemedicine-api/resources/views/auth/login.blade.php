<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Masuk — CareMate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(24,95,165,0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 80%, rgba(55,138,221,0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
            padding: 32px;
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        input[type=email], input[type=password] {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
            background: #f8fafc; color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        input:focus { border-color: #185FA5; box-shadow: 0 0 0 3px rgba(24,95,165,0.1); background: white; }
        label { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; }
        .btn-primary {
            width: 100%; padding: 12px;
            background: #185FA5; color: white;
            border: none; border-radius: 12px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; transition: background 0.15s, transform 0.1s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-primary:hover { background: #0C447C; }
        .btn-primary:active { transform: scale(0.99); }
        .btn-primary:disabled { background: #94a3b8; cursor: not-allowed; }
        .error-box {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 10px; padding: 10px 14px;
            font-size: 13px; color: #dc2626;
            display: none; margin-bottom: 16px;
        }
        .error-box.show { display: block; }
        @media (max-width: 440px) {
            .card { padding: 24px 20px; border-radius: 16px; }
        }
    </style>
</head>
<body>
<div class="card">

    {{-- Logo --}}
    <div class="text-center mb-7">
        <div style="width:52px;height:52px;border-radius:14px;background:#185FA5;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;box-shadow:0 4px 12px rgba(24,95,165,0.3);">
            <svg width="24" height="24" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
        </div>
        <h1 style="font-size:22px;font-weight:700;color:#0f172a;margin:0;">CareMate</h1>
        <p style="font-size:13px;color:#94a3b8;margin:4px 0 0;">Selamat datang kembali</p>
    </div>

    <div id="error-msg" class="error-box"></div>

    <div style="display:flex;flex-direction:column;gap:16px;">
        <div>
            <label>Email</label>
            <input id="email" type="email" placeholder="nama@email.com" autocomplete="email"/>
        </div>
        <div>
            <label>Password</label>
            <div style="position:relative;">
                <input id="password" type="password" placeholder="••••••••" autocomplete="current-password" style="padding-right:42px;"/>
                <button type="button" onclick="togglePwd()" id="pwd-toggle"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;">
                    <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <button class="btn-primary" id="login-btn" onclick="doLogin()">
            Masuk ke Akun
        </button>
    </div>

    <div style="text-align:center;margin-top:20px;font-size:13px;color:#94a3b8;">
        Belum punya akun?
        <a href="/register" style="color:#185FA5;font-weight:600;text-decoration:none;">Daftar sekarang</a>
    </div>

    <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;text-align:center;">
        <span style="font-size:11px;color:#cbd5e1;">🔒 Dilindungi dengan enkripsi SSL</span>
    </div>
</div>

<script>
    // Jika sudah login, redirect sesuai role
    var t = localStorage.getItem('auth_token');
    var u = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (t && u) {
        if (u.role === 'dokter') { window.location.href = '/dokter'; }
        else if (u.role === 'admin') { window.location.href = '/admin'; }
        else { window.location.href = '/welcome'; }  // pasien → chatbot
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
        var email    = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;
        var errDiv   = document.getElementById('error-msg');
        var btn      = document.getElementById('login-btn');
        errDiv.classList.remove('show');

        if (!email || !password) {
            errDiv.textContent = 'Email dan password wajib diisi.';
            errDiv.classList.add('show'); return;
        }

        btn.disabled = true;
        btn.textContent = 'Memproses...';

        try {
            async function submitLogin(url) {
                return fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
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
                errDiv.textContent = data.message || 'Email atau password salah.';
                errDiv.classList.add('show');
                btn.disabled = false; btn.textContent = 'Masuk ke Akun'; return;
            }

            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('auth_user',  JSON.stringify(data.user));

            // ── Redirect sesuai role ──
            if (data.role === 'admin') {
                window.location.href = '/admin';
            } else if (data.role === 'dokter') {
                window.location.href = '/dokter';
            } else {
                // Pasien → chatbot welcome dulu
                window.location.href = '/welcome';
            }
        } catch (e) {
            errDiv.textContent = 'Gagal terhubung ke server.';
            errDiv.classList.add('show');
            btn.disabled = false; btn.textContent = 'Masuk ke Akun';
        }
    }

    document.addEventListener('keydown', function(e) { if (e.key === 'Enter') doLogin(); });
</script>
</body>
</html>