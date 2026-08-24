<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EcommerceManageReviews extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && !$request->has('page')) {
            $reviews = Review::with(['product.category', 'user'])->latest()->get();

            $data = $reviews->map(function ($review) {
                return [
                    'id'            => $review->id,
                    'product'       => $review->product->name ?? 'N/A',
                    'company_name'  => $review->product->category->name ?? 'Uncategorized',
                    'product_image' => $review->product->image ?? null,
                    'reviewer'      => $review->user->name ?? 'Anonymous Customer',
                    'email'         => $review->user->email ?? '',
                    'avatar'        => null,
                    'review'        => $review->rating,
                    'head'          => $review->title ?? 'Customer Review',
                    'para'          => $review->comment ?? '',
                    'date'          => $review->created_at->format('M d, Y'),
                    'status'        => $review->status,
                ];
            });

            return response()->json(['data' => $data]);
        }

        // Server-side query with filters
        $query = Review::with(['product.category', 'user'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'LIKE', "%{$q}%")
                    ->orWhere('comment', 'LIKE', "%{$q}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$q}%")->orWhere('email', 'LIKE', "%{$q}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$q}%"));
            });
        }

        $reviewsList = $query->paginate(15)->withQueryString();

        // Aggregate stats
        $totalReviews   = Review::count();
        $avgRating      = round(Review::avg('rating') ?: 5.0, 1);
        $thisWeek       = Review::whereBetween('created_at', [now()->startOfWeek(), now()])->count();

        $starCounts = [];
        for ($s = 5; $s >= 1; $s--) {
            $starCounts[$s] = Review::where('rating', $s)->count();
        }

        $positivePercent = $totalReviews > 0
            ? round(($starCounts[5] + $starCounts[4]) / $totalReviews * 100)
            : 100;

        $newThisMonth = Review::whereMonth('created_at', now()->month)->count();

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('content.apps.app-ecommerce-manage-reviews', compact(
            'totalReviews', 'avgRating', 'thisWeek', 'starCounts', 'positivePercent', 'newThisMonth',
            'reviewsList', 'products', 'users'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'title'      => 'nullable|string|max:255',
            'comment'    => 'required|string|max:2000',
            'status'     => 'required|in:Published,Pending',
        ]);

        if ($request->customer_mode === 'new') {
            $request->validate([
                'customer_name'  => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
            ]);

            $user = User::firstOrCreate(
                ['email' => strtolower(trim($request->customer_email))],
                [
                    'name'     => trim($request->customer_name),
                    'password' => bcrypt(Str::random(16)),
                ]
            );
            $userId = $user->id;
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            $userId = $request->user_id;
        }

        Review::create([
            'product_id'           => $request->product_id,
            'user_id'              => $userId,
            'rating'               => (int) $request->rating,
            'title'                => $request->title ?: 'Verified Customer Review',
            'comment'              => $request->comment,
            'status'               => $request->status,
            'is_verified_purchase' => $request->has('is_verified_purchase'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Review created successfully!']);
        }

        return redirect()->route('app-ecommerce-manage-reviews')->with('success', 'Customer review added successfully!');
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($request->has('status')) {
            $review->update(['status' => $request->status]);
        }

        if ($request->has('rating')) {
            $review->update([
                'rating'  => (int) $request->rating,
                'title'   => $request->title ?? $review->title,
                'comment' => $request->comment ?? $review->comment,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Review updated.']);
        }

        return redirect()->back()->with('success', 'Review updated.');
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Review deleted.']);
        }

        return redirect()->back()->with('success', 'Review removed successfully.');
    }
}
