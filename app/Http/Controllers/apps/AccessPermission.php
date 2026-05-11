<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessPermission extends Controller
{
  public function index()
  {
    return view('content.apps.app-access-permission');
  }

  public function list()
  {
    $permissions = Permission::with('roles')->get();
    
    $data = $permissions->map(function($permission) {
      return [
        'id' => $permission->id,
        'name' => $permission->name,
        'assigned_to' => $permission->roles->pluck('name')->toArray(),
        'created_date' => $permission->created_at->format('d M Y, h:i A')
      ];
    });

    return response()->json(['data' => $data]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'modalPermissionName' => 'required|string|unique:permissions,name'
    ]);

    Permission::create(['name' => $request->modalPermissionName]);

    return response()->json(['success' => 'Permission created successfully.']);
  }

  public function edit($id)
  {
    $permission = Permission::findOrFail($id);
    return response()->json($permission);
  }

  public function update(Request $request, $id)
  {
    $permission = Permission::findOrFail($id);
    
    $request->validate([
      'editPermissionName' => 'required|string|unique:permissions,name,' . $id
    ]);

    $permission->update(['name' => $request->editPermissionName]);

    return response()->json(['success' => 'Permission updated successfully.']);
  }

  public function destroy($id)
  {
    $permission = Permission::findOrFail($id);
    $permission->delete();

    return response()->json(['success' => 'Permission deleted successfully.']);
  }
}
