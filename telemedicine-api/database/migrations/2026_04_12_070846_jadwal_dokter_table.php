<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('users')->cascadeOnDelete();
            $table->enum('hari', ['senin','selasa','rabu','kamis','jumat','sabtu','minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            // Satu dokter tidak bisa punya jadwal duplikat di hari & jam yang sama
            $table->unique(['dokter_id', 'hari', 'jam_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_dokter');
    }
};