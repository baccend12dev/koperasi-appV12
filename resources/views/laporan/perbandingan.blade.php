@extends('laporan.layout')

@section('laporan-title', 'Laporan Perbandingan (Komparasi Periode)')
@section('laporan-subtitle', 'Analisis komparasi kinerja keuangan antar 2 periode')

@section('laporan-content')
    {{-- Filter Periode Komparasi --}}
    <div class="filter-card" style="background: #ffffff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('laporan.perbandingan') }}" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px;">Periode Acuan (A)</label>
                <input type="month" name="periode1" value="{{ $periode1 }}" style="height: 34px; padding: 6px 12px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 13px; color: #1F2937; outline: none; background: #fff;">
            </div>
            <div>
                <label style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px;">Periode Pembanding (B)</label>
                <input type="month" name="periode2" value="{{ $periode2 }}" style="height: 34px; padding: 6px 12px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 13px; color: #1F2937; outline: none; background: #fff;">
            </div>
            <button type="submit" style="height: 34px; padding: 0 16px; border: 1px solid #2563EB; border-radius: 6px; background: #2563EB; color: #fff; font-size: 12px; font-weight: 600; cursor: pointer;">
                Bandingkan Periode
            </button>
        </form>
    </div>

    {{-- Tabel Perbandingan --}}
    <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                    <th style="padding: 12px 16px; text-align: left; font-weight: 700; color: #374151;">Indikator Kinerja</th>
                    <th style="padding: 12px 16px; text-align: right; font-weight: 700; color: #2563EB;">
                        Periode A ({{ \Carbon\Carbon::parse($periode1)->translatedFormat('F Y') }})
                    </th>
                    <th style="padding: 12px 16px; text-align: right; font-weight: 700; color: #059669;">
                        Periode B ({{ \Carbon\Carbon::parse($periode2)->translatedFormat('F Y') }})
                    </th>
                    <th style="padding: 12px 16px; text-align: right; font-weight: 700; color: #374151;">Pertumbuhan (Selisih)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = [
                        ['label' => 'Total Setoran Simpanan', 'key' => 'setoran_simpanan', 'is_currency' => true],
                        ['label' => 'Total Penarikan Simpanan', 'key' => 'penarikan_simpanan', 'is_currency' => true],
                        ['label' => 'Total Pencairan Pinjaman', 'key' => 'pencairan_pinjaman', 'is_currency' => true],
                        ['label' => 'Total Angsuran Masuk', 'key' => 'angsuran_masuk', 'is_currency' => true],
                        ['label' => 'Jumlah Pinjaman Aktif', 'key' => 'total_pinjaman_aktif', 'is_currency' => false],
                    ];
                @endphp

                @foreach($items as $item)
                    @php
                        $v1 = $metrics1[$item['key']] ?? 0;
                        $v2 = $metrics2[$item['key']] ?? 0;
                        $diff = $v2 - $v1;
                        $isPositive = $diff >= 0;
                    @endphp
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 16px; color: #111827; font-weight: 600;">{{ $item['label'] }}</td>
                        <td style="padding: 12px 16px; text-align: right; color: #4B5563;">
                            {{ $item['is_currency'] ? 'Rp ' . number_format($v1, 0, ',', '.') : number_format($v1) }}
                        </td>
                        <td style="padding: 12px 16px; text-align: right; color: #111827; font-weight: 600;">
                            {{ $item['is_currency'] ? 'Rp ' . number_format($v2, 0, ',', '.') : number_format($v2) }}
                        </td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: 700; color: {{ $isPositive ? '#059669' : '#DC2626' }};">
                            {{ $isPositive ? '+' : '' }}{{ $item['is_currency'] ? 'Rp ' . number_format($diff, 0, ',', '.') : number_format($diff) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
