{{-- resources/views/anggota/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profil Anggota')

@section('topbar-nav')
    <a href="{{ route('anggota.index') }}" class="tb-link active">Karyawan</a>
    <a href="{{ route('departemen.index') }}" class="tb-link">Departemen</a>
    <a href="{{ route('learning.index') }}" class="tb-link">Learning</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('konfigurasi.index') }}" class="tb-link">Konfigurasi</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('anggota.show', $anggota->id) }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;margin-right:4px;">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Batal & Kembali
    </a>
@endsection

@section('page-title', 'Edit Profil Karyawan')

@section('content')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 30px;
        max-width: 700px;
        margin: 0 auto;
    }
    .form-section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-1);
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 10px;
    }
    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 16px;
    }
    .form-group {
        flex: 1;
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
    textarea.form-control {
        height: auto;
        min-height: 80px;
        padding-top: 10px;
        padding-bottom: 10px;
        resize: vertical;
    }
    .submit-container {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
    }
    .btn-submit {
        height: 40px;
        padding: 0 20px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
    }
    .btn-submit:hover { opacity: 0.9; }
</style>

<div class="form-card">
    <div class="form-section-title">Informasi Pribadi & Pekerjaan</div>
    
    <form action="{{ route('anggota.update', $anggota->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">NIK OTTO</label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik', $anggota->nik) }}" required>
                @error('nik') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">KTP / No. Identitas</label>
                <input type="text" name="no_ktp" class="form-control" value="{{ old('no_ktp', $anggota->no_ktp) }}">
                @error('no_ktp') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Departemen</label>
                <select name="department_id" class="form-control" required>
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemen as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $anggota->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->nama }}
                        </option>
                    @endforeach
                </select>
                @error('department_id') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Bagian / Unit</label>
                <input type="text" name="bagian" class="form-control" value="{{ old('bagian', $anggota->bagian) }}">
                @error('bagian') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jabatan</label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $anggota->jabatan) }}">
                @error('jabatan') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Tgl Masuk</label>
                <input type="date" name="tgl_msk" class="form-control" value="{{ old('tgl_msk', $anggota->tgl_msk ? \Carbon\Carbon::parse($anggota->tgl_msk)->format('Y-m-d') : '') }}">
                @error('tgl_msk') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $anggota->no_hp) }}">
                @error('no_hp') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Jml Tanggungan (Orang)</label>
                <input type="number" name="tanggungan" min="0" class="form-control" value="{{ old('tanggungan', $anggota->tanggungan) }}">
                @error('tanggungan') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Status Anggota</label>
            <select name="status_anggota" class="form-control">
                <option value="active" {{ old('status_anggota', $anggota->status_anggota) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="nonactive" {{ old('status_anggota', $anggota->status_anggota) === 'nonactive' ? 'selected' : '' }}>Non-Active</option>
            </select>
            @error('status_anggota') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control">{{ old('alamat', $anggota->alamat) }}</textarea>
            @error('alamat') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="submit-container">
            <button type="submit" class="btn-submit">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;"><path d="M5 13l4 4L19 7"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
