<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Taruh guru_id setelah kelas_id
            $table->foreignId('guru_id')->after('kelas_id')->constrained()->onDelete('cascade');
            // Taruh token setelah end_time
            $table->string('token', 6)->after('end_time')->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['guru_id', 'token']);
        });
    }

};
