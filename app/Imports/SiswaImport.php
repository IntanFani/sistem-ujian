<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Kita cari kelas berdasarkan nama kelas yang diketik di Excel
            // Contoh di excel kolom kelas isinya "VII A"
            $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();

            // Jika kelas ditemukan, proses datanya
            if ($kelas) {
                // 1. Buat data User (Akun Login)
                $user = User::create([
                    'name'     => $row['nama'],
                    'email'    => $row['email'],
                    // Password default pakai NISN seperti fungsi tambah manualmu
                    'password' => Hash::make($row['nisn']), 
                    // Pastikan nama kolom role-nya benar (sesuaikan jika di db beda)
                    'role'     => 'siswa', 
                ]);

                // 2. Buat data Siswa yang terhubung ke User dan Kelas
                Siswa::create([
                    'user_id'  => $user->id,
                    'kelas_id' => $kelas->id,
                    'nisn'     => $row['nisn'],
                    'nama'     => $row['nama'],
                ]);
            }
        }
    }
}