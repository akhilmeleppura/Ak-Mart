<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch\Branch;

class BranchController extends Controller
{
    public function swap(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $request->session()->put('branch_id', $branch->id);
        return redirect()->back()->with('success', 'Switched to branch: ' . $branch->name);
    }
}
