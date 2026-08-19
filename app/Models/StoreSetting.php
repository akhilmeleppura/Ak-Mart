<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use \App\Traits\BelongsToBranch;

    protected $fillable = ['branch_id', 'key', 'value'];

    /** Get a single setting value with optional default */
    public static function get(string $key, $default = null, ?int $branchId = null)
    {
        $bId = $branchId ?: (static::resolveBranchId() ?: 1);
        $row = static::where('branch_id', $bId)->where('key', $key)->first();
        if (!$row && $bId !== 1) {
            $row = static::where('branch_id', 1)->where('key', $key)->first();
        }
        return $row ? $row->value : $default;
    }

    /** Upsert a setting */
    public static function set(string $key, $value, ?int $branchId = null): void
    {
        $bId = $branchId ?: (static::resolveBranchId() ?: 1);
        static::updateOrCreate(['branch_id' => $bId, 'key' => $key], ['value' => $value]);
    }

    /** Return all settings as key=>value array */
    public static function allAsArray(?int $branchId = null): array
    {
        $bId = $branchId ?: (static::resolveBranchId() ?: 1);
        return static::where('branch_id', $bId)->pluck('value', 'key')->toArray();
    }
}
