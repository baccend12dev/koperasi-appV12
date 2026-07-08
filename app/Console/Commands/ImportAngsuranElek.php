<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use App\Models\Pinjaman;
use App\Models\PinjamanAngsuran;
use App\Models\PembayaranAngsuran;
use Illuminate\Support\Facades\DB;
use Exception;

class ImportAngsuranElek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-angsuran-elek {file? : Path file Excel yang akan di-import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import riwayat pembayaran angsuran pinjaman dari Excel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file') ?: 'importdata/ANGSURAN MOTOR 8.xlsx';
        $filePath = base_path($file);

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan di path: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Membaca file Excel: {$file} ...");

        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $rows = $worksheet->toArray();
            if (empty($rows)) {
                $this->error("File Excel kosong.");
                return Command::FAILURE;
            }

            // Baris pertama adalah header
            $headers = array_shift($rows);
            
            // Validasi header
            if (strtolower(trim($headers[0] ?? '')) !== 'loan_id') {
                $this->error("Kolom pertama harus berupa 'loan_id'. Header ditemukan: " . ($headers[0] ?? 'kosong'));
                return Command::FAILURE;
            }

            $totalRows = count($rows);
            $this->info("Menemukan {$totalRows} baris data pinjaman. Memulai proses import...");

            $successCount = 0;
            $skipCount = 0;

            DB::beginTransaction();

            foreach ($rows as $rowIndex => $rowData) {
                $loanId = $rowData[0];
                if (empty($loanId)) {
                    continue;
                }

                $pinjaman = Pinjaman::find($loanId);
                if (!$pinjaman) {
                    $this->warn("Baris " . ($rowIndex + 2) . ": Pinjaman dengan ID {$loanId} tidak ditemukan. Dilewati.");
                    $skipCount++;
                    continue;
                }

                $this->comment("Memproses pinjaman ID: {$loanId} (Anggota: {$pinjaman->anggota?->nama_anggota})");

                // Loop setiap kolom angsuran_ke (dari indeks 1 dst)
                foreach ($rowData as $colIndex => $cellValue) {
                    if ($colIndex === 0) continue; // Kolom loan_id

                    // Ambil nomor angsuran_ke dari header
                    $angsuranKe = trim($headers[$colIndex] ?? '');
                    if (!is_numeric($angsuranKe)) {
                        continue;
                    }

                    // Jika cell kosong, berarti belum dibayar, lewati
                    if ($cellValue === null || trim($cellValue) === '') {
                        continue;
                    }

                    // Konversi tanggal dari Excel
                    $tanggalBayar = null;
                    if (is_numeric($cellValue)) {
                        $tanggalBayar = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                    } else {
                        // Jika dalam bentuk string, parse atau gunakan langsung
                        $tanggalBayar = date('Y-m-d', strtotime($cellValue));
                    }

                    // Cari PinjamanAngsuran untuk loan ini dan angsuran_ke
                    $angsuran = PinjamanAngsuran::where('loan_id', $loanId)
                        ->where('angsuran_ke', $angsuranKe)
                        ->first();

                    if (!$angsuran) {
                        $this->warn("  - Angsuran ke-{$angsuranKe} tidak ditemukan di DB untuk Pinjaman ID {$loanId}. Dilewati.");
                        continue;
                    }

                    // Jika status sudah bayar, lewati agar tidak dobel pembayaran
                    if ($angsuran->status === 'sudah_bayar') {
                        continue;
                    }

                    // Buat PembayaranAngsuran
                    $pembayaran = PembayaranAngsuran::create([
                        'type_bayar' => 'normal',
                        'jumlah' => $angsuran->jumlah_tagihan,
                        'user_id' => $pinjaman->user_id,
                        'loan_id' => $pinjaman->id,
                        'angsuran_id' => $angsuran->id,
                        'tanggal_bayar' => $tanggalBayar,
                    ]);

                    // Update PinjamanAngsuran
                    $angsuran->update([
                        'status' => 'sudah_bayar',
                        'jumlah_dibayar' => $angsuran->jumlah_tagihan,
                        'tanggal_bayar' => $tanggalBayar,
                        'paid_at' => $tanggalBayar,
                        'payment_id' => $pembayaran->id,
                    ]);

                    // Update stats di Pinjaman
                    $pinjaman->update([
                        'total_terbayar' => $pinjaman->total_terbayar + $angsuran->jumlah_tagihan,
                        'sisa_pinjaman' => max(0, $pinjaman->sisa_pinjaman - $angsuran->jumlah_tagihan),
                        'sisa_tenor' => max(0, $pinjaman->sisa_tenor - 1),
                    ]);

                    $pinjaman->refresh();

                    if ($pinjaman->sisa_pinjaman <= 0) {
                        $pinjaman->update(['status' => 'lunas']);
                        $pinjaman->refresh();
                    }

                    $successCount++;
                }
            }

            DB::commit();

            $this->info("Import selesai!");
            $this->info("- Jumlah update pembayaran angsuran berhasil: {$successCount}");
            $this->info("- Jumlah baris pinjaman dilewati (tidak ditemukan): {$skipCount}");
            
            return Command::SUCCESS;

        } catch (Exception $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan saat memproses data: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
