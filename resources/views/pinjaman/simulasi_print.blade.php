<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Simulasi Pinjaman - {{ $anggota->nama_anggota ?? 'Anggota' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #FFFFFF;
            color: #111827;
            padding: 20px;
            font-size: 11px;
            line-height: 1.4;
        }

        /* Printable Page Layout */
        .page {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
        }

        /* Header / Kop Surat */
        .kop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #000000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .kop-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kop-logo {
            width: 50px;
            height: 50px;
            background: #107C41;
            color: #fff;
            font-weight: 900;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .kop-title h1 {
            font-size: 16px;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-title p {
            font-size: 10px;
            color: #475569;
        }

        .doc-info {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }

        .doc-info strong {
            display: block;
            font-size: 12px;
            color: #0F172A;
        }

        /* Section Styling */
        .section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            background: #F1F5F9;
            color: #0F172A;
            padding: 4px 8px;
            border-left: 4px solid #107C41;
            margin-top: 14px;
            margin-bottom: 8px;
        }

        /* Grid info */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 16px;
            margin-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #E2E8F0;
            padding-bottom: 2px;
        }

        .info-label {
            color: #64748B;
            font-weight: 600;
        }

        .info-value {
            font-weight: 700;
            color: #0F172A;
        }

        /* Tables (Excel style) */
        table.print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
            font-size: 10.5px;
        }

        table.print-table th {
            background: #107C41;
            color: #FFFFFF;
            padding: 5px 6px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            border: 1px solid #0E6B37;
        }

        table.print-table td {
            padding: 4px 6px;
            border: 1px solid #CBD5E1;
            color: #000000;
            font-weight: 600;
        }

        table.print-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Highlight Boxes */
        .highlight-box {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .highlight-title {
            font-size: 11px;
            font-weight: 700;
            color: #166534;
        }

        .highlight-val {
            font-size: 16px;
            font-weight: 800;
            color: #15803D;
        }

        /* Signatures */
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-space {
            height: 50px;
        }

        .sig-name {
            font-weight: 700;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .sig-role {
            font-size: 10px;
            color: #64748B;
            margin-top: 2px;
        }

        /* Action bar for screen */
        .no-print-bar {
            background: #0F172A;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .btn-print {
            background: #107C41;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #0D6937;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .page {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi (Hanya Tampil di Layar Monitor) -->
    <div class="no-print-bar">
        <div>
            <strong>Pratinjau Cetak Simulasi Pinjaman</strong>
            <div style="font-size: 11px; opacity: 0.8;">Gunakan opsi "Save as PDF" di dialog cetak browser untuk menyimpan PDF.</div>
        </div>
        <button onclick="window.print()" class="btn-print">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak / Save PDF
        </button>
    </div>

    <div class="page">
        <!-- Kop Surat -->
        <div class="kop-header">
            <div class="kop-brand">
                <div class="kop-logo">KOP</div>
                <div class="kop-title">
                    <h1>Koperasi Karyawan OPI</h1>
                    <p>Lembar Analisis & Simulasi Pinjaman Anggota</p>
                </div>
            </div>
            <div class="doc-info">
                <strong>FORM SIMULASI</strong>
                <div>Tanggal: {{ date('d M Y H:i') }}</div>
            </div>
        </div>

        <!-- 1. DATA DIRI ANGGOTA -->
        <div class="section-title">1. Data Diri Anggota</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Nama Anggota:</span>
                <span class="info-value">{{ $anggota->nama_anggota ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIK:</span>
                <span class="info-value">{{ $anggota->nik ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Departemen:</span>
                <span class="info-value">{{ $anggota->departemen->nama_departemen ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jabatan:</span>
                <span class="info-value">{{ $anggota->jabatan ?? '-' }}</span>
            </div>
        </div>

        <!-- 2. DATA SIMPANAN & LIMIT -->
        <div class="section-title">2. Ringkasan Simpanan & Batas Limit</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Simpanan Pokok:</span>
                <span class="info-value">Rp {{ number_format($simpananPokok ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Maksimal Limit Pinjaman:</span>
                <span class="info-value">Rp {{ number_format($maksPinjaman ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Simpanan Wajib:</span>
                <span class="info-value">Rp {{ number_format($simpananWajib ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Pinjaman Aktif:</span>
                <span class="info-value" style="color:#C2410C;">Rp {{ number_format($pinjamanAktifTotal ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Simpanan Sukarela:</span>
                <span class="info-value">Rp {{ number_format($simpananSukarela ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sisa Limit Tersedia:</span>
                <span class="info-value" style="color:#15803D;">Rp {{ number_format(max(0, ($maksPinjaman ?? 0) - ($pinjamanAktifTotal ?? 0)), 0, ',', '.') }}</span>
            </div>
            <div class="info-row" style="grid-column: span 2; border-top: 1px solid #CBD5E1; margin-top: 4px; padding-top: 4px;">
                <span class="info-label" style="font-size: 11px; font-weight: 700; color: #0F172A;">Total Simpanan Keseluruhan:</span>
                <span class="info-value" style="font-size: 12px; font-weight: 800; color: #1E40AF;">Rp {{ number_format($simpananTotal ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 3. DATA PINJAMAN AKTIF -->
        <div class="section-title">3. Daftar Pinjaman Berjalan / Aktif</div>
        <table class="print-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Jenis Pinjaman</th>
                    <th class="text-right">Pokok Pinjaman</th>
                    <th class="text-right">Sisa Tagihan</th>
                    <th class="text-center">Tenor / Sisa</th>
                    <th class="text-right">Cicilan / Bulan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listPinjaman ?? [] as $idx => $p)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $p['jenis_pinjaman'] }}</td>
                        <td class="text-right">Rp {{ number_format($p['jumlah_pinjaman'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($p['sisa_tagihan'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $p['tenor'] }} Bln (Sisa: {{ $p['sisa_tenor'] }} Bln)</td>
                        <td class="text-right" style="color:#B45309;">Rp {{ number_format($p['cicilan_per_bulan'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 10px; color: #64748B;">Tidak ada pinjaman aktif berjalan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(($simulasi['jumlah'] ?? 0) > 0)
        <!-- 4. HASIL SIMULASI PINJAMAN BARU -->
        <div class="section-title">4. Hasil Simulasi Pinjaman Baru</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Jenis Pinjaman Disimulasikan:</span>
                <span class="info-value">{{ $simulasi['nama_jenis'] ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pengajuan Pokok Pinjaman:</span>
                <span class="info-value">Rp {{ number_format($simulasi['jumlah'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jangka Waktu (Tenor):</span>
                <span class="info-value">{{ $simulasi['tenor'] ?? 0 }} Bulan</span>
            </div>
            <div class="info-row">
                <span class="info-label">Bunga Pinjaman:</span>
                <span class="info-value">{{ $simulasi['bunga_persen'] ?? 0 }}% per tahun</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Bunga:</span>
                <span class="info-value">Rp {{ number_format($simulasi['total_bunga'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Pengembalian:</span>
                <span class="info-value">Rp {{ number_format($simulasi['total_pengembalian'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="highlight-box">
            <div>
                <div class="highlight-title">Estimasi Angsuran Baru Per Bulan</div>
                <div style="font-size: 10px; color: #15803D;">Pokok + Bunga bulanan untuk pengajuan baru ini</div>
            </div>
            <div class="highlight-val">Rp {{ number_format($simulasi['cicilan_per_bulan'] ?? 0, 0, ',', '.') }} / bln</div>
        </div>
        @endif

        <!-- 5. ANALISIS TOTAL POTONGAN & KELAYAKAN -->
        <div class="section-title">5. Ringkasan Total Potongan Bulanan</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Total Cicilan Pinjaman Aktif:</span>
                <span class="info-value">Rp {{ number_format($totalCicilanPerBulan ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Potongan Simpanan Wajib Bulanan:</span>
                <span class="info-value">Rp {{ number_format($simpananWajibBulanan ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estimasi Cicilan Baru:</span>
                <span class="info-value">Rp {{ number_format($simulasi['cicilan_per_bulan'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row" style="border-top: 1px solid #CBD5E1; margin-top: 4px; padding-top: 4px;">
                <span class="info-label" style="font-size: 11px; font-weight: 700; color: #0F172A;">Grand Total Potongan Bulanan:</span>
                <span class="info-value" style="font-size: 13px; font-weight: 800; color: #B91C1C;">
                    Rp {{ number_format(($totalCicilanPerBulan ?? 0) + ($simpananWajibBulanan ?? 0) + ($simulasi['cicilan_per_bulan'] ?? 0), 0, ',', '.') }} / bln
                </span>
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signatures">
            <div class="sig-box">
                <div>Anggota Pemohon,</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $anggota->nama_anggota ?? 'Anggota' }}</div>
                <div class="sig-role">NIK: {{ $anggota->nik ?? '-' }}</div>
            </div>
            <div class="sig-box">
                <div>Petugas / Pengurus Koperasi,</div>
                <div class="sig-space"></div>
                <div class="sig-name">( ............................................ )</div>
                <div class="sig-role">Koperasi Karyawan OPI</div>
            </div>
        </div>
    </div>

</body>
</html>
