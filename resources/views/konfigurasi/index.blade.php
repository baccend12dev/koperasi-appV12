@extends('layouts.app')

@section('title', 'Konfigurasi Sistem')

@section('page-title', 'Konfigurasi Sistem Koperasi')

@section('content')
<style>
    .config-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 24px;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        padding: 0 24px 40px 24px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    .config-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .config-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        border-color: #3b82f6;
    }
    
    .config-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }
    
    .config-title {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .config-desc {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
        flex-grow: 1;
    }

    /* Colors for different categories */
    .color-gray .config-icon-wrapper { background: #f3f4f6; color: #4b5563; }
    .color-blue .config-icon-wrapper { background: #eff6ff; color: #2563eb; }
    .color-green .config-icon-wrapper { background: #ecfdf5; color: #059669; }
    .color-yellow .config-icon-wrapper { background: #fffbeb; color: #d97706; }
    .color-indigo .config-icon-wrapper { background: #eef2ff; color: #4f46e5; }
    .color-red .config-icon-wrapper { background: #fef2f2; color: #dc2626; }
    .color-purple .config-icon-wrapper { background: #f5f3ff; color: #7c3aed; }
    .color-teal .config-icon-wrapper { background: #f0fdfa; color: #0d9488; }
</style>

<div class="config-grid">
    <!-- Setting Umum -->
    <a href="#" class="config-card color-gray">
        <div class="config-icon-wrapper">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="config-title">Setting Umum</div>
        <div class="config-desc">Pengaturan informasi dasar koperasi, nama, alamat, dan logo sistem.</div>
    </a>

    <!-- Jenis Simpanan -->
    <a href="#" class="config-card color-blue">
        <div class="config-icon-wrapper">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="config-title">Jenis Simpanan</div>
        <div class="config-desc">Manajemen jenis-jenis simpanan (Pokok, Wajib, Sukarela) beserta aturannya.</div>
    </a>

    <!-- Jenis Pinjaman -->
    <a href="{{ route('pinjaman.masterJenis') }}" class="config-card color-green">
        <div class="config-icon-wrapper">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <div class="config-title">Jenis Pinjaman</div>
        <div class="config-desc">Mengatur produk pinjaman yang ditawarkan termasuk plafon dan tenor.</div>
    </a>

    <!-- Setting Bunga -->
    <a href="#" class="config-card color-yellow">
        <div class="config-icon-wrapper">
            <i class="fas fa-percent"></i>
        </div>
        <div class="config-title">Setting Bunga</div>
        <div class="config-desc">Menentukan persentase suku bunga pinjaman dan metode perhitungannya.</div>
    </a>

    <!-- Limit Pinjaman -->
    <a href="#" class="config-card color-indigo">
        <div class="config-icon-wrapper">
            <i class="fas fa-tachometer-alt"></i>
        </div>
        <div class="config-title">Limit Pinjaman</div>
        <div class="config-desc">Mengatur batasan maksimal peminjaman berdasarkan saldo atau keanggotaan.</div>
    </a>

    <!-- Denda & Penalti -->
    <a href="#" class="config-card color-red">
        <div class="config-icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="config-title">Denda & Penalti</div>
        <div class="config-desc">Pengaturan sanksi keterlambatan pembayaran dan tarif denda penalti.</div>
    </a>

    <!-- Penomoran Dokumen -->
    <a href="#" class="config-card color-purple">
        <div class="config-icon-wrapper">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="config-title">Penomoran Dokumen</div>
        <div class="config-desc">Format otomatis untuk nomor pengajuan, ID anggota, dan transaksi.</div>
    </a>

    <!-- Periode -->
    <a href="#" class="config-card color-teal">
        <div class="config-icon-wrapper">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="config-title">Periode</div>
        <div class="config-desc">Pengaturan bulan berjalan, serta fitur buka-tutup buku akuntansi.</div>
    </a>
</div>
@endsection
