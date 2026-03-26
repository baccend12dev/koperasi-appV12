{{-- resources/views/Simpanan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Simpanan')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}"
       class="tb-link {{ request()->routeIs('simpanan.index') ? 'active' : '' }}">
        Simpanan Anggota
    </a>
    <a href="{{ route('simpanan.transaksi') }}"
       class="tb-link {{ request()->routeIs('simpanan.transaksi') ? 'active' : '' }}">
        Transaksi
    </a>
    <a href="{{ route('laporan.index') }}"
       class="tb-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        Laporan
    </a>
    <a href="{{ route('simpanan.tagihangenerator') }}"
       class="tb-link {{ request()->routeIs('simpanan.tagihangenerator') ? 'active' : '' }}">
        Tagih Simpanan
    </a>
@endsection

{{-- ── Subbar kiri ── --}}
@section('subbar-actions')
    <a href="{{ route('anggota.create') }}" class="btn-primary">Baru</a>
@endsection

@section('page-title', 'Simpanan Anggota')

@section('page-title-settings')
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M7 1v1M7 12v1M1 7H2M12 7h1M2.5 2.5l.7.7M10.8 10.8l.7.7M2.5 11.5l.7-.7M10.8 3.2l.7-.7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        <circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.3"/>
    </svg>
@endsection

{{-- ── Subbar kanan ── --}}
@section('subbar-search')
    <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari ..."
        autocomplete="off"
    >
@endsection

@section('subbar-pagination')
    <span class="pag-info">
        {{ $simpanan->firstItem() }}–{{ $simpanan->lastItem() }} / {{ $simpanan->total() }}
    </span>
    <a href="{{ $simpanan->previousPageUrl() ?? '#' }}"
       class="pag-btn" {{ $simpanan->onFirstPage() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <a href="{{ $simpanan->nextPageUrl() ?? '#' }}"
       class="pag-btn" {{ !$simpanan->hasMorePages() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M1 1l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
@endsection

@section('subbar-viewmode')
    <button class="vm-btn {{ request('view','list') === 'grid' ? 'active' : '' }}"
            onclick="setView('grid')" title="Kanban">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <rect x="1" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/>
            <rect x="8" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/>
            <rect x="1" y="8" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/>
            <rect x="8" y="8" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/>
        </svg>
    </button>
    <button class="vm-btn {{ request('view','list') === 'list' ? 'active' : '' }}"
            onclick="setView('list')" title="List">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <line x1="2" y1="3.5" x2="12" y2="3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            <line x1="2" y1="7"   x2="12" y2="7"   stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            <line x1="2" y1="10.5" x2="12" y2="10.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
    </button>
@endsection

{{-- ── Sidebar ── --}}
@section('sidebar')
    <div class="sd-section">
        <div class="sd-heading">
            <div style="display:flex;align-items:center;gap:5px">
                <svg class="sd-heading-icon" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <circle cx="5" cy="4.5" r="2.5" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M1 12c0-2.8 1.8-4 4-4s4 1.2 4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    <circle cx="10.5" cy="5" r="1.8" stroke="currentColor" stroke-width="1.1" stroke-opacity=".6"/>
                    <path d="M12.5 11c0-2-1-3.2-2.5-3.5" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-opacity=".6"/>
                </svg>
                DEPARTEMEN
            </div>
            
        </div>

        <a href="{{ route('simpanan.index') }}"
           class="sd-link {{ !request('dept') ? 'active' : '' }}">
            Semua
        </a>

        @foreach($departemen as $dept)
            <a href="{{ route('simpanan.index', ['dept' => $dept->id, 'q' => request('q')]) }}"
               class="sd-link {{ request('dept') == $dept->id ? 'active' : '' }}">
                {{ $dept->nama }}
                <span class="sd-badge">{{ $dept->simpanan_count }}</span>
            </a>
        @endforeach
    </div>
@endsection

{{-- ── Konten utama ── --}}
@section('content')
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="td-check">
                        <input type="checkbox" id="checkAll"
                               onclick="document.querySelectorAll('.row-check').forEach(c=>c.checked=this.checked)">
                    </th>
                    <th style="width:40px"></th>
                    <th>Nama & NIK</th>
                    <th>Tanggal Bergabung</th>
                    <th>Simpanan Wajib</th>
                    <th>Simpanan Pokok</th>
                    <th>Simpanan Sukarela</th>
                    <th>Status Anggota</th>
                    <th class="th-settings">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($simpanan as $item)
                    <tr onclick="window.location='{{ route('simpanan.show', $item) }}'">
                        <td class="td-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="row-check" value="{{ $item->id }}">
                        </td>
                        <td>
                            @if($item->foto)
                                <img class="td-avatar" src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}">
                            @else
                                <div class="td-avatar-placeholder av-{{ $item->avatar_color ?? 'purple' }}">
                                    {{ strtoupper(substr($item->anggota->nama_anggota, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="td-name">
                                <span style="font-weight:500">{{ $item->anggota->nama_anggota }}</span>
                            </div>
                            <div class="td-muted" style="font-size:12px; margin-top:2px;">{{ $item->anggota->nik ?? '—' }}</div>
                        </td>
                        <td>{{ $item->anggota->tgl_bergabung ?? '—' }}</td>
                        <td>{{ number_format($item->simpanan_wajib, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->simpanan_pokok, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->simpanan_sukarela, 2, ',', '.') }}</td>
                        <td>
                            @if($item->anggota->status_anggota == 'active' || $item->anggota->status_anggota == 'Aktif')
                                <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#e6f4ea; color:#137333; font-weight:600;">{{ ucfirst($item->anggota->status_anggota) }}</span>
                            @else
                                <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; background-color:#fce8e6; color:#c5221f; font-weight:600;">{{ ucfirst($item->anggota->status_anggota) }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="th-settings-btn" title="Konfigurasi kolom">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <line x1="1" y1="4" x2="13" y2="4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                    <line x1="1" y1="10" x2="13" y2="10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                    <circle cx="4.5" cy="4" r="1.5" fill="white" stroke="currentColor" stroke-width="1.1"/>
                                    <circle cx="9.5" cy="10" r="1.5" fill="white" stroke="currentColor" stroke-width="1.1"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px 16px;color:var(--text-3)">
                            Tidak ada data ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
function setView(mode) {
    const url = new URL(window.location);
    url.searchParams.set('view', mode);
    window.location = url;
}
</script>
@endpush
