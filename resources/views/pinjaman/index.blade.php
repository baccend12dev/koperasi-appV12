{{-- resources/views/pinjaman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Pinjaman')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link active">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link">Pengajuan Pinjaman</a>
    <a href="{{ route('pinjaman.approval') }}" class="tb-link">Approval Pinjaman</a>
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Pembayaran Angsuran</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link">Master Jenis Pinjaman</a>
@endsection

@section('page-title', 'Dashboard Pinjaman')

@section('content')
<style>
    .dashboard-container {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
        color: #333;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Summary Cards Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .card-summary {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-summary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .card-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-aktif { background: #e8f0fe; color: #1a73e8; }
    .icon-out { background: #e6f4ea; color: #1e8e3e; }
    .icon-pending { background: #fef7e0; color: #fbbc04; }
    .icon-macet { background: #fce8e6; color: #d93025; }

    .card-title {
        font-size: 13px;
        font-weight: 600;
        color: #5f6368;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-value {
        font-size: 28px;
        font-weight: 700;
        color: #202124;
        margin-bottom: 4px;
    }

    /* Additional layout flex for Chart & Table */
    .dashboard-flex-row {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    .flex-col-left {
        flex: 1;
        min-width: 300px;
    }
    .flex-col-right {
        flex: 2;
        min-width: 600px;
    }

    /* Block Container */
    .content-block {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .block-header {
        font-size: 16px;
        font-weight: 600;
        color: #202124;
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .block-header a {
        font-size: 13px;
        font-weight: 500;
        color: #1a73e8;
        text-decoration: none;
    }
    .block-header a:hover {
        text-decoration: underline;
    }

    /* Table styles */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .table-custom th {
        text-align: left;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #5f6368;
        text-transform: uppercase;
        border-bottom: 1px solid #e0e0e0;
        background: #fafafa;
    }
    .table-custom td {
        padding: 16px;
        font-size: 14px;
        color: #3c4043;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }
    .badge-pending { background-color: #fef7e0; color: #b08d00; }
    .badge-approved { background-color: #e6f4ea; color: #137333; }
    .badge-rejected { background-color: #fce8e6; color: #c5221f; }
    
    /* Placeholder Chart */
    .chart-placeholder {
        width: 100%;
        height: 250px;
        background: #f8f9fa;
        border: 2px dashed #dadce0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9aa0a6;
        font-size: 14px;
        font-weight: 500;
    }

</style>

<div class="dashboard-container">

    {{-- Summary Grid --}}
    <div class="summary-grid">
        <div class="card-summary">
            <div class="card-header-flex">
                <span class="card-title">Total Pinjaman Aktif</span>
                <div class="card-icon-wrapper icon-aktif">
                    <svg fill="none" stroke="currentColor" stroke-width="2" width="24" height="24" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div>
                <div class="card-value">{{ number_format($stats['total_pinjaman_aktif'] ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 13px; color: #5f6368;">Akun aktif saat ini</div>
            </div>
        </div>

        <div class="card-summary">
            <div class="card-header-flex">
                <span class="card-title">Total Outstanding</span>
                <div class="card-icon-wrapper icon-out">
                    <svg fill="none" stroke="currentColor" stroke-width="2" width="24" height="24" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
            </div>
            <div>
                <div class="card-value">Rp {{ number_format($stats['total_outstanding'] ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 13px; color: #5f6368;">Sisa pinjaman anggota</div>
            </div>
        </div>

        <div class="card-summary">
            <div class="card-header-flex">
                <span class="card-title">Pengajuan Pending</span>
                <div class="card-icon-wrapper icon-pending">
                    <svg fill="none" stroke="currentColor" stroke-width="2" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>
            <div>
                <div class="card-value">{{ number_format($stats['jumlah_pengajuan_pending'] ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 13px; color: #5f6368;">Menunggu persetujuan</div>
            </div>
        </div>

        <div class="card-summary">
            <div class="card-header-flex">
                <span class="card-title">Pinjaman Macet / Telat</span>
                <div class="card-icon-wrapper icon-macet">
                    <svg fill="none" stroke="currentColor" stroke-width="2" width="24" height="24" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
            </div>
            <div>
                <div class="card-value">{{ number_format($stats['jumlah_pinjaman_macet'] ?? 0, 0, ',', '.') }}</div>
                <div style="font-size: 13px; color: #5f6368;">Perlu tindak lanjut</div>
            </div>
        </div>
    </div>

    {{-- Main Content Flex Row --}}
    <div class="dashboard-flex-row">
        
        {{-- Chart Block --}}
        <div class="flex-col-left">
            <div class="content-block">
                <h3 class="block-header">Grafik Pinjaman (Opsional)</h3>
                <div class="chart-placeholder">
                    <span>[ Canvas Grafik Pertumbuhan Pinjaman ]</span>
                </div>
            </div>
        </div>

        {{-- Table Block --}}
        <div class="flex-col-right">
            <div class="content-block" style="padding: 0;">
                <div class="block-header" style="padding: 24px 24px 0 24px;">
                    Pengajuan Terbaru
                    <a href="{{ route('pinjaman.pengajuan') }}">Lihat Semua &rarr;</a>
                </div>
                
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Anggota</th>
                            <th>Jenis Pinjaman</th>
                            <th>Nominal & Tenor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan_terbaru as $item)
                        <tr>
                            <td style="color: #5f6368;">{{ $item->tanggal }}</td>
                            <td>
                                <div style="font-weight: 600; color: #111;">{{ $item->nama }}</div>
                                <div style="font-size: 12px; color: #888;">NIK: {{ $item->nik }}</div>
                            </td>
                            <td>{{ $item->jenis }}</td>
                            <td>
                                <div style="font-weight: 600;">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                <div style="font-size: 12px; color: #5f6368;">Tenor: {{ $item->tenor }} Bln</div>
                            </td>
                            <td>
                                @if(strtolower($item->status) == 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif(strtolower($item->status) == 'approved')
                                    <span class="badge badge-approved">Approved</span>
                                @else
                                    <span class="badge badge-rejected">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888;">Belum ada data pengajuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
