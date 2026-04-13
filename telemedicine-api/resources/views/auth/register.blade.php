<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar — MediConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 600: '#185FA5', 800: '#0C447C' } } } }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6 font-sans antialiased">

<div class="w-full max-w-md">

    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-brand-600 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">M</div>
        <h1 class="text-2xl font-bold text-slate-800">MediConnect</h1>
        <p class="text-slate-400 text-sm mt-1">Buat akun pasien baru</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-7 shadow-sm">

        <div id="error-msg" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2.5 rounded-lg mb-4"></div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Nama Lengkap</label>
                <input id="name" type="text" placeholder="Nama lengkap Anda" autocomplete="name"
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                    <input id="email" type="email" placeholder="email@contoh.com"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">No. HP</label>
                    <input id="no_hp" type="tel" placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Password</label>
                    <input id="password" type="password" placeholder="Min. 8 karakter"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Konfirmasi</label>
                    <input id="password_confirmation" type="password" placeholder="Ulangi password"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/10 transition"/>
                </div>
            </div>

            <button onclick="doRegister()"
                class="w-full bg-brand-600 hover:bg-brand-800 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                Buat Akun
            </button>
        </div>

        <p class="text-center text-sm text-slate-400 mt-5">
            Sudah punya akun?
            <a href="/login" class="text-brand-600 font-medium hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>

<script>
    async function doRegister() {
        var errDiv = document.getElementById('error-msg');
        errDiv.classList.add('hidden');

        var payload = {
            name:                  document.getElementById('name').value.trim(),
            email:                 document.getElementById('email').value.trim(),
            no_hp:                 document.getElementById('no_hp').value.trim(),
            password:              document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
        };

        if (!payload.name || !payload.email || !payload.password) {
            errDiv.textContent = 'Nama, email, dan password wajib diisi.';
            errDiv.classList.remove('hidden'); return;
        }

        try {
            var res  = await fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            var data = await res.json();

            if (!data.success) {
                var msgs = data.errors
                    ? Object.values(data.errors).flat().join(', ')
                    : (data.message || 'Registrasi gagal.');
                errDiv.textContent = msgs;
                errDiv.classList.remove('hidden'); return;
            }

            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('auth_user',  JSON.stringify(data.user));
            window.location.href = '/pasien';

        } catch (e) {
            errDiv.textContent = 'Gagal terhubung ke server.';
            errDiv.classList.remove('hidden');
        }
    }
</script>
</body>
</html>