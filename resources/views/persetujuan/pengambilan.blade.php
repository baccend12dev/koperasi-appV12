{{-- resources/views/persetujuan/pengambilan.blade.php --}}
@extends('layouts.app')

@section('title', 'Persetujuan Pengambilan Simpanan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('persetujuan.pinjaman') }}" class="tb-link">Persetujuan Pinjaman</a>
    <a href="{{ route('persetujuan.pengambilan') }}" class="tb-link active">Persetujuan Pengambilan Simpanan</a>
@endsection

@section('page-title', 'Persetujuan Pengambilan Simpanan')
@section('page-subtitle', 'Daftar pengajuan penarikan simpanan anggota')

@section('content')
<div class="px-6 py-4 space-y-6 max-w-7xl mx-auto">

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
            <div class="text-gray-400 text-xs font-bold tracking-wider mb-2">TOTAL NOMINAL PENARIKAN PENDING</div>
            <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/10 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        
        <div class="stat-card-light">
            <div class="flex items-center gap-2 mb-2">
                <div class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded leading-tight">TOTAL PENGAMBILAN PENDING</div>
            </div>
            <div class="text-4xl font-bold text-amber-600 mb-2">{{ number_format($totalPengambilan, 0, ',', '.') }}</div>
            
            <div class="absolute right-6 top-1/2 -translate-y-1/2 bg-amber-50 text-amber-600 p-3 rounded-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 border border-green-200 p-4 rounded-xl text-sm font-semibold mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 border border-red-200 p-4 rounded-xl text-sm font-semibold mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Table Section --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3">
            <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR PENGAJUAN PENGAMBILAN SIMPANAN</h3>
            {{-- Filter Tabs --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'Semua'] as $val => $label)
                    <a href="{{ route('persetujuan.pengambilan', ['status' => $val]) }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                           {{ $filterStatus === $val
                               ? 'bg-white shadow text-gray-800'
                               : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 min-w-max">
                <thead class="bg-gray-50/50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">TANGGAL</th>
                        <th class="px-6 py-4">NAMA ANGGOTA</th>
                        <th class="px-6 py-4">NOMINAL TARIKAN</th>
                        <th class="px-6 py-4">ALASAN</th>
                        <th class="px-6 py-4 text-center">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengambilan_list as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-700">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs" style="flex-shrink:0;">
                                        {{ strtoupper(substr($item->anggota->nama_anggota ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $item->anggota->nama_anggota }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $item->anggota->nik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800 text-base">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->alasan_pengajuan }}">
                                    {{ $item->alasan_pengajuan }}
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
                                @if($item->status == 'pending')
                                <div x-data="{ openModal: false }" class="relative">
                                    <button @click="openModal = true" class="btn-approve" style="background:#EEF2FF;color:#4F46E5;border-color:#C7D2FE;" title="Review Pengajuan">Review Detail</button>

                                    <template x-teleport="body">
                                        <div x-show="openModal" class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm p-4" x-transition.opacity="" style="display: none;">
                                            <div @click.away="openModal = false" class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden p-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                                                {{-- Header --}}
                                                <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-5">
                                                    <div>
                                                        <h3 class="text-lg font-bold text-gray-900">Review Penarikan Simpanan</h3>
                                                        <p class="text-sm text-gray-500 mt-1">{{ $item->anggota->nama_anggota }} ({{ $item->anggota->nik }})</p>
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
                                                            <span class="block text-[11px] text-gray-400">Nominal Penarikan</span>
                                                            <strong class="text-base text-gray-800">Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Tanggal Pengajuan</span>
                                                            <strong class="text-sm text-gray-800">{{ $item->created_at->format('d M Y') }}</strong>
                                                        </div>
                                                        @if($item->alasan_pengajuan)
                                                        <div>
                                                            <span class="block text-[11px] text-gray-400">Alasan Pengajuan</span>
                                                            <strong class="text-sm text-gray-700">{{ $item->alasan_pengajuan }}</strong>
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

                                                {{-- Keterangan & Actions --}}
                                                <div x-data="{ alasan: '' }" class="border-t border-gray-100 pt-5 flex flex-col gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan (Opsional)</label>
                                                        <textarea x-model="alasan" rows="2" class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm p-3" placeholder="Masukkan catatan persetujuan atau alasan penolakan..."></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-3">
                                                        <button @click="openModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>

                                                        <form action="{{ route('persetujuan.pengambilan.reject', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menolak penarikan ini?');">
                                                            @csrf
                                                            <input type="hidden" name="alasan" x-model="alasan">
                                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-100">Tolak</button>
                                                        </form>

                                                        <form action="{{ route('persetujuan.pengambilan.approve', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menyetujui penarikan ini?');">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-100">Setujui</button>
                                                        </form>
                                                    </div>
                                                </div>

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
                                Tidak ada data pengajuan pengambilan simpanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pengambilan_list->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Menampilkan {{ $pengambilan_list->firstItem() }}–{{ $pengambilan_list->lastItem() }} dari {{ $pengambilan_list->total() }} data
            </div>
            <div class="flex items-center gap-1">
                @if($pengambilan_list->onFirstPage())
                    <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                @else
                    <a href="{{ $pengambilan_list->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">&laquo;</a>
                @endif

                @foreach($pengambilan_list->getUrlRange(max(1, $pengambilan_list->currentPage()-2), min($pengambilan_list->lastPage(), $pengambilan_list->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-1.5 text-xs rounded-lg border {{ $page == $pengambilan_list->currentPage() ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($pengambilan_list->hasMorePages())
                    <a href="{{ $pengambilan_list->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">&raquo;</a>
                @else
                    <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
