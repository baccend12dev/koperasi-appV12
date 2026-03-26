<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\MasterSimpanan;
use App\Models\TagihanSimpanan;
use App\Models\TagihanSimpananDetail;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimpananController extends Controller
{
    public function showTagihan($id)
    {
        $tagihan = TagihanSimpanan::with('details.anggota')->findOrFail($id);
        return view('simpanan.tagihan_detail', compact('tagihan'));
    }

    public function bayarTagihan(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan_simpanans,id',
            'detail_ids' => 'required|array|min:1',
            'detail_ids.*' => 'exists:tagihan_simpanan_details,id'
        ]);

        DB::beginTransaction();
        try {
            $tagihan = TagihanSimpanan::findOrFail($request->tagihan_id);
            $details = TagihanSimpananDetail::whereIn('id', $request->detail_ids)
                ->where('tagihan_simpanan_id', $tagihan->id)
                ->where('status', '!=', 'Lunas')
                ->get();

            $wajibId = \App\Models\JenisSimpanan::where('nama', 'Wajib')->first()->id ?? 1;
            $pokokId = \App\Models\JenisSimpanan::where('nama', 'Pokok')->first()->id ?? 2;
            $sukarelaId = \App\Models\JenisSimpanan::where('nama', 'Sukarela')->first()->id ?? 3;

            foreach ($details as $detail) {
                // Insert transactions for Pokok, Wajib, Sukarela if > 0
                if ($detail->simpanan_wajib > 0) {
                    \App\Models\TransaksiSimpanan::create([
                        'anggota_id' => $detail->anggota_id,
                        'jenis_simpanan_id' => $wajibId,
                        'amount' => $detail->simpanan_wajib,
                        'transaction_date' => date('Y-m-d'),
                        'periode' => $tagihan->periode,
                        'description' => 'Pembayaran Tagihan Wajib Periode ' . $tagihan->periode
                    ]);
                }
                if ($detail->simpanan_pokok > 0) {
                    \App\Models\TransaksiSimpanan::create([
                        'anggota_id' => $detail->anggota_id,
                        'jenis_simpanan_id' => $pokokId,
                        'amount' => $detail->simpanan_pokok,
                        'transaction_date' => date('Y-m-d'),
                        'periode' => $tagihan->periode,
                        'description' => 'Pembayaran Tagihan Pokok Periode ' . $tagihan->periode
                    ]);
                }
                if ($detail->simpanan_sukarela > 0) {
                    \App\Models\TransaksiSimpanan::create([
                        'anggota_id' => $detail->anggota_id,
                        'jenis_simpanan_id' => $sukarelaId,
                        'amount' => $detail->simpanan_sukarela,
                        'transaction_date' => date('Y-m-d'),
                        'periode' => $tagihan->periode,
                        'description' => 'Pembayaran Tagihan Sukarela Periode ' . $tagihan->periode
                    ]);
                }

                // Update status array detail
                $detail->update(['status' => 'Lunas']);
            }

            // Check global status of Tagihan
            $totalDetails = TagihanSimpananDetail::where('tagihan_simpanan_id', $tagihan->id)->count();
            $lunasDetails = TagihanSimpananDetail::where('tagihan_simpanan_id', $tagihan->id)->where('status', 'Lunas')->count();

            if ($lunasDetails == $totalDetails) {
                $tagihan->update(['status' => 'Paid']);
            } elseif ($lunasDetails > 0) {
                $tagihan->update(['status' => 'Partial']);
            }

            DB::commit();

            return back()->with('success', 'Pembayaran berhasil diproses dan dicatat sebagai transaksi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
    public function transaksi(Request $request)
    {
        $query = \App\Models\TransaksiSimpanan::with(['anggota', 'jenisSimpanan'])->latest();

        $tahun = $request->tahun ?? date('Y');
        $query->whereYear('transaction_date', $tahun);

        if ($request->bulan) {
            $query->whereMonth('transaction_date', $request->bulan);
        }

        if ($request->q) {
            $query->whereHas('anggota', function($q) use ($request) {
                $q->where('nama_anggota', 'like', '%'.$request->q.'%')
                  ->orWhere('nik', 'like', '%'.$request->q.'%');
            });
        }

        if ($request->jenis) {
            $query->where('jenis_simpanan_id', $request->jenis);
        }

        $selectedBulan = $request->bulan ?? date('m');
        $totalBulanIni = (clone $query)->sum('amount');

        $prevDate = \Carbon\Carbon::createFromDate($tahun, $selectedBulan, 1)->subMonth();
        $totalBulanLalu = \App\Models\TransaksiSimpanan::whereMonth('transaction_date', $prevDate->month)
                            ->whereYear('transaction_date', $prevDate->year)
                            ->sum('amount');
        $persenBulanIni = $totalBulanLalu > 0 ? (($totalBulanIni - $totalBulanLalu) / $totalBulanLalu) * 100 : 0;

        $anggotaAktif = Anggota::whereIn('status_anggota', ['active', 'aktif'])->count();

        $transaksi = $query->paginate(10)->withQueryString();
        $jenisSimpanan = \App\Models\JenisSimpanan::all();
        
        $years = \App\Models\TransaksiSimpanan::selectRaw('EXTRACT(YEAR FROM transaction_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        if (count($years) === 0) {
            $years = [date('Y')];
        }

        return view('simpanan.transaksi', compact(
            'transaksi', 'totalBulanIni', 'persenBulanIni', 'anggotaAktif', 'jenisSimpanan', 'years'
        ));
    }
    public function index()
    {
        $simpanan = MasterSimpanan::with('anggota')
            ->latest()
            ->paginate(10);

        $departemen = Departemen::withCount('anggota')
        ->orderBy('nama')
        ->get();
        // dd($simpanan);
        return view('simpanan.index', compact('simpanan', 'departemen'));
    }

    public function tagihangenerator()
    {
        $tagihanGenerator = TagihanSimpanan::latest()->paginate(10);
        $anggotaAktif = Anggota::with('masterSimpanan')->get();
    // dd($anggotaAktif);
        return view('simpanan.tagihan_generator', compact('tagihanGenerator', 'anggotaAktif'));
    }

    public function storeTagihanGenerator(Request $request)
    {
        $request->validate([
            'periode' => 'required',
            'tanggal_generate' => 'required|date',
            'anggota_ids' => 'required|array|min:1',
            'anggota_ids.*' => 'exists:anggotas,id',
        ]);

        //check if tagihan already exists for this periode
        $tagihan = TagihanSimpanan::where('periode', date('F Y', strtotime($request->periode)))->first();
        if ($tagihan) {
            return back()->with('error', 'Tagihan untuk periode ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $anggotas = Anggota::with('masterSimpanan')->whereIn('id', $request->anggota_ids)->get();
            
            $tagihanTotal = 0;
            $type = count($request->anggota_ids) == Anggota::whereIn('status_anggota', ['active', 'aktif'])->count() 
                ? 'Semua Anggota' : 'By Checklist';

            $tagihanSimpanan = TagihanSimpanan::create([
                'periode' => date('F Y', strtotime($request->periode)), // e.g March 2026
                'tanggal_generate' => $request->tanggal_generate,
                'type' => $type,
                'total' => 0, // update later
                'status' => 'Draft',
            ]);

            foreach ($anggotas as $anggota) {
                // If masterSimpanan doesn't exist, use 0
                $simpananPokok = $anggota->masterSimpanan->simpanan_pokok ?? 0;
                $simpananWajib = $anggota->masterSimpanan->simpanan_wajib ?? 0;
                $simpananSukarela = $anggota->masterSimpanan->simpanan_sukarela ?? 0;
                $totalAnggota = $simpananPokok + $simpananWajib + $simpananSukarela;

                TagihanSimpananDetail::create([
                    'tagihan_simpanan_id' => $tagihanSimpanan->id,
                    'anggota_id' => $anggota->id,
                    'simpanan_pokok' => $simpananPokok,
                    'simpanan_wajib' => $simpananWajib,
                    'simpanan_sukarela' => $simpananSukarela,
                    'total' => $totalAnggota,
                    'status' => 'Belum Lunas',
                ]);

                $tagihanTotal += $totalAnggota;
            }

            $tagihanSimpanan->update(['total' => $tagihanTotal]);

            DB::commit();

            return redirect()->route('simpanan.tagihangenerator')->with('success', 'Tagihan berhasil di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        
    }

    public function show($id)
    {
        $master = \App\Models\MasterSimpanan::with('anggota')->findOrFail($id);
        
        // Calculate total keseluruhan from transactions
        $totalKeseluruhan = \App\Models\TransaksiSimpanan::where('anggota_id', $master->anggota_id)->sum('amount');
        
        // History of transactions
        $riwayatTransaksi = \App\Models\TransaksiSimpanan::with('jenisSimpanan')
            ->where('anggota_id', $master->anggota_id)
            ->orderBy('transaction_date', 'desc')
            ->get();
            
        return view('simpanan.show', compact('master', 'totalKeseluruhan', 'riwayatTransaksi'));
    }

    public function store(Request $request)
    {
        
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
        $master = \App\Models\MasterSimpanan::findOrFail($id);
        
        $request->validate([
            'jenis_simpanan' => 'required|in:Pokok,Wajib,Sukarela',
            'nominal_baru' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date'
        ]);

        $field = 'simpanan_' . strtolower($request->jenis_simpanan);
        $master->update([
            $field => $request->nominal_baru,
            'tanggal_mulai' => $request->tanggal_mulai
        ]);

        return back()->with('success', 'Konfigurasi simpanan ' . $request->jenis_simpanan . ' berhasil diperbarui.');
    }

    public function destroy(Simpanan $simpanan)
    {
     
    }
}