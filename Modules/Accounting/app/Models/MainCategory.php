<?php

namespace Modules\Accounting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Accounting\Database\Factories\MainCategoryFactory;

class MainCategory extends Model
{
    use HasFactory;

  protected $table = 'accounting_main_categories';

    protected $fillable = ['name', 'type'];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'main_category_id');
    }
}
