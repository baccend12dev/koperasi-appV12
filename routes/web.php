<?php
// routes/web.php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SimpananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');




    // ── Anggota ──────────────────────────────────────────
    Route::resource('anggota', AnggotaController::class);

    // ── Departemen ───────────────────────────────────────
    Route::resource('departemen', DepartemenController::class);
    // Tambahkan alias agar topbar-nav bisa pakai route('departemen.index')
    Route::get('anggota/departemen', [DepartemenController::class, 'index'])
         ->name('anggota.departemen');

    // ── Learning (placeholder) ───────────────────────────
    Route::get('learning', fn() => view('learning.index'))->name('learning.index');

    // ── Laporan ───────────────────────────────────────────
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // ── Simpanan ───────────────────────────────────────────
    Route::get('simpanan/tagihangenerator', [SimpananController::class, 'tagihangenerator'])
        ->name('simpanan.tagihangenerator');
    Route::get('simpanan/transaksi', [SimpananController::class, 'transaksi'])
        ->name('simpanan.transaksi');
    Route::post('simpanan/tagihangenerator', [SimpananController::class, 'storeTagihanGenerator'])
        ->name('simpanan.tagihangenerator.store');
    Route::get('simpanan/tambah-saldo', [SimpananController::class, 'tambahSaldo'])
        ->name('simpanan.tambah_saldo');
    Route::post('simpanan/tambah-saldo', [SimpananController::class, 'storeTambahSaldo'])
        ->name('simpanan.tambah_saldo.store');
    Route::get('simpanan/tarik-saldo', [SimpananController::class, 'tarikSimpanan'])
        ->name('simpanan.tarik');
    Route::post('simpanan/tarik-saldo', [SimpananController::class, 'storeTarikSimpanan'])
        ->name('simpanan.tarik.store');
    Route::get('simpanan/tagihangenerator/{id}', [SimpananController::class, 'showTagihan'])
        ->name('simpanan.tagihangenerator.show');
    Route::post('simpanan/tagihangenerator/bayar', [SimpananController::class, 'bayarTagihan'])
        ->name('simpanan.tagihangenerator.bayar');
    Route::resource('simpanan', SimpananController::class);

    Route::get('pinjaman', [App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman.index');
    Route::get('pinjaman/pengajuan', [App\Http\Controllers\PinjamanController::class, 'pengajuan'])->name('pinjaman.pengajuan');
    Route::post('pinjaman/pengajuan', [App\Http\Controllers\PinjamanController::class, 'storePengajuan'])->name('pinjaman.pengajuan.store');
    Route::get('pinjaman/pengajuan/search-anggota', [App\Http\Controllers\PinjamanController::class, 'searchAnggota'])->name('pinjaman.pengajuan.searchAnggota');
    Route::get('pinjaman/pengajuan/create', [App\Http\Controllers\PinjamanController::class, 'create'])->name('pinjaman.pengajuan.create');
    Route::post('pinjaman/approval/{id}/approve', [App\Http\Controllers\PersetujuanController::class, 'approvePinjaman'])->name('pinjaman.approval.approve');
    Route::post('pinjaman/approval/{id}/reject', [App\Http\Controllers\PersetujuanController::class, 'rejectPinjaman'])->name('pinjaman.approval.reject');
    Route::get('pinjaman/aktif', [App\Http\Controllers\PinjamanController::class, 'aktif'])->name('pinjaman.aktif');
    Route::get('pinjaman/aktif/{id}', [App\Http\Controllers\PinjamanController::class, 'showAktif'])->name('pinjaman.aktif.show');
    Route::post('pinjaman/aktif/{id}/bayar-langsung', [App\Http\Controllers\PinjamanController::class, 'bayarLangsung'])->name('pinjaman.aktif.bayarLangsung');
    Route::get('pinjaman/angsuran', [App\Http\Controllers\PinjamanController::class, 'angsuran'])->name('pinjaman.angsuran');
    Route::post('pinjaman/angsuran', [App\Http\Controllers\PinjamanController::class, 'storeAngsuran'])->name('pinjaman.angsuran.store');
    Route::post('pinjaman/angsuran/bayar', [App\Http\Controllers\PinjamanController::class, 'bayarAngsuran'])->name('pinjaman.angsuran.bayar');
    Route::get('pinjaman/angsuran/{id}', [App\Http\Controllers\PinjamanController::class, 'showAngsuran'])->name('pinjaman.angsuran.show');
    Route::get('pinjaman/master-jenis', [App\Http\Controllers\PinjamanController::class, 'masterJenis'])->name('pinjaman.masterJenis');
    Route::post('pinjaman/master-jenis', [App\Http\Controllers\PinjamanController::class, 'storeMasterJenis'])->name('pinjaman.masterJenis.store');
    Route::put('pinjaman/master-jenis/{id}', [App\Http\Controllers\PinjamanController::class, 'updateMasterJenis'])->name('pinjaman.masterJenis.update');

    // ── Penagihan ──────────────────────────────────────────
    Route::get('penagihan', [App\Http\Controllers\PenagihanController::class, 'index'])->name('penagihan.index');
    Route::get('penagihan/tagihan-generator', [App\Http\Controllers\PenagihanController::class, 'generator'])->name('penagihan.generator');
    Route::post('penagihan/tagihan-generator/generate', [App\Http\Controllers\PenagihanController::class, 'storeGenerate'])->name('penagihan.storeGenerate');
    Route::post('penagihan/tagihan-generator/generate-mandiri', [App\Http\Controllers\PenagihanController::class, 'storeGenerateMandiri'])->name('penagihan.storeGenerateMandiri');
    Route::post('penagihan/tagihan-generator/bayar', [App\Http\Controllers\PenagihanController::class, 'bayar'])->name('penagihan.bayar');
    Route::get('penagihan/tagihan-generator/export/{id}', [App\Http\Controllers\PenagihanController::class, 'exportExcel'])->name('penagihan.exportExcel');
    Route::get('penagihan/tagihan-generator/{id}', [App\Http\Controllers\PenagihanController::class, 'show'])->name('penagihan.show');
    Route::delete('penagihan/tagihan-generator/detail/{id}', [App\Http\Controllers\PenagihanController::class, 'destroyDetail'])->name('penagihan.destroyDetail');


    // ── Persetujuan (Approval) ───────────────────────────
    Route::get('persetujuan/pinjaman', [App\Http\Controllers\PersetujuanController::class, 'pinjaman'])->name('persetujuan.pinjaman');
    Route::get('persetujuan/pengambilan', [App\Http\Controllers\PersetujuanController::class, 'pengambilan'])->name('persetujuan.pengambilan');
    Route::post('persetujuan/pengambilan/{id}/approve', [App\Http\Controllers\PersetujuanController::class, 'approvePengambilan'])->name('persetujuan.pengambilan.approve');
    Route::post('persetujuan/pengambilan/{id}/reject', [App\Http\Controllers\PersetujuanController::class, 'rejectPengambilan'])->name('persetujuan.pengambilan.reject');

    // ── Pencairan ─────────────────────────────────────────
    Route::get('pencairan/pinjaman', [App\Http\Controllers\PencairanController::class, 'pinjaman'])->name('pencairan.pinjaman');
    Route::get('pencairan/pengambilan', [App\Http\Controllers\PencairanController::class, 'pengambilan'])->name('pencairan.pengambilan');
    Route::post('pencairan/bayar', [App\Http\Controllers\PencairanController::class, 'markPaid'])->name('pencairan.bayar');

    // ── Konfigurasi ───────────────────────────────────────
    Route::get('konfigurasi', fn() => view('konfigurasi.index'))->name('konfigurasi.index');

    // ── Profile ───────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// login
Route::get('/login', fn() => view('auth.login'))->name('login');    

// logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
