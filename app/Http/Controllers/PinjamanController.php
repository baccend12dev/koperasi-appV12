<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\LoanRequest;
use App\Models\PinjamanAngsuran;
use App\Models\PembayaranAngsuran;
use App\Models\LoanRequestTopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'semua');
        $search = $request->input('search');
        $departemen_id = $request->input('departemen_id');

        $query = \App\Models\Anggota::with(['departemen', 'pinjaman' => function($q) use ($status) {
            if ($status !== 'semua') {
                $q->where('status', $status);
            }
            $q->with('jenisPinjaman');
        }])->whereHas('pinjaman', function($q) use ($status) {
            if ($status !== 'semua') {
                $q->where('status', $status);
            }
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                // Using like for search compatibility
                $q->whereRaw("LOWER(nama_anggota) LIKE ?", ['%' . strtolower($search) . '%'])
                  ->orWhereRaw("LOWER(nik) LIKE ?", ['%' . strtolower($search) . '%']);
            });
        }

        if ($departemen_id) {
            $query->where('departemen_id', $departemen_id);
        }

        $anggotaList = $query->paginate(15)->withQueryString();
        $departemens = \App\Models\Departemen::all();

        return view('pinjaman.index', compact('anggotaList', 'status', 'search', 'departemen_id', 'departemens'));
    }

    public function pengajuan(Request $request) { 
        $pengajuan_list = LoanRequest::with('anggota', 'jenisPinjaman')->get();
        // dd($pengajuan_list);
        if ($request->filled('tahun')) {
            $pengajuan_list = $pengajuan_list->filter(function($item) use ($request) {
                return date('Y', strtotime($item->created_at)) == $request->tahun;
            });
        }

        if ($request->filled('bulan')) {
            $pengajuan_list = $pengajuan_list->filter(function($item) use ($request) {
                return date('m', strtotime($item->created_at)) == sprintf('%02d', $request->bulan);
            });
        }

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $pengajuan_list = $pengajuan_list->filter(function($item) use ($q) {
                return str_contains(strtolower($item->anggota->nama_anggota), $q) || str_contains(strtolower($item->anggota->nik), $q);
            });
        }

        if ($request->filled('jenis')) {
            $jenisRaw = \App\Models\MasterJenisPinjaman::find($request->jenis);
            if ($jenisRaw) {
                $pengajuan_list = $pengajuan_list->filter(function($item) use ($jenisRaw) {
                    return $item->jenis_pinjaman_id == $jenisRaw->id;
                });
            }
        }

        $years = [date('Y') - 1, date('Y'), date('Y') + 1];
        sort($years);
        
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::whereNull('parent_id')->get();

        if ($request->get('export') == 'excel') {
            $filename = "pengajuan_pinjaman_" . date('Ymd_His') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['No', 'Tanggal', 'Nama', 'NIK', 'Jenis Pinjaman', 'Jumlah', 'Tenor', 'Status'];

            $callback = function() use($pengajuan_list, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $no = 1;
                foreach ($pengajuan_list as $item) {
                    $row = [
                        $no++,
                        $item->tanggal,
                        $item->nama,
                        $item->nik,
                        $item->jenis_pinjaman,
                        $item->jumlah,
                        $item->tenor . " Bulan",
                        $item->status
                    ];
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('pinjaman.pengajuan', compact('pengajuan_list', 'years', 'jenisPinjamanList')); 
    }

    public function aktif(Request $request) {
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        
        $query = \App\Models\Pinjaman::with(['anggota', 'jenisPinjaman']);
        
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', $request->bulan);
        }

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $query->whereHas('anggota', function($qBuilder) use ($q) {
                $qBuilder->where('nama_anggota', 'ilike', '%'.$q.'%')
                         ->orWhere('nik', 'ilike', '%'.$q.'%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pinjaman_id', $request->jenis);
        }

        $pinjaman_list = $query->orderBy('created_at', 'desc')->get();
        
        $years = \App\Models\Pinjaman::selectRaw('EXTRACT(YEAR FROM tanggal_mulai) as year')
            ->whereNotNull('tanggal_mulai')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $years = [date('Y')];
        }

        if ($request->get('export') == 'excel') {
            $filename = "pinjaman_aktif_" . date('Ymd_His') . ".xls";
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $filterTahun = $request->tahun;
            $filterBulan = $request->bulan;
            $filterStatus = $request->status;

            $html = view('pinjaman.export_aktif_excel', compact('pinjaman_list', 'filterTahun', 'filterBulan', 'filterStatus'))->render();

            return response($html, 200, $headers);
        }

        return view('pinjaman.aktif', compact('pinjaman_list', 'jenisPinjamanList', 'years'));
    }

    public function showAktif($id) {
        $pinjaman = Pinjaman::with(['anggota', 'jenisPinjaman', 'angsuran' => function($q) {
            $q->orderBy('angsuran_ke', 'asc');
        }])->findOrFail($id);

        $angsuranLunas = $pinjaman->angsuran->where('status', 'sudah_bayar')->count();
        $totalAngsuran = $pinjaman->angsuran->count();
        $progressPersen = $totalAngsuran > 0 ? round(($angsuranLunas / $totalAngsuran) * 100) : 0;

        return view('pinjaman.aktif_show', compact('pinjaman', 'angsuranLunas', 'totalAngsuran', 'progressPersen'));
    }


    public function create() {
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        return view('pinjaman.create', compact('jenisPinjamanList'));
    }

    public function storePengajuan(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:anggotas,id',
            'jenis_pinjaman_id' => 'required|exists:master_jenis_pinjaman,id',
            'jumlah_pengajuan' => 'required|numeric|min:100000',
            'tenor' => 'required|numeric|min:1',
            'bunga' => 'required|numeric|min:0',
            'pelunasan_ids' => 'sometimes|array',
            'pelunasan_ids.*' => 'exists:pinjamans,id'
        ]);

        DB::beginTransaction();
        try {
            $jumlah_pengajuan = $request->jumlah_pengajuan;
            $tenor = $request->tenor;
            $bunga = $request->bunga;

            $total_bunga = $jumlah_pengajuan * ($bunga / 100) * $tenor;
            $total_pinjaman = $jumlah_pengajuan + $total_bunga;
            $cicilan_per_bulan = $total_pinjaman / $tenor;

            $loanRequest = LoanRequest::create([
                'user_id'           => $request->user_id,
                'jenis_pinjaman_id' => $request->jenis_pinjaman_id,
                'jumlah_pengajuan'  => $jumlah_pengajuan,
                'tenor'             => $tenor,
                'bunga'             => $bunga,
                'total_bunga'       => $total_bunga,
                'total_pinjaman'    => $total_pinjaman,
                'cicilan_per_bulan' => $cicilan_per_bulan,
                'keterangan'        => $request->keterangan,
                'status'            => 'pending',
                'created_by'        => auth()->id() ?? 1
            ]);

            // Simpan data pelunasan (refinancing) jika ada
            if ($request->has('pelunasan_ids') && is_array($request->pelunasan_ids)) {
                foreach ($request->pelunasan_ids as $pId) {
                    LoanRequestTopup::create([
                        'loan_request_id' => $loanRequest->id,
                        'pinjaman_id'     => $pId
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('pinjaman.pengajuan')->with('success', 'Pengajuan pinjaman berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function searchAnggota(Request $request) {
        $q = $request->q;
        if (!$q) {
            return response()->json(['success' => false, 'message' => 'Query kosong']);
        }

        $anggota = \App\Models\Anggota::with('transaksiSimpanan')
            ->where('nik', $q)
            ->orWhere('no_ktp', $q)
            ->first();

        if (!$anggota) {
            return response()->json(['success' => false, 'message' => 'Anggota tidak ditemukan']);
        }

        $saldoAwal = \App\Models\SaldoAwalSimpanan::where('anggota_id', $anggota->id)->sum('nominal');
        $simpananTotal = 0;
        if ($anggota->transaksiSimpanan) {
            $simpananTotal = $anggota->transaksiSimpanan->sum(function ($item) {
                return $item->simpanan_pokok + $item->simpanan_wajib + $item->simpanan_sukarela;
            });
        }
        $simpananTotal += $saldoAwal;

        // Active loans (status berjalan)
        $pinjamanAktifDb = \App\Models\Pinjaman::where('user_id', $anggota->id)
            ->where('status', 'berjalan')
            ->get();
            
        // Pending loans (status pending)
        $pinjamanPendingDb = \App\Models\LoanRequest::where('user_id', $anggota->id)
            ->where('status', 'pending')
            ->get();

        $usagePerParent = [];

        foreach ($pinjamanAktifDb as $p) {
            $jp = \App\Models\MasterJenisPinjaman::find($p->jenis_pinjaman_id);
            if ($jp) {
                $parentId = $jp->parent_id ?? $jp->id;
                $usagePerParent[$parentId] = ($usagePerParent[$parentId] ?? 0) + $p->sisa_pinjaman;
            }
        }

        foreach ($pinjamanPendingDb as $p) {
            $jp = \App\Models\MasterJenisPinjaman::find($p->jenis_pinjaman_id);
            if ($jp) {
                $parentId = $jp->parent_id ?? $jp->id;
                $usagePerParent[$parentId] = ($usagePerParent[$parentId] ?? 0) + $p->jumlah_pengajuan;
            }
        }
            
        $pinjamanAktifTotal = $pinjamanAktifDb->sum('sisa_pinjaman');
        $maksPinjaman = 20000000;
        if ($simpananTotal > 0) {
            $maksPinjaman = $simpananTotal * 5;
        }
        $sisaLimit = max(0, $maksPinjaman - $pinjamanAktifTotal - $pinjamanPendingDb->sum('jumlah_pengajuan'));

        $listPinjaman = $pinjamanAktifDb->map(function($p) {
            $jp = \App\Models\MasterJenisPinjaman::find($p->jenis_pinjaman_id);
            return [
                'id'              => $p->id,
                'jenis_pinjaman'  => $jp ? $jp->nama_pinjaman : 'Pinjaman',
                'sisa_tenor'      => $p->sisa_tenor,
                'sisa_tenor_label'=> $p->sisa_tenor . ' bulan',
                'sisa_tagihan'    => $p->sisa_pinjaman,
                'jumlah_pinjaman' => $p->jumlah_pinjaman,
                'total_pinjaman'  => $p->total_pinjaman,
                'cicilan_per_bulan' => $p->cicilan_per_bulan,
                'status'          => $p->status
            ];
        });

        return response()->json([
            
            'success' => true,
            'data' => [
                'nama' => $anggota->nama_anggota,
                'nik' => $anggota->nik,
                'user_id' => $anggota->id,
                'tgl_masuk' => \Carbon\Carbon::parse($anggota->tgl_bergabung)->format('d M Y'),
                'total_simpanan' => $simpananTotal,
                'maks_pinjaman' => $maksPinjaman,
                'pinjaman_aktif' => $pinjamanAktifTotal,
                'sisa_limit' => $sisaLimit,
                'pinjaman_berjalan' => $listPinjaman,
                'usage_per_parent' => $usagePerParent
            ]
        ]);
    }

    public function angsuran(Request $request) {
        // List all tagihan batches
        $tagihanList = \App\Models\TagihanPinjaman::latest()->paginate(10);

        // For the generate modal: get all pinjaman berjalan with their unpaid angsuran
        $pinjamanAktif = Pinjaman::with(['anggota', 'jenisPinjaman', 'angsuran' => function($q) {
            $q->where('status', 'belum_bayar')->orderBy('angsuran_ke', 'asc');
        }])->where('status', 'berjalan')
          ->whereHas('angsuran', function($q) {
              $q->where('status', 'belum_bayar');
          })->get();

        return view('pinjaman.angsuran', compact('tagihanList', 'pinjamanAktif'));
    }

    public function storeAngsuran(Request $request) {
        $request->validate([
            'periode' => 'required',
            'tanggal_tagihan' => 'required|date',
            'angsuran_ids' => 'required|array|min:1',
            'angsuran_ids.*' => 'exists:pinjaman_angsurans,id',
        ]);

        DB::beginTransaction();
        try {
            $angsurans = PinjamanAngsuran::with('pinjaman.anggota')
                ->whereIn('id', $request->angsuran_ids)
                ->where('status', 'belum_bayar')
                ->get();

            $totalTagihan = $angsurans->sum('jumlah_tagihan');

            $tagihan = \App\Models\TagihanPinjaman::create([
                'periode' => date('F Y', strtotime($request->periode)),
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'type' => count($request->angsuran_ids) == PinjamanAngsuran::where('status', 'belum_bayar')->count()
                    ? 'Semua Anggota' : 'By Checklist',
                'total' => $totalTagihan,
                'status' => 'Draft',
            ]);

            // Link angsuran records to this tagihan batch
            PinjamanAngsuran::whereIn('id', $request->angsuran_ids)->update([
                'tagihan_pinjaman_id' => $tagihan->id,
            ]);

            DB::commit();

            return redirect()->route('pinjaman.angsuran')->with('success', 'Tagihan angsuran berhasil di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showAngsuran($id) {
        $tagihan = \App\Models\TagihanPinjaman::findOrFail($id);
        $details = PinjamanAngsuran::with(['pinjaman.anggota', 'pinjaman.jenisPinjaman'])
            ->where('tagihan_pinjaman_id', $id)
            ->orderBy('loan_id')
            ->orderBy('angsuran_ke')
            ->get();

        return view('pinjaman.angsuran_show', compact('tagihan', 'details'));
    }

    public function bayarAngsuran(Request $request) {
        $request->validate([
            'tagihan_id' => 'required|exists:penagihan_bills,id',
            'detail_ids' => 'required|array|min:1',
            'detail_ids.*' => 'exists:pinjaman_angsurans,id',
        ]);

        DB::beginTransaction();
        try {
            $tagihan = \App\Models\PenagihanBill::findOrFail($request->tagihan_id);
            $details = PinjamanAngsuran::whereIn('id', $request->detail_ids)
                ->where('penagihan_bill_id', $tagihan->id)
                ->where('status', 'belum_bayar')
                ->get();

            foreach ($details as $angsuran) {
                // 1. Create payment transaction record
                $pembayaran = \App\Models\PembayaranAngsuran::create([
                    'type_bayar' => 'normal',
                    'jumlah' => $angsuran->jumlah_tagihan,
                    'user_id' => $angsuran->pinjaman->user_id,
                    'loan_id' => $angsuran->loan_id,
                    'angsuran_id' => $angsuran->id,
                    'tanggal_bayar' => now()->toDateString(),
                ]);

                // 2. Mark angsuran as paid & link to payment
                $angsuran->update([
                    'status' => 'sudah_bayar',
                    'jumlah_dibayar' => $angsuran->jumlah_tagihan,
                    'tanggal_bayar' => now()->toDateString(),
                    'paid_at' => now()->toDateString(),
                    'payment_id' => $pembayaran->id,
                ]);

                // 3. Update pinjaman tracking
                $pinjaman = $angsuran->pinjaman;
                $pinjaman->update([
                    'total_terbayar' => $pinjaman->total_terbayar + $angsuran->jumlah_tagihan,
                    'sisa_pinjaman' => $pinjaman->sisa_pinjaman - $angsuran->jumlah_tagihan,
                    'sisa_tenor' => $pinjaman->sisa_tenor - 1,
                ]);

                // 4. If all angsuran paid, mark pinjaman as lunas
                $remaining = PinjamanAngsuran::where('loan_id', $pinjaman->id)
                    ->where('status', 'belum_bayar')->count();
                if ($remaining === 0) {
                    $pinjaman->update(['status' => 'lunas']);
                }
            }

            // 5. Update tagihan batch status
            $totalDetails = PinjamanAngsuran::where('penagihan_bill_id', $tagihan->id)->count();
            $lunasDetails = PinjamanAngsuran::where('penagihan_bill_id', $tagihan->id)->where('status', 'sudah_bayar')->count();

            if ($lunasDetails == $totalDetails) {
                $tagihan->update(['status' => 'Paid']);
            } elseif ($lunasDetails > 0) {
                $tagihan->update(['status' => 'Partial']);
            }

            DB::commit();

            return back()->with('success', 'Pembayaran angsuran berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function masterJenis() { 
        $jenis_pinjaman = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        return view('pinjaman.master_jenis', compact('jenis_pinjaman')); 
    }

    public function storeMasterJenis(\Illuminate\Http\Request $request) {
        $request->validate([
            'nama_pinjaman' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:master_jenis_pinjaman,id',
            'limit_maksimal' => 'nullable|numeric',
            'bunga' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:255',
        ]);

        \App\Models\MasterJenisPinjaman::create($request->all());

        return redirect()->back()->with('success', 'Jenis pinjaman berhasil ditambahkan');
    }

    public function updateMasterJenis(\Illuminate\Http\Request $request, $id) {
        $request->validate([
            'nama_pinjaman' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:master_jenis_pinjaman,id',
            'limit_maksimal' => 'nullable|numeric',
            'bunga' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jenis = \App\Models\MasterJenisPinjaman::findOrFail($id);
        $jenis->update($request->only(['nama_pinjaman', 'parent_id', 'limit_maksimal', 'bunga', 'keterangan']));

        return redirect()->back()->with('success', 'Jenis pinjaman berhasil diperbarui');
    }
}
