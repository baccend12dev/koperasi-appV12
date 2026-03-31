<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\LoanRequest;
use Illuminate\Http\Request;

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

    public function approval(Request $request) {
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        
        $query = LoanRequest::with('anggota', 'jenisPinjaman')->where('status', 'pending');
        
        if ($request->filled('jenis')) {
            $query->where('jenis_pinjaman_id', $request->jenis);
        }
        
        $pengajuan_list = $query->orderBy('created_at', 'asc')->get();
        
        $totalPengajuan = $pengajuan_list->count();
        $totalNominal = $pengajuan_list->sum('jumlah_pengajuan');
        
        return view('pinjaman.approval', compact('pengajuan_list', 'jenisPinjamanList', 'totalPengajuan', 'totalNominal'));
    }

    public function aktif(Request $request) {
        $jenisPinjamanList = \App\Models\MasterJenisPinjaman::with('children')->whereNull('parent_id')->get();
        
        $query = \App\Models\Pinjaman::with(['anggota', 'jenisPinjaman']);
        
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $query->whereHas('anggota', function($qBuilder) use ($q) {
                $qBuilder->where('nama_anggota', 'like', '%'.$q.'%')
                         ->orWhere('nik', 'like', '%'.$q.'%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pinjaman_id', $request->jenis);
        }

        $pinjaman_list = $query->orderBy('created_at', 'desc')->get();
        
        $totalPinjamanBerjalan = \App\Models\Pinjaman::where('status', 'berjalan')->sum('sisa_pinjaman');
        $countBerjalan = \App\Models\Pinjaman::where('status', 'berjalan')->count();

        return view('pinjaman.aktif', compact('pinjaman_list', 'jenisPinjamanList', 'totalPinjamanBerjalan', 'countBerjalan'));
    }

    public function approve($id) {
        $loanRequest = LoanRequest::findOrFail($id);
        
        if ($loanRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        \DB::transaction(function () use ($loanRequest) {
            $loanRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id() ?? 1,
                'approved_at' => now(),
            ]);

            \App\Models\Pinjaman::create([
                'loan_request_id'   => $loanRequest->id,
                'user_id'           => $loanRequest->user_id,
                'jenis_pinjaman_id' => $loanRequest->jenis_pinjaman_id,
                'jumlah_pinjaman'   => $loanRequest->jumlah_pengajuan,
                'tenor'             => $loanRequest->tenor,
                'bunga'             => $loanRequest->bunga,
                'total_bunga'       => $loanRequest->total_bunga,
                'total_pinjaman'    => $loanRequest->total_pinjaman,
                'cicilan_per_bulan' => $loanRequest->cicilan_per_bulan,
                'sisa_pinjaman'     => $loanRequest->total_pinjaman,
                'sisa_tenor'        => $loanRequest->tenor,
                'total_terbayar'    => 0,
                'status'            => 'berjalan',
                'tanggal_mulai'     => now(),
                'tanggal_selesai'   => now()->addMonths($loanRequest->tenor)
            ]);
        });

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, $id) {
        $loanRequest = LoanRequest::findOrFail($id);
        
        if ($loanRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah tidak berstatus pending.');
        }

        $loanRequest->update([
            'status' => 'rejected',
            'alasan_penolakan' => $request->alasan,
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
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
            'bunga' => 'required|numeric|min:0'
        ]);

        $jumlah_pengajuan = $request->jumlah_pengajuan;
        $tenor = $request->tenor;
        $bunga = $request->bunga;

        $total_bunga = $jumlah_pengajuan * ($bunga / 100) * $tenor;
        $total_pinjaman = $jumlah_pengajuan + $total_bunga;
        $cicilan_per_bulan = $total_pinjaman / $tenor;

        LoanRequest::create([
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

        return redirect()->route('pinjaman.pengajuan')->with('success', 'Pengajuan pinjaman berhasil dibuat.');
    }

    public function searchAnggota(Request $request) {
        $q = $request->q;
        if (!$q) {
            return response()->json(['success' => false, 'message' => 'Query kosong']);
        }

        $anggota = \App\Models\Anggota::with('transaksiSimpanan')
            ->where('nik', $q)
            ->orWhere('no_pegawai', $q)
            ->first();

        if (!$anggota) {
            return response()->json(['success' => false, 'message' => 'Anggota tidak ditemukan']);
        }

        $simpananTotal = 0;
        if ($anggota->transaksiSimpanan) {
            $simpananTotal = $anggota->transaksiSimpanan->sum('amount');
        }

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
                'jenis_pinjaman' => $jp ? $jp->nama_pinjaman : 'Pinjaman',
                'sisa_tenor' => $p->sisa_tenor . ' bulan',
                'sisa_tagihan' => $p->sisa_pinjaman,
                'status' => $p->status
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
}
