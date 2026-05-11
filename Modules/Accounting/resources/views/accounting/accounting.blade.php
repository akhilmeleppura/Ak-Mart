@extends('accounting::components.layouts.master')

@section('title', 'accounting - Apps')


@section('content')
    <div class="row g-6">
        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
                <h5 class="mb-4">Getting Started</h5>
                @include('accounting::accounting.accounting-menu')
            </div>
        </div>

        <!-- Main content -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="chart_of_accounts" role="tabpanel">
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">Chart of Accounts</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                                + Add New Account
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Subcategory</th>
                                            <th>Opening Balance</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($accounts) > 0)
                                            @foreach ($accounts as $account)
                                                <tr>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td>{{ $account->mainCategory->name ?? '-' }}</td>
                                                    <td>{{ $account->subCategory->name ?? '-' }}</td>
                                                    <td>{{ number_format($account->opening_balance, 2) }}</td>
                                                    <td>{{ $account->status ? 'Active' : 'Inactive' }}</td>
                                                    <td class="text-center">
                                                        <button
                                                            class="btn btn-sm {{ $account->status ? 'btn-danger' : 'btn-success' }}">
                                                            {{ $account->status ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">No accounts found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Add New Account -->
                    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="addActionForm" method="POST" action="{{ route('accounting.chart.store') }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addAccountModalLabel">Add New Account</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label for="main_category_id" class="form-label">Account Category</label>
                                            <select name="main_category_id" id="main_category_id" class="form-select"
                                                required>
                                                @foreach ($mainCategories as $main)
                                                    <option value="{{ $main->id }}">{{ $main->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="subcategory_id" class="form-label">Subcategory</label>
                                            <select name="subcategory_id" id="subcategory_id" class="form-select" required>
                                                @foreach ($subcategories as $sub)
                                                    <option value="{{ $sub->id }}">{{ $sub->name }} -
                                                        {{ $sub->text }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="account_name" class="form-label">Account Name</label>
                                            <input type="text" name="account_name" id="account_name" class="form-control"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="opening_balance" class="form-label">Opening Balance</label>
                                            <input type="number" step="0.01" name="opening_balance" id="opening_balance"
                                                class="form-control" required>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Account</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Modal -->

                </div>
            </div>
        </div>
    </div>
@endsection
