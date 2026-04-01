{{-- resources/views/Simpanan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Simpanan: ' . ($master->anggota->nama_anggota ?? ''))

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link active">Simpanan Anggota</a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link">Transaksi</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('simpanan.tagihangenerator') }}" class="tb-link">Tagih Simpanan</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('simpanan.index') }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;margin-right:4px;">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali
    </a>
@endsection

@section('page-title', 'Detail Simpanan')

@section('content')
<style>
    /* ── Header Strip ── */
    .sp-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 28px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .sp-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .sp-avatar {
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
    .sp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .sp-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-1);
        margin: 0;
        line-height: 1.3;
    }
    .sp-meta {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 2px;
        font-size: 12px;
        color: var(--text-2);
    }
    .sp-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .sp-badge-aktif {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background: var(--green-bg);
        color: var(--green-text);
    }

    /* ── Summary Bar ── */
    .sp-summary {
        display: flex;
        align-items: stretch;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
    }
    .sp-sum-item {
        flex: 1;
        padding: 16px 28px;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .sp-sum-item:last-child { border-right: none; }
    .sp-sum-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-3);
    }
    .sp-sum-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-1);
        line-height: 1.3;
        font-variant-numeric: tabular-nums;
    }
    .sp-sum-value.accent { color: var(--accent); }
    .sp-sum-sub {
        font-size: 11px;
        color: var(--text-3);
        margin-top: 1px;
    }

    /* ── Tabs ── */
    .sp-tabs {
        display: flex;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        padding: 0 28px;
        gap: 0;
    }
    .sp-tab {
        padding: 11px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-2);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
        background: none;
        border-top: none; border-left: none; border-right: none;
    }
    .sp-tab:hover { color: var(--accent); }
    .sp-tab.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    /* ── Tab Body ── */
    .sp-tab-body { display: none; }
    .sp-tab-body.active { display: block; }

    /* ── Table ── */
    .sp-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sp-table thead th {
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
    .sp-table tbody td {
        padding: 12px 28px;
        font-size: 13px;
        color: var(--text-1);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .sp-table tbody tr:last-child td { border-bottom: none; }
    .sp-table tbody tr:hover { background: var(--bg); }
    .sp-empty {
        text-align: center;
        padding: 40px 28px !important;
        color: var(--text-3);
        font-size: 13px;
    }

    /* ── Dot indicator ── */
    .sp-dot {
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

    /* ── Badges ── */
    .sp-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 700;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .sp-badge-green  { background: var(--green-bg);  color: var(--green-text); }
    .sp-badge-blue   { background: #E6F1FB; color: #185FA5; }
    .sp-badge-orange { background: var(--amber-bg);  color: var(--amber-text); }

    /* ── Edit Form (inline panel) ── */
    .sp-form-panel {
        padding: 24px 28px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .sp-form-grid {
        display: grid;
        grid-template-columns: 200px 200px 200px auto;
        align-items: end;
        gap: 16px;
    }
    .sp-form-group { display: flex; flex-direction: column; gap: 4px; }
    .sp-form-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-3);
    }
    .sp-form-input {
        height: 34px;
        padding: 0 10px;
        font-size: 13px;
        color: var(--text-1);
        background: var(--surface);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-md);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        font-family: var(--font);
        width: 100%;
    }
    .sp-form-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(92,79,138,0.12);
    }
    .sp-form-input[readonly] {
        background: var(--bg);
        color: var(--text-2);
        cursor: default;
    }
    .sp-amount-value { font-weight: 600; font-variant-numeric: tabular-nums; }
</style>

<div>

    {{-- 1 ▸ Header Strip --}}
    <div class="sp-header">
        <div class="sp-header-left">
            <div class="sp-avatar">
                @if($master->anggota->foto ?? false)
                    <img src="{{ Storage::url($master->anggota->foto) }}" alt="{{ $master->anggota->nama_anggota }}">
                @else
                    {{ strtoupper(substr($master->anggota->nama_anggota ?? 'AN', 0, 2)) }}
                @endif
            </div>
            <div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <h1 class="sp-name">{{ $master->anggota->nama_anggota ?? '—' }}</h1>
                    <span class="sp-badge-aktif">{{ $master->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="sp-meta">
                    <div class="sp-meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $master->anggota->nik ?? 'N/A' }}
                    </div>
                    <div class="sp-meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        {{ $master->anggota->departemen->nama ?? '—' }}
                    </div>
                    <div class="sp-meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Mulai {{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('anggota.show', $master->anggota_id) }}" class="btn-secondary">
                Lihat Profil Anggota
            </a>
        </div>
    </div>

    {{-- 2 ▸ Summary Bar --}}
    <div class="sp-summary">
        <div class="sp-sum-item">
            <span class="sp-sum-label">Total Terkumpul</span>
            <span class="sp-sum-value accent">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</span>
            <span class="sp-sum-sub">Dari seluruh transaksi</span>
        </div>
        <div class="sp-sum-item">
            <span class="sp-sum-label">Simpanan Pokok</span>
            <span class="sp-sum-value">Rp {{ number_format($master->simpanan_pokok, 0, ',', '.') }}</span>
            <span class="sp-sum-sub">Sekali bayar di awal</span>
        </div>
        <div class="sp-sum-item">
            <span class="sp-sum-label">Simpanan Wajib</span>
            <span class="sp-sum-value">Rp {{ number_format($master->simpanan_wajib, 0, ',', '.') }}</span>
            <span class="sp-sum-sub">Per bulan (rutin)</span>
        </div>
        <div class="sp-sum-item">
            <span class="sp-sum-label">Simpanan Sukarela</span>
            <span class="sp-sum-value">Rp {{ number_format($master->simpanan_sukarela, 0, ',', '.') }}</span>
            <span class="sp-sum-sub">Kontribusi fleksibel</span>
        </div>
    </div>

    {{-- 3 ▸ Tabs --}}
    <div class="sp-tabs">
        <button class="sp-tab active" onclick="spTab(this,'konfigurasi')">Konfigurasi</button>
        <button class="sp-tab" onclick="spTab(this,'riwayat')">
            Riwayat Transaksi
            <span style="font-size:10px;font-weight:700;background:var(--bg);color:var(--text-3);padding:1px 6px;border-radius:8px;margin-left:4px;">{{ $riwayatTransaksi->count() }}</span>
        </button>
    </div>

    {{-- ─── TAB: KONFIGURASI ─── --}}
    <div id="sp-konfigurasi" class="sp-tab-body active">

        {{-- Inline edit form --}}
        <div class="sp-form-panel">
            <form method="POST" action="{{ route('simpanan.update', $master->id) }}">
                @csrf
                @method('PUT')
                <div class="sp-form-grid">
                    <div class="sp-form-group">
                        <label class="sp-form-label">Jenis Simpanan</label>
                        <select name="jenis_simpanan" id="jenis_simpanan_select" class="sp-form-input" onchange="syncNominal(this)">
                            <option value="Pokok" {{ old('jenis_simpanan') == 'Pokok' ? 'selected' : '' }}>Pokok</option>
                            <option value="Wajib" selected {{ old('jenis_simpanan') == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                            <option value="Sukarela" {{ old('jenis_simpanan') == 'Sukarela' ? 'selected' : '' }}>Sukarela</option>
                        </select>
                    </div>
                    <div class="sp-form-group">
                        <label class="sp-form-label">Nominal Baru (Rp)</label>
                        <input type="number" name="nominal_baru" id="nominal_baru_input"
                            value="{{ old('nominal_baru', $master->simpanan_wajib) }}"
                            required class="sp-form-input">
                    </div>
                    <div class="sp-form-group">
                        <label class="sp-form-label">Tanggal Mulai Berlaku</label>
                        <input type="date" name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $master->tanggal_mulai) }}"
                            required class="sp-form-input">
                    </div>
                    <div>
                        <button type="submit" class="btn-primary" style="height:34px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Konfigurasi Table --}}
        <table class="sp-table">
            <thead>
                <tr>
                    <th>Jenis Simpanan</th>
                    <th>Nominal Per Bulan</th>
                    <th>Tanggal Mulai</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr onclick="setEdit('Pokok', {{ $master->simpanan_pokok }})" style="cursor:pointer;">
                    <td style="font-weight:600;">
                        <span class="sp-dot dot-pokok"></span>Pokok
                    </td>
                    <td class="sp-amount-value">Rp {{ number_format($master->simpanan_pokok, 0, ',', '.') }}</td>
                    <td style="color:var(--text-2);">{{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d M Y') }}</td>
                    <td><span class="sp-badge sp-badge-green">Aktif</span></td>
                    <td style="text-align:right;">
                        <button onclick="setEdit('Pokok', {{ $master->simpanan_pokok }}); event.stopPropagation();" class="btn-secondary" style="padding:4px 10px; font-size:12px;">Edit</button>
                    </td>
                </tr>
                <tr onclick="setEdit('Wajib', {{ $master->simpanan_wajib }})" style="cursor:pointer;">
                    <td style="font-weight:600;">
                        <span class="sp-dot dot-wajib"></span>Wajib
                    </td>
                    <td class="sp-amount-value">Rp {{ number_format($master->simpanan_wajib, 0, ',', '.') }}</td>
                    <td style="color:var(--text-2);">{{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d M Y') }}</td>
                    <td><span class="sp-badge sp-badge-green">Aktif</span></td>
                    <td style="text-align:right;">
                        <button onclick="setEdit('Wajib', {{ $master->simpanan_wajib }}); event.stopPropagation();" class="btn-secondary" style="padding:4px 10px; font-size:12px;">Edit</button>
                    </td>
                </tr>
                <tr onclick="setEdit('Sukarela', {{ $master->simpanan_sukarela }})" style="cursor:pointer;">
                    <td style="font-weight:600;">
                        <span class="sp-dot dot-sukarela"></span>Sukarela
                    </td>
                    <td class="sp-amount-value">Rp {{ number_format($master->simpanan_sukarela, 0, ',', '.') }}</td>
                    <td style="color:var(--text-2);">{{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d M Y') }}</td>
                    <td><span class="sp-badge sp-badge-green">Aktif</span></td>
                    <td style="text-align:right;">
                        <button onclick="setEdit('Sukarela', {{ $master->simpanan_sukarela }}); event.stopPropagation();" class="btn-secondary" style="padding:4px 10px; font-size:12px;">Edit</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ─── TAB: RIWAYAT TRANSAKSI ─── --}}
    <div id="sp-riwayat" class="sp-tab-body">
        <table class="sp-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Periode</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th style="text-align:right;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatTransaksi as $trx)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                    <td style="color:var(--text-2);">{{ $trx->periode }}</td>
                    <td>
                        @php
                            $nama = strtolower($trx->jenisSimpanan->nama ?? '');
                            $dotClass = 'dot-sukarela';
                            $badgeClass = 'sp-badge-blue';
                            if(str_contains($nama, 'wajib'))  { $dotClass = 'dot-wajib';    $badgeClass = 'sp-badge-green';  }
                            if(str_contains($nama, 'pokok'))  { $dotClass = 'dot-pokok';    $badgeClass = 'sp-badge-orange'; }
                        @endphp
                        <span class="sp-badge {{ $badgeClass }}">{{ strtoupper($trx->jenisSimpanan->nama ?? '—') }}</span>
                    </td>
                    <td style="color:var(--text-2);">{{ $trx->description ?? '—' }}</td>
                    <td style="text-align:right;" class="sp-amount-value">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="sp-empty">Belum ada riwayat transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    const nominalMap = {
        Pokok:    {{ $master->simpanan_pokok }},
        Wajib:    {{ $master->simpanan_wajib }},
        Sukarela: {{ $master->simpanan_sukarela }},
    };

    function spTab(el, id) {
        document.querySelectorAll('.sp-tab-body').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.sp-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('sp-' + id).classList.add('active');
        el.classList.add('active');
    }

    function setEdit(jenis, nominal) {
        document.getElementById('jenis_simpanan_select').value = jenis;
        document.getElementById('nominal_baru_input').value = nominal;
        document.getElementById('nominal_baru_input').focus();
        document.getElementById('nominal_baru_input').select();
    }

    function syncNominal(select) {
        const jenis = select.value;
        document.getElementById('nominal_baru_input').value = nominalMap[jenis] ?? 0;
    }
</script>
@endsection
