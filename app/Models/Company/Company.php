<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\General\Database\Factories\CompanyFactory;
use App\Models\Branch\Branch;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'branch_id',
    ];

    /**
     * Relationship: A company belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
