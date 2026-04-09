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
    Route::resource('laporan', LaporanController::class);

    // ── Simpanan ───────────────────────────────────────────
    Route::get('simpanan/tagihangenerator', [SimpananController::class, 'tagihangenerator'])
        ->name('simpanan.tagihangenerator');
    Route::get('simpanan/transaksi', [SimpananController::class, 'transaksi'])
        ->name('simpanan.transaksi');
    Route::post('simpanan/tagihangenerator', [SimpananController::class, 'storeTagihanGenerator'])
        ->name('simpanan.tagihangenerator.store');
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
    Route::get('pinjaman/approval', [App\Http\Controllers\PinjamanController::class, 'approval'])->name('pinjaman.approval');
    Route::post('pinjaman/approval/{id}/approve', [App\Http\Controllers\PinjamanController::class, 'approve'])->name('pinjaman.approval.approve');
    Route::post('pinjaman/approval/{id}/reject', [App\Http\Controllers\PinjamanController::class, 'reject'])->name('pinjaman.approval.reject');
    Route::get('pinjaman/aktif', [App\Http\Controllers\PinjamanController::class, 'aktif'])->name('pinjaman.aktif');
    Route::get('pinjaman/aktif/{id}', [App\Http\Controllers\PinjamanController::class, 'showAktif'])->name('pinjaman.aktif.show');
    Route::get('pinjaman/angsuran', [App\Http\Controllers\PinjamanController::class, 'angsuran'])->name('pinjaman.angsuran');
    Route::post('pinjaman/angsuran', [App\Http\Controllers\PinjamanController::class, 'storeAngsuran'])->name('pinjaman.angsuran.store');
    Route::post('pinjaman/angsuran/bayar', [App\Http\Controllers\PinjamanController::class, 'bayarAngsuran'])->name('pinjaman.angsuran.bayar');
    Route::get('pinjaman/angsuran/{id}', [App\Http\Controllers\PinjamanController::class, 'showAngsuran'])->name('pinjaman.angsuran.show');
    Route::get('pinjaman/master-jenis', [App\Http\Controllers\PinjamanController::class, 'masterJenis'])->name('pinjaman.masterJenis');
    Route::post('pinjaman/master-jenis', [App\Http\Controllers\PinjamanController::class, 'storeMasterJenis'])->name('pinjaman.masterJenis.store');

    // ── Penagihan ──────────────────────────────────────────
    Route::get('penagihan/tagihan-generator', [App\Http\Controllers\PenagihanController::class, 'index'])->name('penagihan.generator');
    Route::post('penagihan/tagihan-generator/generate', [App\Http\Controllers\PenagihanController::class, 'storeGenerate'])->name('penagihan.storeGenerate');
    Route::post('penagihan/tagihan-generator/bayar', [App\Http\Controllers\PenagihanController::class, 'bayar'])->name('penagihan.bayar');
    Route::get('penagihan/tagihan-generator/{id}', [App\Http\Controllers\PenagihanController::class, 'show'])->name('penagihan.show');
    Route::delete('penagihan/tagihan-generator/detail/{id}', [App\Http\Controllers\PenagihanController::class, 'destroyDetail'])->name('penagihan.destroyDetail');

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
