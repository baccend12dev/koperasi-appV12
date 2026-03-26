{{-- resources/views/anggota/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Karyawan: ' . $anggota->nama_anggota)

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('anggota.index') }}" class="tb-link active">Karyawan</a>
    <a href="{{ route('departemen.index') }}" class="tb-link">Departemen</a>
    <a href="{{ route('learning.index') }}" class="tb-link">Learning</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('konfigurasi.index') }}" class="tb-link">Konfigurasi</a>
@endsection

{{-- ── Subbar kiri ── --}}
@section('subbar-actions')
    <a href="{{ route('anggota.index') }}" class="btn-secondary" style="margin-right: 10px;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="display:inline; margin-right:4px;">
            <path d="M9 11L5 7l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali
    </a>
@endsection

@section('page-title', 'Detail Pekerja')

@section('content')
<style>
    .profile-wrap {
        padding: 24px;
        max-width: 1200px;
        margin: 0 auto;
        color: #333;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Top Card Layering */
    .top-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-bottom: 24px;
        position: relative;
    }
    .top-card-banner {
        height: 100px;
        background-color: #1a73e8; /* Blue exact color */
        border-radius: 8px 8px 0 0;
        width: 100%;
    }
    .top-card-body {
        padding: 24px 24px 24px 160px; /* Leave space for avatar */
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    
    .avatar-wrapper {
        position: absolute;
        bottom: 24px;
        left: 24px;
        width: 110px;
        height: 110px;
        border-radius: 12px;
        border: 4px solid #fff;
        background-color: #1f2937;
        overflow: visible; /* to allow status dot */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }
    .avatar-status-dot {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 20px;
        height: 20px;
        background-color: #34a853;
        border: 3px solid #fff;
        border-radius: 50%;
    }
    
    .profile-info-main {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .profile-info-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .profile-name-lg {
        font-size: 24px;
        font-weight: 700;
        color: #111;
        margin: 0;
    }
    .badge-aktif {
        background-color: #e6f4ea;
        color: #137333;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
        text-transform: uppercase;
    }
    .profile-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        color: #5f6368;
        font-size: 13px;
    }
    .profile-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .profile-actions {
        display: flex;
        gap: 12px;
    }
    .btn-outline {
        border: 1px solid #dadce0;
        background: #fff;
        color: #3c4043;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-outline:hover { background: #f8f9fa; }
    
    .btn-primary-sc {
        border: none;
        background: #1a73e8;
        color: #fff;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-primary-sc:hover { background: #1557b0; }

    /* 4 Stats Grid */
    .four-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-box {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-box-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .stat-icon-wrap {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    .stat-icon-wrap svg { width: 18px; height: 18px; }
    
    /* Specific Icon Colors */
    .icon-simpanan { background: #e8f0fe; color: #1a73e8; }
    .icon-max { background: #fff3e0; color: #f57c00; }
    .icon-pinjaman { background: #f1f3f4; color: #202124; }
    .icon-sisa { background: #e6f4ea; color: #1e8e3e; }
    
    .stat-box-label {
        font-size: 11px;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
    }
    .stat-box-amount {
        font-size: 22px;
        font-weight: 700;
        color: #111;
        margin-bottom: 4px;
    }
    .stat-box-sub {
        font-size: 12px;
        color: #5f6368;
    }
    .sub-green { color: #1e8e3e; font-weight: 500;}
    
    /* Progress */
    .pb-track {
        background-color: #f1f3f4;
        border-radius: 4px;
        height: 6px;
        width: 100%;
        margin-top: 8px;
    }
    .pb-fill {
        background-color: #1e8e3e;
        height: 100%;
        border-radius: 4px;
    }

    /* Tabs */
    .tabs-container {
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        gap: 32px;
        margin-bottom: 24px;
    }
    .tab-item {
        padding: 12px 0;
        font-size: 14px;
        font-weight: 500;
        color: #5f6368;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .tab-item:hover { color: #1a73e8; }
    .tab-item.active {
        color: #1a73e8;
        border-bottom-color: #1a73e8;
    }
    .tab-item svg { width: 16px; height: 16px; }
    
    /* Tab Contents */
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    /* Simpanan Overview Cards */
    .simpanan-overview {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }
    .so-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
    }
    .so-label {
        font-size: 11px;
        font-weight: 600;
        color: #5f6368;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .so-amount {
        font-size: 20px;
        font-weight: 700;
        color: #111;
    }
    
    /* Transaction History Table */
    .history-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .history-title {
        font-size: 18px;
        font-weight: 700;
        color: #111;
        margin: 0;
    }
    .history-link {
        font-size: 12px;
        font-weight: 600;
        color: #1a73e8;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .history-link:hover { text-decoration: underline; }
    
    .table-history {
        width: 100%;
        border-collapse: collapse;
    }
    .table-history th {
        text-align: left;
        padding: 12px 0;
        font-size: 11px;
        font-weight: 600;
        color: #9aa0a6;
        text-transform: uppercase;
        border-bottom: 1px solid #e0e0e0;
    }
    .table-history td {
        padding: 16px 0;
        font-size: 13px;
        color: #3c4043;
        border-bottom: 1px solid #f1f3f4;
    }
    .table-history tr:last-child td { border-bottom: none; }
    
    .type-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .badge-success {
        background-color: #e6f4ea;
        color: #137333;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 4px;
        border: 1px solid #ceead6;
        text-transform: uppercase;
    }
</style>

<div class="profile-wrap">

    {{-- 1. Top Card --}}
    <div class="top-card">
        <div class="top-card-banner"></div>
        
        <div class="avatar-wrapper">
            @if($anggota->foto)
                <img src="{{ Storage::url($anggota->foto) }}" alt="{{ $anggota->nama_anggota }}">
            @else
                <div style="color:#fff; font-size:36px; font-weight:bold;">
                    {{ strtoupper(substr($anggota->nama_anggota, 0, 2)) }}
                </div>
            @endif
            <div class="avatar-status-dot"></div>
        </div>

        <div class="top-card-body">
            <div class="profile-info-main">
                <div class="profile-info-top">
                    <h1 class="profile-name-lg">{{ $anggota->nama_anggota }}</h1>
                    <span class="badge-aktif">{{ strtoupper($anggota->status_anggota ?? 'Aktif') }}</span>
                </div>
                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        {{ $anggota->nik ?? 'N/A' }}
                    </div>
                    <div class="profile-meta-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        {{ $anggota->departemen ? $anggota->departemen->nama : 'Operational' }}
                    </div>
                    <div class="profile-meta-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Joined {{ $anggota->tgl_bergabung ? \Carbon\Carbon::parse($anggota->tgl_bergabung)->format('d M Y') : 'N/A' }}
                    </div>
                </div>
            </div>
            
            <div class="profile-actions">
                <a href="{{ route('anggota.edit', $anggota) }}" class="btn-outline">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    Edit Profile
                </a>
                <button class="btn-primary-sc">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export Data
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Four Stats Grid --}}
    <div class="four-stats-grid">
        <div class="stat-box">
            <div class="stat-box-row">
                <div class="stat-icon-wrap icon-simpanan">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <span class="stat-box-label">TOTAL SIMPANAN</span>
            </div>
            <div>
                <div class="stat-box-amount">Rp {{ number_format($total_simpanan, 0, ',', '.') }}</div>
                <div class="stat-box-sub"><span class="sub-green">↗ +2.4%</span> from last month</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-box-row">
                <div class="stat-icon-wrap icon-max">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 22 7 12 2"></polygon><polyline points="2 17 22 17"></polyline><polyline points="2 12 22 12"></polyline></svg>
                </div>
                <span class="stat-box-label">MAX PINJAMAN</span>
            </div>
            <div>
                <div class="stat-box-amount">Rp {{ number_format($max_pinjaman, 0, ',', '.') }}</div>
                <div class="stat-box-sub">Based on credit score: {{ $credit_score }}</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-box-row">
                <div class="stat-icon-wrap icon-pinjaman">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                </div>
                <span class="stat-box-label">PINJAMAN AKTIF</span>
            </div>
            <div>
                <div class="stat-box-amount">Rp {{ number_format($pinjaman_aktif_amount, 0, ',', '.') }}</div>
                <div class="stat-box-sub">1 Loan account currently active</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-box-row">
                <div class="stat-icon-wrap icon-sisa">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
                <span class="stat-box-label">SISA PINJAMAN</span>
            </div>
            <div>
                <div class="stat-box-amount">Rp {{ number_format($sisa_pinjaman, 0, ',', '.') }}</div>
                @php $pct = ($sisa_pinjaman / max($pinjaman_aktif_amount, 1)) * 100; @endphp
                <div class="pb-track"><div class="pb-fill" style="width:{{ $pct }}%;"></div></div>
            </div>
        </div>
    </div>

    {{-- 3. Tabs --}}
    <div class="tabs-container">
        <div class="tab-item active" onclick="switchTab(this, 'simpanan')">
            <svg fill="none" class="tab-icon" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
            Simpanan
        </div>
        <div class="tab-item" onclick="switchTab(this, 'pinjaman')">
            <svg fill="none" class="tab-icon" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            Pinjaman
        </div>
        <div class="tab-item" onclick="switchTab(this, 'riwayat')">
            <svg fill="none" class="tab-icon" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Riwayat
        </div>
    </div>

    {{-- 4. Tab Contents --}}
    <div id="tab-simpanan" class="tab-content active">
        {{-- Simpanan Overview --}}
        <div class="simpanan-overview">
            <div class="so-card">
                <div class="so-label">BASIC SAVINGS</div>
                <div class="so-amount">Rp {{ number_format($simpanan_pokok, 0, ',', '.') }}</div>
            </div>
            <div class="so-card">
                <div class="so-label">MANDATORY / MO</div>
                <div class="so-amount">Rp {{ number_format($simpanan_wajib, 0, ',', '.') }}</div>
            </div>
            <div class="so-card">
                <div class="so-label">VOLUNTARY BALANCE</div>
                <div class="so-amount">Rp {{ number_format($simpanan_sukarela, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Transaction History Table --}}
        <div class="history-section-header">
            <h3 class="history-title">Riwayat Transaksi Simpanan</h3>
            <a href="#" class="history-link">VIEW FULL REPORT &rarr;</a>
        </div>
        
        <table class="table-history">
            <thead>
                <tr>
                    <th style="width: 20%;">DATE</th>
                    <th style="width: 30%;">TYPE</th>
                    <th style="width: 20%;">AMOUNT</th>
                    <th style="width: 15%;">PERIOD</th>
                    <th style="width: 15%;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat_simpanan as $rs)
                <tr>
                    <td>{{ $rs->date }}</td>
                    <td style="font-weight: 500;">
                        <span class="type-dot" style="background-color: {{ $rs->color }};"></span>
                        {{ $rs->type }}
                    </td>
                    <td style="font-weight: 600; color: #111;">Rp {{ number_format($rs->amount, 0, ',', '.') }}</td>
                    <td style="color: #5f6368;">{{ $rs->period }}</td>
                    <td><span class="badge-success">{{ $rs->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div id="tab-pinjaman" class="tab-content">
        <div style="padding: 40px; text-align: center; color: #5f6368; border: 1px dashed #dadce0; border-radius: 8px;">
            Konten Pinjaman (Fasilitas Aktif) akan ditampilkan di sini.
        </div>
    </div>
    
    <div id="tab-riwayat" class="tab-content">
        <div style="padding: 40px; text-align: center; color: #5f6368; border: 1px dashed #dadce0; border-radius: 8px;">
            Konten Riwayat (Pinjaman Lunas) akan ditampilkan di sini.
        </div>
    </div>

</div>

<script>
    function switchTab(element, tabId) {
        // Hide all
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
        
        // Show target
        document.getElementById('tab-' + tabId).classList.add('active');
        element.classList.add('active');
    }
</script>
@endsection
