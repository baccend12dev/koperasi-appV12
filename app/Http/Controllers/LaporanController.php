<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Total Simpanan Aktif (Semua anggota aktif)
        $totalSimpananAktif = \App\Models\TransaksiSimpanan::whereHas('anggota', function($q) {
            $q->whereIn('status_anggota', ['active', 'aktif']);
        })->sum(\Illuminate\Support\Facades\DB::raw('simpanan_pokok + simpanan_wajib + simpanan_sukarela')) 
        + \App\Models\SaldoAwalSimpanan::whereHas('anggota', function($q) {
            $q->whereIn('status_anggota', ['active', 'aktif']);
        })->sum('nominal');

        // 2. Total Pinjaman Berjalan (Sisa pokok belum lunas)
        $totalPinjamanBerjalan = \App\Models\Pinjaman::where('status', 'berjalan')->sum('sisa_pinjaman');

        // 3. Total Anggota Aktif
        $totalAnggotaAktif = \App\Models\Anggota::whereIn('status_anggota', ['active', 'aktif'])->count();

        // 4. Anggota Meminjam (Memiliki pinjaman aktif)
        $anggotaMeminjam = \App\Models\Pinjaman::where('status', 'berjalan')->distinct('user_id')->count('user_id');

        // 5. Pengajuan Pinjaman Menunggu Persetujuan
        $pinjamanPending = \App\Models\LoanRequest::where('status', 'pending')->count();

        // 6. Pengajuan Penarikan Simpanan Menunggu Persetujuan
        $penarikanPending = \App\Models\PengambilanSimpanan::where('status', 'pending')->count();

        return view('laporan.index', compact(
            'totalSimpananAktif', 
            'totalPinjamanBerjalan', 
            'totalAnggotaAktif',
            'anggotaMeminjam',
            'pinjamanPending',
            'penarikanPending'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
