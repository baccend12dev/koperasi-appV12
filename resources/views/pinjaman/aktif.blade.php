{{-- resources/views/pinjaman/aktif.blade.php --}}
@extends('layouts.app')

@section('title', 'Pinjaman Aktif')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('pinjaman.pengajuan') }}" class="tb-link">Pengajuan Pinjaman</a>
    <!-- <a href="{{ route('persetujuan.pinjaman') }}" class="tb-link">Approval Pinjaman</a> -->
    <a href="{{ route('pinjaman.aktif') }}" class="tb-link active">Pinjaman Aktif</a>
    <a href="{{ route('pinjaman.angsuran') }}" class="tb-link">Pembayaran Angsuran</a>
    <a href="{{ route('pinjaman.masterJenis') }}" class="tb-link">Master Jenis Pinjaman</a>
@endsection

@section('page-title', 'Pinjaman Aktif')
@section('page-subtitle', 'Daftar seluruh pinjaman anggota koperasi')

{{-- ── Sidebar ── --}}


@section('content')
<div class="px-6 py-4 space-y-6">

    <style>
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-berjalan { background: #ECFDF5; color: #059669; }
        .badge-lunas { background: #EEF2FF; color: #4F46E5; }
        
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

        /* Custom Compact & Bordered Table Styling */
        .table-compact {
            width: 100%;
            border-collapse: collapse !important;
            font-size: 14px;
            color: #1E293B;
            border: 1px solid #CBD5E1 !important;
        }
        .table-compact th,
        .table-compact td {
            padding: 9px 12px;
            border: 1px solid #CBD5E1 !important;
            vertical-align: middle;
        }
        .table-compact th {
            background-color: #F8FAFC;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border: 1px solid #CBD5E1 !important;
        }
        .table-compact tbody tr:hover {
            background-color: #F8FAFC;
        }
        .btn-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            background: #EFF6FF;
            color: #2563EB;
            font-weight: 600;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-detail:hover {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        /* Menu Tab Jenis Pinjaman */
        .jenis-tab-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .jenis-tab-bar::-webkit-scrollbar {
            display: none;
        }
        .jenis-tab-item {
            display: inline-flex;
            align-items: center;
            padding: 7px 16px;
            border-radius: 99px;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.15s ease;
        }
        .jenis-tab-item:hover {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #CBD5E1;
        }
        .jenis-tab-item.active {
            background: #1E3A5F;
            color: #FFFFFF;
            border-color: #1E3A5F;
            box-shadow: 0 2px 5px rgba(30, 58, 95, 0.2);
        }
    </style>

    {{-- Filter Bar --}}
    <form class="filter-card flex items-end gap-4" method="GET" action="{{ route('pinjaman.aktif') }}" style="margin-bottom: 24px;">
        <input type="hidden" name="jenis" value="{{ request('jenis') }}">
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
            <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Cari NIK atau Nama...">
        </div>
        <div class="flex-1">
            <label class="label-text">Status</label>
            <select name="status" class="form-select">
                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="berjalan" {{ request('status', 'berjalan') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-search">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                Cari Data
            </button>
            <a href="{{ route('pinjaman.aktif', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-export">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            @if(request('q') || request('status') || request('jenis') || request('tahun') || request('bulan'))
                <a href="{{ route('pinjaman.aktif') }}" style="display:flex; align-items:center; justify-content:center; padding:0 12px; color:#6B7280; font-size:13px; font-weight:500; text-decoration:none; border-radius:8px; border:1px solid #E5E7EB; background:#fff; height:38px;" title="Reset Filter">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                </a>
            @endif
        </div>
    </form>

    {{-- Main Table Section --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-3">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR PINJAMAN</h3>
            </div>

            {{-- Menu Tab Jenis Pinjaman --}}
            <div class="jenis-tab-bar">
                <a href="{{ route('pinjaman.aktif', request()->except('jenis', 'page')) }}"
                   class="jenis-tab-item {{ !request('jenis') ? 'active' : '' }}">
                    Semua Jenis
                </a>
                @foreach($jenisPinjamanList as $jp)
                    @if($jp->children->count() > 0)
                        @foreach($jp->children as $child)
                            <a href="{{ route('pinjaman.aktif', array_merge(request()->except('jenis', 'page'), ['jenis' => $child->id])) }}"
                               class="jenis-tab-item {{ request('jenis') == $child->id ? 'active' : '' }}">
                                {{ $child->nama_pinjaman }}
                            </a>
                        @endforeach
                    @else
                        <a href="{{ route('pinjaman.aktif', array_merge(request()->except('jenis', 'page'), ['jenis' => $jp->id])) }}"
                           class="jenis-tab-item {{ request('jenis') == $jp->id ? 'active' : '' }}">
                            {{ $jp->nama_pinjaman }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-compact min-w-max">
                <thead>
                    <tr>
                        <th>PERIODE</th>
                        <th>NAMA ANGGOTA</th>
                        <th>JENIS</th>
                        <th>PINJAMAN (POKOK / TOTAL)</th>
                        <th>TENOR</th>
                        <th>TERBAYAR</th>
                        <th>SISA TAGIHAN</th>
                        <th class="text-center">PEMBAYARAN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman_list as $item)
                        <tr>
                            <td class="font-medium text-gray-700">
                                <div class="font-semibold text-gray-800 text-xs">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}</div>
                                <div class="text-[11px] text-gray-400 font-normal">s/d {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-' }}</div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs" style="flex-shrink:0;">
                                        {{ strtoupper(substr($item->anggota->nama_anggota ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-xs">{{ $item->anggota->nama_anggota ?? 'Unknown' }}</div>
                                        <div class="text-[11px] text-gray-400">NIK: {{ $item->anggota->nik ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-medium text-gray-700 text-xs">
                                {{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}
                            </td>
                            <td>
                                <div class="font-bold text-gray-800 text-xs">Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</div>
                                <div class="text-[11px] text-gray-500" title="Bunga {{ $item->bunga }}%">Total: Rp {{ number_format($item->total_pinjaman, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div class="font-bold text-gray-800 text-xs">{{ $item->tenor }} Bulan</div>
                                <div class="text-[11px] text-gray-500">Sisa: {{ $item->sisa_tenor }} Bulan</div>
                            </td>
                            <td class="font-bold text-emerald-600 text-xs">
                                Rp {{ number_format($item->total_terbayar, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($item->status == 'lunas')
                                    <div class="font-bold text-indigo-600 text-xs">Rp 0</div>
                                @else
                                    <div class="font-bold text-amber-600 text-xs">Rp {{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->payment_method == 'gaji')
                                    <span class="badge">Payroll</span>
                                @else
                                    <span class="badge">Tunai</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ strtolower($item->status) === 'berjalan' ? 'badge-berjalan' : 'badge-lunas' }}">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pinjaman.aktif.show', $item->id) }}" class="btn-detail" title="Detail Pinjaman">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-gray-400">
                                Tidak ada data pinjaman yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
