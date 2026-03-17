<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            // Logout-kan saja atau lempar ke halaman error yang rapi
            Auth::logout();
            return redirect('/login')->with('error', 'Akun Anda belum terdaftar sebagai Guru.');
        }

        $countSoal = Question::where('guru_id', $guru->id)->count();
        $countUjian = 0; 

        return view('guru.dashboard', compact('guru', 'countSoal', 'countUjian'));
}
}
