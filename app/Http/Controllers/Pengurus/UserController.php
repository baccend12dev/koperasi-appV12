<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
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

        $query = User::with('role');

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
}
