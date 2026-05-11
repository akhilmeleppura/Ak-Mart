<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Accounting\Database\Factories\SubCategoryFactory;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'accounting_subcategories';

    protected $fillable = ['name', 'description', 'main_category_id'];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }
}
