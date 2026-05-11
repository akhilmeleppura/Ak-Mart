<?php

namespace Modules\Permission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display all roles and grouped permissions.
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        $groupedPermissions = $permissions->groupBy(function ($item) {
            return $item->module ?? 'General';
        });

        return view('permission::permissions.permission', compact('roles', 'groupedPermissions'));
    }

    /**
     * Show the form to assign permissions to a role.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy('module');

        return view('permission::permissions.create', compact('roles', 'permissions'));
    }

    /**
     * Assign permissions to a role.
     */
   public function store(Request $request)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    $role = Role::findById($request->role_id);

    // Load Permission models from IDs
    $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();

    // Sync permissions
    $role->syncPermissions($permissions);

    return redirect()->route('permissions.index')->with('success', 'Permissions assigned successfully.');
}


    /**
     * Show permissions of a specific role (read-only).
     */
    public function show($id)
    {
        $role = Role::findById($id);
        $permissions = $role->permissions->groupBy('module');

        return view('permission::permissions.show', compact('role', 'permissions'));
    }

    /**
     * Show the form to edit a role's assigned permissions.
     */
    public function edit($id)
    {
        $role = Role::findById($id);
        $permissions = Permission::all()->groupBy('module');
        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        return view('permission::permissions.edit', compact('role', 'permissions', 'assignedPermissions'));
    }

    /**
     * Update permissions for a specific role.
     */
   public function update(Request $request, $id)
{
    $request->validate([
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    $role = Role::where('id', $id)->where('guard_name', 'web')->firstOrFail();

    // Convert IDs to permission models
    $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();

    $role->syncPermissions($permissions);

    return redirect()->route('permissions.index')->with('success', 'Permissions updated successfully.');
}


    /**
     * Remove all permissions from the role (optional).
     */
    public function destroy($id)
    {
        $role = Role::findById($id);
        $role->syncPermissions([]);

        return redirect()->route('permissions.index')->with('success', 'All permissions removed from role.');
    }
}
