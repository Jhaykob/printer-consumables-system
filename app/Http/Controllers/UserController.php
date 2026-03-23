<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'confirmed', Password::defaults()],
    //         'permissions' => ['nullable', 'array']
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     // Save assigned permissions to the database pivot table
    //     if ($request->has('permissions')) {
    //         $permissionIds = DB::table('permissions')->whereIn('name', $request->permissions)->pluck('id');
    //         $user->permissions()->sync($permissionIds);
    //     }

    //     return redirect()->route('users.index')->with('success', 'User created successfully.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'permissions' => ['nullable', 'array']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        $this->syncUserPermissions($user, $request->permissions);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }


    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // public function update(Request $request, User $user)
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
    //         'password' => ['nullable', 'confirmed', Password::defaults()],
    //         'permissions' => ['nullable', 'array']
    //     ]);

    //     $user->name = $request->name;
    //     $user->email = $request->email;

    //     // Only update password if they typed a new one
    //     if ($request->filled('password')) {
    //         $user->password = Hash::make($request->password);
    //     }

    //     $user->save();

    //     // Sync updated permissions to the database pivot table
    //     if ($request->has('permissions')) {
    //         $permissionIds = DB::table('permissions')->whereIn('name', $request->permissions)->pluck('id');
    //         $user->permissions()->sync($permissionIds);
    //     } else {
    //         // If the admin unchecked all boxes, completely clear their access
    //         $user->permissions()->detach();
    //     }

    //     return redirect()->route('users.index')->with('success', 'User updated successfully.');
    // }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'permissions' => ['nullable', 'array']
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        $this->syncUserPermissions($user, $request->permissions);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Helper method to safely sync permissions and auto-create missing ones in the DB.
     */
    private function syncUserPermissions(User $user, ?array $permissions)
    {
        if (!$permissions || empty($permissions)) {
            $user->permissions()->detach();
            return;
        }

        $permissionIds = [];
        foreach ($permissions as $permName) {
            // Check if the permission exists in the database
            $perm = \Illuminate\Support\Facades\DB::table('permissions')->where('name', $permName)->first();

            if ($perm) {
                $permissionIds[] = $perm->id;
            } else {
                // Generate a clean display name (e.g. 'manage-requests' becomes 'Manage Requests')
                $displayName = ucwords(str_replace('-', ' ', $permName));

                // Auto-create it with both required columns
                \Illuminate\Support\Facades\DB::table('permissions')->insert([
                    'name' => $permName,
                    'display_name' => $displayName
                ]);

                $newPerm = \Illuminate\Support\Facades\DB::table('permissions')->where('name', $permName)->first();
                $permissionIds[] = $newPerm->id;
            }
        }

        $user->permissions()->sync($permissionIds);
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->delete(); // Soft deletes the user

        return back()->with('success', 'User deactivated successfully.');
    }
}
