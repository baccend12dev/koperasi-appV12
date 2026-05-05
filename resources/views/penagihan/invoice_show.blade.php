{{-- resources/views/penagihan/invoice_show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Invoice ' . $invoice->periode)

@section('topbar-nav')
    <a href="{{ route('penagihan.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('penagihan.generator') }}" class="tb-link">Tagihan Generator</a>
    <a href="{{ route('penagihan.invoice') }}" class="tb-link active">Invoice</a>
@endsection

@section('page-title', 'Detail Invoice: ' . $invoice->periode)

@section('content')
<style>
    .content-wrapper { max-width:1000px; margin:0 auto; padding:24px; font-family:'Inter',system-ui,sans-serif; }
    
    .bon-card {
        background: #fff;
        border: 1px solid #ccc;
        margin-bottom: 30px;
        padding: 30px;
        page-break-inside: avoid;
    }
    .bon-title {
        font-size: 22px;
        font-weight: 800;
        margin-top: 0;
        margin-bottom: 20px;
        text-transform: uppercase;
        color: #111;
        letter-spacing: 1px;
    }
    .info-table {
        width: 100%;
        margin-bottom: 20px;
        font-size: 15px;
        color: #222;
    }
    .info-table td {
        padding: 4px 0;
    }
    .info-label {
        font-weight: 700;
        width: 15%;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        margin-bottom: 20px;
    }
    .data-table th, .data-table td {
        border: 1px solid #888;
        padding: 10px;
        text-align: left;
        color: #111;
    }
    .data-table th {
        background-color: #f1f3f4;
        font-weight: 700;
    }
    .data-table tfoot td {
        font-weight: 700;
    }
    
    .bon-footer {
        font-size: 14px;
        color: #333;
        line-height: 1.5;
    }
    
    @media print {
        body * { visibility: hidden; }
        .content-wrapper { padding: 0; max-width: 100%; }
        #printContainer, #printContainer * { visibility: visible; }
        #printContainer { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .bon-card { border: none; padding: 0; margin-bottom: 50px; page-break-inside: avoid; }
    }
</style>

<div class="content-wrapper">
    <div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;" class="no-print">
        <a href="{{ route('penagihan.invoice') }}" style="color:#1a73e8;text-decoration:none;font-size:14px;font-weight:600;">&larr; Kembali ke Invoice</a>
        <div style="display:flex;gap:10px;">
            <button onclick="window.print()" style="padding:8px 16px;background:#f1f3f4;border:1px solid #d1d5db;border-radius:6px;font-size:13px;font-weight:600;color:#3c4043;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Print Invoice
            </button>
            <button onclick="exportToExcel('printContainer', 'Bon_Cicilan_{{ $invoice->periode }}')" style="padding:8px 16px;background:#0f9d58;border:none;border-radius:6px;font-size:13px;font-weight:600;color:#fff;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Export Excel
            </button>
        </div>
    </div>

    <div id="printContainer">
        @forelse($details->groupBy('user_id') as $userId => $userDetails)
            @php
                $user = $userDetails->first()->anggota;
                $totCicilan = 0;
                $totSisa = 0;
            @endphp
            
            <div class="bon-card">
                <h2 class="bon-title" style="background:#e8eff5; display:inline-block; padding:10px 20px; width:100%; box-sizing:border-box;">BON CICILAN</h2>
                
                <table class="info-table">
                    <tr>
                        <td class="info-label">Nama</td>
                        <td style="width: 35%;">{{ strtoupper($user->nama_anggota ?? 'UNKNOWN') }}</td>
                        <td class="info-label">Nomor</td>
                        <td>{{ $user->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Bagian</td>
                        <td>{{ $user->bagian ?? '-' }}</td>
                        <td class="info-label">Bulan</td>
                        <td>{{ $invoice->generated_at ? \Carbon\Carbon::parse($invoice->generated_at)->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                </table>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Cicilan ke</th>
                            <th>Tenor</th>
                            <th>Cicilan</th>
                            <th>Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userDetails as $d)
                            @php 
                                $totCicilan += $d->cicilan_amount; 
                                $totSisa += $d->sisa_pinjaman;
                            @endphp
                        <tr>
                            <td>{{ $d->jenis_pinjaman }}</td>
                            <td>{{ $d->cicilan_ke }}</td>
                            <td>{{ $d->tenor }}</td>
                            <td>Rp {{ number_format($d->cicilan_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($d->sisa_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-transform:uppercase;">TOTAL</td>
                            <td>Rp {{ number_format($totCicilan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($totSisa, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
                
            </div>
            
        @empty
            <div style="text-align:center;color:#888;padding:60px 20px;background:#fff;border-radius:10px;border:1px solid #e0e0e0;">
                Belum ada data detail invoice untuk periode ini.
            </div>
        @endforelse
    </div>
</div>

<script>
function exportToExcel(containerID, filename = ''){
    var container = document.getElementById(containerID);
    
    // Convert to a giant html table structure that Excel understands better
    var htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                .bon-card { margin-bottom: 30px; border: 1px solid #000; padding: 20px; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                .data-table th, .data-table td { border: 1px solid #888; padding: 8px; }
                .info-table td { padding: 4px; }
            </style>
        </head>
        <body>
            ${container.innerHTML}
        </body>
        </html>
    `;
    
    filename = filename ? filename + '.xls' : 'excel_data.xls';
    
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', htmlContent], { type: 'application/vnd.ms-excel' });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Use base64 encoding to avoid special character and spacing issues
        var base64 = btoa(unescape(encodeURIComponent(htmlContent)));
        downloadLink.href = 'data:application/vnd.ms-excel;base64,' + base64;
        downloadLink.download = filename;
        downloadLink.click();
    }
    
    document.body.removeChild(downloadLink);
}
</script>
@endsection
