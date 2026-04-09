{{-- resources/views/Simpanan/tambah_saldo.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Saldo Simpanan (Sukarela)')

@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link">Simpanan Anggota</a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link active">Transaksi</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('simpanan.transaksi') }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;margin-right:4px;">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Batal & Kembali
    </a>
@endsection

@section('page-title', 'Tambah Saldo (Simpanan Langsung)')

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
    .btn-submit {
        display: flex;
        justify-content: center;
        width: 100%;
        height: 44px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-submit:hover { opacity: 0.9; }

    /* Select2 customizations to match theme */
    .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid var(--border-md);
        border-radius: var(--radius-md);
        background: var(--bg);
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-1);
        font-size: 14px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    .select2-dropdown {
        border: 1px solid var(--border-md);
        border-radius: var(--radius-md);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>

<div class="form-card">
    <form action="{{ route('simpanan.tambah_saldo.store') }}" method="POST">
        @csrf
        @if(request('anggota_id'))
            <input type="hidden" name="redirect_to_show" value="1">
        @endif

        <div class="form-group">
            <label class="form-label">Anggota</label>
            <select name="anggota_id" id="anggota_id" class="form-control select2" required {{ $selectedAnggotaId ? 'readonly' : '' }}>
                <option value="">-- Pilih Anggota --</option>
                @foreach($anggotas as $anggota)
                    <option value="{{ $anggota->id }}" {{ $selectedAnggotaId == $anggota->id ? 'selected' : '' }}>
                        {{ $anggota->nik }} - {{ $anggota->nama_anggota }}
                    </option>
                @endforeach
            </select>
            @error('anggota_id') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Nominal Tambah Saldo (Rp)</label>
            <input type="number" name="nominal" class="form-control" placeholder="Contoh: 500000" min="1" value="{{ old('nominal') }}" required>
            <p style="font-size: 11px; color: var(--text-3); margin-top: 4px;">*Akan dicatat sebagai Simpanan Sukarela</p>
            @error('nominal') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Tanggal Transaksi</label>
            <input type="date" name="tanggal_transaksi" class="form-control" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required>
            @error('tanggal_transaksi') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Keterangan / Deskripsi</label>
            <input type="text" name="description" class="form-control" value="{{ old('description', 'Simpanan Langsung Sukarela') }}" required>
            @error('description') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;"><path d="M12 5v14M5 12h14"></path></svg>
                Proses Tambah Saldo
            </button>
        </div>
    </form>
</div>

<!-- jQuery (Required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih Anggota --",
            allowClear: true,
            width: '100%'
        });

        @if($selectedAnggotaId)
            // Jika memilih dari show, nonaktifkan interaksi select2 supaya fix 1 orang
            $('#anggota_id').select2('enable', false);
            // Tambahkan input hidden karena disabled tidak mengirim data
            $('<input>').attr({
                type: 'hidden',
                name: 'anggota_id',
                value: '{{ $selectedAnggotaId }}'
            }).appendTo('form');
        @endif
    });
</script>
@endsection
