<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenagihanBill;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\PinjamanAngsuran;
use Illuminate\Support\Facades\DB;

class PenagihanController extends Controller
{
    public function generator(Request $request)
    {
        $bills = PenagihanBill::latest()->paginate(10);
        $anggotaAktif = Anggota::whereIn('status_anggota', ['active', 'aktif'])->count();
        $pinjamanAktif = Pinjaman::where('status', 'berjalan')->count();
        
        return view('penagihan.tagihan_generator', compact('bills', 'anggotaAktif', 'pinjamanAktif'));
    }

    public function index(Request $request)
    {
        $availableBills = PenagihanBill::orderBy('tgl_generate', 'desc')->get();
        
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('n')); // n is numeric month without leading zeros

        $currentBill = PenagihanBill::whereYear('tgl_generate', $selectedYear)
            ->whereMonth('tgl_generate', $selectedMonth)
            ->first();
            
        // If no select provided in request, but we have bills, default to latest bill's year and month
        if (!$request->has('year') && !$request->has('month') && $availableBills->isNotEmpty()) {
            $latestTgl = \Carbon\Carbon::parse($availableBills->first()->tgl_generate);
            $selectedYear = $latestTgl->format('Y');
            $selectedMonth = $latestTgl->format('n');
            $currentBill = $availableBills->first();
        }
            
        $details = collect();
        if ($currentBill) {
            $details = \App\Models\PenagihanBillDetail::with('anggota')
                ->where('penagihan_bill_id', $currentBill->id)
                ->get();
        }

