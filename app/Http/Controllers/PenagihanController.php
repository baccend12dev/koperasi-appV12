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
        $billsGabungan = PenagihanBill::where('type', 'Gabungan')->latest()->paginate(10, ['*'], 'gabungan');
        $billsMandiri  = PenagihanBill::where('type', 'Mandiri')->latest()->paginate(10, ['*'], 'mandiri');
        $anggotaAktif  = Anggota::whereIn('status_anggota', ['active', 'aktif'])->count();
        $pinjamanAktif = Pinjaman::where('status', 'berjalan')->count();

        return view('penagihan.tagihan_generator', compact('billsGabungan', 'billsMandiri', 'anggotaAktif', 'pinjamanAktif'));
    }

    public function index(Request $request)
    {
        // All bills for sidebar — unique by tgl_generate (deduplicated periods)
        $availableBills = PenagihanBill::orderBy('tgl_generate', 'desc')->get()
            ->unique(fn($b) => \Carbon\Carbon::parse($b->tgl_generate)->format('Y-m'));

        $selectedYear  = $request->input('year',  date('Y'));
        $selectedMonth = $request->input('month', date('n'));

        // Default to latest period when no filter
        if (!$request->has('year') && !$request->has('month') && $availableBills->isNotEmpty()) {
            $latestTgl    = \Carbon\Carbon::parse($availableBills->first()->tgl_generate);
            $selectedYear  = $latestTgl->format('Y');
            $selectedMonth = $latestTgl->format('n');
        }

        // Fetch each type separately for the selected period
        $billGabungan = PenagihanBill::where('type', 'Gabungan')
            ->whereYear('tgl_generate', $selectedYear)
            ->whereMonth('tgl_generate', $selectedMonth)
            ->first();

        $billMandiri = PenagihanBill::where('type', 'Mandiri')
            ->whereYear('tgl_generate', $selectedYear)
            ->whereMonth('tgl_generate', $selectedMonth)
            ->first();

        $detailsGabungan = $billGabungan
            ? \App\Models\PenagihanBillDetail::with('anggota')
                ->where('penagihan_bill_id', $billGabungan->id)
                ->orderBy('jumlah_pinjaman', 'desc')->get()
            : collect();

        $detailsMandiri = $billMandiri
            ? \App\Models\PenagihanBillDetail::with('anggota')
                ->where('penagihan_bill_id', $billMandiri->id)
                ->orderBy('jumlah_pinjaman', 'desc')->get()
            : collect();

        return view('penagihan.index', compact(
            'availableBills', 'selectedYear', 'selectedMonth',
            'billGabungan', 'billMandiri',
            'detailsGabungan', 'detailsMandiri'
        ));
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

                // 2. Pinjaman — hanya yang payment_method BUKAN mandiri (gaji / null)
                $jumlahPinjaman = 0;
                $angsuranToLink = [];

                foreach ($anggota->pinjaman as $pinjam) {
                    // Lewati pinjaman mandiri — akan ditangani menu terpisah
                    if (($pinjam->payment_method ?? 'gaji') === 'mandiri') {
                        continue;
                    }
                    $firstUnpaid = $pinjam->angsuran->first();
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

    public function storeGenerateMandiri(Request $request)
    {
        $request->validate([
            'periode'          => 'required',
            'tanggal_generate' => 'required|date',
        ]);

        $periodeName = date('F Y', strtotime($request->periode));

        // Cek duplikat untuk type Mandiri saja
        $exists = PenagihanBill::where('periode', $periodeName)->where('type', 'Mandiri')->first();
        if ($exists) {
            return back()->with('error', 'Tagihan Mandiri untuk periode ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $bill = PenagihanBill::create([
                'periode'      => $periodeName,
                'tgl_generate' => $request->tanggal_generate,
                'total_amount' => 0,
                'status'       => 'Draft',
                'type'         => 'Mandiri',
                'keterangan'   => 'Tagihan Pinjaman Mandiri',
            ]);

            // Hanya anggota dengan pinjaman aktif payment_method = mandiri
            $anggotas = Anggota::with(['pinjaman' => function ($q) {
                $q->where('status', 'berjalan')->where('payment_method', 'mandiri');
            }, 'pinjaman.angsuran' => function ($q) {
                $q->where('status', 'belum_bayar')->orderBy('angsuran_ke', 'asc');
            }])->whereIn('status_anggota', ['active', 'aktif'])->get();

            $tagihanTotal = 0;

            foreach ($anggotas as $anggota) {
                $jumlahPinjaman = 0;
                $angsuranToLink = [];

                foreach ($anggota->pinjaman as $pinjam) {
                    $firstUnpaid = $pinjam->angsuran->first();
                    if ($firstUnpaid) {
                        $jumlahPinjaman += $firstUnpaid->jumlah_tagihan;
                        $angsuranToLink[] = $firstUnpaid->id;
                    }
                }

                if ($jumlahPinjaman > 0) {
                    \App\Models\PenagihanBillDetail::create([
                        'penagihan_bill_id' => $bill->id,
                        'anggota_id'        => $anggota->id,
                        'simpanan_pokok'    => 0,
                        'simpanan_wajib'    => 0,
                        'simpanan_sukarela' => 0,
                        'jumlah_simpanan'   => 0,
                        'jumlah_pinjaman'   => $jumlahPinjaman,
                        'total_potongan'    => $jumlahPinjaman,
                        'status'            => 'Belum Lunas',
                    ]);

                    if (count($angsuranToLink) > 0) {
                        PinjamanAngsuran::whereIn('id', $angsuranToLink)->update([
                            'penagihan_bill_id' => $bill->id,
                        ]);
                    }

                    $tagihanTotal += $jumlahPinjaman;
                }
            }

            $bill->update(['total_amount' => $tagihanTotal]);

            DB::commit();
            return redirect()->route('penagihan.generator')
                ->with('success', 'Tagihan Mandiri ' . $periodeName . ' berhasil di-generate (' . $tagihanTotal . ' total).')
                ->withFragment('tab-mandiri');
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
        $tagihan->details = $tagihan->details->sortByDesc('jumlah_pinjaman');

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

    public function invoice(Request $request)
    {
        $invoices = \App\Models\InvoicePeriod::withCount('details')->latest()->paginate(10);
        return view('penagihan.invoice_period', compact('invoices'));
    }

    public function storeGenerateInvoice(Request $request)
    {
        $request->validate([
            'periode' => 'required|date_format:Y-m',
        ]);

        $periode = $request->periode;

        if (\App\Models\InvoicePeriod::where('periode', $periode)->exists()) {
            return back()->with('error', 'Invoice untuk periode ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $invoice = \App\Models\InvoicePeriod::create([
                'periode' => $periode,
                'status' => 'generated',
                'generated_at' => now(),
                'created_by' => auth()->id() ?? null,
            ]);

            $pinjamans = Pinjaman::with('jenisPinjaman')->where('status', 'berjalan')->get();

            $totalGaji = 0;
            $totalMandiri = 0;

            foreach ($pinjamans as $pinjam) {
                $cicilan_ke = $pinjam->tenor - $pinjam->sisa_tenor + 1;
                if ($cicilan_ke > $pinjam->tenor) {
                    $cicilan_ke = $pinjam->tenor;
                }

                $cicilanAmount = $pinjam->cicilan_per_bulan;
                $paymentMethod = strtolower($pinjam->payment_method) === 'mandiri' ? 'mandiri' : 'gaji';

                \App\Models\InvoiceDetail::create([
                    'invoice_period_id' => $invoice->id,
                    'user_id' => $pinjam->user_id,
                    'loan_id' => $pinjam->id,
                    'payment_method' => $paymentMethod,
                    'jenis_pinjaman' => $pinjam->jenisPinjaman->nama_jenis_pinjaman ?? 'Pinjaman',
                    'cicilan_ke' => $cicilan_ke,
                    'tenor' => $pinjam->tenor,
                    'cicilan_amount' => $cicilanAmount,
                    'sisa_pinjaman' => $pinjam->sisa_pinjaman,
                    'sisa_tenor' => $pinjam->sisa_tenor,
                    'status' => 'unpaid',
                ]);

                if ($paymentMethod === 'mandiri') {
                    $totalMandiri += $cicilanAmount;
                } else {
                    $totalGaji += $cicilanAmount;
                }
            }

            $invoice->update([
                'total_gaji' => $totalGaji,
                'total_mandiri' => $totalMandiri,
                'total_amount' => $totalGaji + $totalMandiri,
            ]);

            DB::commit();
            return back()->with('success', 'Invoice periode ' . $periode . ' berhasil digenerate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showInvoice($id)
    {
        $invoice = \App\Models\InvoicePeriod::findOrFail($id);
        $details = \App\Models\InvoiceDetail::with('anggota')
            ->where('invoice_period_id', $id)
            ->orderBy('user_id')
            ->get();

        return view('penagihan.invoice_show', compact('invoice', 'details'));
    }
}
