@extends('layouts.app')

@section('title', 'Hak Akses Khusus User: ' . $user->name)

@section('topbar-nav')
    <a href="{{ route('pengurus.users.index') }}" class="tb-link active">User</a>
    <a href="{{ route('pengurus.roles.index') }}" class="tb-link">Role</a>
    <a href="{{ route('pengurus.permissions.index') }}" class="tb-link">Permission</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('pengurus.users.index') }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;margin-right:4px;">
            <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke Daftar User
    </a>
@endsection

@section('page-title', 'Kustomisasi Hak Akses User: ' . $user->name)

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

        <a href="{{ route('pengurus.users.index') }}" class="sd-link active">User</a>
        <a href="{{ route('pengurus.roles.index') }}" class="sd-link">Role</a>
        <a href="{{ route('pengurus.permissions.index') }}" class="sd-link">Permission</a>
    </div>
@endsection

@section('content')
    <div style="padding: 24px; max-width: 1100px; margin: 0 auto;">

        @if(session('success'))
            <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- User Header Info Card --}}
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px 24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <div style="font-size:11px; font-weight:800; color:#4B5563; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Detail Akses Pengguna</div>
                <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0 0 6px;">{{ $user->name }}</h2>
                <div style="display:flex; align-items:center; gap:12px; font-size:12.5px; color:#4B5563;">
                    <span><strong>NIK:</strong> {{ $user->nik ?? '—' }}</span>
                    <span>•</span>
                    <span><strong>Email:</strong> {{ $user->email }}</span>
                    <span>•</span>
                    <span><strong>Role Default:</strong> <span class="badge" style="background:#EFF6FF; color:#1D4ED8; font-weight:700;">{{ $user->role ? $user->role->name : 'Tanpa Role' }}</span></span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('pengurus.users.permissions.update', $user->id) }}">
            @csrf

            <div style="display:flex; flex-direction:column; gap:20px;">
                @foreach($allPermissions as $moduleName => $permissions)
                    <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                        <div style="padding:14px 20px; background:#F9FAFB; border-bottom:1px solid #E5E7EB;">
                            <h3 style="font-size:14px; font-weight:700; color:#1E3A5F; margin:0; display:flex; align-items:center; gap:8px;">
                                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#1D4ED8;"></span>
                                Modul {{ $moduleName }}
                            </h3>
                        </div>
                        <div style="overflow-x:auto;">
                            <table class="data-table" style="width:100%; border-collapse:collapse; text-align:left;">
                                <thead>
                                    <tr style="background:#FAFAFA; border-bottom:1px solid #E5E7EB; font-size:11px; color:#4B5563; text-transform:uppercase;">
                                        <th style="padding:10px 16px; width:30%;">Permission / Modul</th>
                                        <th style="padding:10px 16px; width:20%;">Status Role ({{ $user->role ? $user->role->name : 'Default' }})</th>
                                        <th style="padding:10px 16px; width:50%;">Kustomisasi Akses Khusus User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $perm)
                                        @php
                                            $isRoleGranted = in_array($perm->id, $rolePermissionIds);
                                            $directAccess = $userDirectPermissions[$perm->id] ?? 'default';
                                        @endphp
                                        <tr style="border-bottom:1px solid #F3F4F6;">
                                            <td style="padding:12px 16px;">
                                                <div style="font-size:13px; font-weight:700; color:#111827;">{{ $perm->label }}</div>
                                                <code style="font-size:11px; color:#1D4ED8; font-family:monospace;">{{ $perm->name }}</code>
                                            </td>
                                            <td style="padding:12px 16px;">
                                                @if($isRoleGranted)
                                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; background:#ECFDF5; color:#047857; border-radius:6px; font-size:11.5px; font-weight:700;">
                                                        ✓ Diizinkan Role
                                                    </span>
                                                @else
                                                    <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; background:#F3F4F6; color:#6B7280; border-radius:6px; font-size:11.5px; font-weight:600;">
                                                        ✕ Tidak Ada di Role
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="padding:12px 16px;">
                                                <div style="display:flex; gap:16px; align-items:center;">
                                                    <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#374151; cursor:pointer;">
                                                        <input type="radio" name="user_permissions[{{ $perm->id }}]" value="default" {{ $directAccess === 'default' ? 'checked' : '' }} style="accent-color:#6B7280; cursor:pointer;" />
                                                        Ikuti Role
                                                    </label>
                                                    <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:#047857; cursor:pointer;">
                                                        <input type="radio" name="user_permissions[{{ $perm->id }}]" value="grant" {{ $directAccess === 'grant' ? 'checked' : '' }} style="accent-color:#107C41; cursor:pointer;" />
                                                        Izinkan Khusus (+ Grant)
                                                    </label>
                                                    <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:#DC2626; cursor:pointer;">
                                                        <input type="radio" name="user_permissions[{{ $perm->id }}]" value="deny" {{ $directAccess === 'deny' ? 'checked' : '' }} style="accent-color:#DC2626; cursor:pointer;" />
                                                        Blokir Khusus (- Deny)
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="position:sticky; bottom:20px; margin-top:24px; background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:16px 24px; box-shadow:0 4px 15px rgba(0,0,0,0.1); display:flex; justify-content:space-between; align-items:center; z-index:99;">
                <span style="font-size:13px; color:#4B5563; font-weight:600;">Pilih "Izinkan Khusus" atau "Blokir Khusus" untuk mengesampingkan permission role default.</span>
                <button type="submit" class="btn-primary" style="height:42px; padding:0 28px; font-size:14px; font-weight:700;">
                    Simpan Hak Akses User
                </button>
            </div>
        </form>

    </div>
@endsection
