<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\CmsBanner;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreBuilderController extends Controller
{
    /**
     * Hero Sliders Management
     */
    public function sliders()
    {
        $sliders = CmsBanner::where('position', 'home_hero')->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->get();
        return view('content.apps.store-management.sliders', compact('sliders', 'categories'));
    }

    /**
     * Store Hero Slider
     */
    public function storeSlider(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'badge_text'  => 'nullable|string|max:100',
            'button_text' => 'required|string|max:50',
            'link_url'    => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'bg_color'    => 'nullable|string|max:100',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $dir = public_path('uploads/sliders');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = 'slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $imagePath = 'uploads/sliders/' . $fileName;
        }

        CmsBanner::create([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'badge_text'  => $request->badge_text ?: 'Special Promotion',
            'button_text' => $request->button_text,
            'link_url'    => $request->link_url ?: '/store/shop',
            'image'       => $imagePath,
            'bg_color'    => $request->bg_color ?: 'linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #0D9488 100%)',
            'position'    => 'home_hero',
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => (int)$request->input('sort_order', 0),
        ]);

        return back()->with('success', 'Hero Slider created successfully!');
    }

    /**
     * Update Hero Slider
     */
    public function updateSlider(Request $request, $id)
    {
        $slider = CmsBanner::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'badge_text'  => 'nullable|string|max:100',
            'button_text' => 'required|string|max:50',
            'link_url'    => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'bg_color'    => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $dir = public_path('uploads/sliders');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = 'slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $slider->image = 'uploads/sliders/' . $fileName;
        }

        $slider->update([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'badge_text'  => $request->badge_text,
            'button_text' => $request->button_text,
            'link_url'    => $request->link_url,
            'bg_color'    => $request->bg_color,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => (int)$request->input('sort_order', 0),
        ]);

        return back()->with('success', 'Hero Slider updated successfully!');
    }

    /**
     * Toggle Slider Status (Active / Draft)
     */
    public function toggleSliderStatus($id)
    {
        $slider = CmsBanner::findOrFail($id);
        $slider->is_active = !$slider->is_active;
        $slider->save();

        return back()->with('success', "Slider '{$slider->title}' status updated to " . ($slider->is_active ? 'Active' : 'Draft') . '.');
    }

    /**
     * Delete Slider
     */
    public function destroySlider($id)
    {
        $slider = CmsBanner::findOrFail($id);
        $slider->delete();

        return back()->with('success', 'Hero Slider deleted.');
    }

    /**
     * Product Merchandising Control Board
     */
    public function merchandising(Request $request)
    {
        $query = Product::with('category');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        $stats = [
            'total_featured'   => Product::where('is_featured', true)->count(),
            'total_trending'   => Product::where('is_trending', true)->count(),
            'total_bestseller' => Product::where('is_best_seller', true)->count(),
            'total_deals'      => Product::where('deal_of_the_day', true)->count(),
        ];

        return view('content.apps.store-management.merchandising', compact('products', 'categories', 'stats'));
    }

    /**
     * 1-Click Merchandising Flag Toggle AJAX
     */
    public function toggleMerchandising(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $flag = $request->input('flag');

        if (!in_array($flag, ['is_featured', 'is_trending', 'is_best_seller', 'is_new_arrival', 'deal_of_the_day'])) {
            return response()->json(['success' => false, 'message' => 'Invalid flag'], 422);
        }

        $product->$flag = !$product->$flag;
        $product->save();

        return response()->json([
            'success'   => true,
            'flag'      => $flag,
            'status'    => $product->$flag,
            'message'   => "Updated {$product->name} merchandising.",
        ]);
    }

    /**
     * Product Related & Suggested Items Management View
     */
    public function productRelations($id)
    {
        $product = Product::with(['relatedProducts', 'suggestedProducts', 'crossSells'])->findOrFail($id);
        $allProducts = Product::where('id', '!=', $id)->where('is_active', true)->get(['id', 'name', 'sku', 'price', 'image']);

        return view('content.apps.store-management.relations', compact('product', 'allProducts'));
    }

    /**
     * Store Product Relation
     */
    public function storeProductRelation(Request $request, $id)
    {
        $request->validate([
            'related_id' => 'required|exists:products,id',
            'type'       => 'required|in:related,suggested,cross_sell',
        ]);

        $product = Product::findOrFail($id);
        
        \Illuminate\Support\Facades\DB::table('product_relations')->updateOrInsert(
            [
                'product_id' => $product->id,
                'related_id' => $request->related_id,
                'type'       => $request->type,
            ],
            [
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return back()->with('success', "Product recommendation added as {$request->type} item.");
    }

    /**
     * Delete Product Relation
     */
    public function destroyProductRelation($id, $relatedId, $type)
    {
        \Illuminate\Support\Facades\DB::table('product_relations')
            ->where('product_id', $id)
            ->where('related_id', $relatedId)
            ->where('type', $type)
            ->delete();

        return back()->with('success', 'Recommendation item removed.');
    }
}

