<table border="1">
    <tr>
        <td colspan="11" style="font-size:16px; font-weight:bold; text-align:center; padding:10px; background:#EFF6FF; color:#1D4ED8;">
            LAPORAN RINCIAN TRANSAKSI PEMBAYARAN PINJAMAN (ANGSURAN)
        </td>
    </tr>
    <tr>
        <td colspan="11" style="font-size:11px; text-align:center; padding:6px; background:#F9FAFB; color:#374151;">
            Koperasi Karyawan OPI &mdash; 
            @if($periode)
                Periode: {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }} &mdash; 
            @endif
            Status: {{ strtoupper($status) }} &mdash; 
            Tanggal Ekspor: {{ date('d/m/Y H:i') }}
        </td>
    </tr>
    @if(!empty($filterSearch) || !empty($filterDepartemen) || !empty($filterMetode))
    <tr>
        <td colspan="11" style="font-size:11px; text-align:center; padding:6px; background:#F3F4F6; color:#374151; font-weight:bold;">
            Filter &mdash; 
            @if(!empty($filterSearch)) Pencarian: "{{ $filterSearch }}" &mdash; @endif
            @if(!empty($filterDepartemen)) Departemen: {{ $filterDepartemen }} &mdash; @endif
            @if(!empty($filterMetode)) Metode: {{ $filterMetode }} &mdash; @endif
        </td>
    </tr>
    @endif
    <tr><td colspan="11"></td></tr>
</table>

<table border="1">
    <thead>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <th style="text-align:center; padding:5px;">NO</th>
            <th style="text-align:center; padding:5px;">TANGGAL BAYAR</th>
            <th style="text-align:center; padding:5px;">NIK</th>
            <th style="text-align:center; padding:5px;">NAMA ANGGOTA</th>
            <th style="text-align:center; padding:5px;">DEPARTEMEN</th>
            <th style="text-align:center; padding:5px;">JENIS PINJAMAN</th>
            <th style="text-align:center; padding:5px;">ANGSURAN KE</th>
            <th style="text-align:center; padding:5px;">JUMLAH TAGIHAN</th>
            <th style="text-align:center; padding:5px;">JUMLAH DIBAYAR</th>
            <th style="text-align:center; padding:5px;">CARA BAYAR</th>
            <th style="text-align:center; padding:5px;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach($angsuran_export as $item)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="text-align:center;">{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('Y-m-d') : '-' }}</td>
                <td style="mso-number-format:'\@'; text-align:center;">{{ $item->pinjaman->anggota->nik ?? '-' }}</td>
                <td>{{ $item->pinjaman->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td>{{ $item->pinjaman->anggota->departemen->nama ?? '-' }}</td>
                <td>{{ $item->pinjaman->jenisPinjaman?->nama_pinjaman ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->angsuran_ke }}</td>
                <td style="text-align:right;">{{ $item->jumlah_tagihan }}</td>
                <td style="text-align:right;">{{ $item->jumlah_dibayar }}</td>
                <td style="text-align:center;">
                    @if($item->pinjaman && $item->pinjaman->payment_method == 'gaji')
                        Payroll
                    @else
                        Manual
                    @endif
                </td>
                <td style="text-align:center;">{{ strtoupper($item->status) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="8" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ $sumTagihan }}</td>
            <td style="text-align:right; color:#10B981;">{{ $sumTerbayar }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
