<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\PengambilanSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersetujuanController extends Controller
{
    public function pinjaman(Request $request) {
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        
        $query = LoanRequest::with(['anggota.transaksiSimpanan', 'anggota.pinjamanAktif.jenisPinjaman', 'jenisPinjaman', 'anggota.masterSimpanan']);
        
        if ($request->filled('jenis')) {
            $query->where('jenis_pinjaman_id', $request->jenis);
        }
        
        // Show all statuses, sort pending first
        $pengajuan_list = $query->get();
        // dd($pengajuan_list);
        
        // Calculate member financial stats for approval modal if pending
        foreach ($pengajuan_list as $item) {
            if ($item->anggota && $item->status == 'pending') {
                $anggotaId = $item->anggota->id;
                $saldo_awal = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggotaId)->sum('nominal');
                
                $total_simpanan = $item->anggota->transaksiSimpanan->sum(function($t) {
                    return $t->simpanan_pokok + $t->simpanan_wajib + $t->simpanan_sukarela;
                }) + $saldo_awal;

                $pinjaman_berjalan = $item->anggota->pinjamanAktif->sum('sisa_pinjaman');
                $cicilan_saat_ini = $item->anggota->pinjamanAktif->sum('cicilan_per_bulan');

                // hitung simpanan yang harus dibayar setiap bulan
                $simpananPerbulan = $item->anggota->masterSimpanan ? ($item->anggota->masterSimpanan->simpanan_wajib + $item->anggota->masterSimpanan->simpanan_sukarela) : 0;
                
                $item->total_simpanan_saat_ini = $total_simpanan;
                $item->pinjaman_berjalan_saat_ini = $pinjaman_berjalan;
                $item->cicilan_saat_ini = $cicilan_saat_ini;
                $item->simpanan_perbulan = $simpananPerbulan;
                $item->total_cicilan_baru = $cicilan_saat_ini + $item->cicilan_per_bulan + $simpananPerbulan;
            }
        }
        
        $totalPengajuan = $pengajuan_list->where('status', 'pending')->count();
        $totalNominal = $pengajuan_list->where('status', 'pending')->sum('jumlah_pengajuan');
        // dd($pengajuan_list);
        return view('persetujuan.pinjaman', compact('pengajuan_list', 'jenisPinjamanList', 'totalPengajuan', 'totalNominal'));
    }

    public function pengambilan(Request $request) {
        $filterStatus = $request->get('status', 'pending');

        $query = PengambilanSimpanan::with(['anggota.transaksiSimpanan', 'anggota.pinjamanAktif', 'anggota.masterSimpanan'])
            ->latest();

        if ($filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        $pengambilan_list = $query->paginate(15)->withQueryString();

        // Calculate member financial stats for modal (pending items only)
        foreach ($pengambilan_list as $item) {
            if ($item->anggota && $item->status == 'pending') {
                $anggotaId = $item->anggota->id;
                $saldo_awal = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggotaId)->sum('nominal');

                $item->total_simpanan = $item->anggota->transaksiSimpanan->sum(function($t) {
                    return $t->simpanan_pokok + $t->simpanan_wajib + $t->simpanan_sukarela;
                }) + $saldo_awal;

                $item->total_hutang = $item->anggota->pinjamanAktif->sum('sisa_pinjaman');
                $item->cicilan_berjalan = $item->anggota->pinjamanAktif->sum('cicilan_per_bulan');
                $item->saldo_setelah_tarik = $item->total_simpanan - $item->nominal;
            }
        }

        // Global counts for stats cards (always from all data)
        $totalPengambilan = PengambilanSimpanan::where('status', 'pending')->count();
        $totalNominal     = PengambilanSimpanan::where('status', 'pending')->sum('nominal');

        return view('persetujuan.pengambilan', compact('pengambilan_list', 'totalPengambilan', 'totalNominal', 'filterStatus'));
    }

    public function approvePengambilan(Request $request, $id) {
        $pengambilan = PengambilanSimpanan::findOrFail($id);
        
        if ($pengambilan->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        DB::beginTransaction();
        try {
            $anggotaId = $pengambilan->anggota_id;
            
            $totalSukarela = \App\Models\TransaksiSimpanan::where('anggota_id', $anggotaId)->sum('simpanan_sukarela');
            $totalWajib = \App\Models\TransaksiSimpanan::where('anggota_id', $anggotaId)->sum('simpanan_wajib');
            $totalPokok = \App\Models\TransaksiSimpanan::where('anggota_id', $anggotaId)->sum('simpanan_pokok');
            $saldoAwal = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggotaId)->sum('nominal');
            
            $totalSukarela += $saldoAwal; 

            $nominalToDeduct = $pengambilan->nominal;
            $deductSukarela = 0;
            $deductWajib = 0;
            $deductPokok = 0;

            if ($nominalToDeduct <= $totalSukarela) {
                $deductSukarela = $nominalToDeduct;
                $nominalToDeduct = 0;
            } else {
                $deductSukarela = $totalSukarela;
                $nominalToDeduct -= $totalSukarela;
            }

            if ($nominalToDeduct > 0) {
                if ($nominalToDeduct <= $totalWajib) {
                    $deductWajib = $nominalToDeduct;
                    $nominalToDeduct = 0;
                } else {
                    $deductWajib = $totalWajib;
                    $nominalToDeduct -= $totalWajib;
                }
            }

            if ($nominalToDeduct > 0) {
                $deductPokok = $nominalToDeduct; 
            }

            \App\Models\TransaksiSimpanan::create([
                'anggota_id' => $anggotaId,
                'simpanan_pokok' => -$deductPokok,
                'simpanan_wajib' => -$deductWajib,
                'simpanan_sukarela' => -$deductSukarela,
                'transaction_date' => now()->toDateString(),
                'periode' => now()->translatedFormat('F Y'),
                'description' => 'Penarikan Simpanan Disetujui'
            ]);

            $pengambilan->update([
                'status' => 'approved',
                'approved_by' => auth()->id() ?? 1,
                'approved_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Penarikan simpanan disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses penarikan: ' . $e->getMessage());
        }
    }

    public function rejectPengambilan(Request $request, $id) {
        $pengambilan = PengambilanSimpanan::findOrFail($id);
        
        if ($pengambilan->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        $pengambilan->update([
            'status' => 'rejected',
            'alasan_approval' => $request->alasan,
        ]);

        return back()->with('success', 'Pengajuan penarikan ditolak.');
    }
}
