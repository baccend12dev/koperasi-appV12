<table border="1">
    <tr>
        <td colspan="8" style="font-size:16px; font-weight:bold; text-align:center; padding:10px; background:#EFF6FF; color:#1D4ED8;">
            LAPORAN SALDO SIMPANAN ANGGOTA
        </td>
    </tr>
    <tr>
        <td colspan="8" style="font-size:11px; text-align:center; padding:6px; background:#F9FAFB; color:#374151;">
            Koperasi Karyawan OPI &mdash; Tanggal Ekspor: {{ date('d/m/Y H:i') }}
        </td>
    </tr>
    <tr><td colspan="8"></td></tr>
</table>

<table border="1">
    <thead>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <th style="text-align:center; padding:5px;">NO</th>
            <th style="text-align:center; padding:5px;">NIK</th>
            <th style="text-align:center; padding:5px;">NAMA ANGGOTA</th>
            <th style="text-align:center; padding:5px;">DEPARTEMEN</th>
            <th style="text-align:center; padding:5px;">SIMPANAN POKOK</th>
            <th style="text-align:center; padding:5px;">SIMPANAN WAJIB</th>
            <th style="text-align:center; padding:5px;">SIMPANAN SUKARELA</th>
            <th style="text-align:center; padding:5px;">SALDO AWAL</th>
            <th style="text-align:center; padding:5px;">TOTAL SIMPANAN</th>
            <th style="text-align:center; padding:5px;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach($members_export as $item)
            @php
                $pokok = $item->total_pokok ?? 0;
                $wajib = $item->total_wajib ?? 0;
                $sukarela = $item->total_sukarela ?? 0;
                $saldoAwal = $item->total_saldo_awal ?? 0;
                $total = $pokok + $wajib + $sukarela + $saldoAwal;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="mso-number-format:'\@'; text-align:center;">{{ $item->nik ?? '-' }}</td>
                <td>{{ $item->nama_anggota }}</td>
                <td>{{ $item->departemen->nama ?? '-' }}</td>
                <td style="text-align:right;">{{ $pokok }}</td>
                <td style="text-align:right;">{{ $wajib }}</td>
                <td style="text-align:right;">{{ $sukarela }}</td>
                <td style="text-align:right;">{{ $saldoAwal }}</td>
                <td style="text-align:right; font-weight:bold;">{{ $total }}</td>
                <td style="text-align:center;">{{ strtoupper($item->status_anggota) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="4" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ $sumPokok }}</td>
            <td style="text-align:right;">{{ $sumWajib }}</td>
            <td style="text-align:right;">{{ $sumSukarela }}</td>
            <td style="text-align:right;">{{ $sumSaldoAwal }}</td>
            <td style="text-align:right; color:#1D4ED8;">{{ $grandTotal }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
