{{--
    resources/views/laporan/transaksi_simpanan.blade.php
    Laporan Transaksi Simpanan — Sub-menu Laporan Transaksi Simpanan Anggota (dari Transaksi Simpanan)
--}}
@extends('laporan.layout')

@section('laporan-title', 'Laporan Transaksi Simpanan')
@section('laporan-subtitle')
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 12px; flex-wrap: wrap;">
        <span>Laporan rincian transaksi simpanan anggota per periode</span>
        <button type="submit" form="filterForm" name="export" value="excel" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border:1px solid #10B981;border-radius:6px;background:#10B981;font-size:11px;font-weight:600;color:#fff;cursor:pointer;transition:background-color 0.15s, border-color 0.15s; outline: none; text-decoration: none; font-family: inherit;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export Excel
        </button>
    </div>
@endsection

@section('subbar-pagination')
    @if($transaksi->hasPages())
        <span class="pag-info" style="font-size:11px;color:#6B7280;margin-right:6px;">
            {{ $transaksi->firstItem() }}–{{ $transaksi->lastItem() }} / {{ $transaksi->total() }}
        </span>
        <a href="{{ $transaksi->previousPageUrl() ?? '#' }}"
           class="pag-btn" {!! $transaksi->onFirstPage() ? 'style="opacity:.4;pointer-events:none"' : '' !!} style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border:1px solid #D1D5DB;border-radius:5px;color:#374151;text-decoration:none;margin-right:3px;">
            <svg width="6" height="10" viewBox="0 0 7 12" fill="none">
                <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <a href="{{ $transaksi->nextPageUrl() ?? '#' }}"
           class="pag-btn" {!! !$transaksi->hasMorePages() ? 'style="opacity:.4;pointer-events:none"' : '' !!} style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border:1px solid #D1D5DB;border-radius:5px;color:#374151;text-decoration:none;">
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
        <p>Laporan Rincian Transaksi Simpanan Anggota</p>
        @if($periode)
            <p style="font-weight: 600; font-size: 11px;">
                Periode: {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}
            </p>
        @endif
        <p style="font-size: 11px; margin-top: 2px;">Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <hr style="margin: 12px 0; border: 0; border-top: 2px double #D1D5DB;">
    </div>

    {{-- Filter Card (Compact) --}}
    <div class="filter-card" style="background: #ffffff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('laporan.transaksi_simpanan') }}" id="filterForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 150px; gap: 12px; align-items: flex-end;">
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

            <!-- Periode -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Periode (Bulan & Tahun)</label>
                <input type="month" name="periode" value="{{ $periode }}" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; outline: none; box-sizing: border-box; background: #fff; cursor: pointer;">
            </div>

            <!-- Jenis Transaksi -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Jenis Transaksi</label>
                <select name="jenis" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; background: #fff; outline: none; box-sizing: border-box; cursor: pointer;">
                    <option value="">Semua Jenis</option>
                    <option value="bulanan" {{ request('jenis') == 'bulanan' ? 'selected' : '' }}>Simpanan Bulanan (Payroll)</option>
                    <option value="langsung" {{ request('jenis') == 'langsung' ? 'selected' : '' }}>Simpanan Langsung</option>
                    <option value="penarikan" {{ request('jenis') == 'penarikan' ? 'selected' : '' }}>Penarikan Simpanan</option>
                </select>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 6px; box-sizing: border-box;">
                <button type="submit" style="flex: 1; height: 32px; background: #1D4ED8; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: background 0.15s;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Filter
                </button>
                <a href="{{ route('laporan.transaksi_simpanan') }}" style="height: 32px; width: 32px; background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; text-decoration: none; transition: background 0.15s; box-sizing: border-box;" title="Reset Filter">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Stats Cards Row (Compact & Thinner Borders) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 16px;">
        <!-- Pokok -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #3B82F6;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Total Pokok Terbayar</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumPokok, 0, ',', '.') }}</div>
        </div>
        <!-- Wajib -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #10B981;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Total Wajib Terbayar</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumWajib, 0, ',', '.') }}</div>
        </div>
        <!-- Sukarela -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #F59E0B;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Total Sukarela Terbayar</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumSukarela, 0, ',', '.') }}</div>
        </div>
        <!-- Grand Total -->
        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #1D4ED8;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #1E40AF; letter-spacing: 0.05em; margin-bottom: 2px;">Total Transaksi Terbayar</div>
            <div style="font-size: 15px; font-weight: 800; color: #1D4ED8;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Data Table (Compact Padding) --}}
    <div class="data-table-wrap" style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow-x: auto;">
        <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left; min-width: 950px;">
            <thead>
                <tr style="background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 40px;">No</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 100px;">Tgl Transaksi</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 180px;">Nama & NIK</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 110px;">Departemen</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 90px;">Periode</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563;">Keterangan</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 100px;">Pokok</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 100px;">Wajib</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 100px;">Sukarela</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 110px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $index => $item)
                    @php
                        $pokok = $item->simpanan_pokok ?? 0;
                        $wajib = $item->simpanan_wajib ?? 0;
                        $sukarela = $item->simpanan_sukarela ?? 0;
                        $total = $pokok + $wajib + $sukarela;
                    @endphp
                    <tr style="border-bottom: 1px solid #E5E7EB; transition: background-color 0.1s;">
                        <td style="padding: 10px 12px; font-size: 12px; color: #4B5563; vertical-align: middle;">{{ $transaksi->firstItem() + $index }}</td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; vertical-align: middle;">{{ $item->transaction_date }}</td>
                        <td style="padding: 10px 12px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #111827; font-size: 12px;">{{ $item->anggota->nama_anggota ?? '—' }}</div>
                            <div style="font-size: 10px; color: #6B7280; margin-top: 1px;">{{ $item->anggota->nik ?? '—' }}</div>
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; vertical-align: middle;">{{ $item->anggota->departemen->nama ?? '—' }}</td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; vertical-align: middle;">{{ $item->periode }}</td>
                        <td style="padding: 10px 12px; font-size: 11px; color: #6B7280; vertical-align: middle; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $item->description ?? '—' }}
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; text-align: right; font-variant-numeric: tabular-nums; vertical-align: middle; {!! $pokok < 0 ? 'color: #DC2626;' : '' !!}">
                            {{ number_format($pokok, 2, ',', '.') }}
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; text-align: right; font-variant-numeric: tabular-nums; vertical-align: middle; {!! $wajib < 0 ? 'color: #DC2626;' : '' !!}">
                            {{ number_format($wajib, 2, ',', '.') }}
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; text-align: right; font-variant-numeric: tabular-nums; vertical-align: middle; {!! $sukarela < 0 ? 'color: #DC2626;' : '' !!}">
                            {{ number_format($sukarela, 2, ',', '.') }}
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; font-weight: 700; color: #1D4ED8; text-align: right; font-variant-numeric: tabular-nums; vertical-align: middle; {!! $total < 0 ? 'color: #DC2626;' : '' !!}">
                            {{ number_format($total, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                         <td colspan="10" style="text-align:center; padding:30px 12px; color:#6B7280; font-size:13px; vertical-align: middle;">
                             Tidak ada data transaksi simpanan ditemukan.
                         </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
        #topbar, #sidebar, #subbar, .filter-card, .sb-pagination, .pag-btn, form, button, a {
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
