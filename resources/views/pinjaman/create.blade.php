{{-- resources/views/pinjaman/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Pinjaman')

@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link active">Pengajuan Pinjaman</a>
    <a href="{{ route('persetujuan.pinjaman') }}" class="tb-link">Persetujuan Pinjaman</a>
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Pembayaran Angsuran</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link">Master Jenis Pinjaman</a>
@endsection

@section('page-title', 'Tambah Pinjaman')
@section('page-subtitle', 'Buat pengajuan pinjaman baru dengan validasi otomatis')

@section('subbar-actions')
    <a href="{{ route('pinjaman.pengajuan') }}" class="btn-secondary" style="margin-right:10px;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="display:inline;margin-right:4px;"><path d="M9 11L5 7l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Kembali
    </a>
@endsection

@section('content')
<style>
/* ─────────────────────────────────────────────
   GLOBAL & RESET
───────────────────────────────────────────── */
:root {
    --navy: #0B1C3F;
    --navy-light: #132E63;
    --blue-btn: #0B214F;
    --bg-gray: #F4F6F8;
    --text-main: #111827;
    --text-muted: #6B7280;
    --green-light: #E4F3E8;
    --green-dark: #065F46;
    --green-accent: #10B981;
    --brown-bg: #5D2A00;
    --orange-text: #F97316;
}

body { font-family: 'Inter', system-ui, sans-serif; background-color: #F9FAFB; }

.pc-wrap {
    margin: 0 auto; padding: 20px 24px 48px;
    max-width: 1100px;
}

/* ─────────────────────────────────────────────
   SEARCH SECTION
───────────────────────────────────────────── */
.search-label {
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
}
.search-box {
    display: flex; gap: 12px; margin-bottom: 20px;
}
.search-input-wrap {
    flex: 1; display: flex; align-items: center;
    background: #EAECEF; border-radius: 8px; padding: 0 16px;
    height: 48px;
}
.search-input-wrap svg { color: #8C94A1; margin-right: 12px; }
.search-input {
    border: none; background: transparent; outline: none;
    width: 100%; font-size: 15px; font-weight: 500; color: var(--text-main);
}
.search-btn {
    background: var(--blue-btn); color: #fff; border: none;
    border-radius: 8px; padding: 0 24px; font-weight: 600;
    font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: 0.2s;
}
.search-btn:hover { background: var(--navy); }

/* ─────────────────────────────────────────────
   MEMBER BANNER
───────────────────────────────────────────── */
#mb-expand-container { display: none; margin-bottom: 24px; }
.mbanner {
    background: var(--navy); border-radius: 12px; padding: 20px 24px;
    margin-bottom: 24px; display: flex; align-items: center; gap: 30px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
.mb-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center; color: #fff;
}
.mb-info { flex: 1; display: flex; align-items: center; gap: 16px; }
.mb-text h3 { margin: 0; color: #fff; font-size: 16px; font-weight: 800; text-transform: uppercase; }
.mb-text p { margin: 4px 0 0; color: #9CA3AF; font-size: 12px; }
.mb-stats { display: flex; gap: 40px; }
.mb-stat-box p { margin: 0 0 4px; font-size: 10px; color: #9CA3AF; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
.mb-stat-box h4 { margin: 0; font-size: 18px; color: #fff; font-weight: 700; }
.mb-stat-box h4.text-green { color: #4ADE80; }

.btn-expand-loans {
    background: rgba(255,255,255,0.1); border: none; border-radius: 4px;
    padding: 4px; color: #fff; cursor: pointer; transition: 0.2s;
    display: flex; align-items: center; justify-content: center;
}
.btn-expand-loans:hover { background: rgba(255,255,255,0.2); }
.btn-expand-loans.active { transform: rotate(180deg); background: rgba(74, 222, 128, 0.2); }

.detail-wrap {
    display: none; background: #fff; border: 1px solid #E5E7EB;
    border-top: none; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;
    margin-top: -12px; margin-bottom: 24px; padding: 20px 24px;
    animation: slideDown 0.3s ease-out;
}
.detail-wrap.show { display: block; }

.dt-header { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 15px; }
.dt-table { width: 100%; border-collapse: collapse; }
.dt-table th { text-align: left; font-size: 10px; color: var(--text-muted); text-transform: uppercase; padding-bottom: 8px; border-bottom: 1px solid #F3F4F6; }
.dt-table td { padding: 12px 0; border-bottom: 1px solid #F3F4F6; font-size: 12px; }
.dt-table tr:last-child td { border-bottom: none; }

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ─────────────────────────────────────────────
   MAIN GRID & CARDS
───────────────────────────────────────────── */
.main-grid {
    display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;
}
.card {
    background: #fff; border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 24px;
}
.card-title {
    font-size: 16px; font-weight: 700; color: var(--navy);
    display: flex; align-items: center; gap: 10px; margin-bottom: 24px;
}

/* ─────────────────────────────────────────────
   FORM ELEMENTS
───────────────────────────────────────────── */
.f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
.f-group { display: flex; flex-direction: column; gap: 8px; }
.f-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
.f-input-wrap {
    background: var(--bg-gray); border-radius: 8px;
    height: 48px; display: flex; align-items: center; padding: 0 16px;
}
.f-input, select.f-input {
    width: 100%; border: none; background: transparent; outline: none;
    font-size: 15px; font-weight: 600; color: var(--text-main); font-family: inherit;
}
select.f-input { cursor: pointer; appearance: none; }

/* Range Slider */
.slider-wrap { display: flex; align-items: center; gap: 16px; }
.range-slider {
    flex: 1; -webkit-appearance: none; width: 100%; height: 6px;
    background: #E5E7EB; border-radius: 4px; outline: none;
}
.range-slider::-webkit-slider-thumb {
    -webkit-appearance: none; appearance: none;
    width: 20px; height: 20px; border-radius: 50%;
    background: var(--navy); cursor: pointer; border: 3px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
.tenor-badge {
    background: var(--navy); color: #fff; padding: 6px 12px;
    border-radius: 6px; font-size: 13px; font-weight: 700;
}

/* ─────────────────────────────────────────────
   REFINANCING SECTION
───────────────────────────────────────────── */
.refinance-box {
    background: var(--bg-gray); border-radius: 12px; padding: 20px; margin-top: 32px;
}
.ref-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;
}
.ref-title { font-size: 14px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }

/* Toggle Switch */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider-sw { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #D1D5DB; transition: .3s; border-radius: 34px; }
.slider-sw:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
input:checked + .slider-sw { background-color: var(--green-accent); }
input:checked + .slider-sw:before { transform: translateX(20px); }
.sw-wrap { display: flex; align-items: center; gap: 12px; font-size: 13px; font-weight: 600; color: var(--text-main); }

/* Loan List */
#refinance-list { display: none; flex-direction: column; gap: 12px; }
#refinance-list.show { display: flex; }
.ref-item {
    display: flex; align-items: center; background: #fff; border: 2px solid transparent;
    padding: 16px; border-radius: 8px; cursor: pointer; transition: 0.2s; gap: 16px;
}
.ref-item:hover { border-color: #E5E7EB; }
.ref-item.selected { border-color: var(--green-accent); }

.cb-custom {
    width: 20px; height: 20px; border: 2px solid #D1D5DB; border-radius: 4px;
    display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.ref-item.selected .cb-custom { background: var(--green-accent); border-color: var(--green-accent); }
.cb-custom svg { width: 12px; height: 12px; color: #fff; opacity: 0; }
.ref-item.selected .cb-custom svg { opacity: 1; }

.ref-info { flex: 1; display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 1.2fr; align-items: center; }
.ref-name { font-size: 13px; font-weight: 700; color: var(--text-main); }
.ref-id { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.ref-val { font-size: 13px; font-weight: 700; color: var(--text-main); }
.ref-sub { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.ref-orange { color: #D97706; font-weight: 600; }

/* ─────────────────────────────────────────────
   RIGHT SIDEBAR (SUMMARY)
───────────────────────────────────────────── */
.sidebar { display: flex; flex-direction: column; gap: 16px; }

/* Top Green Card */
.sum-card-top {
    background: var(--green-light); border-radius: 12px; padding: 24px;
    border-bottom-left-radius: 0; border-bottom-right-radius: 0;
}
.s-label-green { font-size: 11px; font-weight: 700; color: var(--green-dark); display: flex; align-items: center; gap: 6px; letter-spacing: 0.5px; }
.s-label-muted { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-top: 16px; }
.s-val-large { font-size: 28px; font-weight: 900; color: var(--navy); margin-top: 4px; }
.s-row { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 16px; }
.s-row-label { font-size: 12px; color: var(--text-muted); }
.s-row-val { font-size: 14px; font-weight: 700; color: #DC2626; }

/* Middle Brown Card */
.sum-card-mid { background: var(--brown-bg); padding: 20px 24px; position: relative; }
.s-label-white { font-size: 11px; font-weight: 600; color: #fff; }
.s-val-orange { font-size: 24px; font-weight: 900; color: var(--orange-text); margin-top: 4px; }
.s-note { font-size: 10px; color: rgba(255,255,255,0.6); margin-top: 8px; line-height: 1.4; font-style: italic; }
.icon-wallet { position: absolute; right: 20px; top: 20px; opacity: 0.5; color: #fff; }

/* Bottom Grey Card */
.sum-card-bot { background: var(--bg-gray); padding: 20px 24px; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; position: relative;}
.s-label-gray { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;}
.s-val-green { font-size: 22px; font-weight: 800; color: var(--green-dark); margin-top: 4px; }
.icon-calc { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: #E5E7EB; border-radius: 50%; padding: 8px; color: var(--green-dark); }

/* Submit Button */
.btn-submit {
    background: var(--navy); color: #fff; width: 100%; padding: 16px;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 800;
    text-transform: uppercase; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: 0.2s;
}
.btn-submit:hover { background: #081530; }

/* Hidden Fields */
.hidden-inputs { display: none; }

/* Info chip bunga */
.bunga-info {
    display: none; align-items: center; gap: 6px;
    margin-top: 6px; padding: 6px 10px;
    background: #EFF6FF; border: 1px solid #BFDBFE;
    border-radius: 6px; font-size: 11px; color: #1d4ed8; font-weight: 600;
}
.bunga-info.show { display: flex; }
.bunga-info svg { flex-shrink: 0; }

/* Limit notice */
.limit-notice {
    display: none; align-items: flex-start; gap: 7px;
    margin-top: 8px; padding: 8px 12px;
    background: #FFFBEB; border: 1px solid #FDE68A;
    border-radius: 7px; font-size: 11px; color: #92400e; font-weight: 500;
    line-height: 1.5;
}
.limit-notice.show { display: flex; }
.limit-notice svg { flex-shrink: 0; margin-top: 1px; color: #b45309; }

/* Pelunasan error notice */
.payoff-error {
    display: none; align-items: flex-start; gap: 7px;
    margin-top: 8px; padding: 8px 12px;
    background: #FEF2F2; border: 1px solid #FECACA;
    border-radius: 7px; font-size: 11px; color: #991B1B; font-weight: 500;
    line-height: 1.5;
}
.payoff-error.show { display: flex; }
.payoff-error svg { flex-shrink: 0; margin-top: 1px; color: #DC2626; }

/* ─────────────────────────────────────────────
   PAYMENT METHOD CARDS
───────────────────────────────────────────── */
.pm-section {
    margin-top: 28px;
}
.pm-label {
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;
    display: flex; align-items: center; gap: 6px;
}
.pm-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
.pm-card {
    position: relative; cursor: pointer;
}
.pm-card input[type="radio"] {
    position: absolute; opacity: 0; width: 0; height: 0;
}
.pm-card-inner {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 16px 18px;
    border: 2px solid #E5E7EB;
    border-radius: 10px;
    background: #fff;
    transition: all 0.2s ease;
    cursor: pointer;
}
.pm-card input[type="radio"]:checked + .pm-card-inner {
    border-color: var(--navy);
    background: #EEF2FF;
    box-shadow: 0 0 0 3px rgba(11,28,63,0.08);
}
.pm-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.pm-icon-gaji { background: #DCFCE7; color: #16A34A; }
.pm-icon-mandiri { background: #DBEAFE; color: #1D4ED8; }
.pm-card input[type="radio"]:checked + .pm-card-inner .pm-icon-gaji { background: #16A34A; color: #fff; }
.pm-card input[type="radio"]:checked + .pm-card-inner .pm-icon-mandiri { background: #1D4ED8; color: #fff; }
.pm-text h4 {
    margin: 0 0 4px; font-size: 13px; font-weight: 700; color: var(--text-main);
}
.pm-text p {
    margin: 0; font-size: 11px; color: var(--text-muted); line-height: 1.5;
}
.pm-card input[type="radio"]:checked + .pm-card-inner .pm-text h4 { color: var(--navy); }
.pm-check {
    margin-left: auto; width: 18px; height: 18px; flex-shrink: 0;
    border-radius: 50%; border: 2px solid #D1D5DB;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.pm-card input[type="radio"]:checked + .pm-card-inner .pm-check {
    background: var(--navy); border-color: var(--navy);
}
.pm-check-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #fff; opacity: 0; transition: 0.2s;
}
.pm-card input[type="radio"]:checked + .pm-card-inner .pm-check-dot { opacity: 1; }
</style>

<form id="form-pengajuan" method="POST" action="{{ route('pinjaman.pengajuan.store') }}">
@csrf
<div class="hidden-inputs">
    <input type="hidden" name="user_id" id="user_id_input">
    <input type="hidden" name="bunga" id="bunga_input">
    <input type="hidden" name="include_pelunasan" id="include_pelunasan" value="0">
    <div id="pelunasan_inputs"></div>
</div>

<div class="pc-wrap">

    {{-- ══ SEARCH AREA ══ --}}
    <div class="search-label">CARI IDENTIFIKASI ANGGOTA</div>
    <div class="search-box">
        <div class="search-input-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <input type="text" id="nik" class="search-input" placeholder="Masukkan NIK atau User ID..." onkeydown="if(event.key==='Enter'){ event.preventDefault(); cari(); }">
        </div>
        <button type="button" class="search-btn" onclick="cari()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Cari
        </button>
    </div>

    {{-- ══ MEMBER BANNER ══ --}}
    <div id="mb-expand-container" style="flex-direction: column;">
        <div id="member-banner" class="mbanner" style="margin-bottom: 0; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
            <div class="mb-info">
                <div class="mb-avatar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="mb-text">
                    <h3 id="m-nama"></h3>
                    <p id="m-nik">NIK: • Gold Member</p>
                </div>
            </div>
            <div class="mb-stats">
                <div class="mb-stat-box">
                    <p>Total Simpanan</p>
                    <h4 id="m-simp"></h4>
                </div>
                <div class="mb-stat-box">
                    <p>Pinjaman Aktif</p>
                    <h4 id="m-aktif"></h4>
                </div>
            </div>
            <div class="mb-divider"></div>
            <div class="mb-stat-box">
                <p>Cicilan Saat Ini</p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h4 class="text-green" id="m-cicilan"> <span style="font-size:10px;font-weight:500;color:rgba(255,255,255,0.6)">/bln</span></h4>
                    <button type="button" id="btn-expand-loans" class="btn-expand-loans" onclick="toggleLoansDetail()" title="Lihat rincian pinjaman">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                </div>
            </div>
        </div>
        {{-- Detail Table (Expandable) --}}
        <div id="active-loans-detail" class="detail-wrap">
            <div class="dt-header">Rincian Pinjaman Aktif</div>
            <div id="detail-table-content"></div>
        </div>
    </div>

    {{-- ══ MAIN GRID ══ --}}
    <div class="main-grid">

        {{-- LEFT COLUMN: FORM --}}
        <div>
            <div class="card">
                <div class="card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Pengajuan Pinjaman Baru
                </div>

                <div class="f-row">
                    <div class="f-group">
                        <label class="f-label">Jenis Pinjaman</label>
                        <div class="f-input-wrap" style="background: #fff; border: 1.5px solid #E5E7EB;">
                            <select class="f-input" id="jenis" name="jenis_pinjaman_id" onchange="onJenis()">
                                <option value="">— Pilih Jenis —</option>
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
                        {{-- Info bunga otomatis --}}
                        <div class="bunga-info" id="bunga-info">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/><path d="M6.5 5v3.5M6.5 9.5h.01" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            Bunga: <strong id="bunga-val">0%</strong> / bulan &nbsp;|&nbsp; Dihitung otomatis dari jenis pinjaman
                        </div>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Jumlah Pinjaman (IDR)</label>
                        <div class="f-input-wrap">
                            <input type="number" class="f-input" id="jumlah" name="jumlah_pengajuan" placeholder="0" oninput="hitung()" required>
                        </div>
                        {{-- Notifikasi max limit (informatif saja, tidak memblokir) --}}
                        <div class="limit-notice" id="limit-notice">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1.5L13 12H1L7 1.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M7 5.5v3.5M7 10.5h.01" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            <span id="limit-notice-text">Jumlah melebihi batas maksimal jenis pinjaman ini. Pengajuan tetap bisa dikirim namun perlu persetujuan admin.</span>
                        </div>
                    </div>
                </div>

                <div class="f-group" style="margin-bottom: 8px;">
                    <label class="f-label">Tenor (Bulan)</label>
                    <div class="f-input-wrap" style="background: #fff; border: 1.5px solid #E5E7EB; margin-bottom: 8px;">
                        <select class="f-input" id="tenor_select" onchange="onTenorSelectChange()">
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                            <option value="18">18 Bulan</option>
                            <option value="24" selected>24 Bulan</option>
                            <option value="30">30 Bulan</option>
                            <option value="36">36 Bulan</option>
                            <option value="custom">Lainnya...</option>
                        </select>
                    </div>
                    <div class="f-input-wrap" id="custom_tenor_wrap" style="display: none; background: #fff; border: 1.5px solid #E5E7EB;">
                        <input type="number" class="f-input" id="custom_tenor" placeholder="Masukkan jumlah bulan" oninput="updateCustomTenor(this.value)" min="1">
                    </div>
                    <input type="hidden" id="tenor" name="tenor" value="24">
                </div>

                <input type="hidden" name="payment_method" value="gaji">

                {{-- REFINANCING SECTION --}}
                <div class="refinance-box">
                    <div class="ref-header">
                        <div class="ref-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="M17 3v2M7 3v2M3 11h18M4 7h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"></path><path d="M9 15l2 2 4-4"></path></svg>
                            Opsi Pelunasan
                        </div>
                        <div class="sw-wrap">
                            <label class="switch">
                                <input type="checkbox" id="toggle-refinance" onchange="toggleRefinance(this)">
                                <span class="slider-sw"></span>
                            </label>
                            Gunakan pinjaman untuk melunasi pinjaman yang ada
                        </div>
                    </div>
                    
                    <div id="refinance-list">
                        <!-- Diisi via JS -->
                        <div style="text-align:center; padding:10px; color:#6B7280; font-size:12px;">Cari anggota terlebih dahulu untuk melihat pinjaman aktif</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT COLUMN: SUMMARY --}}
        <div class="sidebar">
            <div style="border-radius:12px; overflow:hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

                {{-- Top Green --}}
                <div class="sum-card-top">
                    <div class="s-label-green">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        RINGKASAN PENGAJUAN
                    </div>
                    <div class="s-label-muted">Jumlah Pinjaman Baru</div>
                    <div class="s-val-large" id="sum-pokok">Rp 0</div>

                    {{-- Bunga info rows --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                        <span style="font-size:11px;color:#6B7280;">Total Bunga</span>
                        <span style="font-size:12px;font-weight:700;color:#D97706;" id="sum-total-bunga">Rp 0</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;padding-top:6px;border-top:1px dashed rgba(0,0,0,0.08);">
                        <span style="font-size:11px;color:#374151;font-weight:600;">Pokok + Bunga</span>
                        <span style="font-size:13px;font-weight:800;color:#0B1C3F;" id="sum-total-keseluruhan">Rp 0</span>
                    </div>

                    <div class="s-row" style="border-top:1px solid rgba(0,0,0,.05);padding-top:16px;margin-top:20px;flex-direction:column;gap:6px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;width:100%;">
                            <span class="s-row-label">Total Pelunasan Pinjaman</span>
                            <span class="s-row-val" id="sum-payoff">Rp 0</span>
                        </div>
                        {{-- Peringatan jika payoff > jumlah pinjaman --}}
                        <div class="payoff-error" id="payoff-error" style="width:100%;box-sizing:border-box;">
                            <svg width="13" height="13" viewBox="0 0 14 14" fill="none"><path d="M7 1.5L13 12H1L7 1.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M7 5.5v3.5M7 10.5h.01" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            <span id="payoff-error-text">Jumlah pinjaman lebih kecil dari total yang akan dilunasi.</span>
                        </div>
                    </div>
                </div>

                {{-- Mid Brown --}}
                <div class="sum-card-mid">
                    <svg class="icon-wallet" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><path d="M22 12h-4v4h4v-4z"></path></svg>
                    <div class="s-label-white">Dana Yang Dicairkan</div>
                    <div class="s-val-orange" id="sum-net">Rp 0</div>
                    <div class="s-note">Dihitung setelah melunasi kewajiban pinjaman yang dipilih.</div>
                </div>

                {{-- Bottom Gray --}}
                <div class="sum-card-bot">
                    <svg class="icon-calc" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="8.01" y2="10"></line><line x1="12" y1="10" x2="12.01" y2="10"></line><line x1="16" y1="10" x2="16.01" y2="10"></line><line x1="8" y1="14" x2="8.01" y2="14"></line><line x1="12" y1="14" x2="12.01" y2="14"></line><line x1="16" y1="14" x2="16.01" y2="14"></line><line x1="8" y1="18" x2="8.01" y2="18"></line><line x1="12" y1="18" x2="16" y2="18"></line></svg>
                    <div class="s-label-gray">Cicilan Per Bulan (Baru)</div>
                    <div class="s-val-green" id="sum-cicilan">Rp 0</div>
                </div>

            </div>

            {{-- ══ KONDISI FINANSIAL SAAT INI (Expandable) ══ --}}
            <div id="card-kondisi" style="display:none;background:#fff;border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <button type="button" onclick="toggleKondisi()"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#EFF6FF;border:none;cursor:pointer;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span style="font-size:12px;font-weight:800;color:#1D4ED8;text-transform:uppercase;letter-spacing:.04em;">Kondisi Finansial Saat Ini</span>
                    </div>
                    <svg id="kondisi-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2.5" style="transition:.2s;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>

                <div id="kondisi-body" style="display:none;padding:16px 18px;">

                    {{-- Simpanan --}}
                    <div style="margin-bottom:14px;">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;margin-bottom:4px;">Total Simpanan</div>
                        <div style="font-size:18px;font-weight:800;color:#059669;" id="kf-simpanan">Rp 0</div>
                    </div>

                    {{-- Separator --}}
                    <div style="height:1px;background:#F3F4F6;margin-bottom:14px;"></div>

                    {{-- Cicilan Existing --}}
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;margin-bottom:8px;">Cicilan Pinjaman Berjalan / Bulan</div>

                    <table style="width:100%;font-size:11px;border-collapse:collapse;">
                        <thead>
                            <tr style="color:#6B7280;">
                                <th style="text-align:left;padding:3px 0;font-weight:600;">Metode</th>
                                <th style="text-align:right;padding:3px 0;font-weight:600;">Sebelum</th>
                                <th style="text-align:right;padding:3px 0;font-weight:600;">Setelah ✓</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-top:1px solid #E5E7EB;">
                                <td style="padding:6px 0;">
                                    <span style="display:inline-flex;align-items:center;gap:4px;background:#DCFCE7;color:#15803D;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                        Potong Gaji
                                    </span>
                                </td>
                                <td style="text-align:right;color:#374151;font-weight:600;" id="kf-gaji-before">Rp 0</td>
                                <td style="text-align:right;font-weight:700;color:#15803D;" id="kf-gaji-after">Rp 0</td>
                            </tr>
                            <tr style="border-top:1px solid #E5E7EB;">
                                <td style="padding:6px 0;">
                                    <span style="display:inline-flex;align-items:center;gap:4px;background:#DBEAFE;color:#1D4ED8;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                        Cash (Manual)
                                    </span>
                                </td>
                                <td style="text-align:right;color:#374151;font-weight:600;" id="kf-mandiri-before">Rp 0</td>
                                <td style="text-align:right;font-weight:700;color:#1D4ED8;" id="kf-mandiri-after">Rp 0</td>
                            </tr>
                            <tr style="border-top:2px solid #E5E7EB;background:#F9FAFB;">
                                <td style="padding:7px 0;font-size:11px;font-weight:800;color:#111;">Total Cicilan</td>
                                <td style="text-align:right;font-weight:700;color:#374151;" id="kf-total-before">Rp 0</td>
                                <td style="text-align:right;font-weight:800;color:#DC2626;" id="kf-total-after">Rp 0</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Note --}}
                    <div style="margin-top:10px;font-size:10px;color:#9CA3AF;line-height:1.5;">* Kolom <em>Setelah</em> menunjukkan estimasi cicilan jika pengajuan ini disetujui.</div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                KIRIM PENGAJUAN
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>

    </div>
</div>
</form>

<script>
/* ─── Helpers ─── */
function rp(n) { return 'Rp ' + (Math.round(n) || 0).toLocaleString('id-ID'); }

let activeLoans        = [];
let selectedRefinanceIds = [];
let currentBunga       = 0;
let usagePerParent     = {};   // { parent_id: total_sudah_dipakai }

/* ─── Pencarian Anggota ─── */
async function cari() {
    const v = document.getElementById('nik').value.trim();
    if (!v) return;

    try {
        const res = await fetch(`{{ route('pinjaman.pengajuan.searchAnggota') }}?q=${encodeURIComponent(v)}`);
        const data = await res.json();

        if (data.success) {
            const d = data.data;
            // console.log(d);
            activeLoans    = d.pinjaman_berjalan || [];
            usagePerParent = d.usage_per_parent  || {};
            
            // Set User Data
            document.getElementById('user_id_input').value = d.user_id;
            document.getElementById('m-nama').textContent = d.nama;
            document.getElementById('m-nik').textContent = `NIK: ${d.nik} • Anggota Aktif`;
            document.getElementById('m-simp').textContent = rp(d.total_simpanan);
            document.getElementById('m-aktif').textContent = rp(d.pinjaman_aktif);
            
            // Hitung total cicilan saat ini
            let totalCicilan = activeLoans.reduce((sum, p) => sum + (parseFloat(p.cicilan_per_bulan) || 0), 0);
            document.getElementById('m-cicilan').innerHTML = `${rp(totalCicilan)} <span style="font-size:10px;font-weight:500;color:rgba(255,255,255,0.6)">/bln</span>`;

            document.getElementById('mb-expand-container').style.display = 'flex';
            
            // Reset detail view
            document.getElementById('active-loans-detail').classList.remove('show');
            document.getElementById('btn-expand-loans').classList.remove('active');

            // ── Kondisi Finansial: pisah cicilan per metode ──
            _simpanan = parseFloat(d.total_simpanan) || 0;
            _gajiExisting = activeLoans
                .filter(p => !p.payment_method || p.payment_method === 'gaji')
                .reduce((s, p) => s + (parseFloat(p.cicilan_per_bulan) || 0), 0);
            _mandiriExisting = activeLoans
                .filter(p => p.payment_method === 'mandiri')
                .reduce((s, p) => s + (parseFloat(p.cicilan_per_bulan) || 0), 0);

            // Tampilkan card kondisi
            document.getElementById('card-kondisi').style.display = 'block';

            buildRefinanceList();
            hitung();
        } else {
            alert('Anggota tidak ditemukan.');
            document.getElementById('mb-expand-container').style.display = 'none';
            document.getElementById('card-kondisi').style.display = 'none';
        }
    } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan sistem.');
    }
}

/* ─── Expandable Details ─── */
function toggleLoansDetail() {
    const detail = document.getElementById('active-loans-detail');
    const btn    = document.getElementById('btn-expand-loans');
    const isShowing = detail.classList.toggle('show');
    btn.classList.toggle('active');
    
    if (isShowing) renderLoansDetail();
}

function renderLoansDetail() {
    const list = document.getElementById('detail-table-content');
    list.innerHTML = '';

    if (activeLoans.length === 0) {
        list.innerHTML = '<div style="text-align:center; padding:10px; color:#6B7280; font-size:12px;">Tidak ada pinjaman berjalan.</div>';
        return;
    }

    let html = `
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Jenis & ID Pinjaman</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Total Bunga</th>
                    <th>Sisa Tagihan</th>
                    <th>Cicilan / Bulan</th>
                    <th>Metode</th>
                    <th style="text-align:right;">Sisa Tenor</th>
                </tr>
            </thead>
            <tbody>
    `;

    activeLoans.forEach(p => {
        const sisaBulan = p.sisa_tenor_label ? p.sisa_tenor_label : p.sisa_tenor + ' bulan tersisa';
        const paymentMethod = p.payment_method ? (p.payment_method.charAt(0).toUpperCase() + p.payment_method.slice(1)) : 'Gaji';
        const totalBungaLabel = rp(parseFloat(p.total_bunga) || 0) + ' (' + parseFloat(p.bunga*p.tenor || 0) + '%)';
        
        html += `
            <tr>
                <td>
                    <div class="ref-name">${p.jenis_pinjaman}</div>
                    <div class="ref-id">ID: LN-2023-${p.id.toString().padStart(3, '0')}</div>
                </td>
                <td><div class="ref-val">${rp(parseFloat(p.jumlah_pinjaman) || 0)}</div></td>
                <td><div class="ref-val">${totalBungaLabel}</div></td>
                <td><div class="ref-val">${rp(parseFloat(p.sisa_tagihan) || 0)}</div></td>
                <td><div class="ref-val">${rp(parseFloat(p.cicilan_per_bulan) || 0)}</div></td>
                <td>
                    <div class="ref-val" style="display:inline-flex;align-items:center;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;${p.payment_method === 'mandiri' ? 'background:#DBEAFE;color:#1D4ED8;' : 'background:#DCFCE7;color:#15803D;'}">${paymentMethod}</div>
                </td>
                <td style="text-align:right;"><div class="ref-val ref-orange">${sisaBulan}</div></td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    list.innerHTML = html;
}

/* ─── Form Inputs ─── */
function onJenis() {
    const sel = document.getElementById('jenis');
    const opt = sel.options[sel.selectedIndex];

    const bungaInfo = document.getElementById('bunga-info');
    const bungaVal  = document.getElementById('bunga-val');

    if (opt && opt.value) {
        currentBunga = parseFloat(opt.dataset.bunga) || 0;
        document.getElementById('bunga_input').value = currentBunga;

        // Tampilkan info bunga
        bungaVal.textContent = currentBunga + '%';
        bungaInfo.classList.add('show');

        // Hitung sisa limit efektif:
        // limit_maksimal_parent - pinjaman aktif yang sudah berjalan pada parent yang sama
        const limitParent  = parseFloat(opt.dataset.limit)  || 0;
        const parentId     = opt.dataset.parent || '';
        const parentNama   = opt.dataset.parentNama || '';
        const sudahDigunakan = parseFloat(usagePerParent[parentId]) || 0;
        const sisaLimit    = Math.max(0, limitParent - sudahDigunakan);

        // Simpan ke dataset untuk dipakai hitung()
        document.getElementById('jenis').dataset.activelimit    = limitParent;
        document.getElementById('jenis').dataset.sisa_limit     = sisaLimit;
        document.getElementById('jenis').dataset.sudah_dipakai  = sudahDigunakan;
        document.getElementById('jenis').dataset.activenama     = parentNama;
    } else {
        currentBunga = 0;
        document.getElementById('bunga_input').value = 0;
        bungaInfo.classList.remove('show');
        document.getElementById('limit-notice').classList.remove('show');
        document.getElementById('jenis').dataset.activelimit   = 0;
        document.getElementById('jenis').dataset.sisa_limit    = 0;
        document.getElementById('jenis').dataset.sudah_dipakai = 0;
    }
    hitung();
}

function onTenorSelectChange() {
    const val = document.getElementById('tenor_select').value;
    const customWrap = document.getElementById('custom_tenor_wrap');
    if (val === 'custom') {
        customWrap.style.display = 'flex';
        const customVal = document.getElementById('custom_tenor').value;
        document.getElementById('tenor').value = customVal ? customVal : 0;
        hitung();
    } else {
        customWrap.style.display = 'none';
        document.getElementById('tenor').value = val;
        hitung();
    }
}

function updateCustomTenor(val) {
    if (val && parseInt(val) > 0) {
        document.getElementById('tenor').value = parseInt(val);
    } else {
        document.getElementById('tenor').value = 0;
    }
    hitung();
}

function updateTenor(val) {
    const select = document.getElementById('tenor_select');
    let optionExists = false;
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value == val) {
            optionExists = true;
            break;
        }
    }
    
    if (optionExists) {
        select.value = val;
        document.getElementById('custom_tenor_wrap').style.display = 'none';
    } else {
        select.value = 'custom';
        document.getElementById('custom_tenor_wrap').style.display = 'flex';
        document.getElementById('custom_tenor').value = val;
    }
    document.getElementById('tenor').value = val;
    hitung();
}

/* ─── Refinancing Logic ─── */
function toggleRefinance(checkbox) {
    const list = document.getElementById('refinance-list');
    if (checkbox.checked) {
        list.classList.add('show');
        document.getElementById('include_pelunasan').value = '1';
    } else {
        list.classList.remove('show');
        document.getElementById('include_pelunasan').value = '0';
        selectedRefinanceIds = []; // reset selection
        buildRefinanceList(); // re-render to uncheck UI
    }
    hitung();
}

function buildRefinanceList() {
    const list = document.getElementById('refinance-list');
    list.innerHTML = '';

    if (activeLoans.length === 0) {
        list.innerHTML = '<div style="text-align:center; padding:10px; color:#6B7280; font-size:12px;">Tidak ada pinjaman aktif yang bisa dilunasi.</div>';
        return;
    }

    activeLoans.forEach(p => {
        const isSelected = selectedRefinanceIds.includes(p.id);
        const sisaBulan = p.sisa_tenor_label ? p.sisa_tenor_label : p.sisa_tenor + ' bulan tersisa';

        const item = document.createElement('div');
        item.className = `ref-item ${isSelected ? 'selected' : ''}`;
        item.onclick = () => toggleSelectLoan(p.id);

        item.innerHTML = `
            <div class="cb-custom">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="ref-info">
                <div>
                    <div class="ref-name">${p.jenis_pinjaman}</div>
                    <div class="ref-id">ID: LN-2023-${p.id.toString().padStart(3, '0')}</div>
                </div>
                <div>
                    <div class="ref-val">${rp(parseFloat(p.sisa_tagihan) || 0)}</div>
                    <div class="ref-sub">Sisa Tagihan</div>
                </div>
                <div>
                    <div class="ref-val">${rp(parseFloat(p.cicilan_per_bulan) || 0)}</div>
                    <div class="ref-sub">Cicilan / Bulan</div>
                </div>
                <div style="text-align:right;">
                    <div class="ref-val ref-orange">${sisaBulan}</div>
                    <div class="ref-sub">Sisa Tenor</div>
                </div>
            </div>
        `;
        list.appendChild(item);
    });
}

function toggleSelectLoan(id) {
    const idx = selectedRefinanceIds.indexOf(id);
    if (idx > -1) {
        selectedRefinanceIds.splice(idx, 1);
    } else {
        selectedRefinanceIds.push(id);
    }
    buildRefinanceList();
    hitung();
}

/* ─── Kalkulasi Summary ─── */
function hitung() {
    const j = parseFloat(document.getElementById('jumlah').value) || 0;
    const t = parseInt(document.getElementById('tenor').value) || 0;

    // Cek limit dari parent pinjaman
    const jenisEl      = document.getElementById('jenis');
    const limitMax     = parseFloat(jenisEl.dataset.activelimit)   || 0;
    const sisaLimit    = parseFloat(jenisEl.dataset.sisa_limit)    || 0;
    const sudahDipakai = parseFloat(jenisEl.dataset.sudah_dipakai) || 0;
    const parentNama   = jenisEl.dataset.activenama || '';
    const notice       = document.getElementById('limit-notice');
    const noticeText   = document.getElementById('limit-notice-text');

    if (limitMax > 0 && j > sisaLimit) {
        const totalTerpakai = sudahDipakai + j;
        noticeText.innerHTML =
            `Jumlah pengajuan <strong>${rp(j)}</strong> melebihi sisa limit yang tersedia.<br>` +
            `<span style="font-size:10px">` +
            `Limit ${parentNama}: ${rp(limitMax)} &nbsp;|&nbsp; ` +
            `Sudah digunakan: ${rp(sudahDipakai)} &nbsp;|&nbsp; ` +
            `Sisa tersedia: <strong>${rp(sisaLimit)}</strong>` +
            `</span><br>` +
            `<span style="font-size:10px">Pengajuan tetap bisa dikirim, namun memerlukan persetujuan admin.</span>`;
        notice.classList.add('show');
    } else {
        notice.classList.remove('show');
    }

    // Hitung Total Payoff (Pelunasan)
    let totalPayoff = 0;
    if (document.getElementById('toggle-refinance').checked) {
        activeLoans.forEach(p => {
            if (selectedRefinanceIds.includes(p.id)) {
                totalPayoff += parseFloat(p.sisa_tagihan || 0);
            }
        });
    }

    // ── Validasi: jumlah pinjaman tidak boleh < total payoff ──
    const payoffError    = document.getElementById('payoff-error');
    const payoffErrText  = document.getElementById('payoff-error-text');
    const submitBtn      = document.querySelector('.btn-submit');
    const isPayoffInvalid = totalPayoff > 0 && j < totalPayoff;

    if (isPayoffInvalid) {
        payoffErrText.innerHTML =
            `Jumlah pinjaman <strong>${rp(j)}</strong> lebih kecil dari total pelunasan <strong>${rp(totalPayoff)}</strong>. ` +
            `Minimal pinjam <strong>${rp(totalPayoff)}</strong>.`;
        payoffError.classList.add('show');
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor  = 'not-allowed';
    } else {
        payoffError.classList.remove('show');
        submitBtn.disabled = false;
        submitBtn.style.opacity = '';
        submitBtn.style.cursor  = '';
    }

    let net = j - totalPayoff;

    // Hitung Cicilan (Bunga Flat)
    let totalBunga = j * (currentBunga / 100) * t;
    let totalKeseluruhan = j + totalBunga;
    let cicilan = t > 0 ? totalKeseluruhan / t : 0;

    // Update UI Summary
    document.getElementById('sum-pokok').textContent            = rp(j);
    document.getElementById('sum-total-bunga').textContent      = rp(totalBunga);
    document.getElementById('sum-total-keseluruhan').textContent= rp(totalKeseluruhan);
    document.getElementById('sum-payoff').textContent           = totalPayoff > 0 ? rp(totalPayoff) : 'Rp 0';
    document.getElementById('sum-net').textContent              = rp(net);
    document.getElementById('sum-cicilan').textContent          = rp(cicilan);

    // Update Kondisi Finansial card (after values)
    updateKondisiFinansial(cicilan);

    // Update Hidden Inputs for Backend Form Submit
    const container = document.getElementById('pelunasan_inputs');
    container.innerHTML = '';
    selectedRefinanceIds.forEach(id => {
        container.innerHTML += `<input type="hidden" name="pelunasan_ids[]" value="${id}">`;
    });
}

/* ─── Kondisi Finansial Card ─── */
let _simpanan = 0, _gajiExisting = 0, _mandiriExisting = 0;

function updateKondisiFinansial(cicilanBaru) {
    // Determine payment method
    const pm = document.querySelector('input[name="payment_method"]')?.value || 'gaji';

    const gajiAfter    = pm === 'gaji'    ? _gajiExisting + cicilanBaru    : _gajiExisting;
    const mandiriAfter = pm === 'mandiri' ? _mandiriExisting + cicilanBaru : _mandiriExisting;

    document.getElementById('kf-simpanan').textContent       = rp(_simpanan);
    document.getElementById('kf-gaji-before').textContent    = rp(_gajiExisting);
    document.getElementById('kf-mandiri-before').textContent = rp(_mandiriExisting);
    document.getElementById('kf-gaji-after').textContent     = rp(gajiAfter);
    document.getElementById('kf-mandiri-after').textContent  = rp(mandiriAfter);
    document.getElementById('kf-total-before').textContent   = rp(_gajiExisting + _mandiriExisting);
    document.getElementById('kf-total-after').textContent    = rp(gajiAfter + mandiriAfter);
}

function toggleKondisi() {
    const body   = document.getElementById('kondisi-body');
    const caret  = document.getElementById('kondisi-caret');
    const isOpen = body.style.display !== 'none';
    body.style.display  = isOpen ? 'none' : 'block';
    caret.style.transform = isOpen ? '' : 'rotate(180deg)';
}

// Inisialisasi on load
document.addEventListener("DOMContentLoaded", () => {
    updateTenor(document.getElementById('tenor').value);
});
</script>
@endsection