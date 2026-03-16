<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // Tampilkan daftar mapel
    public function index()
    {
        $subjects = Subject::orderBy('name', 'asc')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    // Simpan mapel baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:subjects,name|max:255'
        ]);

        Subject::create($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    // Update Mapel
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:subjects,name,' . $id
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    // Hapus Mapel
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return back()->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}