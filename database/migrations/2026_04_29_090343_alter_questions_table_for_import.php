<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // 1. Tambah kolom jenis_soal setelah exam_id
            $table->string('jenis_soal')->default('pilihan_ganda')->after('exam_id');

            // 2. Ubah kolom opsi menjadi boleh kosong (nullable)
            $table->text('opsi_a')->nullable()->change();
            $table->text('opsi_b')->nullable()->change();
            $table->text('opsi_c')->nullable()->change();
            $table->text('opsi_d')->nullable()->change();
            $table->text('opsi_e')->nullable()->change();

            // 3. Ubah jawaban_benar dari enum menjadi string dan boleh kosong
            $table->string('jawaban_benar')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Rollback jika terjadi kesalahan (kembalikan ke aturan awal)
            $table->dropColumn('jenis_soal');
            
            $table->text('opsi_a')->nullable(false)->change();
            $table->text('opsi_b')->nullable(false)->change();
            $table->text('opsi_c')->nullable(false)->change();
            $table->text('opsi_d')->nullable(false)->change();
            $table->text('opsi_e')->nullable(false)->change();
            
            // Catatan: Mengembalikan ke enum murni mungkin butuh raw SQL tergantung versi database
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd', 'e'])->nullable(false)->change();
        });
    }
};