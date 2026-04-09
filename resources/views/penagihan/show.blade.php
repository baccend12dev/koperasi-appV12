{{-- resources/views/penagihan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tagihan: ' . $tagihan->periode)

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('penagihan.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('penagihan.generator') }}" class="tb-link active">Tagihan Generator</a>
    <a href="{{ route('simpanan.index') }}" class="tb-link">Simpanan Anggota</a>
    <a href="{{ route('pinjaman.index') }}" class="tb-link">Pinjaman Anggota</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('penagihan.generator') }}" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali
    </a>
@endsection

@section('page-title', 'Detail Report: ' . $tagihan->periode)

@section('content')
<div class="px-6 py-4 space-y-6">

    <style>
        .details-header {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            margin-bottom: 24px;
        }
        .header-meta {
            display: flex;
            gap: 40px;
        }
        .meta-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .meta-group .value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }
        .btn-pay {
            background: #10B981;
            color: #fff;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-pay:hover { background: #059669; }
        .btn-pay:disabled { background: #D1D5DB; cursor: not-allowed; }
        
        .report-table th, .report-table td {
            border: 1px solid #E5E7EB;
            padding: 8px 12px;
            text-align: right;
            vertical-align: middle;
        }
        .report-table th { background: #F9FAFB; font-weight: 700; text-align: center; color: #374151; font-size: 11px;}
        .report-table td { color: #4B5563; font-size: 12px; }
        .report-table td.cell-name { text-align: left; font-weight: 600; color: #1F2937; }
        .report-table th.col-simpanan { background: #EFF6FF; color: #1D4ED8; }
        .report-table th.col-pinjaman { background: #FEF2F2; color: #B91C1C; }
        .report-table th.col-jumlah { background: #ECFDF5; color: #047857; }
        .report-table td.val-jumlah { font-weight: 700; color: #111827; }
        .report-table tr:hover td { background: #F3F4F6; }
    </style>

    <div class="details-header flex justify-between items-end">
        <div class="header-meta">
            <div class="meta-group">
                <label>Periode</label>
                <div class="value">{{ $tagihan->periode }}</div>
            </div>
            <div class="meta-group">
                <label>Tanggal Generate</label>
                <div class="value">{{ \Carbon\Carbon::parse($tagihan->tgl_generate)->format('d M Y') }}</div>
            </div>
            <div class="meta-group">
                <label>Total Tagihan (Potongan)</label>
                <div class="value text-green-700">Rp {{ number_format($tagihan->total_amount, 0, ',', '.') }}</div>
            </div>
            <div class="meta-group">
                <label>Keterangan</label>
                <div class="value">{{ $tagihan->keterangan }}</div>
            </div>
            <div class="meta-group">
                <label>Status Global</label>
                <div class="value">
                    @if($tagihan->status == 'Draft')
                        <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#F3F4F6; color:#4B5563; font-weight:600;">Draft</span>
                    @elseif($tagihan->status == 'Partial')
                        <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#FEF3C7; color:#B45309; font-weight:600;">Partial</span>
                    @else
                        <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; background-color:#DEF7EC; color:#03543F; font-weight:600;">Paid</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $activeDetails = $tagihan->details->whereNull('deleted_at');
        $deletedDetails = $tagihan->details->whereNotNull('deleted_at');
    @endphp

    <!-- Tampilkan Data Terhapus di Atas Jika Ada -->
    @if($deletedDetails->count() > 0)
    <div style="margin-bottom: 24px; border: 1px solid #FECACA; border-radius: 12px; background: #FEF2F2; overflow: hidden;">
        <div style="padding: 12px 20px; background: #FEE2E2; border-bottom: 1px solid #FECACA; color: #991B1B; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Data Tagihan yang Dihapus (Dibatalkan)
        </div>
        <div class="overflow-x-auto">
            <table class="w-full report-table" style="border-collapse: collapse;">
                <thead style="opacity: 0.8;">
                    <tr>
                        <th style="min-width: 200px;">NAMA ANGGOTA</th>
                        <th class="col-simpanan">SIMPANAN (TOTAL)</th>
                        <th class="col-pinjaman">PINJAMAN</th>
                        <th class="col-jumlah">JUMLAH POTONGAN</th>
                        <th>TANGGAL DIHAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deletedDetails as $del)
                    <tr>
                        <td class="cell-name" style="color: #9CA3AF; text-decoration: line-through;">
                            {{ $del->anggota->nama_anggota ?? 'Unknown' }}
                        </td>
                        <td style="color: #9CA3AF;">Rp {{ number_format($del->jumlah_simpanan, 0, ',', '.') }}</td>
                        <td style="color: #9CA3AF;">Rp {{ number_format($del->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td class="val-jumlah" style="color: #9CA3AF;">Rp {{ number_format($del->total_potongan, 0, ',', '.') }}</td>
                        <td style="color: #9CA3AF; font-size: 11px;">{{ \Carbon\Carbon::parse($del->deleted_at)->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Details Table wrapped in a Form for processing payments -->
    <form method="POST" action="{{ route('penagihan.bayar') }}">
        @csrf
        <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
        <input type="hidden" name="tanggal_transaksi" value="{{ $tagihan->tgl_generate }}">
        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 flex justify-between items-center bg-white border-b border-gray-200">
                <h3 class="font-bold text-sm text-gray-800 tracking-wide uppercase">Daftar Potongan Anggota - {{ $tagihan->periode }}</h3>
                <div>
                    <!-- Payment Button -->
                    <button type="submit" class="btn-pay" onclick="return confirm('Proses pembayaran untuk anggota yang dipilih? Keseluruhan tagihan di Simpanan & Pinjaman anggota ini akan langsung tersimpan/lunas.')" {{ $activeDetails->where('status', '!=', 'Lunas')->count() == 0 ? 'disabled' : '' }}>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Proses Potongan Terpilih
                    </button>
                    <!-- Export Button placeholder -->
                    <button type="button" onclick="alert('Export Excel fitur dalam tahap pengembangan')" style="border:1px solid #D1D5DB; background:#fff; color:#374151; padding:7px 16px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; margin-left:6px;">
                        Export
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full report-table" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 40px; text-align: center;">
                                <input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.row-check:not([disabled])').forEach(c=>c.checked=this.checked)" class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4 cursor-pointer">
                            </th>
                            <th rowspan="2" style="text-align: left; min-width: 200px;">NAMA ANGGOTA</th>
                            <th colspan="4" class="col-simpanan">SIMPANAN {{ strtoupper($tagihan->periode) }}</th>
                            <th rowspan="2" class="col-pinjaman">PINJAMAN<br>{{ strtoupper($tagihan->periode) }}</th>
                            <th rowspan="2" class="col-jumlah">JUMLAH<br>POTONGAN</th>
                            <th rowspan="2">STATUS</th>
                            <th rowspan="2">AKSI</th>
                        </tr>
                        <tr>
                            <th class="col-simpanan">POKOK</th>
                            <th class="col-simpanan">WAJIB</th>
                            <th class="col-simpanan">S.RELA</th>
                            <th class="col-simpanan">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @php
                            $totalPokok = 0;
                            $totalWajib = 0;
                            $totalSukarela = 0;
                            $totalSimpanan = 0;
                            $totalPinjaman = 0;
                            $totalGrand = 0;
                        @endphp
                        
                        @forelse($activeDetails as $detail)
                            @php
                                $totalPokok += $detail->simpanan_pokok;
                                $totalWajib += $detail->simpanan_wajib;
                                $totalSukarela += $detail->simpanan_sukarela;
                                $totalSimpanan += $detail->jumlah_simpanan;
                                $totalPinjaman += $detail->jumlah_pinjaman;
                                $totalGrand += $detail->total_potongan;
                            @endphp
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" class="row-check rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4 cursor-pointer" {{ $detail->status == 'Lunas' ? 'disabled' : '' }}>
                                </td>
                                <td class="cell-name">
                                    {{ $detail->anggota->nama_anggota ?? 'Unknown' }}
                                    <div style="font-size: 10px; color: #9CA3AF; font-weight: normal;">NIK: {{ $detail->anggota->nik ?? '-' }}</div>
                                </td>
                                <td>{{ number_format($detail->simpanan_pokok, 0, ',', '.') }}</td>
                                <td>{{ number_format($detail->simpanan_wajib, 0, ',', '.') }}</td>
                                <td>{{ number_format($detail->simpanan_sukarela, 0, ',', '.') }}</td>
                                <td class="val-jumlah" style="background:#F9FAFB;">{{ number_format($detail->jumlah_simpanan, 0, ',', '.') }}</td>
                                <td style="color:#B91C1C;">
                                    @if($detail->jumlah_pinjaman > 0)
                                        {{ number_format($detail->jumlah_pinjaman, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="val-jumlah" style="background:#F0FDF4; font-size:13px;">{{ number_format($detail->total_potongan, 0, ',', '.') }}</td>
                                <td style="text-align: center;">
                                    @if($detail->status == 'Lunas')
                                        <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:12px; font-size:10px; background-color:#DEF7EC; color:#03543F; font-weight:700;">
                                            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            LUNAS
                                        </span>
                                    @else
                                        <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:10px; background-color:#FEE2E2; color:#991B1B; font-weight:700;">
                                            BELUM
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($detail->status != 'Lunas')
                                    <button type="button" onclick="if(confirm('Hapus tagihan atas nama {{ $detail->anggota->nama_anggota }}? Data tagihan ini akan dibatalkan.')) document.getElementById('delete-{{$detail->id}}').submit()" style="color:#DC2626; padding: 4px; border-radius: 4px;" title="Hapus Tagihan (Soft Delete)">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @else
                                    <span style="color:#D1D5DB;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px 16px; color: #9CA3AF;">
                                    Belum ada data detail potongan tagihan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                    {{-- Table Footer for Totals --}}
                    @if($activeDetails->count() > 0)
                    <tfoot>
                        <tr style="background: #F3F4F6; font-weight: 700; color: #111827;">
                            <td colspan="2" style="text-align: center; border: 1px solid #E5E7EB; padding: 10px 12px; font-size: 13px;">TOTAL KESELURUHAN</td>
                            <td style="border: 1px solid #E5E7EB; padding: 10px 12px;">{{ number_format($totalPokok, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #E5E7EB; padding: 10px 12px;">{{ number_format($totalWajib, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #E5E7EB; padding: 10px 12px;">{{ number_format($totalSukarela, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #E5E7EB; padding: 10px 12px; color: #1D4ED8;">{{ number_format($totalSimpanan, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #E5E7EB; padding: 10px 12px; color: #B91C1C;">{{ number_format($totalPinjaman, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #E5E7EB; padding: 10px 12px; color: #047857; font-size: 14px;">{{ number_format($totalGrand, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #E5E7EB;" colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </form>

    @isset($activeDetails)
        @foreach($activeDetails as $detail)
            @if($detail->status != 'Lunas')
            <form id="delete-{{$detail->id}}" action="{{ route('penagihan.destroyDetail', $detail->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
            @endif
        @endforeach
    @endisset
</div>
@endsection
