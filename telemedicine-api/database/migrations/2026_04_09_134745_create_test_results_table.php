<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('test_results', function (Blueprint $table) {
        $table->id();
        $table->string('skenario');           // nama skenario
        $table->string('kondisi_jaringan');   // offline/slow-3g/online
        $table->integer('durasi_offline_detik')->nullable(); // berapa lama offline
        $table->integer('waktu_sync_ms')->nullable();        // waktu sync berhasil
        $table->enum('hasil', ['success', 'conflict', 'failed']);
        $table->integer('data_pending')->default(0);         // jumlah data pending
        $table->integer('data_synced')->default(0);          // jumlah berhasil sync
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
