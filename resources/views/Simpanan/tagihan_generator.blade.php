{{-- resources/views/Simpanan/tagihan_generator.blade.php --}}
@extends('layouts.app')

@section('title', 'Tagihan Simpanan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link">
        Simpanan Anggota
    </a>
    <a href="#" class="tb-link">
        Transaksi
    </a>
    <a href="{{ route('laporan.index') }}" class="tb-link">
        Laporan
    </a>
    <a href="#" class="tb-link active">
        Tagih Simpanan
    </a>
@endsection

{{-- ── Subbar kiri ── --}}
@section('subbar-actions')
    <button onclick="openModal()" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; border:none;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="7" y1="1" x2="7" y2="13"></line>
            <line x1="1" y1="7" x2="13" y2="7"></line>
        </svg>
        Generate Tagihan
    </button>
@endsection

@section('page-title', 'Tagihan Simpanan')

@section('page-title-settings')
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M7 1v1M7 12v1M1 7H2M12 7h1M2.5 2.5l.7.7M10.8 10.8l.7.7M2.5 11.5l.7-.7M10.8 3.2l.7-.7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        <circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.3"/>
    </svg>
@endsection

{{-- ── Subbar kanan ── --}}
@section('subbar-search')
    <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari Periode..."
        autocomplete="off"
    >
@endsection

@section('subbar-pagination')
    <span class="pag-info">
        1–3 / 3
    </span>
    <a href="#" class="pag-btn" style="opacity:.4;pointer-events:none">
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <a href="#" class="pag-btn" style="opacity:.4;pointer-events:none">
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M1 1l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
@endsection

{{-- ── Konten utama ── --}}
@section('content')
    <div class="data-table-wrap" style="margin: 20px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="td-check">
                        <input type="checkbox" id="checkAll"
                               onclick="document.querySelectorAll('.row-check').forEach(c=>c.checked=this.checked)">
                    </th>
                    <th>Periode</th>
                    <th>Tanggal Generate</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="text-align: right; padding-right: 20px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihanGenerator as $item)
                <tr>
                    <td class="td-check" onclick="event.stopPropagation()">
                        <input type="checkbox" class="row-check" value="{{ $item->id }}">
                    </td>
                    <td>
                        <div class="td-name">
                            <span style="font-weight:600; color:#111827;">{{ $item->periode }}</span>
                        </div>
                    </td>
                    <td style="color:#4B5563;">{{ \Carbon\Carbon::parse($item->tanggal_generate)->format('d M Y') }}</td>
                    <td style="color:#4B5563;">{{ $item->type }}</td>
                    <td style="font-weight:500;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>
                        @if($item->status == 'Draft')
                            <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#F3F4F6; color:#4B5563; font-weight:600;">Draft</span>
                        @elseif($item->status == 'Partial')
                            <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#FEF3C7; color:#B45309; font-weight:600;">Partial</span>
                        @else
                            <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#DEF7EC; color:#03543F; font-weight:600;">Paid</span>
                        @endif
                    </td>
                    <td style="text-align: right; padding-right: 20px;">
                        <button onclick="window.location.href='{{ route('simpanan.tagihangenerator.show', $item->id) }}'" style="border:1px solid #D1D5DB; background:#fff; color:#374151; padding:5px 10px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; margin-right:6px; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                            Detail
                        </button>
                        <button style="border:1px solid #D1D5DB; background:#fff; color:#374151; padding:5px 10px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                            Export
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px 16px;color:#6B7280;">Belum ada tagihan di-generate.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Generate Tagihan -->
    <div id="modalGenerate" class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <form method="POST" action="{{ route('simpanan.tagihangenerator.store') }}" class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden mx-4">
            @csrf
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-[17px] font-bold text-gray-800">Generate Tagihan Simpanan</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 bg-white">
                <!-- Inputs Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Periode (Bulan & Tahun)</label>
                        <input type="month" name="periode" required value="{{ date('Y-m') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Tagihan</label>
                        <input type="date" name="tanggal_generate" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">
                    </div>
                </div>

                <!-- Preview Label -->
                <div class="mb-2.5 flex items-center justify-between">
                    <span class="text-[14px] font-bold text-gray-800">Preview Data</span>
                    <span class="text-[12px] text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md font-medium border border-blue-100">{{ $anggotaAktif->count() }} Anggota Aktif</span>
                </div>

                <!-- Table Preview -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="max-h-56 overflow-y-auto">
                        <table class="w-full text-left text-[13px] whitespace-nowrap">
                            <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 sticky top-0">
                                <tr>
                                    <th class="py-2.5 px-4 w-12 text-center">
                                        <input type="checkbox" checked id="checkAllModal" onclick="document.querySelectorAll('.modal-check').forEach(c=>c.checked=this.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                    </th>
                                    <th class="py-2.5 px-4 font-bold">Nama Anggota</th>
                                    <th class="py-2.5 px-4 font-bold">Jenis Simpanan</th>
                                    <th class="py-2.5 px-4 font-bold text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 bg-white">
                                @forelse($anggotaAktif as $anggota)
                                @php
                                    $wajib = $anggota->masterSimpanan->simpanan_wajib ?? 0;
                                    $sukarela = $anggota->masterSimpanan->simpanan_sukarela ?? 0;
                                    $pokok = $anggota->masterSimpanan->simpanan_pokok ?? 0;
                                    $totalAnggota = $wajib + $sukarela + $pokok;
                                    
                                    $jenis = [];
                                    if($wajib > 0) $jenis[] = 'Wajib';
                                    if($sukarela > 0) $jenis[] = 'Sukarela';
                                    if($pokok > 0) $jenis[] = 'Pokok';
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-2.5 px-4 text-center">
                                        <input type="checkbox" name="anggota_ids[]" value="{{ $anggota->id }}" checked class="modal-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                    </td>
                                    <td class="py-2.5 px-4 font-medium text-gray-800">{{ $anggota->nama_anggota }}</td>
                                    <td class="py-2.5 px-4">{{ implode(' & ', $jenis) ?: 'Tidak Ada Setup' }}</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-gray-800">Rp {{ number_format($totalAnggota, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada anggota aktif.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 sm:gap-4">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-[13px] font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-[13px] font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2 cursor-pointer border border-transparent">
                    <svg width="15" height="15" viewBox="0 0 14 14" fill="none" class="opacity-90">
                        <path d="M7 1v1M7 12v1M1 7H2M12 7h1M2.5 2.5l.7.7M10.8 10.8l.7.7M2.5 11.5l.7-.7M10.8 3.2l.7-.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Generate
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modalGenerate');
    
    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
