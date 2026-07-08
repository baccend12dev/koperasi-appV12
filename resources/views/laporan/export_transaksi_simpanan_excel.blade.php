<table border="1">
    <tr>
        <td colspan="11" style="font-size:16px; font-weight:bold; text-align:center; padding:10px; background:#EFF6FF; color:#1D4ED8;">
            LAPORAN RINCIAN TRANSAKSI SIMPANAN ANGGOTA
        </td>
    </tr>
    <tr>
        <td colspan="11" style="font-size:11px; text-align:center; padding:6px; background:#F9FAFB; color:#374151;">
            Koperasi Karyawan OPI &mdash; 
            @if($periode)
                Periode: {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }} &mdash; 
            @endif
            Tanggal Ekspor: {{ date('d/m/Y H:i') }}
        </td>
    </tr>
    @if(!empty($filterSearch) || !empty($filterDepartemen) || !empty($filterJenis))
    <tr>
        <td colspan="11" style="font-size:11px; text-align:center; padding:6px; background:#F3F4F6; color:#374151; font-weight:bold;">
            Filter &mdash; 
            @if(!empty($filterSearch)) Pencarian: "{{ $filterSearch }}" &mdash; @endif
            @if(!empty($filterDepartemen)) Departemen: {{ $filterDepartemen }} &mdash; @endif
            @if(!empty($filterJenis)) Jenis Transaksi: {{ $filterJenis }} &mdash; @endif
        </td>
    </tr>
    @endif
    <tr><td colspan="11"></td></tr>
</table>

<table border="1">
    <thead>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <th style="text-align:center; padding:5px;">NO</th>
            <th style="text-align:center; padding:5px;">TANGGAL TRANSAKSI</th>
            <th style="text-align:center; padding:5px;">NIK</th>
            <th style="text-align:center; padding:5px;">NAMA ANGGOTA</th>
            <th style="text-align:center; padding:5px;">DEPARTEMEN</th>
            <th style="text-align:center; padding:5px;">PERIODE</th>
            <th style="text-align:center; padding:5px;">KETERANGAN</th>
            <th style="text-align:center; padding:5px;">SIMPANAN POKOK</th>
            <th style="text-align:center; padding:5px;">SIMPANAN WAJIB</th>
            <th style="text-align:center; padding:5px;">SIMPANAN SUKARELA</th>
            <th style="text-align:center; padding:5px;">TOTAL TRANSAKSI</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach($transaksi_export as $item)
            @php
                $pokok = $item->simpanan_pokok ?? 0;
                $wajib = $item->simpanan_wajib ?? 0;
                $sukarela = $item->simpanan_sukarela ?? 0;
                $total = $pokok + $wajib + $sukarela;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td style="text-align:center;">{{ $item->transaction_date }}</td>
                <td style="mso-number-format:'\@'; text-align:center;">{{ $item->anggota->nik ?? '-' }}</td>
                <td>{{ $item->anggota->nama_anggota ?? 'Unknown' }}</td>
                <td>{{ $item->anggota->departemen->nama ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->periode }}</td>
                <td>{{ $item->description ?? '-' }}</td>
                <td style="text-align:right;">{{ $pokok }}</td>
                <td style="text-align:right;">{{ $wajib }}</td>
                <td style="text-align:right;">{{ $sukarela }}</td>
                <td style="text-align:right; font-weight:bold;">{{ $total }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#F3F4F6; font-weight:bold;">
            <td colspan="7" style="text-align:center;">TOTAL KESELURUHAN</td>
            <td style="text-align:right;">{{ $sumPokok }}</td>
            <td style="text-align:right;">{{ $sumWajib }}</td>
            <td style="text-align:right;">{{ $sumSukarela }}</td>
            <td style="text-align:right; color:#1D4ED8;">{{ $grandTotal }}</td>
        </tr>
    </tfoot>
</table>
