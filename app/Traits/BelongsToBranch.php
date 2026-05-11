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
            // Bypass scope for Super Admin users
            if (auth()->check() && auth()->user()->user_type === 'super_admin') {
                return;
            }

            $branchId = static::resolveBranchId();
            if ($branchId) {
                $builder->where($builder->getModel()->getTable() . '.branch_id', $branchId);
            }
        });
    }

    protected static function resolveBranchId(): ?int
    {
        // 1. Check authenticated user (most secure)
        if (auth()->check() && auth()->user()->branch_id) {
            return auth()->user()->branch_id;
        }

        // 2. Check session (for web panel state)
        if (session()->has('branch_id')) {
            return session()->get('branch_id');
        }

        // 3. Check custom header (for API/Mobile app requests)
        if (request()->hasHeader('X-Branch-ID')) {
            return (int) request()->header('X-Branch-ID');
        }

        return null;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
