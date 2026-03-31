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
    .block-header {
        font-size: 16px;
        font-weight: 600;
        color: #202124;
        padding: 20px 24px;
        margin: 0;
        border-bottom: 1px solid #e0e0e0;
        background: #fafafa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Filter styles */
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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .filter-item:hover {
        background: #f1f3f4;
    }
    .filter-item.active {
        background: #e8f0fe;
        color: #1a73e8;
        font-weight: 600;
    }
    .search-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #dadce0;
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 12px;
        outline: none;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .search-input:focus {
        border-color: #1a73e8;
    }
    .btn-search {
        width: 100%;
        padding: 10px;
        background: #1a73e8;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-search:hover {
        background: #1557b0;
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
        background: #fff;
    }
    .table-custom td {
        padding: 16px;
        font-size: 14px;
        color: #3c4043;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    .main-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .main-row:hover {
        background-color: #f8f9fa;
    }
    .main-row.expanded {
        background-color: #f8f9fa;
        border-left: 3px solid #1a73e8;
    }
    .detail-row {
        background-color: #f8f9fa;
        display: none;
    }
    .detail-row.show {
        display: table-row;
    }
    .detail-table-wrapper {
        padding: 16px 24px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin: 8px 16px 16px 16px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }
    .detail-table th {
        font-size: 11px;
        padding: 8px;
        background: #fafafa;
        border-bottom: 1px solid #e0e0e0;
    }
    .detail-table td {
        font-size: 13px;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
    }
    .badge-count { background: #e8f0fe; color: #1a73e8; }
    .badge-berjalan { background-color: #e6f4ea; color: #137333; }
    .badge-lunas { background-color: #f1f3f4; color: #5f6368; }
    
    .pagination-wrapper {
        padding: 16px 24px;
        border-top: 1px solid #e0e0e0;
        background: #fff;
    }
    
    .currency {
        font-variant-numeric: tabular-nums;
        font-weight: 500;
    }
</style>

<div class="with-sidebar">

    <!-- Sidebar Filter -->
    <div class="sidebar-panel">
        <form action="{{ route('pinjaman.index') }}" method="GET" id="filterForm">
            <!-- Retain current status if filtering by anything else -->
            <input type="hidden" name="status" value="{{ $status }}">
            
            <div class="filter-group">
                <div class="filter-title">Pencarian</div>
                <input type="text" name="search" class="search-input" placeholder="Cari Nama / NIK anggota..." value="{{ request('search') }}">
                <button type="submit" class="btn-search">Cari</button>
            </div>
            
            <div class="filter-group">
                <div class="filter-title">Status Pinjaman</div>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'semua']) }}" class="filter-item {{ $status === 'semua' ? 'active' : '' }}">
                    Semua Status
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'berjalan']) }}" class="filter-item {{ $status === 'berjalan' ? 'active' : '' }}">
                    Berjalan
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'lunas']) }}" class="filter-item {{ $status === 'lunas' ? 'active' : '' }}">
                    Lunas
                </a>
            </div>

            <div class="filter-group">
                <div class="filter-title">Departemen</div>
                <a href="{{ request()->fullUrlWithQuery(['departemen_id' => null]) }}" class="filter-item {{ request('departemen_id') ? '' : 'active' }}">
                    Semua Departemen
                </a>
                @foreach($departemens as $dept)
                <a href="{{ request()->fullUrlWithQuery(['departemen_id' => $dept->id]) }}" class="filter-item {{ request('departemen_id') == $dept->id ? 'active' : '' }}">
                    {{ $dept->nama }}
                </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-panel">
        <div class="content-block">
            <h3 class="block-header">
                Data Pinjaman Anggota
                @if($search || $status !== 'semua' || request('departemen_id'))
                    <a href="{{ route('pinjaman.index') }}" style="font-size: 13px; font-weight: normal; color: #1a73e8; text-decoration: none;">Reset Filter</a>
                @endif
            </h3>
            
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th>Anggota</th>
                        <th>Departemen</th>
                        <th>Total Pokok</th>
                        <th>Total Terbayar</th>
                        <th>Total Sisa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggotaList as $anggota)
                        @php
                            $total_pokok = $anggota->pinjaman->sum('jumlah_pinjaman');
                            $total_bunga = $anggota->pinjaman->sum('total_bunga');
                            $total_pinjaman_bunga = $anggota->pinjaman->sum('total_pinjaman');
                            $total_sisa = $anggota->pinjaman->sum('sisa_pinjaman');
                            $total_terbayar = $anggota->pinjaman->sum('total_terbayar');
                            $count_berjalan = $anggota->pinjaman->where('status', 'berjalan')->count();
                            $count_total = $anggota->pinjaman->count();
                        @endphp
                        
                        <!-- Main Row -->
                        <tr class="main-row" onclick="toggleRow('detail-{{ $anggota->id }}')">
                            <td style="color: #1a73e8;">
                                <svg id="icon-detail-{{ $anggota->id }}" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" viewBox="0 0 24 24" style="transition: transform 0.2s;">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #111;">{{ $anggota->nama_anggota }}</div>
                                <div style="font-size: 12px; color: #888;">NIK: {{ $anggota->nik }}</div>
                            </td>
                            <td>
                                {{ $anggota->departemen->nama ?? '-' }}
                            </td>
                            <td>
                                <div class="currency">Rp {{ number_format($total_pokok, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div class="currency" style="color: #137333;">Rp {{ number_format($total_terbayar, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div class="currency" style="color: #c5221f;">Rp {{ number_format($total_sisa, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <span class="badge badge-count">{{ $count_berjalan }} Berjalan</span>
                            </td>
                        </tr>
                        
                        <!-- Detail Row -->
                        <tr class="detail-row" id="detail-{{ $anggota->id }}">
                            <td colspan="7" style="padding: 0; background: #fafafa;">
                                <div class="detail-table-wrapper">
                                    <div style="font-size: 13px; font-weight: 600; color: #5f6368; margin-bottom: 12px; display: flex; justify-content: space-between;">
                                        <span>Rincian Kepemilikan Pinjaman</span>
                                        <span>Total (Pokok + Bunga): Rp {{ number_format($total_pinjaman_bunga, 0, ',', '.') }}</span>
                                    </div>
                                    <table class="detail-table">
                                        <thead>
                                            <tr>
                                                <th>Jenis Pinjaman</th>
                                                <th>Jumlah (Pokok)</th>
                                                <th>Bunga</th>
                                                <th>Tenor</th>
                                                <th>Sisa Tenor</th>
                                                <th>Terbayar</th>
                                                <th>Sisa Pinjaman</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($anggota->pinjaman as $p)
                                            <tr>
                                                <td style="font-weight: 500;">{{ $p->jenisPinjaman->nama_pinjaman ?? 'Pinjaman' }}</td>
                                                <td class="currency">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                                                <td class="currency">Rp {{ number_format($p->total_bunga, 0, ',', '.') }}</td>
                                                <td>{{ $p->tenor }} Bln</td>
                                                <td>{{ $p->sisa_tenor }} Bln</td>
                                                <td class="currency" style="color: #137333;">Rp {{ number_format($p->total_terbayar, 0, ',', '.') }}</td>
                                                <td class="currency" style="color: #c5221f;">Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}</td>
                                                <td>
                                                    @if(strtolower($p->status) == 'berjalan')
                                                        <span class="badge badge-berjalan">Berjalan</span>
                                                    @else
                                                        <span class="badge badge-lunas">Lunas</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #888; padding: 40px 16px;">
                                @if($search || $status !== 'semua' || request('departemen_id'))
                                    Tidak ada data pinjaman yang sesuai dengan filter.
                                @else
                                    Belum ada data pinjaman.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="pagination-wrapper">
                {{ $anggotaList->links() }}
            </div>
            
        </div>
    </div>

</div>

<script>
    function toggleRow(id) {
        var row = document.getElementById(id);
        var icon = document.getElementById('icon-' + id);
        
        // Cek jika null (mungkin ID tidak valid)
        if(!row) return;
        
        if (row.classList.contains('show')) {
            row.classList.remove('show');
            icon.style.transform = "rotate(0deg)";
        } else {
            row.classList.add('show');
            icon.style.transform = "rotate(90deg)";
        }
    }
</script>
@endsection
