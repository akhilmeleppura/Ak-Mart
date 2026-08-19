<?php

namespace App\Services;

use App\Models\Branch\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\TenantSubscription;
use App\Models\User;

class PlanLimitService
{
    /**
     * Get usage metrics and limits for the given branch.
     */
    public static function getUsageForBranch(?int $branchId): array
    {
        $subscription = null;
        if ($branchId) {
            $subscription = TenantSubscription::with('plan')->where('branch_id', $branchId)->first();
        }

        $plan = $subscription?->plan;
        $features = $plan?->features ?? [
            'products_limit' => 100,
            'staff_accounts' => 2,
            'custom_domain' => false,
        ];

        // Real counts from database
        $productsQuery = Product::query();
        $ordersQuery = Order::query();
        $staffQuery = User::query();

        if ($branchId) {
            $productsQuery->where('branch_id', $branchId);
            $ordersQuery->where('branch_id', $branchId);
            $staffQuery->where('branch_id', $branchId);
        }

        $productsCount = $productsQuery->count();
        $ordersCount = $ordersQuery->count();
        $staffCount = $staffQuery->count();
        $branchesCount = Branch::count();

        $productsLimit = $features['products_limit'] ?? 100;
        $staffLimit = $features['staff_accounts'] ?? 2;
        $branchLimit = $features['branch_limit'] ?? 5;

        return [
            'subscription' => $subscription,
            'plan' => $plan,
            'features' => $features,
            'products' => [
                'count' => $productsCount,
                'limit' => $productsLimit,
                'is_unlimited' => $productsLimit === -1 || $productsLimit === '-1',
                'percentage' => ($productsLimit > 0) ? min(100, round(($productsCount / $productsLimit) * 100)) : 0,
            ],
            'orders' => [
                'count' => $ordersCount,
            ],
            'staff' => [
                'count' => $staffCount,
                'limit' => $staffLimit,
                'is_unlimited' => $staffLimit === -1 || $staffLimit === '-1',
                'percentage' => ($staffLimit > 0) ? min(100, round(($staffCount / $staffLimit) * 100)) : 0,
            ],
            'branches' => [
                'count' => $branchesCount,
                'limit' => $branchLimit,
            ],
        ];
    }

    /**
     * Check if a specific limit is reached.
     */
    public static function canAddProduct(?int $branchId): bool
    {
        $usage = self::getUsageForBranch($branchId);
        if ($usage['products']['is_unlimited']) {
            return true;
        }
        return $usage['products']['count'] < $usage['products']['limit'];
    }

    /**
     * Check if a staff account can be added.
     */
    public static function canAddStaff(?int $branchId): bool
    {
        $usage = self::getUsageForBranch($branchId);
        if ($usage['staff']['is_unlimited']) {
            return true;
        }
        return $usage['staff']['count'] < $usage['staff']['limit'];
    }
}
