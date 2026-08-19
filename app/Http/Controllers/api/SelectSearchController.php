<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Branch\Branch;
use App\Models\Category;
use App\Models\Customers\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

/**
 * SelectSearchController
 *
 * Provides server-side AJAX search endpoints for searchable dropdowns.
 * All endpoints:
 *  - Enforce minimum character search (or return first page on empty)
 *  - Paginate results (15 per page)
 *  - Return Select2-compatible JSON: {results: [{id, text, ...}], pagination: {more: bool}}
 *  - Use authoritative database schemas
 */
class SelectSearchController extends Controller
{
    private const PER_PAGE   = 15;
    private const MIN_LENGTH = 2;

    // -----------------------------------------------------------------
    // Products
    // GET /api/select/products?q=&page=
    // -----------------------------------------------------------------

    public function products(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        if (strlen($q) < self::MIN_LENGTH && strlen($q) > 0) {
            return $this->tooShort();
        }

        $query = Product::query()
            ->where(function ($query) {
                $query->where('is_active', true)->orWhereNull('is_active');
            })
            ->when(strlen($q) >= self::MIN_LENGTH, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'LIKE', "%{$q}%")
                        ->orWhere('sku', 'LIKE', "%{$q}%")
                        ->orWhere('barcode', 'LIKE', "%{$q}%");
                });
            })
            ->select('id', 'name', 'sku', 'price', 'qty', 'brand');

        // Apply branch scope if user has an active branch in session
        if ($branchId = session('current_branch_id')) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $paginator = $query->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($p) => [
                'id'    => $p->id,
                'text'  => $p->name . ($p->sku ? " ({$p->sku})" : ''),
                'sku'   => $p->sku,
                'price' => $p->price ? number_format((float)$p->price, 2) : '0.00',
                'stock' => $p->qty ?? 0,
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Customers
    // GET /api/select/customers?q=&page=
    // -----------------------------------------------------------------

    public function customers(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        if (strlen($q) < self::MIN_LENGTH && strlen($q) > 0) {
            return $this->tooShort();
        }

        $customerModel = $this->resolveCustomerModel();
        if (! $customerModel) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $query = $customerModel::query()
            ->when(strlen($q) >= self::MIN_LENGTH, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'LIKE', "%{$q}%")
                        ->orWhere('email', 'LIKE', "%{$q}%")
                        ->orWhere('phone', 'LIKE', "%{$q}%");
                });
            });

        $paginator = $query->paginate(self::PER_PAGE, ['id', 'name', 'email', 'phone'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($c) => [
                'id'    => $c->id,
                'text'  => $c->name . ($c->phone ? " ({$c->phone})" : ''),
                'email' => $c->email ?? '',
                'phone' => $c->phone ?? '',
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Branches
    // GET /api/select/branches?q=&page=
    // -----------------------------------------------------------------

    public function branches(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        $user = Auth::user();

        $query = Branch::query()
            ->when(strlen($q) >= 1, fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('code', 'LIKE', "%{$q}%");
            }));

        // Restrict to user's authorized branch IDs unless super-admin
        if ($user && method_exists($user, 'hasRole') && ! $user->hasRole(['super-admin', 'Super Admin', 'admin', 'Admin'])) {
            if (method_exists($user, 'branches')) {
                $authorizedIds = $user->branches()->pluck('branches.id')->toArray();
                if (!empty($authorizedIds)) {
                    $query->whereIn('id', $authorizedIds);
                }
            }
        }

        $paginator = $query->paginate(self::PER_PAGE, ['id', 'name', 'code', 'address'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($b) => [
                'id'   => $b->id,
                'text' => $b->name . ($b->code ? " [{$b->code}]" : ''),
                'code' => $b->code ?? '',
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Suppliers
    // GET /api/select/suppliers?q=&page=
    // -----------------------------------------------------------------

    public function suppliers(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        if (strlen($q) < self::MIN_LENGTH && strlen($q) > 0) {
            return $this->tooShort();
        }

        $paginator = Supplier::query()
            ->when(strlen($q) >= self::MIN_LENGTH, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'LIKE', "%{$q}%")
                        ->orWhere('company_name', 'LIKE', "%{$q}%")
                        ->orWhere('email', 'LIKE', "%{$q}%")
                        ->orWhere('phone', 'LIKE', "%{$q}%");
                });
            })
            ->paginate(self::PER_PAGE, ['id', 'name', 'company_name', 'email', 'phone'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($s) => [
                'id'      => $s->id,
                'text'    => $s->name . ($s->company_name ? " — {$s->company_name}" : ''),
                'email'   => $s->email ?? '',
                'phone'   => $s->phone ?? '',
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Categories
    // GET /api/select/categories?q=&page=
    // -----------------------------------------------------------------

    public function categories(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        $paginator = Category::query()
            ->when(strlen($q) >= 1, fn($query) => $query->where('name', 'LIKE', "%{$q}%"))
            ->orderBy('name')
            ->paginate(self::PER_PAGE, ['id', 'name', 'parent_id'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($c) => [
                'id'   => $c->id,
                'text' => $c->name,
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Users (Staff)
    // GET /api/select/users?q=&page=
    // -----------------------------------------------------------------

    public function users(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        if (strlen($q) < self::MIN_LENGTH && strlen($q) > 0) {
            return $this->tooShort();
        }

        $paginator = User::query()
            ->when(strlen($q) >= self::MIN_LENGTH, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'LIKE', "%{$q}%")
                        ->orWhere('email', 'LIKE', "%{$q}%");
                });
            })
            ->paginate(self::PER_PAGE, ['id', 'name', 'email'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($u) => [
                'id'    => $u->id,
                'text'  => $u->name . " ({$u->email})",
                'email' => $u->email,
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Roles
    // GET /api/select/roles?q=&page=
    // -----------------------------------------------------------------

    public function roles(Request $request): JsonResponse
    {
        $q    = $this->getQuery($request);
        $page = max(1, (int) $request->input('page', 1));

        $roleClass = class_exists(\Spatie\Permission\Models\Role::class)
            ? \Spatie\Permission\Models\Role::class
            : \App\Models\Role::class;

        $paginator = $roleClass::query()
            ->when(strlen($q) >= 1, fn($query) => $query->where('name', 'LIKE', "%{$q}%"))
            ->paginate(self::PER_PAGE, ['id', 'name'], 'page', $page);

        return response()->json([
            'results'    => $paginator->map(fn($r) => [
                'id'   => $r->id,
                'text' => ucfirst($r->name),
            ]),
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function getQuery(Request $request): string
    {
        return trim((string) $request->input('q', ''));
    }

    private function tooShort(): JsonResponse
    {
        return response()->json([
            'results'    => [],
            'pagination' => ['more' => false],
            'hint'       => __('Type at least :n characters to search.', ['n' => self::MIN_LENGTH]),
        ]);
    }

    private function resolveCustomerModel(): ?string
    {
        $candidates = [
            \App\Models\Customers\Customer::class,
            \App\Models\Customer::class,
            \App\Models\User::class,
        ];

        foreach ($candidates as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return null;
    }
}
