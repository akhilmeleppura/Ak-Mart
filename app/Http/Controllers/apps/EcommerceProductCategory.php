<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EcommerceProductCategory extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $query = Category::with(['parent'])->withCount('products');

      // Category Hierarchy Filter
      if ($request->has('category_filter')) {
        if ($request->category_filter === 'parent') {
          $query->whereNull('parent_id');
        } elseif ($request->category_filter === 'sub') {
          $query->whereNotNull('parent_id');
        }
      }

      // Date Filter
      if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
        $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
      }

      $categories = $query->get();
      $data = $categories->map(function ($cat) {
        return [
          'id' => $cat->id,
          'categories' => $cat->name,
          'category_detail' => $cat->description ?? 'No description',
          'total_products' => $cat->products_count,
          'total_earnings' => '$' . number_format($cat->products->sum('price'), 2),
          'parent_name' => $cat->parent->name ?? 'Main Category',
          'parent_id' => $cat->parent_id,
          'date' => $cat->created_at->format('Y-m-d'), // For date filtering/handling
          'icon' => 'bx bx-purchase-tag',
          'cat_image' => '',
          'slug' => $cat->slug
        ];
      });
      return response()->json(['data' => $data]);
    }

    $parentCategories = Category::whereNull('parent_id')->get();
    return view('content.apps.app-ecommerce-category-list', compact('parentCategories'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'categoryTitle' => 'required|string|max:255',
      'description' => 'nullable|string',
      'slug' => 'nullable|string|max:255',
      'parent_id' => 'nullable|exists:categories,id'
    ]);

    Category::create([
      'name' => $request->categoryTitle,
      'description' => $request->description ?? '',
      'slug' => $request->slug ?: Str::slug($request->categoryTitle),
      'parent_id' => $request->parent_id ?: null
    ]);

    return redirect()->route('app-ecommerce-product-category')->with('success', 'Category added!');
  }

  public function update(Request $request, $id)
  {
    $category = Category::findOrFail($id);

    $request->validate([
      'categoryTitle' => 'required|string|max:255',
      'description' => 'nullable|string',
      'slug' => 'nullable|string|max:255',
      'parent_id' => 'nullable|exists:categories,id'
    ]);

    $category->update([
      'name' => $request->categoryTitle,
      'description' => $request->description ?? '',
      'slug' => $request->slug ?: Str::slug($request->categoryTitle),
      'parent_id' => $request->parent_id ?: null
    ]);

    return response()->json(['success' => true, 'message' => 'Category updated.']);
  }

  public function destroy($id)
  {
    $category = Category::findOrFail($id);
    $category->delete();
    return response()->json(['success' => true, 'message' => 'Category deleted.']);
  }
}
