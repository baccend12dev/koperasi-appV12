<?php

namespace App\Http\Controllers;
use App\Models\Anggota;
use App\Models\Departemen;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Anggota::query();

        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_anggota', 'ilike', '%' . $request->q . '%')
                  ->orWhere('nokop', 'ilike', '%' . $request->q . '%');
            });
        }

        $anggota = $query->paginate(10);
        $departemen = Departemen::withCount('anggota')->get();

        return view('anggota.index', compact('anggota', 'departemen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departemen = Departemen::all();
        return view('anggota.create', compact('departemen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:16|unique:anggotas,nik',
            'bagian' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departemens,id',
            'no_pegawai' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|in:L,P',
            'ikatan_kerja' => 'nullable|string',
            'status_anggota' => 'required|string',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'required|date',
            'simpanan_pokok' => 'nullable|numeric|min:0',
            'simpanan_wajib' => 'nullable|numeric|min:0',
            'simpanan_sukarela' => 'nullable|numeric|min:0',
        ]);
        // dd($validated);
        \DB::beginTransaction();
        try {
            $anggota = Anggota::create([
                'nama_anggota' => $validated['nama'],
                'nik' => $validated['nik'],
                'ket_bagian' => $validated['bagian'] ?? null,
                'bagian_id' => 1, // Fallback int if required
                'department_id' => $validated['department_id'],
                'no_pegawai' => $validated['no_pegawai'] ?? null,
                'tgl_lahir' => $validated['tanggal_lahir'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'ikatan_kerja' => $validated['ikatan_kerja'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'status_anggota' => $validated['status_anggota'],
                'tgl_bergabung' => $validated['tanggal_masuk'],
            ]);

            \App\Models\MasterSimpanan::create([
                'anggota_id' => $anggota->id,
                'simpanan_pokok' => $validated['simpanan_pokok'] ?? 0,
                'simpanan_wajib' => $validated['simpanan_wajib'] ?? 0,
                'simpanan_sukarela' => $validated['simpanan_sukarela'] ?? 0,
                'tanggal_mulai' => $validated['tanggal_masuk'],
                'aktif' => true,
            ]);

            \DB::commit();

            return redirect()->route('anggota.index')->with('success', 'Anggota dan data simpanan berhasil ditambahkan.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $anggota = Anggota::with(['departemen', 'masterSimpanan'])->findOrFail($id);
        
        // Data for new mockup design
        $total_simpanan = 15450000;
        $max_pinjaman = 25000000;
        $pinjaman_aktif_amount = 10000000;
        $sisa_pinjaman = 1200000;
        $credit_score = 780;

        $simpanan_pokok = $anggota->masterSimpanan ? $anggota->masterSimpanan->simpanan_pokok : 1000000;
        $simpanan_wajib = $anggota->masterSimpanan ? $anggota->masterSimpanan->simpanan_wajib : 50000;
        $simpanan_sukarela = $anggota->masterSimpanan ? $anggota->masterSimpanan->simpanan_sukarela : 100000;

        $riwayat_simpanan = collect([
            (object)['date' => '12 Mar 2026', 'type' => 'Mandatory Savings', 'color' => '#1a73e8', 'amount' => 50000, 'period' => 'Mar 2026', 'status' => 'SUCCESS'],
            (object)['date' => '05 Mar 2026', 'type' => 'Voluntary Deposit', 'color' => '#fbbc04', 'amount' => 100000, 'period' => 'Mar 2026', 'status' => 'SUCCESS'],
            (object)['date' => '12 Feb 2026', 'type' => 'Mandatory Savings', 'color' => '#1a73e8', 'amount' => 50000, 'period' => 'Feb 2026', 'status' => 'SUCCESS'],
        ]);

        return view('anggota.show', compact(
            'anggota', 
            'total_simpanan', 
            'max_pinjaman', 
            'pinjaman_aktif_amount', 
            'sisa_pinjaman', 
            'credit_score',
            'simpanan_pokok',
            'simpanan_wajib',
            'simpanan_sukarela',
            'riwayat_simpanan'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
