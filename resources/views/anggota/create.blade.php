{{-- resources/views/anggota/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('topbar-nav')
    <a href="{{ route('anggota.index') }}" class="tb-link {{ request()->routeIs('anggota.*') ? 'active' : '' }}">Karyawan</a>
    <a href="{{ route('departemen.index') }}" class="tb-link">Departemen</a>
    <a href="{{ route('learning.index') }}" class="tb-link">Learning</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('konfigurasi.index') }}" class="tb-link">Konfigurasi</a>
@endsection

@section('subbar-actions')
    {{-- kosong, tombol ada di header khusus --}}
@endsection

@section('page-title', '')

@push('styles')
    <style>
    /* ── Form page header ────────────────────────────── */
    .form-topbar{
        display:flex;align-items:center;justify-content:space-between;
        padding:12px 28px;
        background:#fff;
        border-bottom:1px solid #E8E6F0;
        z-index:100;
    }
    .form-topbar-left{ display:flex;align-items:center;gap:14px; }
    .form-topbar-logo{
        display:flex;align-items:center;gap:8px;
        font-size:15px;font-weight:700;color:#1a56db;
        text-decoration:none;
    }
    .form-topbar-logo svg{ color:#1a56db; }
    .form-topbar-divider{ width:1px;height:32px;background:#E8E6F0; }
    .form-topbar-info strong{
        display:block;font-size:13px;font-weight:700;color:#111827;
    }
    .form-topbar-info span{
        display:block;font-size:11px;color:#6B7280;margin-top:1px;
    }
    .form-topbar-actions{ display:flex;align-items:center;gap:8px; }
    .btn-cancel{
        padding:8px 18px;font-size:13px;font-weight:600;
        color:#374151;background:#fff;
        border:1.5px solid #D1D5DB;border-radius:8px;
        cursor:pointer;transition:background .12s;
    }
    .btn-cancel:hover{ background:#F9FAFB; }
    .btn-save{
        display:inline-flex;align-items:center;gap:6px;
        padding:8px 20px;font-size:13px;font-weight:700;
        color:#fff;background:#1a56db;
        border:none;border-radius:8px;
        cursor:pointer;transition:background .15s;
    }
    .btn-save:hover{ background:#1447c0; }

    /* ── Page body ───────────────────────────────────── */
    .create-body{
        min-height:calc(100vh - 108px);
        background:#F3F4F6;
        padding:28px;
    }

    /* Page heading */
    .create-heading{ margin-bottom:24px; }
    .create-heading h1{
        font-size:26px;font-weight:800;color:#111827;line-height:1.2;
    }
    .create-heading p{
        font-size:13px;color:#6B7280;margin-top:6px;
    }

    /* Two-column layout */
    .create-grid{
        display:grid;
        grid-template-columns:1fr 320px;
        gap:20px;
        align-items:start;
    }
    @media(max-width:900px){
        .create-grid{ grid-template-columns:1fr; }
    }

    /* Cards */
    .form-card{
        background:#fff;
        border:1px solid #E5E7EB;
        border-radius:14px;
        overflow:hidden;
        margin-bottom:20px;
    }
    .form-card:last-of-type{ margin-bottom:0; }

    .card-header{
        display:flex;align-items:center;justify-content:space-between;
        padding:18px 22px 14px;
        border-bottom:1px solid #F3F4F6;
    }
    .card-header-left{
        display:flex;align-items:center;gap:10px;
        font-size:15px;font-weight:700;color:#111827;
    }
    .card-header-left svg{ color:#1a56db; }
    .card-body{ padding:22px; }

    /* Form grid */
    .field-row{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:16px;
        margin-bottom:16px;
    }
    .field-row.full{ grid-template-columns:1fr; }

    .form-group{ display:flex;flex-direction:column;gap:5px; }

    .form-label{
        font-size:12px;font-weight:600;color:#374151;
    }

    .form-control{
        height:42px;
        padding:0 13px;
        font-size:13px;
        color:#111827;
        background:#fff;
        border:1.5px solid #D1D5DB;
        border-radius:8px;
        outline:none;
        transition:border-color .15s,box-shadow .15s;
        font-family:inherit;
        width:100%;
    }
    .form-control::placeholder{ color:#9CA3AF; }
    .form-control:focus{
        border-color:#1a56db;
        box-shadow:0 0 0 3px rgba(26,86,219,.12);
    }

    textarea.form-control{
        height:90px;
        padding:11px 13px;
        resize:vertical;
    }

    select.form-control{
        appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat:no-repeat;
        background-position:right 13px center;
        padding-right:34px;
        cursor:pointer;
    }

    input[type="date"].form-control{ cursor:pointer; }

    /* Input prefix (Rp) */
    .input-prefix-wrap{ position:relative;display:flex;align-items:center; }
    .input-prefix{
        position:absolute;left:13px;
        font-size:13px;font-weight:600;color:#374151;
        pointer-events:none;
    }
    .input-prefix-wrap .form-control{ padding-left:34px; }

    /* Simpanan row (amount + dropdown) */
    .simpanan-row{
        display:grid;
        grid-template-columns:1fr 160px;
        gap:10px;
    }

    /* Toggle switch */
    .toggle-wrap{ display:flex;align-items:center;gap:8px; }
    .toggle-label{ font-size:12px;font-weight:500;color:#374151; }
    .toggle{
        position:relative;width:40px;height:22px;
        display:inline-block;flex-shrink:0;
    }
    .toggle input{ opacity:0;width:0;height:0; }
    .toggle-slider{
        position:absolute;inset:0;
        background:#D1D5DB;border-radius:20px;
        cursor:pointer;transition:background .2s;
    }
    .toggle-slider::before{
        content:'';position:absolute;
        width:16px;height:16px;
        left:3px;top:3px;
        background:#fff;border-radius:50%;
        transition:transform .2s;
        box-shadow:0 1px 3px rgba(0,0,0,.2);
    }
    .toggle input:checked + .toggle-slider{ background:#1a56db; }
    .toggle input:checked + .toggle-slider::before{ transform:translateX(18px); }

    /* Info note */
    .info-note{
        display:flex;align-items:flex-start;gap:7px;
        background:#EFF6FF;border:1px solid #BFDBFE;
        border-radius:8px;padding:9px 13px;
        font-size:12px;color:#1e40af;margin-top:10px;
    }
    .info-note svg{ flex-shrink:0;margin-top:1px; }

    /* Auto-generate badge */
    .auto-badge{
        display:inline-flex;align-items:center;gap:7px;
        background:#EFF6FF;border:1px solid #BFDBFE;
        border-radius:8px;padding:8px 14px;
        font-size:12px;font-weight:600;color:#1a56db;
        margin-top:10px;width:100%;
    }
    .auto-badge svg{ color:#1a56db;flex-shrink:0; }

    /* Simpanan section separator */
    .simpanan-item{
        padding:18px 0;
        border-bottom:1px solid #F3F4F6;
    }
    .simpanan-item:last-child{ border-bottom:none;padding-bottom:0; }
    .simpanan-item-top{
        display:grid;grid-template-columns:200px 1fr;
        gap:16px;align-items:start;
    }
    .simpanan-item-label strong{
        display:block;font-size:13px;font-weight:700;color:#111827;
    }
    .simpanan-item-label span{
        display:block;font-size:11px;color:#9CA3AF;margin-top:3px;
    }

    /* ── RIGHT COLUMN ────────────────────────────────── */
    .summary-card{
        background:#fff;border:1px solid #E5E7EB;
        border-radius:14px;overflow:hidden;
        position:sticky;top:calc(var(--topbar-h,40px) + 60px);
    }
    .summary-header{
        display:flex;align-items:center;gap:10px;
        padding:16px 20px;border-bottom:1px solid #F3F4F6;
        font-size:14px;font-weight:700;color:#111827;
    }
    .summary-header svg{ color:#1a56db; }
    .summary-body{ padding:18px 20px; }
    .summary-row{
        display:flex;align-items:center;justify-content:space-between;
        padding:7px 0;border-bottom:1px solid #F9FAFB;
    }
    .summary-row:last-of-type{ border-bottom:none; }
    .summary-row-label{ font-size:12px;color:#6B7280; }
    .summary-row-val{ font-size:12px;font-weight:600;color:#111827; }
    .summary-row-sub{ font-size:10px;color:#9CA3AF; }
    .summary-total{
        margin-top:14px;padding-top:14px;
        border-top:2px solid #E5E7EB;
    }
    .summary-total-label{
        font-size:10px;font-weight:700;letter-spacing:.07em;
        text-transform:uppercase;color:#1a56db;
    }
    .summary-total-amount{
        display:flex;align-items:center;justify-content:space-between;
        margin-top:4px;
    }
    .summary-total-amount span{
        font-size:24px;font-weight:800;color:#111827;
    }
    .summary-total-icon{
        width:34px;height:34px;
        background:#EFF6FF;border:1px solid #BFDBFE;
        border-radius:8px;display:flex;align-items:center;justify-content:center;
        color:#1a56db;
    }
    .btn-confirm{
        display:block;width:100%;
        padding:12px;font-size:14px;font-weight:700;
        color:#fff;background:#1a56db;
        border:none;border-radius:10px;
        cursor:pointer;margin-top:16px;
        transition:background .15s;
    }
    .btn-confirm:hover{ background:#1447c0; }
    .summary-confirm-note{
        font-size:11px;color:#9CA3AF;text-align:center;
        margin-top:8px;line-height:1.5;
    }

    /* Help card */
    .help-card{
        margin-top:16px;
        background:linear-gradient(135deg,#1a56db 0%,#1e3a8a 100%);
        border-radius:14px;padding:18px 20px;
        color:#fff;
    }
    .help-card-title{
        display:flex;align-items:center;gap:8px;
        font-size:14px;font-weight:700;margin-bottom:8px;
    }
    .help-card-desc{
        font-size:12px;color:rgba(255,255,255,.8);
        line-height:1.6;margin-bottom:14px;
    }
    .btn-help{
        display:block;width:100%;
        padding:10px;font-size:13px;font-weight:600;
        color:#fff;background:rgba(255,255,255,.18);
        border:1px solid rgba(255,255,255,.3);
        border-radius:8px;text-align:center;
        cursor:pointer;transition:background .12s;
    }
    .btn-help:hover{ background:rgba(255,255,255,.28); }

    /* Footer */
    .create-footer{
        display:flex;align-items:center;justify-content:space-between;
        padding:16px 28px;
        background:#fff;border-top:1px solid #E8E6F0;
        font-size:12px;color:#9CA3AF;
    }
    .create-footer-links{ display:flex;gap:18px; }
    .create-footer-links a{ color:#6B7280;text-decoration:none; }
    .create-footer-links a:hover{ color:#111827; }

    /* Validation error */
    .field-error{ font-size:11px;color:#DC2626;margin-top:3px; }
    </style>
@endpush

@section('content')

{{-- ── Sticky form header ───────────────────────────── --}}
<div class="form-topbar">
    <div class="form-topbar-left">
        <a href="{{ route('anggota.index') }}" class="form-topbar-logo">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                <rect width="22" height="22" rx="5" fill="#EFF6FF"/>
                <path d="M5 16v-1a5 5 0 0 1 10 0v1" stroke="#1a56db" stroke-width="1.6" stroke-linecap="round"/>
                <circle cx="10" cy="8" r="3" stroke="#1a56db" stroke-width="1.6"/>
            </svg>
            Koperasi Modern
        </a>
        <div class="form-topbar-divider"></div>
        <div class="form-topbar-info">
            <strong>Tambah Anggota</strong>
            <span>Manajemen Keanggotaan</span>
        </div>
    </div>
    <div class="form-topbar-actions">
        <a href="{{ route('anggota.index') }}" class="btn-cancel">Batal</a>
        <button type="submit" form="form-anggota" class="btn-save">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                <path d="M2 7.5l4 4 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Simpan Anggota
        </button>
    </div>
</div>

{{-- ── Main form body ──────────────────────────────── --}}
<div class="create-body">

    <div class="create-heading">
        <h1>Formulir Pendaftaran</h1>
        <p>Input data anggota baru dan kustomisasi pengaturan simpanan wajib/pokok.</p>
    </div>

    <form id="form-anggota" method="POST" action="{{ route('anggota.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="create-grid">

            {{-- ════════════════════════════════════
                 KOLOM KIRI — form fields
                 ════════════════════════════════════ --}}
            <div>

                {{-- Card: Informasi Identitas --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <circle cx="9" cy="6" r="3.5" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M2 16c0-4 3-6 7-6s7 2 7 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M12 2l1 1-1 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                            Informasi Identitas
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Nama + NIK --}}
                        <div class="field-row">
                            <div class="form-group">
                                <label class="form-label" for="nama">Nama Lengkap</label>
                                <input id="nama" name="nama" type="text"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       placeholder="Masukkan nama sesuai KTP"
                                       value="{{ old('nama') }}">
                                @error('nama')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="nik">NIK (Nomor Induk Kependudukan)</label>
                                <input id="nik" name="nik" type="text"
                                       class="form-control @error('nik') is-invalid @enderror"
                                       placeholder="16 Digit NIK" maxlength="16"
                                       value="{{ old('nik') }}">
                                @error('nik')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Bagian + No Pegawai --}}
                        <div class="field-row">
                            <div class="form-group">
                                <label class="form-label" for="bagian">Bagian</label>
                                <input id="bagian" name="bagian" type="text"
                                       class="form-control @error('bagian') is-invalid @enderror"
                                       placeholder="Masukkan bagian"
                                       value="{{ old('bagian') }}">
                                @error('bagian')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="no_pegawai">No. Pegawai</label>
                                <input id="no_pegawai" name="no_pegawai" type="text"
                                       class="form-control @error('no_pegawai') is-invalid @enderror"
                                       placeholder="Contoh: 08123456789"
                                       value="{{ old('no_pegawai') }}">
                                @error('no_pegawai')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Tanggal Lahir + No HP --}}
                        <div class="field-row">
                            <div class="form-group">
                                <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                <input id="tanggal_lahir" name="tanggal_lahir" type="date"
                                       class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                       value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="no_hp">No. HP / WhatsApp</label>
                                <input id="no_hp" name="no_hp" type="text"
                                       class="form-control @error('no_hp') is-invalid @enderror"
                                       placeholder="Contoh: 08123456789"
                                       value="{{ old('no_hp') }}">
                                @error('no_hp')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        {{-- Jenis Kelamin + Status Pegawai --}}
                        <div class="field-row">
                            <div class="form-group">
                                <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin"
                                       class="form-control @error('jenis_kelamin') is-invalid @enderror"
                                       value="{{ old('jenis_kelamin') }}">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('jenis_kelamin')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="status_pegawai">Status Pegawai</label>
                                <select id="status_pegawai" name="status_pegawai"
                                       class="form-control @error('status_pegawai') is-invalid @enderror"
                                       value="{{ old('status_pegawai') }}">
                                    <option value="">Pilih Status Pegawai</option>
                                    <option value="Kontrak">Kontrak</option>
                                    <option value="Tetap">Tetap</option>
                                </select>
                                @error('status_pegawai')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="field-row full">
                            <div class="form-group">
                                <label class="form-label" for="alamat">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat"
                                          class="form-control @error('alamat') is-invalid @enderror"
                                          placeholder="Jl. Nama Jalan No. 123, Kelurahan, Kecamatan...">{{ old('alamat') }}</textarea>
                                @error('alamat')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="field-row" style="grid-template-columns:1fr 1fr;max-width:320px">
                            <div class="form-group">
                                <label class="form-label" for="status">Status Keanggotaan</label>
                                <select id="status" name="status"
                                        class="form-control @error('status') is-invalid @enderror">
                                    <option value="aktif"   {{ old('status','aktif') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif"{{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="pending" {{ old('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                                </select>
                                @error('status')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="tanggal_masuk">Tanggal Masuk</label>
                                <input id="tanggal_masuk" name="tanggal_masuk" type="date"
                                       class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                       value="{{ old('tanggal_masuk', date('Y-m-d')) }}">
                                @error('tanggal_masuk')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                    </div>
                </div>{{-- /card identitas --}}

                {{-- Card: Pengaturan Simpanan --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <rect x="2" y="5" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M5 5V4a3 3 0 0 1 8 0v1" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="9" cy="10" r="2" stroke="currentColor" stroke-width="1.3"/>
                            </svg>
                            Pengaturan Simpanan
                        </div>
                        <div class="toggle-wrap">
                            <span class="toggle-label">Default Koperasi</span>
                            <label class="toggle">
                                <input type="checkbox" id="defaultToggle" checked onchange="toggleDefault(this)">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="card-body" style="padding-top:6px">

                        {{-- Simpanan Pokok --}}
                        <div class="simpanan-item">
                            <div class="simpanan-item-top">
                                <div class="simpanan-item-label">
                                    <strong>Simpanan Pokok</strong>
                                    <span>Dibayar satu kali saat menjadi anggota koperasi.</span>
                                </div>
                                <div class="form-group">
                                    <div class="input-prefix-wrap">
                                        <span class="input-prefix">Rp</span>
                                        <input id="simpanan_pokok" name="simpanan_pokok" type="number"
                                               class="form-control @error('simpanan_pokok') is-invalid @enderror"
                                               placeholder="0" min="0"
                                               value="{{ old('simpanan_pokok', 0) }}"
                                               oninput="updateSummary()">
                                    </div>
                                    @error('simpanan_pokok')<span class="field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Simpanan Wajib --}}
                        <div class="simpanan-item">
                            <div class="simpanan-item-top">
                                <div class="simpanan-item-label">
                                    <strong>Simpanan Wajib</strong>
                                    <span>Nominal simpanan rutin setiap bulan.</span>
                                </div>
                                <div class="form-group">
                                    <div class="simpanan-row">
                                        <div class="input-prefix-wrap">
                                            <span class="input-prefix">Rp</span>
                                            <input id="simpanan_wajib" name="simpanan_wajib" type="number"
                                                   class="form-control @error('simpanan_wajib') is-invalid @enderror"
                                                   placeholder="0" min="0"
                                                   value="{{ old('simpanan_wajib', 0) }}"
                                                   oninput="updateSummary()">
                                        </div>
                                        <select name="jatuh_tempo" class="form-control">
                                            <option value="">Pilih Jatuh Tempo</option>
                                            @for($i = 1; $i <= 28; $i++)
                                                <option value="{{ $i }}" {{ old('jatuh_tempo') == $i ? 'selected' : '' }}>
                                                    Tgl {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    @error('simpanan_wajib')<span class="field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="auto-badge">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                    <circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/>
                                    <path d="M4.5 7.5l2 2.5 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Auto-generate tagihan bulanan secara otomatis
                            </div>
                        </div>

                        {{-- Simpanan Sukarela --}}
                        <div class="simpanan-item">
                            <div class="simpanan-item-top">
                                <div class="simpanan-item-label">
                                    <strong>Simpanan Sukarela</strong>
                                    <span>Setoran awal opsional (dapat diisi nanti).</span>
                                </div>
                                <div class="form-group">
                                    <div class="input-prefix-wrap">
                                        <span class="input-prefix">Rp</span>
                                        <input id="simpanan_sukarela" name="simpanan_sukarela" type="number"
                                               class="form-control @error('simpanan_sukarela') is-invalid @enderror"
                                               placeholder="0 (Opsional)" min="0"
                                               value="{{ old('simpanan_sukarela', 0) }}"
                                               oninput="updateSummary()">
                                    </div>
                                    @error('simpanan_sukarela')<span class="field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="info-note" style="margin-top:10px">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <circle cx="7" cy="7" r="6" stroke="#1e40af" stroke-width="1.2"/>
                                    <path d="M7 6v4M7 4.5h.01" stroke="#1e40af" stroke-width="1.3" stroke-linecap="round"/>
                                </svg>
                                Saldo awal ini akan langsung masuk ke buku tabungan anggota.
                            </div>
                        </div>

                    </div>
                </div>{{-- /card simpanan --}}

            </div>{{-- /kolom kiri --}}

            {{-- ════════════════════════════════════
                 KOLOM KANAN — ringkasan + bantuan
                 ════════════════════════════════════ --}}
            <div>
                {{-- Summary card --}}
                <div class="summary-card">
                    <div class="summary-header">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                            <rect x="1.5" y="3" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M4 7h9M4 10h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                        </svg>
                        Ringkasan Pendaftaran
                    </div>
                    <div class="summary-body">
                        <div class="summary-row">
                            <span class="summary-row-label">Simpanan Pokok</span>
                            <span class="summary-row-val" id="sum-pokok">Rp 0</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-row-label">Simpanan Wajib</span>
                            <div style="text-align:right">
                                <div class="summary-row-val" id="sum-wajib">Rp 0</div>
                                <div class="summary-row-sub">/ bln</div>
                            </div>
                        </div>
                        <div class="summary-row">
                            <span class="summary-row-label">Simpanan Sukarela</span>
                            <span class="summary-row-val" id="sum-sukarela">Rp 0</span>
                        </div>

                        <div class="summary-total">
                            <div class="summary-total-label">Total Setoran Awal</div>
                            <div class="summary-total-amount">
                                <span id="sum-total">Rp 0</span>
                                <div class="summary-total-icon">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <rect x="1" y="4" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.4"/>
                                        <circle cx="8" cy="8.5" r="1.8" stroke="currentColor" stroke-width="1.2"/>
                                        <path d="M4 4V3a2 2 0 0 1 8 0v1" stroke="currentColor" stroke-width="1.3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <button type="submit" form="form-anggota" class="btn-confirm">
                            Konfirmasi Data
                        </button>
                        <p class="summary-confirm-note">
                            Pastikan NIK dan data pribadi telah sesuai dengan KTP anggota yang bersangkutan.
                        </p>
                    </div>
                </div>

                {{-- Help card --}}
                <div class="help-card">
                    <div class="help-card-title">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M4 6a4 4 0 0 1 8 0c0 2-2 3-2 5H6c0-2-2-3-2-5z" stroke="white" stroke-width="1.4"/>
                            <path d="M6 14h4" stroke="white" stroke-width="1.4" stroke-linecap="round"/>
                        </svg>
                        Butuh bantuan?
                    </div>
                    <p class="help-card-desc">
                        Hubungi admin sistem jika Anda mengalami kendala pada saat penginputan data anggota baru.
                    </p>
                    <button type="button" class="btn-help">Lihat Panduan</button>
                </div>

            </div>{{-- /kolom kanan --}}

        </div>{{-- /create-grid --}}
    </form>

    {{-- Footer --}}
    <div class="create-footer" style="margin-top:28px;border-radius:0 0 12px 12px">
        <span>&copy; {{ date('Y') }} Koperasi Modern &mdash; Sistem Manajemen Terpadu.</span>
        <div class="create-footer-links">
            <a href="#">Syarat &amp; Ketentuan</a>
            <a href="#">Pusat Bantuan</a>
            <a href="#">Status Server</a>
        </div>
    </div>

</div>{{-- /create-body --}}

@endsection

@push('scripts')
<script>
/* Hapus subbar bawaan karena halaman ini punya header sendiri */
const subbar = document.getElementById('subbar');
if (subbar) subbar.style.display = 'none';

/* Format angka ke Rupiah singkat */
function formatRp(val) {
    const n = parseInt(val) || 0;
    return 'Rp ' + n.toLocaleString('id-ID');
}

/* Update ringkasan saat input berubah */
function updateSummary() {
    const pokok    = parseInt(document.getElementById('simpanan_pokok').value)    || 0;
    const wajib    = parseInt(document.getElementById('simpanan_wajib').value)    || 0;
    const sukarela = parseInt(document.getElementById('simpanan_sukarela').value) || 0;
    const total    = pokok + wajib + sukarela;

    document.getElementById('sum-pokok').textContent    = formatRp(pokok);
    document.getElementById('sum-wajib').textContent    = formatRp(wajib);
    document.getElementById('sum-sukarela').textContent = formatRp(sukarela);
    document.getElementById('sum-total').textContent    = formatRp(total);
}

/* Toggle default koperasi — lock/unlock input */
function toggleDefault(cb) {
    const locked = cb.checked;
    ['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela'].forEach(id => {
        const el = document.getElementById(id);
        if (locked) {
            el.setAttribute('readonly', true);
            el.style.background = '#F9FAFB';
            el.style.color = '#9CA3AF';
        } else {
            el.removeAttribute('readonly');
            el.style.background = '';
            el.style.color = '';
        }
    });
}

/* NIK — hanya angka, max 16 */
document.getElementById('nik')?.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 16);
});

/* Init */
document.addEventListener('DOMContentLoaded', () => {
    toggleDefault(document.getElementById('defaultToggle'));
    updateSummary();
});
</script>
@endpush