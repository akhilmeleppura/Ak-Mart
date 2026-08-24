<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Supreme Admins & Super Admins can access and switch to any branch across the platform
        if ($user && method_exists($user, 'canSwitchBranch') && $user->canSwitchBranch()) {
            return $next($request);
        }

        $targetBranchId = $request->route('id') ?? session('branch_id');

        // If user is restricted to a specific branch
        if ($user && isset($user->branch_id) && $user->branch_id !== null) {
            // Keep session aligned with their locked branch
            if (session('branch_id') != $user->branch_id) {
                session(['branch_id' => (int) $user->branch_id]);
            }

            // If they are trying to switch or access another branch
            if ($targetBranchId && (int)$user->branch_id !== (int)$targetBranchId) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => 'Unauthorized branch access.'], 403);
                }
                return redirect()->back()->with('error', __('You do not have access to this branch.'));
            }
        }

        return $next($request);
    }
}
