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
    <div style="margin-bottom: 32px;">
        <h2 style="font-size:16px;font-weight:700;color:#374151;margin-bottom:16px;margin-top:0;">Ringkasan Keuangan</h2>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #1D4ED8;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;">Total Simpanan Aktif</div>
                    <div style="padding:6px;background:#EFF6FF;border-radius:8px;color:#1D4ED8;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div style="font-size:22px;font-weight:800;color:#1D4ED8;">Rp {{ number_format($totalSimpananAktif ?? 0, 0, ',', '.') }}</div>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Akumulasi seluruh simpanan anggota</div>
            </div>

            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #DC2626;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;">Total Pinjaman Berjalan</div>
                    <div style="padding:6px;background:#FEF2F2;border-radius:8px;color:#DC2626;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
                <div style="font-size:22px;font-weight:800;color:#DC2626;">Rp {{ number_format($totalPinjamanBerjalan ?? 0, 0, ',', '.') }}</div>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Sisa pokok belum lunas</div>
            </div>

            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #059669;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;">Total Anggota Aktif</div>
                    <div style="padding:6px;background:#ECFDF5;border-radius:8px;color:#059669;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div style="font-size:22px;font-weight:800;color:#059669;">{{ number_format($totalAnggotaAktif ?? 0, 0, ',', '.') }}</div>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Anggota terdaftar di koperasi</div>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 32px;">
        <h2 style="font-size:16px;font-weight:700;color:#374151;margin-bottom:16px;margin-top:0;">Aktivitas & Antrean</h2>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #F59E0B;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;">Anggota Meminjam</div>
                    <div style="padding:6px;background:#FFFBEB;border-radius:8px;color:#F59E0B;">
                        <i class="fas fa-user-tag"></i>
                    </div>
                </div>
                <div style="font-size:22px;font-weight:800;color:#F59E0B;">{{ number_format($anggotaMeminjam ?? 0, 0, ',', '.') }}</div>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Mempunyai pinjaman aktif</div>
            </div>

            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #8B5CF6;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;">Antrean Pengajuan Pinjaman</div>
                    <div style="padding:6px;background:#F5F3FF;border-radius:8px;color:#8B5CF6;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div style="font-size:22px;font-weight:800;color:#8B5CF6;">{{ number_format($pinjamanPending ?? 0, 0, ',', '.') }}</div>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Menunggu persetujuan</div>
            </div>

            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:18px 20px;border-top:3px solid #14B8A6;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;">Antrean Penarikan Simpanan</div>
                    <div style="padding:6px;background:#F0FDFA;border-radius:8px;color:#14B8A6;">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                </div>
                <div style="font-size:22px;font-weight:800;color:#14B8A6;">{{ number_format($penarikanPending ?? 0, 0, ',', '.') }}</div>
                <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Menunggu pencairan dana</div>
            </div>
        </div>
    </div>
@endsection
