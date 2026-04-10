@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold" style="background:linear-gradient(135deg,#0d9488,#0284c7)">T</div>
        <h1 class="text-2xl font-bold text-slate-800">Selamat Datang</h1>
        <p class="text-slate-500 text-sm mt-1">Masuk ke akun telemedicine Anda</p>
    </div>

    <div class="card p-8">
        <div id="error-msg" class="hidden bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl mb-5"></div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input id="email" type="email" placeholder="email@example.com"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <input id="password" type="password" placeholder="••••••••"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition"/>
            </div>
            <button onclick="doLogin()"
                class="w-full text-white font-medium py-3 rounded-xl text-sm transition hover:opacity-90 active:scale-95"
                style="background:linear-gradient(135deg,#0d9488,#0284c7)">
                Masuk
            </button>
        </div>

        <p class="text-center text-sm text-slate-500 mt-5">
            Belum punya akun?
            <a href="/register" class="text-teal-600 font-medium hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">
        🔒 Data Anda terlindungi dengan enkripsi
    </p>
</div>
</div>
@endsection

@section('scripts')
<script>
async function doLogin() {
    const email    = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errDiv   = document.getElementById('error-msg');
    errDiv.classList.add('hidden');

    try {
        const res  = await fetch('http://localhost:8000/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await res.json();

        if (!data.success) {
            errDiv.textContent = data.message || 'Email atau password salah';
            errDiv.classList.remove('hidden');
            return;
        }

        localStorage.setItem('auth_token', data.token);
        localStorage.setItem('auth_user', JSON.stringify(data.user));
        window.location.href = data.role === 'dokter' || data.role === 'admin' ? '/dokter' : '/pasien';

    } catch (err) {
        errDiv.textContent = 'Gagal terhubung ke server';
        errDiv.classList.remove('hidden');
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); });

const existingToken = localStorage.getItem('auth_token');
const existingUser  = JSON.parse(localStorage.getItem('auth_user') || 'null');
if (existingToken && existingUser) {
    window.location.href = existingUser.role === 'dokter' ? '/dokter' : '/pasien';
}
</script>
@endsection