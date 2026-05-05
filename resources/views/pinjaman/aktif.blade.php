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
    </style>

    {{-- Filter Bar --}}
    <form class="filter-card flex items-end gap-4" method="GET" action="{{ route('pinjaman.aktif') }}" style="margin-bottom: 24px;">
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
            <label class="label-text">Jenis Pinjaman</label>
            <select name="jenis" class="form-select">
                <option value="">Semua Jenis</option>
                @foreach($jenisPinjamanList as $jp)
                    @if($jp->children->count() > 0)
                        <optgroup label="{{ $jp->nama_pinjaman }}">
                            @foreach($jp->children as $child)
                                <option value="{{ $child->id }}" {{ request('jenis') == $child->id ? 'selected' : '' }}>{{ $child->nama_pinjaman }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $jp->id }}" {{ request('jenis') == $jp->id ? 'selected' : '' }}>{{ $jp->nama_pinjaman }}</option>
                    @endif
                @endforeach
            </select>
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
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR PINJAMAN</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 min-w-max">
                <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">PERIODE</th>
                        <th class="px-6 py-4">NAMA ANGGOTA</th>
                        <th class="px-6 py-4">JENIS</th>
                        <th class="px-6 py-4">PINJAMAN (POKOK / TOTAL)</th>
                        <th class="px-6 py-4">TENOR (TOTAL / SISA)</th>
                        <th class="px-6 py-4">TERBAYAR</th>
                        <th class="px-6 py-4">SISA TAGIHAN</th>
                        <th class="px-6 py-4">PEMBAYARAN</th>
                        <th class="px-6 py-4">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pinjaman_list as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-700">
                                <div>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-gray-400 font-normal">s/d {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs" style="flex-shrink:0;">
                                        {{ strtoupper(substr($item->anggota->nama_anggota ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $item->anggota->nama_anggota ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $item->anggota->nik ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700">
                                {{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500 mt-0.5" title="Bunga {{ $item->bunga }}%">Total: Rp {{ number_format($item->total_pinjaman, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $item->tenor }} Bulan</div>
                                <div class="text-xs text-gray-500 mt-0.5">Sisa: {{ $item->sisa_tenor }} Bulan</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600">
                                Rp {{ number_format($item->total_terbayar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status == 'lunas')
                                    <div class="font-bold text-indigo-600">Rp 0</div>
                                @else
                                    <div class="font-bold text-amber-600">Rp {{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->payment_method == 'gaji')
                                    <span class="badge">Gaji</span>
                                @else
                                    <span class="badge">Mandiri</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge {{ strtolower($item->status) === 'berjalan' ? 'badge-berjalan' : 'badge-lunas' }}">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pinjaman.aktif.show', $item->id) }}" title="Detail Pinjaman" style="color:#6366f1; font-weight:600; font-size:13px; text-decoration:none;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
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
