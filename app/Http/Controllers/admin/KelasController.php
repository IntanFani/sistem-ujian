<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Mengambil kelas beserta nama wali kelasnya (Eager Loading)
        $kelas = Kelas::with('waliKelas')->get();
        // Mengambil semua guru untuk pilihan dropdown di modal
        $gurus = \App\Models\Guru::all(); 
        
        return view('admin.kelas.index', compact('kelas', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'guru_id'    => 'nullable|exists:gurus,id' // Boleh kosong jika belum ada wali
        ]);

        Kelas::create($request->all());
        return back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $id,
            'guru_id'    => 'nullable|exists:gurus,id'
        ]);

        $kelas->update($request->all());
        return back()->with('success', 'Data kelas diperbarui!');
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return back()->with('success', 'Kelas berhasil dihapus!');
    }
}