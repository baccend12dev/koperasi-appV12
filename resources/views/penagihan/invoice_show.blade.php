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
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        border-radius: 12px;
        margin-bottom: 30px;
        padding: 30px;
        page-break-inside: avoid;
    }
    .bon-header {
        background: #f1f5f9;
        border-left: 4px solid #3b82f6;
        padding: 12px 20px;
        margin-bottom: 24px;
        border-radius: 4px;
    }
    .bon-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .info-table {
        width: 100%;
        margin-bottom: 24px;
        font-size: 14px;
        color: #334155;
        border-collapse: collapse;
    }
    .info-table td {
        padding: 8px 12px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .info-label {
        font-weight: 600;
        color: #64748b;
        width: 15%;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .data-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 10px;
    }
    .data-table td {
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 10px;
        color: #334155;
    }
    .data-table tfoot td {
        font-weight: 700;
        background-color: #f8fafc;
        border-top: 2px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
        color: #1e293b;
        padding: 12px 10px;
    }
    
    /* Alignments & Widths */
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .w-jenis { width: 40%; }
    .w-cicilan-ke { width: 15%; }
    .w-tenor { width: 15%; }
    .w-cicilan { width: 15%; }
    .w-sisa { width: 15%; }
    
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
        .bon-card { border: none; padding: 0; margin-bottom: 50px; page-break-inside: avoid; box-shadow: none !important; }
        
        .data-table th, .data-table td {
            border-bottom: 1px solid #94a3b8 !important;
        }
        .data-table th {
            border-bottom: 2px solid #475569 !important;
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .data-table tfoot td {
            border-top: 2px solid #475569 !important;
            border-bottom: 2px solid #475569 !important;
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .bon-header {
            background: #f1f5f9 !important;
            border-left: 4px solid #1d4ed8 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .info-table td {
            border-bottom: 1px dashed #cbd5e1 !important;
        }
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
                <div class="bon-header">
                    <h2>BON CICILAN</h2>
                </div>
                
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
                            <th class="text-left w-jenis">Jenis</th>
                            <th class="text-center w-cicilan-ke">Cicilan ke</th>
                            <th class="text-center w-tenor">Tenor</th>
                            <th class="text-right w-cicilan">Cicilan</th>
                            <th class="text-right w-sisa">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userDetails as $d)
                            @php 
                                $totCicilan += $d->cicilan_amount; 
                                $totSisa += $d->sisa_pinjaman;
                            @endphp
                        <tr>
                            <td class="text-left">{{ $d->pinjaman->jenisPinjaman->nama_pinjaman ?? '-' }}</td>
                            <td class="text-center">{{ $d->cicilan_ke }}</td>
                            <td class="text-center">{{ $d->tenor }}</td>
                            <td class="text-right">Rp {{ number_format($d->cicilan_amount, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($d->sisa_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-left" style="text-transform:uppercase;">TOTAL</td>
                            <td class="text-right">Rp {{ number_format($totCicilan, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($totSisa, 0, ',', '.') }}</td>
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
    
    // Clone the container so we can modify the DOM for Excel export without affecting the UI
    var clone = container.cloneNode(true);
    
    // Convert .bon-header divs into styled tables because Excel doesn't render div border/padding well
    var headers = clone.querySelectorAll('.bon-header');
    headers.forEach(function(header) {
        var h2 = header.querySelector('h2');
        var text = h2 ? h2.innerText : (header.innerText || header.textContent);
        
        var headerTable = document.createElement('table');
        headerTable.style.width = '100%';
        headerTable.style.marginBottom = '15px';
        headerTable.style.borderCollapse = 'collapse';
        
        var tr = document.createElement('tr');
        var td = document.createElement('td');
        td.style.backgroundColor = '#f1f5f9';
        td.style.borderLeft = '4px solid #3b82f6';
        td.style.fontSize = '14pt';
        td.style.fontWeight = 'bold';
        td.style.padding = '10px 15px';
        td.style.color = '#1e293b';
        td.innerText = text.trim();
        
        tr.appendChild(td);
        headerTable.appendChild(tr);
        header.parentNode.replaceChild(headerTable, header);
    });
    
    // Convert .bon-card divs into styled outer tables so they render with border and padding in Excel
    var cards = clone.querySelectorAll('.bon-card');
    cards.forEach(function(card) {
        var outerTable = document.createElement('table');
        outerTable.style.width = '100%';
        outerTable.style.border = '1px solid #cbd5e1';
        outerTable.style.marginBottom = '30px';
        outerTable.style.borderCollapse = 'collapse';
        
        var tr = document.createElement('tr');
        var td = document.createElement('td');
        td.style.padding = '20px';
        td.style.backgroundColor = '#ffffff';
        
        // Move all children of the card div to the td
        while (card.firstChild) {
            td.appendChild(card.firstChild);
        }
        
        tr.appendChild(td);
        outerTable.appendChild(tr);
        card.parentNode.replaceChild(outerTable, card);
    });
    
    // Convert to a giant html table structure that Excel understands better
    var htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                .info-table { 
                    width: 100%; 
                    margin-bottom: 20px; 
                    border-collapse: collapse;
                }
                .info-table td { 
                    padding: 8px 12px; 
                    font-size: 11pt;
                    color: #334155;
                    border-bottom: 1px dashed #cbd5e1;
                }
                .info-label { 
                    font-weight: bold; 
                    color: #64748b; 
                    width: 15%;
                }
                .data-table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-bottom: 10px;
                }
                .data-table th { 
                    background-color: #f8fafc; 
                    color: #475569; 
                    font-weight: bold; 
                    font-size: 10pt;
                    border: 1px solid #cbd5e1; 
                    padding: 10px; 
                }
                .data-table td { 
                    border: 1px solid #cbd5e1; 
                    padding: 10px; 
                    font-size: 10pt;
                    color: #334155;
                }
                .data-table tfoot td { 
                    font-weight: bold; 
                    background-color: #f8fafc;
                    border: 1px solid #cbd5e1; 
                    padding: 10px; 
                }
                .text-left { text-align: left; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
            ${clone.innerHTML}
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
