<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users (untuk login)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Relasi ke tabel subjects (untuk mata pelajaran yang diampu)
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            
            $table->string('nip')->unique();
            $table->string('nama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
