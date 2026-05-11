<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UjicobaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Mata Pelajaran
        $subject = Subject::firstOrCreate(
            ['name' => 'Pemrograman Web'],
            ['name' => 'Pemrograman Web']
        );

        // 2. Buat User Guru
        $userGuru = User::firstOrCreate(
            ['email' => 'guru@ujian.com'],
            [
                'name' => 'Bapak Guru Web',
                'password' => Hash::make('password'), // password: password
                'role' => 'guru',
            ]
        );

        $guru = Guru::firstOrCreate(
            ['user_id' => $userGuru->id],
            [
                'subject_id' => $subject->id,
                'nip' => '198001012005011003',
                'nama' => 'Bapak Guru Web',
            ]
        );

        // 3. Buat Kelas
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'XII RPL 1'],
            [
                'guru_id' => $guru->id,
            ]
        );

        // 4. Buat User Siswa (Buat 3 Siswa untuk ujicoba)
        for ($i = 1; $i <= 3; $i++) {
            $userSiswa = User::firstOrCreate(
                ['email' => "siswa{$i}@ujian.com"],
                [
                    'name' => "Siswa Ujicoba {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                ]
            );

            Siswa::firstOrCreate(
                ['user_id' => $userSiswa->id],
                [
                    'kelas_id' => $kelas->id,
                    'nisn' => '005123456' . $i,
                    'nama' => "Siswa Ujicoba {$i}",
                    'password_text' => 'password',
                ]
            );
        }

        // 5. Buat Ujian (Exam)
        $exam = Exam::firstOrCreate(
            ['title' => 'Ujian Akhir Semester Web'],
            [
                'subject_id' => $subject->id,
                'kelas_id' => $kelas->id,
                'guru_id' => $guru->id,
                'duration' => 60, // 60 menit
                'start_time' => Carbon::now()->subMinutes(10), // Bisa dikerjakan sekarang
                'end_time' => Carbon::now()->addDays(1),
                'token' => 'UJI123'
            ]
        );

        // 6. Buat Soal Pilihan Ganda
        $q1 = Question::firstOrCreate(
            ['question_text' => 'Apa kepanjangan dari HTML?'],
            [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'guru_id' => $guru->id,
                'jenis_soal' => 'pilihan_ganda',
                'opsi_a' => 'Hyper Text Markup Language',
                'opsi_b' => 'High Text Markup Language',
                'opsi_c' => 'Hyper Tabular Markup Language',
                'opsi_d' => 'None of these',
                'opsi_e' => 'Hyper Tool Multi Language',
                'jawaban_benar' => 'a'
            ]
        );

        $q2 = Question::firstOrCreate(
            ['question_text' => 'Apa tag HTML untuk membuat teks tebal?'],
            [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'guru_id' => $guru->id,
                'jenis_soal' => 'pilihan_ganda',
                'opsi_a' => '<italic>',
                'opsi_b' => '<bold>',
                'opsi_c' => '<b>',
                'opsi_d' => '<strong>',
                'opsi_e' => 'Keduanya C dan D',
                'jawaban_benar' => 'e'
            ]
        );

        // 7. Buat Soal Benar Salah
        $q3 = Question::firstOrCreate(
            ['question_text' => 'CSS digunakan untuk mengatur tampilan web.'],
            [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'guru_id' => $guru->id,
                'jenis_soal' => 'benar_salah',
                'opsi_a' => 'Benar',
                'opsi_b' => 'Salah',
                'jawaban_benar' => 'a'
            ]
        );

        // 8. Buat Soal Essay
        $q4 = Question::firstOrCreate(
            ['question_text' => 'Jelaskan fungsi dari JavaScript dalam sebuah website, serta berikan satu contoh penerapannya!'],
            [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'guru_id' => $guru->id,
                'jenis_soal' => 'essay',
            ]
        );

        // Pivot table tidak digunakan karena Exam memiliki relasi hasMany ke Question.
    }
}
