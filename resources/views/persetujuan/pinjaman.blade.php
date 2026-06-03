{{-- resources/views/persetujuan/pinjaman.blade.php --}}
@extends('layouts.app')

@section('title', 'Persetujuan Pinjaman')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('persetujuan.pinjaman') }}" class="tb-link active">Persetujuan Pinjaman</a>
    <a href="{{ route('persetujuan.pengambilan') }}" class="tb-link">Persetujuan Pengambilan Simpanan</a>
@endsection

@section('page-title', 'Persetujuan Pinjaman')
@section('page-subtitle', 'Daftar pengajuan pinjaman karyawan')

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

        <a href="{{ route('persetujuan.pinjaman') }}" class="sd-link {{ !request('jenis') ? 'active' : '' }}" style="width: 100%; display: block; border-radius: 6px; padding: 8px 12px; margin-bottom: 4px;">
            <span style="font-weight: 600;">Semua Pengajuan</span>
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
                            <a href="{{ route('persetujuan.pinjaman', ['jenis' => $jpChild->id]) }}"
                               class="sd-link {{ request('jenis') == $jpChild->id ? 'active' : '' }}"
                               style="padding: 6px 12px; font-size: 13px;">
                                {{ $jpChild->nama_pinjaman }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ route('persetujuan.pinjaman', ['jenis' => $jpParent->id]) }}"
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
        .badge-pending { background: #FEF3C7; color: #D97706; }
        .badge-approved { background: #D1FAE5; color: #059669; }
        .badge-rejected { background: #FEE2E2; color: #DC2626; }

        .btn-approve {
            background: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-approve:hover { background: #D1FAE5; }
        .btn-reject {
            background: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-reject:hover { background: #FEE2E2; }
    </style>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card-dark">
            <div class="text-gray-400 text-xs font-bold tracking-wider mb-2">TOTAL NOMINAL PENGAJUAN PENDING</div>
            <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/10 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card-light">
            <div class="flex items-center gap-2 mb-2">
                <div class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded leading-tight">TOTAL PENGAJUAN PENDING</div>
            </div>
            <div class="text-4xl font-bold text-amber-600 mb-2">{{ number_format($totalPengajuan, 0, ',', '.') }}</div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-amber-50 text-amber-600 p-3 rounded-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR PENGAJUAN PINJAMAN</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 min-w-max">
                <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">TANGGAL</th>
                        <th class="px-6 py-4">NAMA ANGGOTA</th>
                        <th class="px-6 py-4">JENIS</th>
                        <th class="px-6 py-4">NOMINAL / TENOR</th>
                        <th class="px-6 py-4">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengajuan_list as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-700">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs" style="flex-shrink:0;">
                                        {{ strtoupper(substr($item->anggota?->nama_anggota ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $item->anggota?->nama_anggota ?? 'UNKNOWN' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $item->anggota?->nik ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge bg-gray-100 text-gray-700">
                                    {{ strtoupper($item->jenisPinjaman?->nama_pinjaman ?? '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">Rp {{ number_format($item->jumlah_pengajuan, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $item->tenor }} Bulan / Bunga {{ $item->bunga }}%</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status == 'pending')
                                    <span class="badge badge-pending">PENDING</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge badge-approved">APPROVED</span>
                                @else
                                    <span class="badge badge-rejected">REJECTED</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->status == 'pending')
                                <div x-data="{ openModal: false }" class="relative">
                                    <button @click="openModal = true" class="btn-approve" style="background:#EEF2FF;color:#4F46E5;border-color:#C7D2FE;" title="Review Pengajuan">Review Detail</button>

                                    <!-- Modal View -->
                                    <template x-teleport="body">
                                        <div x-show="openModal" class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm p-2" x-transition.opacity="" style="display: none;">
                                            <div @click.away="openModal = false" class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden" style="max-height:90vh;overflow-y:auto;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                                                <div class="px-4 pt-4 pb-0">
                                                <div class="flex justify-between items-start border-b border-gray-100 pb-2 mb-3">
                                                    <div>
                                                        <h3 class="text-lg font-bold text-gray-900">Review Pengajuan Pinjaman</h3>
                                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                            <p class="text-sm text-gray-500">{{ $item->anggota?->nama_anggota ?? 'UNKNOWN' }} ({{ $item->anggota?->nik ?? '-' }})</p>
                                                            @if(($item->payment_method ?? 'gaji') === 'gaji')
                                                                <span style="display:inline-flex;align-items:center;gap:4px;background:#DCFCE7;color:#15803D;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;">
                                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                                                                    POTONG GAJI
                                                                </span>
                                                            @else
                                                                <span style="display:inline-flex;align-items:center;gap:4px;background:#DBEAFE;color:#1D4ED8;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;">
                                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h2M10 15h6"/></svg>
                                                                    MANDIRI
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3 mb-3">
                                                    <!-- Data Pinjaman -->
                                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-2">
                                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Pengajuan</h4>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Jenis Pinjaman</span>
                                                            <strong class="text-sm text-gray-800">{{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Nominal Pengajuan</span>
                                                            <strong class="text-sm text-gray-800">Rp {{ number_format($item->jumlah_pengajuan, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Tenor & Bunga</span>
                                                            <strong class="text-sm text-gray-800">{{ $item->tenor }} Bulan / {{ $item->bunga }}%</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Cicilan Pengajuan (Per Bulan)</span>
                                                            <strong class="text-sm text-blue-600">Rp {{ number_format($item->cicilan_per_bulan, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Total Hutang (Pengajuan + Berjalan)</span>
                                                            <strong class="text-sm text-blue-600">Rp {{ number_format($item->total_pinjaman + $item->pinjaman_berjalan_saat_ini, 0, ',', '.') }}</strong>
                                                        </div>
                                                        @if($item->total_pelunasan_pasti > 0)
                                                        <div class="pt-2 border-t border-gray-200 mt-2">
                                                            <div class="flex justify-between items-center mb-1">
                                                                <span class="text-[11px] text-orange-600 font-bold uppercase">Pelunasan Pinjaman Lama</span>
                                                                <span class="text-sm text-orange-600 font-bold">- Rp {{ number_format($item->total_pelunasan_pasti, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="flex justify-between items-center bg-blue-100 p-2 rounded-lg">
                                                                <span class="text-[11px] text-blue-800 font-bold uppercase">Net Cair (Diterima)</span>
                                                                <span class="text-sm text-blue-800 font-bold">Rp {{ number_format($item->net_cair, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    <!-- Data Finansial Karyawan -->
                                                    <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100 space-y-2">
                                                        <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Kondisi Finansial saat ini</h4>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-500">Total Simpanan </span>
                                                            <strong class="text-sm text-gray-800">Rp {{ number_format($item->total_simpanan_saat_ini ?? 0, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-500">Pinjaman Berjalan (Sisa)</span>
                                                            <strong class="text-sm text-gray-800">Rp {{ number_format($item->pinjaman_berjalan_saat_ini ?? 0, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-500">Cicilan Saat Ini (Per Bulan)</span>
                                                            <strong class="text-sm text-gray-800">Rp {{ number_format($item->cicilan_saat_ini ?? 0, 0, ',', '.') }}</strong>*<small>{{ $item->sisa_tenor }}</small>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-500">Simpanan Wajib & Sukarela (Per Bulan)</span>
                                                            <strong class="text-sm text-gray-800">Rp {{ number_format($item->simpanan_perbulan ?? 0, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div class="pt-2 border-t border-blue-200">
                                                            <div class="flex justify-between items-start mb-2">
                                                                <span class="block text-[11px] text-blue-700 font-bold uppercase">Rencana Cicilan Per Bulan (Setelah Disetujui)</span>
                                                                @if($item->total_pelunasan_pasti > 0)
                                                                    <div class="text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold">Adjustment Pelunasan Aktif</div>
                                                                @endif
                                                            </div>

                                                            {{-- Breakdown table gaji / mandiri / total --}}
                                                            <table style="width:100%;font-size:11px;border-collapse:collapse;">
                                                                <thead>
                                                                    <tr style="color:#6B7280;">
                                                                        <th style="text-align:left;padding:3px 0;font-weight:600;">Metode</th>
                                                                        <th style="text-align:right;padding:3px 0;font-weight:600;">Sebelum</th>
                                                                        <th style="text-align:right;padding:3px 0;font-weight:600;">Setelah</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    {{-- Baris Gaji --}}
                                                                    <tr style="border-top:1px solid #e5e7eb;">
                                                                        <td style="padding:5px 0;">
                                                                            <span style="display:inline-flex;align-items:center;gap:4px;background:#DCFCE7;color:#15803D;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">
                                                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                                                                Potong Gaji
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align:right;color:#374151;font-weight:600;">Rp {{ number_format($item->cicilan_gaji_existing ?? 0, 0, ',', '.') }}</td>
                                                                        <td style="text-align:right;font-weight:700;{{ ($item->payment_method ?? 'gaji') === 'gaji' ? 'color:#15803D;' : 'color:#374151;' }}">
                                                                            Rp {{ number_format($item->cicilan_gaji_baru ?? 0, 0, ',', '.') }}
                                                                            @if(($item->payment_method ?? 'gaji') === 'gaji')
                                                                                <span style="font-size:9px;background:#DCFCE7;color:#15803D;padding:1px 5px;border-radius:10px;margin-left:2px;">+baru</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    {{-- Baris Mandiri --}}
                                                                    <tr style="border-top:1px solid #e5e7eb;">
                                                                        <td style="padding:5px 0;">
                                                                            <span style="display:inline-flex;align-items:center;gap:4px;background:#DBEAFE;color:#1D4ED8;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">
                                                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                                                                                Mandiri
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align:right;color:#374151;font-weight:600;">Rp {{ number_format($item->cicilan_mandiri_existing ?? 0, 0, ',', '.') }}</td>
                                                                        <td style="text-align:right;font-weight:700;{{ ($item->payment_method ?? 'gaji') === 'mandiri' ? 'color:#1D4ED8;' : 'color:#374151;' }}">
                                                                            Rp {{ number_format($item->cicilan_mandiri_baru ?? 0, 0, ',', '.') }}
                                                                            @if(($item->payment_method ?? 'gaji') === 'mandiri')
                                                                                <span style="font-size:9px;background:#DBEAFE;color:#1D4ED8;padding:1px 5px;border-radius:10px;margin-left:2px;">+baru</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    {{-- Baris Total --}}
                                                                    <tr style="border-top:2px solid #bfdbfe;background:#EFF6FF;">
                                                                        <td style="padding:6px 4px;font-size:11px;font-weight:700;color:#1e40af;">Total Semua</td>
                                                                        <td style="text-align:right;color:#1e40af;font-weight:700;">Rp {{ number_format(($item->cicilan_gaji_existing ?? 0) + ($item->cicilan_mandiri_existing ?? 0), 0, ',', '.') }}</td>
                                                                        <td style="text-align:right;color:#1e40af;font-weight:800;font-size:13px;">Rp {{ number_format(($item->cicilan_gaji_baru ?? 0) + ($item->cicilan_mandiri_baru ?? 0), 0, ',', '.') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div class="text-[10px] text-gray-400 mt-1">*Sudah dikurangi cicilan yang akan lunas</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Accordion Detail Pinjaman Aktif -->
                                                <div x-data="{ showLoans: false }" class="mb-2 border border-gray-100 rounded-xl overflow-hidden bg-white shadow-sm">
                                                    <button @click="showLoans = !showLoans" type="button" class="w-full flex justify-between items-center bg-gray-50/80 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                            </svg>
                                                            View Active Loans Details
                                                        </div>
                                                        <svg :class="showLoans ? 'transform rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                    <div x-show="showLoans" x-collapse style="display: none;">
                                                        <div class="p-2 border-t border-gray-100">
                                                            @if($item->anggota && $item->anggota->pinjamanAktif->count() > 0)
                                                                <div class="overflow-x-auto">
                                                                    <table class="w-full text-left text-[11px] text-gray-600">
                                                                        <thead class="text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                                                            <tr>
                                                                                <th class="pb-2 font-bold w-1/3">Jenis Pinjaman</th>
                                                                                <th class="pb-2 font-bold text-right">Sisa Pinjaman</th>
                                                                                <th class="pb-2 font-bold text-right">Cicilan</th>
                                                                                <th class="pb-2 font-bold text-center">Tenor</th>
                                                                                <th class="pb-2 font-bold text-center">Status</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-gray-50">
                                                                            @foreach($item->anggota->pinjamanAktif as $loan)
                                                                                <tr>
                                                                                    <td class="py-2 font-bold text-gray-700">{{ $loan->jenisPinjaman?->nama_pinjaman ?? 'Pinjaman' }}</td>
                                                                                    <td class="py-2 text-right font-medium">Rp {{ number_format($loan->sisa_pinjaman, 0, ',', '.') }}</td>
                                                                                    <td class="py-2 text-right">Rp {{ number_format($loan->cicilan_per_bulan, 0, ',', '.') }}</td>
                                                                                    <td class="py-2 text-center text-gray-500">{{ $loan->sisa_tenor }} mos</td>
                                                                                    <td class="py-2 text-center">
                                                                                        @if(isset($item->pelunasan_ids) && in_array($loan->id, $item->pelunasan_ids))
                                                                                            <span class="inline-block px-2 py-0.5 bg-red-100 text-red-600 rounded text-[9px] font-bold uppercase ring-1 ring-red-200">AKAN LUNAS</span>
                                                                                        @else
                                                                                            <span class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[9px] font-bold uppercase">{{ $loan->status }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="text-center py-4 text-xs text-gray-400">Tidak ada pinjaman berjalan.</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div x-data="{ action: null, alasan: '' }" class="border-t border-gray-100 pt-3 px-4 pb-4">
                                                    <div class="flex flex-col gap-2">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan / Alasan (Opsional)</label>
                                                            <textarea x-model="alasan" rows="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm p-2" placeholder="Catatan persetujuan atau alasan penolakan..."></textarea>
                                                        </div>
                                                        
                                                        <div class="flex justify-end gap-2">
                                                            <button @click="openModal = false" type="button" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                                            
                                                            <form action="{{ route('pinjaman.approval.reject', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menolak pengajuan ini?');">
                                                                @csrf
                                                                <input type="hidden" name="alasan" x-model="alasan">
                                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700">Tolak Pengajuan</button>
                                                            </form>

                                                            <form action="{{ route('pinjaman.approval.approve', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menyetujui pengajuan ini?');">
                                                                @csrf
                                                                <!-- We can pass reason to approve if backend supports it, but currently backend might not -->
                                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700">Setujui Pengajuan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>{{-- end px-4 wrapper --}}

                                            </div>
                                        </div>
                                    </template>
                                </div>
                                @else
                                    <span class="text-xs text-gray-400">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                Tidak ada data pengajuan pinjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
