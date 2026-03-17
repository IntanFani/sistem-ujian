<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah data secara real-time
        $countSiswa   = Siswa::count();
        $countGuru    = Guru::count();
        $countKelas   = Kelas::count();
        $countSubject = Subject::count();

        return view('admin.dashboard', compact(
            'countSiswa', 
            'countGuru', 
            'countKelas', 
            'countSubject'
        ));
    }
}