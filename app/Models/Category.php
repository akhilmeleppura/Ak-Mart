<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \App\Traits\BelongsToBranch;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubcategories($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubcategory(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get all category IDs including self and all recursive children.
     */
    public function getAllCategoryIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllCategoryIds());
        }
        return array_unique($ids);
    }

    /**
     * Get aggregate products count across self and all subcategories
     */
    public function getTotalProductsCountAttribute(): int
    {
        $ids = $this->getAllCategoryIds();
        return \App\Models\Product::whereIn('category_id', $ids)->where('is_active', true)->count();
    }

    /**
     * Hierarchical display name (e.g., "Beverages > Fruit Juices")
     */
    public function getHierarchyNameAttribute(): string
    {
        return $this->parent ? "{$this->parent->name} > {$this->name}" : $this->name;
    }
}
