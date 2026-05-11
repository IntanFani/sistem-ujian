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
        Schema::table('exam_answers', function (Blueprint $table) {
            // Ubah tipe data answer dari string(1) menjadi text agar bisa menyimpan essay
            $table->text('answer')->nullable()->change();
            
            // Tambahkan composite index untuk mempercepat pencarian dan updateOrCreate
            $table->index(['exam_session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            // Karena ini rollback, kita kembalikan ke string(1), tapi berisiko data terpotong.
            $table->string('answer', 1)->change();
            
            $table->dropIndex(['exam_session_id', 'question_id']);
        });
    }
};
