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
        if ($user && (
            $user->is_supreme_admin == 1 ||
            $user->is_super_admin == 1 ||
            $user->user_type === 'super_admin' ||
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('admin')))
        )) {
            return $next($request);
        }

        $targetBranchId = $request->route('id') ?? session('branch_id');

        // If user is restricted to a specific branch
        if ($user && isset($user->branch_id) && $user->branch_id !== null) {
            
            // If they are trying to switch or access another branch
            if ($targetBranchId && $user->branch_id != $targetBranchId) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Unauthorized branch access.'], 403);
                }
                abort(403, 'You do not have access to this branch.');
            }
        }

        return $next($request);
    }
}
