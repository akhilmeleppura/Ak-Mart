<?php

namespace Modules\General\App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customers\CustomerType;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerTypes = CustomerType::withCount('customers')->get();
        return view('general::customer-types.index', compact('customerTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('general::customer-types.create');
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
            'name' => 'required|string|max:255|unique:customer_types,name'
        ]);

        try {
            $customerType = CustomerType::create($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer type created successfully.',
                    'customerType' => $customerType,
                    'message' => 'Customer type has been added to the system.'
                ]);
            }

            return redirect()->route('customer-types.index')
                ->with('success', 'Customer type created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to create customer type.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Failed to create customer type.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customers\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerType $customerType)
    {
        $customers = $customerType->customers;
        return view('general::customer-types.show', compact('customerType', 'customers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customers\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerType $customerType)
    {
        return view('general::customer-types.edit', compact('customerType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customers\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerType $customerType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:customer_types,name,'.$customerType->id
        ]);

        try {
            $customerType->update($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer type updated successfully.',
                    'customerType' => $customerType,
                    'message' => 'Customer type information has been updated.'
                ]);
            }

            return redirect()->route('customer-types.index')
                ->with('success', 'Customer type updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to update customer type.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Failed to update customer type.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customers\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerType $customerType, Request $request)
    {
        try {
            // Check if there are customers associated with this type
            if ($customerType->customers()->count() > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Cannot delete customer type.',
                        'message' => 'There are customers associated with this type. Please reassign or delete those customers first.'
                    ], 422);
                }
                
                return redirect()->route('customer-types.index')
                    ->with('error', 'Cannot delete customer type. There are customers associated with this type.');
            }

            $typeName = $customerType->name;
            $customerType->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Customer type deleted successfully.',
                    'message' => "Customer type '{$typeName}' has been removed from the system."
                ]);
            }

            return redirect()->route('customer-types.index')
                ->with('success', 'Customer type deleted successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to delete customer type.',
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->route('customer-types.index')
                ->with('error', 'Failed to delete customer type.');
        }
    }
}