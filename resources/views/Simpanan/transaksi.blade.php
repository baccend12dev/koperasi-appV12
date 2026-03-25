{{-- resources/views/simpanan/transaksi.blade.php --}}
@extends('layouts.app')

@section('title', 'Transaksi Simpanan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link">
        Simpanan Anggota
    </a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link active">
        Transaksi
    </a>
    <a href="{{ route('laporan.index') }}" class="tb-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        Laporan
    </a>
    <a href="{{ route('simpanan.tagihangenerator') }}" class="tb-link">
        Tagih Simpanan
    </a>
@endsection

@section('page-title', 'Riwayat Transaksi')

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
                PERIODE
            </div>
        </div>

        @foreach($years as $yr)
            <div x-data="{ expanded: {{ (request('tahun') == $yr || (!request('tahun') && date('Y') == $yr)) ? 'true' : 'false' }} }" style="margin-bottom: 4px;">
                <button @click="expanded = !expanded" class="sd-link" style="width: 100%; display: flex; justify-content: space-between; align-items: center; background: {{ (request('tahun') == $yr || (!request('tahun') && date('Y') == $yr)) ? '#f3f4f6' : 'transparent' }}; border-radius: 6px;">
                    <span style="font-weight: 600;">Tahun {{ $yr }}</span>
                    <svg :class="expanded ? 'transform rotate-180' : ''" width="12" height="12" viewBox="0 0 24 24" fill="none" class="transition-transform duration-200">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div x-show="expanded" x-collapse style="margin-left: 12px; margin-top: 4px; display: flex; flex-direction: column; gap: 2px;">
                    @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        // If it's the current year, only show up to current month generally, but let's just show all standard 12 months for simplicity, or we can check what exists.
                    @endphp
                    @foreach($months as $num => $name)
                        <a href="{{ route('simpanan.transaksi', array_merge(request()->query(), ['tahun' => $yr, 'bulan' => $num])) }}"
                           class="sd-link {{ request('tahun') == $yr && request('bulan') == $num ? 'active' : '' }}"
                           style="padding: 6px 12px; font-size: 13px;">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('content')
<div class="px-6 py-4 space-y-6">

    <style>
        .filter-card {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
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
            gap: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-search:hover {
            background: #DBEAFE;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-wajib { background: #ECFDF5; color: #059669; }
        .badge-sukarela { background: #EFF6FF; color: #2563EB; }
        .badge-pokok { background: #FFF7ED; color: #EA580C; }
    </style>

    {{-- Filter Bar --}}
    <form class="filter-card flex items-end gap-4" method="GET" action="{{ route('simpanan.transaksi') }}">
        <div class="flex-1">
            <label class="label-text">Tahun</label>
            <select name="tahun" class="form-select">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ (request('tahun') ?? date('Y')) == $yr ? 'selected' : '' }}>{{ $yr }}</option>
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
                <svg class="absolute left-3 top-2.5 text-gray-400" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" class="form-input pl-9" placeholder="Cari anggota...">
            </div>
        </div>
        <div class="flex-1">
            <label class="label-text">Jenis Simpanan</label>
            <select name="jenis" class="form-select">
                <option value="">Semua Jenis</option>
                @foreach($jenisSimpanan as $js)
                    <option value="{{ $js->id }}" {{ request('jenis') == $js->id ? 'selected' : '' }}>
                        {{ $js->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn-search">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                Cari Data
            </button>
        </div>
    </form>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card-dark">
            <div class="text-gray-400 text-xs font-bold tracking-wider mb-2">TOTAL TRANSAKSI {{ request('bulan') ? strtoupper(date('F', mktime(0, 0, 0, request('bulan'), 10))) : 'BULAN INI' }}</div>
            <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</div>
            <div class="text-sm border-2 inline-block px-1 rounded border-transparent" style="color: {{ $persenBulanIni >= 0 ? '#10B981' : '#EF4444' }}">
                <svg class="inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $persenBulanIni >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"/>
                </svg>
                {{ number_format(abs($persenBulanIni), 1, ',', '.') }}% dari bulan lalu
            </div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/10 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card-light relative">
            <div class="flex items-center gap-2 mb-2">
                <div class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded leading-tight">JUMLAH ANGGOTA AKTIF</div>
            </div>
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($anggotaAktif, 0, ',', '.') }}</div>
            <div class="flex -space-x-2 overflow-hidden mt-3">
                <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name=A&background=random" alt=""/>
                <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name=B&background=random" alt=""/>
                <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name=C&background=random" alt=""/>
            </div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-blue-50 text-blue-600 p-3 rounded-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-sm text-gray-800 tracking-wide">RIWAYAT TRANSAKSI TERBARU</h3>
            <div class="flex gap-2">
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">TANGGAL</th>
                    <th class="px-6 py-4">NAMA ANGGOTA</th>
                    <th class="px-6 py-4">JENIS</th>
                    <th class="px-6 py-4">NOMINAL</th>
                    <th class="px-6 py-4">PERIODE</th>
                    <th class="px-6 py-4">REFERENSI</th>
                    <th class="px-6 py-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksi as $trx)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-700">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($trx->anggota->nama_anggota ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $trx->anggota->nama_anggota }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $trx->anggota->nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $jenisClass = 'badge-sukarela';
                                $namaJenis = strtolower($trx->jenisSimpanan->nama ?? '');
                                if (str_contains($namaJenis, 'wajib')) $jenisClass = 'badge-wajib';
                                elseif (str_contains($namaJenis, 'pokok')) $jenisClass = 'badge-pokok';
                            @endphp
                            <span class="badge {{ $jenisClass }}">
                                {{ strtoupper($trx->jenisSimpanan->nama ?? '-') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $trx->periode }}
                        </td>
                        <td class="px-6 py-4 text-gray-400 font-medium">
                            TRX-{{ str_pad($trx->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="text-gray-400 hover:text-gray-600 p-1">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            Tidak ada transaksi ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($transaksi->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-sm text-gray-500 font-medium">
                Menampilkan <span class="text-gray-800 font-bold">{{ $transaksi->firstItem() ?? 0 }}</span>-
                <span class="text-gray-800 font-bold">{{ $transaksi->lastItem() ?? 0 }}</span> 
                dari <span class="text-gray-800 font-bold">{{ $transaksi->total() }}</span> transaksi
            </div>
            <div>
                {{ $transaksi->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
