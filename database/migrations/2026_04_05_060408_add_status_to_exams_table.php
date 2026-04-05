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
        Schema::table('exams', function (Blueprint $table) {
            // Hanya menambahkan kolom status dengan nilai default 'nonaktif'
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif')->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('status');
        });
    }
};