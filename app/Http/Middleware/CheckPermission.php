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

        // Step 2: Supreme / Super admin bypass
        if (
            $user->is_supreme_admin == 1 ||
            $user->is_super_admin == 1 ||
            $user->user_type === 'super_admin' ||
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('admin')))
        ) {
            return $next($request);
        }

        // Step 3: Fallback to route name if permission is not passed manually
        $permissionName = $permission ?: $request->route()->getName();

        if (!$permissionName) {
            abort(403, 'Route name or permission not defined.');
        }

        // Step 4: Check permission using Spatie / Gate
        if (!$user->can($permissionName) && (method_exists($user, 'hasPermissionTo') ? !$user->hasPermissionTo($permissionName) : true)) {
            abort(403, 'Unauthorized: Missing permission for this action.');
        }

        return $next($request);
    }
}
