{{-- resources/views/simpanan/tagihan_detail.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tagihan Simpanan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link">Simpanan Anggota</a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link">Transaksi</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('simpanan.tagihangenerator') }}" class="tb-link active">Tagih Simpanan</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('simpanan.tagihangenerator') }}" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali
    </a>
@endsection

@section('page-title', 'Detail Tagihan: ' . $tagihan->periode)

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
    </style>

    <div class="details-header flex justify-between items-end">
        <div class="header-meta">
            <div class="meta-group">
                <label>Periode</label>
                <div class="value">{{ $tagihan->periode }}</div>
            </div>
            <div class="meta-group">
                <label>Tanggal Generate</label>
                <div class="value">{{ \Carbon\Carbon::parse($tagihan->tanggal_generate)->format('d M Y') }}</div>
            </div>
            <div class="meta-group">
                <label>Total Tagihan</label>
                <div class="value">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</div>
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

    <!-- Details Table wrapped in a Form for processing payments -->
    <form method="POST" action="{{ route('simpanan.tagihangenerator.bayar') }}">
        @csrf
        <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
        
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-sm text-gray-800 tracking-wide">DAFTAR ANGGOTA DITAGIH</h3>
                <div>
                    <!-- Payment Button is here -->
                    <button type="submit" class="btn-pay" onclick="return confirm('Proses pembayaran untuk anggota yang dipilih? Transaksi akan otomatis tercatat ke Riwayat Transaksi.')">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Bayar Terpilih
                    </button>
                </div>
            </div>

            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-white text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">
                            <input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.row-check:not([disabled])').forEach(c=>c.checked=this.checked)" class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-6 py-4">Nama Anggota</th>
                        <th class="px-6 py-4">Wajib</th>
                        <th class="px-6 py-4">Pokok</th>
                        <th class="px-6 py-4">Sukarela</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($tagihan->details as $detail)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" class="row-check rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4 cursor-pointer" {{ $detail->status == 'Lunas' ? 'disabled' : '' }}>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $detail->anggota->nama_anggota ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ number_format($detail->simpanan_wajib, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ number_format($detail->simpanan_pokok, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ number_format($detail->simpanan_sukarela, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($detail->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($detail->status == 'Lunas')
                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#DEF7EC; color:#03543F; font-weight:600;">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Lunas
                                    </span>
                                @else
                                    <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#FEE2E2; color:#991B1B; font-weight:600;">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                Tidak ada data detail tagihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection
