<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function index()
    {
        // Ambil data guru beserta relasi user dan subject-nya
        $gurus = Guru::with(['user', 'subject'])->get();
        // Ambil data mapel untuk dropdown di modal tambah
        $subjects = Subject::all(); 
        
        return view('admin.gurus.index', compact('gurus', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:gurus,nip',
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|min:6', 
            'subject_id' => 'required|exists:subjects,id'
        ]);

        DB::transaction(function () use ($request) {
            // LOGIKA OTOMATIS: 
            // Jika password diisi, pakai itu. Jika kosong, pakai NIP.
            $passwordFixed = $request->password ?: $request->nip;

            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($passwordFixed),
                'role' => 'guru',
            ]);

            Guru::create([
                'user_id' => $user->id,
                'subject_id' => $request->subject_id,
                'nip' => $request->nip,
                'nama' => $request->nama,
            ]);
        });

        return back()->with('success', 'Data Guru berhasil ditambahkan! Password default adalah NIP.');
    }

    public function update(Request $request, $id)
{
    $guru = Guru::findOrFail($id);
    $user = $guru->user;

    $request->validate([
        'nip'        => 'required|unique:gurus,nip,' . $id,
        'nama'       => 'required',
        'email'      => 'required|email|unique:users,email,' . $user->id,
        'subject_id' => 'required|exists:subjects,id',
        'password'   => 'nullable|min:6' // Nullable artinya boleh kosong
    ]);

    DB::transaction(function () use ($request, $guru, $user) {
        $userData = [
            'name'  => $request->nama,
            'email' => $request->email,
        ];

        // LOGIKA RESET: Jika kotak password diisi, maka update password-nya
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $guru->update([
            'nip'        => $request->nip,
            'nama'       => $request->nama,
            'subject_id' => $request->subject_id,
        ]);
    });

    return back()->with('success', 'Data Guru berhasil diperbarui!');
}

    public function destroy($id)
    {
        // Cari data guru
        $guru = Guru::findOrFail($id);
        
        // Ambil user terkait
        $user = $guru->user;

        // Hapus user (ini akan otomatis menghapus data di tabel gurus karena cascade)
        $user->delete();

        return back()->with('success', 'Data Guru dan akun login berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        $guru = Guru::findOrFail($id);
        $user = $guru->user;

        $user->update([
            'password' => Hash::make($guru->nip) // Reset balik ke NIP
        ]);

        return back()->with('success', 'Password berhasil direset kembali ke NIP!');
    }
}