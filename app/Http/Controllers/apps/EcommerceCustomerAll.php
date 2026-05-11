<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

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
        $totalSpent = Order::where('user_id', $user->id)->sum('total_amount');

        return [
          'id' => $user->id,
          'customer' => $user->name,
          'customer_id' => str_pad($user->id, 5, '0', STR_PAD_LEFT),
          'email' => $user->email,
          'phone' => $user->phone,
          'country' => $user->country ?? 'United States',
          'country_code' => $user->country ? strtolower($user->country) : 'us',
          'order' => $user->orders_count,
          'total_spent' => '$' . number_format($totalSpent, 2),
          'address_line_1' => $user->address_line_1,
          'address_line_2' => $user->address_line_2,
          'town' => $user->town,
          'state' => $user->state,
          'post_code' => $user->post_code,
          'image' => ''
        ];
      });
      return response()->json(['data' => $formatted]);
    }

    return view('content.apps.app-ecommerce-customer-all');
  }

  public function store(Request $request)
  {
    $request->validate([
      'customerName' => 'required|string|max:255',
      'customerEmail' => 'required|email|unique:users,email',
      'customerContact' => 'nullable|string',
      'customerAddress1' => 'nullable|string',
      'customerAddress2' => 'nullable|string',
      'customerTown' => 'nullable|string',
      'customerState' => 'nullable|string',
      'pin' => 'nullable|string',
      'country' => 'nullable|string',
    ]);

    User::create([
      'name' => $request->customerName,
      'email' => $request->customerEmail,
      'password' => bcrypt('password'),
      'phone' => $request->customerContact,
      'address_line_1' => $request->customerAddress1,
      'address_line_2' => $request->customerAddress2,
      'town' => $request->customerTown,
      'state' => $request->customerState,
      'post_code' => $request->pin,
      'country' => $request->country,
      'user_type' => 'customer'
    ]);

    return response()->json(['success' => true, 'message' => 'Customer created successfully.']);
  }

  public function update(Request $request, $id)
  {
    $user = User::findOrFail($id);
    $request->validate([
      'customerName' => 'required|string|max:255',
      'customerEmail' => 'required|email|unique:users,email,' . $user->id,
      'customerContact' => 'nullable|string',
      'customerAddress1' => 'nullable|string',
      'customerAddress2' => 'nullable|string',
      'customerTown' => 'nullable|string',
      'customerState' => 'nullable|string',
      'pin' => 'nullable|string',
      'country' => 'nullable|string',
    ]);

    $user->update([
      'name' => $request->customerName,
      'email' => $request->customerEmail,
      'phone' => $request->customerContact,
      'address_line_1' => $request->customerAddress1,
      'address_line_2' => $request->customerAddress2,
      'town' => $request->customerTown,
      'state' => $request->customerState,
      'post_code' => $request->pin,
      'country' => $request->country,
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
