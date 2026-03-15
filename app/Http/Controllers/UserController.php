<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Get all users except the superuser to prevent self-lockout
        $users = User::where('is_superuser', false)->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $permissions = Permission::all();
        return view('admin.users.edit', compact('user', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        // Sync checkboxes with the database pivot table
        $user->permissions()->sync($request->permissions);

        return redirect()->route('users.index')->with('status', 'Permissions updated for ' . $user->name);
    }
}
