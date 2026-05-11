@extends('accounting::components.layouts.master')

@section('title', 'Add Prefix - Journal Name')

@section('content')
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
                <h5 class="mb-4">Setting For {{ ucfirst($moduleName) }}</h5>
                @include('accounting::accounting.accounting-menu')
            </div>
        </div>

        <!-- Content -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="chart_of_accounts" role="tabpanel">
                    <div class="container px-0">
                        <div class="card shadow rounded">
                            <div class="card-header">
                                <h5 class="mb-0">Add Journal Prefix</h5>
                            </div>

                            <div class="card-body">
                                {{-- Success Message --}}
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                {{-- Validation Errors --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form id="addActionForm" action="{{ route('accounting.prefix.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="journal_name" class="form-label">Journal Name</label>
                                        <input type="text" name="journal_name" id="journal_name" class="form-control"
                                            placeholder="Enter journal name" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Prefix</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
