{{--
    resources/views/laporan/simpanan.blade.php
    Laporan Saldo Simpanan — Sub-menu Laporan Saldo Simpanan Anggota (Akumulasi Riil)
--}}
@extends('laporan.layout')

@section('laporan-title', 'Laporan Saldo Simpanan')
@section('laporan-subtitle')
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 12px; flex-wrap: wrap;">
        <span>Laporan akumulasi saldo simpanan riil dan saldo awal seluruh anggota koperasi</span>
        <button type="button" onclick="submitExport()" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border:1px solid #10B981;border-radius:6px;background:#10B981;font-size:11px;font-weight:600;color:#fff;cursor:pointer;transition:background-color 0.15s, border-color 0.15s; outline: none; text-decoration: none; font-family: inherit;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export Excel
        </button>
    </div>
@endsection

@section('subbar-pagination')
    @if($members->hasPages())
        <span class="pag-info" style="font-size:11px;color:#6B7280;margin-right:6px;">
            {{ $members->firstItem() }}–{{ $members->lastItem() }} / {{ $members->total() }}
        </span>
        <a href="{{ $members->previousPageUrl() ?? '#' }}"
           class="pag-btn" {!! $members->onFirstPage() ? 'style="opacity:.4;pointer-events:none"' : '' !!} style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border:1px solid #D1D5DB;border-radius:5px;color:#374151;text-decoration:none;margin-right:3px;">
            <svg width="6" height="10" viewBox="0 0 7 12" fill="none">
                <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <a href="{{ $members->nextPageUrl() ?? '#' }}"
           class="pag-btn" {!! !$members->hasMorePages() ? 'style="opacity:.4;pointer-events:none"' : '' !!} style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border:1px solid #D1D5DB;border-radius:5px;color:#374151;text-decoration:none;">
            <svg width="6" height="10" viewBox="0 0 7 12" fill="none">
                <path d="M1 1l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    @endif
@endsection

