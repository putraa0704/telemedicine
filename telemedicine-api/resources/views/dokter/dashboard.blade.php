@extends('layouts.app')
@section('title', 'Dashboard Dokter')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="card p-6" style="background:linear-gradient(135deg,#0284c7,#1d4ed8)">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white text-xl font-bold" id="doc-initial">D</div>
            <div>
                <p class="text-white/80 text-sm">Dashboard Dokter</p>
                <h1 class="text-white text-xl font-bold" id="doc-name">Dokter</h1>
            </div>
            <div class="ml-auto">
                <div class="bg-white/20 rounded-xl px-4 py-2 text-center">
                    <p class="text-white/70 text-xs">Konsultasi</p>
                    <p class="text-white text-xl font-bold" id="total-count">-</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter tabs --}}
    <div class="flex gap-2 bg-white rounded-xl p-1 card">
        <button onclick="filterKonsultasi('all')" id="filter-all"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition filter-active">
            Semua
        </button>
        <button onclick="filterKonsultasi('received')" id="filter-received"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition text-slate-500 hover:text-slate-700">
            Belum Dijawab
        </button>
        <button onclick="filterKonsultasi('done')" id="filter-done"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition text-slate-500 hover:text-slate-700">
            Selesai
        </button>
    </div>

    {{-- List --}}
    <div id="konsultasi-list" class="space-y-4">
        <div class="card p-8 text-center text-slate-400 text-sm">Memuat data konsultasi...</div>
    </div>

</div>

<style>
.filter-active { background: linear-gradient(135deg,#0284c7,#1d4ed8); color: white; }
</style>
@endsection

@section('scripts')
<script>
if (!token || !user) window.location.href = '/login';
if (user && user.role === 'pasien') window.location.href = '/pasien';

if (user) {
    document.getElementById('doc-name').textContent = user.name;
    document.getElementById('doc-initial').textContent = user.name.charAt(0).toUpperCase();
}

const API = 'http://localhost:8000/api';
let allKonsultasi = [];
let currentFilter = 'all';

async function loadKonsultasi() {
    try {
        const res  = await fetch(`${API}/dokter/konsultasi`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        allKonsultasi = await res.json();
        document.getElementById('total-count').textContent = allKonsultasi.length;
        renderKonsultasi();
    } catch (err) {
        document.getElementById('konsultasi-list').innerHTML =
            '<div class="card p-8 text-center text-red-400 text-sm">Gagal memuat data</div>';
    }
}

function filterKonsultasi(filter) {
    currentFilter = filter;
    ['all','received','done'].forEach(f => {
        const btn = document.getElementById(`filter-${f}`);
        btn.classList.remove('filter-active');
        btn.classList.add('text-slate-500');
    });
    const activeBtn = document.getElementById(`filter-${filter}`);
    activeBtn.classList.add('filter-active');
    activeBtn.classList.remove('text-slate-500');
    renderKonsultasi();
}

function renderKonsultasi() {
    const list = document.getElementById('konsultasi-list');
    const data = currentFilter === 'all'
        ? allKonsultasi
        : allKonsultasi.filter(k => k.status === currentFilter);

    if (data.length === 0) {
        list.innerHTML = `
            <div class="card p-10 text-center">
                <p class="text-4xl mb-3">${currentFilter === 'done' ? '✅' : '📭'}</p>
                <p class="text-slate-500 text-sm">${currentFilter === 'done' ? 'Belum ada konsultasi selesai' : 'Tidak ada konsultasi'}</p>
            </div>`;
        return;
    }

    list.innerHTML = data.map(item => `
        <div class="card p-5">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#0d9488,#0284c7)">
                        ${item.nama_pasien.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700">${item.nama_pasien}</p>
                        <p class="text-xs text-slate-400">${new Date(item.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'})}</p>
                    </div>
                </div>
                <span class="badge-${item.status}">${item.status === 'done' ? 'Selesai' : 'Menunggu'}</span>
            </div>

            <div class="bg-slate-50 rounded-xl px-4 py-3 mb-4">
                <p class="text-xs text-slate-400 mb-1 font-medium">KELUHAN PASIEN</p>
                <p class="text-sm text-slate-700 leading-relaxed">${item.keluhan}</p>
            </div>

            ${item.jawaban ? `
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <p class="text-xs text-blue-600 mb-1 font-medium">💬 JAWABAN ANDA</p>
                    <p class="text-sm text-slate-700">${item.jawaban}</p>
                    <p class="text-xs text-slate-400 mt-2">${item.dijawab_at ? new Date(item.dijawab_at).toLocaleString('id-ID') : ''}</p>
                </div>
            ` : `
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Tulis jawaban / saran medis</label>
                        <textarea id="jawaban-${item.id}" rows="3"
                            placeholder="Berikan diagnosis atau saran kesehatan yang tepat..."
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition resize-none"></textarea>
                    </div>
                    <button onclick="kirimJawaban(${item.id})"
                        class="text-white text-sm font-medium px-5 py-2.5 rounded-xl transition hover:opacity-90 active:scale-95 flex items-center gap-2"
                        style="background:linear-gradient(135deg,#0284c7,#1d4ed8)">
                        <span>Kirim Jawaban</span>
                    </button>
                </div>
            `}
        </div>
    `).join('');
}

async function kirimJawaban(id) {
    const jawaban = document.getElementById(`jawaban-${id}`).value.trim();
    if (!jawaban) { alert('Jawaban tidak boleh kosong'); return; }

    try {
        const res = await fetch(`${API}/dokter/konsultasi/${id}/jawab`, {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ jawaban })
        });
        if (res.ok) await loadKonsultasi();
    } catch (err) {
        alert('Gagal mengirim jawaban');
    }
}

loadKonsultasi();
setInterval(loadKonsultasi, 30000); // auto refresh tiap 30 detik
</script>
@endsection