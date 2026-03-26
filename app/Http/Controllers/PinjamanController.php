<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pinjaman_aktif' => 125,
            'total_outstanding' => 2500000000, // 2.5 M
            'jumlah_pengajuan_pending' => 12,
            'jumlah_pinjaman_macet' => 3
        ];
        
        $pengajuan_terbaru = collect([
            (object)[
                'tanggal' => '26 Mar 2026', 
                'nama' => 'Budi Santoso', 
                'nik' => '32750123', 
                'jenis' => 'Pinjaman Reguler', 
                'jumlah' => 15000000, 
                'tenor' => 12, 
                'status' => 'Pending'
            ],
            (object)[
                'tanggal' => '25 Mar 2026', 
                'nama' => 'Siti Aminah', 
                'nik' => '32750199', 
                'jenis' => 'Pinjaman Darurat', 
                'jumlah' => 5000000, 
                'tenor' => 6, 
                'status' => 'Pending'
            ],
            (object)[
                'tanggal' => '24 Mar 2026', 
                'nama' => 'Agus Setiawan', 
                'nik' => '32750888', 
                'jenis' => 'Pinjaman Reguler', 
                'jumlah' => 25000000, 
                'tenor' => 24, 
                'status' => 'Approved'
            ],
            (object)[
                'tanggal' => '22 Mar 2026', 
                'nama' => 'Dewi Lestari', 
                'nik' => '32750777', 
                'jenis' => 'Pinjaman Pendidikan', 
                'jumlah' => 10000000, 
                'tenor' => 12, 
                'status' => 'Rejected'
            ],
        ]);

        return view('pinjaman.index', compact('stats', 'pengajuan_terbaru'));
    }

    public function pengajuan() { 
        $pengajuan_list = collect([
            (object)[
                'id' => 1,
                'tanggal' => '26 Mar 2026', 
                'nama' => 'Budi Santoso', 
                'nik' => '32750123', 
                'jenis_pinjaman' => 'Pinjaman Reguler', 
                'jumlah' => 15000000, 
                'tenor' => 12, 
                'status' => 'Pending'
            ],
            (object)[
                'id' => 2,
                'tanggal' => '25 Mar 2026', 
                'nama' => 'Siti Aminah', 
                'nik' => '32750199', 
                'jenis_pinjaman' => 'Pinjaman Darurat', 
                'jumlah' => 5000000, 
                'tenor' => 6, 
                'status' => 'Pending'
            ],
            (object)[
                'id' => 3,
                'tanggal' => '24 Mar 2026', 
                'nama' => 'Agus Setiawan', 
                'nik' => '32750888', 
                'jenis_pinjaman' => 'Pinjaman Reguler', 
                'jumlah' => 25000000, 
                'tenor' => 24, 
                'status' => 'Approved'
            ],
            (object)[
                'id' => 4,
                'tanggal' => '22 Mar 2026', 
                'nama' => 'Dewi Lestari', 
                'nik' => '32750777', 
                'jenis_pinjaman' => 'Pinjaman Pendidikan', 
                'jumlah' => 10000000, 
                'tenor' => 12, 
                'status' => 'Rejected'
            ],
            (object)[
                'id' => 5,
                'tanggal' => '20 Mar 2026', 
                'nama' => 'Hendra Putra', 
                'nik' => '32750555', 
                'jenis_pinjaman' => 'Pinjaman Reguler', 
                'jumlah' => 8000000, 
                'tenor' => 12, 
                'status' => 'Approved'
            ],
        ]);
        return view('pinjaman.pengajuan', compact('pengajuan_list')); 
    }

    public function create() {
        return view('pinjaman.create');
    }

    public function masterJenis() { 
        $jenis_pinjaman = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        return view('pinjaman.master_jenis', compact('jenis_pinjaman')); 
    }

    public function storeMasterJenis(\Illuminate\Http\Request $request) {
        $request->validate([
            'nama_pinjaman' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:master_jenis_pinjaman,id',
            'limit_maksimal' => 'nullable|numeric',
            'bunga' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:255',
        ]);

        \App\Models\MasterJenisPinjaman::create($request->all());

        return redirect()->back()->with('success', 'Jenis pinjaman berhasil ditambahkan');
    }
}
