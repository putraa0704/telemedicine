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
    Schema::table('konsultasi', function (Blueprint $table) {
        $table->foreignId('dokter_id')->nullable()->after('user_id')
              ->constrained('users')->nullOnDelete();
        $table->timestamp('dijawab_at')->nullable()->after('jawaban_dokter');
    });
}

public function down(): void
{
    Schema::table('konsultasi', function (Blueprint $table) {
        $table->dropForeign(['dokter_id']);
        $table->dropColumn(['dokter_id', 'dijawab_at']);
    });
}
};
