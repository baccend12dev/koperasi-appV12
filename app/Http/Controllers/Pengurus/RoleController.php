<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');

        $query = Role::withCount('users');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('pengurus.roles.index', compact('roles', 'search'));
    }
}
