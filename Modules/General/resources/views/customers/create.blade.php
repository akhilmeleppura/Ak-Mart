@extends('layouts/layoutMaster')

@section('title', 'Create Customer - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
    @vite('Modules/General/resources/assets/js/customer-form.js')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Create Customer</h4>

            <div class="row g-6 mb-6">
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">Total Customers</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">{{ $customers->count() }}</h4>
                                        <p class="text-success mb-0">(+{{ rand(5, 30) }}%)</p>
                                    </div>
                                    <small class="mb-0">All registered customers</small>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="icon-base bx bx-group icon-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">Active Customers</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">{{ $customers->where('status', 'active')->count() }}</h4>
                                        <p class="text-success mb-0">(+{{ rand(5, 20) }}%)</p>
                                    </div>
                                    <small class="mb-0">Currently active customers</small>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="icon-base bx bx-user-check icon-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">Inactive Customers</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">{{ $customers->where('status', 'inactive')->count() }}</h4>
                                        <p class="text-danger mb-0">(-{{ rand(5, 15) }}%)</p>
                                    </div>
                                    <small class="mb-0">Currently inactive customers</small>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="icon-base bx bx-user-x icon-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">New Customers</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">
                                            {{ $customers->where('created_at', '>=', now()->subDays(30))->count() }}</h4>
                                        <p class="text-warning mb-0">(+{{ rand(1, 10) }}%)</p>
                                    </div>
                                    <small class="mb-0">Joined in last 30 days</small>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="icon-base bx bx-user-plus icon-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <h5 class="card-header">Customer Information</h5>
                        <div class="card-body">
                            <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="phone">Phone</label>
                                        <input type="text" class="form-control phone-mask" id="phone" name="phone"
                                            value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active"
                                                {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="customer_type_id">Customer Type</label>
                                    <select class="form-select" id="customer_type_id" name="customer_type_id" required>
                                        <option value="">Select Type</option>
                                        @foreach ($customerTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('customer_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer_type_id')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary me-2">Save Customer</button>
                                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
