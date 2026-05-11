@extends('accounting::components.layouts.master')

@section('title', 'Chart of Accounts')

@section('accounting-content')
    <div class="tab-content p-0">
        <div class="tab-pane fade show active" id="chart_of_accounts" role="tabpanel">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-4">Setting For {{ ucfirst($moduleName) }}</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        + Add New Account
                    </button>
                </div>
                <div class="card-body">
                    <!-- Chart table here -->
                </div>
            </div>

            @include('accounting::accounting.modals.add-account-modal')
        </div>
    </div>
@endsection
