<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch\Branch;
use App\Traits\BelongsToBranch;

class Expense extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'expense_category_id',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'reference_no',
        'notes',
        'attachment',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
