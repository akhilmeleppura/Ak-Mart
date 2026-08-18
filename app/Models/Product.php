<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToBranch;

class Product extends Model
{
    use HasFactory;
    use BelongsToBranch;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'qty' => 'integer',
            'min_stock' => 'integer',
            'max_stock' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** Check if product stock is low */
    public function isLowStock(): bool
    {
        $min = $this->min_stock ?? 5;
        return $this->qty > 0 && $this->qty <= $min;
    }

    /** Check if product is out of stock */
    public function isOutOfStock(): bool
    {
        return $this->qty <= 0;
    }

    /** Calculate deterministic recommended purchase quantity */
    public function recommendedPurchaseQty(): int
    {
        $max = $this->max_stock ?? 50;
        $diff = $max - $this->qty;
        return max(0, $diff);
    }

    public function scopeLowStock($query)
    {
        return $query->where('qty', '>', 0)->whereRaw('qty <= COALESCE(min_stock, 5)');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('qty', '<=', 0);
    }

    /**
     * Calculate comprehensive product listing quality score (0 - 100%).
     */
    public function getQualityScoreAttribute(): int
    {
        $breakdown = $this->quality_breakdown;
        return (int) round(array_sum($breakdown));
    }

    /**
     * Get detailed quality metric breakdown.
     */
    public function getQualityBreakdownAttribute(): array
    {
        $scores = [];

        // 1. Title (15 pts)
        $titleLen = strlen(trim($this->name ?? ''));
        $scores['title'] = $titleLen >= 20 ? 15 : ($titleLen >= 8 ? 10 : 3);

        // 2. Description (20 pts)
        $descLen = strlen(strip_tags($this->description ?? ''));
        $scores['description'] = $descLen >= 150 ? 20 : ($descLen >= 40 ? 12 : 3);

        // 3. Image (15 pts)
        $scores['image'] = (!empty($this->image) && !str_contains($this->image, 'placeholder')) ? 15 : 0;

        // 4. Pricing & MRP (15 pts)
        $scores['pricing'] = ($this->price > 0) ? ($this->compare_at_price > $this->price ? 15 : 12) : 0;

        // 5. SKU & Barcode (15 pts)
        $skuScore = !empty($this->sku) ? 8 : 0;
        $barcodeScore = !empty($this->barcode) ? 7 : 0;
        $scores['identifiers'] = $skuScore + $barcodeScore;

        // 6. Category & Brand (10 pts)
        $catScore = !empty($this->category_id) ? 5 : 0;
        $brandScore = !empty($this->brand) ? 5 : 0;
        $scores['classification'] = $catScore + $brandScore;

        // 7. SEO Meta (10 pts)
        $metaTitleScore = !empty($this->meta_title) ? 5 : 0;
        $metaDescScore = !empty($this->meta_description) ? 5 : 0;
        $scores['seo'] = $metaTitleScore + $metaDescScore;

        return $scores;
    }
}
