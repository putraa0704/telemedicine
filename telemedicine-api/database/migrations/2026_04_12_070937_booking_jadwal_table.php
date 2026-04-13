<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal_dokter')->cascadeOnDelete();
            $table->foreignId('pasien_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('konsultasi_id')->nullable()->constrained('konsultasi')->nullOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['booked', 'selesai', 'dibatalkan'])->default('booked');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Satu slot per tanggal hanya bisa dibooking sekali
            $table->unique(['jadwal_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_jadwal');
    }
};