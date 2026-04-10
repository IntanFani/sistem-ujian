<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Wajib ditambah untuk cek password manual
use App\Models\User;
use App\Models\Siswa; // Wajib ditambah untuk cek NISN

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login (Sudah mendukung Email dan NISN)
    public function login(Request $request)
    {
        // 1. Validasi: 'email' diganti jadi 'login' dan dihapus aturan format emailnya
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $loginInput = $request->login;
        $password = $request->password;

        // 2. Cari akun berdasarkan Email (di tabel users)
        $user = User::where('email', $loginInput)->first();

        // 3. Jika tidak ketemu pakai email, cari berdasarkan NISN (di tabel siswas)
        if (!$user) {
            $siswa = Siswa::where('nisn', $loginInput)->first();
            if ($siswa) {
                $user = $siswa->user; // Tarik relasi akun usernya
            }
        }

        // --- LOGIKA CEK ERROR SPESIFIK (Meneruskan gaya kodemu) ---
        if (!$user) {
            // Jika Email / NISN tidak ditemukan sama sekali
            return back()->withErrors(['login' => 'Email/NISN ini salah atau tidak terdaftar.'])->withInput();
        }

        if (!Hash::check($password, $user->password)) {
            // Jika Email/NISN ada, tapi password salah
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah.'])->withInput();
        }

        // --- JIKA BERHASIL ---
        // 4. Proses Login
        Auth::login($user);
        $request->session()->regenerate();

        // 5. Logika Redirect Berdasarkan Role
        if ($user->role == 'admin') {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->role == 'guru') {
            return redirect()->intended('/guru/dashboard');
        } elseif ($user->role == 'siswa') {
            return redirect()->intended('/siswa/dashboard');
        }

        // Default jika role tidak dikenal
        return redirect('/');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}