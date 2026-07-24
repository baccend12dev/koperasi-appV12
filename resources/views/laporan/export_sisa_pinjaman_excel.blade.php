<table border="1">
    <tr>
        <td colspan="12" style="font-size:16px; font-weight:bold; text-align:center; padding:10px; background:#EFF6FF; color:#1D4ED8;">
            LAPORAN RINCIAN SISA PINJAMAN ANGGOTA
        </td>
    </tr>
    <tr>
        <td colspan="12" style="font-size:11px; text-align:center; padding:6px; background:#F9FAFB; color:#374151;">
            Koperasi Karyawan OPI &mdash; 
            @if($periode)
                Periode: {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }} &mdash; 
            @endif
            Jenis Pinjaman: {{ $jenis_pinjaman ?? 'Semua Jenis Pinjaman' }} &mdash; 
            Status: {{ strtoupper($status) }} &mdash; 
            Tanggal Ekspor: {{ date('d/m/Y H:i') }}
        </td>
    </tr>
    @if(!empty($filterSearch) || !empty($filterDepartemen))
    <tr>
        <td colspan="12" style="font-size:11px; text-align:center; padding:6px; background:#F3F4F6; color:#374151; font-weight:bold;">
            Filter &mdash; 
            @if(!empty($filterSearch)) Pencarian: "{{ $filterSearch }}" &mdash; @endif
            @if(!empty($filterDepartemen)) Departemen: {{ $filterDepartemen }} &mdash; @endif
        </td>
    </tr>
    @endif
    <tr><td colspan="12"></td></tr>
</table>

<table border="1">
    <thead>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <th style="text-align:center; padding:5px;">NO</th>
            <th style="text-align:center; padding:5px;">NIK</th>
            <th style="text-align:center; padding:5px;">NAMA ANGGOTA</th>
            <th style="text-align:center; padding:5px;">BAGIAN</th>
            <th style="text-align:center; padding:5px;">Jenis Pinjaman</th>
            <th style="text-align:center; padding:5px;">TENOR</th>
            <th style="text-align:center; padding:5px;">SISA TENOR</th>
            <th style="text-align:center; padding:5px;">POKOK PINJAMAN</th>
            <th style="text-align:center; padding:5px;">BUNGA (TOTAL)</th>
            <th style="text-align:center; padding:5px;">SISA POKOK</th>
            <th style="text-align:center; padding:5px;">SISA BUNGA</th>
            <th style="text-align:center; padding:5px;">TOTAL SISA HUTANG</th>
            <th style="text-align:center; padding:5px;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach($pinjaman_export as $item)
            @php
                $statusLabel = $item->sisa_total_hutang <= 0 ? 'LUNAS' : 'BERJALAN';
            @endphp
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="mso-number-format:'\@'; text-align:center;">{{ $item->anggota->nik ?? '-' }}</td>
                <td>{{ $item->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td>{{ $item->anggota->bagian ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->jenisPinjaman->nama_pinjaman ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->tenor }}</td>
                <td style="text-align:center;">{{ $item->sisa_tenor_historis }}</td>
                <td style="text-align:right;">{{ number_format($item->jumlah_pinjaman, 2, '.', ',') }}</td>
                <td style="text-align:right;">{{ number_format($item->total_bunga, 2, '.', ',') }}</td>
                <td style="text-align:right; color:#10B981; font-weight:600;">{{ number_format($item->sisa_pokok, 2, '.', ',') }}</td>
                <td style="text-align:right; color:#EC4899; font-weight:600;">{{ number_format($item->sisa_bunga, 2, '.', ',') }}</td>
                <td style="text-align:right; color:#D97706; font-weight:700;">{{ number_format($item->sisa_total_hutang, 2, '.', ',') }}</td>
                <td style="text-align:center;">{{ $statusLabel }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="6" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ number_format($sumPokok, 2, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($sumBunga, 2, '.', ',') }}</td>
            <td style="text-align:right; color:#10B981;">{{ number_format($sumSisaPokok, 2, '.', ',') }}</td>
            <td style="text-align:right; color:#EC4899;">{{ number_format($sumSisaBunga, 2, '.', ',') }}</td>
            <td style="text-align:right; color:#D97706;">{{ number_format($sumSisaTotal, 2, '.', ',') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
