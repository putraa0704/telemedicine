@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8">
<div class="w-full max-w-md">

    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold" style="background:linear-gradient(135deg,#0d9488,#0284c7)">T</div>
        <h1 class="text-2xl font-bold text-slate-800">Buat Akun Baru</h1>
        <p class="text-slate-500 text-sm mt-1">Daftar sebagai pasien telemedicine</p>
    </div>

    <div class="card p-8">
        <div id="error-msg" class="hidden bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl mb-5"></div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                <input id="name" type="text" placeholder="Nama lengkap Anda"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input id="email" type="email" placeholder="email@example.com"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP</label>
                <input id="no_hp" type="tel" placeholder="08xxxxxxxxxx"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <input id="password" type="password" placeholder="Minimal 8 karakter"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" placeholder="Ulangi password"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <button onclick="doRegister()"
                class="w-full text-white font-medium py-3 rounded-xl text-sm transition hover:opacity-90 active:scale-95"
                style="background:linear-gradient(135deg,#0d9488,#0284c7)">
                Buat Akun
            </button>
        </div>

        <p class="text-center text-sm text-slate-500 mt-5">
            Sudah punya akun?
            <a href="/login" class="text-teal-600 font-medium hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
async function doRegister() {
    const errDiv = document.getElementById('error-msg');
    errDiv.classList.add('hidden');

    const payload = {
        name:                  document.getElementById('name').value,
        email:                 document.getElementById('email').value,
        no_hp:                 document.getElementById('no_hp').value,
        password:              document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value,
    };

    try {
        const res  = await fetch('http://localhost:8000/api/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (!data.success) {
            const msgs = data.errors
                ? Object.values(data.errors).flat().join(', ')
                : (data.message || 'Registrasi gagal');
            errDiv.textContent = msgs;
            errDiv.classList.remove('hidden');
            return;
        }

        localStorage.setItem('auth_token', data.token);
        localStorage.setItem('auth_user', JSON.stringify(data.user));
        window.location.href = '/pasien';

    } catch (err) {
        errDiv.textContent = 'Gagal terhubung ke server';
        errDiv.classList.remove('hidden');
    }
}
</script>
@endsection