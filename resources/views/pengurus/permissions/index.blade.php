@extends('layouts.app')

@section('title', 'Manajemen Permission')

@section('topbar-nav')
    <a href="{{ route('pengurus.users.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.users.*') ? 'active' : '' }}">
        User
    </a>
    <a href="{{ route('pengurus.roles.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.roles.*') ? 'active' : '' }}">
        Role
    </a>
    <a href="{{ route('pengurus.permissions.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.permissions.*') ? 'active' : '' }}">
        Permission
    </a>
@endsection

@section('subbar-actions')
    <button class="btn-primary" onclick="openAddModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Permission
    </button>
@endsection

@section('page-title', 'Manajemen Permission')

@section('subbar-search')
    <form method="GET" action="{{ route('pengurus.permissions.index') }}" style="display:flex; gap:8px; width:100%">
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari key, label, atau deskripsi permission..."
            autocomplete="off"
            onsearch="this.form.submit()"
            style="flex:1;"
        >
        <select name="module" onchange="this.form.submit()" style="height:32px; padding:0 10px; font-size:12px; border:1px solid var(--border-md); border-radius:6px; background:#fff; cursor:pointer;">
            <option value="">— Semua Modul —</option>
            @foreach($modules as $m)
                <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>Modul {{ $m }}</option>
            @endforeach
        </select>
    </form>
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
    <div style="padding: 24px;">

        @if(session('success'))
            <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:20px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Group permissions by Module --}}
        @php
            $groupedPermissions = $permissions->groupBy('module');
        @endphp

        @forelse($groupedPermissions as $moduleName => $items)
            <div class="sim-card" style="margin-bottom: 24px; background:#fff; border:1px solid #E5E7EB; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="padding: 14px 20px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB; display:flex; justify-between; align-items:center;">
                    <h3 style="font-size: 14px; font-weight: 700; color: #1E3A5F; margin: 0; display:flex; align-items:center; gap:8px;">
                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#1D4ED8;"></span>
                        Modul: {{ $moduleName }}
                        <span style="font-size:11px; font-weight:600; color:#6B7280; background:#E5E7EB; padding:2px 8px; border-radius:99px; margin-left:6px;">
                            {{ count($items) }} Permission
                        </span>
                    </h3>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background:#FAFAFA; border-bottom:1px solid #E5E7EB; font-size:11px; color:#4B5563; text-transform:uppercase;">
                                <th style="padding:10px 16px; text-align:left;">Key Permission</th>
                                <th style="padding:10px 16px; text-align:left;">Nama / Label</th>
                                <th style="padding:10px 16px; text-align:left;">Deskripsi</th>
                                <th style="padding:10px 16px; text-align:center;">Role Terkait</th>
                                <th style="padding:10px 16px; text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $perm)
                                <tr style="border-bottom:1px solid #F3F4F6;">
                                    <td style="padding:12px 16px;">
                                        <code style="background:#EFF6FF; color:#1D4ED8; padding:3px 8px; border-radius:6px; font-size:12px; font-weight:700; font-family:monospace;">
                                            {{ $perm->name }}
                                        </code>
                                    </td>
                                    <td style="padding:12px 16px; font-weight:700; color:#111827; font-size:13px;">
                                        {{ $perm->label }}
                                    </td>
                                    <td style="padding:12px 16px; color:#4B5563; font-size:12.5px;">
                                        {{ $perm->description ?? '—' }}
                                    </td>
                                    <td style="padding:12px 16px; text-align:center;">
                                        <span class="badge" style="background-color: #ECFDF5; color: #047857; font-weight:700; padding: 3px 8px; font-size:11px; border-radius:6px;">
                                            {{ $perm->roles_count }} Role
                                        </span>
                                    </td>
                                    <td style="padding:12px 16px; text-align:right;">
                                        <div style="display:inline-flex; gap:6px;">
                                            <button type="button" class="btn-secondary" style="padding: 4px 10px; font-size: 11px;"
                                                onclick="openEditModal({{ json_encode($perm) }})">
                                                Edit
                                            </button>
                                            <form action="{{ route('pengurus.permissions.destroy', $perm->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permission {{ $perm->name }}?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="padding: 4px 10px; font-size: 11px; background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; border-radius:6px; cursor:pointer; font-weight:600;">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div style="padding: 60px 24px; text-align: center; background: #fff; border-radius: 12px; border: 1px solid #E5E7EB;">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5" style="margin-bottom:12px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 4px;">Tidak Ada Data Permission</h3>
                <p style="font-size:13px; color:#6B7280; margin:0;">Silakan klik tombol <strong>Tambah Permission</strong> untuk membuat permission baru.</p>
            </div>
        @endforelse

    </div>

    {{-- MODAL TAMBAH / EDIT PERMISSION --}}
    <div id="permissionModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; width:100%; max-width:500px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden;">
            <div style="padding:16px 20px; background:#F9FAFB; border-bottom:1px solid #E5E7EB; display:flex; justify-content:space-between; align-items:center;">
                <h3 id="modalTitle" style="font-size:15px; font-weight:700; color:#111827; margin:0;">Tambah Permission Baru</h3>
                <button type="button" onclick="closeModal()" style="border:none; background:transparent; font-size:18px; font-weight:700; cursor:pointer; color:#6B7280;">&times;</button>
            </div>
            <form id="permissionForm" method="POST" action="{{ route('pengurus.permissions.store') }}" style="padding:20px;">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; text-transform:uppercase; margin-bottom:6px;">Modul</label>
                    <input type="text" id="perm_module" name="module" required placeholder="Contoh: Simpanan, Pinjaman, Anggota..." style="width:100%; height:38px; padding:0 12px; border:1px solid #D1D5DB; border-radius:6px; font-size:13px; outline:none;" />
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; text-transform:uppercase; margin-bottom:6px;">Key Permission (Unik)</label>
                    <input type="text" id="perm_name" name="name" required placeholder="Contoh: simpanan.view, pinjaman.approve" style="width:100%; height:38px; padding:0 12px; border:1px solid #D1D5DB; border-radius:6px; font-size:13px; font-family:monospace; outline:none;" />
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; text-transform:uppercase; margin-bottom:6px;">Nama / Label (Deskriptif)</label>
                    <input type="text" id="perm_label" name="label" required placeholder="Contoh: Lihat Data Simpanan" style="width:100%; height:38px; padding:0 12px; border:1px solid #D1D5DB; border-radius:6px; font-size:13px; outline:none;" />
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#374151; text-transform:uppercase; margin-bottom:6px;">Deskripsi Opsional</label>
                    <textarea id="perm_description" name="description" placeholder="Penjelasan singkat kegunaan permission ini..." style="width:100%; height:70px; padding:8px 12px; border:1px solid #D1D5DB; border-radius:6px; font-size:13px; outline:none; font-family:inherit; resize:vertical;"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #F3F4F6; padding-top:16px;">
                    <button type="button" onclick="closeModal()" style="height:38px; padding:0 18px; background:#F3F4F6; color:#374151; border:1px solid #D1D5DB; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">
                        Batal
                    </button>
                    <button type="submit" style="height:38px; padding:0 22px; background:#1D4ED8; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer;">
                        Simpan Permission
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Permission Baru';
        document.getElementById('permissionForm').action = "{{ route('pengurus.permissions.store') }}";
        document.getElementById('methodField').value = 'POST';
        document.getElementById('perm_module').value = '';
        document.getElementById('perm_name').value = '';
        document.getElementById('perm_label').value = '';
        document.getElementById('perm_description').value = '';
        document.getElementById('permissionModal').style.display = 'flex';
    }

    function openEditModal(perm) {
        document.getElementById('modalTitle').textContent = 'Edit Permission: ' + perm.name;
        document.getElementById('permissionForm').action = "/pengurus/permissions/" + perm.id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('perm_module').value = perm.module || '';
        document.getElementById('perm_name').value = perm.name || '';
        document.getElementById('perm_label').value = perm.label || '';
        document.getElementById('perm_description').value = perm.description || '';
        document.getElementById('permissionModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('permissionModal').style.display = 'none';
    }
    </script>
    @endpush
@endsection
