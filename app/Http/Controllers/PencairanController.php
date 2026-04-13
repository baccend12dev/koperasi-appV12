<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\Pencairan;
use App\Models\PengambilanSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $tipe  = $request->get('tipe', 'all'); // all | pinjaman | simpanan

        // ── Sidebar: tahun-bulan available dari kedua sumber ──────
        $periodeRaw = DB::table(function ($q) {
            $q->select(DB::raw("EXTRACT(YEAR FROM updated_at)::int as tahun, EXTRACT(MONTH FROM updated_at)::int as bulan"))
              ->from('loan_requests')
              ->where('status', 'approved')
              ->union(
                  DB::table('pengambilan_simpanans')
                      ->select(DB::raw("EXTRACT(YEAR FROM updated_at)::int as tahun, EXTRACT(MONTH FROM updated_at)::int as bulan"))
                      ->where('status', 'approved')
              );
        }, 'periode')
        ->selectRaw('tahun, bulan')
        ->distinct()
        ->orderByDesc('tahun')
        ->orderByDesc('bulan')
        ->get();

        // Grup per tahun → [ tahun => [bulan, ...] ]
        $sidebarPeriode = [];
        foreach ($periodeRaw as $row) {
            $sidebarPeriode[$row->tahun][] = $row->bulan;
        }

        // ── Query pinjaman approved ────────────────────────────────
        $pinjamanQuery = LoanRequest::with('anggota', 'jenisPinjaman')
            ->where('status', 'approved');

        if ($tahun) {
            $pinjamanQuery->whereRaw('EXTRACT(YEAR FROM updated_at) = ?', [$tahun]);
            if ($bulan) $pinjamanQuery->whereRaw('EXTRACT(MONTH FROM updated_at) = ?', [$bulan]);
        }

        // ── Query pengambilan simpanan approved ────────────────────
        $pengambilanQuery = PengambilanSimpanan::with('anggota')
            ->where('status', 'approved');

        if ($tahun) {
            $pengambilanQuery->whereRaw('EXTRACT(YEAR FROM updated_at) = ?', [$tahun]);
            if ($bulan) $pengambilanQuery->whereRaw('EXTRACT(MONTH FROM updated_at) = ?', [$bulan]);
        }

        // Terapkan filter tipe
        $listPinjaman    = in_array($tipe, ['all', 'pinjaman']) ? $pinjamanQuery->latest('updated_at')->get()    : collect();
        $listPengambilan = in_array($tipe, ['all', 'simpanan']) ? $pengambilanQuery->latest('updated_at')->get() : collect();

        // ── Load existing Pencairan records (untuk status bayar) ───
        // Bangun lookup: "pinjaman-{id}" => Pencairan, "simpanan-{id}" => Pencairan
        $pinjamanIds    = $listPinjaman->pluck('id');
        $pengambilanIds = $listPengambilan->pluck('id');

        $pencairanExisting = Pencairan::where(function ($q) use ($pinjamanIds, $pengambilanIds) {
            $q->where(fn($q2) => $q2->where('ref_type', 'pinjaman')->whereIn('ref_id', $pinjamanIds))
              ->orWhere(fn($q2) => $q2->where('ref_type', 'simpanan')->whereIn('ref_id', $pengambilanIds));
        })->get()->keyBy(fn($p) => $p->ref_type . '-' . $p->ref_id);

        // Merge & sort by updated_at descending
        $pencairanList = $listPinjaman->map(fn($r) => (object)[
            'id'          => $r->id,
            'ref_type'    => 'pinjaman',
            'anggota'     => $r->anggota,
            'anggota_id'  => $r->anggota?->id,
            'keterangan'  => $r->jenisPinjaman?->nama_pinjaman ?? 'Pinjaman',
            'nominal'     => $r->jumlah_pengajuan,
            'tanggal'     => $r->updated_at,
            'approved_at' => $r->approved_at,
            'pencairan'   => $pencairanExisting->get('pinjaman-' . $r->id),
        ])->merge(
            $listPengambilan->map(fn($r) => (object)[
                'id'          => $r->id,
                'ref_type'    => 'simpanan',
                'anggota'     => $r->anggota,
                'anggota_id'  => $r->anggota?->id,
                'keterangan'  => 'Penarikan Simpanan',
                'nominal'     => $r->nominal,
                'tanggal'     => $r->updated_at,
                'approved_at' => $r->approved_at,
                'pencairan'   => $pencairanExisting->get('simpanan-' . $r->id),
            ])
        )->sortByDesc('tanggal')->values();

        // Summary
        $totalNominalPinjaman    = $listPinjaman->sum('jumlah_pengajuan');
        $totalNominalPengambilan = $listPengambilan->sum('nominal');
        $totalNominal            = $totalNominalPinjaman + $totalNominalPengambilan;

        return view('pencairan.index', compact(
            'pencairanList',
            'sidebarPeriode',
            'tahun', 'bulan', 'tipe',
            'totalNominal', 'totalNominalPinjaman', 'totalNominalPengambilan'
        ));
    }

    /**
     * Tandai pencairan sebagai sudah dibayar (status = paid).
     * Jika sudah ada record di pencairans → update, jika belum → buat baru.
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
