<?php

namespace Modules\Permission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next)
    {
        // Step 1: User must be authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized: Please log in.');
        }

        $user = Auth::user();

        // Step 2: Supreme / Super admin bypass check
        if (
            $user->is_supreme_admin == 1 ||
            $user->is_super_admin == 1 ||
            $user->user_type === 'super_admin' ||
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('admin')))
        ) {
            return $next($request);
        }

        // Step 3: Check role_id or roles
        if (!$user->role_id && (!method_exists($user, 'roles') || $user->roles()->count() === 0)) {
            abort(403, 'Unauthorized: No role assigned.');
        }

        // Step 4: Get the route name (example: 'users.index')
        $routeName = $request->route()->getName();

        if (!$routeName) {
            abort(403, 'Route name not defined.');
        }

        // Check if user has permission via Spatie or Gate
        if ($user->can($routeName) || (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($routeName))) {
            return $next($request);
        }

        // Step 5: Check if a permission exists for this route in DB
        $permission = DB::table('permissions')
            ->where('name', $routeName)
            ->first();

        if (!$permission) {
            abort(403, 'No permission mapping found for this route.');
        }

        // Step 6: Check if the user’s role has this permission
        $roleId = $user->role_id ?? ($user->roles()->first()?->id);
        $hasPermission = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permission->id)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'Unauthorized: Missing permission for this action.');
        }

        return $next($request);
    }
}
