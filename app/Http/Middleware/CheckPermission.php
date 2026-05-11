<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // Step 1: User must be authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized: Please log in.');
        }

        $user = Auth::user();

        // Step 2: Super admin bypass (assumes column is `is_supreme_admin`)
        if ($user->is_supreme_admin == 1) {
            return $next($request);
        }

        // Step 3: Fallback to route name if permission is not passed manually
        $permissionName = $permission ?: $request->route()->getName();

        if (!$permissionName) {
            abort(403, 'Route name or permission not defined.');
        }

        // Step 4: Check permission using Spatie
        if (!$user->hasPermissionTo($permissionName)) {
            abort(403, 'Unauthorized: Missing permission for this action.');
        }

        return $next($request);
    }
}
