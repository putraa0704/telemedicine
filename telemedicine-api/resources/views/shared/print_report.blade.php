<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Konsultasi #KSL-{{ str_pad($id, 3, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-container { box-shadow: none !important; padding: 0 !important; background: transparent !important; }
            body { background: white !important; }
        }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .page-container {
            max-width: 800px; margin: 0 auto; background: white; padding: 40px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); min-height: 100vh;
        }
    </style>
</head>
<body class="text-slate-800">

<div class="no-print text-center py-4 bg-brand-600 text-white font-semibold">
    Menyiapkan dokumen untuk dicetak...
</div>

<div class="page-container" id="report-content" style="display: none;">
    <!-- Header -->
    <div class="border-b-2 border-slate-200 pb-6 mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-brand-700">CareMate Telemedicine</h1>
            <p class="text-sm text-slate-500 mt-1">Laporan Medis & Rekam Konsultasi</p>
        </div>
        <div class="text-right">
            <div class="text-sm font-semibold">ID Konsultasi: #KSL-<span id="lbl-id">{{ str_pad($id, 3, '0', STR_PAD_LEFT) }}</span></div>
            <div class="text-xs text-slate-500 mt-1">Dicetak pada: <span id="lbl-print-date"></span></div>
        </div>
    </div>

    <!-- Info Pasien & Dokter -->
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Informasi Pasien</h3>
            <div class="font-semibold text-slate-800" id="val-pasien-nama">-</div>
        </div>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Informasi Dokter</h3>
            <div class="font-semibold text-slate-800" id="val-dokter-nama">-</div>
            <div class="text-xs text-slate-500" id="val-dokter-spesialis">-</div>
        </div>
    </div>

    <!-- Keluhan Utama -->
    <div class="mb-8">
        <h3 class="text-sm font-bold text-brand-700 mb-2 border-b border-brand-100 pb-2">Keluhan Utama (Diagnosis Awal)</h3>
        <p class="text-sm text-slate-700 leading-relaxed" id="val-keluhan">-</p>
    </div>

    <!-- Transkrip Chat -->
    <div>
        <h3 class="text-sm font-bold text-brand-700 mb-4 border-b border-brand-100 pb-2">Transkrip Percakapan</h3>
        <div id="chat-transcript" class="space-y-4">
            <!-- Messages go here -->
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-12 pt-6 border-t border-slate-200 text-center text-xs text-slate-400">
        <p>Dokumen ini dicetak secara otomatis dari sistem CareMate Telemedicine.</p>
        <p>Harap simpan dokumen ini sebagai bukti konsultasi digital Anda.</p>
    </div>
</div>

<script>
    var token = localStorage.getItem('auth_token');
    var user  = JSON.parse(localStorage.getItem('auth_user') || '{}');
    var targetId = {{ $id }};
    
    if (!token) {
        alert("Sesi tidak valid, silakan login kembali.");
        window.close();
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDate(s) {
        if (!s) return '-';
        return new Date(s).toLocaleString('id-ID');
    }

    document.getElementById('lbl-print-date').textContent = new Date().toLocaleString('id-ID');

    async function loadData() {
        try {
            var endpoint = user.role === 'dokter' || user.role === 'admin' ? '/api/dokter/konsultasi' : '/api/konsultasi/saya';
            var res = await fetch(endpoint + '?_t=' + new Date().getTime(), { headers: { 'Authorization': 'Bearer ' + token } });
            var allData = await res.json();
            
            var item = allData.find(d => String(d.id) === String(targetId));
            if (!item) {
                alert("Data konsultasi tidak ditemukan.");
                window.close();
                return;
            }

            // Populate Info
            var pasName = item.nama_pasien || item.nama || 'Pasien';
            var drName = item.dokter ? item.dokter.name : 'Dokter';
            var drSpes = item.dokter ? item.dokter.spesialisasi : 'Umum';

            document.getElementById('val-pasien-nama').textContent = escapeHtml(pasName);
            document.getElementById('val-dokter-nama').textContent = escapeHtml(drName);
            document.getElementById('val-dokter-spesialis').textContent = escapeHtml(drSpes);
            document.getElementById('val-keluhan').textContent = escapeHtml(item.keluhan || '-');

            // Load Chat Messages
            var msgRes = await fetch('/api/konsultasi/' + targetId + '/messages?_t=' + new Date().getTime(), { headers: { 'Authorization': 'Bearer ' + token } });
            var messages = [];
            if(msgRes.ok) messages = await msgRes.json();

            var transcript = document.getElementById('chat-transcript');
            if (messages.length === 0) {
                transcript.innerHTML = '<div class="text-sm text-slate-500 italic">Tidak ada percakapan terekam.</div>';
            } else {
                transcript.innerHTML = messages.map(function(msg) {
                    var senderName = msg.sender_role === 'pasien' ? pasName : drName;
                    var senderColor = msg.sender_role === 'pasien' ? 'text-slate-800' : 'text-brand-700';
                    return '<div class="text-sm border-l-2 border-slate-100 pl-3 mb-3">' +
                        '<div class="flex items-center gap-2 mb-1">' +
                            '<span class="font-bold ' + senderColor + '">' + escapeHtml(senderName) + '</span>' +
                            '<span class="text-[10px] text-slate-400">' + formatDate(msg.created_at) + '</span>' +
                        '</div>' +
                        '<div class="text-slate-700">' + escapeHtml(msg.message) + '</div>' +
                    '</div>';
                }).join('');
            }

            document.getElementById('report-content').style.display = 'block';
            document.querySelector('.no-print').style.display = 'none';

            // Wait a bit for render
            setTimeout(function() {
                window.print();
            }, 500);

        } catch (e) {
            console.error(e);
            alert("Terjadi kesalahan saat memuat data report.");
        }
    }

    loadData();
</script>
</body>
</html>
