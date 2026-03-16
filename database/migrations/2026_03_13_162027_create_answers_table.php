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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->onDelete('cascade'); // Hubungkan ke sesi
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->char('jawaban_siswa', 1);
            $table->boolean('is_correct');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
