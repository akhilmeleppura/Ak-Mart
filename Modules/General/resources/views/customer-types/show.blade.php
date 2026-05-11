@extends('layouts/layoutMaster')

@section('title', 'Customer Type Details - Pages')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Customer Types /</span> Customer Type Details</h4>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <h5 class="card-header">Customer Type Information</h5>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-0">Name</p>
                                    <h6 class="mb-0">{{ $customerType->name }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0">Customers Count</p>
                                    <h6 class="mb-0">{{ $customers->count() }}</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0">Created At</p>
                                    <h6 class="mb-0">{{ $customerType->created_at->format('M d, Y') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0">Last Updated</p>
                                    <h6 class="mb-0">{{ $customerType->updated_at->format('M d, Y') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <h5 class="card-header">Customers with this Type</h5>
                        <div class="card-body">
                            @if ($customers->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($customers as $customer)
                                                <tr>
                                                    <td>{{ $customer->name }}</td>
                                                    <td>{{ $customer->email }}</td>
                                                    <td>{{ $customer->phone ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('customers.show', $customer->id) }}"
                                                            class="btn btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No customers found with this type.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('customer-types.edit', $customerType->id) }}" class="btn btn-primary me-2">Edit Customer
                    Type</a>
                <a href="{{ route('customer-types.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection
