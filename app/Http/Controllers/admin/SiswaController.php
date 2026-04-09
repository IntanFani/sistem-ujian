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
            'email'    => 'required|email|unique:users,email',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat User Login (Role: siswa, Password: NISN)
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->nisn), // NISN jadi password default
                'role'     => 'siswa',
            ]);

            // 2. Buat Profil Siswa
            Siswa::create([
                'user_id'  => $user->id,
                'kelas_id' => $request->kelas_id,
                'nisn'     => $request->nisn,
                'nama'     => $request->nama,
            ]);
        });

        return back()->with('success', 'Siswa berhasil ditambah! Password default adalah NISN.');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = $siswa->user; // Ambil akun login terkait

        $request->validate([
            'nisn'     => 'required|unique:siswas,nisn,' . $id,
            'nama'     => 'required',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'kelas_id' => 'required|exists:kelas,id',
            'password' => 'nullable|min:6', // Password boleh kosong kalau nggak mau diubah
        ]);

        DB::transaction(function () use ($request, $siswa, $user) {
            // 1. Update Akun Login (User)
            $userData = [
                'name'  => $request->nama,
                'email' => $request->email,
            ];

            // Cek kalau admin input password baru
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // 2. Update Data Profil Siswa
            $siswa->update([
                'nisn'     => $request->nisn,
                'nama'     => $request->nama,
                'kelas_id' => $request->kelas_id,
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
