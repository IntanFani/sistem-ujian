<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            }

            // Default jika role tidak dikenal
            return redirect('/');
        }
        return back()->withErrors(['email' => 'Email atau password salah!']);
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