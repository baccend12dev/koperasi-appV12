@extends('layouts.app')

@section('title', 'Pencairan Pinjaman')
@section('page-title', 'Pencairan Pinjaman')
@section('page-subtitle', 'Rekap pencairan dana pinjaman yang telah disetujui')

@section('topbar-nav')
    <a href="{{ route('pencairan.pinjaman') }}" class="tb-link active flex items-center gap-2">
        <span>Pencairan Pinjaman</span>
        @if(($countPendingPinjaman ?? 0) > 0)
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold bg-amber-500 text-white rounded-full leading-none shadow-sm">
                {{ $countPendingPinjaman }}
            </span>
        @endif
    </a>
    <a href="{{ route('pencairan.pengambilan') }}" class="tb-link flex items-center gap-2">
        <span>Penarikan Simpanan</span>
        @if(($countPendingSimpanan ?? 0) > 0)
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold bg-amber-500 text-white rounded-full leading-none shadow-sm">
                {{ $countPendingSimpanan }}
            </span>
        @endif
    </a>
@endsection

@section('content')
<div class="px-6 pt-2 pb-6 space-y-6">

<style>
    .badge { display:inline-flex; align-items:center; padding:2px 9px; border-radius:12px; font-size:11px; font-weight:600; }
    .badge-paid    { background:#D1FAE5; color:#059669; }
    .badge-pending { background:#FEF3C7; color:#D97706; }

    /* Checkbox styles */
    .bulk-checkbox {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #4F46E5;
        border-radius: 4px;
    }

    /* Bulk action bar */
    #bulk-action-bar {
        display: none;
        align-items: center;
        gap: 12px;
        background: #EEF2FF;
        border: 1px solid #C7D2FE;
        border-radius: 10px;
        padding: 10px 16px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }
    #bulk-action-bar.visible {
        display: flex;
    }
    #bulk-count-label {
        font-size: 13px;
        font-weight: 600;
        color: #4338CA;
    }
    .btn-bulk-bayar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        background: #4F46E5;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-bulk-bayar:hover { background: #4338CA; }
    .btn-bulk-clear {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        background: #fff;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-bulk-clear:hover { background: #F3F4F6; }

    /* Row selected highlight */
    tr.row-selected td { background: #EEF2FF !important; }

    /* Filter bar */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .filter-search-wrap svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        pointer-events: none;
    }
    .filter-search-input {
        width: 100%;
        height: 36px;
        padding: 0 12px 0 34px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 13px;
        color: #374151;
        background: #fff;
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    .filter-search-input:focus {
        border-color: #4F46E5;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }
    .filter-search-input::placeholder { color: #9CA3AF; }
    .filter-status-tabs {
        display: flex;
        background: #F3F4F6;
        border-radius: 8px;
        padding: 3px;
        gap: 2px;
    }
    .filter-tab {
        padding: 5px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .filter-tab.active {
        background: #fff;
        color: #4338CA;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .filter-tab:hover:not(.active) { color: #374151; }
    .filter-result-info {
        font-size: 12px;
        color: #9CA3AF;
        white-space: nowrap;
    }
    #no-result-row { display: none; }

    /* Jenis pinjaman select */
    .filter-jenis-select {
        height: 36px;
        padding: 0 10px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 12px;
        color: #374151;
        background: #fff;
        outline: none;
        cursor: pointer;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
        min-width: 155px;
        max-width: 200px;
    }
    .filter-jenis-select:focus {
        border-color: #4F46E5;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }
    .btn-filter-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        background: #F9FAFB;
        color: #6B7280;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .btn-filter-reset:hover {
        background: #EEF2FF;
        border-color: #C7D2FE;
        color: #4F46E5;
    }
    /* Compact & Bordered Table */
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
</style>

{{-- Filter Bar — 1 baris --}}
<form action="{{ route('pencairan.pinjaman') }}" method="GET"
      class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3 flex-wrap">

    {{-- Pertahankan filter periode dari sidebar --}}
    @if($tahun)<input type="hidden" name="tahun" value="{{ $tahun }}">@endif
    @if($bulan)<input type="hidden" name="bulan" value="{{ $bulan }}">@endif

    {{-- Search --}}
    <div class="filter-search-wrap" style="min-width:180px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="filter-search" class="filter-search-input"
               placeholder="Cari nama anggota atau NIK..."
               oninput="applyFilter()">
    </div>

    {{-- Divider --}}
    <div style="width:1px;height:24px;background:#E5E7EB;flex-shrink:0;"></div>

    {{-- Jenis Pinjaman --}}
    <select name="jenis" class="filter-jenis-select" onchange="this.form.submit()" title="Filter jenis pinjaman">
        <option value="">Semua Jenis</option>
        @foreach($jenisPinjamanList as $jp)
            @if($jp->children->count() > 0)
                <optgroup label="{{ $jp->nama_pinjaman }}">
                    @foreach($jp->children as $child)
                        <option value="{{ $child->id }}" {{ $jenis == $child->id ? 'selected' : '' }}>{{ $child->nama_pinjaman }}</option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{ $jp->id }}" {{ $jenis == $jp->id ? 'selected' : '' }}>{{ $jp->nama_pinjaman }}</option>
            @endif
        @endforeach
    </select>

    {{-- Divider --}}
    <div style="width:1px;height:24px;background:#E5E7EB;flex-shrink:0;"></div>

    {{-- Status Tabs --}}
    <div class="filter-status-tabs">
        <button type="button" class="filter-tab active" data-status="semua" onclick="setStatusFilter('semua', this)">Semua</button>
        <button type="button" class="filter-tab" data-status="pending" onclick="setStatusFilter('pending', this)">Belum Bayar</button>
        <button type="button" class="filter-tab" data-status="paid" onclick="setStatusFilter('paid', this)">Lunas</button>
    </div>

    {{-- Info hasil filter --}}
    <span class="filter-result-info" id="filter-info"></span>

    {{-- Spacer --}}
    <div style="flex:1;"></div>

    {{-- Reset button --}}
    <a href="{{ route('pencairan.pinjaman') }}" class="btn-filter-reset" title="Reset semua filter">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
        </svg>
    </a>

</form>

{{-- Bulk Action Bar --}}
<div id="bulk-action-bar">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4338CA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
    </svg>
    <span id="bulk-count-label">0 dipilih</span>
    <button type="button" class="btn-bulk-bayar" onclick="openBulkModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Tandai Sudah Dibayar
    </button>
    <button type="button" class="btn-bulk-clear" onclick="clearSelection()">Batal Pilih</button>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <h3 class="font-bold text-sm text-gray-800 tracking-wide uppercase">
            REKAP PENCAIRAN PINJAMAN
            @if($tahun)
                <span class="ml-2 text-xs text-gray-400 font-normal">
                    {{ $tahun }}@if($bulan) / {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}@endif
                </span>
            @endif
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="table-compact min-w-max">
            <thead>
                <tr>
                    <th class="w-10 text-center">
                        <input type="checkbox" id="check-all" class="bulk-checkbox" title="Pilih semua belum bayar" onchange="toggleAll(this)">
                    </th>
                    <th>TANGGAL</th>
                    <th>ANGGOTA</th>
                    <th>JENIS PINJAMAN</th>
                    <th class="text-right">POKOK</th>
                    <th class="text-right">BUNGA</th>
                    <th class="text-right">TOTAL</th>
                    <th class="text-right">POTONGAN</th>
                    <th class="text-right bg-emerald-50/80 text-emerald-800 font-extrabold">NET CAIR</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody id="pinjaman-tbody">
                @forelse($listPinjaman as $row)
                @php $p = $pencairanExisting->get($row->id); $isPaid = $p && $p->status === 'paid'; @endphp
                <tr class="hover:bg-gray-50/50 transition-colors {{ $isPaid ? '' : 'row-pending' }}"
                    data-id="{{ $row->id }}"
                    data-nominal="{{ $row->jumlah_cair }}"
                    data-anggota="{{ $row->user_id }}">
                    <td class="text-center">
                        @if(!$isPaid)
                        <input type="checkbox"
                               class="bulk-checkbox row-check"
                               value="{{ $row->id }}"
                               data-nominal="{{ $row->jumlah_cair }}"
                               data-anggota="{{ $row->user_id }}"
                               onchange="onCheckChange()">
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="font-semibold text-gray-800 whitespace-nowrap text-sm">
                        {{ $row->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($row->anggota?->nama_anggota ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 text-sm">{{ $row->anggota?->nama_anggota ?? '-' }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $row->anggota?->nik ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="font-semibold text-gray-800 text-sm">{{ $row->jenisPinjaman?->nama_pinjaman ?? 'Pinjaman' }}</span>
                    </td>
                    <td class="text-right font-semibold text-gray-800 text-sm whitespace-nowrap">
                        Rp {{ number_format($row->jumlah_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-medium text-gray-600 text-sm whitespace-nowrap">
                        Rp {{ number_format($row->total_bunga ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-semibold text-gray-800 text-sm whitespace-nowrap">
                        Rp {{ number_format($row->total_pinjaman ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-semibold text-red-600 text-sm whitespace-nowrap">
                        @if($row->potongan_pelunasan > 0)
                            -Rp {{ number_format($row->potongan_pelunasan, 0, ',', '.') }}
                        @else
                            <span class="text-gray-300">Rp 0</span>
                        @endif
                    </td>
                    <td class="text-right font-black text-emerald-700 text-base whitespace-nowrap bg-emerald-50/50">
                        Rp {{ number_format($row->jumlah_cair, 0, ',', '.') }}
                    </td>

                    <td class="text-center">
                        @if($isPaid)
                            <span class="badge badge-paid">LUNAS</span>
                            <div class="text-xs text-gray-500 mt-0.5">{{ ucfirst($p->metode) }} · {{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</div>
                        @else
                            <span class="badge badge-pending">BELUM BAYAR</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($isPaid)
                            <span class="text-xs text-gray-400">—</span>
                        @else
                        <div x-data="{ openBayar: false }">
                            <button @click="openBayar = true"
                                class="px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                                Tandai Bayar
                            </button>

                            <template x-teleport="body">
                                <div x-show="openBayar" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4" x-transition.opacity style="display:none;">
                                    <div @click.away="openBayar = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                        <div class="flex justify-between items-start mb-5">
                                            <div>
                                                <h3 class="text-base font-bold text-gray-900">Konfirmasi Pembayaran</h3>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $row->anggota?->nama_anggota }} · {{ $row->jenisPinjaman?->nama_pinjaman }}</p>
                                            </div>
                                            <button @click="openBayar = false" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <div class="bg-emerald-50 rounded-xl p-4 mb-5 text-center">
                                            <div class="text-xs text-emerald-600 font-semibold mb-1 uppercase tracking-wider">NOMINAL BERSIH DIBAYARKAN</div>
                                            <div class="text-2xl font-bold text-emerald-700">Rp {{ number_format($row->jumlah_cair, 0, ',', '.') }}</div>
                                        </div>

                                        <form action="{{ route('pencairan.bayar') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="ref_type"   value="pinjaman">
                                            <input type="hidden" name="ref_id"     value="{{ $row->id }}">
                                            <input type="hidden" name="anggota_id" value="{{ $row->user_id }}">
                                            <input type="hidden" name="nominal"    value="{{ $row->jumlah_cair }}">

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode</label>
                                                    <select name="metode" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                                        <option value="transfer">Transfer</option>
                                                        <option value="cash">Cash</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                                                <input type="text" name="keterangan" placeholder="No. ref / catatan..." class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            </div>
                                            <div class="flex justify-end gap-3 pt-2">
                                                <button @click.prevent="openBayar = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95">Simpan & Tandai Lunas</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-6 py-14 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tidak ada data pencairan pinjaman untuk periode ini.
                    </td>
                </tr>
                @endforelse
                {{-- Row ditampilkan saat filter tidak menemukan hasil --}}
                <tr id="no-result-row" style="display:none;">
                    <td colspan="11" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-9 h-9 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="11" cy="11" r="8" stroke-width="1.5"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="1.5"/>
                        </svg>
                        <div class="text-sm font-medium text-gray-400">Tidak ada data yang sesuai filter</div>
                        <div class="text-xs text-gray-300 mt-1">Coba ubah kata kunci atau status</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($listPinjaman->count() > 0)
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-between items-center text-xs">
        <div class="text-gray-400">Total {{ $listPinjaman->count() }} Transaksi</div>
        <div class="flex gap-4 flex-wrap">
            <span class="text-gray-500">Total Pokok: <strong class="text-gray-700">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</strong></span>
            <span class="text-gray-500">Total Bunga: <strong class="text-gray-700">Rp {{ number_format($totalBunga, 0, ',', '.') }}</strong></span>
            <span class="text-gray-500">Total Potongan: <strong class="text-red-500">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</strong></span>
            <span class="text-gray-500">Total Net Cair: <strong class="text-emerald-600 font-bold">Rp {{ number_format($totalNet, 0, ',', '.') }}</strong></span>
        </div>
    </div>
    @endif
</div>

</div>

{{-- Modal Bulk Bayar --}}
<div id="modal-bulk" class="fixed inset-0 z-[9999] items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 animate-fade-in">
        <div class="flex justify-between items-start mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-900">Tandai Sudah Dibayar (Bulk)</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="bulk-modal-subtitle">0 transaksi dipilih</p>
            </div>
            <button onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- List item terpilih --}}
        <div id="bulk-selected-list" class="mb-4 max-h-40 overflow-y-auto space-y-1.5 text-sm text-gray-600 bg-indigo-50 rounded-xl p-3"></div>

        <div class="bg-emerald-50 rounded-xl p-4 mb-5 text-center">
            <div class="text-xs text-emerald-600 font-semibold mb-1 uppercase tracking-wider">TOTAL NOMINAL DIBAYARKAN</div>
            <div class="text-2xl font-bold text-emerald-700" id="bulk-total-nominal">Rp 0</div>
        </div>

        <form action="{{ route('pencairan.bayar.bulk') }}" method="POST" id="bulk-form" class="space-y-4">
            @csrf
            <input type="hidden" name="ref_type" value="pinjaman">
            <div id="bulk-hidden-inputs"></div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode</label>
                    <select name="metode" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="transfer">Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" placeholder="No. ref / catatan..." class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeBulkModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-all active:scale-95">
                    Simpan & Tandai Lunas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Filter logic ──────────────────────────────────────────
let activeStatus = 'semua';

function setStatusFilter(status, btn) {
    activeStatus = status;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    applyFilter();
}

function applyFilter() {
    const q = document.getElementById('filter-search').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#pinjaman-tbody tr:not(#no-result-row)');
    let visible = 0;

    rows.forEach(row => {
        const isPaid   = row.querySelector('.badge-paid') !== null;
        const isPending = row.querySelector('.badge-pending') !== null;

        // Status filter
        let statusOk = true;
        if (activeStatus === 'paid')    statusOk = isPaid;
        if (activeStatus === 'pending') statusOk = isPending;

        // Search filter (nama + NIK)
        let searchOk = true;
        if (q) {
            const text = row.textContent.toLowerCase();
            searchOk = text.includes(q);
        }

        const show = statusOk && searchOk;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // No result row
    document.getElementById('no-result-row').style.display = (visible === 0) ? '' : 'none';

    // Info text
    const total = rows.length;
    const info = document.getElementById('filter-info');
    if (q || activeStatus !== 'semua') {
        info.textContent = visible + ' dari ' + total + ' data';
    } else {
        info.textContent = '';
    }

    // After filter, uncheck hidden rows
    rows.forEach(row => {
        if (row.style.display === 'none') {
            const cb = row.querySelector('.row-check');
            if (cb) cb.checked = false;
        }
    });
    onCheckChange();
}

// ─── Checkbox & Bulk logic ──────────────────────────────────
function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
}

function onCheckChange() {
    const checked = getChecked();
    const bar = document.getElementById('bulk-action-bar');
    const label = document.getElementById('bulk-count-label');
    label.textContent = checked.length + ' dipilih';
    if (checked.length > 0) {
        bar.classList.add('visible');
    } else {
        bar.classList.remove('visible');
    }

    // Highlight rows
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.closest('tr').classList.toggle('row-selected', cb.checked);
    });

    // Sync check-all state (only visible unchecked rows)
    const allVisible = [...document.querySelectorAll('.row-check')].filter(cb => cb.closest('tr').style.display !== 'none');
    document.getElementById('check-all').indeterminate = checked.length > 0 && checked.length < allVisible.length;
    document.getElementById('check-all').checked = allVisible.length > 0 && checked.length === allVisible.length;
}

function toggleAll(source) {
    // Only toggle visible rows
    document.querySelectorAll('.row-check').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = source.checked;
        }
    });
    onCheckChange();
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('check-all').checked = false;
    onCheckChange();
}

function formatRupiah(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function openBulkModal() {
    const checked = getChecked();
    if (checked.length === 0) return;

    let total = 0;
    let listHtml = '';
    let hiddenHtml = '';

    checked.forEach((cb, i) => {
        const nom = parseFloat(cb.dataset.nominal);
        const anggota = cb.dataset.anggota;
        total += nom;
        const row = cb.closest('tr');
        const namaEl = row.querySelector('.font-bold.text-gray-800');
        const nama = namaEl ? namaEl.textContent.trim() : '-';
        listHtml += `<div class="flex justify-between items-center py-1 border-b border-indigo-100 last:border-0">
            <span class="text-indigo-800 font-medium">${nama}</span>
            <span class="text-emerald-700 font-bold">${formatRupiah(nom)}</span>
        </div>`;
        hiddenHtml += `<input type="hidden" name="ids[]" value="${cb.value}">`;
        hiddenHtml += `<input type="hidden" name="nominals[]" value="${nom}">`;
        hiddenHtml += `<input type="hidden" name="anggota_ids[]" value="${anggota}">`;
    });

    document.getElementById('bulk-modal-subtitle').textContent = checked.length + ' transaksi dipilih';
    document.getElementById('bulk-selected-list').innerHTML = listHtml;
    document.getElementById('bulk-total-nominal').textContent = formatRupiah(total);
    document.getElementById('bulk-hidden-inputs').innerHTML = hiddenHtml;

    const modal = document.getElementById('modal-bulk');
    modal.style.display = 'flex';
}

function closeBulkModal() {
    document.getElementById('modal-bulk').style.display = 'none';
}

// Close on backdrop click
document.getElementById('modal-bulk').addEventListener('click', function(e) {
    if (e.target === this) closeBulkModal();
});
</script>
@endsection
