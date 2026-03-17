<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users (untuk login)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Relasi ke tabel kelas (wadah siswa)
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            
            $table->string('nisn')->unique();
            $table->string('nama');
            $table->timestamps();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
