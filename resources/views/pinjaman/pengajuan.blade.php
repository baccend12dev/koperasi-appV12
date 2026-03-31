{{-- resources/views/pinjaman/pengajuan.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengajuan Pinjaman')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link active">Pengajuan Pinjaman</a>
    <a href="{{ route('pinjaman.approval') }}" class="tb-link">Approval Pinjaman</a>
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Pembayaran Angsuran</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link">Master Jenis Pinjaman</a>
@endsection

{{-- ── Subbar kiri ── --}}
@section('subbar-actions')
    <a href="{{ route('pinjaman.pengajuan.create') }}" class="btn-primary">
        <svg fill="none" style="display:inline; margin-right:4px;" stroke="currentColor" stroke-width="2" width="14" height="14" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Pengajuan
    </a>
@endsection

@section('page-title', 'Pengajuan Pinjaman')

@section('content')
    <style>
        .filter-card {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        .form-select, .form-input {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 14px;
        }
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #714B67;
            box-shadow: 0 0 0 3px rgba(113, 75, 103, 0.1);
        }
        .label-text {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            display: block;
        }
        .btn-search {
            background: #EFF6FF;
            color: #1D4ED8;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.2s;
            height: 38px;
        }
        .btn-search:hover {
            background: #DBEAFE;
        }
        .btn-export {
            background: #ECFDF5;
            color: #059669;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            height: 38px;
        }
        .btn-export:hover {
            background: #D1FAE5;
        }
    </style>

    <div class="px-6 py-4 space-y-6">
    <form class="filter-card flex items-end gap-4" method="GET" action="{{ route('pinjaman.pengajuan') }}" style="margin-bottom: 24px;">
        <div class="flex-1">
            <label class="label-text">Tahun</label>
            <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="label-text">Bulan</label>
            <select name="bulan" class="form-select">
                <option value="">Semua Bulan</option>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="flex-2" style="flex: 2;">
            <label class="label-text">Nama / NIK Anggota</label>
            <div class="relative">
                
                <input type="text" name="q" value="{{ request('q') }}" class="form-input pl-9" placeholder="Cari anggota...">
            </div>
        </div>
        <div class="flex-1">
            <label class="label-text">Jenis Pinjaman</label>
            <select name="jenis" class="form-select">
                <option value="">Semua Jenis</option>
                @foreach($jenisPinjamanList as $jp)
                    <option value="{{ $jp->id }}" {{ request('jenis') == $jp->id ? 'selected' : '' }}>
                        {{ $jp->nama_pinjaman }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-search">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                Cari Data
            </button>
            <a href="{{ route('pinjaman.pengajuan', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-export">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </form>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="td-check">
                        <input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.row-check').forEach(c=>c.checked=this.checked)">
                    </th>
                    <th>Nama Anggota</th>
                    <th>Tanggal</th>
                    <th>Jenis Pinjaman</th>
                    <th>Jumlah</th>
                    <th>Tenor</th>
                    <th>Total Bunga</th>
                    <th>Total Pinjaman</th>
                    <th>Cicilan per Bulan</th>
                    <th>Status</th>
                    <th style="min-width: 140px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan_list as $item)
                    <tr>
                        <td class="td-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="row-check" value="{{ $item->id }}">
                        </td>
                        <td>
                            <div class="td-name" style="font-weight: 500; color: #111;">{{ $item->anggota->nama_anggota }}</div>
                            <div style="font-size: 12px; color: var(--text-3);">{{ $item->anggota->nik }}</div>
                        </td>
                        <td style="color: var(--text-2);">{{ $item->created_at->format('d M Y') }}</td>
                        <td>{{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}</td>
                        <td style="font-weight:600; font-family: monospace; font-size:14px;">Rp {{ number_format($item->jumlah_pengajuan, 0, ',', '.') }}</td>
                        <td>{{ $item->tenor }} Bulan</td>
                        <td>Rp {{ number_format($item->total_bunga, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_pinjaman, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->cicilan_per_bulan, 0, ',', '.') }}</td>
                        <td>
                            @if(strtolower($item->status) == 'pending')
                                <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#fef7e0; color:#b08d00; font-weight:600;">Pending</span>
                            @elseif(strtolower($item->status) == 'approved')
                                <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#e6f4ea; color:#137333; font-weight:600;">Approved</span>
                            @else
                                <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#fce8e6; color:#c5221f; font-weight:600;">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" title="Detail">Detail</button>
                            @if(strtolower($item->status) == 'pending')
                                <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px; margin-left: 4px;" title="Edit">Edit</button>
                            @endif
                            <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px; margin-left: 4px; color: #d93025; border-color: #f1f3f4; background-color: #fdf2f2;" title="Hapus">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px 16px;color:var(--text-3)">
                            Belum ada data pengajuan pinjaman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>
@endsection
