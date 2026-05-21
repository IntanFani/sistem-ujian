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
        // 1. Tambah index exam_id pada tabel questions karena sering dicari/eager load
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'exam_id')) {
                $table->index('exam_id');
            }
        });

        // 2. Tambah composite index pada exam_sessions untuk mempercepat pencarian session aktif/riwayat
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->index(['exam_id', 'user_id']);
            $table->index(['user_id', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'exam_id')) {
                $table->dropIndex(['exam_id']);
            }
        });

        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropIndex(['exam_id', 'user_id']);
            $table->dropIndex(['user_id', 'completed_at']);
        });
    }
};
