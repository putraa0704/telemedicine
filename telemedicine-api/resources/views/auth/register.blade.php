<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar — MediConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: #f1f5f9; min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 16px;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background: radial-gradient(ellipse at 20% 20%, rgba(24,95,165,0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 80%, rgba(55,138,221,0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .card {
            background: white; border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
            padding: 32px; width: 100%; max-width: 460px; position: relative;
        }
        .field-group { display: flex; flex-direction: column; gap: 14px; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 440px) { .row-2 { grid-template-columns: 1fr; } .card { padding: 24px 18px; } }
        input { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; transition: all 0.15s; background: #f8fafc; color: #1e293b; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        input:focus { border-color: #185FA5; box-shadow: 0 0 0 3px rgba(24,95,165,0.1); background: white; }
        label { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; }
        .btn-primary { width: 100%; padding: 12px; background: #185FA5; color: white; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.15s; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-primary:hover { background: #0C447C; }
        .btn-primary:active { transform: scale(0.99); }
        .btn-primary:disabled { background: #94a3b8; cursor: not-allowed; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #dc2626; display: none; margin-bottom: 16px; }
        .error-box.show { display: block; }
        .strength-bar { height: 4px; border-radius: 2px; transition: all 0.3s; margin-top: 6px; }
        .strength-text { font-size: 11px; margin-top: 4px; }
    </style>
</head>
<body>
<div class="card">
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:52px;height:52px;border-radius:14px;background:#185FA5;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;box-shadow:0 4px 12px rgba(24,95,165,0.3);">
            <svg width="24" height="24" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
        </div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Buat Akun</h1>
        <p style="font-size:13px;color:#94a3b8;margin:4px 0 0;">Daftar sebagai pasien MediConnect</p>
    </div>

    <div id="error-msg" class="error-box"></div>

    <div class="field-group">
        <div>
            <label>Nama Lengkap</label>
            <input id="name" type="text" placeholder="Nama lengkap Anda" autocomplete="name"/>
        </div>
        <div class="row-2">
            <div>
                <label>Email</label>
                <input id="email" type="email" placeholder="email@contoh.com"/>
            </div>
            <div>
                <label>No. HP</label>
                <input id="no_hp" type="tel" placeholder="08xxxxxxxxxx"/>
            </div>
        </div>
        <div class="row-2">
            <div>
                <label>Password</label>
                <input id="password" type="password" placeholder="Min. 8 karakter" oninput="checkStrength()"/>
                <div id="strength-bar" class="strength-bar" style="background:#e2e8f0;width:0%;"></div>
                <div id="strength-text" class="strength-text" style="color:#94a3b8;"></div>
            </div>
            <div>
                <label>Konfirmasi Password</label>
                <input id="password_confirmation" type="password" placeholder="Ulangi password"/>
            </div>
        </div>

        <button class="btn-primary" id="reg-btn" onclick="doRegister()">Buat Akun Sekarang</button>
    </div>

    <div style="text-align:center;margin-top:18px;font-size:13px;color:#94a3b8;">
        Sudah punya akun? <a href="/login" style="color:#185FA5;font-weight:600;text-decoration:none;">Masuk di sini</a>
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
            {w:'25%',c:'#ef4444',t:'Lemah'},
            {w:'50%',c:'#f59e0b',t:'Sedang'},
            {w:'75%',c:'#3b82f6',t:'Baik'},
            {w:'100%',c:'#22c55e',t:'Kuat'},
        ];
        var cfg = configs[score] || configs[0];
        bar.style.width = cfg.w; bar.style.background = cfg.c;
        txt.textContent = cfg.t; txt.style.color = cfg.c;
    }

    async function doRegister() {
        var errDiv = document.getElementById('error-msg');
        var btn    = document.getElementById('reg-btn');
        errDiv.classList.remove('show');

        var payload = {
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            no_hp: document.getElementById('no_hp').value.trim(),
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
        };

        if (!payload.name || !payload.email || !payload.password) {
            errDiv.textContent = 'Nama, email, dan password wajib diisi.';
            errDiv.classList.add('show'); return;
        }
        if (payload.password !== payload.password_confirmation) {
            errDiv.textContent = 'Password dan konfirmasi tidak cocok.';
            errDiv.classList.add('show'); return;
        }
        if (payload.password.length < 8) {
            errDiv.textContent = 'Password minimal 8 karakter.';
            errDiv.classList.add('show'); return;
        }

        btn.disabled = true; btn.textContent = 'Memproses...';
        try {
            var res  = await fetch('/api/auth/register', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            var data = await res.json();
            if (!data.success) {
                var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Registrasi gagal.');
                errDiv.textContent = msgs; errDiv.classList.add('show');
                btn.disabled = false; btn.textContent = 'Buat Akun Sekarang'; return;
            }
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('auth_user', JSON.stringify(data.user));
            // Pasien baru → chatbot welcome
            window.location.href = '/welcome';
        } catch (e) {
            errDiv.textContent = 'Gagal terhubung ke server.';
            errDiv.classList.add('show');
            btn.disabled = false; btn.textContent = 'Buat Akun Sekarang';
        }
    }
</script>
</body>
</html>