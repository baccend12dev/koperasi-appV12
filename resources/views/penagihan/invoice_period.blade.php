{{-- resources/views/penagihan/invoice_period.blade.php --}}
@extends('layouts.app')

@section('title', 'Invoice Period')

@section('topbar-nav')
    <a href="{{ route('penagihan.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('penagihan.generator') }}" class="tb-link">Tagihan Generator</a>
    <a href="{{ route('penagihan.invoice') }}" class="tb-link active">Invoice</a>
@endsection

@section('subbar-actions')
    <button onclick="openModalGenerateInvoice()" class="btn-primary" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;border:none;margin-right:6px;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="1" x2="7" y2="13"></line><line x1="1" y1="7" x2="13" y2="7"></line></svg>
        Generate Invoice
    </button>
@endsection

@section('page-title', 'Invoice Period')

@section('content')
<style>
    .content-wrapper { max-width:1400px; margin:0 auto; padding:24px; font-family:'Inter',system-ui,sans-serif; }
    .content-block { background:#fff; border:1px solid #e0e0e0; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden; }
    .table-custom { width:100%; border-collapse:collapse; }
    .table-custom th { text-align:left; padding:11px 16px; font-size:11px; font-weight:700; color:#5f6368; text-transform:uppercase; border-bottom:1px solid #e0e0e0; background:#fafafa; }
    .table-custom td { padding:14px 16px; font-size:13px; color:#3c4043; border-bottom:1px solid #f1f3f4; vertical-align:middle; }
    .badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:12px; font-size:11px; font-weight:700; }
    .badge-draft { background:#f1f3f4; color:#5f6368; }
    .badge-generated { background:#e8f0fe; color:#1a73e8; }
    .badge-closed { background:#e6f4ea; color:#137333; }
</style>

<div class="content-wrapper">
    @if(session('success'))
        <div style="padding:16px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <div style="font-size:14px;font-weight:500;">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="padding:16px;background:#fee2e2;color:#991b1b;border-radius:8px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div style="font-size:14px;font-weight:500;">{{ session('error') }}</div>
        </div>
    @endif

    <div class="content-block">
        <div style="padding:16px 20px;border-bottom:1px solid #e0e0e0;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111;">Daftar Invoice</h3>
                <p style="margin:2px 0 0;font-size:12px;color:#6B7280;">Menampilkan riwayat invoice berdasarkan periode penagihan</p>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th style="text-align:right;">Total Gaji</th>
                        <th style="text-align:right;">Total Mandiri</th>
                        <th style="text-align:right;">Total Amount</th>
                        <th style="text-align:center;">Jumlah Detail</th>
                        <th>Status</th>
                        <th>Generated At</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td style="font-weight:600;color:#111;">{{ $inv->periode }}</td>
                        <td style="text-align:right;">Rp {{ number_format($inv->total_gaji, 0, ',', '.') }}</td>
                        <td style="text-align:right;">Rp {{ number_format($inv->total_mandiri, 0, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:700;">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                        <td style="text-align:center;">{{ $inv->details_count }} Item</td>
                        <td>
                            @php
                                $badgeClass = 'badge-draft';
                                if($inv->status == 'generated') $badgeClass = 'badge-generated';
                                if($inv->status == 'closed') $badgeClass = 'badge-closed';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($inv->status) }}</span>
                        </td>
                        <td style="font-size:12px;color:#888;">
                            {{ $inv->generated_at ? \Carbon\Carbon::parse($inv->generated_at)->format('d M Y H:i') : '-' }}
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('penagihan.invoice.show', $inv->id) }}" style="font-size:12px;font-weight:600;color:#1a73e8;text-decoration:none;border:1px solid #e8f0fe;background:#f8faff;padding:6px 12px;border-radius:6px;transition:.2s;">Lihat Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#888;padding:32px 16px;">Belum ada data invoice.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($invoices->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e0e0e0;">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL GENERATE INVOICE --}}
<div id="modalGenerateInvoice" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;width:100%;max-width:400px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
        <div style="padding:16px 20px;border-bottom:1px solid #e0e0e0;display:flex;justify-content:space-between;align-items:center;background:#f8f9fa;">
            <h3 style="margin:0;font-size:16px;font-weight:600;color:#111;">Generate Invoice Baru</h3>
            <button onclick="closeModalGenerateInvoice()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;">&times;</button>
        </div>
        <form action="{{ route('penagihan.invoice.generate') }}" method="POST" style="padding:20px;">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:600;color:#5f6368;margin-bottom:6px;">Periode (Bulan & Tahun)</label>
                <input type="month" name="periode" required value="{{ date('Y-m') }}" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box;">
                <div style="font-size:11px;color:#888;margin-top:4px;">Invoice akan digenerate berdasarkan pinjaman yang sedang berjalan bulan ini.</div>
            </div>
            
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;">
                <button type="button" onclick="closeModalGenerateInvoice()" style="padding:8px 16px;background:#f1f3f4;border:none;border-radius:6px;font-size:13px;font-weight:600;color:#3c4043;cursor:pointer;">Batal</button>
                <button type="submit" style="padding:8px 16px;background:#1a73e8;border:none;border-radius:6px;font-size:13px;font-weight:600;color:#fff;cursor:pointer;">Generate</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalGenerateInvoice() {
    document.getElementById('modalGenerateInvoice').style.display = 'flex';
}
function closeModalGenerateInvoice() {
    document.getElementById('modalGenerateInvoice').style.display = 'none';
}
</script>
@endsection
