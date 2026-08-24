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
        $user = $request->user();

        // Check if user has permission to access this branch
        if ($user && method_exists($user, 'canAccessBranch') && !$user->canAccessBranch($branch->id)) {
            return redirect()->back()->with('error', __('You do not have permission to access this branch.'));
        }

        // 1. Session Storage
        $request->session()->put('branch_id', $branch->id);
        $request->session()->save();

        // 2. Persist to Database for Authenticated User
        if ($user) {
            $user->branch_id = $branch->id;
            $user->save();
        }

        // 3. Return with long-lived cookie & redirect
        return redirect()->back()
            ->with('success', __('Switched to branch: :name', ['name' => $branch->name]))
            ->withCookie(cookie()->forever('akmart_branch_id', $branch->id));
    }
}
