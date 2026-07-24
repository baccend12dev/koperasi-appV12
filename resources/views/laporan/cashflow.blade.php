@extends('laporan.layout')

@section('laporan-title', 'Laporan Cashflow (Arus Kas)')
@section('laporan-subtitle', 'Monitoring perputaran uang masuk (inflow) & uang keluar (outflow) Koperasi')

@section('laporan-content')
    {{-- Filter Periode --}}
    <div class="filter-card" style="background: #ffffff; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('laporan.cashflow') }}" style="display: flex; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #4B5563; margin-bottom: 4px;">Pilih Periode</label>
                <input type="month" name="periode" value="{{ $periode }}" style="height: 34px; padding: 6px 12px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 13px; color: #1F2937; outline: none; background: #fff;">
            </div>
            <button type="submit" style="height: 34px; padding: 0 16px; border: 1px solid #2563EB; border-radius: 6px; background: #2563EB; color: #fff; font-size: 12px; font-weight: 600; cursor: pointer;">
                Filter Cashflow
            </button>
        </form>
    </div>

    {{-- Top Cards Summary --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- Inflow -->
        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #065F46; text-transform: uppercase; letter-spacing: 0.05em;">Kas Masuk (Inflow)</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #10B981; border-radius: 50%; color: #fff;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </span>
            </div>
            <div style="font-size: 22px; font-weight: 700; color: #047857;">Rp {{ number_format($totalInflow, 0, ',', '.') }}</div>
            <div style="font-size: 11px; color: #059669; margin-top: 4px;">Setoran Simpanan & Angsuran</div>
        </div>

        <!-- Outflow -->
        <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #991B1B; text-transform: uppercase; letter-spacing: 0.05em;">Kas Keluar (Outflow)</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #EF4444; border-radius: 50%; color: #fff;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                </span>
            </div>
            <div style="font-size: 22px; font-weight: 700; color: #B91C1C;">Rp {{ number_format($totalOutflow, 0, ',', '.') }}</div>
            <div style="font-size: 11px; color: #DC2626; margin-top: 4px;">Pencairan Pinjaman & Penarikan</div>
        </div>

        <!-- Net Cashflow -->
        <div style="background: {{ $netCashflow >= 0 ? '#EFF6FF' : '#FFFBEB' }}; border: 1px solid {{ $netCashflow >= 0 ? '#BFDBFE' : '#FDE68A' }}; border-radius: 10px; padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: {{ $netCashflow >= 0 ? '#1E40AF' : '#92400E' }}; text-transform: uppercase; letter-spacing: 0.05em;">Arus Kas Bersih (Net)</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: {{ $netCashflow >= 0 ? '#3B82F6' : '#F59E0B' }}; border-radius: 50%; color: #fff;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><line x1="16" y1="21" x2="16" y2="17"/><line x1="12" y1="21" x2="12" y2="15"/><line x1="8" y1="21" x2="8" y2="19"/></svg>
                </span>
            </div>
            <div style="font-size: 22px; font-weight: 700; color: {{ $netCashflow >= 0 ? '#1D4ED8' : '#B45309' }};">Rp {{ number_format($netCashflow, 0, ',', '.') }}</div>
            <div style="font-size: 11px; color: {{ $netCashflow >= 0 ? '#2563EB' : '#D97706' }}; margin-top: 4px;">{{ $netCashflow >= 0 ? 'Surplus Likuiditas' : 'Defisit Likuiditas' }}</div>
        </div>
    </div>

    {{-- Rincian Table --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
        <!-- Detail Inflow -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="padding: 12px 16px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB; font-weight: 700; font-size: 13px; color: #111827;">
                Rincian Kas Masuk (Penerimaan)
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tbody>
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 16px; color: #374151;">Setoran Simpanan Anggota</td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #10B981;">Rp {{ number_format($simpananMasuk, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 16px; color: #374151;">Pembayaran Angsuran Pinjaman</td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #10B981;">Rp {{ number_format($angsuranMasuk, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background: #F9FAFB; font-weight: 700;">
                        <td style="padding: 12px 16px; color: #111827;">Total Penerimaan</td>
                        <td style="padding: 12px 16px; text-align: right; color: #047857;">Rp {{ number_format($totalInflow, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Detail Outflow -->
        <div style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="padding: 12px 16px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB; font-weight: 700; font-size: 13px; color: #111827;">
                Rincian Kas Keluar (Pengeluaran)
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tbody>
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 16px; color: #374151;">Pencairan Pinjaman Anggota</td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #EF4444;">Rp {{ number_format($pencairanPinjaman, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #F3F4F6;">
                        <td style="padding: 12px 16px; color: #374151;">Penarikan Simpanan Anggota</td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #EF4444;">Rp {{ number_format($penarikanSimpanan, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background: #F9FAFB; font-weight: 700;">
                        <td style="padding: 12px 16px; color: #111827;">Total Pengeluaran</td>
                        <td style="padding: 12px 16px; text-align: right; color: #B91C1C;">Rp {{ number_format($totalOutflow, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
