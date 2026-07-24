<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pinjaman;
use App\Models\PembayaranAngsuran;
use App\Models\PinjamanAngsuran;

class SyncTanggalMulaiPinjamanSeeder extends Seeder
{
    public function run()
    {
        $updatedCount = 0;
        $pinjamans = Pinjaman::all();

        foreach ($pinjamans as $pinjaman) {
            // Cari tanggal pembayaran angsuran pertama
            $pembayaranPertama = PembayaranAngsuran::where('loan_id', $pinjaman->id)
                ->orderBy('tanggal_bayar', 'asc')
                ->first();

            // Jika tidak ada pembayaran, cari jadwal angsuran pertama (tanggal_jatuh_tempo)
            if (!$pembayaranPertama) {
                $angsuranPertama = PinjamanAngsuran::where('loan_id', $pinjaman->id)
                    ->orderBy('tanggal_jatuh_tempo', 'asc')
                    ->first();
                $tanggalAwal = $angsuranPertama ? $angsuranPertama->tanggal_jatuh_tempo : null;
            } else {
                $tanggalAwal = $pembayaranPertama->tanggal_bayar;
            }

            if ($tanggalAwal && $pinjaman->tanggal_mulai != $tanggalAwal) {
                $this->command->info("Loan ID {$pinjaman->id}: {$pinjaman->tanggal_mulai} => {$tanggalAwal}");
                $pinjaman->update([
                    'tanggal_mulai' => $tanggalAwal
                ]);
                $updatedCount++;
            }
        }

        $this->command->info("Selesai! Total {$updatedCount} data pinjaman berhasil diperbarui.");
    }
}
