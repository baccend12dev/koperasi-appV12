<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\Pinjaman;
use App\Models\Pencairan;
use App\Models\PengambilanSimpanan;
use App\Models\MasterJenisPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function pinjaman(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $jenis = $request->get('jenis');

        // Sidebar periode khusus pinjaman
        $sidebarPeriode = $this->getSidebarPeriode('pinjamans', 'created_at');

        // List jenis pinjaman untuk filter dropdown
        $jenisPinjamanList = MasterJenisPinjaman::with('children')
            ->whereNull('parent_id')
            ->orderBy('nama_pinjaman')
            ->get();

        $query = Pinjaman::with('anggota', 'jenisPinjaman');

        if ($tahun) {
            $query->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$tahun]);
            if ($bulan) $query->whereRaw('EXTRACT(MONTH FROM created_at) = ?', [$bulan]);
        }

        if ($jenis) {
            $query->where('jenis_pinjaman_id', $jenis);
        }

        $listPinjaman = $query->latest()->get();

        // Check internal Pencairan records for 'paid' status
        $pinjamanIds = $listPinjaman->pluck('id');
        $pencairanExisting = Pencairan::where('ref_type', 'pinjaman')
            ->whereIn('ref_id', $pinjamanIds)
            ->get()->keyBy('ref_id');

        // Statistik
        $totalPinjaman = $listPinjaman->sum('jumlah_pinjaman');
        $totalBunga    = $listPinjaman->sum('total_bunga');
        $totalPotongan = $listPinjaman->sum('potongan_pelunasan');
        $totalNet      = $listPinjaman->sum('jumlah_cair');

        // Hitung badge belum bayar (pending)
        $countPendingPinjaman = Pinjaman::whereNotIn('id', 
            Pencairan::where('ref_type', 'pinjaman')->where('status', 'paid')->pluck('ref_id')
        )->count();

        $countPendingSimpanan = PengambilanSimpanan::where('status', 'approved')->whereNotIn('id', 
            Pencairan::where('ref_type', 'simpanan')->where('status', 'paid')->pluck('ref_id')
        )->count();

        return view('pencairan.pinjaman', compact(
            'listPinjaman', 'sidebarPeriode', 'pencairanExisting',
            'tahun', 'bulan', 'jenis', 'totalPinjaman', 'totalBunga', 'totalPotongan', 'totalNet',
            'jenisPinjamanList', 'countPendingPinjaman', 'countPendingSimpanan'
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

        // Hitung badge belum bayar (pending)
        $countPendingPinjaman = Pinjaman::whereNotIn('id', 
            Pencairan::where('ref_type', 'pinjaman')->where('status', 'paid')->pluck('ref_id')
        )->count();

        $countPendingSimpanan = PengambilanSimpanan::where('status', 'approved')->whereNotIn('id', 
            Pencairan::where('ref_type', 'simpanan')->where('status', 'paid')->pluck('ref_id')
        )->count();

        return view('pencairan.pengambilan', compact(
            'listPengambilan', 'sidebarPeriode', 'pencairanExisting',
            'tahun', 'bulan', 'totalNominal', 'countPendingPinjaman', 'countPendingSimpanan'
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

    /**
     * Tandai bulk pencairan sebagai sudah dibayar via checkbox.
     */
    public function markPaidBulk(Request $request)
    {
        $request->validate([
            'ref_type'   => 'required|in:pinjaman,simpanan',
            'ids'        => 'required|array|min:1',
            'ids.*'      => 'required|integer',
            'nominals'   => 'required|array',
            'nominals.*' => 'required|numeric',
            'anggota_ids'   => 'required|array',
            'anggota_ids.*' => 'required|integer',
            'metode'     => 'required|in:transfer,cash',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $refType    = $request->ref_type;
        $ids        = $request->ids;
        $nominals   = $request->nominals;
        $anggotaIds = $request->anggota_ids;
        $metode     = $request->metode;
        $tanggal    = $request->tanggal;
        $keterangan = $request->keterangan;
        $createdBy  = auth()->id() ?? 1;

        foreach ($ids as $key => $id) {
            Pencairan::updateOrCreate(
                [
                    'ref_type' => $refType,
                    'ref_id'   => $id,
                ],
                [
                    'anggota_id'  => $anggotaIds[$key] ?? null,
                    'nominal'     => $nominals[$key]   ?? 0,
                    'status'      => 'paid',
                    'metode'      => $metode,
                    'tanggal'     => $tanggal,
                    'keterangan'  => $keterangan,
                    'created_by'  => $createdBy,
                ]
            );
        }

        $count = count($ids);
        return back()->with('success', "$count data pencairan berhasil ditandai sebagai sudah dibayar.");
    }
}
