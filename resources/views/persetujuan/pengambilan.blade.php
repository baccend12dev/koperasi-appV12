{{-- resources/views/persetujuan/pengambilan.blade.php --}}
@extends('layouts.app')

@section('title', 'Persetujuan Pengambilan Simpanan')

@section('topbar-nav')
    <a href="{{ route('persetujuan.pinjaman') }}" class="tb-link">Persetujuan Pinjaman</a>
    <a href="{{ route('persetujuan.pengambilan') }}" class="tb-link active">Persetujuan Pengambilan Simpanan</a>
@endsection

@section('page-title', 'Persetujuan Pengambilan Simpanan')
@section('page-subtitle', 'Daftar pengajuan penarikan simpanan anggota')

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
    #bulk-bar-wd {
        display: none; align-items: center; gap: 12px;
        background: #ECFDF5; border: 1px solid #A7F3D0;
        border-radius: 10px; padding: 10px 16px;
        transition: all 0.2s;
    }
    #bulk-bar-wd.visible { display: flex; }
    #bulk-count-wd { font-size: 13px; font-weight: 600; color: #065F46; }

    .btn-bulk-approve-wd {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 16px; font-size: 12px; font-weight: 700;
        color: #fff; background: #059669; border: none;
        border-radius: 8px; cursor: pointer; transition: background 0.15s;
    }
    .btn-bulk-approve-wd:hover { background: #047857; }
    .btn-bulk-cancel-wd {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 7px 12px; font-size: 12px; font-weight: 600;
        color: #6B7280; background: #fff; border: 1px solid #D1D5DB;
        border-radius: 8px; cursor: pointer; transition: all 0.15s;
    }
    .btn-bulk-cancel-wd:hover { background: #F3F4F6; }

    /* Row highlight when selected */
    tr.row-selected td { background: #F0FDF4 !important; }

    /* Filter bar */
    .filter-search-wrap-wd {
        position: relative; flex: 1; min-width: 200px;
    }
    .filter-search-wrap-wd svg {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: #9CA3AF; pointer-events: none;
    }
    .filter-search-input-wd {
        width: 100%; height: 36px; padding: 0 12px 0 34px;
        border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 13px; color: #374151; background: #fff;
        outline: none; transition: all 0.2s; font-family: inherit;
        box-sizing: border-box;
    }
    .filter-search-input-wd:focus {
        border-color: #059669; box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
    }
    .filter-search-input-wd::placeholder { color: #9CA3AF; }
    .filter-status-tabs-wd {
        display: flex; background: #F3F4F6;
        border-radius: 8px; padding: 3px; gap: 2px;
    }
    .filter-tab-wd {
        padding: 5px 14px; border-radius: 6px; font-size: 12px;
        font-weight: 600; color: #6B7280; background: transparent;
        border: none; cursor: pointer; transition: all 0.15s; white-space: nowrap;
    }
    .filter-tab-wd.active {
        background: #fff; color: #065F46;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .filter-tab-wd:hover:not(.active) { color: #374151; }
    .filter-result-info-wd { font-size: 12px; color: #9CA3AF; white-space: nowrap; }
    #no-result-row-wd { display: none; }
</style>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="bg-green-50 text-green-700 border border-green-200 p-4 rounded-xl text-sm font-semibold">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 text-red-700 border border-red-200 p-4 rounded-xl text-sm font-semibold">
        {{ session('error') }}
    </div>
@endif

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3 flex-wrap">
    <div class="filter-search-wrap-wd">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="filter-search-wd" class="filter-search-input-wd"
               placeholder="Cari nama anggota atau NIK..." oninput="applyFilterWd()">
    </div>

    <div style="width:1px;height:24px;background:#E5E7EB;flex-shrink:0;"></div>

    <div class="filter-status-tabs-wd">
        <button type="button" class="filter-tab-wd active" data-status="semua" onclick="setStatusWd('semua',this)">Semua</button>
        <button type="button" class="filter-tab-wd" data-status="pending" onclick="setStatusWd('pending',this)">Pending</button>
        <button type="button" class="filter-tab-wd" data-status="approved" onclick="setStatusWd('approved',this)">Approved</button>
        <button type="button" class="filter-tab-wd" data-status="rejected" onclick="setStatusWd('rejected',this)">Rejected</button>
    </div>

    <span class="filter-result-info-wd" id="filter-info-wd"></span>
</div>

{{-- Bulk Action Bar --}}
<div id="bulk-bar-wd">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#065F46" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
    </svg>
    <span id="bulk-count-wd">0 dipilih</span>
    <button type="button" class="btn-bulk-approve-wd" onclick="openBulkModalWd()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Setujui Sekaligus
    </button>
    <button type="button" class="btn-bulk-cancel-wd" onclick="clearSelWd()">Batal Pilih</button>
</div>

{{-- Main Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR PENGAJUAN PENGAMBILAN SIMPANAN</h3>
        <span class="text-xs text-gray-400">{{ $pengambilan_list->where('status','pending')->count() }} pending</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 min-w-max">
            <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-4 py-4">
                        <input type="checkbox" id="check-all-wd" class="bulk-checkbox" onchange="toggleAllWd(this)" title="Pilih semua pending">
                    </th>
                    <th class="px-6 py-4">TANGGAL</th>
                    <th class="px-6 py-4">NAMA ANGGOTA</th>
                    <th class="px-6 py-4">NOMINAL TARIKAN</th>
                    <th class="px-6 py-4">ALASAN</th>
                    <th class="px-6 py-4 text-center">STATUS</th>
                    <th class="px-6 py-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="wd-approval-tbody">
                @forelse($pengambilan_list as $item)
                    @php $isPending = $item->status === 'pending'; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors"
                        data-status="{{ $item->status }}">
                        <td class="px-4 py-4">
                            @if($isPending)
                                <input type="checkbox" class="bulk-checkbox row-check-wd"
                                       value="{{ $item->id }}"
                                       data-nama="{{ $item->anggota->nama_anggota ?? '-' }}"
                                       data-nominal="{{ $item->nominal }}"
                                       onchange="onCheckWd()">
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-700 whitespace-nowrap">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($item->anggota->nama_anggota ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $item->anggota->nama_anggota ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $item->anggota->nik ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 text-base">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $item->alasan_pengajuan }}">
                                {{ $item->alasan_pengajuan ?? '—' }}
                            </div>
                            @if($item->alasan_approval)
                                <div class="text-xs text-red-500 mt-1">Catatan Tolak: {{ $item->alasan_approval }}</div>
                            @endif
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
                                    <div x-show="openModal"
                                         class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm p-4"
                                         x-transition.opacity="" style="display:none;">
                                        <div @click.away="openModal = false"
                                             class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden p-6"
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                                            {{-- Header --}}
                                            <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-5">
                                                <div>
                                                    <h3 class="text-lg font-bold text-gray-900">Review Penarikan Simpanan</h3>
                                                    <p class="text-sm text-gray-500 mt-1">{{ $item->anggota->nama_anggota ?? '-' }} ({{ $item->anggota->nik ?? '-' }})</p>
                                                </div>
                                                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>

                                            {{-- Info grid --}}
                                            <div class="grid grid-cols-2 gap-4 mb-5">
                                                {{-- Pengajuan Penarikan --}}
                                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Pengajuan</h4>
                                                    <div>
                                                        <span class="block text-[11px] text-gray-400">Total Penarikan (Bruto)</span>
                                                        <strong class="text-base text-gray-800">Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong>
                                                    </div>
                                                    @if(($item->total_pelunasan ?? 0) > 0)
                                                        <div class="py-2 border-t border-gray-200">
                                                            <span class="block text-[11px] text-amber-600 font-bold">Total Pelunasan Hutang</span>
                                                            <strong class="text-sm text-amber-700">- Rp {{ number_format($item->total_pelunasan, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div class="py-2 border-t border-gray-200">
                                                            <span class="block text-[11px] text-emerald-600 font-bold">Bersih Diterima (Net)</span>
                                                            <strong class="text-base text-emerald-700">Rp {{ number_format($item->net_payout, 0, ',', '.') }}</strong>
                                                        </div>
                                                    @endif
                                                    <div class="pt-2 border-t border-gray-200">
                                                        <span class="block text-[11px] text-gray-400">Tanggal Pengajuan</span>
                                                        <strong class="text-xs text-gray-800">{{ $item->created_at->format('d M Y') }}</strong>
                                                    </div>
                                                    @if($item->alasan_pengajuan)
                                                    <div>
                                                        <span class="block text-[11px] text-gray-400">Alasan Pengajuan</span>
                                                        <strong class="text-xs text-gray-700 line-clamp-2">{{ $item->alasan_pengajuan }}</strong>
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Kondisi Finansial --}}
                                                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-3">
                                                    <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Kondisi Finansial</h4>
                                                    <div>
                                                        <span class="block text-[11px] text-gray-500">Total Simpanan Saat Ini</span>
                                                        <strong class="text-sm text-gray-800">Rp {{ number_format($item->total_simpanan ?? 0, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div>
                                                        <span class="block text-[11px] text-gray-500">Total Hutang Aktif (Sisa)</span>
                                                        <strong class="text-sm text-gray-800">Rp {{ number_format($item->total_hutang ?? 0, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div>
                                                        <span class="block text-[11px] text-gray-500">Cicilan Aktif (Per Bulan)</span>
                                                        <strong class="text-sm text-gray-800">Rp {{ number_format($item->cicilan_berjalan ?? 0, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div class="pt-2 border-t border-blue-200">
                                                        <span class="block text-[11px] font-bold {{ ($item->saldo_setelah_tarik ?? 0) < 0 ? 'text-red-600' : 'text-blue-700' }}">Estimasi Saldo Setelah Tarik</span>
                                                        <strong class="text-base {{ ($item->saldo_setelah_tarik ?? 0) < 0 ? 'text-red-600' : 'text-blue-700' }}">
                                                            Rp {{ number_format($item->saldo_setelah_tarik ?? 0, 0, ',', '.') }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Rincian Pelunasan --}}
                                            @if(($item->total_pelunasan ?? 0) > 0)
                                            <div class="mb-5 bg-amber-50/30 p-4 rounded-xl border border-amber-100">
                                                <h4 class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-3">Rincian Pinjaman Yang Akan Dilunasi</h4>
                                                <div class="space-y-2">
                                                    @foreach($item->settlements as $settle)
                                                        @if($settle->pinjaman)
                                                            <div class="bg-white border border-amber-100 p-2.5 rounded-lg flex justify-between items-center hover:border-amber-200 transition-all">
                                                                <div>
                                                                    <div class="text-[11px] font-bold text-gray-800">{{ $settle->pinjaman->jenisPinjaman->nama_pinjaman ?? 'Pinjaman' }}</div>
                                                                    <div class="text-[10px] text-gray-400 font-medium">ID: LN-{{ str_pad($settle->pinjaman->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                                </div>
                                                                <div class="text-right">
                                                                    <div class="text-xs font-black text-amber-700">Rp {{ number_format($settle->pinjaman->sisa_pinjaman, 0, ',', '.') }}</div>
                                                                    <div class="text-[9px] text-gray-400 font-bold uppercase">Sisa Tagihan</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Action Buttons — Tolak hanya di modal --}}
                                            <div x-data="{ alasan: '' }" class="border-t border-gray-100 pt-5 flex flex-col gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan (Opsional)</label>
                                                    <textarea x-model="alasan" rows="2" class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm p-3" placeholder="Catatan persetujuan atau alasan penolakan..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button @click="openModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>

                                                    {{-- Tolak hanya dari modal Review --}}
                                                    <form action="{{ route('persetujuan.pengambilan.reject', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menolak penarikan ini?');">
                                                        @csrf
                                                        <input type="hidden" name="alasan" x-model="alasan">
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Tolak</button>
                                                    </form>

                                                    <form action="{{ route('persetujuan.pengambilan.approve', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menyetujui penarikan ini?');">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Setujui</button>
                                                    </form>
                                                </div>
                                            </div>

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
                            Tidak ada data pengajuan pengambilan simpanan.
                        </td>
                    </tr>
                @endforelse

                {{-- No-result row --}}
                <tr id="no-result-row-wd" style="display:none;">
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
<div id="modal-bulk-wd" class="fixed inset-0 z-[9999] items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <div class="flex justify-between items-start mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-900">Konfirmasi Persetujuan Massal</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="bulk-subtitle-wd">0 pengajuan dipilih</p>
            </div>
            <button onclick="closeBulkModalWd()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="bulk-list-wd" class="mb-4 max-h-48 overflow-y-auto bg-emerald-50 rounded-xl p-3 space-y-1 text-sm"></div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-800 flex gap-2">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>Tindakan ini akan menyetujui semua pengajuan penarikan yang dipilih secara permanen dan simpanan akan dikurangi. Untuk <strong>Tolak</strong>, gunakan tombol Review satu per satu.</span>
        </div>

        <form action="{{ route('persetujuan.pengambilan.approve.bulk') }}" method="POST" id="bulk-form-wd">
            @csrf
            <div id="bulk-hidden-inputs-wd"></div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeBulkModalWd()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95">
                    ✓ Setujui Semua
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Filter ────────────────────────────────────────────────
let activeStatusWd = 'semua';

function setStatusWd(s, btn) {
    activeStatusWd = s;
    document.querySelectorAll('.filter-tab-wd').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    applyFilterWd();
}

function applyFilterWd() {
    const q = document.getElementById('filter-search-wd').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#wd-approval-tbody tr:not(#no-result-row-wd)');
    let visible = 0;

    rows.forEach(row => {
        const rowStatus = row.dataset.status ?? 'semua';
        let statusOk = activeStatusWd === 'semua' || rowStatus === activeStatusWd;
        let searchOk = !q || row.textContent.toLowerCase().includes(q);
        const show = statusOk && searchOk;
        row.style.display = show ? '' : 'none';
        if (show) visible++;

        if (!show) {
            const cb = row.querySelector('.row-check-wd');
            if (cb) cb.checked = false;
        }
    });

    document.getElementById('no-result-row-wd').style.display = visible === 0 ? '' : 'none';

    const info = document.getElementById('filter-info-wd');
    info.textContent = (q || activeStatusWd !== 'semua')
        ? visible + ' dari ' + rows.length + ' data' : '';

    onCheckWd();
}

// ─── Checkbox & Bulk ───────────────────────────────────────
function getCheckedWd() {
    return [...document.querySelectorAll('.row-check-wd:checked')];
}

function onCheckWd() {
    const checked = getCheckedWd();
    const bar = document.getElementById('bulk-bar-wd');
    document.getElementById('bulk-count-wd').textContent = checked.length + ' dipilih';
    checked.length > 0 ? bar.classList.add('visible') : bar.classList.remove('visible');

    document.querySelectorAll('.row-check-wd').forEach(cb => {
        cb.closest('tr').classList.toggle('row-selected', cb.checked);
    });

    const allVisible = [...document.querySelectorAll('.row-check-wd')]
        .filter(cb => cb.closest('tr').style.display !== 'none');
    const chkAll = document.getElementById('check-all-wd');
    chkAll.indeterminate = checked.length > 0 && checked.length < allVisible.length;
    chkAll.checked = allVisible.length > 0 && checked.length === allVisible.length;
}

function toggleAllWd(src) {
    document.querySelectorAll('.row-check-wd').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') cb.checked = src.checked;
    });
    onCheckWd();
}

function clearSelWd() {
    document.querySelectorAll('.row-check-wd').forEach(cb => cb.checked = false);
    document.getElementById('check-all-wd').checked = false;
    onCheckWd();
}

function openBulkModalWd() {
    const checked = getCheckedWd();
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

    document.getElementById('bulk-subtitle-wd').textContent = checked.length + ' pengajuan dipilih';
    document.getElementById('bulk-list-wd').innerHTML = listHtml;
    document.getElementById('bulk-hidden-inputs-wd').innerHTML = hiddenHtml;
    document.getElementById('modal-bulk-wd').style.display = 'flex';
}

function closeBulkModalWd() {
    document.getElementById('modal-bulk-wd').style.display = 'none';
}

document.getElementById('modal-bulk-wd').addEventListener('click', function(e) {
    if (e.target === this) closeBulkModalWd();
});
</script>
@endsection
