<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\Pinjaman;
use App\Models\Pencairan;
use App\Models\PengambilanSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function pinjaman(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        // Sidebar periode khusus pinjaman
        $sidebarPeriode = $this->getSidebarPeriode('pinjamans', 'created_at');

        $query = Pinjaman::with('anggota', 'jenisPinjaman');

        if ($tahun) {
            $query->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$tahun]);
            if ($bulan) $query->whereRaw('EXTRACT(MONTH FROM created_at) = ?', [$bulan]);
        }

        $listPinjaman = $query->latest()->get();

        // Check internal Pencairan records for 'paid' status
        $pinjamanIds = $listPinjaman->pluck('id');
        $pencairanExisting = Pencairan::where('ref_type', 'pinjaman')
            ->whereIn('ref_id', $pinjamanIds)
            ->get()->keyBy('ref_id');

        // Statistik
        $totalPinjaman = $listPinjaman->sum('jumlah_pinjaman');
        $totalPotongan = $listPinjaman->sum('potongan_pelunasan');
        $totalNet      = $listPinjaman->sum('jumlah_cair');

        return view('pencairan.pinjaman', compact(
            'listPinjaman', 'sidebarPeriode', 'pencairanExisting',
            'tahun', 'bulan', 'totalPinjaman', 'totalPotongan', 'totalNet'
        ));
    }

    public function pengambilan(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        // Sidebar periode khusus pengambilan
        $sidebarPeriode = $this->getSidebarPeriode('pengambilan_simpanans', 'updated_at');

        $query = PengambilanSimpanan::with('anggota')->where('status', 'approved');

        if ($tahun) {
            $query->whereRaw('EXTRACT(YEAR FROM updated_at) = ?', [$tahun]);
            if ($bulan) $query->whereRaw('EXTRACT(MONTH FROM updated_at) = ?', [$bulan]);
        }

        $listPengambilan = $query->latest('updated_at')->get();

        // Check internal Pencairan records
        $pengambilanIds = $listPengambilan->pluck('id');
        $pencairanExisting = Pencairan::where('ref_type', 'simpanan')
            ->whereIn('ref_id', $pengambilanIds)
            ->get()->keyBy('ref_id');

        $totalNominal = $listPengambilan->sum('nominal');

        return view('pencairan.pengambilan', compact(
            'listPengambilan', 'sidebarPeriode', 'pencairanExisting',
            'tahun', 'bulan', 'totalNominal'
        ));
    }

    private function getSidebarPeriode($table, $column)
    {
        $periodeRaw = DB::table($table)
            ->select(DB::raw("EXTRACT(YEAR FROM $column)::int as tahun, EXTRACT(MONTH FROM $column)::int as bulan"))
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $sidebarPeriode = [];
        foreach ($periodeRaw as $row) {
            $sidebarPeriode[$row->tahun][] = $row->bulan;
        }
        return $sidebarPeriode;
    }

    /**
     * Tandai pencairan sebagai sudah dibayar (status = paid).
     */
    public function markPaid(Request $request)
    {
        $request->validate([
            'ref_type'   => 'required|in:pinjaman,simpanan',
            'ref_id'     => 'required|integer',
            'anggota_id' => 'required|integer',
            'nominal'    => 'required|numeric',
            'metode'     => 'required|in:transfer,cash',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Pencairan::updateOrCreate(
            [
                'ref_type' => $request->ref_type,
                'ref_id'   => $request->ref_id,
            ],
            [
                'anggota_id'  => $request->anggota_id,
                'nominal'     => $request->nominal,
                'status'      => 'paid',
                'metode'      => $request->metode,
                'tanggal'     => $request->tanggal,
                'keterangan'  => $request->keterangan,
                'created_by'  => auth()->id() ?? 1,
            ]
        );

        return back()->with('success', 'Pencairan berhasil ditandai sebagai sudah dibayar.');
    }
}
