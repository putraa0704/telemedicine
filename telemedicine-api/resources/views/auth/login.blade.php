<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Masuk — MediConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 600: '#185FA5', 800: '#0C447C' } } } }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6 font-sans antialiased">

<div class="w-full max-w-sm">

    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-brand-600 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">M</div>
        <h1 class="text-2xl font-bold text-slate-800">MediConnect</h1>
        <p class="text-slate-400 text-sm mt-1">Selamat datang kembali</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-7 shadow-sm">

        <div id="error-msg" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                <input id="email" type="email" placeholder="email@contoh.com" autocomplete="email"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Password</label>
                <input id="password" type="password" placeholder="••••••••" autocomplete="current-password"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
            </div>
            <button onclick="doLogin()"
                class="w-full bg-brand-600 hover:bg-brand-800 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                Masuk
            </button>
        </div>

        <p class="text-center text-sm text-slate-400 mt-5">
            Belum punya akun?
            <a href="/register" class="text-brand-600 font-medium hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <p class="text-center text-xs text-slate-400 mt-4">🔒 Data Anda dilindungi dengan enkripsi</p>
</div>

<script>
    // Redirect jika sudah login
    var t = localStorage.getItem('auth_token');
    var u = JSON.parse(localStorage.getItem('auth_user') || 'null');
    if (t && u) {
        window.location.href = (u.role === 'dokter' || u.role === 'admin') ? '/dokter' : '/welcome';
    }

    async function doLogin() {
        var email    = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;
        var errDiv   = document.getElementById('error-msg');
        errDiv.classList.add('hidden');

        if (!email || !password) {
            errDiv.textContent = 'Email dan password wajib diisi.';
            errDiv.classList.remove('hidden'); return;
        }

        try {
            var res  = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            var data = await res.json();

            if (!data.success) {
                errDiv.textContent = data.message || 'Email atau password salah.';
                errDiv.classList.remove('hidden'); return;
            }

            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('auth_user',  JSON.stringify(data.user));

            // Pasien → welcome chat dulu, dokter/admin → langsung dashboard
            if (data.role === 'dokter' || data.role === 'admin') {
                window.location.href = '/dokter';
            } else {
                window.location.href = '/welcome';
            }

        } catch (e) {
            errDiv.textContent = 'Gagal terhubung ke server.';
            errDiv.classList.remove('hidden');
        }
    }

    document.addEventListener('keydown', function(e) { if (e.key === 'Enter') doLogin(); });
</script>
</body>
</html>