        return view('penagihan.index', compact('availableBills', 'selectedYear', 'selectedMonth', 'currentBill', 'details'));
    }

    public function storeGenerate(Request $request)
    {
        $request->validate([
            'periode' => 'required',
            'tanggal_generate' => 'required|date',
        ]);

        $periodeName = date('F Y', strtotime($request->periode));
        
        $tagihan = PenagihanBill::where('periode', $periodeName)->first();
        if ($tagihan) {
            return back()->with('error', 'Tagihan untuk periode ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $bill = PenagihanBill::create([
                'periode' => $periodeName,
                'tgl_generate' => $request->tanggal_generate,
                'total_amount' => 0,
                'status' => 'Draft',
                'type' => 'Gabungan', 
                'keterangan' => 'Tagihan Keseluruhan'
            ]);

            $anggotas = Anggota::with(['masterSimpanan', 'pinjaman' => function($q) {
                $q->where('status', 'berjalan');
            }, 'pinjaman.angsuran' => function($q) {
                $q->where('status', 'belum_bayar')->orderBy('angsuran_ke', 'asc');
            }])->whereIn('status_anggota', ['active', 'aktif'])->get();

            $tagihanTotal = 0;

            foreach ($anggotas as $anggota) {
                // 1. Simpanan
                $simpananPokok = $anggota->masterSimpanan->simpanan_pokok ?? 0;
                if ($anggota->masterSimpanan && $anggota->masterSimpanan->pokok_terbayar) {
                    $simpananPokok = 0;
                }
                $simpananWajib = $anggota->masterSimpanan->simpanan_wajib ?? 0;
                $simpananSukarela = $anggota->masterSimpanan->simpanan_sukarela ?? 0;
                $jumlahSimpanan = $simpananPokok + $simpananWajib + $simpananSukarela;

                // 2. Pinjaman
                $jumlahPinjaman = 0;
                $angsuranToLink = [];
                
                foreach ($anggota->pinjaman as $pinjam) {
                    $firstUnpaid = $pinjam->angsuran->first(); // get the earliest unpaid angsuran
                    if ($firstUnpaid) {
                        $jumlahPinjaman += $firstUnpaid->jumlah_tagihan;
                        $angsuranToLink[] = $firstUnpaid->id;
                    }
                }

                $totalPotongan = $jumlahSimpanan + $jumlahPinjaman;

                if ($totalPotongan > 0) {
                    \App\Models\PenagihanBillDetail::create([
                        'penagihan_bill_id' => $bill->id,
                        'anggota_id' => $anggota->id,
                        'simpanan_pokok' => $simpananPokok,
                        'simpanan_wajib' => $simpananWajib,
                        'simpanan_sukarela' => $simpananSukarela,
                        'jumlah_simpanan' => $jumlahSimpanan,
                        'jumlah_pinjaman' => $jumlahPinjaman,
                        'total_potongan' => $totalPotongan,
                        'status' => 'Belum Lunas'
                    ]);
                    
                    // Link angsuran if any
                    if (count($angsuranToLink) > 0) {
                        PinjamanAngsuran::whereIn('id', $angsuranToLink)->update([
                            'penagihan_bill_id' => $bill->id
                        ]);
                    }

                    $tagihanTotal += $totalPotongan;
                }
            }

            $bill->update(['total_amount' => $tagihanTotal]);

            DB::commit();

            return redirect()->route('penagihan.generator')->with('success', 'Tagihan Gabungan berhasil di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $tagihan = PenagihanBill::with(['details' => function($q) {
            $q->withTrashed()->with('anggota');
        }])->findOrFail($id);

        return view('penagihan.show', compact('tagihan'));
    }

    public function destroyDetail(Request $request, $id)
    {
        $detail = \App\Models\PenagihanBillDetail::findOrFail($id);
        
        // Cannot delete if paid
        if ($detail->status == 'Lunas') {
            return back()->with('error', 'Detail tagihan yang sudah lunas tidak dapat dihapus.');
        }

        // Unlink angsuran if any
        \App\Models\PinjamanAngsuran::where('penagihan_bill_id', $detail->penagihan_bill_id)
            ->whereHas('pinjaman', function($q) use ($detail) {
                $q->where('user_id', $detail->anggota_id);
            })
            ->update(['penagihan_bill_id' => null]);

        // Recalculate bill total
        $bill = PenagihanBill::findOrFail($detail->penagihan_bill_id);
        $bill->update([
            'total_amount' => $bill->total_amount - $detail->total_potongan
        ]);

        $detail->delete();

        return back()->with('success', 'Detail tagihan berhasil dihapus (Soft Delete) dan list angsuran pinjaman di-unlink.');
    }

    public function exportExcel($id)
    {
        $tagihan = PenagihanBill::findOrFail($id);
        $fileName = 'Export_Tagihan_' . str_replace(' ', '_', $tagihan->periode) . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PenagihanBillExport($id), $fileName);
    }

    public function bayar(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:penagihan_bills,id',
            'detail_ids' => 'required|array|min:1',
            'detail_ids.*' => 'exists:penagihan_bill_details,id',
            'tanggal_transaksi' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $tagihan = PenagihanBill::findOrFail($request->tagihan_id);
            $details = \App\Models\PenagihanBillDetail::with('anggota')
                ->whereIn('id', $request->detail_ids)
                ->where('penagihan_bill_id', $tagihan->id)
                ->where('status', '!=', 'Lunas')
                ->get();

            foreach ($details as $detail) {
                // A. Bayar Simpanan
                if ($detail->jumlah_simpanan > 0) {
                    \App\Models\TransaksiSimpanan::create([
                        'anggota_id' => $detail->anggota_id,
                        'simpanan_pokok' => $detail->simpanan_pokok,
                        'simpanan_wajib' => $detail->simpanan_wajib,
                        'simpanan_sukarela' => $detail->simpanan_sukarela,
                        'transaction_date' => $request->tanggal_transaksi,
                        'periode' => $tagihan->periode,
                        'description' => 'Pembayaran Simpanan Periode ' . $tagihan->periode
                    ]);

                    if ($detail->simpanan_pokok > 0) {
                        \App\Models\MasterSimpanan::where('anggota_id', $detail->anggota_id)->update(['pokok_terbayar' => true]);
                    }
                }

                // B. Bayar Pinjaman
                if ($detail->jumlah_pinjaman > 0) {
                    $angsurans = PinjamanAngsuran::with('pinjaman')
                        ->where('penagihan_bill_id', $tagihan->id)
                        ->whereHas('pinjaman', function($q) use ($detail) {
                            $q->where('user_id', $detail->anggota_id);
                        })
                        ->where('status', 'belum_bayar')
                        ->get();

                    foreach ($angsurans as $angsuran) {
                        $pembayaran = \App\Models\PembayaranAngsuran::create([
                            'type_bayar' => 'normal',
                            'jumlah' => $angsuran->jumlah_tagihan,
                            'user_id' => $angsuran->pinjaman->user_id,
                            'loan_id' => $angsuran->loan_id,
                            'angsuran_id' => $angsuran->id,
                            'tanggal_bayar' => $request->tanggal_transaksi,
                        ]);

                        $angsuran->update([
                            'status' => 'sudah_bayar',
                            'jumlah_dibayar' => $angsuran->jumlah_tagihan,
                            'tanggal_bayar' => $request->tanggal_transaksi,
                            'paid_at' => $request->tanggal_transaksi,
                            'payment_id' => $pembayaran->id,
                        ]);

                        $pinjaman = $angsuran->pinjaman;
                        $pinjaman->update([
                            'total_terbayar' => $pinjaman->total_terbayar + $angsuran->jumlah_tagihan,
                            'sisa_pinjaman' => $pinjaman->sisa_pinjaman - $angsuran->jumlah_tagihan,
                            'sisa_tenor' => $pinjaman->sisa_tenor - 1,
                        ]);

                        $sisaUnpaid = PinjamanAngsuran::where('loan_id', $pinjaman->id)->where('status', 'belum_bayar')->count();
                        if ($sisaUnpaid == 0) {
                            $pinjaman->update(['status' => 'lunas']);
                        }
                    }
                }

                $detail->update(['status' => 'Lunas']);
            }

            // C. Update status bill
            $totalDetails = \App\Models\PenagihanBillDetail::where('penagihan_bill_id', $tagihan->id)->count();
            $lunasDetails = \App\Models\PenagihanBillDetail::where('penagihan_bill_id', $tagihan->id)->where('status', 'Lunas')->count();

            if ($lunasDetails == $totalDetails) {
                $tagihan->update(['status' => 'Paid']);
            } elseif ($lunasDetails > 0) {
                $tagihan->update(['status' => 'Partial']);
            }

            DB::commit();
            return back()->with('success', 'Pembayaran Tagihan Gabungan berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
