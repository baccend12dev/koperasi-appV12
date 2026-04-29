{{--
    resources/views/laporan/index.blade.php
    Dashboard Ringkasan — contoh implementasi layout laporan
--}}
@extends('laporan.layout')

@section('laporan-title', 'Dashboard Ringkasan')
@section('laporan-subtitle', 'Ikhtisar performa keuangan koperasi secara keseluruhan')

@section('laporan-actions')
    <button style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid #D1D5DB;border-radius:8px;background:#fff;font-size:12px;font-weight:600;color:#374151;cursor:pointer;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export PDF
    </button>
@endsection

@section('laporan-content')
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">

    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #1D4ED8;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;margin-bottom:8px;">Total Simpanan Aktif</div>
        <div style="font-size:22px;font-weight:800;color:#1D4ED8;">Rp 0</div>
        <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Semua anggota aktif</div>
    </div>

    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #DC2626;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;margin-bottom:8px;">Total Pinjaman Berjalan</div>
        <div style="font-size:22px;font-weight:800;color:#DC2626;">Rp 0</div>
        <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Sisa pokok belum lunas</div>
    </div>

    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #059669;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;margin-bottom:8px;">Total Anggota Aktif</div>
        <div style="font-size:22px;font-weight:800;color:#059669;">0</div>
        <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Anggota terdaftar</div>
    </div>

</div>

{{-- Placeholder konten --}}
<div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:60px 20px;text-align:center;">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin:0 auto 12px;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
    <div style="font-size:15px;font-weight:700;color:#6B7280;margin-bottom:6px;">Dashboard Ringkasan</div>
    <div style="font-size:13px;color:#9CA3AF;">Konten laporan akan ditampilkan di sini setelah data tersedia.</div>
</div>
@endsection
