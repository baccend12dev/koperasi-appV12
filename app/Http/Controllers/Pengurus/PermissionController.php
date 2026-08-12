<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $module = $request->input('module');

        $query = Permission::withCount(['roles', 'users']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($module) {
            $query->where('module', $module);
        }

        $permissions = $query->orderBy('module', 'asc')->orderBy('name', 'asc')->get();
        $modules = Permission::select('module')->distinct()->pluck('module');

        return view('pengurus.permissions.index', compact('permissions', 'modules', 'search', 'module'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:permissions,name',
            'label'       => 'required|string|max:255',
            'module'      => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        Permission::create($validated);

        return redirect()->route('pengurus.permissions.index')
            ->with('success', 'Permission baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'label'       => 'required|string|max:255',
            'module'      => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $permission->update($validated);

        return redirect()->route('pengurus.permissions.index')
            ->with('success', 'Data permission berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('pengurus.permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
