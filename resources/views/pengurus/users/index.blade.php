@extends('layouts.app')

@section('title', 'Manajemen User')

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
    <button class="btn-primary" onclick="alert('Fitur Tambah User akan diimplementasikan pada tahap berikutnya.')">
        Tambah User
    </button>
@endsection

@section('page-title', 'Manajemen User')

@section('subbar-search')
    <form method="GET" action="{{ route('pengurus.users.index') }}" style="display:flex; width:100%">
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari NIK, nama, atau email..."
            autocomplete="off"
            onsearch="this.form.submit()"
        >
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif
    </form>
@endsection

@section('subbar-pagination')
    <span class="pag-info">
        {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} / {{ $users->total() }}
    </span>
    <a href="{{ $users->previousPageUrl() ?? '#' }}"
       class="pag-btn" {{ $users->onFirstPage() ? 'style=opacity:.4;pointer-events:none' : '' }}>
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none">
            <path d="M6 1L1 6l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <a href="{{ $users->nextPageUrl() ?? '#' }}"
       class="pag-btn" {{ !$users->hasMorePages() ? 'style=opacity:.4;pointer-events:none' : '' }}>
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
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nik', 'direction' => (request('sort') === 'nik' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" style="display:inline-flex; align-items:center; gap:4px">
                            NIK
                            @if(request('sort') === 'nik')
                                @if(request('direction') === 'asc')
                                    ▲
                                @else
                                    ▼
                                @endif
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => (request('sort') === 'name' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" style="display:inline-flex; align-items:center; gap:4px">
                            Nama
                            @if(request('sort') === 'name')
                                @if(request('direction') === 'asc')
                                    ▲
                                @else
                                    ▼
                                @endif
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => (request('sort') === 'email' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" style="display:inline-flex; align-items:center; gap:4px">
                            Email
                            @if(request('sort') === 'email')
                                @if(request('direction') === 'asc')
                                    ▲
                                @else
                                    ▼
                                @endif
                            @endif
                        </a>
                    </th>
                    <th>Role</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => (request('sort') === 'status' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" style="display:inline-flex; align-items:center; gap:4px">
                            Status
                            @if(request('sort') === 'status')
                                @if(request('direction') === 'asc')
                                    ▲
                                @else
                                    ▼
                                @endif
                            @endif
                        </a>
                    </th>
                    <th>Last Login</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => (request('sort') === 'created_at' && request('direction') === 'asc') ? 'desc' : 'asc']) }}" style="display:inline-flex; align-items:center; gap:4px">
                            Created At
                            @if(request('sort') === 'created_at')
                                @if(request('direction') === 'asc')
                                    ▲
                                @else
                                    ▼
                                @endif
                            @endif
                        </a>
                    </th>
                    <th style="text-align: right; padding-right: 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <span style="font-weight: 600; color: var(--accent);">{{ $user->nik ?? '—' }}</span>
                        </td>
                        <td>
                            <div style="font-weight:500">{{ $user->name }}</div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role)
                                <span class="badge" style="background-color: var(--accent-light); color: var(--accent-text); font-weight:600;">
                                    {{ $user->role->name }}
                                </span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <span class="td-muted" style="font-size:12px">
                                {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Belum pernah' }}
                            </span>
                        </td>
                        <td>
                            <span class="td-muted" style="font-size:12px">
                                {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '—' }}
                            </span>
                        </td>
                        <td style="text-align: right; padding-right: 16px;" onclick="event.stopPropagation()">
                            <div style="display:inline-flex; gap:6px;">
                                <button class="btn-secondary" style="padding: 3px 8px; font-size: 11px;" onclick="alert('Detail User {{ $user->name }} akan diimplementasikan pada tahap berikutnya.')">
                                    Detail
                                </button>
                                <button class="btn-secondary" style="padding: 3px 8px; font-size: 11px;" onclick="alert('Edit User {{ $user->name }} akan diimplementasikan pada tahap berikutnya.')">
                                    Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px 16px;color:var(--text-3)">
                            Tidak ada data user ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
