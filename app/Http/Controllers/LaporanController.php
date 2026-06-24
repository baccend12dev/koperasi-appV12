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

    public function simpanan(Request $request)
    {
        $query = \App\Models\Anggota::query()
            ->with(['departemen']);

        // Filter: Departemen
        if ($request->filled('departemen_id')) {
            $query->where('department_id', $request->departemen_id);
        }

        // Filter: Status Anggota
        if ($request->filled('status_anggota')) {
            $query->where('status_anggota', $request->status_anggota);
        }

        // Filter: Search Name/NIK
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_anggota', 'ilike', '%' . $request->q . '%')
                  ->orWhere('nik', 'ilike', '%' . $request->q . '%');
            });
        }

        // We load sum of transactions and initial balances
        $query->withSum('transaksiSimpanan as total_pokok', 'simpanan_pokok')
              ->withSum('transaksiSimpanan as total_wajib', 'simpanan_wajib')
              ->withSum('transaksiSimpanan as total_sukarela', 'simpanan_sukarela')
              ->withSum('saldoAwalSimpanan as total_saldo_awal', 'nominal');

        // Totals query for the cards
        $memberIdsQuery = \App\Models\Anggota::query();
        if ($request->filled('departemen_id')) {
            $memberIdsQuery->where('department_id', $request->departemen_id);
        }
        if ($request->filled('status_anggota')) {
            $memberIdsQuery->where('status_anggota', $request->status_anggota);
        }
        if ($request->filled('q')) {
            $memberIdsQuery->where(function($q) use ($request) {
                $q->where('nama_anggota', 'ilike', '%' . $request->q . '%')
                  ->orWhere('nik', 'ilike', '%' . $request->q . '%');
            });
        }

        $memberIds = $memberIdsQuery->pluck('id');

        $sumPokok = \App\Models\TransaksiSimpanan::whereIn('anggota_id', $memberIds)->sum('simpanan_pokok');
        $sumWajib = \App\Models\TransaksiSimpanan::whereIn('anggota_id', $memberIds)->sum('simpanan_wajib');
        $sumSukarela = \App\Models\TransaksiSimpanan::whereIn('anggota_id', $memberIds)->sum('simpanan_sukarela');
        $sumSaldoAwal = \App\Models\SaldoAwalSimpanan::whereIn('anggota_id', $memberIds)->sum('nominal');
        $grandTotal = $sumPokok + $sumWajib + $sumSukarela + $sumSaldoAwal;

        if ($request->get('export') === 'excel') {
            $filename = "laporan_saldo_simpanan_" . date('Ymd_His') . ".xls";
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $members_export = $query->orderBy('nama_anggota')->get();

            $html = view('laporan.export_simpanan_excel', compact(
                'members_export',
                'sumPokok',
                'sumWajib',
                'sumSukarela',
                'sumSaldoAwal',
                'grandTotal'
            ))->render();

            return response($html, 200, $headers);
        }

        $departments = \App\Models\Departemen::orderBy('nama')->get();
        $members = $query->orderBy('nama_anggota')->paginate(15)->appends($request->except(['page', 'export']));

        return view('laporan.simpanan', compact(
            'members',
            'departments',
            'sumPokok',
            'sumWajib',
            'sumSukarela',
            'sumSaldoAwal',
            'grandTotal'
        ));
    }

    public function transaksiSimpanan(Request $request)
    {
        $query = \App\Models\TransaksiSimpanan::with(['anggota.departemen']);

        // Filter: Departemen
        if ($request->filled('departemen_id')) {
            $query->whereHas('anggota', function($q) use ($request) {
                $q->where('department_id', $request->departemen_id);
            });
        }

        // Filter: Search Name/NIK
        if ($request->filled('q')) {
            $query->whereHas('anggota', function($q) use ($request) {
                $q->where('nama_anggota', 'ilike', '%' . $request->q . '%')
                  ->orWhere('nik', 'ilike', '%' . $request->q . '%');
            });
        }

        // Filter: Periode (Bulan & Tahun) via input type="month"
        $periode = null;
        if ($request->has('periode')) {
            $periode = $request->input('periode');
        } else {
            $periode = date('Y-m');
        }

        if ($periode) {
            $parts = explode('-', $periode);
            if (count($parts) === 2) {
                $query->whereYear('transaction_date', $parts[0]);
                $query->whereMonth('transaction_date', $parts[1]);
            }
        }

        // Filter: Jenis Transaksi
        if ($request->filled('jenis')) {
            $jenis = $request->jenis;
            if ($jenis === 'langsung') {
                $query->where('description', 'Simpanan Langsung Sukarela');
            } elseif ($jenis === 'penarikan') {
                $query->where('description', 'like', 'Penarikan Simpanan Disetujui%');
            } elseif ($jenis === 'bulanan') {
                $query->where('description', 'not like', 'Penarikan Simpanan Disetujui%')
                      ->where('description', '!=', 'Simpanan Langsung Sukarela');
            }
        }

        // Calculate sums from TransaksiSimpanan based on the filtered query
        $sumPokok = (clone $query)->sum('simpanan_pokok');
        $sumWajib = (clone $query)->sum('simpanan_wajib');
        $sumSukarela = (clone $query)->sum('simpanan_sukarela');
        $grandTotal = $sumPokok + $sumWajib + $sumSukarela;

        if ($request->get('export') === 'excel') {
            $filename = "laporan_transaksi_simpanan_" . date('Ymd_His') . ".xls";
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $transaksi_export = $query->orderBy('transaction_date', 'desc')->get();

            $html = view('laporan.export_transaksi_simpanan_excel', compact(
                'transaksi_export',
                'periode',
                'sumPokok',
                'sumWajib',
                'sumSukarela',
                'grandTotal'
            ))->render();

            return response($html, 200, $headers);
        }

        $departments = \App\Models\Departemen::orderBy('nama')->get();
        $transaksi = $query->orderBy('transaction_date', 'desc')
            ->paginate(15)
            ->appends(array_merge(
                $request->except(['page', 'export']),
                ['periode' => $periode ?? '']
            ));

        return view('laporan.transaksi_simpanan', compact(
            'transaksi',
            'departments',
            'periode',
            'sumPokok',
            'sumWajib',
            'sumSukarela',
            'grandTotal'
        ));
    }
}
