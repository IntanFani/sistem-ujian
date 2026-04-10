<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas; // Sesuaikan dengan nama Model Kelas kamu
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// Gunakan ToCollection agar kita bisa membungkusnya dengan DB::transaction
class SiswaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            
            // 1. Skip data kalau NISN kosong atau sudah ada di database (biar nggak error duplicate)
            if (empty($row['nisn']) || Siswa::where('nisn', $row['nisn'])->exists()) {
                continue; 
            }

            DB::transaction(function () use ($row) {
                // 2. Generate Password (bisa Str::random(6) atau samakan dengan NISN)
                $generatedPassword = Str::random(6); 
                // $generatedPassword = $row['nisn']; // Pakai ini kalau mau password default-nya NISN

                // 3. Buat Akun User
                $user = User::create([
                    'name'     => $row['nama'],
                    'email'    => $row['nisn'] . '@cbt.com', // Email dummy dari NISN
                    'password' => Hash::make($generatedPassword),
                    'role'     => 'siswa' // Sesuaikan dengan role di sistemmu
                ]);

                // 4. Cari ID Kelas berdasarkan nama kelas di Excel
                // Asumsi di excel ada kolom 'kelas' yang isinya teks misal: "X RPL 1"
                $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();
                $kelasId = $kelas ? $kelas->id : null; 

                // 5. Simpan Data Siswa
                Siswa::create([
                    'user_id'       => $user->id,
                    'kelas_id'      => $kelasId,
                    'nisn'          => $row['nisn'],
                    'nama'          => $row['nama'],
                    'password_text' => $generatedPassword, // Simpan untuk dicetak
                ]);
            });
        }
    }
}