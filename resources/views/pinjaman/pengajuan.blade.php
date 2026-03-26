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
                            <div class="td-name" style="font-weight: 500; color: #111;">{{ $item->nama }}</div>
                            <div style="font-size: 12px; color: var(--text-3);">{{ $item->nik }}</div>
                        </td>
                        <td style="color: var(--text-2);">{{ $item->tanggal }}</td>
                        <td>{{ $item->jenis_pinjaman }}</td>
                        <td style="font-weight:600; font-family: monospace; font-size:14px;">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $item->tenor }} Bulan</td>
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
@endsection
