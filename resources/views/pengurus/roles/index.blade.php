@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('topbar-nav')
    <a href="{{ route('pengurus.users.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.users.index') ? 'active' : '' }}">
        User
    </a>
    <a href="{{ route('pengurus.roles.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.roles.index') ? 'active' : '' }}">
        Role
    </a>
    <a href="{{ route('pengurus.permissions.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.permissions.index') ? 'active' : '' }}">
        Permission
    </a>
@endsection

@section('subbar-actions')
    <button class="btn-primary" onclick="alert('Fitur Tambah Role akan diimplementasikan pada tahap berikutnya.')">
        Tambah Role
    </button>
@endsection

@section('page-title', 'Manajemen Role')

@section('subbar-search')
    <form method="GET" action="{{ route('pengurus.roles.index') }}" style="display:flex; width:100%">
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama role..."
            autocomplete="off"
            onsearch="this.form.submit()"
        >
    </form>
@endsection

@section('subbar-pagination')
    <span class="pag-info">
        {{ $roles->firstItem() ?? 0 }}–{{ $roles->lastItem() ?? 0 }} / {{ $roles->total() }}
    </span>
    <a href="{{ $roles->previousPageUrl() ?? '#' }}"
       class="pag-btn" {{ $roles->onFirstPage() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <a href="{{ $roles->nextPageUrl() ?? '#' }}"
       class="pag-btn" {{ !$roles->hasMorePages() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M1 1l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
@endsection

@section('sidebar')
    <div class="sd-section">
        <div class="sd-heading">
            <div style="display:flex;align-items:center;gap:5px">
                <svg class="sd-heading-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                PENGURUS
            </div>
        </div>

        <a href="{{ route('pengurus.users.index') }}"
           class="sd-link {{ request()->routeIs('pengurus.users.*') ? 'active' : '' }}">
            User
        </a>
        <a href="{{ route('pengurus.roles.index') }}"
           class="sd-link {{ request()->routeIs('pengurus.roles.*') ? 'active' : '' }}">
            Role
        </a>
        <a href="{{ route('pengurus.permissions.index') }}"
           class="sd-link {{ request()->routeIs('pengurus.permissions.*') ? 'active' : '' }}">
            Permission
        </a>
    </div>
@endsection

@section('content')
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Role</th>
                    <th>Deskripsi</th>
                    <th>Jumlah User</th>
                    <th>Created At</th>
                    <th style="text-align: right; padding-right: 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>
                            <span class="badge" style="background-color: var(--accent-light); color: var(--accent-text); font-weight:700; padding: 4px 10px; font-size:12px;">
                                {{ $role->name }}
                            </span>
                        </td>
                        <td>{{ $role->description ?? '—' }}</td>
                        <td>
                            <span style="font-weight: 600;">{{ $role->users_count }}</span>
                            <span class="td-muted" style="font-size:12px"> User</span>
                        </td>
                        <td>
                            <span class="td-muted" style="font-size:12px">
                                {{ $role->created_at ? $role->created_at->format('d M Y H:i') : '—' }}
                            </span>
                        </td>
                        <td style="text-align: right; padding-right: 16px;" onclick="event.stopPropagation()">
                            <div style="display:inline-flex; gap:6px;">
                                <a href="{{ route('pengurus.roles.permissions', $role->id) }}" class="btn-secondary" style="padding: 4px 10px; font-size: 11.5px; font-weight:700; color:#1D4ED8; background:#EFF6FF; border-color:#BFDBFE; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Kelola Permission
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px 16px;color:var(--text-3)">
                            Tidak ada data role ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
