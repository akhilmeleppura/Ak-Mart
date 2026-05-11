<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Branch\Branch;
use Illuminate\Http\Request;

class EcommerceBranchManagement extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('content.apps.app-ecommerce-branch-list', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        Branch::create($request->all());

        return redirect()->back()->with('success', 'Branch added successfully!');
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return response()->json($branch);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update($request->all());

        return response()->json(['message' => 'Branch updated successfully!']);
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        
        // Prevent deleting the currently active branch in session
        if (session('branch_id') == $id) {
            return response()->json(['message' => 'Cannot delete the currently active branch.'], 400);
        }

        $branch->delete();
        return response()->json(['message' => 'Branch deleted successfully!']);
    }
}
