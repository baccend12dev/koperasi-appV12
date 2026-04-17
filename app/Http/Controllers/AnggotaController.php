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

        // karena data masih dibawah 1000 data maka pakai ilike
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_anggota', 'ilike', '%' . $request->q . '%')
                  ->orWhere('nik', 'ilike', '%' . $request->q . '%')
                  ->orWhere('no_ktp', 'ilike', '%' . $request->q . '%');
            });
        }

        $anggota = $query->paginate(10);
        $departemen = Departemen::withCount('anggota')->get();
        // dd($departemen);
        // dd($anggota);
        return view('anggota.index', compact('anggota', 'departemen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ikatanKerjaOptions = Anggota::query()
            ->whereNotNull('ikatan_kerja')
            ->where('ikatan_kerja', '!=', '')
            ->distinct()
            ->orderBy('ikatan_kerja')
            ->pluck('ikatan_kerja');

        $departemen = Departemen::all();
        return view('anggota.create', compact('departemen', 'ikatanKerjaOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'nik'              => 'required|string|max:16|unique:anggotas,nik',
            'no_ktp'           => 'nullable|string|max:16|unique:anggotas,no_ktp',
            'department_id'    => 'required|exists:departemens,id',
            'jabatan'          => 'nullable|string|max:255',
            'tanggungan'       => 'nullable|integer|min:0|max:20',
            'bagian'           => 'nullable|string|max:255',
            'ket_bagian'       => 'nullable|string|max:500',
            'tanggal_lahir'    => 'nullable|date',
            'no_hp'            => 'nullable|string|max:255',
            'jenis_kelamin'    => 'nullable|string|in:L,P',
            'ikatan_kerja'     => 'nullable|string|max:100',
            'status_anggota'   => 'required|string',
            'alamat'           => 'nullable|string',
            'tanggal_masuk'    => 'required|date',
            'simpanan_pokok'   => 'nullable|numeric|min:0',
            'simpanan_wajib'   => 'nullable|numeric|min:0',
            'simpanan_sukarela'=> 'nullable|numeric|min:0',
        ]);
        // dd($validated);
        \DB::beginTransaction();
        try {
            $anggota = Anggota::create([
                'nama_anggota'  => $validated['nama'],
                'nik'           => $validated['nik'],
                'no_ktp'        => $validated['no_ktp'] ?? null,
                'department_id' => $validated['department_id'],
                'jabatan'       => $validated['jabatan'] ?? null,
                'tanggungan'    => $validated['tanggungan'] ?? null,
                'bagian'        => $validated['bagian'] ?? null,
                'ket_bagian'    => $validated['ket_bagian'] ?? null,
                'tgl_lahir'     => $validated['tanggal_lahir'] ?? null,
                'no_hp'         => $validated['no_hp'] ?? null,
                'sex'           => $validated['jenis_kelamin'] ?? null,
                'ikatan_kerja'  => $validated['ikatan_kerja'] ?? null,
                'alamat'        => $validated['alamat'] ?? null,
                'status_anggota'=> $validated['status_anggota'],
                'tgl_msk'       => $validated['tanggal_masuk'],
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
        
        $saldo_awal = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggota->id)->sum('nominal');
        $total_simpanan = $anggota->transaksiSimpanan()->sum(\Illuminate\Support\Facades\DB::raw('simpanan_pokok + simpanan_wajib + simpanan_sukarela')) + $saldo_awal;
        $max_pinjaman = $total_simpanan > 0 ? $total_simpanan * 5 : 20000000;
        
        $pinjaman_aktif_amount = $anggota->pinjamanAktif()->sum('jumlah_pinjaman');
        $sisa_pinjaman = $anggota->pinjamanAktif()->sum('sisa_pinjaman');
        $pengajuan_pending = \App\Models\LoanRequest::where('user_id', $anggota->id)->where('status', 'pending')->sum('jumlah_pengajuan');
        $sisa_kuota = max(0, $max_pinjaman - $sisa_pinjaman - $pengajuan_pending);

        $simpanan_pokok = $anggota->masterSimpanan ? $anggota->masterSimpanan->simpanan_pokok : 0;
        $simpanan_wajib = $anggota->masterSimpanan ? $anggota->masterSimpanan->simpanan_wajib : 0;
        $simpanan_sukarela = $anggota->masterSimpanan ? $anggota->masterSimpanan->simpanan_sukarela : 0;

        $riwayat_simpanan = $anggota->transaksiSimpanan()->latest()->get();
        
        // Pinjaman fasilitas aktif & pengajuan
        $pinjaman_berjalan = \App\Models\Pinjaman::where('user_id', $anggota->id)
                                ->whereIn('status', ['berjalan', 'pending'])
                                ->with('jenisPinjaman')
                                ->latest()->get();
        // dd($pinjaman_berjalan);                        
        $pinjaman_lunas = \App\Models\Pinjaman::where('user_id', $anggota->id)
                                ->whereIn('status', ['lunas', 'rejected'])
                                ->with('jenisPinjaman')
                                ->latest()->get();

        return view('anggota.show', compact(
            'anggota', 
            'total_simpanan', 
            'max_pinjaman', 
            'pinjaman_aktif_amount', 
            'sisa_pinjaman', 
            'sisa_kuota',
            'simpanan_pokok',
            'simpanan_wajib',
            'simpanan_sukarela',
            'riwayat_simpanan',
            'pinjaman_berjalan',
            'pinjaman_lunas'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $anggota = Anggota::findOrFail($id);
        $departemen = \App\Models\Departemen::all();
        
        return view('anggota.edit', compact('anggota', 'departemen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $anggota = Anggota::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|string|max:16|unique:anggotas,nik,'.$id,
            'no_ktp' => 'nullable|string|max:16|unique:anggotas,no_ktp,'.$id,
            'department_id' => 'required|exists:departemens,id',
            'bagian' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'tgl_msk' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'tanggungan' => 'nullable|integer|min:0|max:20',
            'alamat' => 'nullable|string',
        ]);

        $anggota->update($validated);

        return redirect()->route('anggota.show', $id)->with('success', 'Data profil anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
