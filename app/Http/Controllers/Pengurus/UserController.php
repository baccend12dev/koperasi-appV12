<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $sortBy = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');

        // Whitelist sorting columns to prevent SQL injection
        $allowedSorts = ['nik', 'name', 'email', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $allowedDirections = ['asc', 'desc'];
        if (!in_array($sortDir, $allowedDirections)) {
            $sortDir = 'desc';
        }

        $query = User::with(['role', 'permissions']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();

        return view('pengurus.users.index', compact('users', 'search', 'sortBy', 'sortDir'));
    }

    public function permissions($id)
    {
        $user = User::with(['role.permissions', 'permissions'])->findOrFail($id);
        $allPermissions = Permission::orderBy('module', 'asc')->orderBy('name', 'asc')->get()->groupBy('module');

        // Role permissions
        $rolePermissionIds = $user->role ? $user->role->permissions->pluck('id')->toArray() : [];

        // Direct user permissions mapping [permission_id => access_type ('grant'|'deny')]
        $userDirectPermissions = $user->permissions->pluck('pivot.access_type', 'id')->toArray();

        return view('pengurus.users.permissions', compact('user', 'allPermissions', 'rolePermissionIds', 'userDirectPermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $permissions = $request->input('user_permissions', []); // format: [perm_id => 'grant'|'deny'|'default']

        $syncData = [];
        foreach ($permissions as $permId => $type) {
            if (in_array($type, ['grant', 'deny'])) {
                $syncData[$permId] = ['access_type' => $type];
            }
        }

        $user->permissions()->sync($syncData);

        return redirect()->route('pengurus.users.index')
            ->with('success', 'Hak akses khusus untuk User "' . $user->name . '" berhasil disimpan.');
    }
}
