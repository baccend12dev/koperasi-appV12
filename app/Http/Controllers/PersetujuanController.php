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
        
        $query = LoanRequest::with('anggota', 'jenisPinjaman');
        
        if ($request->filled('jenis')) {
            $query->where('jenis_pinjaman_id', $request->jenis);
        }
        
        // Show all statuses, sort pending first
        $pengajuan_list = $query->get();
        // dd($pengajuan_list);
        
        $totalPengajuan = $pengajuan_list->where('status', 'pending')->count();
        $totalNominal = $pengajuan_list->where('status', 'pending')->sum('jumlah_pengajuan');
        
        return view('persetujuan.pinjaman', compact('pengajuan_list', 'jenisPinjamanList', 'totalPengajuan', 'totalNominal'));
    }

    public function pengambilan(Request $request) {
        $query = PengambilanSimpanan::with('anggota');
        $pengambilan_list = $query->get();
        // dd($pengambilan_list);
        $totalPengambilan = $pengambilan_list->where('status', 'pending')->count();
        $totalNominal = $pengambilan_list->where('status', 'pending')->sum('nominal');

        return view('persetujuan.pengambilan', compact('pengambilan_list', 'totalPengambilan', 'totalNominal'));
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
