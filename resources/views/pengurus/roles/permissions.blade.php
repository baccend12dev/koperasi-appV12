@extends('layouts.app')

@section('title', 'Hak Akses Role: ' . $role->name)

@section('topbar-nav')
    <a href="{{ route('pengurus.users.index') }}" class="tb-link">User</a>
    <a href="{{ route('pengurus.roles.index') }}" class="tb-link active">Role</a>
    <a href="{{ route('pengurus.permissions.index') }}" class="tb-link">Permission</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('pengurus.roles.index') }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;margin-right:4px;">
            <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke Daftar Role
    </a>
@endsection

@section('page-title', 'Pengaturan Hak Akses Role: ' . $role->name)

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

        <a href="{{ route('pengurus.users.index') }}" class="sd-link">User</a>
        <a href="{{ route('pengurus.roles.index') }}" class="sd-link active">Role</a>
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

        {{-- Role Header Info Card --}}
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px 24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <div style="font-size:11px; font-weight:800; color:#4B5563; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Role Pengguna</div>
                <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0 0 4px;">{{ $role->name }}</h2>
                <p style="font-size:13px; color:#4B5563; margin:0;">{{ $role->description ?? 'Tidak ada deskripsi role.' }}</p>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="button" onclick="selectAllPermissions(true)" style="padding:8px 14px; background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;">
                    Pilih Semua
                </button>
                <button type="button" onclick="selectAllPermissions(false)" style="padding:8px 14px; background:#F3F4F6; color:#4B5563; border:1px solid #D1D5DB; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;">
                    Hapus Semua Pilihan
                </button>
            </div>
        </div>

        {{-- Form Matrix Checklist --}}
        <form method="POST" action="{{ route('pengurus.roles.permissions.update', $role->id) }}">
            @csrf

            <div style="display:flex; flex-direction:column; gap:20px;">
                @foreach($allPermissions as $moduleName => $permissions)
                    <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                        <div style="padding:14px 20px; background:#F9FAFB; border-bottom:1px solid #E5E7EB; display:flex; justify-content:space-between; align-items:center;">
                            <h3 style="font-size:14px; font-weight:700; color:#1E3A5F; margin:0; display:flex; align-items:center; gap:8px;">
                                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#107C41;"></span>
                                Modul {{ $moduleName }}
                            </h3>
                            <label style="font-size:12px; font-weight:700; color:#1D4ED8; cursor:pointer; user-select:none;">
                                <input type="checkbox" onchange="toggleModulePermissions(this, 'module-{{ Str::slug($moduleName) }}')" style="margin-right:4px; accent-color:#1D4ED8;" />
                                Pilih Modul Ini
                            </label>
                        </div>
                        <div class="module-{{ Str::slug($moduleName) }}" style="padding:16px 20px; display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:12px;">
                            @foreach($permissions as $perm)
                                @php
                                    $isChecked = in_array($perm->id, $rolePermissionIds);
                                @endphp
                                <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #E5E7EB; border-radius:8px; background:{{ $isChecked ? '#F0FDF4' : '#ffffff' }}; border-color:{{ $isChecked ? '#86EFAC' : '#E5E7EB' }}; cursor:pointer; transition:all 0.15s ease;">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="perm-checkbox" {{ $isChecked ? 'checked' : '' }} onchange="onPermCheckChange(this)" style="margin-top:2px; width:16px; height:16px; accent-color:#107C41; cursor:pointer;" />
                                    <div style="flex:1;">
                                        <div style="font-size:13px; font-weight:700; color:#111827;">{{ $perm->label }}</div>
                                        <div style="font-size:11px; font-family:monospace; color:#1D4ED8; font-weight:600; margin:2px 0;">{{ $perm->name }}</div>
                                        @if($perm->description)
                                            <div style="font-size:11.5px; color:#4B5563; margin-top:2px;">{{ $perm->description }}</div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="position:sticky; bottom:20px; margin-top:24px; background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:16px 24px; box-shadow:0 4px 15px rgba(0,0,0,0.1); display:flex; justify-content:space-between; align-items:center; z-index:99;">
                <span style="font-size:13px; color:#4B5563; font-weight:600;">Pastikan perubahan hak akses sudah sesuai sebelum disimpan.</span>
                <button type="submit" class="btn-primary" style="height:42px; padding:0 28px; font-size:14px; font-weight:700;">
                    Simpan Hak Akses Role
                </button>
            </div>
        </form>

    </div>

    @push('scripts')
    <script>
    function selectAllPermissions(status) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = status;
            onPermCheckChange(cb);
        });
    }

    function toggleModulePermissions(masterCb, moduleClass) {
        document.querySelectorAll('.' + moduleClass + ' .perm-checkbox').forEach(cb => {
            cb.checked = masterCb.checked;
            onPermCheckChange(cb);
        });
    }

    function onPermCheckChange(cb) {
        const labelBox = cb.closest('label');
        if (labelBox) {
            if (cb.checked) {
                labelBox.style.background = '#F0FDF4';
                labelBox.style.borderColor = '#86EFAC';
            } else {
                labelBox.style.background = '#ffffff';
                labelBox.style.borderColor = '#E5E7EB';
            }
        }
    }
    </script>
    @endpush
@endsection
