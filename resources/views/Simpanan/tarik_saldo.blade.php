{{-- resources/views/Simpanan/tarik_saldo.blade.php --}}
@extends('layouts.app')

@section('title', 'Tarik Simpanan')

@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link active">Simpanan Anggota</a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link">Transaksi</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('simpanan.show', $master->id) }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;margin-right:4px;">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Batal & Kembali
    </a>
@endsection

@section('page-title', 'Pengajuan Penarikan Simpanan')

@section('content')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-2);
        margin-bottom: 8px;
    }
    .form-control {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        font-size: 14px;
        color: var(--text-1);
        background: var(--bg);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-md);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(92, 79, 138, 0.12);
        background: var(--surface);
    }
    .form-control[readonly] {
        background: var(--bg);
        color: var(--text-2);
        cursor: not-allowed;
    }
    textarea.form-control {
        height: auto;
        min-height: 100px;
        padding-top: 10px;
        resize: vertical;
    }
    .btn-submit {
        display: flex;
        justify-content: center;
        width: 100%;
        height: 44px;
        background: var(--danger, #E02424);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-submit:hover { opacity: 0.9; }
    
    .info-box {
        background: #FDFDEA;
        border: 1px solid #FDF6B2;
        padding: 16px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .info-box.danger {
        background: #FDF2F2;
        border-color: #FBD5D5;
        color: #9B1C1C;
    }
</style>

<div class="form-card">
    <div class="info-box">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D03801" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div>
            <h4 style="margin: 0 0 4px 0; font-size: 14px; color: #D03801;">Informasi Saldo</h4>
            <p style="margin: 0; font-size: 13px; color: #8A2C0D; line-height: 1.4;">
                Total gabungan Simpanan <strong>{{ $anggota->nama_anggota }}</strong> adalah sebesar <strong>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</strong>.<br>
                Penarikan tidak boleh melebihi total tersebut.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="info-box danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div style="font-size:13px; font-weight:500;">{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('simpanan.tarik.store') }}" method="POST">
        @csrf
        <input type="hidden" name="anggota_id" value="{{ $anggota->id }}">

        <div class="form-group">
            <label class="form-label">Anggota</label>
            <input type="text" class="form-control" value="{{ $anggota->nik }} - {{ $anggota->nama_anggota }}" readonly>
        </div>

        <div class="form-group">
            <label class="form-label">Nominal Penarikan (Rp)</label>
            <input type="number" name="nominal" class="form-control" placeholder="Contoh: 1000000" min="1" max="{{ $totalKeseluruhan }}" value="{{ old('nominal') }}" required>
            @error('nominal') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Alasan / Tujuan Pengajuan</label>
            <textarea name="alasan_pengajuan" class="form-control" placeholder="Contoh: Kebutuhan mendesak biaya sekolah anak" required>{{ old('alasan_pengajuan') }}</textarea>
            @error('alasan_pengajuan') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Ajukan Penarikan
            </button>
        </div>
    </form>
</div>
@endsection
