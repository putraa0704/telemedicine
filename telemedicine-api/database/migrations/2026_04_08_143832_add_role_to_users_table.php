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
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['pasien', 'dokter', 'admin'])->default('pasien')->after('email');
        $table->string('no_hp')->nullable()->after('role');
        $table->text('alamat')->nullable()->after('no_hp');
        // Khusus dokter
        $table->string('spesialisasi')->nullable()->after('alamat');
        $table->string('no_str')->nullable()->after('spesialisasi'); // nomor izin dokter
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'no_hp', 'alamat', 'spesialisasi', 'no_str']);
    });
}
};
