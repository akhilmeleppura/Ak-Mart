<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AccountBaseController extends Controller
{
    use AuthorizesRequests;

    protected $permissions = [];

    // public function __construct()
    // {
    //     $this->middleware(['auth', function ($request, $next) {
    //         $this->checkPermissions();
    //         return $next($request);
    //     }]);
    // }

    protected function checkPermissions()
    {
        if (auth()->check() && !empty($this->permissions)) {
            foreach ($this->permissions as $permission) {
                if (!auth()->user()->hasPermissionTo($permission)) {
                    throw new UnauthorizedException(403, "User does not have the required permission: {$permission}");
                }
            }
        }
    }
}
