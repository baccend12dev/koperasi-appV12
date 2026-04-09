{{-- resources/views/anggota/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil: ' . $anggota->nama_anggota)

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('anggota.index') }}" class="tb-link active">Karyawan</a>
    <a href="{{ route('departemen.index') }}" class="tb-link">Departemen</a>
    <a href="{{ route('learning.index') }}" class="tb-link">Learning</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('konfigurasi.index') }}" class="tb-link">Konfigurasi</a>
@endsection

{{-- ── Subbar ── --}}
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
    /* ── Layout ── */
    .profile-layout {
        display: flex;
        align-items: flex-start;
        gap: 24px;
        padding: 24px;
    }
    
    /* ── Sidebar ── */
    .profile-sidebar {
        width: 320px;
        flex-shrink: 0;
        background: #fff;
        border: 1px solid var(--border, #E5E7EB);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .ps-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
    }
    .ps-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--accent, #1a56db);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        overflow: hidden;
    }
    .ps-avatar img { width:100%; height:100%; object-fit:cover; }
    .ps-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-1, #111827);
        line-height: 1.3;
        margin: 0;
    }
    .ps-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
    }
    .ps-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .badge-green { background: #DEF7EC; color: #03543F; }
    .badge-red { background: #FDE8E8; color: #9B1C1C; }
    .badge-blue { background: #E1EFFE; color: #1E429F; }

    .ps-divider {
        height: 1px;
        background: var(--border, #E5E7EB);
        margin: 0;
        border: none;
    }
    .ps-info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .ps-info-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .ps-info-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-3, #6B7280);
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .ps-info-value {
        font-size: 13px;
        color: var(--text-1, #111827);
        font-weight: 500;
    }

    /* ── Main Content ── */
    .profile-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Summary Cards */
    .pm-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .pm-sum-card {
        background: #fff;
        border: 1px solid var(--border, #E5E7EB);
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .pm-sum-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-3, #6B7280);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .pm-sum-val {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-1, #111827);
        line-height: 1.2;
    }
    .pm-sum-val.accent { color: var(--accent, #1a56db); }
    .pm-sum-val.red { color: #E02424; }
    .pm-sum-sub {
        font-size: 12px;
        color: var(--text-2, #4b5563);
    }

    /* Main Container (Tabs) */
    .pm-container {
        background: #fff;
        border: 1px solid var(--border, #E5E7EB);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    /* Tabs Header */
    .ag-tabs {
        display: flex;
        gap: 0;
        border-bottom: 1px solid var(--border, #E5E7EB);
        background: var(--surface, #fff);
        padding: 0 16px;
    }
    .ag-tab {
        padding: 14px 24px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-2, #4b5563);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
        display: flex;
        align-items: center;
        gap: 8px;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
    }
    .ag-tab:hover { color: var(--accent, #1a56db); }
    .ag-tab.active {
        color: var(--accent, #1a56db);
        border-bottom-color: var(--accent, #1a56db);
    }
    .ag-tab-count {
        font-size: 10px;
        font-weight: 700;
        background: var(--bg, #F3F4F6);
        color: var(--text-3, #6B7280);
        padding: 2px 8px;
        border-radius: 10px;
        min-width: 20px;
        text-align: center;
    }
    .ag-tab.active .ag-tab-count {
        background: rgba(26,86,219,0.1);
        color: var(--accent, #1a56db);
    }
    
    /* Tab Bodies */
    .ag-tab-body { display: none; }
    .ag-tab-body.active { display: block; }
    
    /* Simpanan Breakdown */
    .pm-breakdown {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        border-bottom: 1px solid var(--border, #E5E7EB);
        background: #FAFAFA;
    }
    .pm-bk-item {
        padding: 16px 24px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        border-right: 1px solid var(--border, #E5E7EB);
    }
    .pm-bk-item:last-child { border-right: none; }
    .pm-bk-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-3, #6B7280);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .pm-bk-val {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-1, #111827);
    }

    /* Sub Tabs Pinjaman */
    .ag-subtabs {
        display: flex;
        gap: 0;
        padding: 0 16px;
        border-bottom: 1px solid var(--border, #E5E7EB);
        background: #FAFAFA;
    }
    .ag-subtab {
        padding: 12px 20px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-2, #4b5563);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
        background: none;
        border-top: none; border-left: none; border-right: none;
    }
    .ag-subtab:hover { color: var(--text-1, #111827); }
    .ag-subtab.active {
        color: var(--text-1, #111827);
        border-bottom-color: var(--text-1, #111827);
    }
    .ag-sub-body { display: none; }
    .ag-sub-body.active { display: block; }

    /* Tables */
    .ag-table-wrap { overflow-x: auto; }
    .ag-table { width: 100%; border-collapse: collapse; }
    .ag-table thead th {
        text-align: left;
        padding: 12px 24px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-3, #6B7280);
        border-bottom: 1px solid var(--border, #E5E7EB);
        background: #fff;
    }
    .ag-table tbody td {
        padding: 16px 24px;
        font-size: 13px;
        color: var(--text-1, #111827);
        border-bottom: 1px solid var(--border, #E5E7EB);
        vertical-align: middle;
    }
    .ag-table tbody tr:hover { background: #F9FAFB; }
    .ag-empty { text-align: center; padding: 40px !important; color: var(--text-3, #6B7280); font-size: 13px; }

    .ag-dot {
        display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px;
    }
    .dot-pokok { background: #F57C00; }
    .dot-wajib { background: #059669; }
    .dot-sukarela { background: #1a56db; }
</style>

<div class="profile-layout">
    
    {{-- ── SIDEBAR (Informasi Pribadi) ── --}}
    <div class="profile-sidebar">
        <div class="ps-header">
            <div class="ps-avatar">
                @if($anggota->foto)
                    <img src="{{ Storage::url($anggota->foto) }}" alt="{{ $anggota->nama_anggota }}">
                @else
                    {{ strtoupper(substr($anggota->nama_anggota, 0, 2)) }}
                @endif
            </div>
            <h1 class="ps-name">{{ $anggota->nama_anggota }}</h1>
            <div class="ps-badges">
                <span class="ps-badge {{ strtolower($anggota->status_anggota ?? 'aktif') == 'aktif' ? 'badge-green' : 'badge-red' }}">
                    {{ strtoupper($anggota->status_anggota ?? 'Aktif') }}
                </span>
                <span class="ps-badge badge-blue">{{ strtoupper($anggota->ikatan_kerja ?? 'Permanent') }}</span>
            </div>
        </div>

        <a href="{{ route('anggota.edit', $anggota) }}" class="btn-secondary" style="justify-content:center; width:100%;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px;"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            Edit Profil
        </a>

        <hr class="ps-divider">

        <div class="ps-info-list">
            <div class="ps-info-item">
                <span class="ps-info-label">NIK OTTO</span>
                <span class="ps-info-value">{{ $anggota->nik ?? '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">KTP / No. Identitas</span>
                <span class="ps-info-value">{{ $anggota->no_ktp ?? '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">Departemen</span>
                <span class="ps-info-value">{{ $anggota->departemen ? $anggota->departemen->nama : '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">Bagian / Unit</span>
                <span class="ps-info-value">{{ $anggota->bagian ?? '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">Jabatan</span>
                <span class="ps-info-value">{{ $anggota->jabatan ?? '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">Tgl Masuk</span>
                <span class="ps-info-value">{{ $anggota->tgl_msk ? \Carbon\Carbon::parse($anggota->tgl_msk)->format('d M Y') : '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">No. HP</span>
                <span class="ps-info-value">{{ $anggota->no_hp ?? '—' }}</span>
            </div>
            <div class="ps-info-item">
                <span class="ps-info-label">Jml Tanggungan</span>
                <span class="ps-info-value">{{ $anggota->tanggungan . ' Orang' ?? '—' }}</span>
            </div>
            @if($anggota->alamat)
            <div class="ps-info-item">
                <span class="ps-info-label">Alamat</span>
                <span class="ps-info-value" style="line-height:1.4;">{{ $anggota->alamat }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── MAIN CONTENT (Simpanan / Pinjaman) ── --}}
    <div class="profile-main">
        
        {{-- Summary Grid --}}
        <div class="pm-summary-grid">
            <div class="pm-sum-card">
                <span class="pm-sum-label">Total Simpanan</span>
                <span class="pm-sum-val accent">Rp {{ number_format($total_simpanan, 0, ',', '.') }}</span>
                <span class="pm-sum-sub">Kumulatif saldo</span>
            </div>
            <div class="pm-sum-card">
                <span class="pm-sum-label">Max Pinjaman</span>
                <span class="pm-sum-val">Rp {{ number_format($max_pinjaman, 0, ',', '.') }}</span>
                <span class="pm-sum-sub">Limit plafond</span>
            </div>
            <div class="pm-sum-card">
                <span class="pm-sum-label">Sisa Hutang</span>
                <span class="pm-sum-val red">Rp {{ number_format($sisa_pinjaman, 0, ',', '.') }}</span>
                <span class="pm-sum-sub">Dari {{ $pinjaman_berjalan->count() }} pinjaman aktif</span>
            </div>
            <div class="pm-sum-card">
                <span class="pm-sum-label">Sisa Kuota</span>
                <span class="pm-sum-val">Rp {{ number_format($sisa_kuota, 0, ',', '.') }}</span>
                <span class="pm-sum-sub">Plafond tersedia</span>
            </div>
        </div>

        {{-- Detail Container (Tabs) --}}
        <div class="pm-container">
            <div class="ag-tabs">
                <button class="ag-tab active" onclick="switchMainTab(this, 'simpanan')">
                    Data Simpanan
                    <span class="ag-tab-count">{{ $riwayat_simpanan->count() }}</span>
                </button>
                <button class="ag-tab" onclick="switchMainTab(this, 'pinjaman')">
                    Data Pinjaman
                    <span class="ag-tab-count">{{ $pinjaman_berjalan->count() + $pinjaman_lunas->count() }}</span>
                </button>
            </div>

            {{-- ── TAB: SIMPANAN ── --}}
            <div id="main-simpanan" class="ag-tab-body active">
                <div class="pm-breakdown">
                    <div class="pm-bk-item">
                        <span class="pm-bk-label">Simpanan Pokok</span>
                        <span class="pm-bk-val">Rp {{ number_format($simpanan_pokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="pm-bk-item">
                        <span class="pm-bk-label">Simpanan Wajib</span>
                        <span class="pm-bk-val">Rp {{ number_format($simpanan_wajib, 0, ',', '.') }}</span>
                    </div>
                    <div class="pm-bk-item">
                        <span class="pm-bk-label">Simpanan Sukarela</span>
                        <span class="pm-bk-val">Rp {{ number_format($simpanan_sukarela, 0, ',', '.') }}</span>
                    </div>
                </div>

                @php
                    $groupedByYear = $riwayat_simpanan->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->transaction_date)->format('Y');
                    })->sortKeysDesc();
                @endphp

                @forelse($groupedByYear as $year => $transactionsYear)
                <div class="year-section" style="margin-top: 16px;">
                    <div style="display: flex; gap: 16px; align-items: center; padding: 12px 16px; background: #fff; border-top: 1px solid var(--border, #E5E7EB); border-bottom: 1px solid var(--border, #E5E7EB);">
                        <span style="font-weight: 700; color: var(--text-3, #6B7280); font-size: 13px; text-transform: uppercase;">TAHUN</span>
                        <span style="font-weight: 700; color: var(--text-1, #111827); font-size: 14px;">{{ $year }}</span>
                    </div>
                    
                    <div class="ag-table-wrap">
                        <table class="ag-table">
                            <thead>
                                <tr>
                                    <th>PERIODE</th>
                                    <th style="text-align:right;">POKOK</th>
                                    <th style="text-align:right;">WAJIB</th>
                                    <th style="text-align:right;">SUKARELA</th>
                                    <th style="text-align:right;">BUNGA</th>
                                    <th style="text-align:right;">SHU {{ $year - 1 }}</th>
                                    <th style="text-align:right;">PENGAMBILAN</th>
                                    <th style="text-align:right;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $saldo_awal = 0;
                                    foreach($riwayat_simpanan as $rs) {
                                        if (\Carbon\Carbon::parse($rs->transaction_date)->format('Y') < $year) {
                                            $saldo_awal += ($rs->simpanan_pokok + $rs->simpanan_wajib + $rs->simpanan_sukarela);
                                        }
                                    }

                                    $groupedByPeriod = $transactionsYear->groupBy(function($item) {
                                        return \Carbon\Carbon::parse($item->transaction_date)->format('Y-m');
                                    })->sortKeys();
                                    
                                    $running_total = $saldo_awal;
                                @endphp
                                
                                <tr>
                                    <td style="font-weight:500;">saldo awal {{ $year }}</td>
                                    <td style="text-align:right;">-</td>
                                    <td style="text-align:right;">-</td>
                                    <td style="text-align:right;">-</td>
                                    <td style="text-align:right;">-</td>
                                    <td style="text-align:right;">-</td>
                                    <td style="text-align:right;">-</td>
                                    <td style="text-align:right; font-weight:600;">{{ number_format($running_total, 0, ',', '.') }}</td>
                                </tr>

                                @foreach($groupedByPeriod as $periodKey => $transactions)
                                    @php
                                        $pokok = 0;
                                        $wajib = 0;
                                        $sukarela = 0;
                                        $pengambilan = 0;
                                        foreach($transactions as $t) {
                                            $pokok += $t->simpanan_pokok;
                                            $wajib += $t->simpanan_wajib;
                                            $sukarela += $t->simpanan_sukarela;
                                        }
                                        $row_total = $pokok + $wajib + $sukarela - $pengambilan;
                                        $running_total += $row_total;
                                        $period_label = \Carbon\Carbon::parse($periodKey . '-01')->translatedFormat('M-y');
                                    @endphp
                                    <tr>
                                        <td style="font-weight:500;">{{ $period_label }}</td>
                                        <td style="text-align:right;">{{ $pokok > 0 ? number_format($pokok, 0, ',', '.') : '-' }}</td>
                                        <td style="text-align:right;">{{ $wajib > 0 ? number_format($wajib, 0, ',', '.') : '-' }}</td>
                                        <td style="text-align:right;">{{ $sukarela > 0 ? number_format($sukarela, 0, ',', '.') : '-' }}</td>
                                        <td style="text-align:right;">-</td>
                                        <td style="text-align:right;">-</td>
                                        <td style="text-align:right;">{{ $pengambilan > 0 ? number_format($pengambilan, 0, ',', '.') : '-' }}</td>
                                        <td style="text-align:right; font-weight:600;">{{ number_format($running_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="ag-empty" style="padding: 40px; text-align: center; color: var(--text-3);">Belum ada riwayat transaksi simpanan.</div>
                @endforelse
            </div>

            {{-- ── TAB: PINJAMAN ── --}}
            <div id="main-pinjaman" class="ag-tab-body">
                <div class="ag-subtabs">
                    <button class="ag-subtab active" onclick="switchSubTab(this, 'berjalan')">
                        Sedang Berjalan ({{ $pinjaman_berjalan->count() }})
                    </button>
                    <button class="ag-subtab" onclick="switchSubTab(this, 'lunas')">
                        Histori Selesai/Ditolak ({{ $pinjaman_lunas->count() }})
                    </button>
                </div>

                <div id="sub-berjalan" class="ag-sub-body active">
                    <div class="ag-table-wrap">
                        <table class="ag-table">
                            <thead>
                                <tr>
                                    <th>Tgl Pengajuan</th>
                                    <th>Program Pinjaman</th>
                                    <th>Tenor/Bunga</th>
                                    <th style="text-align:right;">Kredit</th>
                                    <th style="text-align:right;">Sisa Tagihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pinjaman_berjalan as $p)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                                    <td style="font-weight:500; color:var(--accent);">{{ $p->masterJenisPinjaman->nama_pinjaman ?? 'Pinjaman' }}</td>
                                    <td style="color:var(--text-3);">{{ $p->tenor }} bln &middot; {{ $p->bunga }}%</td>
                                    <td style="text-align:right; font-weight:600;">Rp {{ number_format($p->jumlah_pinjaman ?? $p->jumlah_pengajuan, 0, ',', '.') }}</td>
                                    <td style="text-align:right; font-weight:600; color:#E02424;">
                                        Rp {{ number_format($p->status == 'berjalan' ? $p->sisa_pinjaman : ($p->jumlah_pengajuan + ($p->jumlah_pengajuan * ($p->bunga/100) * $p->tenor)), 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="ag-empty">Tidak ada pinjaman yang sedang berjalan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="sub-lunas" class="ag-sub-body">
                    <div class="ag-table-wrap">
                        <table class="ag-table">
                            <thead>
                                <tr>
                                    <th>Tgl Pengajuan</th>
                                    <th>Program Pinjaman</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pinjaman_lunas as $pl)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pl->created_at)->format('d M Y') }}</td>
                                    <td style="font-weight:500;">{{ $pl->masterJenisPinjaman->nama_pinjaman ?? 'Pinjaman' }}</td>
                                    <td>
                                        @if($pl->status == 'lunas')
                                            <span class="ps-badge badge-green">Lunas</span>
                                        @else
                                            <span class="ps-badge badge-red">Ditolak</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right; font-weight:600;">Rp {{ number_format($pl->jumlah_pinjaman ?? $pl->jumlah_pengajuan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="ag-empty">Tidak ada riwayat pinjaman di masa lalu.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function switchMainTab(el, tabId) {
        document.querySelectorAll('.ag-tab-body').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.ag-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('main-' + tabId).classList.add('active');
        el.classList.add('active');
    }

    function switchSubTab(el, subId) {
        document.querySelectorAll('.ag-sub-body').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.ag-subtab').forEach(t => t.classList.remove('active'));
        document.getElementById('sub-' + subId).classList.add('active');
        el.classList.add('active');
    }
</script>
@endsection
