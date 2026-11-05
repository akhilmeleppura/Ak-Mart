<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use \App\Traits\BelongsToBranch;

    protected $fillable = ['branch_id', 'key', 'value'];

    /** Resolve a valid branch ID, creating the default branch if necessary */
    protected static function resolveEffectiveBranchId(?int $branchId = null): int
    {
        if ($branchId && \App\Models\Branch\Branch::where('id', $branchId)->exists()) {
            return $branchId;
        }

        $resolved = static::resolveBranchId();
        if ($resolved && \App\Models\Branch\Branch::where('id', $resolved)->exists()) {
            return $resolved;
        }

        $firstBranch = \App\Models\Branch\Branch::first();
        if ($firstBranch) {
            return $firstBranch->id;
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('branches')) {
            $created = \App\Models\Branch\Branch::firstOrCreate(['id' => 1], [
                'name' => 'Main Flagship Branch',
                'code' => 'MAIN',
            ]);
            return $created->id;
        }

        return 1;
    }

    /** Get a single setting value with optional default */
    public static function get(string $key, $default = null, ?int $branchId = null)
    {
        $bId = static::resolveEffectiveBranchId($branchId);
        $row = static::where('branch_id', $bId)->where('key', $key)->first();
        if (!$row && $bId !== 1) {
            $row = static::where('branch_id', 1)->where('key', $key)->first();
        }
        return $row ? $row->value : $default;
    }

    /** Upsert a setting */
    public static function set(string $key, $value, ?int $branchId = null): void
    {
        $bId = static::resolveEffectiveBranchId($branchId);
        static::updateOrCreate(['branch_id' => $bId, 'key' => $key], ['value' => $value]);
    }

    /** Return all settings as key=>value array */
    public static function allAsArray(?int $branchId = null): array
    {
        $bId = static::resolveEffectiveBranchId($branchId);
        return static::where('branch_id', $bId)->pluck('value', 'key')->toArray();
    }
}
