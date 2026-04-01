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
    /* ── Page Container ── */
    .ag-page { padding: 0; }

    /* ── Header Strip ── */
    .ag-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 28px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .ag-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .ag-avatar {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: var(--accent);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    .ag-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ag-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-1);
        margin: 0;
        line-height: 1.3;
    }
    .ag-meta {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 2px;
        font-size: 12px;
        color: var(--text-2);
    }
    .ag-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .ag-status-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .ag-status-aktif { background: var(--green-bg); color: var(--green-text); }
    .ag-status-nonaktif { background: var(--red-bg); color: var(--red-text); }

    .ag-header-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── Summary Bar ── */
    .ag-summary {
        display: flex;
        align-items: stretch;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
    }
    .ag-sum-item {
        flex: 1;
        padding: 16px 28px;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .ag-sum-item:last-child { border-right: none; }
    .ag-sum-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-3);
    }
    .ag-sum-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-1);
        line-height: 1.3;
    }
    .ag-sum-value.accent { color: var(--accent); }
    .ag-sum-value.green  { color: var(--green-text); }
    .ag-sum-value.red    { color: var(--red-text); }
    .ag-sum-sub {
        font-size: 11px;
        color: var(--text-3);
        margin-top: 1px;
    }

    /* ── Tabs ── */
    .ag-tabs {
        display: flex;
        gap: 0;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        padding: 0 28px;
    }
    .ag-tab {
        padding: 11px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-2);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
        display: flex;
        align-items: center;
        gap: 6px;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
    }
    .ag-tab:hover { color: var(--accent); }
    .ag-tab.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }
    .ag-tab-count {
        font-size: 10px;
        font-weight: 700;
        background: var(--bg);
        color: var(--text-3);
        padding: 1px 6px;
        border-radius: 8px;
        min-width: 18px;
        text-align: center;
    }
    .ag-tab.active .ag-tab-count {
        background: var(--accent-light);
        color: var(--accent);
    }

    /* ── Tab Content ── */
    .ag-tab-body { display: none; }
    .ag-tab-body.active { display: block; }

    /* ── Sub Tabs (Pinjaman) ── */
    .ag-subtabs {
        display: flex;
        gap: 0;
        padding: 0 28px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .ag-subtab {
        padding: 9px 16px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-3);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
        background: none;
        border-top: none; border-left: none; border-right: none;
    }
    .ag-subtab:hover { color: var(--text-1); }
    .ag-subtab.active {
        color: var(--text-1);
        border-bottom-color: var(--text-1);
    }
    .ag-sub-body { display: none; }
    .ag-sub-body.active { display: block; }

    /* ── Simpanan breakdown row ── */
    .ag-breakdown {
        display: flex;
        align-items: center;
        gap: 0;
        padding: 0 28px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .ag-bk-item {
        padding: 12px 20px 12px 0;
        margin-right: 20px;
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .ag-bk-item:not(:last-child) {
        border-right: 1px solid var(--border);
        padding-right: 20px;
    }
    .ag-bk-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-3);
    }
    .ag-bk-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-1);
    }

    /* ── Table ── */
    .ag-table-wrap {
        padding: 0;
    }
    .ag-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ag-table thead th {
        text-align: left;
        padding: 10px 28px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-3);
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        white-space: nowrap;
    }
    .ag-table tbody td {
        padding: 12px 28px;
        font-size: 13px;
        color: var(--text-1);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .ag-table tbody tr:last-child td { border-bottom: none; }
    .ag-table tbody tr:hover { background: var(--bg); }

    .ag-empty {
        text-align: center;
        padding: 40px 28px !important;
        color: var(--text-3);
        font-size: 13px;
    }

    /* ── Inline badges ── */
    .ag-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 700;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .ag-badge-green  { background: var(--green-bg);  color: var(--green-text); }
    .ag-badge-blue   { background: #E6F1FB; color: #185FA5; }
    .ag-badge-amber  { background: var(--amber-bg);  color: var(--amber-text); }
    .ag-badge-red    { background: var(--red-bg);    color: var(--red-text); }

    /* ── Type dot ── */
    .ag-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        margin-right: 6px;
        flex-shrink: 0;
    }
    .dot-pokok    { background: #F57C00; }
    .dot-wajib    { background: var(--green-text); }
    .dot-sukarela { background: var(--accent); }

    /* ── Amount formatting ── */
    .ag-amount { font-weight: 600; font-variant-numeric: tabular-nums; }
    .ag-amount-red { color: var(--red-text); }
</style>

<div class="ag-page">

    {{-- 1 ▸ Header Strip --}}
    <div class="ag-header">
        <div class="ag-header-left">
            <div class="ag-avatar">
                @if($anggota->foto)
                    <img src="{{ Storage::url($anggota->foto) }}" alt="{{ $anggota->nama_anggota }}">
                @else
                    {{ strtoupper(substr($anggota->nama_anggota, 0, 2)) }}
                @endif
            </div>
            <div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <h1 class="ag-name">{{ $anggota->nama_anggota }}</h1>
                    <span class="ag-status-badge {{ strtolower($anggota->status_anggota ?? 'aktif') == 'aktif' || strtolower($anggota->status_anggota ?? '') == 'active' ? 'ag-status-aktif' : 'ag-status-nonaktif' }}">
                        {{ strtoupper($anggota->status_anggota ?? 'Aktif') }}
                    </span>
                </div>
                <div class="ag-meta">
                    <div class="ag-meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        {{ $anggota->nik ?? 'N/A' }}
                    </div>
                    <div class="ag-meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        {{ $anggota->departemen ? $anggota->departemen->nama : '—' }}
                    </div>
                    <div class="ag-meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        {{ $anggota->tgl_bergabung ? \Carbon\Carbon::parse($anggota->tgl_bergabung)->format('d M Y') : '—' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="ag-header-right">
            <a href="{{ route('anggota.edit', $anggota) }}" class="btn-secondary">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- 2 ▸ Summary Bar --}}
    <div class="ag-summary">
        <div class="ag-sum-item">
            <span class="ag-sum-label">Total Simpanan</span>
            <span class="ag-sum-value accent">Rp {{ number_format($total_simpanan, 0, ',', '.') }}</span>
        </div>
        <div class="ag-sum-item">
            <span class="ag-sum-label">Max Pinjaman</span>
            <span class="ag-sum-value">Rp {{ number_format($max_pinjaman, 0, ',', '.') }}</span>
            <span class="ag-sum-sub">Threshold 5× Simpanan</span>
        </div>
        <div class="ag-sum-item">
            <span class="ag-sum-label">Pinjaman Aktif</span>
            <span class="ag-sum-value">Rp {{ number_format($pinjaman_aktif_amount, 0, ',', '.') }}</span>
            <span class="ag-sum-sub">{{ $pinjaman_berjalan->count() }} fasilitas berjalan</span>
        </div>
        <div class="ag-sum-item">
            <span class="ag-sum-label">Sisa Hutang</span>
            <span class="ag-sum-value red">Rp {{ number_format($sisa_pinjaman, 0, ',', '.') }}</span>
            <span class="ag-sum-sub">Kuota tersisa: Rp {{ number_format($sisa_kuota, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- 3 ▸ Main Tabs --}}
    <div class="ag-tabs">
        <button class="ag-tab active" onclick="switchMainTab(this, 'simpanan')">
            Simpanan
            <span class="ag-tab-count">{{ $riwayat_simpanan->count() }}</span>
        </button>
        <button class="ag-tab" onclick="switchMainTab(this, 'pinjaman')">
            Pinjaman
            <span class="ag-tab-count">{{ $pinjaman_berjalan->count() + $pinjaman_lunas->count() }}</span>
        </button>
    </div>

    {{-- ─── TAB: SIMPANAN ─── --}}
    <div id="main-simpanan" class="ag-tab-body active">

        {{-- Breakdown --}}
        <div class="ag-breakdown">
            <div class="ag-bk-item">
                <span class="ag-bk-label">Simpanan Pokok</span>
                <span class="ag-bk-value">Rp {{ number_format($simpanan_pokok, 0, ',', '.') }}</span>
            </div>
            <div class="ag-bk-item">
                <span class="ag-bk-label">Simpanan Wajib</span>
                <span class="ag-bk-value">Rp {{ number_format($simpanan_wajib, 0, ',', '.') }}</span>
            </div>
            <div class="ag-bk-item">
                <span class="ag-bk-label">Simpanan Sukarela</span>
                <span class="ag-bk-value">Rp {{ number_format($simpanan_sukarela, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Transaction Table --}}
        <div class="ag-table-wrap">
            <table class="ag-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Periode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat_simpanan as $rs)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($rs->transaction_date)->format('d M Y') }}</td>
                        <td style="font-weight:500;">
                            @php
                                $type_name = strtolower($rs->jenisSimpanan->nama ?? '');
                                $dotClass = 'dot-sukarela';
                                if(str_contains($type_name, 'wajib')) $dotClass = 'dot-wajib';
                                elseif(str_contains($type_name, 'pokok')) $dotClass = 'dot-pokok';
                            @endphp
                            <span class="ag-dot {{ $dotClass }}"></span>
                            {{ $rs->jenisSimpanan->nama ?? 'Simpanan' }}
                        </td>
                        <td class="ag-amount">Rp {{ number_format($rs->amount, 0, ',', '.') }}</td>
                        <td style="color:var(--text-2);">{{ $rs->periode }}</td>
                        <td><span class="ag-badge ag-badge-green">Berhasil</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="ag-empty">Belum ada riwayat transaksi simpanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── TAB: PINJAMAN ─── --}}
    <div id="main-pinjaman" class="ag-tab-body">

        {{-- Sub-tabs --}}
        <div class="ag-subtabs">
            <button class="ag-subtab active" onclick="switchSubTab(this, 'berjalan')">
                Berjalan ({{ $pinjaman_berjalan->count() }})
            </button>
            <button class="ag-subtab" onclick="switchSubTab(this, 'lunas')">
                Lunas ({{ $pinjaman_lunas->count() }})
            </button>
        </div>

        {{-- Sub: Berjalan --}}
        <div id="sub-berjalan" class="ag-sub-body active">
            <div class="ag-table-wrap">
                <table class="ag-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Pinjaman</th>
                            <th>Jumlah Kredit</th>
                            <th>Tenor / Bunga</th>
                            <th>Sisa Tagihan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman_berjalan as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                            <td style="font-weight:500; color:var(--accent);">{{ $p->masterJenisPinjaman->nama_pinjaman ?? 'Pinjaman' }}</td>
                            <td class="ag-amount">Rp {{ number_format($p->jumlah_pinjaman ?? $p->jumlah_pengajuan, 0, ',', '.') }}</td>
                            <td style="color:var(--text-2);">{{ $p->tenor }} Bln &middot; {{ $p->bunga }}%</td>
                            <td class="ag-amount ag-amount-red">
                                Rp {{ number_format($p->status == 'berjalan' ? $p->sisa_pinjaman : ($p->jumlah_pengajuan + ($p->jumlah_pengajuan * ($p->bunga/100) * $p->tenor)), 0, ',', '.') }}
                            </td>
                            <td>
                                @if($p->status == 'berjalan')
                                    <span class="ag-badge ag-badge-blue">Berjalan</span>
                                @else
                                    <span class="ag-badge ag-badge-amber">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="ag-empty">Tidak ada pinjaman yang sedang berjalan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sub: Lunas --}}
        <div id="sub-lunas" class="ag-sub-body">
            <div class="ag-table-wrap">
                <table class="ag-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Pinjaman</th>
                            <th>Total Kredit</th>
                            <th>Sisa Tagihan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman_lunas as $pl)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pl->created_at)->format('d M Y') }}</td>
                            <td style="font-weight:500;">{{ $pl->masterJenisPinjaman->nama_pinjaman ?? 'Pinjaman' }}</td>
                            <td class="ag-amount">Rp {{ number_format($pl->jumlah_pinjaman ?? $pl->jumlah_pengajuan, 0, ',', '.') }}</td>
                            <td style="color:var(--text-2);">Rp {{ number_format($pl->sisa_pinjaman, 0, ',', '.') }}</td>
                            <td>
                                @if($pl->status == 'lunas')
                                    <span class="ag-badge ag-badge-green">Lunas</span>
                                @else
                                    <span class="ag-badge ag-badge-red">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="ag-empty">Tidak ada riwayat pinjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
