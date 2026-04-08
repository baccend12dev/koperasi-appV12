{{-- resources/views/departemen/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Departemen')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('anggota.index') }}"
       class="tb-link {{ request()->routeIs('anggota.*') ? 'active' : '' }}">
        Karyawan
    </a>
    <a href="{{ route('departemen.index') }}"
       class="tb-link {{ request()->routeIs('departemen.*') ? 'active' : '' }}">
        Departemen
    </a>
    <a href="{{ route('learning.index') }}"
       class="tb-link {{ request()->routeIs('learning.*') ? 'active' : '' }}">
        Learning
    </a>
    <a href="{{ route('laporan.index') }}"
       class="tb-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        Laporan
    </a>
    <a href="{{ route('konfigurasi.index') }}"
       class="tb-link {{ request()->routeIs('konfigurasi.*') ? 'active' : '' }}">
        Konfigurasi
    </a>
@endsection

{{-- ── Subbar ── --}}
@section('subbar-actions')
    <button class="btn-primary" onclick="openModal('modal-tambah')">+ Departemen Baru</button>
@endsection

@section('page-title', 'Master Departemen')

@section('subbar-search')
    <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama atau kode..."
        autocomplete="off"
    >
@endsection

@section('subbar-pagination')
    <span class="pag-info">
        {{ $departemen->firstItem() }}–{{ $departemen->lastItem() }} / {{ $departemen->total() }}
    </span>
    <a href="{{ $departemen->previousPageUrl() ?? '#' }}"
       class="pag-btn" {{ $departemen->onFirstPage() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <a href="{{ $departemen->nextPageUrl() ?? '#' }}"
       class="pag-btn" {{ !$departemen->hasMorePages() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M1 1l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
@endsection

{{-- ── Konten utama ── --}}
@section('content')
<div class="data-table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>Nama Departemen</th>
                <th>Kode</th>
                <th>Deskripsi</th>
                <th>Jumlah Anggota</th>
                <th class="th-settings" style="width:80px;text-align:right;padding-right:16px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departemen as $dept)
                <tr>
                    <td style="color:var(--text-3);font-size:12px">{{ $loop->iteration + ($departemen->currentPage() - 1) * $departemen->perPage() }}</td>
                    <td style="font-weight:500">{{ $dept->nama }}</td>
                    <td>
                        @if($dept->kode)
                            <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:var(--accent-light);color:var(--accent)">{{ $dept->kode }}</span>
                        @else
                            <span style="color:var(--text-3)">—</span>
                        @endif
                    </td>
                    <td style="color:var(--text-2);max-width:300px">
                        {{ $dept->deskripsi ?? '—' }}
                    </td>
                    <td>
                        <a href="{{ route('anggota.index', ['dept' => $dept->id]) }}"
                           style="display:inline-flex;align-items:center;gap:5px;color:var(--accent);font-weight:600;font-size:13px;text-decoration:none"
                           title="Lihat anggota departemen ini">
                            {{ $dept->anggota_count }}
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M2 6h8M6 2l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </td>
                    <td style="text-align:right;padding-right:16px">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px">
                            {{-- Tombol Edit --}}
                            <button class="icon-btn"
                                    title="Edit"
                                    onclick="openEdit({{ $dept->id }}, '{{ addslashes($dept->nama) }}', '{{ addslashes($dept->kode ?? '') }}', '{{ addslashes($dept->deskripsi ?? '') }}')">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M10 2l2 2-7 7H3v-2l7-7z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            {{-- Tombol Hapus --}}
                            <form method="POST" action="{{ route('departemen.destroy', $dept) }}" onsubmit="return confirm('Hapus departemen {{ addslashes($dept->nama) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn icon-btn--danger" title="Hapus">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M2 4h10M5 4V2.5h4V4M5.5 6.5v4M8.5 6.5v4M3 4l.7 7.5h6.6L11 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px 16px;color:var(--text-3)">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" style="margin:0 auto 8px;display:block;opacity:.4">
                            <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        Belum ada departemen. Klik <strong>+ Departemen Baru</strong> untuk menambahkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════════════════
     MODAL — Tambah Departemen Baru
     ══════════════════════════════════════════ --}}
<div id="modal-tambah" class="modal-overlay" style="display:none" onclick="closeModalOutside(event,'modal-tambah')">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Tambah Departemen Baru</span>
            <button class="modal-close" onclick="closeModal('modal-tambah')" title="Tutup">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('departemen.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="nama-add">Nama Departemen <span style="color:#c5221f">*</span></label>
                    <input id="nama-add" type="text" name="nama" class="form-input" placeholder="cth: Human Resources" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="kode-add">Kode</label>
                    <input id="kode-add" type="text" name="kode" class="form-input" placeholder="cth: HR (opsional)" maxlength="20">
                </div>
                <div class="form-group">
                    <label class="form-label" for="deskripsi-add">Deskripsi</label>
                    <textarea id="deskripsi-add" name="deskripsi" class="form-input" rows="3" placeholder="Keterangan departemen (opsional)" style="resize:vertical"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-tambah')">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL — Edit Departemen
     ══════════════════════════════════════════ --}}
<div id="modal-edit" class="modal-overlay" style="display:none" onclick="closeModalOutside(event,'modal-edit')">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Edit Departemen</span>
            <button class="modal-close" onclick="closeModal('modal-edit')" title="Tutup">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <form id="form-edit" method="POST" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="nama-edit">Nama Departemen <span style="color:#c5221f">*</span></label>
                    <input id="nama-edit" type="text" name="nama" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="kode-edit">Kode</label>
                    <input id="kode-edit" type="text" name="kode" class="form-input" maxlength="20">
                </div>
                <div class="form-group">
                    <label class="form-label" for="deskripsi-edit">Deskripsi</label>
                    <textarea id="deskripsi-edit" name="deskripsi" class="form-input" rows="3" style="resize:vertical"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-edit')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── Icon action buttons ──────────────────────── */
.icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1px solid var(--border-md);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-2);
    cursor: pointer;
    transition: background .1s, color .1s, border-color .1s;
}
.icon-btn:hover { background: var(--bg); color: var(--text-1); }
.icon-btn--danger { color: var(--red-text); border-color: #f5c6c6; }
.icon-btn--danger:hover { background: var(--red-bg); }

/* ── Modal ────────────────────────────────────── */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(28,26,46,0.45);
    z-index: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    backdrop-filter: blur(2px);
    animation: fadeIn .15s ease;
}
@keyframes fadeIn { from { opacity:0 } to { opacity:1 } }

.modal-box {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: 0 8px 40px rgba(0,0,0,0.18);
    width: 100%;
    max-width: 440px;
    overflow: hidden;
    animation: slideUp .18s ease;
}
@keyframes slideUp { from { transform:translateY(12px);opacity:0 } to { transform:translateY(0);opacity:1 } }

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
}
.modal-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-1);
}
.modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border: none;
    background: transparent;
    color: var(--text-3);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: background .1s, color .1s;
}
.modal-close:hover { background: var(--bg); color: var(--text-1); }

