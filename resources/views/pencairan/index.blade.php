{{-- resources/views/pencairan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pencairan Dana')
@section('page-title', 'Pencairan Dana')
@section('page-subtitle', 'Rekap pencairan pinjaman & penarikan simpanan yang telah disetujui')

{{-- ── Sidebar: Filter Periode ── --}}
@section('sidebar')
<div class="sd-section">
    <div class="sd-heading" style="margin-bottom:12px;font-weight:600;font-size:13px;color:#4B5563;">
        <div style="display:flex;align-items:center;gap:5px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            PERIODE
        </div>
    </div>

    {{-- Semua periode --}}
    <a href="{{ route('pencairan.index', ['tipe' => $tipe]) }}"
       class="sd-link {{ !$tahun ? 'active' : '' }}"
       style="width:100%;display:block;border-radius:6px;padding:8px 12px;margin-bottom:4px;">
        <span style="font-weight:600;">Semua Periode</span>
    </a>

    @foreach($sidebarPeriode as $yr => $months)
    <div x-data="{ open: {{ $tahun == $yr ? 'true' : 'false' }} }" style="margin-bottom:4px;">
        <button @click="open = !open"
                class="sd-link"
                style="width:100%;display:flex;justify-content:space-between;align-items:center;border-radius:6px;padding:8px 12px;background:transparent;">
            <span style="font-weight:600;">{{ $yr }}</span>
            <svg :class="open ? 'transform rotate-180' : ''" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform duration-200">
                <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div x-show="open" x-collapse style="margin-left:12px;margin-top:4px;display:flex;flex-direction:column;gap:2px;">
            @foreach($months as $m)
            <a href="{{ route('pencairan.index', ['tahun' => $yr, 'bulan' => $m, 'tipe' => $tipe]) }}"
               class="sd-link {{ ($tahun == $yr && $bulan == $m) ? 'active' : '' }}"
               style="padding:6px 12px;font-size:13px;">
                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
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
    .stats-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; }
    .stat-card-dark {
        background:#0B1727; border-radius:12px; padding:22px;
        color:#fff; position:relative; overflow:hidden;
    }
    .stat-card-light {
        background:#fff; border-radius:12px; padding:22px;
        box-shadow:0 1px 3px rgba(0,0,0,.05); border:1px solid #f1f5f9;
        position:relative; overflow:hidden;
    }
    .badge { display:inline-flex; align-items:center; padding:2px 9px; border-radius:12px; font-size:11px; font-weight:600; }
    .badge-pinjaman { background:#EEF2FF; color:#4F46E5; }
    .badge-simpanan  { background:#FEF3C7; color:#D97706; }
    .badge-paid      { background:#D1FAE5; color:#059669; }
    .badge-pending   { background:#FEF3C7; color:#D97706; }
    .filter-tab { display:inline-flex; align-items:center; gap:4px; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; transition:all .15s; text-decoration:none; }
    .filter-tab.active  { background:#4F46E5; color:#fff; }
    .filter-tab:not(.active) { background:#F3F4F6; color:#6B7280; }
    .filter-tab:not(.active):hover { background:#E5E7EB; }
</style>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card-dark">
        <div class="text-gray-400 text-xs font-bold tracking-wider mb-2">TOTAL PENCAIRAN</div>
        <div class="text-2xl font-bold text-white">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
        <div class="absolute right-5 top-1/2 -translate-y-1/2 bg-white/10 p-3 rounded-xl">
            <svg class="w-7 h-7 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>
    <div class="stat-card-light">
        <div class="text-xs font-bold tracking-wider text-indigo-600 mb-1">PENCAIRAN PINJAMAN</div>
        <div class="text-2xl font-bold text-indigo-700">Rp {{ number_format($totalNominalPinjaman, 0, ',', '.') }}</div>
        <div class="absolute right-5 top-1/2 -translate-y-1/2 bg-indigo-50 p-3 rounded-xl">
            <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
    </div>
    <div class="stat-card-light">
        <div class="text-xs font-bold tracking-wider text-amber-600 mb-1">PENCAIRAN SIMPANAN</div>
        <div class="text-2xl font-bold text-amber-600">Rp {{ number_format($totalNominalPengambilan, 0, ',', '.') }}</div>
        <div class="absolute right-5 top-1/2 -translate-y-1/2 bg-amber-50 p-3 rounded-xl">
            <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <h3 class="font-bold text-sm text-gray-800 tracking-wide">
            DAFTAR PENCAIRAN
            @if($tahun)
                <span class="ml-2 text-xs text-gray-400 font-normal">
                    {{ $tahun }}@if($bulan) / {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}@endif
                </span>
            @endif
        </h3>

        {{-- Filter Tipe --}}
        <div class="flex items-center gap-2">
            @foreach(['all' => 'Semua', 'pinjaman' => 'Pinjaman', 'simpanan' => 'Penarikan'] as $val => $label)
                <a href="{{ route('pencairan.index', array_merge(request()->only(['tahun','bulan']), ['tipe' => $val])) }}"
                   class="filter-tab {{ $tipe === $val ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 min-w-max">
            <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">TANGGAL APPROVE</th>
                    <th class="px-6 py-4">ANGGOTA</th>
                    <th class="px-6 py-4">JENIS</th>
                    <th class="px-6 py-4">KETERANGAN</th>
                    <th class="px-6 py-4 text-right">NOMINAL</th>
                    <th class="px-6 py-4 text-center">STATUS BAYAR</th>
                    <th class="px-6 py-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pencairanList as $row)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-700 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $row->ref_type === 'pinjaman' ? 'bg-indigo-100 text-indigo-600' : 'bg-amber-100 text-amber-600' }}">
                                {{ strtoupper(substr($row->anggota?->nama_anggota ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">{{ $row->anggota?->nama_anggota ?? '-' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $row->anggota?->nik ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($row->ref_type === 'pinjaman')
                            <span class="badge badge-pinjaman">PINJAMAN</span>
                        @else
                            <span class="badge badge-simpanan">PENARIKAN</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $row->keterangan }}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800 whitespace-nowrap">
                        Rp {{ number_format($row->nominal, 0, ',', '.') }}
                    </td>

                    {{-- Status Bayar --}}
                    <td class="px-6 py-4 text-center">
                        @if($row->pencairan && $row->pencairan->status === 'paid')
                            <span class="badge badge-paid">LUNAS</span>
                            <div class="text-[10px] text-gray-400 mt-1">{{ ucfirst($row->pencairan->metode) }} · {{ \Carbon\Carbon::parse($row->pencairan->tanggal)->format('d M Y') }}</div>
                        @else
                            <span class="badge badge-pending">BELUM BAYAR</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-center">
                        @if($row->pencairan && $row->pencairan->status === 'paid')
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
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $row->anggota?->nama_anggota }} · {{ $row->keterangan }}</p>
                                            </div>
                                            <button @click="openBayar = false" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <div class="bg-emerald-50 rounded-xl p-4 mb-5 text-center">
                                            <div class="text-xs text-emerald-600 font-semibold mb-1">NOMINAL PENCAIRAN</div>
                                            <div class="text-2xl font-bold text-emerald-700">Rp {{ number_format($row->nominal, 0, ',', '.') }}</div>
                                        </div>

                                        <form action="{{ route('pencairan.bayar') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="ref_type"   value="{{ $row->ref_type }}">
                                            <input type="hidden" name="ref_id"     value="{{ $row->id }}">
                                            <input type="hidden" name="anggota_id" value="{{ $row->anggota_id }}">
                                            <input type="hidden" name="nominal"    value="{{ $row->nominal }}">

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                                           class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode</label>
                                                    <select name="metode" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500" required>
                                                        <option value="transfer">Transfer</option>
                                                        <option value="cash">Cash</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                                                <input type="text" name="keterangan" placeholder="No. ref / catatan..."
                                                       class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
                                            </div>
                                            <div class="flex justify-end gap-3 pt-2">
                                                <button @click.prevent="openBayar = false" type="button"
                                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                                <button type="submit"
                                                    class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Simpan & Tandai Lunas</button>
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
                    <td colspan="7" class="px-6 py-14 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tidak ada data pencairan untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Jumlah baris info --}}
    @if($pencairanList->count() > 0)
    <div class="px-6 py-3 border-t border-gray-100 text-xs text-gray-400 text-right">
        Total {{ $pencairanList->count() }} transaksi &bull;
        <span class="font-semibold text-gray-600">Rp {{ number_format($totalNominal, 0, ',', '.') }}</span>
    </div>
    @endif
</div>

</div>
@endsection
