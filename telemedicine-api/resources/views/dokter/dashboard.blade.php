@extends('layouts.app')
@section('title', 'Dashboard Dokter')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-800">Dashboard Dokter</h1>
        <p class="text-gray-500 text-sm">Daftar konsultasi masuk</p>
    </div>

    <div id="konsultasi-list" class="space-y-4">
        <p class="text-sm text-gray-400">Memuat data...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
// const token = localStorage.getItem('auth_token');
// const user  = JSON.parse(localStorage.getItem('auth_user') || 'null');
if (!token || !user) window.location.href = '/login';
if (user && user.role === 'pasien') window.location.href = '/pasien';

const API = 'http://localhost:8000/api';

async function loadKonsultasi() {
    const list = document.getElementById('konsultasi-list');
    try {
        const res  = await fetch(`${API}/dokter/konsultasi`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();

        if (data.length === 0) {
            list.innerHTML = '<p class="text-sm text-gray-400 italic">Belum ada konsultasi masuk</p>';
            return;
        }

        list.innerHTML = data.map(item => `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-medium text-gray-800">${item.nama_pasien}</p>
                        <p class="text-xs text-gray-400">${new Date(item.created_at).toLocaleString('id-ID')}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full ${
                        item.status === 'done'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-blue-100 text-blue-700'
                    }">${item.status}</span>
                </div>

                <div class="bg-gray-50 rounded-lg px-4 py-3 mb-3">
                    <p class="text-xs text-gray-500 mb-1">Keluhan:</p>
                    <p class="text-sm text-gray-700">${item.keluhan}</p>
                </div>

                ${item.jawaban
                    ? `<div class="bg-green-50 rounded-lg px-4 py-3">
                        <p class="text-xs text-green-600 font-medium mb-1">Jawaban Anda:</p>
                        <p class="text-sm text-gray-700">${item.jawaban}</p>
                       </div>`
                    : `<div class="space-y-2">
                        <textarea id="jawaban-${item.id}" rows="3"
                            placeholder="Tulis jawaban/saran medis..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        <button onclick="kirimJawaban(${item.id})"
                            class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            Kirim Jawaban
                        </button>
                       </div>`
                }
            </div>
        `).join('');

    } catch (err) {
        list.innerHTML = '<p class="text-sm text-red-400">Gagal memuat data</p>';
    }
}

async function kirimJawaban(id) {
    const jawaban = document.getElementById(`jawaban-${id}`).value.trim();
    if (!jawaban) return alert('Jawaban tidak boleh kosong');

    try {
        const res = await fetch(`${API}/dokter/konsultasi/${id}/jawab`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ jawaban })
        });

        if (res.ok) {
            await loadKonsultasi(); // refresh list
        }
    } catch (err) {
        alert('Gagal mengirim jawaban');
    }
}

loadKonsultasi();
</script>
@endsection