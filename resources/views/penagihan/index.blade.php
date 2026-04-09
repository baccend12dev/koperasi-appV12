{{-- resources/views/penagihan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Penagihan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('penagihan.index') }}" class="tb-link active">Dashboard</a>
    <a href="{{ route('penagihan.generator') }}" class="tb-link">Tagihan Generator</a>
@endsection

@section('page-title', 'Dashboard Penagihan')

@section('content')
<style>
    .with-sidebar {
        display: flex;
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
        gap: 24px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    .sidebar-panel {
        flex: 0 0 280px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 24px;
        height: fit-content;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .main-panel {
        flex: 1;
        min-width: 0;
    }
    .content-block {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .filter-group {
        margin-bottom: 24px;
    }
    .filter-title {
        font-size: 12px;
        font-weight: 700;
        color: #5f6368;
        text-transform: uppercase;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }
    .filter-item {
        display: block;
        padding: 8px 12px;
        color: #3c4043;
        text-decoration: none;
        font-size: 14px;
        border-radius: 6px;
        margin-bottom: 4px;
        transition: background-color 0.2s, color 0.2s;
    }
    .filter-item:hover {
        background: #f1f3f4;
    }
    .filter-item.active {
        background: #e8f0fe;
        color: #1a73e8;
        font-weight: 600;
    }
    .stat-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }
    .stat-title {
        font-size: 13px;
        font-weight: 600;
        color: #5f6368;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }
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
        vertical-align: top;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-lunas { background-color: #e6f4ea; color: #137333; }
    .badge-belum { background-color: #fce8e6; color: #c5221f; }
</style>

<div class="with-sidebar">

    <!-- Sidebar Filter -->
    <div class="sidebar-panel">
        <div class="filter-group">
            <div class="filter-title">Periode Tagihan</div>
            @forelse($availableBills->groupBy(function($item) { return \Carbon\Carbon::parse($item->tgl_generate)->format('Y'); }) as $year => $billsByYear)
                <div style="font-weight: 600; color: #111; margin: 16px 0 8px 0;">Tahun {{ $year }}</div>
                @foreach($billsByYear as $bill)
                    @php
                        $m = \Carbon\Carbon::parse($bill->tgl_generate)->format('n');
                        $mName = \Carbon\Carbon::parse($bill->tgl_generate)->translatedFormat('F');
                        $isActive = ($selectedYear == $year && $selectedMonth == $m);
                    @endphp
                    <a href="{{ route('penagihan.index', ['year' => $year, 'month' => $m]) }}" class="filter-item {{ $isActive ? 'active' : '' }}">
                        {{ $mName }}
                    </a>
                @endforeach
            @empty
                <div style="font-size: 13px; color: #888;">Belum ada riwayat tagihan.</div>
            @endforelse
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-panel">
        
        @if($currentBill)
            @php
                $totalLunas = $details->where('status', 'Lunas')->sum('total_potongan');
                $totalBelum = $details->where('status', '!=', 'Lunas')->sum('total_potongan');
            @endphp
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
                <div class="stat-card">
                    <span class="stat-title">Total Tagihan</span>
                    <span class="stat-value text-blue-600">Rp {{ number_format($currentBill->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-title">Total Terbayar (Lunas)</span>
                    <span class="stat-value text-green-600">Rp {{ number_format($totalLunas, 0, ',', '.') }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-title">Total Belum Bayar</span>
                    <span class="stat-value text-red-600">Rp {{ number_format($totalBelum, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="content-block">
                <div style="padding: 20px 24px; border-bottom: 1px solid #e0e0e0; background: #fff; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Daftar Rincian Anggota (Periode {{ $currentBill->periode }})</h3>
                    <span class="badge {{ $currentBill->status == 'Paid' ? 'badge-lunas' : 'badge-belum' }}">{{ $currentBill->status }}</span>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Anggota</th>
                                <th style="text-align: right;">Simpanan</th>
                                <th style="text-align: right;">Pinjaman</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $detail)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #111;">{{ $detail->anggota->nama_anggota }}</div>
                                        <div style="font-size: 12px; color: #888;">NIK: {{ $detail->anggota->nik }}</div>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="font-weight: 500;">Rp {{ number_format($detail->jumlah_simpanan, 0, ',', '.') }}</div>
                                        @if($detail->jumlah_simpanan > 0)
                                            <div style="font-size: 11px; color: #888; margin-top: 4px;">
                                                P: {{ number_format($detail->simpanan_pokok, 0, ',', '.') }} |
                                                W: {{ number_format($detail->simpanan_wajib, 0, ',', '.') }} |
                                                S: {{ number_format($detail->simpanan_sukarela, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-weight: 500;">
                                        Rp {{ number_format($detail->jumlah_pinjaman, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: #111827;">
                                        Rp {{ number_format($detail->total_potongan, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($detail->status == 'Lunas')
                                            <span class="badge badge-lunas">Lunas</span>
                                        @else
                                            <span class="badge badge-belum">Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #888; padding: 40px 16px;">
                                        Tidak ada detail rincian untuk tagihan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="content-block" style="padding: 60px 20px; text-align: center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin: 0 auto 16px auto;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <div style="font-size: 16px; font-weight: 600; color: #475569; margin-bottom: 8px;">Tidak Ada Data Tagihan</div>
                <div style="font-size: 14px; color: #64748b;">Silakan pilih bulan/tahun yang tesedia dari sidebar, atau gunakan Generator untuk membuat tagihan baru.</div>
            </div>
        @endif
    </div>

</div>
@endsection
