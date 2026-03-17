<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{

    public function index()
    {
        $siswas = Siswa::with(['user', 'kelas'])->get();
        $kelases = Kelas::all(); // Untuk dropdown pilih kelas
        return view('admin.siswas.index', compact('siswas', 'kelases'));
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
}
