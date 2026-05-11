<?php

namespace Modules\General\App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerType;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Customer::with('customerType');
        
        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }
        
        // Filter by customer type if provided
        if ($request->has('customer_type_id') && $request->customer_type_id) {
            $query->where('customer_type_id', $request->customer_type_id);
        }
        
        $customers = $query->get();
        $customerTypes = CustomerType::all();
        
        return view('general::customers.index', compact('customers', 'customerTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customerTypes = CustomerType::all();
        // Get customers for stats in create view
        $customers = Customer::all();
        return view('general::customers.create', compact('customerTypes', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'customer_type_id' => 'required|exists:customer_types,id'
        ]);

        try {
            $customer = Customer::create($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer created successfully.',
                    'customer' => $customer,
                    'message' => "Customer '{$customer->name}' has been added to the system."
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to create customer.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Failed to create customer.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customers\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('general::customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customers\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        $customerTypes = CustomerType::all();
        return view('general::customers.edit', compact('customer', 'customerTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customers\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,'.$customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'customer_type_id' => 'required|exists:customer_types,id'
        ]);

        try {
            $customer->update($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer updated successfully.',
                    'customer' => $customer,
                    'message' => "Customer '{$customer->name}' information has been updated."
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to update customer.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Failed to update customer.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customers\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer, Request $request)
    {
        try {
            $customerName = $customer->name;
            $customer->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer deleted successfully.',
                    'message' => "Customer '{$customerName}' has been removed from the system."
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to delete customer.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->route('customers.index')
                ->with('error', 'Failed to delete customer.');
        }
    }

    /**
     * Toggle customer status.
     *
     * @param  \App\Models\Customers\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(Customer $customer, Request $request)
    {
        try {
            $newStatus = $customer->status === 'active' ? 'inactive' : 'active';
            $customer->update(['status' => $newStatus]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer status updated successfully.',
                    'message' => "Customer '{$customer->name}' status has been changed to {$newStatus}.",
                    'status' => $newStatus
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer status updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to update customer status.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->route('customers.index')
                ->with('error', 'Failed to update customer status.');
        }
    }
}