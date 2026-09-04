<?php

namespace App\Traits;

use App\Models\Branch\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch()
    {
        static::creating(function ($model) {
            $branchId = static::resolveBranchId();
            if ($branchId) {
                if (!$model->branch_id) {
                    $model->branch_id = $branchId;
                }

                // If the model also has a company_id field, resolve it from the branch
                if (Schema::hasColumn($model->getTable(), 'company_id') && !$model->company_id) {
                    $branch = \App\Models\Branch\Branch::find($branchId);
                    if ($branch) {
                        $company = $branch->companies()->first();
                        if ($company) {
                            $model->company_id = $company->id;
                        }
                    }
                }
            }
        });

        static::addGlobalScope('branch', function (Builder $builder) {
            // Bypass scope for Supreme Admin and Super Admin users
            if (auth()->check() && (
                auth()->user()->is_supreme_admin == 1 ||
                auth()->user()->is_super_admin == 1 ||
                auth()->user()->user_type === 'super_admin' ||
                (method_exists(auth()->user(), 'hasRole') && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('admin')))
            )) {
                return;
            }

            $branchId = static::resolveBranchId();
            if ($branchId) {
                $table = $builder->getModel()->getTable();
                $builder->where(function ($sub) use ($table, $branchId) {
                    $sub->where($table . '.branch_id', $branchId)
                        ->orWhereNull($table . '.branch_id');
                });
            }
        });
    }

    protected static function resolveBranchId(): ?int
    {
        // 1. Check session state (active user selection)
        if (session()->has('branch_id')) {
            return (int) session()->get('branch_id');
        }

        // 2. Check authenticated user database record
        if (auth()->check() && auth()->user()->branch_id) {
            return (int) auth()->user()->branch_id;
        }

        // 3. Check custom header (for API/Mobile app requests)
        if (request()->hasHeader('X-Branch-ID')) {
            return (int) request()->header('X-Branch-ID');
        }

        // 4. Check persistent cookie
        if (request()->hasCookie('akmart_branch_id')) {
            return (int) request()->cookie('akmart_branch_id');
        }

        return null;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
