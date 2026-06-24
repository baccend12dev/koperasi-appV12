{{--
    resources/views/laporan/pinjaman.blade.php
    Laporan Pinjaman — Sub-menu Laporan Pinjaman Anggota
--}}
@extends('laporan.layout')

@section('laporan-title', 'Laporan Pinjaman')
@section('laporan-subtitle')
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 12px; flex-wrap: wrap;">
        <span>Laporan rincian pinjaman anggota yang masih berjalan/aktif</span>
        <button type="button" onclick="submitExport()" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border:1px solid #10B981;border-radius:6px;background:#10B981;font-size:11px;font-weight:600;color:#fff;cursor:pointer;transition:background-color 0.15s, border-color 0.15s; outline: none; text-decoration: none; font-family: inherit;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export Excel
        </button>
    </div>
@endsection

@section('subbar-pagination')
    @if($pinjaman->hasPages())
        <span class="pag-info" style="font-size:11px;color:#6B7280;margin-right:6px;">
            {{ $pinjaman->firstItem() }}–{{ $pinjaman->lastItem() }} / {{ $pinjaman->total() }}
        </span>
        <a href="{{ $pinjaman->previousPageUrl() ?? '#' }}"
           class="pag-btn" {!! $pinjaman->onFirstPage() ? 'style="opacity:.4;pointer-events:none"' : '' !!} style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border:1px solid #D1D5DB;border-radius:5px;color:#374151;text-decoration:none;margin-right:3px;">
            <svg width="6" height="10" viewBox="0 0 7 12" fill="none">
                <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <a href="{{ $pinjaman->nextPageUrl() ?? '#' }}"
           class="pag-btn" {!! !$pinjaman->hasMorePages() ? 'style="opacity:.4;pointer-events:none"' : '' !!} style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border:1px solid #D1D5DB;border-radius:5px;color:#374151;text-decoration:none;">
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
        <p>Laporan Rincian Pinjaman Anggota</p>
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
        <form method="GET" action="{{ route('laporan.pinjaman') }}" id="filterForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 150px; gap: 12px; align-items: flex-end;">
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
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Periode (Mulai Pinjaman)</label>
                <input type="month" name="periode" value="{{ $periode }}" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; outline: none; box-sizing: border-box; background: #fff; cursor: pointer;">
            </div>

            <!-- Jenis Pinjaman -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Jenis Pinjaman</label>
                <select name="jenis" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; background: #fff; outline: none; box-sizing: border-box; cursor: pointer;">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisPinjamanList as $jp)
                        @if($jp->children->count() > 0)
                            <optgroup label="{{ $jp->nama_pinjaman }}">
                                @foreach($jp->children as $child)
                                    <option value="{{ $child->id }}" {{ request('jenis') == $child->id ? 'selected' : '' }}>{{ $child->nama_pinjaman }}</option>
                                @endforeach
                            </optgroup>
                        @else
                            <option value="{{ $jp->id }}" {{ request('jenis') == $jp->id ? 'selected' : '' }}>{{ $jp->nama_pinjaman }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label style="display: block; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px; letter-spacing: 0.05em;">Status</label>
                <select name="status" style="width: 100%; height: 32px; padding: 6px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; color: #1F2937; background: #fff; outline: none; box-sizing: border-box; cursor: pointer;">
                    <option value="semua" {{ $status == 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="berjalan" {{ $status == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="lunas" {{ $status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 6px; box-sizing: border-box;">
                <button type="submit" style="flex: 1; height: 32px; background: #1D4ED8; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: background 0.15s;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Filter
                </button>
                <a href="{{ route('laporan.pinjaman') }}" style="height: 32px; width: 32px; background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; text-decoration: none; transition: background 0.15s; box-sizing: border-box;" title="Reset Filter">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Stats Cards Row (Compact & Thinner Borders) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 16px;">
        <!-- Pokok -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #3B82F6;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Total Pokok Pinjaman</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumPokok, 0, ',', '.') }}</div>
        </div>
        <!-- Tagihan (Pokok + Bunga) -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #8B5CF6;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Total Pinjaman + Bunga</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumTotal, 0, ',', '.') }}</div>
        </div>
        <!-- Terbayar -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #10B981;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #6B7280; letter-spacing: 0.05em; margin-bottom: 2px;">Total Telah Terbayar</div>
            <div style="font-size: 15px; font-weight: 800; color: #1F2937;">Rp {{ number_format($sumTerbayar, 0, ',', '.') }}</div>
        </div>
        <!-- Outstanding -->
        <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-top: 3px solid #F59E0B;">
            <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; color: #B45309; letter-spacing: 0.05em; margin-bottom: 2px;">Sisa Pinjaman (Outstanding)</div>
            <div style="font-size: 15px; font-weight: 800; color: #B45309;">Rp {{ number_format($sumSisa, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Data Table (Compact Padding) --}}
    <div class="data-table-wrap" style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow-x: auto;">
        <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left; min-width: 1000px;">
            <thead>
                <tr style="background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 40px;">No</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 140px;">Periode Kontrak</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 180px;">Nama & NIK</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 100px;">Departemen</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; width: 120px;">Jenis Pinjaman</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 120px;">Pinjaman (Pokok / Total)</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: center; width: 110px;">Tenor (Total / Sisa)</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 110px;">Terbayar</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: right; width: 110px;">Sisa Tagihan</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: center; width: 80px;">Status</th>
                    <th style="padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; text-align: center; width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pinjaman as $index => $item)
                    @php
                        $pokok = $item->jumlah_pinjaman ?? 0;
                        $total_kontrak = $item->total_pinjaman ?? 0;
                        $terbayar = $item->total_terbayar ?? 0;
                        $sisa = $item->status == 'lunas' ? 0 : ($item->sisa_pinjaman ?? 0);
                    @endphp
                    <tr style="border-bottom: 1px solid #E5E7EB; transition: background-color 0.1s;">
                        <td style="padding: 10px 12px; font-size: 12px; color: #4B5563; vertical-align: middle;">{{ $pinjaman->firstItem() + $index }}</td>
                        <td style="padding: 10px 12px; font-size: 11px; color: #374151; vertical-align: middle; line-height: 1.3;">
                            <div style="font-weight: 600;">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '—' }}</div>
                            <div style="color: #6B7280; font-size: 10px; margin-top: 1px;">s.d. {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '—' }}</div>
                        </td>
                        <td style="padding: 10px 12px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #111827; font-size: 12px;">{{ $item->anggota->nama_anggota ?? '—' }}</div>
                            <div style="font-size: 10px; color: #6B7280; margin-top: 1px;">{{ $item->anggota->nik ?? '—' }}</div>
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; vertical-align: middle;">{{ $item->anggota->departemen->nama ?? '—' }}</td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; vertical-align: middle;">
                            {{ $item->jenisPinjaman?->nama_pinjaman ?? '—' }}
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; text-align: right; vertical-align: middle; line-height: 1.3;">
                            <div style="font-weight: 600;">Rp {{ number_format($pokok, 0, ',', '.') }}</div>
                            <div style="color: #6B7280; font-size: 10px;">Total: Rp {{ number_format($total_kontrak, 0, ',', '.') }}</div>
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; color: #374151; text-align: center; vertical-align: middle; line-height: 1.3;">
                            <div style="font-weight: 600;">{{ $item->tenor }} Bln</div>
                            <div style="color: #6B7280; font-size: 10px;">Sisa: {{ $item->sisa_tenor }} Bln</div>
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; font-weight: 600; color: #10B981; text-align: right; font-variant-numeric: tabular-nums; vertical-align: middle;">
                            Rp {{ number_format($terbayar, 0, ',', '.') }}
                        </td>
                        <td style="padding: 10px 12px; font-size: 12px; font-weight: 700; color: #D97706; text-align: right; font-variant-numeric: tabular-nums; vertical-align: middle;">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>
                        <td style="padding: 10px 12px; text-align: center; vertical-align: middle;">
                            @if(strtolower($item->status) === 'berjalan')
                                <span style="display:inline-block; padding:1px 6px; border-radius:10px; font-size: 10px; background-color:#e6f4ea; color:#137333; font-weight:600; text-transform: uppercase;">Berjalan</span>
                            @elseif(strtolower($item->status) === 'lunas')
                                <span style="display:inline-block; padding:1px 6px; border-radius:10px; font-size: 10px; background-color:#e8f0fe; color:#1a73e8; font-weight:600; text-transform: uppercase;">Lunas</span>
                            @else
                                <span style="display:inline-block; padding:1px 6px; border-radius:10px; font-size: 10px; background-color:#f5f5f5; color:#666; font-weight:600; text-transform: uppercase;">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td style="padding: 10px 12px; text-align: center; vertical-align: middle;">
                            <a href="{{ route('pinjaman.aktif.show', $item->id) }}" style="color: #4F46E5; font-weight: 600; font-size: 11px; text-decoration: none;" title="Detail Angsuran">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                         <td colspan="11" style="text-align:center; padding:30px 12px; color:#6B7280; font-size:13px; vertical-align: middle;">
                             Tidak ada data pinjaman berjalan ditemukan.
                         </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination (Bottom of table) --}}
        @if($pinjaman->hasPages())
            <div class="pagination-wrapper" style="padding: 10px 14px; border-top: 1px solid #E5E7EB; background: #fff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="font-size: 11px; color: #6B7280;">
                    Menampilkan {{ $pinjaman->firstItem() }} sampai {{ $pinjaman->lastItem() }} dari {{ $pinjaman->total() }} data
                </div>
                <div class="pagination-links" style="font-size: 12px;">
                    {{ $pinjaman->links() }}
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
