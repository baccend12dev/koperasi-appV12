@php
    $filterParts = [];
    if (!empty($filterTahun)) $filterParts[] = 'Tahun: ' . $filterTahun;
    if (!empty($filterBulan)) $filterParts[] = 'Bulan: ' . date('F', mktime(0, 0, 0, $filterBulan, 10));
    if (!empty($filterStatus) && $filterStatus !== 'semua') $filterParts[] = 'Status: ' . ucfirst($filterStatus);
    $filterLabel = count($filterParts) > 0 ? implode(' | ', $filterParts) : 'Semua Data';
@endphp

<table border="1">
    <tr>
        <td colspan="15" style="font-size:16px; font-weight:bold; text-align:center; padding:10px; background:#EFF6FF; color:#1D4ED8;">
            DATA PINJAMAN AKTIF
        </td>
    </tr>
    <tr>
        <td colspan="15" style="font-size:12px; text-align:center; padding:6px; background:#F9FAFB; color:#374151;">
            Filter: {{ $filterLabel }} &mdash; Diekspor: {{ date('d/m/Y H:i') }}
        </td>
    </tr>
    <tr><td colspan="15"></td></tr>
</table>

<table border="1">
    <thead>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <th style="text-align:center;">NO</th>
            <th style="text-align:center;">NIK</th>
            <th style="text-align:center;">NAMA ANGGOTA</th>
            <th style="text-align:center;">JENIS PINJAMAN</th>
            <th style="text-align:center;">TANGGAL MULAI</th>
            <th style="text-align:center;">TANGGAL SELESAI</th>
            <th style="text-align:center;">PINJAMAN POKOK</th>
            <th style="text-align:center;">BUNGA (%)</th>
            <th style="text-align:center;">TOTAL PINJAMAN</th>
            <th style="text-align:center;">TENOR</th>
            <th style="text-align:center;">SISA TENOR</th>
            <th style="text-align:center;">CICILAN/BULAN</th>
            <th style="text-align:center;">TERBAYAR</th>
            <th style="text-align:center;">SISA TAGIHAN</th>
            <th style="text-align:center;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $totalPokok = 0;
            $totalPinjaman = 0;
            $totalTerbayar = 0;
            $totalSisa = 0;
        @endphp

        @foreach($pinjaman_list as $item)
            @php
                $totalPokok += $item->jumlah_pinjaman;
                $totalPinjaman += $item->total_pinjaman;
                $totalTerbayar += $item->total_terbayar;
                $totalSisa += $item->status == 'lunas' ? 0 : $item->sisa_pinjaman;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="mso-number-format:'\@';">{{ $item->anggota->nik ?? '-' }}</td>
                <td>{{ $item->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td>{{ $item->jenisPinjaman?->nama_pinjaman ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                <td style="text-align:center;">{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                <td style="text-align:right;">{{ $item->jumlah_pinjaman }}</td>
                <td style="text-align:center;">{{ $item->bunga }}%</td>
                <td style="text-align:right;">{{ $item->total_pinjaman }}</td>
                <td style="text-align:center;">{{ $item->tenor }} Bulan</td>
                <td style="text-align:center;">{{ $item->sisa_tenor }} Bulan</td>
                <td style="text-align:right;">{{ $item->cicilan_per_bulan }}</td>
                <td style="text-align:right;">{{ $item->total_terbayar }}</td>
                <td style="text-align:right;">{{ $item->status == 'lunas' ? 0 : $item->sisa_pinjaman }}</td>
                <td style="text-align:center;">{{ strtoupper($item->status) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="6" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ $totalPokok }}</td>
            <td></td>
            <td style="text-align:right;">{{ $totalPinjaman }}</td>
            <td colspan="2"></td>
            <td></td>
            <td style="text-align:right; color:#047857;">{{ $totalTerbayar }}</td>
            <td style="text-align:right; color:#B91C1C;">{{ $totalSisa }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
