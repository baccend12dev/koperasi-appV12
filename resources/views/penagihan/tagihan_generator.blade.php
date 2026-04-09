{{-- resources/views/penagihan/tagihan_generator.blade.php --}}
@extends('layouts.app')

@section('title', 'Penagihan Bills')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="#" class="tb-link">Dashboard</a>
    <a href="#" class="tb-link active">Tagihan Generator</a>
@endsection

@section('subbar-actions')
    <button onclick="openModalGenerate()" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; border:none; margin-right: 10px;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="7" y1="1" x2="7" y2="13"></line>
            <line x1="1" y1="7" x2="13" y2="7"></line>
        </svg>
        Generate Tagihan Gabungan
    </button>
@endsection

@section('page-title', 'Daftar Tagihan (Bills)')

@section('page-title-settings')
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.3"/>
    </svg>
@endsection

@section('subbar-search')
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari Periode..." autocomplete="off">
@endsection

@section('content')
    <div class="data-table-wrap" style="margin: 20px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="td-check">
                        <input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.row-check').forEach(c=>c.checked=this.checked)">
                    </th>
                    <th>Periode</th>
                    <th>Tanggal Generate</th>
                    <th>Status Global</th>
                    <th>Total Potongan</th>
                    <th style="text-align: right; padding-right: 20px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $item)
                <tr>
                    <td class="td-check" onclick="event.stopPropagation()">
                        <input type="checkbox" class="row-check" value="{{ $item->id }}">
                    </td>
                    <td>
                        <div class="td-name">
                            <span style="font-weight:600; color:#111827;">{{ $item->periode }}</span>
                        </div>
                    </td>
                    <td style="color:#4B5563;">{{ \Carbon\Carbon::parse($item->tgl_generate)->format('d M Y') }}</td>
                    <td>
                        @if($item->status == 'Draft')
                            <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#F3F4F6; color:#4B5563; font-weight:600;">Draft</span>
                        @elseif($item->status == 'Partial')
                            <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#FEF3C7; color:#B45309; font-weight:600;">Partial</span>
                        @else
                            <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#DEF7EC; color:#03543F; font-weight:600;">Paid</span>
                        @endif
                    </td>
                    <td style="font-weight:800; color:#111827;">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    <td style="text-align: right; padding-right: 20px;">
                        <button onclick="window.location.href='{{ route('penagihan.show', $item->id) }}'" style="border:1px solid #D1D5DB; background:#fff; color:#374151; padding:5px 10px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; margin-right:6px; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                            Detail Report
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px 16px;color:#6B7280;">Belum ada Tagihan yang ter-generate.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 15px 20px;">
            {{ $bills->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Modal Generate -->
    <div id="modalGenerate" class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <form method="POST" action="{{ route('penagihan.storeGenerate') }}" class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden mx-4 my-8 flex flex-col">
            @csrf
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-[17px] font-bold text-gray-800">Generate Tagihan Bulanan</h3>
                <button type="button" onclick="closeModalGenerate()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 bg-white overflow-y-auto flex-1">
                <p class="text-xs text-gray-500 mb-4 bg-blue-50 p-3 rounded-lg">
                    Sistem akan otomatis menghitung seluruh tagihan Simpanan (Wajib, Pokok, Sukarela) dan angsuran Pinjaman yang sedang berjalan untuk setiap anggota aktif.
                </p>

                <!-- Inputs Row -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Periode (Bulan & Tahun)</label>
                        <input type="month" name="periode" required value="{{ date('Y-m') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Tagihan Dibuat</label>
                        <input type="date" name="tanggal_generate" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="closeModalGenerate()" class="px-5 py-2.5 text-[13px] font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-[13px] font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2 cursor-pointer border border-transparent">
                    Generate Data
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const modalGenerate = document.getElementById('modalGenerate');
    
    function openModalGenerate() {
        modalGenerate.classList.remove('hidden');
        modalGenerate.classList.add('flex');
    }
    
    function closeModalGenerate() {
        modalGenerate.classList.add('hidden');
        modalGenerate.classList.remove('flex');
    }
</script>
@endpush
