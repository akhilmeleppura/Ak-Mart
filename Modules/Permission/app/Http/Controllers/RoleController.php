<?php

namespace Modules\Permission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $roles = Role::all();
        //  dd($roles);

        return view('permission::roles.role', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permission::create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'status' => 'required|in:0,1',
        ]);

        Role::create($request->only('name', 'status'));

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('permission::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('permission::roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'status' => 'required|in:0,1',
        ]);

        $role->update($request->only('name', 'status'));

        return redirect()->route('role.view')->with('success', 'Role updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
public function destroy(Role $role)
{
    // Check if any user has this role_id
    $isAssigned = \App\Models\User::where('role_id', $role->id)->exists();

    if ($isAssigned) {
        return redirect()->back()->with('error', 'Role cannot be deleted because it is assigned to one or more users.');
    }

    $role->delete();

    return redirect()->back()->with('success', 'Role deleted successfully.');
}


}
