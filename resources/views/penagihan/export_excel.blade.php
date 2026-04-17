
<table border="1">
    <thead>
        <tr>
            <th rowspan="2" style="background:#F9FAFB; font-weight:bold; text-align:center;">NIK</th>
            <th rowspan="2" style="background:#F9FAFB; font-weight:bold; text-align:center;">NAMA ANGGOTA</th>
            <th colspan="4" style="background:#EFF6FF; color:#1D4ED8; font-weight:bold; text-align:center;">SIMPANAN {{ strtoupper($tagihan->periode) }}</th>
            <th rowspan="2" style="background:#FEF2F2; color:#B91C1C; font-weight:bold; text-align:center;">PINJAMAN<br>{{ strtoupper($tagihan->periode) }}</th>
            <th rowspan="2" style="background:#ECFDF5; color:#047857; font-weight:bold; text-align:center;">JUMLAH POTONGAN</th>
            <th rowspan="2" style="background:#F9FAFB; font-weight:bold; text-align:center;">STATUS</th>
        </tr>
        <tr>
            <th style="background:#EFF6FF; color:#1D4ED8; font-weight:bold; text-align:center;">POKOK</th>
            <th style="background:#EFF6FF; color:#1D4ED8; font-weight:bold; text-align:center;">WAJIB</th>
            <th style="background:#EFF6FF; color:#1D4ED8; font-weight:bold; text-align:center;">S.RELA</th>
            <th style="background:#EFF6FF; color:#1D4ED8; font-weight:bold; text-align:center;">JUMLAH</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPokok = 0;
            $totalWajib = 0;
            $totalSukarela = 0;
            $totalSimpanan = 0;
            $totalPinjaman = 0;
            $totalGrand = 0;
        @endphp
        
        @foreach($tagihan->details as $detail)
            @php
                $totalPokok += $detail->simpanan_pokok;
                $totalWajib += $detail->simpanan_wajib;
                $totalSukarela += $detail->simpanan_sukarela;
                $totalSimpanan += $detail->jumlah_simpanan;
                $totalPinjaman += $detail->jumlah_pinjaman;
                $totalGrand += $detail->total_potongan;
            @endphp
            <tr>
                <!-- NIK ditafsirkan sebagai string, mencegah format scientific notation di excel apabila panjang -->
                <td style="mso-number-format:'\@';">{{ $detail->anggota->nik ?? '-' }}</td>
                <td>{{ $detail->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td style="text-align:right;">{{ $detail->simpanan_pokok }}</td>
                <td style="text-align:right;">{{ $detail->simpanan_wajib }}</td>
                <td style="text-align:right;">{{ $detail->simpanan_sukarela }}</td>
                <td style="text-align:right; font-weight:bold; background:#F9FAFB;">{{ $detail->jumlah_simpanan }}</td>
                <td style="text-align:right; color:#B91C1C;">{{ $detail->jumlah_pinjaman > 0 ? $detail->jumlah_pinjaman : '-' }}</td>
                <td style="text-align:right; font-weight:bold; background:#F0FDF4;">{{ $detail->total_potongan }}</td>
                <td style="text-align:center;">{{ $detail->status }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="2" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ $totalPokok }}</td>
            <td style="text-align:right;">{{ $totalWajib }}</td>
            <td style="text-align:right;">{{ $totalSukarela }}</td>
            <td style="text-align:right; color:#1D4ED8;">{{ $totalSimpanan }}</td>
            <td style="text-align:right; color:#B91C1C;">{{ $totalPinjaman }}</td>
            <td style="text-align:right; color:#047857;">{{ $totalGrand }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
