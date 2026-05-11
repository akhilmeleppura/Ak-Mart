<?php

namespace Modules\Permission\Http\Middleware;

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
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next)
    {
        // Step 1: User must be authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized: Please log in.');
        }

        $user = Auth::user();

        // Step 2: Supreme admin bypass check (note: is_supre_admin)
        if ($user->is_supre_admin == 1) {
            return $next($request);
        }

        // Step 3: Check role_id
        if (!$user->role_id) {
            abort(403, 'Unauthorized: No role assigned.');
        }

        // Step 4: Get the route name (example: 'users.index')
        $routeName = $request->route()->getName();

        if (!$routeName) {
            abort(403, 'Route name not defined.');
        }

        // Step 5: Check if a permission exists for this route
        $permission = DB::table('permissions')
            ->where('name', $routeName)
            ->first();

        if (!$permission) {
            abort(403, 'No permission mapping found for this route.');
        }

        // Step 6: Check if the user’s role has this permission
        $hasPermission = DB::table('role_has_permissions')
            ->where('role_id', $user->role_id)
            ->where('permission_id', $permission->id)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'Unauthorized: Missing permission for this action.');
        }

        return $next($request);
    }
}
