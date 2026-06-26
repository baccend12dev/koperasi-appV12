{{-- resources/views/pinjaman/simulasi.blade.php --}}
@extends('layouts.app')

@section('title', 'Simulasi Pinjaman')

@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link">Pengajuan</a>
    <a href="{{ route('persetujuan.pinjaman') }}" class="tb-link">Persetujuan</a>
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Angsuran</a>
    <a href="{{ route('pinjaman.simulasi') }}" class="tb-link active">Simulasi</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link">Master Jenis</a>
@endsection

@section('page-title', 'Simulasi Pinjaman')

@section('content')
<style>
/* ══════════════════════════════
   LAYOUT UTAMA
══════════════════════════════ */
.sim-layout {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 20px 24px 48px;
}

/* ══════════════════════════════
   SIDEBAR (TETAP)
══════════════════════════════ */
.sim-sidebar {
    width: 280px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.sim-search-box {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 10px 10px 0 0;
    padding: 16px;
    border-bottom: none;
}
.sim-search-label {
    font-size: 10.5px;
    font-weight: 800;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: block;
}
.sim-search-input-wrap { display: flex; flex-direction: column; gap: 6px; }
.sim-search-input {
    width: 100%; height: 36px;
    border: 1px solid #D1D5DB; border-radius: 6px;
    padding: 0 10px; font-size: 13px; font-family: inherit;
    outline: none; box-sizing: border-box;
}
.sim-search-input:focus { border-color: #1E3A5F; }
.sim-search-btn {
    width: 100%; height: 34px;
    background: #1E3A5F; color: #fff;
    border: none; border-radius: 6px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    font-family: inherit;
}
.sim-search-btn:hover { background: #162d4a; }
.sim-search-btn:disabled { background: #9CA3AF; cursor: not-allowed; }
.sim-spinner {
    display: none; width: 13px; height: 13px;
    border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff;
    border-radius: 50%; animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.sim-error-box {
    display: none; background: #FEF2F2;
    border: 1px solid #FECACA;
    border-left: 1px solid #E5E7EB; border-right: 1px solid #E5E7EB;
    padding: 9px 12px; font-size: 12px; color: #DC2626;
}
.sim-member-panel {
    display: none; background: #fff;
    border: 1px solid #E5E7EB; border-top: none;
    border-radius: 0 0 10px 10px; flex-direction: column;
}
.sim-member-panel.show { display: flex; }
.smp-header {
    display: flex; flex-direction: column;
    align-items: center; text-align: center; gap: 8px;
    padding: 20px 16px 16px; border-bottom: 1px solid #F3F4F6;
}
.smp-avatar {
    width: 60px; height: 60px; border-radius: 50%;
    background: #1E3A5F; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 700; flex-shrink: 0;
}
.smp-name { font-size: 15px; font-weight: 700; color: #111827; margin: 0; line-height: 1.3; }
.smp-badge {
    font-size: 10px; font-weight: 700;
    padding: 3px 9px; border-radius: 99px; text-transform: uppercase;
    background: #DEF7EC; color: #03543F; letter-spacing: 0.04em;
}
.smp-info-list {
    padding: 12px 16px; display: flex; flex-direction: column;
    gap: 10px; border-bottom: 1px solid #F3F4F6;
}
.smp-info-item { display: flex; flex-direction: column; gap: 1px; }
.smp-info-label { font-size: 10.5px; font-weight: 800; color: #4B5563; text-transform: uppercase; letter-spacing: 0.3px; }
.smp-info-value { font-size: 12.5px; color: #111827; font-weight: 500; }
.smp-finance { padding: 12px 16px; border-bottom: 1px solid #F3F4F6; }
.smp-finance-title { font-size: 10.5px; font-weight: 800; color: #374151; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
.smp-finance-card {
    background: #F9FAFB; border-radius: 8px;
    padding: 10px 12px; display: flex; flex-direction: column; gap: 7px;
}
.smp-finance-row { display: flex; justify-content: space-between; align-items: center; }
.smp-finance-label { font-size: 11.5px; color: #374151; font-weight: 600; display: flex; align-items: center; gap: 5px; }
.smp-finance-val { font-size: 11.5px; font-weight: 700; color: #111827; }
.smp-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.smp-divider { height: 1px; background: #E5E7EB; margin: 2px 0; }
.smp-finance-row.total .smp-finance-label,
.smp-finance-row.total .smp-finance-val { font-weight: 800; color: #1E3A5F; font-size: 12.5px; }
.smp-limit { padding: 12px 16px; }
.smp-limit-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.smp-limit-label { font-size: 11.5px; color: #374151; font-weight: 700; }
.smp-limit-val { font-size: 12px; font-weight: 700; color: #111827; }
.smp-limit-val.green { color: #059669; }
.smp-limit-val.orange { color: #D97706; }
.smp-bar-track { height: 5px; background: #E5E7EB; border-radius: 99px; overflow: hidden; margin-top: 4px; }
.smp-bar-fill { height: 100%; background: #1E3A5F; border-radius: 99px; transition: width 0.4s ease; }
.smp-bar-fill.warn { background: #EF4444; }

/* ══════════════════════════════
   MAIN CONTENT (Stitch-inspired)
══════════════════════════════ */
.sim-main {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Empty state */
.sim-empty-state {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 48px 24px;
    text-align: center;
    color: #4B5563;
    font-weight: 500;
}
.sim-empty-state svg { margin-bottom: 12px; opacity: 0.6; }
.sim-empty-state p { font-size: 13px; margin: 0; }

/* Content wrapper */
#sim-content { display: none; flex-direction: column; gap: 20px; }

/* Page title area */
.sim-page-header { margin-bottom: 4px; }
.sim-page-header h2 { font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px; }
.sim-page-header p { font-size: 14.5px; color: #374151; font-weight: 500; margin: 0; }

/* ── Layout Stacked: Pinjaman dan Form di bawahnya ── */
.sim-bento-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Card container */
.sim-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}
.sim-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #F3F4F6;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sim-card-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    margin: 0;
}
.sim-card-icon {
    width: 28px; height: 28px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sim-card-icon.blue   { background: #EFF6FF; color: #2563EB; }
.sim-card-icon.indigo { background: #EEF2FF; color: #4F46E5; }
.sim-card-icon.purple { background: #F5F3FF; color: #7C3AED; }
.sim-card-icon.green  { background: #ECFDF5; color: #059669; }
.sim-card-icon.navy   { background: #EFF6FF; color: #1E3A5F; }
.sim-card-icon.orange { background: #FFF7ED; color: #EA580C; }

/* ── Savings stacked cards ── */
.sim-left-col { display: flex; flex-direction: column; gap: 10px; }

.sim-saving-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.sim-saving-icon {
    width: 40px; height: 40px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}
.sim-saving-label { font-size: 11.5px; font-weight: 700; color: #374151; margin-bottom: 2px; }
.sim-saving-val { font-size: 17px; font-weight: 700; color: #111827; }

.sim-total-card {
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.sim-total-card-left .sim-saving-label { color: #1E40AF; }
.sim-total-card-left .sim-saving-val { font-size: 19px; color: #1E3A5F; }

.sim-maks-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.sim-maks-row {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 8px;
    font-size: 12.5px;
}
.sim-maks-row:last-of-type { margin-bottom: 0; }
.sim-maks-key { color: #374151; font-weight: 600; }
.sim-maks-val { font-weight: 700; color: #111827; }
.sim-maks-val.navy   { color: #1E3A5F; }
.sim-maks-val.orange { color: #D97706; }
.sim-maks-val.green  { color: #059669; }
.sim-maks-bar { height: 5px; background: #E5E7EB; border-radius: 99px; overflow: hidden; margin-top: 10px; }
.sim-maks-bar-fill { height: 100%; background: #1E3A5F; border-radius: 99px; transition: width 0.4s; }
.sim-maks-bar-fill.warn { background: #EF4444; }

/* ── Right Column ── */
.sim-right-col { display: flex; flex-direction: column; gap: 12px; }

/* ── Pinjaman Aktif Table ── */
.sim-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
}
.sim-table thead th {
    text-align: left; padding: 11px 20px;
    font-size: 11.5px; font-weight: 800; color: #1F2937;
    text-transform: uppercase; letter-spacing: 0.4px;
    border-bottom: 1px solid #E5E7EB;
    background: #FAFAFA;
}
.sim-table th.right, .sim-table td.right { text-align: right; }
.sim-table tbody td {
    padding: 12px 10px;
    border-bottom: 1px solid #F3F4F6;
    color: #111827; font-weight: 500; vertical-align: middle;
}
.sim-table tbody tr:hover { background: #FAFAFA; }
.sim-table tbody tr:last-child td { border-bottom: none; }
.sim-table .empty-row td {
    text-align: center; padding: 28px;
    color: #4B5563; font-weight: 500; font-size: 13px;
}
.sim-table-footer {
    padding: 12px 20px;
    background: #F9FAFB;
    border-top: 1px solid #E5E7EB;
    display: flex; justify-content: flex-end; align-items: center; gap: 12px;
}
.sim-table-footer span { font-size: 13px; color: #374151; font-weight: 500; }
.sim-table-footer strong { font-size: 18px; font-weight: 700; color: #DC2626; }

.pill-status {
    display: inline-block; padding: 2px 8px;
    border-radius: 99px; font-size: 10px; font-weight: 700;
    text-transform: uppercase; background: #D1FAE5; color: #065F46;
}

/* ── Simulasi Form ── */
.sim-form-body { padding: 20px; }
.sim-form-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 16px; margin-bottom: 20px;
}
.sim-field label {
    display: block; font-size: 11.5px; font-weight: 800;
    color: #1F2937; text-transform: uppercase;
    letter-spacing: 0.4px; margin-bottom: 5px;
}
.sim-field-input-wrap { position: relative; }
.sim-field-prefix {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-size: 13.5px; font-weight: 700; color: #374151; pointer-events: none;
}
.sim-field input, .sim-field select {
    width: 100%; height: 40px;
    border: 1px solid #D1D5DB; border-radius: 8px;
    padding: 0 12px; font-size: 13.5px; font-family: inherit;
    outline: none; box-sizing: border-box; background: #fff;
    transition: border-color 0.15s;
}
.sim-field input.with-prefix { padding-left: 36px; }
.sim-field input:focus, .sim-field select:focus { border-color: #1E3A5F; box-shadow: 0 0 0 2px rgba(30,58,95,0.08); }
.sim-form-footer {
    display: flex; justify-content: flex-end;
    border-top: 1px solid #F3F4F6; padding-top: 16px;
}
.sim-hitung-btn {
    height: 40px; padding: 0 24px;
    background: #1E3A5F; color: #fff;
    border: none; border-radius: 8px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 6px;
    font-family: inherit; transition: background 0.15s;
}
.sim-hitung-btn:hover { background: #162d4a; }
.sim-hitung-btn svg { opacity: 0.85; }

/* ══════════════════════════════
   RESULTS PANEL (Stitch)
══════════════════════════════ */
#sim-hasil-panel { display: none; }

.sim-results-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
    align-items: stretch;
}

/* Validasi banner */
.sim-validasi {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 16px; font-size: 13px; font-weight: 600;
    border-radius: 8px; margin-bottom: 16px;
}
.sim-validasi.ok   { background: #F0FDF4; border: 1px solid #BBF7D0; color: #065F46; }
.sim-validasi.warn { background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; }
.sim-validasi-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

/* Hasil detail card */
.sim-hasil-detail { padding: 20px; }
.sim-hasil-cards {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 16px;
}
.sim-hasil-mini-card {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 12px 14px;
}
.sim-hasil-mini-label { font-size: 10.5px; font-weight: 800; color: #4B5563; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
.sim-hasil-mini-val { font-size: 14px; font-weight: 700; color: #111827; }

.sim-cicilan-row {
    display: flex; align-items: center; justify-content: space-between;
    background: #EFF6FF; border: 1px solid #BFDBFE;
    border-radius: 8px; padding: 14px 16px;
}
.sim-cicilan-row-left p { font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px; }
.sim-cicilan-row-left span { font-size: 11px; color: #374151; font-weight: 500; }
.sim-cicilan-row-val { font-size: 22px; font-weight: 800; color: #1E3A5F; }

/* Ringkasan Potongan gradient card */
.sim-ringkasan-card {
    border-radius: 12px;
    background: linear-gradient(135deg, #1E3A5F 0%, #3B5F96 100%);
    padding: 20px;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.sim-ringkasan-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -20px;
    width: 120px; height: 120px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}
.sim-ringkasan-title {
    font-size: 14px; font-weight: 700;
    color: rgba(255,255,255,0.9);
    margin: 0 0 16px;
    display: flex; align-items: center; gap: 6px;
}
.sim-ringkasan-items { display: flex; flex-direction: column; gap: 0; margin-bottom: 20px; }
.sim-ringkasan-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 0;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    font-size: 13px;
}
.sim-ringkasan-item:last-child { border-bottom: none; }
.sim-ringkasan-item span:first-child { color: rgba(255,255,255,0.75); }
.sim-ringkasan-item span:last-child { font-weight: 700; }
.sim-ringkasan-total-label { font-size: 11px; color: rgba(255,255,255,0.6); margin-bottom: 4px; }
.sim-ringkasan-total-val { font-size: 26px; font-weight: 800; color: #fff; line-height: 1.1; }
</style>

<div class="sim-layout">

    {{-- ══ SIDEBAR (TETAP) ══ --}}
    <div class="sim-sidebar">
        <div class="sim-search-box">
            <span class="sim-search-label">Cari Anggota</span>
            <div class="sim-search-input-wrap">
                <input type="text" id="sim-nik" class="sim-search-input"
                    placeholder="NIK / No. KTP…" autocomplete="off" />
                <button class="sim-search-btn" id="sim-btn" onclick="cariAnggota()">
                    <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                        <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10 10l2.5 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span id="sim-btn-txt">Cari Anggota</span>
                    <span class="sim-spinner" id="sim-spinner"></span>
                </button>
            </div>
        </div>
        <div class="sim-error-box" id="sim-error"></div>
        <div class="sim-member-panel" id="sim-member-panel">
            <div class="smp-header">
                <div class="smp-avatar" id="smp-avatar">?</div>
                <h2 class="smp-name" id="smp-nama">-</h2>
                <span class="smp-badge">Anggota Aktif</span>
            </div>
            <div class="smp-info-list">
                <div class="smp-info-item">
                    <span class="smp-info-label">NIK</span>
                    <span class="smp-info-value" id="smp-nik">-</span>
                </div>
                <div class="smp-info-item">
                    <span class="smp-info-label">Departemen</span>
                    <span class="smp-info-value" id="smp-dept">-</span>
                </div>
                <div class="smp-info-item">
                    <span class="smp-info-label">Jabatan</span>
                    <span class="smp-info-value" id="smp-jabatan">-</span>
                </div>
                <div class="smp-info-item">
                    <span class="smp-info-label">Tgl Masuk</span>
                    <span class="smp-info-value" id="smp-tgl-masuk">-</span>
                </div>
            </div>

            <div class="smp-limit">
                <div class="smp-limit-row">
                    <span class="smp-limit-label">Maks. Pinjaman (5×)</span>
                    <span class="smp-limit-val" id="smp-maks">Rp 0</span>
                </div>
                <div class="smp-limit-row">
                    <span class="smp-limit-label">Pinjaman Aktif</span>
                    <span class="smp-limit-val orange" id="smp-aktif">Rp 0</span>
                </div>
                <div class="smp-limit-row" style="margin-bottom:6px;">
                    <span class="smp-limit-label">Sisa Limit</span>
                    <span class="smp-limit-val green" id="smp-sisa">Rp 0</span>
                </div>
                <div class="smp-bar-track">
                    <div class="smp-bar-fill" id="smp-bar" style="width:0%"></div>
                </div>
                <div style="font-size:10px; color:#4B5563; font-weight:600; margin-top:4px; text-align:right;" id="smp-bar-txt">0%</div>
            </div>
        </div>
    </div>{{-- end sidebar --}}

    {{-- ══ MAIN CONTENT (Stitch-style) ══ --}}
    <div class="sim-main">

        {{-- Empty state --}}
        <div class="sim-empty-state" id="sim-empty">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#4B5563" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <p style="font-weight: 500;">Masukkan NIK atau No. KTP anggota lalu klik <strong>Cari Anggota</strong></p>
        </div>

        {{-- ── Content setelah pencarian ── --}}
        <div id="sim-content">
            {{-- Grid: Pinjaman Aktif (kiri) + Form Simulasi (kanan) --}}
            <div class="sim-bento-grid">

                {{-- Saldo Simpanan --}}
                <div class="sim-card">
                    <div class="sim-card-header">
                        <div class="sim-card-icon indigo">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <h3>Saldo Simpanan</h3>
                    </div>
                    <div class="sim-form-body">
                        <div class="sim-hasil-cards" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 0;">
                            <div class="sim-hasil-mini-card">
                                <div class="sim-hasil-mini-label"><span class="smp-dot" style="background:#F57C00; display:inline-block; margin-right:4px;"></span>Simpanan Pokok</div>
                                <div class="sim-hasil-mini-val" id="smp-pokok">Rp 0</div>
                            </div>
                            <div class="sim-hasil-mini-card">
                                <div class="sim-hasil-mini-label"><span class="smp-dot" style="background:#059669; display:inline-block; margin-right:4px;"></span>Simpanan Wajib</div>
                                <div class="sim-hasil-mini-val" id="smp-wajib">Rp 0</div>
                            </div>
                            <div class="sim-hasil-mini-card">
                                <div class="sim-hasil-mini-label"><span class="smp-dot" style="background:#1a56db; display:inline-block; margin-right:4px;"></span>Simpanan Sukarela</div>
                                <div class="sim-hasil-mini-val" id="smp-sukarela">Rp 0</div>
                            </div>
                            <div class="sim-hasil-mini-card" style="background: #EFF6FF; border-color: #BFDBFE;">
                                <div class="sim-hasil-mini-label" style="color: #1E40AF;"><span class="smp-dot" style="background:#1E3A5F; display:inline-block; margin-right:4px;"></span>Total Simpanan</div>
                                <div class="sim-hasil-mini-val" style="color: #1E3A5F;" id="smp-total">Rp 0</div>
                            </div>
                            <div class="sim-hasil-mini-card" id="smp-maks-card" style="background: #F9FAFB; border-color: #E5E7EB; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div class="sim-hasil-mini-label" id="smp-maks-lbl" style="color: #4B5563; margin-bottom: 4px;"><span class="smp-dot" id="smp-maks-dot" style="background:#4B5563; display:inline-block; margin-right:4px;"></span>Maks. Pinjaman</div>
                                    <div class="sim-hasil-mini-val" id="smp-maks-val" style="color: #111827;">Rp 0</div>
                                </div>
                                <div id="smp-maks-warning" style="font-size: 10px; color: #DC2626; font-weight: 700; margin-top: 4px; display: none;">⚠️ Pinjaman Melebihi Limit!</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Pinjaman Aktif --}}
                <div class="sim-card">
                    <div class="sim-card-header">
                        <div class="sim-card-icon navy">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <h3>Daftar Pinjaman Aktif</h3>
                    </div>
                    <div>
                        <table class="sim-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Pinjaman</th>
                                    <th class="right">Pokok Pinjaman</th>
                                    <th class="right">Pokok + Bunga</th>
                                    <th class="right">Tenor</th>
                                    <th class="right">Sisa Tenor</th>
                                    <th class="right">Sisa Pinjaman</th>
                                    <th class="right">Cicilan/Bulan</th>
                                </tr>
                            </thead>
                            <tbody id="sc-tbody"></tbody>
                        </table>
                    </div>
                    <div class="sim-table-footer" id="sc-table-footer" style="display:none; flex-direction:column; align-items:flex-end; gap:8px; padding: 16px 20px;">
                        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151;">
                            <span>Total Cicilan Aktif:</span>
                            <span id="sc-total-cicilan" style="font-weight:700; color:#111827;">Rp 0</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151;">
                            <span>Total Potongan Simpanan (Wajib + Sukarela):</span>
                            <span id="sc-total-simpanan-bulanan" style="font-weight:700; color:#111827;">Rp 0</span>
                        </div>
                        <div style="height:1px; background:#E5E7EB; width:100%; max-width:320px; margin: 4px 0;"></div>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <span style="font-size:14px; font-weight:700; color:#1E3A5F;">Grand Total Potongan Bulanan:</span>
                            <strong id="sc-grand-total-potongan" style="font-size:18px; font-weight:800; color:#DC2626;">Rp 0</strong>
                            <span style="font-size:12px; color:#4B5563; font-weight:600;">/ bulan</span>
                        </div>
                        <div style="height:1px; background:#E5E7EB; width:100%; max-width:320px; margin: 4px 0;"></div>
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px; width:100%; max-width:320px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                                <span style="font-size:12.5px; color:#374151; font-weight:600;">Input Gaji Bulanan:</span>
                                <div style="position:relative; width:180px;">
                                    <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:12px; font-weight:600; color:#4B5563;">Rp</span>
                                    <input type="number" id="sc-input-gaji" placeholder="0" oninput="hitungRasioTotal()"
                                           style="width:100%; height:32px; padding: 0 10px 0 30px; border:1px solid #D1D5DB; border-radius:6px; font-size:12.5px; outline:none; text-align:right; font-family:inherit;" />
                                </div>
                            </div>
                            <div id="sc-rasio-box" style="display:none; justify-content:space-between; align-items:center; width:100%; margin-top:4px; font-size:13px; font-weight:700; color:#374151;">
                                <span>Rasio Gaji Diterima (Net):</span>
                                <span id="sc-rasio-val" style="font-size:14px;">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Simulasi Pinjaman Baru --}}
                <div class="sim-card">
                    <div class="sim-card-header">
                        <div class="sim-card-icon blue">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
                        </div>
                        <h3>Simulasi Pinjaman Baru</h3>
                    </div>
                    <div class="sim-form-body">
                        <div class="sim-form-grid">
                            
                            <div class="sim-field">
                                <label for="si-jumlah">Jumlah Pinjaman</label>
                                <div class="sim-field-input-wrap">
                                    <span class="sim-field-prefix">Rp</span>
                                    <input type="number" id="si-jumlah" class="with-prefix"
                                        placeholder="10.000.000" min="100000" step="100000"
                                        oninput="hitungSimulasi()" />
                                </div>
                            </div>
                            <div class="sim-field">
                                <label for="si-jenis">Jenis Pinjaman</label>
                                <select id="si-jenis" onchange="onJenisChange()">
                                    <option value="">— Pilih Jenis Pinjaman —</option>
                                    @foreach($jenisPinjamanList as $jpParent)
                                        @if($jpParent->children->count() > 0)
                                            <optgroup label="{{ $jpParent->nama_pinjaman }}">
                                            @foreach($jpParent->children as $jpChild)
                                                <option value="{{ $jpChild->id }}"
                                                    data-bunga="{{ $jpChild->bunga }}"
                                                    data-parent="{{ $jpParent->id }}"
                                                    data-limit="{{ $jpParent->limit_maksimal ?? 0 }}"
                                                    data-parent-nama="{{ $jpParent->nama_pinjaman }}">
                                                    {{ $jpChild->nama_pinjaman }}
                                                </option>
                                            @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $jpParent->id }}"
                                                data-bunga="{{ $jpParent->bunga }}"
                                                data-parent="{{ $jpParent->id }}"
                                                data-limit="{{ $jpParent->limit_maksimal ?? 0 }}"
                                                data-parent-nama="{{ $jpParent->nama_pinjaman }}">
                                                {{ $jpParent->nama_pinjaman }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="sim-field">
                                <label for="si-tenor">Lama Angsuran (Bulan)</label>
                                <input type="number" id="si-tenor"
                                    placeholder="12" min="1" max="60"
                                    oninput="hitungSimulasi()" />
                            </div>
                            <div class="sim-field">
                                <label for="si-bunga">Bunga (%/bulan)</label>
                                <input type="number" id="si-bunga"
                                    placeholder="1" min="0" max="10" step="0.1"
                                    oninput="hitungSimulasi()" />
                            </div>

                        </div>
                        <div class="sim-form-footer">
                            <button class="sim-hitung-btn" onclick="hitungSimulasi()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                Hitung Simulasi
                            </button>
                        </div>
                    </div>
                </div>

            </div>{{-- end bento grid --}}

            {{-- ── Results Panel (muncul setelah hitung) ── --}}
            <div id="sim-hasil-panel">
                <div class="sim-results-grid">

                    {{-- Hasil Detail --}}
                    <div class="sim-card">
                        <div class="sim-card-header">
                            <div class="sim-card-icon green">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            </div>
                            <h3>Hasil Simulasi</h3>
                        </div>
                        <div class="sim-hasil-detail">
                            <div class="sim-validasi" id="sim-validasi">
                                <span class="sim-validasi-dot"></span>
                                <span id="sim-validasi-txt"></span>
                            </div>
                            <div class="sim-hasil-cards">
                                <div class="sim-hasil-mini-card">
                                    <div class="sim-hasil-mini-label">Pinjaman Pokok</div>
                                    <div class="sim-hasil-mini-val" id="sh-pokok">—</div>
                                </div>
                                <div class="sim-hasil-mini-card">
                                    <div class="sim-hasil-mini-label">Tenor</div>
                                    <div class="sim-hasil-mini-val" id="sh-tenor">—</div>
                                </div>
                                <div class="sim-hasil-mini-card">
                                    <div class="sim-hasil-mini-label">Total Bunga</div>
                                    <div class="sim-hasil-mini-val" id="sh-bunga">—</div>
                                </div>
                                <div class="sim-hasil-mini-card">
                                    <div class="sim-hasil-mini-label">Total Pengembalian</div>
                                    <div class="sim-hasil-mini-val" id="sh-total">—</div>
                                </div>
                            </div>
                            <div class="sim-cicilan-row">
                                <div class="sim-cicilan-row-left">
                                    <p>Estimasi Cicilan Baru</p>
                                    <span>Angsuran pokok + bunga / bulan</span>
                                </div>
                                <div class="sim-cicilan-row-val" id="sh-cicilan">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Potongan (gradient) --}}
                    <div class="sim-ringkasan-card">
                        <div>
                            <p class="sim-ringkasan-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Ringkasan Potongan
                            </p>
                            <div class="sim-ringkasan-items">
                                <div class="sim-ringkasan-item">
                                    <span>Cicilan Aktif</span>
                                    <span id="rp-cicilan-aktif">Rp 0</span>
                                </div>
                                <div class="sim-ringkasan-item">
                                    <span>Potongan Simpanan</span>
                                    <span id="rp-wajib">Rp 0</span>
                                </div>
                                <div class="sim-ringkasan-item">
                                    <span>Cicilan Simulasi Baru</span>
                                    <span id="rp-cicilan-baru">Rp 0</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="sim-ringkasan-total-label">Total Potongan Bulanan Estimasi</div>
                            <div class="sim-ringkasan-total-val" id="rp-grand-total">Rp 0</div>
                        </div>
                    </div>

                </div>
            </div>{{-- end results panel --}}

        </div>{{-- end #sim-content --}}
    </div>{{-- end main --}}
</div>{{-- end layout --}}

@push('scripts')
<script>
const SEARCH_URL = "{{ route('pinjaman.pengajuan.searchAnggota') }}";
let anggotaData  = null;
let simulasiCicilanBaru = 0;

function fmt(n) {
    return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID');
}

function cariAnggota() {
    const nik  = document.getElementById('sim-nik').value.trim();
    if (!nik) return;

    const btn   = document.getElementById('sim-btn');
    const txt   = document.getElementById('sim-btn-txt');
    const spin  = document.getElementById('sim-spinner');
    const errEl = document.getElementById('sim-error');

    btn.disabled = true;
    txt.style.display  = 'none';
    spin.style.display = 'block';
    errEl.style.display = 'none';
    document.getElementById('sim-content').style.display = 'none';
    document.getElementById('sim-empty').style.display   = 'block';
    document.getElementById('sim-member-panel').classList.remove('show');

    fetch(`${SEARCH_URL}?q=${encodeURIComponent(nik)}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) {
                errEl.textContent   = res.message ?? 'Anggota tidak ditemukan.';
                errEl.style.display = 'block';
                return;
            }
            anggotaData = res.data;
            simulasiCicilanBaru = 0;
            const inputGaji = document.getElementById('sc-input-gaji');
            if (inputGaji) inputGaji.value = '';
            const rasioBox = document.getElementById('sc-rasio-box');
            if (rasioBox) rasioBox.style.display = 'none';
            renderSidebar(res.data);
            renderMain(res.data);
            document.getElementById('sim-member-panel').classList.add('show');
            document.getElementById('sim-empty').style.display   = 'none';
            document.getElementById('sim-content').style.display = 'flex';
            // reset form & hasil
            ['si-jumlah','si-tenor','si-bunga','si-jenis'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('sim-hasil-panel').style.display = 'none';
        })
        .catch(() => {
            errEl.textContent   = 'Terjadi kesalahan. Periksa koneksi dan coba lagi.';
            errEl.style.display = 'block';
        })
        .finally(() => {
            btn.disabled       = false;
            txt.style.display  = '';
            spin.style.display = 'none';
        });
}

function renderSidebar(d) {
    document.getElementById('smp-avatar').textContent    = (d.nama || '?').charAt(0).toUpperCase();
    document.getElementById('smp-nama').textContent      = d.nama;
    document.getElementById('smp-nik').textContent       = d.nik;
    document.getElementById('smp-dept').textContent      = d.departemen    ?? '-';
    document.getElementById('smp-jabatan').textContent   = d.jabatan       ?? '-';
    document.getElementById('smp-tgl-masuk').textContent = d.tgl_masuk     ?? '-';

    document.getElementById('smp-pokok').textContent    = fmt(d.simpanan_pokok    ?? 0);
    document.getElementById('smp-wajib').textContent    = fmt(d.simpanan_wajib    ?? 0);
    document.getElementById('smp-sukarela').textContent = fmt(d.simpanan_sukarela ?? 0);
    document.getElementById('smp-total').textContent    = fmt(d.total_simpanan    ?? 0);

    const maks = d.maks_pinjaman  ?? 0;
    const aktif= d.pinjaman_aktif ?? 0;
    const sisa = d.sisa_limit     ?? 0;
    const pct  = maks > 0 ? Math.min((aktif / maks) * 100, 100) : 0;

    document.getElementById('smp-maks').textContent  = fmt(maks);
    document.getElementById('smp-aktif').textContent = fmt(aktif);
    document.getElementById('smp-sisa').textContent  = fmt(sisa);
    const bar = document.getElementById('smp-bar');
    bar.style.width = pct + '%';
    bar.className   = 'smp-bar-fill' + (pct >= 90 ? ' warn' : '');
    document.getElementById('smp-bar-txt').textContent = Math.round(pct) + '% terpakai';

    // Maks Pinjaman Card & Warning
    const maksCard = document.getElementById('smp-maks-card');
    const maksLbl = document.getElementById('smp-maks-lbl');
    const maksDot = document.getElementById('smp-maks-dot');
    const maksVal = document.getElementById('smp-maks-val');
    const maksWarn = document.getElementById('smp-maks-warning');

    if (maksCard && maksWarn) {
        maksVal.textContent = fmt(maks);
        if (aktif > maks) {
            maksCard.style.background = '#FEF2F2';
            maksCard.style.borderColor = '#FCA5A5';
            maksLbl.style.color = '#B91C1C';
            maksDot.style.background = '#B91C1C';
            maksVal.style.color = '#B91C1C';
            maksWarn.style.display = 'block';
        } else {
            maksCard.style.background = '#F9FAFB';
            maksCard.style.borderColor = '#E5E7EB';
            maksLbl.style.color = '#4B5563';
            maksDot.style.background = '#4B5563';
            maksVal.style.color = '#111827';
            maksWarn.style.display = 'none';
        }
    }
}

function renderMain(d) {
    // Page header
    

    // Simpanan wajib bulanan di form
    const wajibBulan = d.simpanan_wajib_bulanan ?? 0;
    const wajibEl = document.getElementById('si-wajib-bulan');
    if (wajibEl) wajibEl.value = wajibBulan > 0
        ? 'Rp ' + Math.round(wajibBulan).toLocaleString('id-ID')
        : 'Rp 0';

    // Tabel pinjaman aktif
    const tbody = document.getElementById('sc-tbody');
    const footer = document.getElementById('sc-table-footer');
    tbody.innerHTML = '';
    const list = d.pinjaman_berjalan ?? [];

    if (list.length === 0) {
        tbody.innerHTML = `<tr class="empty-row"><td colspan="8">Tidak ada pinjaman aktif</td></tr>`;
    } else {
        list.forEach((p, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i + 1}</td>
                <td>${p.jenis_pinjaman}</td>
                <td class="right">${fmt(p.jumlah_pinjaman)}</td>
                <td class="right">${fmt(p.total_pinjaman)}</td>
                <td class="right">${p.tenor} Bln</td>
                <td class="right">${p.sisa_tenor} Bln</td>
                <td class="right">${fmt(p.sisa_tagihan)}</td>
                <td class="right" style="font-weight:700; color:#DC2626;">${fmt(p.cicilan_per_bulan)}</td>
            `;
            tbody.appendChild(tr);
        });
    }
    const totalCicilan = d.total_cicilan_per_bulan ?? list.reduce((s, p) => s + (p.cicilan_per_bulan || 0), 0);
    const totalSimpananBulanan = d.simpanan_wajib_bulanan ?? 0;
    const grandTotal = totalCicilan + totalSimpananBulanan;

    document.getElementById('sc-total-cicilan').textContent = fmt(totalCicilan);
    document.getElementById('sc-total-simpanan-bulanan').textContent = fmt(totalSimpananBulanan);
    document.getElementById('sc-grand-total-potongan').textContent = fmt(grandTotal);
    footer.style.display = 'flex';
    hitungRasioTotal();
}

function onJenisChange() {
    const select = document.getElementById('si-jenis');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value) {
        const bunga = parseFloat(selectedOption.dataset.bunga) || 0;
        document.getElementById('si-bunga').value = bunga;
    } else {
        document.getElementById('si-bunga').value = '';
    }
    hitungSimulasi();
}

function hitungSimulasi() {
    const jumlah = parseFloat(document.getElementById('si-jumlah').value) || 0;
    const tenor  = parseInt(document.getElementById('si-tenor').value)    || 0;
    const bunga  = parseFloat(document.getElementById('si-bunga').value)  || 0;
    const panel  = document.getElementById('sim-hasil-panel');

    if (!jumlah || !tenor || jumlah < 100000) {
        panel.style.display = 'none';
        simulasiCicilanBaru = 0;
        hitungRasioTotal();
        return;
    }

    // Flat rate
    const totalBunga    = jumlah * (bunga / 100) * tenor;
    const totalPinjaman = jumlah + totalBunga;
    const cicilan       = totalPinjaman / tenor;

    simulasiCicilanBaru = cicilan;

    // Isi hasil mini cards
    document.getElementById('sh-pokok').textContent  = fmt(jumlah);
    document.getElementById('sh-tenor').textContent  = tenor + ' Bulan';
    document.getElementById('sh-bunga').textContent  = fmt(totalBunga);
    document.getElementById('sh-total').textContent  = fmt(totalPinjaman);
    document.getElementById('sh-cicilan').textContent = fmt(cicilan);

    // Validasi
    const sisaLimit = parseFloat(anggotaData ? (anggotaData.sisa_limit ?? 0) : 0) || 0;
    const maks      = parseFloat(anggotaData ? (anggotaData.maks_pinjaman ?? 0) : 0) || 0;
    const vEl  = document.getElementById('sim-validasi');
    const vTxt = document.getElementById('sim-validasi-txt');

    if (jumlah > maks) {
        vEl.className = 'sim-validasi warn';
        vTxt.textContent = `Melebihi batas maksimal! Maks pinjaman: ${fmt(maks)}`;
    } else if (jumlah > sisaLimit) {
        vEl.className = 'sim-validasi warn';
        vTxt.textContent = `Melebihi sisa limit yang tersedia: ${fmt(sisaLimit)}`;
    } else {
        vEl.className = 'sim-validasi ok';
        vTxt.textContent = `Sesuai limit. Sisa limit setelah pengajuan: ${fmt(sisaLimit - jumlah)}`;
    }

    // Ringkasan Potongan
    const totalCicilan = parseFloat(anggotaData ? (anggotaData.total_cicilan_per_bulan ?? 0) : 0) || 0;
    const wajibBulan   = parseFloat(anggotaData ? (anggotaData.simpanan_wajib_bulanan  ?? 0) : 0) || 0;
    const grandTotal   = totalCicilan + wajibBulan + cicilan;

    document.getElementById('rp-cicilan-aktif').textContent = fmt(totalCicilan);
    document.getElementById('rp-wajib').textContent         = fmt(wajibBulan);
    document.getElementById('rp-cicilan-baru').textContent  = fmt(cicilan);
    document.getElementById('rp-grand-total').textContent   = fmt(grandTotal);

    panel.style.display = 'block';
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    hitungRasioTotal();
}

function hitungRasioTotal() {
    const gaji = parseFloat(document.getElementById('sc-input-gaji').value) || 0;
    const rasioBox = document.getElementById('sc-rasio-box');
    const rasioVal = document.getElementById('sc-rasio-val');

    if (gaji <= 0) {
        if (rasioBox) rasioBox.style.display = 'none';
        return;
    }

    const totalCicilan = anggotaData ? (anggotaData.total_cicilan_per_bulan ?? 0) : 0;
    const totalSimpananBulanan = anggotaData ? (anggotaData.simpanan_wajib_bulanan ?? 0) : 0;
    const grandTotal = totalCicilan + totalSimpananBulanan + simulasiCicilanBaru;

    const netSalary = gaji - grandTotal;
    const rasio = (netSalary / gaji) * 100;
    const displayRasio = Math.max(0, rasio);
    rasioVal.textContent = displayRasio.toFixed(1) + '%';

    if (rasio >= 70) {
        rasioVal.style.color = '#059669';
        rasioVal.textContent += ' (Aman)';
    } else if (rasio >= 50) {
        rasioVal.style.color = '#D97706';
        rasioVal.textContent += ' (Perlu Perhatian)';
    } else {
        rasioVal.style.color = '#DC2626';
        rasioVal.textContent += ' (Tidak Memenuhi Syarat)';
    }

    if (rasioBox) rasioBox.style.display = 'flex';
}

// Enter key
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('sim-nik').addEventListener('keydown', e => {
        if (e.key === 'Enter') cariAnggota();
    });
});
</script>
@endpush
@endsection
