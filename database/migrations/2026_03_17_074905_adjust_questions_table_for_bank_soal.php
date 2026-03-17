<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // 1. Hapus exam_id karena kita mau bikin Bank Soal (Mapel-Sentris)
            // Pastikan foreign key-nya dihapus dulu sebelum kolomnya
            $table->dropForeign(['exam_id']); 
            $table->dropColumn('exam_id');

            // 2. Tambah subject_id dan guru_id (setelah ID agar rapi)
            $table->foreignId('subject_id')->after('id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('guru_id')->after('subject_id')->constrained('gurus')->onDelete('cascade');
            
            // 3. Tambah kolom gambar (opsional)
            $table->string('gambar')->nullable()->after('question_text');

            // 4. Tambah kolom opsi A sampai E
            $table->text('opsi_a')->after('gambar');
            $table->text('opsi_b')->after('opsi_a');
            $table->text('opsi_c')->after('opsi_b');
            $table->text('opsi_d')->after('opsi_c');
            $table->text('opsi_e')->after('opsi_d');

            // 5. Kunci Jawaban
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd', 'e'])->after('opsi_e');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Untuk membatalkan migrasi (rollback)
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['guru_id']);
            
            $table->dropColumn([
                'subject_id', 'guru_id', 'gambar', 
                'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 
                'jawaban_benar'
            ]);

            // Kembalikan exam_id (jika perlu)
            $table->foreignId('exam_id')->nullable()->constrained('exams');
        });
    }
};