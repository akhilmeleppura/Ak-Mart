@extends('accounting::components.layouts.master')


@section('title', 'accounting - Apps')


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

                    <!-- Add Subcategory Button Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">Category Management</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                                + Add Category
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-0">Use the button above to add a new category with main type and
                                description.</p>
                        </div>
                    </div>

                    <!-- Modal: Add Subcategory -->
                    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="addActionForm" method="POST" action="{{ route('accounting.subcategory.store') }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addAccountModalLabel">Add Subcategory</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label for="main_category_id" class="form-label">Main Category</label>
                                            <select name="main_category_id" id="main_category_id"
                                                class="form-select select2" required>
                                                <option value="">-- Select Category --</option>
                                                @foreach ($mainCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Subcategory Name</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" id="description" class="form-control"></textarea>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Subcategory</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Subcategory List Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Subcategory List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Main Category</th>
                                            <th>Subcategory Name</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subCategories as $index => $subcategory)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $subcategory->mainCategory->name ?? '-' }}</td>
                                                <td>{{ $subcategory->name }}</td>
                                                <td>{{ $subcategory->description ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No subcategories found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
