@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-2xl font-semibold text-gray-800 mb-1">Masuk</h1>
        <p class="text-gray-500 text-sm mb-6">Masuk ke akun telemedicine Anda</p>

        <div id="error-msg" class="hidden bg-red-50 text-red-600 text-sm px-4 py-3 rounded-lg mb-4"></div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" type="email" placeholder="email@example.com"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" type="password" placeholder="••••••••"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <button onclick="doLogin()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Masuk
            </button>
        </div>

        <p class="text-center text-sm text-gray-500 mt-4">
            Belum punya akun? <a href="/register" class="text-blue-500 hover:underline">Daftar</a>
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
            errDiv.textContent = data.message || 'Login gagal';
            errDiv.classList.remove('hidden');
            return;
        }

        // Simpan token & user
        localStorage.setItem('auth_token', data.token);
        localStorage.setItem('auth_user', JSON.stringify(data.user));

        // Redirect berdasarkan role
        if (data.role === 'dokter' || data.role === 'admin') {
            window.location.href = '/dokter';
        } else {
            window.location.href = '/pasien';
        }

    } catch (err) {
        errDiv.textContent = 'Gagal terhubung ke server';
        errDiv.classList.remove('hidden');
    }
}

// Enter key trigger login
document.addEventListener('keydown', e => {
    if (e.key === 'Enter') doLogin();
});


// Redirect kalau sudah login
const existingToken = localStorage.getItem('auth_token');
const existingUser  = JSON.parse(localStorage.getItem('auth_user') || 'null');
if (existingToken && existingUser) {
    window.location.href = existingUser.role === 'dokter' ? '/dokter' : '/pasien';
}
</script>
@endsection