<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $query = Departemen::withCount('anggota');

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('kode', 'like', '%' . $request->q . '%');
        }

        $departemen = $query->orderBy('nama')->paginate(25)->withQueryString();

        return view('departemen.index', compact('departemen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'kode'     => 'nullable|string|max:20|unique:departemens,kode',
            'deskripsi'=> 'nullable|string|max:500',
        ]);

        Departemen::create($request->only('nama', 'kode', 'deskripsi'));

        return redirect()->route('departemen.index')
                         ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $departemen = Departemen::find($id);
        $request->validate([
            'nama'     => 'required|string|max:100',
            'kode'     => 'nullable|string|max:20|unique:departemens,kode,' . $departemen->id,
            'deskripsi'=> 'nullable|string|max:500',
        ]);
        $departemen->update($request->all());
        return redirect()->route('departemen.index')
                         ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $departemen = Departemen::find($id);
        if ($departemen->anggota()->count() > 0) {
            return redirect()->route('departemen.index')
                             ->with('error', 'Departemen tidak bisa dihapus karena masih memiliki anggota.');
        }

        $departemen->delete();

        return redirect()->route('departemen.index')
                         ->with('success', 'Departemen berhasil dihapus.');
    }
}
