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
