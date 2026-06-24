{{--
    resources/views/laporan/layout.blade.php
    ═══════════════════════════════════════════
    Layout induk untuk seluruh sub-menu Laporan.
    Extend dari layouts.app lalu sediakan:
      @yield('laporan-title')      — judul halaman aktif
      @yield('laporan-subtitle')   — subjudul opsional
      @yield('laporan-actions')    — tombol/aksi di header konten
      @yield('laporan-content')    — konten utama halaman
      @stack('laporan-scripts')    — script spesifik halaman
--}}
@extends('layouts.app')

@section('title', trim(strip_tags($__env->yieldContent('laporan-title', 'Laporan'))) ?: 'Laporan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('laporan.index') }}" class="tb-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
        Laporan
    </a>
@endsection

@section('page-title')
    @yield('laporan-title', 'Laporan')
@endsection

@section('subbar-actions')
    @yield('laporan-actions')
@endsection

{{-- ── Sidebar laporan ── --}}
@section('sidebar')
<style>
    /* ── Laporan Sidebar ── */
    .lap-nav { list-style: none; margin: 0; padding: 0; }

    /* Section label */
    .lap-nav-label {
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #9CA3AF;
        padding: 18px 14px 6px 14px;
        display: block;
    }
    .lap-nav-label:first-child { padding-top: 8px; }

    /* Nav item */
    .lap-nav-item { margin: 1px 0; }

    .lap-nav-link {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        transition: background .13s, color .13s;
        position: relative;
    }
    .lap-nav-link:hover {
        background: #F3F4F6;
        color: #111827;
    }
    .lap-nav-link.active {
        background: #EFF6FF;
        color: #1D4ED8;
        font-weight: 700;
    }
    .lap-nav-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 6px; bottom: 6px;
        width: 3px;
        background: #1D4ED8;
        border-radius: 0 3px 3px 0;
    }
    .lap-nav-link svg { flex-shrink: 0; opacity: .7; }
    .lap-nav-link.active svg { opacity: 1; }

    /* Sub items (indented) */
    .lap-nav-sub { margin: 0; padding: 0; list-style: none; }
    .lap-nav-sub .lap-nav-link {
        padding-left: 36px;
        font-size: 12px;
    }

    /* Parent collapsible toggle */
    .lap-nav-parent {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        border-radius: 8px;
        transition: background .13s;
        user-select: none;
    }
    .lap-nav-parent:hover { background: #F3F4F6; }
    .lap-nav-parent svg { flex-shrink: 0; opacity: .7; }
    .lap-nav-parent .caret {
        margin-left: auto;
        transition: transform .2s;
    }
    .lap-nav-parent.open .caret { transform: rotate(90deg); }
    .lap-nav-parent.open { color: #1D4ED8; }

    .lap-sub-wrap {
        overflow: hidden;
        max-height: 0;
        transition: max-height .2s ease;
    }
    .lap-sub-wrap.open { max-height: 200px; }
</style>

<ul class="lap-nav" id="laporan-sidebar-nav">

    {{-- ── Dashboard / Ringkasan ── --}}
    <li class="lap-nav-item">
        <a href="{{ route('laporan.index') }}"
           class="lap-nav-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
            Dashboard Ringkasan
        </a>
    </li>

    {{-- ── Separator ── --}}
    <li><span class="lap-nav-label">Analitik</span></li>

    {{-- ── Cashflow ── --}}
    <li class="lap-nav-item">
        <a href="#"
           class="lap-nav-link {{ request()->routeIs('laporan.cashflow') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            Cashflow
        </a>
    </li>

    {{-- ── Perbandingan ── --}}
    <li class="lap-nav-item">
        <a href="#"
           class="lap-nav-link {{ request()->routeIs('laporan.perbandingan') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Perbandingan
        </a>
    </li>

    {{-- ── Separator ── --}}
    <li><span class="lap-nav-label">Data Anggota</span></li>

    {{-- ── Simpanan ── --}}
    <li class="lap-nav-item">
        <a href="{{ route('laporan.simpanan') }}"
           class="lap-nav-link {{ request()->routeIs('laporan.simpanan') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Saldo Simpanan
        </a>
    </li>

    {{-- ── Transaksi Simpanan ── --}}
    <li class="lap-nav-item">
        <a href="{{ route('laporan.transaksi_simpanan') }}"
           class="lap-nav-link {{ request()->routeIs('laporan.transaksi_simpanan') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Transaksi Simpanan
        </a>
    </li>

    {{-- ── Pinjaman ── --}}
    <li class="lap-nav-item">
        <a href="#"
           class="lap-nav-link {{ request()->routeIs('laporan.pinjaman') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
            Pinjaman
        </a>
    </li>

    {{-- ── Posisi Anggota ── --}}
    <li class="lap-nav-item">
        <a href="#"
           class="lap-nav-link {{ request()->routeIs('laporan.posisi-anggota') ? 'active' : '' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Posisi Anggota
        </a>
    </li>

    {{-- ── Separator ── --}}
    <li><span class="lap-nav-label">Pembayaran Pinjaman</span></li>

    {{-- ── Pembayaran Pinjaman (collapsible parent) ── --}}
    <li class="lap-nav-item">
        <div class="lap-nav-parent {{ request()->routeIs('laporan.pembayaran*') ? 'open' : '' }}"
             onclick="toggleLapSub(this)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            Pembayaran Pinjaman
            <svg class="caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </div>
        <div class="lap-sub-wrap {{ request()->routeIs('laporan.pembayaran*') ? 'open' : '' }}">
            <ul class="lap-nav-sub">
                <li class="lap-nav-item">
                    <a href="#"
                       class="lap-nav-link {{ request()->routeIs('laporan.pembayaran.angsuran') ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 11 12 14 22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        Angsuran
                    </a>
                </li>
                <li class="lap-nav-item">
                    <a href="#"
                       class="lap-nav-link {{ request()->routeIs('laporan.pembayaran.pelunasan') ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Pelunasan
                    </a>
                </li>
                <li class="lap-nav-item">
                    <a href="#"
                       class="lap-nav-link {{ request()->routeIs('laporan.pembayaran.semua') ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/>
                            <line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                        Semua Pembayaran
                    </a>
                </li>
            </ul>
        </div>
    </li>

</ul>

@push('laporan-scripts')
<script>
function toggleLapSub(el) {
    el.classList.toggle('open');
    const wrap = el.nextElementSibling;
    wrap.classList.toggle('open');
}
// Auto-expand if child is active on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.lap-nav-parent').forEach(parent => {
        const hasActive = parent.nextElementSibling?.querySelector('.active');
        if (hasActive) {
            parent.classList.add('open');
            parent.nextElementSibling.classList.add('open');
        }
    });
});
</script>
@endpush

@endsection

{{-- ══════════════════════════════════════════════════
     KONTEN UTAMA
     Setiap halaman laporan yang extends layout ini
     akan mengisi section berikut:
══════════════════════════════════════════════════ --}}
@section('content')
<div style="padding: 24px; max-width: 1300px; margin: 0 auto;">

    {{-- Page header dalam konten (opsional, setiap halaman bisa override) --}}
    @hasSection('laporan-subtitle')
    <div style="margin-bottom: 20px;">
        <div style="font-size: 13px; color: #6B7280; margin: 0;">@yield('laporan-subtitle')</div>
    </div>
    @endif

    {{-- Flash messages (jika ada yang dikirim controller) --}}
    @if(session('success'))
    <div style="background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">
        {{ session('error') }}
    </div>
    @endif

    {{-- Konten halaman --}}
    @yield('laporan-content')

</div>
@stack('laporan-scripts')
@endsection
