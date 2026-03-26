<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Logika Redirect Berdasarkan Role
            if (Auth::user()->role == 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif (Auth::user()->role == 'guru') {
                return redirect()->intended('/guru/dashboard');
            } elseif (Auth::user()->role == 'siswa') {
                return redirect()->intended('/siswa/dashboard');
            }

            // Default jika role tidak dikenal
            return redirect('/');
        }
        // --- LOGIKA CEK ERROR SPESIFIK ---
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Jika email tidak ditemukan
            return back()->withErrors(['email' => 'Email ini salah atau tidak terdaftar.'])->withInput();
        } else {
            // Jika email ada tapi password salah
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah.'])->withInput();
        }
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