@section('laporan-content')
    {{-- Header untuk Cetak --}}
    <div class="print-header">
        <h1>Koperasi Karyawan OPI</h1>
        <p>Laporan Saldo Akumulasi Simpanan Anggota</p>
        <p style="font-size: 11px; margin-top: 2px;">Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <hr style="margin: 12px 0; border: 0; border-top: 2px double #D1D5DB;">
    </div>

    {{-- Filter Card (Compact) --}}
    <div class="filter-card" style="background: #ffffff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('laporan.simpanan') }}" id="filterForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 150px; gap: 12px; align-items: flex-end;">
            <!-- Search -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Cari Anggota</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama atau NIK..." style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; outline: none; box-sizing: border-box;">
            </div>
            
            <!-- Departemen -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Departemen</label>
                <select name="departemen_id" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; background: #fff; outline: none; box-sizing: border-box; cursor: pointer;">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>{{ $dept->nama }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Status Anggota</label>
                <select name="status_anggota" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; background: #fff; outline: none; box-sizing: border-box; cursor: pointer;">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status_anggota') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-aktif" {{ request('status_anggota') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 6px; box-sizing: border-box;">
                <button type="submit" style="flex: 1; height: 32px; background: #1D4ED8; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: background 0.15s;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Filter
                </button>
                <a href="{{ route('laporan.simpanan') }}" style="height: 32px; width: 32px; background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; text-decoration: none; transition: background 0.15s; box-sizing: border-box;" title="Reset Filter">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Stats Cards Row (Compact & Thinner Borders) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px;">
        <!-- Pokok -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #3B82F6;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Simpanan Pokok</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumPokok, 0, ',', '.') }}</div>
        </div>
        <!-- Wajib -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #10B981;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Simpanan Wajib</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumWajib, 0, ',', '.') }}</div>
        </div>
        <!-- Sukarela -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #F59E0B;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Simpanan Sukarela</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumSukarela, 0, ',', '.') }}</div>
        </div>
        <!-- Saldo Awal -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #6B7280;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Saldo Awal</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumSaldoAwal, 0, ',', '.') }}</div>
        </div>
        <!-- Grand Total -->
        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #1D4ED8;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #1E40AF; letter-spacing: 0.05em; margin-bottom: 2px;">Total Saldo Simpanan</div>
            <div style="font-size: 15px; font-weight: 800; color: #1D4ED8;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Data Table (Compact Padding) --}}
    <div class="data-table-wrap" style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow-x: auto;">
        <table class="data-table excel-style-table" style="width: 100%; border-collapse: collapse; text-align: left; min-width: 900px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
            <thead>
                <tr style="background: #107C41; color: #FFFFFF; border-bottom: 2px solid #0B5C30;">
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; width: 35px; text-align: center;">No</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; width: 170px;">Nama & NIK</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; width: 110px;">Departemen</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; text-align: right; width: 115px;">Simpanan Pokok</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; text-align: right; width: 115px;">Simpanan Wajib</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; text-align: right; width: 115px;">Simpanan Sukarela</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; text-align: right; width: 115px;">Saldo Awal</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; text-align: right; width: 125px;">Total Simpanan</th>
                    <th style="padding: 6px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #0E6B37; text-align: center; width: 75px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $item)
                    @php
                        $pokok = ($item->total_pokok ?? 0) + ($item->saldo_awal_pokok ?? 0);
                        $wajib = ($item->total_wajib ?? 0) + ($item->saldo_awal_wajib ?? 0);
                        $sukarela = ($item->total_sukarela ?? 0) + ($item->saldo_awal_sukarela ?? 0);
                        $saldoAwal = $item->total_saldo_awal ?? 0;
                        $total = $pokok + $wajib + $sukarela;
                        $rowBg = $index % 2 === 0 ? '#FFFFFF' : '#F9FAFB';
                    @endphp
                    <tr style="background-color: {{ $rowBg }}; border-bottom: 1px solid #E5E7EB; transition: background-color 0.1s;">
                        <td style="padding: 5px 8px; font-size: 11px; color: #000000; font-weight: 600; text-align: center; border: 1px solid #E5E7EB; vertical-align: middle;">{{ $members->firstItem() + $index }}</td>
                        <td style="padding: 5px 8px; border: 1px solid #E5E7EB; vertical-align: middle;">
                            <div style="font-weight: 700; color: #000000; font-size: 11px; line-height: 1.2;">{{ $item->nama_anggota }}</div>
                            <div style="font-size: 10px; color: #4B5563; font-weight: 600; margin-top: 1px;">NIK: {{ $item->nik ?? '—' }}</div>
                        </td>
                        <td style="padding: 5px 8px; font-size: 11px; color: #000000; font-weight: 600; border: 1px solid #E5E7EB; vertical-align: middle;">{{ $item->departemen->nama ?? '—' }}</td>
                        <td style="padding: 5px 8px; font-size: 11px; color: {{ $pokok < 0 ? '#DC2626' : '#000000' }}; font-weight: 600; text-align: right; border: 1px solid #E5E7EB; font-variant-numeric: tabular-nums; vertical-align: middle;">
                            {{ number_format($pokok, 2, ',', '.') }}
                        </td>
                        <td style="padding: 5px 8px; font-size: 11px; color: {{ $wajib < 0 ? '#DC2626' : '#000000' }}; font-weight: 600; text-align: right; border: 1px solid #E5E7EB; font-variant-numeric: tabular-nums; vertical-align: middle;">
                            {{ number_format($wajib, 2, ',', '.') }}
                        </td>
                        <td style="padding: 5px 8px; font-size: 11px; color: {{ $sukarela < 0 ? '#DC2626' : '#000000' }}; font-weight: 600; text-align: right; border: 1px solid #E5E7EB; font-variant-numeric: tabular-nums; vertical-align: middle;">
                            {{ number_format($sukarela, 2, ',', '.') }}
                        </td>
                        <td style="padding: 5px 8px; font-size: 11px; color: #000000; font-weight: 600; text-align: right; border: 1px solid #E5E7EB; font-variant-numeric: tabular-nums; vertical-align: middle;">
                            {{ number_format($saldoAwal, 2, ',', '.') }}
                        </td>
                        <td style="padding: 5px 8px; font-size: 11px; font-weight: 800; color: {{ $total < 0 ? '#DC2626' : '#1E40AF' }}; text-align: right; border: 1px solid #E5E7EB; font-variant-numeric: tabular-nums; vertical-align: middle; background-color: rgba(30, 64, 175, 0.04);">
                            {{ number_format($total, 2, ',', '.') }}
                        </td>
                        <td style="padding: 5px 8px; text-align: center; border: 1px solid #E5E7EB; vertical-align: middle;">
                            @if($item->status_anggota == 'active' || $item->status_anggota == 'aktif')
                                <span style="display:inline-block; padding:2px 6px; border-radius:4px; font-size: 10px; background-color:#DEF7EC; color:#03543F; font-weight:800; text-transform: capitalize;">Aktif</span>
                            @else
                                <span style="display:inline-block; padding:2px 6px; border-radius:4px; font-size: 10px; background-color:#FDE8E8; color:#9B1C1C; font-weight:800; text-transform: capitalize;">{{ $item->status_anggota }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                         <td colspan="9" style="text-align:center; padding:20px 8px; color:#374151; font-weight: 600; font-size:12px; vertical-align: middle;">
                             Tidak ada data saldo simpanan ditemukan.
                         </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination (Bottom of table) --}}
        @if($members->hasPages())
            <div class="pagination-wrapper" style="padding: 10px 14px; border-top: 1px solid #E5E7EB; background: #fff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="font-size: 11px; color: #6B7280;">
                    Menampilkan {{ $members->firstItem() }} sampai {{ $members->lastItem() }} dari {{ $members->total() }} data
                </div>
                <div class="pagination-links" style="font-size: 12px;">
                    {{ $members->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    /* Table row hover effect */
    .data-table tbody tr:hover {
        background-color: #F9FAFB !important;
    }
    
    /* Print specific styling */
    .print-header {
        display: none;
    }
    
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
            padding: 0 !important;
        }
        #topbar, #sidebar, #subbar, .filter-card, .sb-pagination, .pag-btn, .pagination-wrapper, form, button, a {
            display: none !important;
        }
        .data-table-wrap {
            box-shadow: none !important;
            border: none !important;
            overflow: visible !important;
        }
        .data-table {
            border: 1px solid #D1D5DB !important;
            width: 100% !important;
            min-width: unset !important;
        }
        .data-table th, .data-table td {
            border: 1px solid #E5E7EB !important;
            padding: 6px 8px !important;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 16px;
        }
        .print-header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        .print-header p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #4B5563;
        }
    }
</style>
@endpush

@push('laporan-scripts')
<script>
    function submitExport() {
        const form = document.getElementById('filterForm');
        if (!form) return;
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export';
        input.value = 'excel';
        form.appendChild(input);
        
        form.submit();
        
        setTimeout(() => {
            if (input.parentNode) {
                input.parentNode.removeChild(input);
            }
        }, 100);
    }
</script>
@endpush
