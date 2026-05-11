@extends('layouts/layoutMaster')

@section('title', 'Customer Types - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
    @vite('Modules/General/resources/assets/js/customer-types-list.js')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Customer Types</h4>

            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Customer Types</h5>
                    <a href="{{ route('customer-types.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Add Customer Type
                    </a>
                </div>
                <div class="card-datatable">
                    <table class="datatables-customer-types table border-top">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Customers Count</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customerTypes as $type)
                                <tr>
                                    <td></td>
                                    <td>{{ $type->name }}</td>
                                    <td>{{ $type->customers_count }}</td>
                                    <td>{{ optional($type->created_at)->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('customer-types.show', $type->id) }}">
                                                    <i class="bx bx-show me-1"></i> View
                                                </a>
                                                <a class="dropdown-item"
                                                    href="{{ route('customer-types.edit', $type->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <form action="{{ route('customer-types.destroy', $type->id) }}"
                                                    method="POST" class="delete-customer-type-form">
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
        </div>
    </div>
@endsection
