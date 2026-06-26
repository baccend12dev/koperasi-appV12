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
<div class="px-6 py-4 space-y-4">

<style>
    .badge {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 12px;
        font-size: 11px; font-weight: 600;
    }
    .badge-pending  { background: #FEF3C7; color: #D97706; }
    .badge-approved { background: #D1FAE5; color: #059669; }
    .badge-rejected { background: #FEE2E2; color: #DC2626; }

    .btn-review {
        background: #EEF2FF; color: #4F46E5;
        border: 1px solid #C7D2FE;
        font-weight: 600; padding: 5px 12px;
        border-radius: 6px; font-size: 12px;
        transition: all 0.2s; cursor: pointer;
        white-space: nowrap;
    }
    .btn-review:hover { background: #E0E7FF; }

    /* Checkbox */
    .bulk-checkbox {
        width: 16px; height: 16px;
        cursor: pointer; accent-color: #059669;
        border-radius: 4px;
    }

    /* Bulk action bar */
    #bulk-bar {
        display: none; align-items: center; gap: 12px;
        background: #ECFDF5; border: 1px solid #A7F3D0;
        border-radius: 10px; padding: 10px 16px;
        transition: all 0.2s;
    }
    #bulk-bar.visible { display: flex; }
    #bulk-count { font-size: 13px; font-weight: 600; color: #065F46; }

    .btn-bulk-approve {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 16px; font-size: 12px; font-weight: 700;
        color: #fff; background: #059669; border: none;
        border-radius: 8px; cursor: pointer; transition: background 0.15s;
    }
    .btn-bulk-approve:hover { background: #047857; }
    .btn-bulk-cancel {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 7px 12px; font-size: 12px; font-weight: 600;
        color: #6B7280; background: #fff; border: 1px solid #D1D5DB;
        border-radius: 8px; cursor: pointer; transition: all 0.15s;
    }
    .btn-bulk-cancel:hover { background: #F3F4F6; }

    /* Row highlight when selected */
    tr.row-selected td { background: #F0FDF4 !important; }

    /* Filter bar */
    .filter-search-wrap {
        position: relative; flex: 1; min-width: 200px;
    }
    .filter-search-wrap svg {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: #9CA3AF; pointer-events: none;
    }
    .filter-search-input {
        width: 100%; height: 36px; padding: 0 12px 0 34px;
        border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 13px; color: #374151; background: #fff;
        outline: none; transition: all 0.2s; font-family: inherit;
        box-sizing: border-box;
    }
    .filter-search-input:focus {
        border-color: #059669; box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
    }
    .filter-search-input::placeholder { color: #9CA3AF; }
    .filter-status-tabs {
        display: flex; background: #F3F4F6;
        border-radius: 8px; padding: 3px; gap: 2px;
    }
    .filter-tab {
        padding: 5px 14px; border-radius: 6px; font-size: 12px;
        font-weight: 600; color: #6B7280; background: transparent;
        border: none; cursor: pointer; transition: all 0.15s; white-space: nowrap;
    }
    .filter-tab.active {
        background: #fff; color: #065F46;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .filter-tab:hover:not(.active) { color: #374151; }
    .filter-result-info { font-size: 12px; color: #9CA3AF; white-space: nowrap; }
    #no-result-row { display: none; }
</style>

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3 flex-wrap">
    <div class="filter-search-wrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="filter-search" class="filter-search-input"
               placeholder="Cari nama anggota atau NIK..." oninput="applyFilter()">
    </div>

    <div style="width:1px;height:24px;background:#E5E7EB;flex-shrink:0;"></div>

    <div class="filter-status-tabs">
        <button type="button" class="filter-tab active" data-status="semua" onclick="setStatus('semua',this)">Semua</button>
        <button type="button" class="filter-tab" data-status="pending" onclick="setStatus('pending',this)">Pending</button>
        <button type="button" class="filter-tab" data-status="approved" onclick="setStatus('approved',this)">Approved</button>
        <button type="button" class="filter-tab" data-status="rejected" onclick="setStatus('rejected',this)">Rejected</button>
    </div>

    <span class="filter-result-info" id="filter-info"></span>
</div>

{{-- Bulk Action Bar --}}
<div id="bulk-bar">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#065F46" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
    </svg>
    <span id="bulk-count">0 dipilih</span>
    <button type="button" class="btn-bulk-approve" onclick="openBulkModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Setujui Sekaligus
    </button>
    <button type="button" class="btn-bulk-cancel" onclick="clearSel()">Batal Pilih</button>
</div>

{{-- Main Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR PENGAJUAN PINJAMAN</h3>
        <span class="text-xs text-gray-400">{{ $pengajuan_list->where('status','pending')->count() }} pending</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 min-w-max">
            <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-4 py-4">
                        <input type="checkbox" id="check-all" class="bulk-checkbox" onchange="toggleAll(this)" title="Pilih semua pending">
                    </th>
                    <th class="px-6 py-4">TANGGAL</th>
                    <th class="px-6 py-4">NAMA ANGGOTA</th>
                    <th class="px-6 py-4">JENIS</th>
                    <th class="px-6 py-4">NOMINAL / TENOR</th>
                    <th class="px-6 py-4 text-center">STATUS</th>
                    <th class="px-6 py-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="approval-tbody">
                @forelse($pengajuan_list as $item)
                    @php $isPending = $item->status === 'pending'; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors"
                        data-status="{{ $item->status }}">
                        <td class="px-4 py-4">
                            @if($isPending)
                                <input type="checkbox" class="bulk-checkbox row-check"
                                       value="{{ $item->id }}"
                                       data-nama="{{ $item->anggota?->nama_anggota ?? '-' }}"
                                       data-nominal="{{ $item->jumlah_pengajuan }}"
                                       onchange="onCheck()">
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-700 whitespace-nowrap">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
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
                        <td class="px-6 py-4 text-center">
                            @if($item->status == 'pending')
                                <span class="badge badge-pending">PENDING</span>
                            @elseif($item->status == 'approved')
                                <span class="badge badge-approved">APPROVED</span>
                            @else
                                <span class="badge badge-rejected">REJECTED</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($isPending)
                            <div x-data="{ openModal: false }" class="relative">
                                <button @click="openModal = true" class="btn-review">Review Detail</button>

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

                                                        {{-- Breakdown table --}}
                                                        <table style="width:100%;font-size:11px;border-collapse:collapse;">
                                                            <thead>
                                                                <tr style="color:#6B7280;">
                                                                    <th style="text-align:left;padding:3px 0;font-weight:600;">Metode</th>
                                                                    <th style="text-align:right;padding:3px 0;font-weight:600;">Sebelum</th>
                                                                    <th style="text-align:right;padding:3px 0;font-weight:600;">Setelah</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
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

                                            {{-- Action Buttons di dalam modal: Approve + Reject --}}
                                            <div x-data="{ alasan: '' }" class="border-t border-gray-100 pt-3 px-4 pb-4">
                                                <div class="flex flex-col gap-2">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan / Alasan (Opsional)</label>
                                                        <textarea x-model="alasan" rows="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm p-2" placeholder="Catatan persetujuan atau alasan penolakan..."></textarea>
                                                    </div>

                                                    <div class="flex justify-end gap-2">
                                                        <button @click="openModal = false" type="button" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>

                                                        {{-- Tombol Tolak hanya ada di dalam modal Review --}}
                                                        <form action="{{ route('pinjaman.approval.reject', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menolak pengajuan ini?');">
                                                            @csrf
                                                            <input type="hidden" name="alasan" x-model="alasan">
                                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700">Tolak Pengajuan</button>
                                                        </form>

                                                        <form action="{{ route('pinjaman.approval.approve', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menyetujui pengajuan ini?');">
                                                            @csrf
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
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            Tidak ada data pengajuan pinjaman.
                        </td>
                    </tr>
                @endforelse

                {{-- No-result row untuk client-side filter --}}
                <tr id="no-result-row" style="display:none;">
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-9 h-9 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="11" cy="11" r="8" stroke-width="1.5"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="1.5"/>
                        </svg>
                        <div class="text-sm font-medium">Tidak ada data yang sesuai filter</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</div>

{{-- Modal Bulk Approve Confirm --}}
<div id="modal-bulk" class="fixed inset-0 z-[9999] items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <div class="flex justify-between items-start mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-900">Konfirmasi Persetujuan Massal</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="bulk-subtitle">0 pengajuan dipilih</p>
            </div>
            <button onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="bulk-list" class="mb-4 max-h-48 overflow-y-auto bg-emerald-50 rounded-xl p-3 space-y-1 text-sm"></div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-800 flex gap-2">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>Tindakan ini akan menyetujui semua pengajuan yang dipilih secara permanen. Pastikan Anda telah mereview masing-masing sebelum menyetujui massal. Untuk <strong>Tolak</strong>, gunakan tombol Review satu per satu.</span>
        </div>

        <form action="{{ route('persetujuan.pinjaman.approve.bulk') }}" method="POST" id="bulk-form">
            @csrf
            <div id="bulk-hidden-inputs"></div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeBulkModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95">
                    ✓ Setujui Semua
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Filter ────────────────────────────────────────────────
let activeStatus = 'semua';

function setStatus(s, btn) {
    activeStatus = s;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    applyFilter();
}

function applyFilter() {
    const q = document.getElementById('filter-search').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#approval-tbody tr:not(#no-result-row)');
    let visible = 0;

    rows.forEach(row => {
        const rowStatus = row.dataset.status ?? 'semua';
        let statusOk = activeStatus === 'semua' || rowStatus === activeStatus;
        let searchOk = !q || row.textContent.toLowerCase().includes(q);
        const show = statusOk && searchOk;
        row.style.display = show ? '' : 'none';
        if (show) visible++;

        // Uncheck hidden rows
        if (!show) {
            const cb = row.querySelector('.row-check');
            if (cb) cb.checked = false;
        }
    });

    document.getElementById('no-result-row').style.display = visible === 0 ? '' : 'none';

    const info = document.getElementById('filter-info');
    info.textContent = (q || activeStatus !== 'semua')
        ? visible + ' dari ' + rows.length + ' data' : '';

    onCheck();
}

// ─── Checkbox & Bulk ───────────────────────────────────────
function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
}

function onCheck() {
    const checked = getChecked();
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = checked.length + ' dipilih';
    checked.length > 0 ? bar.classList.add('visible') : bar.classList.remove('visible');

    document.querySelectorAll('.row-check').forEach(cb => {
        cb.closest('tr').classList.toggle('row-selected', cb.checked);
    });

    const allVisible = [...document.querySelectorAll('.row-check')]
        .filter(cb => cb.closest('tr').style.display !== 'none');
    const chkAll = document.getElementById('check-all');
    chkAll.indeterminate = checked.length > 0 && checked.length < allVisible.length;
    chkAll.checked = allVisible.length > 0 && checked.length === allVisible.length;
}

function toggleAll(src) {
    document.querySelectorAll('.row-check').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') cb.checked = src.checked;
    });
    onCheck();
}

function clearSel() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('check-all').checked = false;
    onCheck();
}

function openBulkModal() {
    const checked = getChecked();
    if (!checked.length) return;

    let listHtml = '', hiddenHtml = '';
    checked.forEach(cb => {
        const nama = cb.dataset.nama ?? '-';
        const nominal = parseInt(cb.dataset.nominal);
        listHtml += `<div class="flex justify-between items-center py-1 border-b border-emerald-100 last:border-0">
            <span class="text-emerald-900 font-semibold">${nama}</span>
            <span class="text-emerald-700 font-bold">Rp ${nominal.toLocaleString('id-ID')}</span>
        </div>`;
        hiddenHtml += `<input type="hidden" name="ids[]" value="${cb.value}">`;
    });

    document.getElementById('bulk-subtitle').textContent = checked.length + ' pengajuan dipilih';
    document.getElementById('bulk-list').innerHTML = listHtml;
    document.getElementById('bulk-hidden-inputs').innerHTML = hiddenHtml;
    document.getElementById('modal-bulk').style.display = 'flex';
}

function closeBulkModal() {
    document.getElementById('modal-bulk').style.display = 'none';
}

document.getElementById('modal-bulk').addEventListener('click', function(e) {
    if (e.target === this) closeBulkModal();
});
</script>
@endsection
