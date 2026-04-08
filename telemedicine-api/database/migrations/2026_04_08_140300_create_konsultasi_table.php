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
    Schema::create('konsultasi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('local_id')->unique(); // UUID dari IndexedDB
        $table->string('nama');
        $table->text('keluhan');
        $table->enum('status', ['received', 'in_review', 'done'])->default('received');
        $table->string('jawaban_dokter')->nullable();
        $table->timestamp('client_created_at')->nullable(); // waktu dibuat di client
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};
