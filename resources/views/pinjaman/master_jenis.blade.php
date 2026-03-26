{{-- resources/views/pinjaman/master_jenis.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Jenis Pinjaman')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link">Pengajuan Pinjaman</a>
    <a href="{{ route('pinjaman.approval') }}" class="tb-link">Approval Pinjaman</a>
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Pembayaran Angsuran</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link active">Master Jenis Pinjaman</a>
@endsection

@section('page-title', 'Master Jenis Pinjaman')
@section('page-subtitle', 'Kelola kategori induk dan sub-jenis pinjaman beserta aturan bunga dan limit')

@section('content')
<style>
.mj-container {
    font-family: system-ui, sans-serif;
    font-size: 13px;
    color: #111827;
}

.grid-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 16px;
    align-items: start;
}

.card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    overflow: hidden;
}
.c-head {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px 9px;
    border-bottom: 1px solid #F3F4F6;
    font-size: 13px;
    font-weight: 700;
    color: #111827;
}
.c-head svg { color: #1a56db; flex-shrink: 0; }
.c-body { padding: 16px; }

/* form styling */
.fg {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 12px;
}
.lbl { font-size: 11px; font-weight: 600; color: #374151; }
.fc {
    display: block;
    width: 100%;
    height: 35px;
    padding: 0 10px;
    font-size: 12px;
    color: #111827;
    background: #fff;
    border: 1.5px solid #D1D5DB;
    border-radius: 7px;
    outline: none;
}
.fc:focus { border-color: #1a56db; box-shadow: 0 0 0 3px rgba(26,86,219,.09); }
textarea.fc { height: 60px; padding: 8px 10px; resize: none; }
select.fc {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath d='M2 3.5l3 3 3-3' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}

.btn-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    gap: 5px;
    padding: 9px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    background: #1a56db;
    border: none;
    border-radius: 7px;
    cursor: pointer;
}
.btn-save:hover { background: #1447c0; }

/* tree styling */
.tree-tbl { width: 100%; border-collapse: collapse; }
.tree-tbl th {
    padding: 10px 14px;
    font-size: 11px;
    font-weight: 600;
    color: #6B7280;
    text-align: left;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
}
.tree-tbl td {
    padding: 12px 14px;
    border-bottom: 1px solid #F3F4F6;
    vertical-align: middle;
}
.row-parent td { background: #fdfdfd; font-weight: 600; }
.row-child td { background: #fff; }
.ind { display: inline-block; width: 20px; }
.child-icon {
    display: inline-flex;
    color: #9CA3AF;
    margin-right: 6px;
    vertical-align: middle;
}

.badge-info {
    padding: 2px 8px;
    background: #EFF6FF;
    color: #1d4ed8;
    border-radius: 12px;
    font-size: 10px;
}
.badge-success {
    padding: 2px 8px;
    background: #F0FDF4;
    color: #15803d;
    border-radius: 12px;
    font-size: 10px;
}
</style>

<div class="mj-container">

    @if(session('success'))
        <div style="padding: 10px 14px; background: #DEF7EC; color: #03543F; border-radius: 6px; margin-bottom: 16px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid-layout">
        <!-- FORM COL -->
        <div class="card">
            <div class="c-head">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Tambah / Edit Kategori
            </div>
            <div class="c-body">
                <form action="{{ route('pinjaman.masterJenis.store') }}" method="POST">
                    @csrf
                    
                    <div class="fg">
                        <label class="lbl">Tipe Kategori</label>
                        <select class="fc" id="tipe_kategori" onchange="toggleForm()">
                            <option value="parent">Induk Kategori</option>
                            <option value="child">Sub Kategori (Anak)</option>
                        </select>
                    </div>

                    <div class="fg" id="wrap_parent" style="display: none;">
                        <label class="lbl">Pilih Induk Kategori</label>
                        <select class="fc" name="parent_id">
                            <option value="">-- Pilih Induk --</option>
                            @foreach($jenis_pinjaman as $jp)
                                <option value="{{ $jp->id }}">{{ $jp->nama_pinjaman }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fg">
                        <label class="lbl">Nama Pinjaman / Kategori <span style="color:#dc2626">*</span></label>
                        <input type="text" class="fc" name="nama_pinjaman" required placeholder="Mis. Pinjaman Barang">
                    </div>

                    <div class="fg" id="wrap_limit">
                        <label class="lbl">Limit Maksimal (Rp) <span style="font-weight:400;color:#9ca3af">— khusus Induk</span></label>
                        <input type="number" class="fc" name="limit_maksimal" placeholder="Contoh: 20000000">
                    </div>

                    <div class="fg" id="wrap_bunga" style="display: none;">
                        <label class="lbl">Bunga (% / bulan) <span style="font-weight:400;color:#9ca3af">— khusus Anak</span></label>
                        <input type="number" step="0.01" class="fc" name="bunga" placeholder="Contoh: 1.5">
                    </div>

                    <div class="fg">
                        <label class="lbl">Keterangan Tambahan</label>
                        <textarea class="fc" name="keterangan" placeholder="Keterangan opsional..."></textarea>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-save">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE COL -->
        <div class="card">
            <div class="c-head">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2 7h12M6 3v10" stroke="currentColor" stroke-width="1.2"/></svg>
                Struktur Jenis Pinjaman
            </div>
            
            <table class="tree-tbl">
                <thead>
                    <tr>
                        <th>Kategori / Nama Pinjaman</th>
                        <th style="text-align: right">Limit (Max)</th>
                        <th style="text-align: right">Bunga (%)</th>
                        <th style="text-align: center">Level</th>
                        <th style="text-align: right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenis_pinjaman as $parent)
                        <tr class="row-parent">
                            <td>
                                <strong style="color: #1f2937;">{{ $parent->nama_pinjaman }}</strong><br>
                                <span style="font-size: 10px; color: #9CA3AF; font-weight: 400;">{{ $parent->keterangan ?? 'Induk Kategori' }}</span>
                            </td>
                            <td style="text-align: right; color: #047857;">{{ $parent->limit_maksimal ? 'Rp '.number_format($parent->limit_maksimal,0,',','.') : '-' }}</td>
                            <td style="text-align: right; color: #9CA3AF;">-</td>
                            <td style="text-align: center"><span class="badge-info">Induk</span></td>
                            <td style="text-align: right">
                                <button type="button" style="border:none;background:transparent;color:#1a56db;cursor:pointer;font-size:11px;font-weight:600;">Edit</button>
                            </td>
                        </tr>
                        @foreach($parent->children as $child)
                            <tr class="row-child">
                                <td>
                                    <span class="ind"></span>
                                    <svg class="child-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    <span style="color: #4B5563;">{{ $child->nama_pinjaman }}</span>
                                </td>
                                <td style="text-align: right; color: #9CA3AF;">-</td>
                                <td style="text-align: right; font-weight: 600; color: #b45309;">{{ $child->bunga ? $child->bunga.' %' : '-' }}</td>
                                <td style="text-align: center"><span class="badge-success">Sub</span></td>
                                <td style="text-align: right">
                                    <button type="button" style="border:none;background:transparent;color:#1a56db;cursor:pointer;font-size:11px;font-weight:600;">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #9CA3AF;">
                                Belum ada data jenis pinjaman. Tambahkan kategori baru di form sebelah kiri.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleForm() {
    const tipe = document.getElementById('tipe_kategori').value;
    const wrapParent = document.getElementById('wrap_parent');
    const wrapLimit  = document.getElementById('wrap_limit');
    const wrapBunga  = document.getElementById('wrap_bunga');

    if (tipe === 'child') {
        wrapParent.style.display = 'flex';
        wrapBunga.style.display  = 'flex';
        wrapLimit.style.display  = 'none';
        
        // Atur required constraints
        document.querySelector('select[name="parent_id"]').required = true;
        document.querySelector('input[name="limit_maksimal"]').value = '';
    } else {
        wrapParent.style.display = 'none';
        wrapBunga.style.display  = 'none';
        wrapLimit.style.display  = 'flex';

        // Atur required constraints
        document.querySelector('select[name="parent_id"]').required = false;
        document.querySelector('select[name="parent_id"]').value = '';
        document.querySelector('input[name="bunga"]').value = '';
    }
}

// Inisialisasi awal
toggleForm();
</script>
@endsection
