<?php

namespace App\Http\Middleware;

use App\Models\Branch\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchSessionMiddleware
{
    /**
     * Handle an incoming request to ensure multi-layer branch resolution & persistence.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $branchId = null;

        // If user is restricted to a specific branch, enforce strict assignment
        if ($user && !empty($user->branch_id) && method_exists($user, 'canSwitchBranch') && !$user->canSwitchBranch()) {
            $branchId = (int) $user->branch_id;
        } else {
            // For Super Admins & Multi-branch users, resolve with multi-layer fallback:
            // 1. Session state (Active user switch)
            if ($request->session()->has('branch_id')) {
                $branchId = (int) $request->session()->get('branch_id');
            }

            // 2. Authenticated user database persistence
            if (!$branchId && $user && !empty($user->branch_id)) {
                $branchId = (int) $user->branch_id;
            }

            // 3. Persistent Cookie
            if (!$branchId && $request->hasCookie('akmart_branch_id')) {
                $branchId = (int) $request->cookie('akmart_branch_id');
            }

            // 4. Fallback to default branch
            if (!$branchId) {
                try {
                    $firstBranch = Branch::query()->value('id');
                    $branchId = $firstBranch ? (int) $firstBranch : 1;
                } catch (\Throwable $e) {
                    $branchId = 1;
                }
            }
        }

        // Keep session synchronized
        if ($request->session()->get('branch_id') !== $branchId) {
            $request->session()->put('branch_id', $branchId);
        }

        // Ensure user DB record has a branch assigned if missing
        if ($request->user() && empty($request->user()->branch_id)) {
            try {
                if ($branchId && Branch::where('id', $branchId)->exists()) {
                    $request->user()->branch_id = $branchId;
                    $request->user()->saveQuietly();
                }
            } catch (\Throwable $e) {}
        }

        $response = $next($request);

        // Ensure persistent cookie reflects active branch
        if ($branchId && method_exists($response, 'withCookie')) {
            $response->withCookie(cookie()->forever('akmart_branch_id', $branchId));
        }

        return $response;
    }
}
