@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-2xl font-semibold text-gray-800 mb-1">Daftar Akun</h1>
        <p class="text-gray-500 text-sm mb-6">Buat akun pasien baru</p>

        <div id="error-msg" class="hidden bg-red-50 text-red-600 text-sm px-4 py-3 rounded-lg mb-4"></div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input id="name" type="text" placeholder="Nama lengkap"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" type="email" placeholder="email@example.com"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input id="no_hp" type="tel" placeholder="08xxxxxxxxxx"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" type="password" placeholder="Min. 8 karakter"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" placeholder="Ulangi password"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <button onclick="doRegister()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Daftar
            </button>
        </div>

        <p class="text-center text-sm text-gray-500 mt-4">
            Sudah punya akun? <a href="/login" class="text-blue-500 hover:underline">Masuk</a>
        </p>
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