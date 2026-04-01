{{-- resources/views/pinjaman/aktif_show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Riwayat Angsuran')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link">Pengajuan Pinjaman</a>
    <a href="{{ route('pinjaman.approval') }}" class="tb-link">Approval Pinjaman</a>
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link active">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Pembayaran Angsuran</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link">Master Jenis Pinjaman</a>
@endsection

@section('page-title', 'Detail Riwayat Angsuran')
@section('page-subtitle', ($pinjaman->jenisPinjaman->nama_pinjaman ?? 'Pinjaman') . ' #LP-' . str_pad($pinjaman->id, 4, '0', STR_PAD_LEFT))

@section('subbar-actions')
    <a href="{{ route('pinjaman.aktif') }}" class="btn-secondary" style="margin-right: 10px;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="display:inline; margin-right:4px;">
            <path d="M9 11L5 7l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali
    </a>
@endsection

@section('content')
<style>
.detail-container {
    font-family: system-ui, -apple-system, sans-serif;
    font-size: 13px;
    color: #111827;
    padding: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

/* ── Header info ── */
.dh-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}
.dh-avatar {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: #EFF6FF;
    border: 2px solid #BFDBFE;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; color: #1a56db;
    flex-shrink: 0;
}
.dh-info strong {
    display: block; font-size: 13px; font-weight: 700; color: #111827;
}
.dh-info span {
    display: block; font-size: 11px; color: #9CA3AF;
}

/* ── Stat cards row ── */
.stat-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
}
.s-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 20px 22px;
    position: relative;
    overflow: hidden;
}
.s-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.s-card.sisa::before  { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.s-card.bayar::before { background: linear-gradient(90deg, #10b981, #34d399); }
.s-card.prog::before  { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

.s-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #9CA3AF;
    margin-bottom: 8px;
}
.s-amount {
    font-size: 22px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 4px;
}
.s-card.sisa .s-amount { color: #d97706; }
.s-card.bayar .s-amount { color: #059669; }
.s-card.prog .s-amount { color: #2563eb; }

.s-sub {
    font-size: 11px;
    color: #9CA3AF;
    display: flex; align-items: center; gap: 4px;
}
.s-sub svg { flex-shrink: 0; }

/* ── Sisa card with breakdown ── */
.sisa-wrapper {
    display: flex;
    gap: 0;
}
.sisa-main {
    flex: 1;
    min-width: 0;
}
.sisa-divider {
    width: 1px;
    background: #E5E7EB;
    margin: 0 18px;
    align-self: stretch;
}
.sisa-breakdown {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
    min-width: 160px;
}
.sisa-breakdown .bd-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.sisa-breakdown .bd-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9CA3AF;
}
.sisa-breakdown .bd-value {
    font-size: 14px;
    font-weight: 700;
    color: #374151;
}
.sisa-breakdown .bd-value.pokok { color: #1e40af; }
.sisa-breakdown .bd-value.bunga { color: #b45309; }

/* ── Progress bar ── */
.prog-track {
    height: 8px;
    background: #E5E7EB;
    border-radius: 10px;
    overflow: hidden;
    margin: 12px 0 6px;
}
.prog-fill {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    transition: width .4s ease;
}
.prog-label {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #9CA3AF;
}

/* ── Table card ── */
.tbl-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    overflow: hidden;
}
.tbl-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #F3F4F6;
}
.tbl-title {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
    display: flex; align-items: center; gap: 8px;
}
.tbl-title svg { color: #1a56db; }
.tbl-actions {
    display: flex; gap: 8px;
}
.tbl-actions button {
    width: 32px; height: 32px;
    border-radius: 7px;
    border: 1px solid #E5E7EB;
    background: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #6B7280;
    transition: all .15s;
}
.tbl-actions button:hover { background: #F3F4F6; color: #111827; }

/* ── Data table ── */
.dtbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.dtbl thead th {
    padding: 12px 16px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9CA3AF;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
    text-align: center;
    white-space: nowrap;
}
.dtbl thead th:first-child { text-align: center; }
.dtbl tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #F3F4F6;
    color: #4B5563;
    text-align: right;
    vertical-align: middle;
}
.dtbl tbody td:first-child {
    text-align: center;
    font-weight: 700;
    color: #111827;
}
.dtbl tbody tr:last-child td { border-bottom: none; }
.dtbl tbody tr:hover td { background: #F9FAFB; }

/* ── Status pill ── */
.st-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.st-pill::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
}
.st-lunas   { background: #F0FDF4; color: #15803d; }
.st-belum   { background: #FEF3C7; color: #b45309; }
</style>

<div class="detail-container">

    {{-- ── Anggota info ── --}}
    <div class="dh-row">
        <div class="dh-avatar">
            {{ strtoupper(substr($pinjaman->anggota->nama_anggota ?? 'U', 0, 2)) }}
        </div>
        <div class="dh-info">
            <strong>{{ $pinjaman->anggota->nama_anggota ?? 'Unknown' }}</strong>
            <span>ID: KOP-{{ str_pad($pinjaman->anggota->id ?? 0, 5, '0', STR_PAD_LEFT) }} &middot; NIK: {{ $pinjaman->anggota->nik ?? '-' }}</span>
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="stat-row">

        {{-- Sisa Pinjaman --}}
        <div class="s-card sisa">
            <div class="sisa-wrapper">
                <div class="sisa-main">
                    <div class="s-label">Sisa Pinjaman</div>
                    <div class="s-amount">Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</div>
                    <div class="s-sub">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 2v8M3 7l3 3 3-3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @php
                            $sisaPersen = $pinjaman->total_pinjaman > 0 ? round(($pinjaman->sisa_pinjaman / $pinjaman->total_pinjaman) * 100, 1) : 0;
                        @endphp
                        ~ {{ $sisaPersen }}% dari total pinjaman
                    </div>
                </div>
                <div class="sisa-divider"></div>
                <div class="sisa-breakdown">
                    <div class="bd-item">
                        <span class="bd-label">Pinjaman Pokok</span>
                        <span class="bd-value pokok">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</span>
                    </div>
                    <div class="bd-item">
                        <span class="bd-label">Total Bunga ({{ $pinjaman->bunga }}%)</span>
                        <span class="bd-value bunga">Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Terbayar --}}
        <div class="s-card bayar">
            <div class="s-label">Total Terbayar</div>
            <div class="s-amount">Rp {{ number_format($pinjaman->total_terbayar, 0, ',', '.') }}</div>
            <div class="s-sub">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $angsuranLunas }} dari {{ $totalAngsuran }} angsuran selesai
            </div>
        </div>

        {{-- Progress Pembayaran --}}
        <div class="s-card prog">
            <div class="s-label">Progress Pembayaran</div>
            <div class="s-amount">{{ $progressPersen }}%</div>
            <div class="prog-track">
                <div class="prog-fill" style="width: {{ $progressPersen }}%"></div>
            </div>
            <div class="prog-label">
                <span>Target selesai:</span>
                <span>{{ $pinjaman->tanggal_selesai ? \Carbon\Carbon::parse($pinjaman->tanggal_selesai)->format('M Y') : '-' }}</span>
            </div>
        </div>
    </div>

    {{-- ── Tabel Angsuran ── --}}
    <div class="tbl-card">
        <div class="tbl-head">
            <div class="tbl-title">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <rect x="1.5" y="2" width="13" height="12" rx="2" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M1.5 6h13M5.5 6v8M10.5 6v8" stroke="currentColor" stroke-width="1.1"/>
                </svg>
                Tabel Angsuran
            </div>
            <div class="tbl-actions">
                <button title="Print" onclick="window.print()">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="3" y="8" width="8" height="4" rx="0.5" stroke="currentColor" stroke-width="1.1"/><path d="M3 10H1.5a1 1 0 01-1-1V5.5a1 1 0 011-1h11a1 1 0 011 1V9a1 1 0 01-1 1H11" stroke="currentColor" stroke-width="1.1"/><path d="M3 4.5V1.5a.5.5 0 01.5-.5h7a.5.5 0 01.5.5v3" stroke="currentColor" stroke-width="1.1"/></svg>
                </button>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="dtbl">
                <thead>
                    <tr>
                        <th>Angsuran Ke-</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Bayar</th>
                        <th>Jumlah Tagihan</th>
                        <th>Jumlah Dibayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman->angsuran as $row)
                        <tr>
                            <td>{{ $row->angsuran_ke }}</td>
                            <td style="text-align:center">
                                {{ $row->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($row->tanggal_jatuh_tempo)->format('d M Y') : '-' }}
                            </td>
                            <td style="text-align:center">
                                {{ $row->tanggal_bayar ? \Carbon\Carbon::parse($row->tanggal_bayar)->format('d M Y') : '-' }}
                            </td>
                            <td>Rp {{ number_format($row->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($row->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td style="text-align:center">
                                @if($row->status === 'sudah_bayar')
                                    <span class="st-pill st-lunas">Lunas</span>
                                @else
                                    <span class="st-pill st-belum">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#9CA3AF; padding:32px 16px;">
                                Belum ada data angsuran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
