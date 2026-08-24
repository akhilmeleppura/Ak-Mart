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

  public function treeView()
  {
    $totalCategories = Category::count();
    $mainCategoriesCount = Category::whereNull('parent_id')->count();
    $subCategoriesCount = Category::whereNotNull('parent_id')->count();
    $allCategories = Category::with(['parent', 'children'])->orderBy('name')->get();
    $parentCategories = Category::whereNull('parent_id')->with('children')->get();

    return view('content.apps.app-ecommerce-category-tree', compact(
      'totalCategories',
      'mainCategoriesCount',
      'subCategoriesCount',
      'parentCategories',
      'allCategories'
    ));
  }

  public function treeData()
  {
    $categories = Category::withCount('products')->orderBy('name')->get();
    $tree = $this->buildJsTree($categories, null);
    return response()->json($tree);
  }

  private function buildJsTree($categories, $parentId = null)
  {
    $branch = [];
    $nodes = $categories->where('parent_id', $parentId);

    foreach ($nodes as $node) {
      $children = $this->buildJsTree($categories, $node->id);
      $hasChildren = !empty($children);
      $isRoot = empty($parentId);
      
      // Determine Icon based on hierarchy depth & children
      if ($isRoot) {
        $icon = $hasChildren ? 'icon-base bx bx-folder-open text-primary' : 'icon-base bx bx-folder text-primary';
      } else {
        $icon = $hasChildren ? 'icon-base bx bx-folder text-warning' : 'icon-base bx bx-git-branch text-success';
      }

      $item = [
        'id' => (string) $node->id,
        'text' => $node->name . ' <span class="badge bg-label-primary rounded-pill ms-1 font-monospace" style="font-size: 10px;">' . $node->products_count . ' items</span>',
        'icon' => $icon,
        'state' => ['opened' => true],
        'data' => [
          'id'             => $node->id,
          'name'           => $node->name,
          'slug'           => $node->slug,
          'description'    => $node->description ?? '',
          'parent_id'      => $node->parent_id,
          'products_count' => $node->products_count,
          'has_children'   => $hasChildren,
          'is_root'        => $isRoot,
        ],
      ];

      if ($hasChildren) {
        $item['children'] = $children;
      }

      $branch[] = $item;
    }

    return $branch;
  }

  public function moveNode(Request $request)
  {
    $request->validate([
      'id'        => 'required|exists:categories,id',
      'parent_id' => 'nullable',
    ]);

    $nodeId = (int) $request->id;
    $parentId = ($request->parent_id === '#' || empty($request->parent_id)) ? null : (int) $request->parent_id;

    if ($parentId) {
      if ($nodeId === $parentId) {
        return response()->json(['success' => false, 'message' => 'A category cannot be its own parent.'], 422);
      }

      // Prevent circular hierarchy (cannot move parent inside its own descendant)
      $movingCategory = Category::findOrFail($nodeId);
      $descendantIds = array_map('intval', $movingCategory->getAllCategoryIds());

      if (in_array($parentId, $descendantIds, true)) {
        return response()->json([
          'success' => false,
          'message' => 'Cannot move a parent category inside one of its own subcategories (Circular Dependency).'
        ], 422);
      }
    }

    $category = Category::findOrFail($nodeId);
    $category->update([
      'parent_id' => $parentId,
    ]);

    $total = Category::count();
    $mainCount = Category::whereNull('parent_id')->count();
    $subCount = Category::whereNotNull('parent_id')->count();

    $actionMsg = $parentId 
      ? "Category '{$category->name}' is now a subcategory under " . (Category::find($parentId)?->name ?? 'parent') . "!"
      : "Category '{$category->name}' promoted to Main Top-Level Aisle!";

    return response()->json([
      'success' => true,
      'message' => $actionMsg,
      'stats'   => [
        'total' => $total,
        'main'  => $mainCount,
        'sub'   => $subCount,
      ]
    ]);
  }
}
