<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Branch\Branch;
use Illuminate\Http\Request;

class BranchManagementController extends Controller
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
            'code' => 'required|string|unique:branches,code|max:10',
            'address' => 'required|string',
        ]);

        Branch::create($request->all());

        return redirect()->back()->with('success', 'Branch added successfully!');
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:branches,code,' . $id,
            'address' => 'required|string',
        ]);

        $branch->update($request->all());

        return redirect()->back()->with('success', 'Branch updated successfully!');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        
        if (Branch::count() <= 1) {
            return response()->json(['message' => 'Cannot delete the last remaining branch.'], 403);
        }

        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully.']);
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return response()->json($branch);
    }
}
