<?php

namespace Modules\General\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\General\Database\Factories\BranchFactory;

class Branch extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'address',
        'logo',
    ];

    /**
     * Relationship: A branch can have many companies
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'branch_id');
    }
}
