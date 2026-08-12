<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');

        $query = Role::withCount(['users', 'permissions']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('pengurus.roles.index', compact('roles', 'search'));
    }

    public function permissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $allPermissions = Permission::orderBy('module', 'asc')->orderBy('name', 'asc')->get()->groupBy('module');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('pengurus.roles.permissions', compact('role', 'allPermissions', 'rolePermissionIds'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissionIds = $request->input('permissions', []);

        $role->permissions()->sync($permissionIds);

        return redirect()->route('pengurus.roles.index')
            ->with('success', 'Hak akses (permission) untuk Role "' . $role->name . '" berhasil diperbarui.');
    }
}