.modal-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding: 14px 20px;
    border-top: 1px solid var(--border);
    background: var(--bg);
}

/* ── Form elements ─────────────────────────────── */
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    text-transform: uppercase;
    letter-spacing: .04em;
}
.form-input {
    width: 100%;
    padding: 8px 12px;
    font-size: 13px;
    font-family: var(--font);
    color: var(--text-1);
    background: var(--surface);
    border: 1px solid var(--border-md);
    border-radius: var(--radius-md);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(92,79,138,0.12);
}
.form-input::placeholder { color: var(--text-3); }
</style>
@endpush

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    // focus first input
    const first = document.getElementById(id).querySelector('input,textarea');
    if (first) setTimeout(() => first.focus(), 80);
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
function closeModalOutside(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}

function openEdit(id, nama, kode, deskripsi) {
    document.getElementById('nama-edit').value     = nama;
    document.getElementById('kode-edit').value     = kode;
    document.getElementById('deskripsi-edit').value = deskripsi;
    document.getElementById('form-edit').action    = '/departemen/' + id;
    openModal('modal-edit');
}

// ESC tutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-tambah','modal-edit'].forEach(closeModal);
    }
});

// Auto-open modal jika ada error validasi
@if($errors->any())
    openModal('modal-tambah');
@endif
</script>
@endpush
