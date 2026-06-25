<table border="1">
    <tr>
        <td colspan="10" style="font-size:16px; font-weight:bold; text-align:center; padding:10px; background:#EFF6FF; color:#1D4ED8;">
            LAPORAN RINCIAN PINJAMAN ANGGOTA
        </td>
    </tr>
    <tr>
        <td colspan="10" style="font-size:11px; text-align:center; padding:6px; background:#F9FAFB; color:#374151;">
            Koperasi Karyawan OPI &mdash; 
            @if($periode)
                Periode: {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }} &mdash; 
            @endif
            Status: {{ strtoupper($status) }} &mdash; 
            Tanggal Ekspor: {{ date('d/m/Y H:i') }}
        </td>
    </tr>
    <tr><td colspan="10"></td></tr>
</table>

<table border="1">
    <thead>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <th style="text-align:center; padding:5px;">NO</th>
            <th style="text-align:center; padding:5px;">TGL MULAI</th>
            <th style="text-align:center; padding:5px;">TGL SELESAI</th>
            <th style="text-align:center; padding:5px;">NIK</th>
            <th style="text-align:center; padding:5px;">NAMA ANGGOTA</th>
            <th style="text-align:center; padding:5px;">BAGIAN</th>
            <th style="text-align:center; padding:5px;">JENIS PINJAMAN</th>
            <th style="text-align:center; padding:5px;">POKOK PINJAMAN</th>
            <th style="text-align:center; padding:5px;">TOTAL TAGIHAN</th>
            <th style="text-align:center; padding:5px;">TENOR</th>
            <th style="text-align:center; padding:5px;">CICILAN PER BULAN</th>
            <th style="text-align:center; padding:5px;">SISA TENOR</th>
            <th style="text-align:center; padding:5px;">TELAH TERBAYAR</th>
            <th style="text-align:center; padding:5px;">SISA TAGIHAN</th>
            <th style="text-align:center; padding:5px;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach($pinjaman_export as $item)
            @php
                $pokok = $item->jumlah_pinjaman ?? 0;
                $total_kontrak = $item->total_pinjaman ?? 0;
                $terbayar = $item->total_terbayar_historis ?? 0;
                $sisa = $total_kontrak - $terbayar;
                if ($sisa < 0) $sisa = 0;
                $statusLabel = $sisa <= 0 ? 'lunas' : 'berjalan';
            @endphp
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="text-align:center;">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') : '-' }}</td>
                <td style="text-align:center;">{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('Y-m-d') : '-' }}</td>
                <td style="mso-number-format:'\@'; text-align:center;">{{ $item->anggota->nik ?? '-' }}</td>
                <td>{{ $item->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td>{{ $item->anggota->bagian ?? '-' }}</td>
                <td>{{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}</td>
                <td style="text-align:right;">{{ number_format($pokok, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($total_kontrak, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ $item->tenor }} Bln</td>
                <td style="text-align:right;">{{ number_format($item->cicilan_per_bulan, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ $item->sisa_tenor_historis }} Bln</td>
                <td style="text-align:right;">{{ number_format($terbayar, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($sisa, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ strtoupper($statusLabel) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="7" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ number_format($sumPokok, 2, ',', '.') }}</td>
            <td style="text-align:right;">{{ number_format($sumTotal, 2, ',', '.') }}</td>
            <td ></td>
            <td style="text-align:right;">{{ number_format($sumCicilan, 2, ',', '.') }}</td>
            <td ></td>
            <td style="text-align:right;">{{ number_format($sumTerbayar, 2, ',', '.') }}</td>
            <td style="text-align:right; color:#1D4ED8;">{{ number_format($sumSisa, 2, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
