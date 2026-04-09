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
@section('sidebar')
    <div class="sd-section">
        <div class="sd-heading" style="margin-bottom: 12px; font-weight: 600; font-size: 13px; color: #4B5563;">
            <div style="display:flex;align-items:center;gap:5px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                JENIS PINJAMAN
            </div>
        </div>

        <a href="{{ route('pinjaman.aktif') }}" class="sd-link {{ !request('jenis') ? 'active' : '' }}" style="width: 100%; display: block; border-radius: 6px; padding: 8px 12px; margin-bottom: 4px;">
            <span style="font-weight: 600;">Semua Jenis Pinjaman</span>
        </a>

        @foreach($jenisPinjamanList as $jpParent)
            <div x-data="{ expanded: true }" style="margin-bottom: 4px;">
                @if($jpParent->children->count() > 0)
                    <button @click="expanded = !expanded" class="sd-link" style="width: 100%; display: flex; justify-content: space-between; align-items: center; border-radius: 6px; padding: 8px 12px; background: transparent;">
                        <span style="font-weight: 600;">{{ $jpParent->nama_pinjaman }}</span>
                        <svg :class="expanded ? 'transform rotate-180' : ''" width="12" height="12" viewBox="0 0 24 24" fill="none" class="transition-transform duration-200">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div x-show="expanded" x-collapse style="margin-left: 12px; margin-top: 4px; display: flex; flex-direction: column; gap: 2px;">
                        @foreach($jpParent->children as $jpChild)
                            <a href="{{ route('pinjaman.aktif', ['jenis' => $jpChild->id, 'status' => request('status'), 'q' => request('q')]) }}"
                               class="sd-link {{ request('jenis') == $jpChild->id ? 'active' : '' }}"
                               style="padding: 6px 12px; font-size: 13px;">
                                {{ $jpChild->nama_pinjaman }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ route('pinjaman.aktif', ['jenis' => $jpParent->id, 'status' => request('status'), 'q' => request('q')]) }}"
                       class="sd-link {{ request('jenis') == $jpParent->id ? 'active' : '' }}"
                       style="padding: 6px 12px; font-size: 14px; font-weight: 600;">
                        {{ $jpParent->nama_pinjaman }}
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@endsection

@section('content')
<div class="px-6 py-4 space-y-6">

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: 2fr 2fr;
            gap: 20px;
        }
        .stat-card-dark {
            background: #0B1727;
            border-radius: 12px;
            padding: 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .stat-card-light {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
        }
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
            border: 1px solid #efefef;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            align-items: center;
            gap: 16px;
        }
        .fc {
            background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:8px 12px; font-size:13px; color:#374151; width:100%; transition: border-color 0.2s; outline:none; height:36px;
        }
        .fc:focus { border-color:#6366f1; }
        .btn-search {
            background:#0B1727; color:#fff; font-size:13px; font-weight:600; padding:0 16px; border-radius:6px; height:36px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background 0.2s;
        }
        .btn-search:hover { background:#1f2937; }
    </style>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card-dark">
            <div class="text-gray-400 text-xs font-bold tracking-wider mb-2">SISA DANA PINJAMAN BERJALAN</div>
            <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($totalPinjamanBerjalan, 0, ',', '.') }}</div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/10 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card-light">
            <div class="flex items-center gap-2 mb-2">
                <div class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded leading-tight">JUMLAH PINJAMAN BERJALAN</div>
            </div>
            <div class="text-4xl font-bold text-indigo-600 mb-2">{{ number_format($countBerjalan, 0, ',', '.') }}</div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-indigo-50 text-indigo-600 p-3 rounded-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form action="{{ route('pinjaman.aktif') }}" method="GET" class="filter-card">
        @if(request('jenis')) <input type="hidden" name="jenis" value="{{ request('jenis') }}"> @endif
        <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;">
            <div style="flex:1; min-width:200px;">
                <label style="font-size:12px; font-weight:600; color:#4B5563; margin-bottom:6px; display:block;">Pencarian Anggota</label>
                <div style="position:relative;">
                    <svg style="position:absolute; left:12px; top:10px; color:#9CA3AF;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="q" value="{{ request('q') }}" class="fc" style="padding-left:36px;" placeholder="Cari NIK atau Nama...">
                </div>
            </div>
            <div style="width:180px;">
                <label style="font-size:12px; font-weight:600; color:#4B5563; margin-bottom:6px; display:block;">Status</label>
                <select name="status" class="fc">
                    <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="berjalan" {{ request('status', 'berjalan') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn-search">
                    Terapkan Filter
                </button>
                @if(request('q') || request('status') || request('jenis'))
                    <a href="{{ route('pinjaman.aktif') }}" style="display:flex; align-items:center; justify-content:center; padding:0 12px; color:#6B7280; font-size:13px; font-weight:500; text-decoration:none; border-radius:6px; border:1px solid #E5E7EB; background:#fff; height:36px;">Reset</a>
                @endif
            </div>
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
