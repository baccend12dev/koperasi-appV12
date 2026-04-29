{{-- resources/views/penagihan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Penagihan')

@section('topbar-nav')
    <a href="{{ route('penagihan.index') }}" class="tb-link active">Dashboard</a>
    <a href="{{ route('penagihan.generator') }}" class="tb-link">Tagihan Generator</a>
@endsection

@section('page-title', 'Dashboard Penagihan')

@section('content')
<style>
    .with-sidebar { display:flex; max-width:1400px; margin:0 auto; padding:24px; gap:24px; font-family:'Inter',system-ui,sans-serif; }
    .sidebar-panel { flex:0 0 240px; background:#fff; border:1px solid #e0e0e0; border-radius:10px; padding:20px; height:fit-content; box-shadow:0 1px 3px rgba(0,0,0,.05); }
    .main-panel { flex:1; min-width:0; }
    .filter-title { font-size:11px; font-weight:700; color:#5f6368; text-transform:uppercase; margin-bottom:10px; letter-spacing:.5px; }
    .filter-item { display:block; padding:7px 11px; color:#3c4043; text-decoration:none; font-size:13px; border-radius:6px; margin-bottom:3px; transition:background .15s,color .15s; }
    .filter-item:hover { background:#f1f3f4; }
    .filter-item.active { background:#e8f0fe; color:#1a73e8; font-weight:600; }
    .stat-card { background:#fff; border-radius:10px; padding:18px 20px; border:1px solid #e0e0e0; box-shadow:0 1px 3px rgba(0,0,0,.05); }
    .stat-title { font-size:11px; font-weight:700; color:#5f6368; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px; display:block; }
    .stat-value { font-size:22px; font-weight:700; color:#111827; }
    .table-custom { width:100%; border-collapse:collapse; }
    .table-custom th { text-align:left; padding:11px 16px; font-size:11px; font-weight:700; color:#5f6368; text-transform:uppercase; border-bottom:1px solid #e0e0e0; background:#fafafa; }
    .table-custom td { padding:14px 16px; font-size:13px; color:#3c4043; border-bottom:1px solid #f1f3f4; vertical-align:top; }
    .badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:12px; font-size:11px; font-weight:700; }
    .badge-lunas { background:#e6f4ea; color:#137333; }
    .badge-belum { background:#fce8e6; color:#c5221f; }
    .badge-mandiri { background:#DBEAFE; color:#1D4ED8; }
    .content-block { background:#fff; border:1px solid #e0e0e0; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden; }
    .section-divider { display:flex; align-items:center; gap:12px; margin:24px 0 16px 0; }
    .section-divider-line { flex:1; height:1px; background:#E5E7EB; }
    .section-divider-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; padding:3px 12px; border-radius:20px; }
</style>

@php
    $hasPeriode = $billGabungan || $billMandiri;
    $defaultTab = $billGabungan ? 'gabungan' : 'mandiri';
@endphp

<div class="with-sidebar" x-data="{ tab: '{{ $defaultTab }}' }">

    {{-- Sidebar Filter Periode --}}
    <div class="sidebar-panel">
        <div class="filter-title" style="margin-bottom:14px;">Periode Tagihan</div>
        @forelse($availableBills->groupBy(fn($b) => \Carbon\Carbon::parse($b->tgl_generate)->format('Y')) as $year => $billsByYear)
            <div style="font-weight:700;color:#111;margin:14px 0 6px 0;font-size:13px;">{{ $year }}</div>
            @foreach($billsByYear as $bill)
                @php
                    $m      = \Carbon\Carbon::parse($bill->tgl_generate)->format('n');
                    $mName  = \Carbon\Carbon::parse($bill->tgl_generate)->translatedFormat('F');
                    $isActive = ($selectedYear == $year && $selectedMonth == $m);
                @endphp
                <a href="{{ route('penagihan.index', ['year' => $year, 'month' => $m]) }}"
                   class="filter-item {{ $isActive ? 'active' : '' }}">
                    {{ $mName }}
                </a>
            @endforeach
        @empty
            <div style="font-size:13px;color:#888;">Belum ada riwayat tagihan.</div>
        @endforelse
    </div>

    {{-- Main Content --}}
    <div class="main-panel">

        @if($hasPeriode)

            {{-- Tab Bar --}}
            <div style="display:flex;gap:4px;border-bottom:2px solid #E5E7EB;margin-bottom:20px;">
                @if($billGabungan)
                <button @click="tab='gabungan'" type="button"
                    :style="tab==='gabungan' ? 'border-bottom:2px solid #059669;color:#059669;' : 'border-bottom:2px solid transparent;color:#6B7280;'"
                    style="padding:9px 18px;font-size:13px;font-weight:700;border:none;background:transparent;cursor:pointer;display:inline-flex;align-items:center;gap:6px;margin-bottom:-2px;transition:.15s;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    Tagihan Perusahaan
                    <span style="font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;background:#DCFCE7;color:#15803D;">{{ $detailsGabungan->count() }}</span>
                </button>
                @endif
                @if($billMandiri)
                <button @click="tab='mandiri'" type="button"
                    :style="tab==='mandiri' ? 'border-bottom:2px solid #1D4ED8;color:#1D4ED8;' : 'border-bottom:2px solid transparent;color:#6B7280;'"
                    style="padding:9px 18px;font-size:13px;font-weight:700;border:none;background:transparent;cursor:pointer;display:inline-flex;align-items:center;gap:6px;margin-bottom:-2px;transition:.15s;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    Tagihan Mandiri
                    <span style="font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;background:#DBEAFE;color:#1D4ED8;">{{ $detailsMandiri->count() }}</span>
                </button>
                @endif
            </div>

            {{-- ══ TAB GABUNGAN ══ --}}
            @if($billGabungan)
            <div x-show="tab==='gabungan'" x-transition>
                @php
                    $lunas = $detailsGabungan->where('status','Lunas')->sum('total_potongan');
                    $belum = $detailsGabungan->where('status','!=','Lunas')->sum('total_potongan');
                @endphp

                {{-- Stat Cards --}}
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
                    <div class="stat-card">
                        <span class="stat-title">Total Potongan Gaji</span>
                        <span class="stat-value" style="color:#059669;">Rp {{ number_format($billGabungan->total_amount, 0, ',', '.') }}</span>
                        <span style="font-size:11px;color:#6B7280;margin-top:4px;display:block;">{{ $detailsGabungan->count() }} anggota</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-title">Sudah Diproses</span>
                        <span class="stat-value" style="color:#15803D;">Rp {{ number_format($lunas, 0, ',', '.') }}</span>
                        <span style="font-size:11px;color:#6B7280;margin-top:4px;display:block;">{{ $detailsGabungan->where('status','Lunas')->count() }} anggota lunas</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-title">Belum Diproses</span>
                        <span class="stat-value" style="color:#DC2626;">Rp {{ number_format($belum, 0, ',', '.') }}</span>
                        <span style="font-size:11px;color:#6B7280;margin-top:4px;display:block;">{{ $detailsGabungan->where('status','!=','Lunas')->count() }} anggota pending</span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="content-block">
                    <div style="padding:16px 20px;border-bottom:1px solid #e0e0e0;background:#F0FDF4;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <h3 style="margin:0;font-size:14px;font-weight:700;color:#065F46;">Rincian Potongan Gaji — {{ $billGabungan->periode }}</h3>
                            <p style="margin:2px 0 0;font-size:11px;color:#6B7280;">Simpanan + Pinjaman Potong Gaji</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="badge {{ $billGabungan->status == 'Paid' ? 'badge-lunas' : ($billGabungan->status == 'Partial' ? '' : 'badge-belum') }}"
                                  style="{{ $billGabungan->status == 'Partial' ? 'background:#FEF3C7;color:#B45309;' : '' }}">
                                {{ $billGabungan->status }}
                            </span>
                            <a href="{{ route('penagihan.show', $billGabungan->id) }}" style="font-size:12px;font-weight:600;color:#059669;text-decoration:none;border:1px solid #BBF7D0;background:#ECFDF5;padding:4px 12px;border-radius:6px;">
                                Kelola →
                            </a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Anggota</th>
                                    <th style="text-align:right;">Simpanan</th>
                                    <th style="text-align:right;">Pinjaman Gaji</th>
                                    <th style="text-align:right;">Total Potongan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailsGabungan as $d)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;color:#111;">{{ $d->anggota->nama_anggota }}</div>
                                        <div style="font-size:11px;color:#888;">NIK: {{ $d->anggota->nik }}</div>
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="font-weight:500;">Rp {{ number_format($d->jumlah_simpanan, 0, ',', '.') }}</div>
                                        @if($d->jumlah_simpanan > 0)
                                        <div style="font-size:10px;color:#888;margin-top:3px;">
                                            P:{{ number_format($d->simpanan_pokok,0,',','.') }} | W:{{ number_format($d->simpanan_wajib,0,',','.') }} | S:{{ number_format($d->simpanan_sukarela,0,',','.') }}
                                        </div>
                                        @endif
                                    </td>
                                    <td style="text-align:right;font-weight:500;">
                                        {{ $d->jumlah_pinjaman > 0 ? 'Rp '.number_format($d->jumlah_pinjaman,0,',','.') : '-' }}
                                    </td>
                                    <td style="text-align:right;font-weight:700;color:#111827;">Rp {{ number_format($d->total_potongan,0,',','.') }}</td>
                                    <td>
                                        <span class="badge {{ $d->status=='Lunas' ? 'badge-lunas' : 'badge-belum' }}">{{ $d->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" style="text-align:center;color:#888;padding:32px 16px;">Tidak ada data rincian.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- ══ TAB MANDIRI ══ --}}
            @if($billMandiri)
            <div x-show="tab==='mandiri'" x-transition style="display:none;">
                @php
                    $lunasMandiri = $detailsMandiri->where('status','Lunas')->sum('total_potongan');
                    $belumMandiri = $detailsMandiri->where('status','!=','Lunas')->sum('total_potongan');
                @endphp

                {{-- Stat Cards --}}
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
                    <div class="stat-card" style="border-color:#BFDBFE;">
                        <span class="stat-title">Total Cicilan Mandiri</span>
                        <span class="stat-value" style="color:#1D4ED8;">Rp {{ number_format($billMandiri->total_amount, 0, ',', '.') }}</span>
                        <span style="font-size:11px;color:#6B7280;margin-top:4px;display:block;">{{ $detailsMandiri->count() }} anggota</span>
                    </div>
                    <div class="stat-card" style="border-color:#BBF7D0;">
                        <span class="stat-title">Sudah Dibayar</span>
                        <span class="stat-value" style="color:#15803D;">Rp {{ number_format($lunasMandiri, 0, ',', '.') }}</span>
                        <span style="font-size:11px;color:#6B7280;margin-top:4px;display:block;">{{ $detailsMandiri->where('status','Lunas')->count() }} anggota lunas</span>
                    </div>
                    <div class="stat-card" style="border-color:#FECACA;">
                        <span class="stat-title">Belum Dibayar</span>
                        <span class="stat-value" style="color:#DC2626;">Rp {{ number_format($belumMandiri, 0, ',', '.') }}</span>
                        <span style="font-size:11px;color:#6B7280;margin-top:4px;display:block;">{{ $detailsMandiri->where('status','!=','Lunas')->count() }} anggota pending</span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="content-block" style="border-color:#BFDBFE;">
                    <div style="padding:16px 20px;border-bottom:1px solid #BFDBFE;background:#EFF6FF;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <h3 style="margin:0;font-size:14px;font-weight:700;color:#1E40AF;">Rincian Cicilan Mandiri — {{ $billMandiri->periode }}</h3>
                            <p style="margin:2px 0 0;font-size:11px;color:#6B7280;">Pinjaman dibayar langsung oleh anggota</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="badge {{ $billMandiri->status == 'Paid' ? 'badge-lunas' : ($billMandiri->status == 'Partial' ? '' : 'badge-belum') }}"
                                  style="{{ $billMandiri->status == 'Partial' ? 'background:#FEF3C7;color:#B45309;' : '' }}">
                                {{ $billMandiri->status }}
                            </span>
                            <a href="{{ route('penagihan.show', $billMandiri->id) }}" style="font-size:12px;font-weight:600;color:#1D4ED8;text-decoration:none;border:1px solid #BFDBFE;background:#EFF6FF;padding:4px 12px;border-radius:6px;">
                                Kelola →
                            </a>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Anggota</th>
                                    <th style="text-align:right;">Cicilan Mandiri</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailsMandiri as $d)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;color:#111;">{{ $d->anggota->nama_anggota }}</div>
                                        <div style="font-size:11px;color:#1D4ED8;">NIK: {{ $d->anggota->nik }}</div>
                                    </td>
                                    <td style="text-align:right;font-weight:700;color:#B91C1C;font-size:14px;">
                                        Rp {{ number_format($d->jumlah_pinjaman, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $d->status=='Lunas' ? 'badge-lunas' : 'badge-mandiri' }}">
                                            {{ $d->status == 'Lunas' ? 'Lunas' : 'Belum Bayar' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="text-align:center;color:#888;padding:32px 16px;">Tidak ada data cicilan mandiri.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        @else
            {{-- Empty state --}}
            <div class="content-block" style="padding:60px 20px;text-align:center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 16px auto;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <div style="font-size:16px;font-weight:600;color:#475569;margin-bottom:8px;">Tidak Ada Data Tagihan</div>
                <div style="font-size:14px;color:#64748b;">Pilih periode dari sidebar, atau gunakan <a href="{{ route('penagihan.generator') }}" style="color:#1D4ED8;font-weight:600;">Tagihan Generator</a> untuk membuat tagihan baru.</div>
            </div>
        @endif

    </div>{{-- end main-panel --}}
</div>
@endsection
