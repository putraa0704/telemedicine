const express = require('express');
const cors    = require('cors');
const path    = require('path');

const app = express();
app.use(cors());
app.use(express.json());

// Serve file statis (index.html, sw.js, dll)
app.use(express.static(path.join(__dirname)));

// Storage sementara (nanti diganti PostgreSQL/MySQL)
const konsultasiDB = [];
let   autoIncrement = 1;

// POST /api/konsultasi — terima data dari client
app.post('/api/konsultasi', (req, res) => {
  const { local_id, nama, keluhan, created_at } = req.body;

  // Simulasi delay jaringan
  setTimeout(() => {
    const record = {
      server_id:  autoIncrement++,
      local_id,
      nama,
      keluhan,
      created_at,
      received_at: new Date().toISOString()
    };

    konsultasiDB.push(record);
    console.log('[Server] Received:', record);

    res.json({
      success:   true,
      server_id: record.server_id,
      message:   'Konsultasi berhasil disimpan'
    });
  }, 500); // 500ms delay simulasi network
});

// GET /api/konsultasi — lihat semua data di server
app.get('/api/konsultasi', (req, res) => {
  res.json(konsultasiDB);
});

app.listen(3000, () => {
  console.log('[Server] Running at http://localhost:3000');
});