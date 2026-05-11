@extends('layouts/layoutMaster')

@section('title', 'Customer List - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
    @vite('Modules/General/resources/assets/js/customers-list.js')
@endsection

@section('content')
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
    <!-- Customers List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Search Filters</h5>
            <div class="d-flex justify-content-between align-items-center row pt-4 gap-md-0 g-6">
                <div class="col-md-4 customer_type">
                    <select id="customer-type-filter" class="form-select">
                        <option value="">All Types</option>
                        @foreach ($customerTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 customer_status">
                    <select id="customer-status-filter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Add Customer
                    </a>
                </div>
            </div>
        </div>
        <div class="card-datatable">
            <table class="datatables-customers table border-top" id="customers-table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr data-id="{{ $customer->id }}">
                            <td></td>
                            <td>
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr($customer->name, 0, 1) }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>{{ $customer->customerType->name ?? '-' }}</td>
                            <td>{!! $customer->status_badge !!}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('customers.show', $customer->id) }}">
                                            <i class="bx bx-show me-1"></i> View
                                        </a>
                                        <a class="dropdown-item" href="{{ route('customers.edit', $customer->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-item toggle-status"
                                            data-id="{{ $customer->id }}"
                                            data-url="{{ route('customers.toggle-status', $customer->id) }}"
                                            data-current-status="{{ $customer->status }}">
                                            <i class="bx bx-power-off me-1"></i>
                                            {{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Offcanvas to add new customer -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddCustomer"
        aria-labelledby="offcanvasAddCustomerLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasAddCustomerLabel" class="offcanvas-title">Add Customer</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
            <form class="add-new-customer pt-0" id="addNewCustomerForm" action="{{ route('customers.store') }}"
                method="POST">
                @csrf
                <div class="mb-6 form-control-validation">
                    <label class="form-label" for="add-customer-name">Name</label>
                    <input type="text" class="form-control" id="add-customer-name" placeholder="John Doe"
                        name="name" aria-label="John Doe" required />
                </div>
                <div class="mb-6 form-control-validation">
                    <label class="form-label" for="add-customer-email">Email</label>
                    <input type="email" id="add-customer-email" class="form-control"
                        placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="email" required />
                </div>
                <div class="mb-6">
                    <label class="form-label" for="add-customer-phone">Phone</label>
                    <input type="text" id="add-customer-phone" class="form-control phone-mask"
                        placeholder="+1 (609) 988-44-11" aria-label="+1 (609) 988-44-11" name="phone" />
                </div>
                <div class="mb-6">
                    <label class="form-label" for="add-customer-address">Address</label>
                    <textarea id="add-customer-address" class="form-control" placeholder="123 Main St, City, State"
                        aria-label="123 Main St, City, State" name="address"></textarea>
                </div>
                <div class="mb-6">
                    <label class="form-label" for="add-customer-status">Status</label>
                    <select id="add-customer-status" class="form-select" name="status" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="form-label" for="customer-type">Customer Type</label>
                    <select id="customer-type" class="form-select" name="customer_type_id" required>
                        <option value="">Select Type</option>
                        @foreach ($customerTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary me-3 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
@endsection
