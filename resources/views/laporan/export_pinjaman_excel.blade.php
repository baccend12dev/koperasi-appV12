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
            <th style="text-align:center; padding:5px;">DEPARTEMEN</th>
            <th style="text-align:center; padding:5px;">JENIS PINJAMAN</th>
            <th style="text-align:center; padding:5px;">POKOK PINJAMAN</th>
            <th style="text-align:center; padding:5px;">TOTAL TAGIHAN</th>
            <th style="text-align:center; padding:5px;">TENOR</th>
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
                $terbayar = $item->total_terbayar ?? 0;
                $sisa = $item->status == 'lunas' ? 0 : ($item->sisa_pinjaman ?? 0);
            @endphp
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="text-align:center;">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') : '-' }}</td>
                <td style="text-align:center;">{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('Y-m-d') : '-' }}</td>
                <td style="mso-number-format:'\@'; text-align:center;">{{ $item->anggota->nik ?? '-' }}</td>
                <td>{{ $item->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td>{{ $item->anggota->departemen->nama ?? '-' }}</td>
                <td>{{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}</td>
                <td style="text-align:right;">{{ $pokok }}</td>
                <td style="text-align:right;">{{ $total_kontrak }}</td>
                <td style="text-align:center;">{{ $item->tenor }} Bln</td>
                <td style="text-align:center;">{{ $item->sisa_tenor }} Bln</td>
                <td style="text-align:right;">{{ $terbayar }}</td>
                <td style="text-align:right;">{{ $sisa }}</td>
                <td style="text-align:center;">{{ strtoupper($item->status) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="7" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ $sumPokok }}</td>
            <td style="text-align:right;">{{ $sumTotal }}</td>
            <td colspan="2"></td>
            <td style="text-align:right;">{{ $sumTerbayar }}</td>
            <td style="text-align:right; color:#1D4ED8;">{{ $sumSisa }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
