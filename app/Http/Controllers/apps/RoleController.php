<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions', 'users')->get();
        $permissions = Permission::all();
        $users = User::all();
        return view('content.apps.app-access-roles', compact('roles', 'permissions', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $request->name]);
        return response()->json(['success' => 'Role created successfully!']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'admin' || $role->name === 'superadmin') {
            return response()->json(['error' => 'System roles cannot be deleted!'], 403);
        }
        $role->delete();
        return response()->json(['success' => 'Role deleted successfully!']);
    }

    public function getRolePermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    public function syncPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['success' => 'Permissions synchronized successfully!']);
    }

    public function syncUserRoles(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles($request->roles ?? []);

        return response()->json(['success' => 'User roles updated successfully!']);
    }

    public function getUserRoles($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json([
            'roles' => $user->roles->pluck('name')
        ]);
    }
}
