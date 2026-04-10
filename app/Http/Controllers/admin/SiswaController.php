<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiswaController extends Controller
{

    // 1. Fungsi Index (Sesuai dengan kode asli kamu)
    public function index(Request $request)
    {
        // Mengambil data kelas untuk dropdown filter dan form
        $kelases = Kelas::all();

        // Query data siswa dengan filter dinamis
        $query = Siswa::with(['kelas', 'user'])->latest();

        // Jika ada request filter 'kelas' di URL, jalankan filter ini
        if ($request->filled('kelas')) {
            $query->where('kelas_id', $request->kelas);
        }

        $siswas = $query->get();

        return view('admin.siswas.index', compact('siswas', 'kelases'));
    }
    // 2. Fungsi Proses Naik Kelas
    public function prosesNaikKelas(Request $request)
    {
        // Validasi input
        $request->validate([
            'kelas_asal' => 'required|exists:kelas,id',
            'kelas_tujuan' => 'required|exists:kelas,id|different:kelas_asal',
        ], [
            'kelas_asal.required' => 'Pilih kelas asal terlebih dahulu!',
            'kelas_tujuan.required' => 'Pilih kelas tujuan terlebih dahulu!',
            'kelas_tujuan.different' => 'Kelas tujuan tidak boleh sama dengan kelas asal!'
        ]);

        // Proses update massal berdasarkan kelas_id
        $jumlahSiswaDipindah = Siswa::where('kelas_id', $request->kelas_asal)
            ->update(['kelas_id' => $request->kelas_tujuan]);

        // Cek apakah ada data yang berhasil diupdate
        if ($jumlahSiswaDipindah > 0) {
            return redirect()->back()->with('success', "$jumlahSiswaDipindah siswa berhasil dipindah ke kelas baru!");
        }

        return redirect()->back()->with('error', 'Gagal dipindah! Tidak ada siswa di kelas asal yang dipilih.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn'     => 'required|unique:siswas,nisn',
            'nama'     => 'required',
            'kelas_id' => 'required|exists:kelas,id', // Sesuaikan nama tabel kelas kamu
            'email'    => 'required|email|unique:users,email', // Validasi email nyala lagi
        ]);

        // 1. Generate Password Acak 6 Karakter (Kombinasi huruf & angka)
        $generatedPassword = Str::random(6);
        // Atau kalau mau password default-nya angka acak: rand(100000, 999999);

        DB::transaction(function () use ($request, $generatedPassword) {
            // 2. Buat Akun User dengan Email yang diinput
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($generatedPassword),
                'role'     => 'siswa' // Sesuaikan dengan sistem role kamu
            ]);

            // 3. Simpan Data Siswa dan Password Text-nya
            Siswa::create([
                'user_id'       => $user->id,
                'kelas_id'      => $request->kelas_id,
                'nisn'          => $request->nisn,
                'nama'          => $request->nama,
                'password_text' => $generatedPassword
            ]);
        });

        return redirect()->route('admin.siswas.index')->with('success', 'Siswa berhasil ditambahkan dan password otomatis dibuat!');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = $siswa->user;

        $request->validate([
            'nisn'     => 'required|unique:siswas,nisn,' . $id,
            'nama'     => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6', // Untuk reset manual
        ]);

        DB::transaction(function () use ($request, $siswa, $user) {
            $userData = [
                'name'  => $request->nama,
                'email' => $request->email,
            ];

            $newPasswordText = $siswa->password_text;

            // Kalau admin mengisi form reset password
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
                $newPasswordText = $request->password;
            }

            $user->update($userData);

            $siswa->update([
                'nisn'          => $request->nisn,
                'nama'          => $request->nama,
                'kelas_id'      => $request->kelas_id,
                'password_text' => $newPasswordText,
            ]);
        });

        return back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // 1. Cari data siswanya
        $siswa = Siswa::findOrFail($id);

        // 2. Cari akun user terkait
        $user = $siswa->user;

        // 3. Eksekusi penghapusan dalam Transaksi agar aman
        DB::transaction(function () use ($siswa, $user) {
            // Hapus profil siswanya dulu
            $siswa->delete();

            // Baru hapus akun loginnya
            if ($user) {
                $user->delete();
            }
        });

        return back()->with('success', 'Data siswa dan akun login berhasil dihapus permanen!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data siswa dan akun ujian berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
