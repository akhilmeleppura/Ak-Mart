<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class EcommerceManageReviews extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['product', 'user'])->get();

            $data = $reviews->map(function ($review) {
                return [
                    'id'            => $review->id,
                    'product'       => $review->product->name ?? 'N/A',
                    'company_name'  => $review->product->category->name ?? 'Uncategorized',
                    'product_image' => null,
                    'reviewer'      => $review->user->name ?? 'Anonymous',
                    'email'         => $review->user->email ?? '',
                    'avatar'        => null,
                    'review'        => $review->rating,
                    'head'          => $review->title ?? 'Review',
                    'para'          => $review->comment ?? '',
                    'date'          => $review->created_at->toDateString(),
                    'status'        => $review->status,
                ];
            });

            return response()->json(['data' => $data]);
        }

        // Aggregate stats for the summary cards
        $totalReviews   = Review::count();
        $avgRating      = round(Review::avg('rating'), 2);
        $thisWeek       = Review::whereBetween('created_at', [now()->startOfWeek(), now()])->count();

        $starCounts = [];
        for ($s = 5; $s >= 1; $s--) {
            $starCounts[$s] = Review::where('rating', $s)->count();
        }

        $positivePercent = $totalReviews > 0
            ? round(($starCounts[5] + $starCounts[4]) / $totalReviews * 100)
            : 0;

        $newThisMonth = Review::whereMonth('created_at', now()->month)->count();

        return view('content.apps.app-ecommerce-manage-reviews', compact(
            'totalReviews', 'avgRating', 'thisWeek', 'starCounts', 'positivePercent', 'newThisMonth'
        ));
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
