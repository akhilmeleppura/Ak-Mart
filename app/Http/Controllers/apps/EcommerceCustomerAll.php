<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcommerceCustomerAll extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $query = User::where('user_type', 'customer')->withCount('orders');

      // Date Filter
      if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
        $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
      }

      $users = $query->get();
      $formatted = $users->map(function ($user) {
        $orders = Order::where('user_id', $user->id)->get();
        $totalSpent = (float) $orders->sum('total_amount');
        $orderCount = $orders->count();
        $aov = $orderCount > 0 ? ($totalSpent / $orderCount) : 0;
        
        $firstOrder = $orders->sortBy('created_at')->first();
        $lastOrder = $orders->sortByDesc('created_at')->first();

        // Favorite Category
        $favCat = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.user_id', $user->id)
            ->select('categories.name', DB::raw('COUNT(*) as total_items'))
            ->groupBy('categories.name')
            ->orderByDesc('total_items')
            ->first();

        // Deterministic Customer Segmentation
        $group = 'New';
        $daysSinceLastOrder = $lastOrder ? now()->diffInDays($lastOrder->created_at) : 999;

        if ($totalSpent >= 500 || $orderCount >= 10) {
            $group = 'VIP';
        } elseif ($totalSpent >= 200) {
            $group = 'High Value';
        } elseif ($orderCount >= 3 && $daysSinceLastOrder > 60) {
            $group = 'At Risk';
        } elseif ($orderCount >= 2 && $daysSinceLastOrder <= 60) {
            $group = 'Regular';
        } elseif ($orderCount == 0 && now()->diffInDays($user->created_at) > 30) {
            $group = 'Inactive';
        }

        // Net loyalty points
        $loyaltyPoints = LoyaltyTransaction::getCustomerBalance($user->id);

        return [
          'id'                => $user->id,
          'customer'          => $user->name,
          'customer_id'       => str_pad($user->id, 5, '0', STR_PAD_LEFT),
          'email'             => $user->email,
          'phone'             => $user->phone ?? 'N/A',
          'country'           => $user->country ?? 'United States',
          'country_code'      => $user->country ? strtolower($user->country) : 'us',
          'order'             => $orderCount,
          'total_spent'       => '$' . number_format($totalSpent, 2),
          'aov'               => '$' . number_format($aov, 2),
          'group'             => $group,
          'loyalty_points'    => $loyaltyPoints,
          'favorite_category' => $favCat ? $favCat->name : 'N/A',
          'first_order'       => $firstOrder ? $firstOrder->created_at->format('d M Y') : 'None',
          'last_order'        => $lastOrder ? $lastOrder->created_at->format('d M Y') : 'None',
          'address_line_1'    => $user->address_line_1,
          'address_line_2'    => $user->address_line_2,
          'town'              => $user->town,
          'state'             => $user->state,
          'post_code'         => $user->post_code,
          'image'             => $user->profile_photo_url ?? ''
        ];
      });
      return response()->json(['data' => $formatted]);
    }

    return view('content.apps.app-ecommerce-customer-all');
  }

  public function store(Request $request)
  {
    $request->validate([
      'customerName'    => 'required|string|max:255',
      'customerEmail'   => 'required|email|unique:users,email',
      'customerContact' => 'nullable|string',
      'customerAddress1'=> 'nullable|string',
      'customerAddress2'=> 'nullable|string',
      'customerTown'    => 'nullable|string',
      'customerState'   => 'nullable|string',
      'pin'             => 'nullable|string',
      'country'         => 'nullable|string',
    ]);

    User::create([
      'name'           => $request->customerName,
      'email'          => $request->customerEmail,
      'password'       => bcrypt('password'),
      'phone'          => $request->customerContact,
      'address_line_1' => $request->customerAddress1,
      'address_line_2' => $request->customerAddress2,
      'town'           => $request->customerTown,
      'state'          => $request->customerState,
      'post_code'      => $request->pin,
      'country'        => $request->country,
      'user_type'      => 'customer'
    ]);

    return response()->json(['success' => true, 'message' => 'Customer created successfully.']);
  }

  public function update(Request $request, $id)
  {
    $user = User::findOrFail($id);
    $request->validate([
      'customerName'    => 'required|string|max:255',
      'customerEmail'   => 'required|email|unique:users,email,' . $user->id,
      'customerContact' => 'nullable|string',
      'customerAddress1'=> 'nullable|string',
      'customerAddress2'=> 'nullable|string',
      'customerTown'    => 'nullable|string',
      'customerState'   => 'nullable|string',
      'pin'             => 'nullable|string',
      'country'         => 'nullable|string',
    ]);

    $user->update([
      'name'           => $request->customerName,
      'email'          => $request->customerEmail,
      'phone'          => $request->customerContact,
      'address_line_1' => $request->customerAddress1,
      'address_line_2' => $request->customerAddress2,
      'town'           => $request->customerTown,
      'state'          => $request->customerState,
      'post_code'      => $request->pin,
      'country'        => $request->country,
    ]);

    return response()->json(['success' => true, 'message' => 'Customer updated successfully.']);
  }

  public function destroy($id)
  {
    $user = User::findOrFail($id);
    $user->delete();
    return response()->json(['success' => true, 'message' => 'Customer deleted.']);
  }
}
