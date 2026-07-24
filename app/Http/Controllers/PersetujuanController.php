<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\Pinjaman;
use App\Models\PinjamanAngsuran;
use App\Models\PembayaranAngsuran;
use App\Models\PengambilanSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersetujuanController extends Controller
{
    public function pinjaman(Request $request) {
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        
        $query = LoanRequest::with([
            'anggota.transaksiSimpanan', 
            'anggota.pinjamanAktif.jenisPinjaman', 
            'jenisPinjaman', 
            'anggota.masterSimpanan',
            'topups.pinjaman'
        ]);
        
        if ($request->filled('jenis')) {
            $query->where('jenis_pinjaman_id', $request->jenis);
        }
        
        // Show all statuses, sort pending first then latest
        $pengajuan_list = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Calculate member financial stats for approval modal if pending
        foreach ($pengajuan_list as $item) {
            if ($item->anggota && $item->status == 'pending') {
                $anggotaId = $item->anggota->id;
                $saldo_awal = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggotaId)->sum('nominal');
                
                $total_simpanan = $item->anggota->transaksiSimpanan->sum(function($t) {
                    return $t->simpanan_pokok + $t->simpanan_wajib + $t->simpanan_sukarela;
                }) + $saldo_awal;

                // Identify refinancing items
                $pelunasanIds = $item->topups->pluck('pinjaman_id')->toArray();
                $totalPelunasan = $item->topups->sum(fn($t) => $t->pinjaman ? $t->pinjaman->sisa_pinjaman : 0);
                
                $pinjaman_berjalan = $item->anggota->pinjamanAktif->sum('sisa_pinjaman');
                $cicilan_saat_ini = $item->anggota->pinjamanAktif->sum('cicilan_per_bulan');

                // Cicilan yang akan hilang karena pelunasan
                $cicilanHilang = $item->anggota->pinjamanAktif
                    ->whereIn('id', $pelunasanIds)
                    ->sum('cicilan_per_bulan');

                // hitung simpanan yang harus dibayar setiap bulan
                $simpananPerbulan = $item->anggota->masterSimpanan ? ($item->anggota->masterSimpanan->simpanan_wajib + $item->anggota->masterSimpanan->simpanan_sukarela) : 0;

                // ── Breakdown cicilan per payment_method (sebelum pengajuan ini) ──
                $cicilan_gaji_existing = $item->anggota->pinjamanAktif
                    ->where('payment_method', 'gaji')
                    ->whereNotIn('id', $pelunasanIds)
                    ->sum('cicilan_per_bulan');

                $cicilan_mandiri_existing = $item->anggota->pinjamanAktif
                    ->where('payment_method', 'mandiri')
                    ->whereNotIn('id', $pelunasanIds)
                    ->sum('cicilan_per_bulan');

                // Tambahkan cicilan baru sesuai payment_method pengajuan ini
                $newMethod = $item->payment_method ?? 'gaji';
                $cicilan_gaji_baru    = $cicilan_gaji_existing    + ($newMethod === 'gaji'    ? $item->cicilan_per_bulan : 0);
                $cicilan_mandiri_baru = $cicilan_mandiri_existing + ($newMethod === 'mandiri' ? $item->cicilan_per_bulan : 0);

                $item->total_simpanan_saat_ini = $total_simpanan;
                $item->pinjaman_berjalan_saat_ini = $pinjaman_berjalan;
                $item->cicilan_saat_ini = $cicilan_saat_ini;
                $item->simpanan_perbulan = $simpananPerbulan;

                // Breakdown per metode
                $item->cicilan_gaji_existing    = $cicilan_gaji_existing;
                $item->cicilan_mandiri_existing = $cicilan_mandiri_existing;
                $item->cicilan_gaji_baru        = $cicilan_gaji_baru;
                $item->cicilan_mandiri_baru     = $cicilan_mandiri_baru;

                // Info Pelunasan
                $item->total_pelunasan_pasti = $totalPelunasan;
                $item->net_cair = $item->jumlah_pengajuan - $totalPelunasan;
                $item->pelunasan_ids = $pelunasanIds; // Keep array for UI marker

                // Corrected: (Old Total - Settled) + New Loan + Monthly Savings
                $item->total_cicilan_baru = ($cicilan_saat_ini - $cicilanHilang) + $item->cicilan_per_bulan + $simpananPerbulan;
            }
        }
        
        $totalPengajuan = $pengajuan_list->where('status', 'pending')->count();
        $totalNominal = $pengajuan_list->where('status', 'pending')->sum('jumlah_pengajuan');
        // dd($pengajuan_list);
        return view('persetujuan.pinjaman', compact('pengajuan_list', 'jenisPinjamanList', 'totalPengajuan', 'totalNominal'));
    }

    public function pengambilan(Request $request) {
        $filterStatus = $request->get('status', 'semua');

        $query = PengambilanSimpanan::with([
            'anggota.transaksiSimpanan',
            'anggota.pinjamanAktif.jenisPinjaman',
            'anggota.masterSimpanan',
            'settlements.pinjaman.jenisPinjaman'
        ])->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
          ->orderBy('created_at', 'desc');

        $pengambilan_list = $query->get();

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

                // Settlement info
                $item->total_pelunasan = $item->settlements->sum(fn($s) => $s->pinjaman ? $s->pinjaman->sisa_pinjaman : 0);
                $item->net_payout = $item->nominal - $item->total_pelunasan;
                $item->pelunasan_ids = $item->settlements->pluck('pinjaman_id')->toArray();

                $item->saldo_setelah_tarik = $item->total_simpanan - $item->nominal;
            }
        }

        // Global counts for stats cards (always from all data)
        $totalPengambilan = PengambilanSimpanan::where('status', 'pending')->count();
        $totalNominal     = PengambilanSimpanan::where('status', 'pending')->sum('nominal');

        return view('persetujuan.pengambilan', compact('pengambilan_list', 'totalPengambilan', 'totalNominal', 'filterStatus'));
    }

    public function approvePengambilan(Request $request, $id) {
        $pengambilan = PengambilanSimpanan::with('settlements.pinjaman')->findOrFail($id);
        
        if ($pengambilan->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        DB::beginTransaction();
        try {
            $anggotaId = $pengambilan->anggota_id;
            
            // --- 1. DEDUCT SAVINGS ---
            $saldoAwalModel = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggotaId)->first();
            $saldoAwalPokok = $saldoAwalModel ? $saldoAwalModel->pokok : 0;
            $saldoAwalWajib = $saldoAwalModel ? $saldoAwalModel->wajib : 0;
            $saldoAwalSukarela = $saldoAwalModel ? $saldoAwalModel->sukarela : 0;

            $totalSukarela = \App\Models\TransaksiSimpanan::where('anggota_id', $anggotaId)->sum('simpanan_sukarela') + $saldoAwalSukarela;
            $totalWajib = \App\Models\TransaksiSimpanan::where('anggota_id', $anggotaId)->sum('simpanan_wajib') + $saldoAwalWajib;
            $totalPokok = \App\Models\TransaksiSimpanan::where('anggota_id', $anggotaId)->sum('simpanan_pokok') + $saldoAwalPokok; 

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
                'description' => 'Penarikan Simpanan Disetujui' . ($pengambilan->settlements->count() > 0 ? ' (Pelunasan Pinjaman)' : '')
            ]);

            // --- 2. PROCESS LOAN SETTLEMENTS (If any) ---
            foreach ($pengambilan->settlements as $settlement) {
                $oldPinjaman = $settlement->pinjaman;
                if ($oldPinjaman && $oldPinjaman->status === 'berjalan') {
                    $unpaidAngsurans = PinjamanAngsuran::where('loan_id', $oldPinjaman->id)
                        ->where('status', 'belum_bayar')
                        ->get();

                    foreach ($unpaidAngsurans as $ang) {
                        // Create payment record
                        $pembayaran = PembayaranAngsuran::create([
                            'type_bayar'    => 'pelunasan',
                            'jumlah'        => $ang->jumlah_tagihan,
                            'user_id'       => $oldPinjaman->user_id,
                            'loan_id'       => $oldPinjaman->id,
                            'angsuran_id'   => $ang->id,
                            'tanggal_bayar' => now(),
                        ]);

                        // Mark installment as paid
                        $ang->update([
                            'status'         => 'sudah_bayar',
                            'payment_id'     => $pembayaran->id,
                            'jumlah_dibayar' => $ang->jumlah_tagihan,
                            'tanggal_bayar'  => now(),
                            'paid_at'        => now(),
                        ]);
                    }

                    // Close the loan
                    $oldPinjaman->update([
                        'total_terbayar' => $oldPinjaman->total_pinjaman,
                        'sisa_pinjaman'  => 0,
                        'sisa_tenor'     => 0,
                        'status'         => 'lunas'
                    ]);
                }
            }

            // --- 3. FINALIZE WITHDRAWAL STATUS ---
            $pengambilan->update([
                'status' => 'approved',
                'approved_by' => auth()->id() ?? 1,
                'approved_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Penarikan simpanan disetujui' . ($pengambilan->settlements->count() > 0 ? ' dan pinjaman terkait telah dilunasi.' : '.'));
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

    /**
     * Bulk approve multiple withdrawal requests via checkbox selection.
     */
    public function approvePengambilanBulk(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:pengambilan_simpanan,id',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->ids as $id) {
            try {
                $this->approvePengambilan($request, $id);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID #{$id}: " . $e->getMessage();
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return back()->with('success', "{$successCount} pengajuan penarikan simpanan berhasil disetujui.");
        } elseif ($successCount > 0) {
            return back()->with('success', "{$successCount} disetujui, " . count($errors) . " gagal.");
        } else {
            return back()->with('error', 'Gagal menyetujui pengajuan: ' . implode(', ', $errors));
        }
    }

    public function approvePinjaman($id) {
        $loanRequest = LoanRequest::with('topups')->findOrFail($id);
        
        if ($loanRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        DB::beginTransaction();
        try {
            $loanRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id() ?? 1,
                'approved_at' => now(),
            ]);

            // --- CALCULATE REFINANCING VALUES ---
            $totalPelunasan = 0;
            foreach ($loanRequest->topups as $topup) {
                $oldP = \App\Models\Pinjaman::find($topup->pinjaman_id);
                if ($oldP) {
                    $totalPelunasan += $oldP->sisa_pinjaman;
                }
            }

            $pinjaman = \App\Models\Pinjaman::create([
                'loan_request_id'   => $loanRequest->id,
                'user_id'           => $loanRequest->user_id,
                'jenis_pinjaman_id' => $loanRequest->jenis_pinjaman_id,
                'jumlah_pinjaman'   => $loanRequest->jumlah_pengajuan,
                'tenor'             => $loanRequest->tenor,
                'bunga'             => $loanRequest->bunga,
                'total_bunga'       => $loanRequest->total_bunga,
                'total_pinjaman'    => $loanRequest->total_pinjaman,
                'cicilan_per_bulan' => $loanRequest->cicilan_per_bulan,
                'jumlah_cair'       => $loanRequest->jumlah_pengajuan - $totalPelunasan,
                'potongan_pelunasan'=> $totalPelunasan,
                'sisa_pinjaman'     => $loanRequest->total_pinjaman,
                'sisa_tenor'        => $loanRequest->tenor,
                'payment_method'    => $loanRequest->payment_method,
                'total_terbayar'    => 0,
                'status'            => 'berjalan',
                'tanggal_mulai'     => now(),
                'tanggal_selesai'   => now()->addMonths($loanRequest->tenor)
            ]);

            // Generate installments for the NEW loan
            for ($i = 1; $i <= $pinjaman->tenor; $i++) {
                PinjamanAngsuran::create([
                    'loan_id'             => $pinjaman->id,
                    'angsuran_ke'         => $i,
                    'tanggal_jatuh_tempo' => null,
                    'jumlah_tagihan'      => $pinjaman->cicilan_per_bulan,
                    'jumlah_dibayar'      => 0,
                    'status'              => 'belum_bayar',
                ]);
            }

            // --- REFINANCING LOGIC (PELUNASAN PINJAMAN LAMA) ---
            foreach ($loanRequest->topups as $topup) {
                $oldPinjaman = Pinjaman::find($topup->pinjaman_id);
                if ($oldPinjaman) {
                    $unpaidAngsurans = PinjamanAngsuran::where('loan_id', $oldPinjaman->id)
                        ->where('status', 'belum_bayar')
                        ->get();

                    foreach ($unpaidAngsurans as $ang) {
                        // 1. Create payment record
                        $pembayaran = PembayaranAngsuran::create([
                            'type_bayar'    => 'pelunasan',
                            'jumlah'        => $ang->jumlah_tagihan,
                            'user_id'       => $oldPinjaman->user_id,
                            'loan_id'       => $oldPinjaman->id,
                            'angsuran_id'   => $ang->id,
                            'tanggal_bayar' => now(),
                        ]);

                        // 2. Mark installment as paid
                        $ang->update([
                            'status'         => 'sudah_bayar',
                            'payment_id'     => $pembayaran->id,
                            'jumlah_dibayar' => $ang->jumlah_tagihan,
                            'tanggal_bayar'  => now(),
                            'paid_at'        => now(),
                        ]);
                    }

                    // 3. Close the old loan
                    $oldPinjaman->update([
                        'total_terbayar' => $oldPinjaman->total_pinjaman,
                        'sisa_pinjaman'  => 0,
                        'sisa_tenor'     => 0,
                        'status'         => 'lunas'
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Pengajuan berhasil disetujui dan pinjaman lama (jika ada) telah dilunasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui pengajuan: ' . $e->getMessage());
        }
    }

    public function rejectPinjaman(Request $request, $id) {
        $loanRequest = LoanRequest::findOrFail($id);
        
        if ($loanRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        $loanRequest->update([
            'status'            => 'rejected',
            'alasan_penolakan'  => $request->alasan,
            'rejected_at'       => now(),
        ]);

        return back()->with('success', 'Pengajuan pinjaman ditolak.');
    }

    /**
     * Bulk approve multiple loan requests via checkbox selection.
     */
    public function approvePinjamanBulk(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:loan_requests,id',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->ids as $id) {
            try {
                $this->approvePinjaman($id);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID #{$id}: " . $e->getMessage();
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return back()->with('success', "{$successCount} pengajuan pinjaman berhasil disetujui.");
        } elseif ($successCount > 0) {
            return back()->with('success', "{$successCount} disetujui, " . count($errors) . " gagal.");
        } else {
            return back()->with('error', 'Gagal menyetujui pengajuan: ' . implode(', ', $errors));
        }
    }

    /**
     * Bulk approve all pending loan requests marked as NORMAL in keterangan.
     */
    public function approveBulkNormal(Request $request)
    {
        $normalRequests = LoanRequest::where('status', 'pending')
            ->where(function($q) {
                $q->where('keterangan', 'NORMAL')
                  ->orWhere('keterangan', 'LIKE', '%NORMAL%')
                  ->orWhereNull('keterangan');
            })
            ->pluck('id');

        if ($normalRequests->isEmpty()) {
            return back()->with('error', 'Tidak ada pengajuan pinjaman berstatus NORMAL yang belum disetujui.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($normalRequests as $id) {
            try {
                $this->approvePinjaman($id);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID #{$id}: " . $e->getMessage();
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return back()->with('success', "Berhasil menyetujui {$successCount} pengajuan pinjaman berstatus NORMAL!");
        } elseif ($successCount > 0) {
            return back()->with('success', "{$successCount} pengajuan NORMAL disetujui, " . count($errors) . " gagal.");
        } else {
            return back()->with('error', 'Gagal menyetujui pengajuan NORMAL: ' . implode(', ', $errors));
        }
    }
}
