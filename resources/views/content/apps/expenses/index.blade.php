@extends('layouts/layoutMaster')

@section('title', 'Expense Management — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-receipt text-primary me-2"></i> Expense Management</h4>
        <p class="text-muted small mb-0">Record operational store expenditures, categorize costs, and track profit & loss</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="bx bx-folder-plus me-1"></i> Add Category
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
            <i class="bx bx-plus me-1"></i> Record Expense
        </button>
    </div>
</div>

{{-- Summary cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card bg-label-primary">
            <div class="card-body">
                <span class="text-heading">Total Expenses (All Time)</span>
                <h4 class="my-1">${{ number_format($totalExpenses, 2) }}</h4>
                <small>Gross store expenditure</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card bg-label-warning">
            <div class="card-body">
                <span class="text-heading">This Month Expenses</span>
                <h4 class="my-1">${{ number_format($thisMonthExpenses, 2) }}</h4>
                <small>Current billing period</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card bg-label-info">
            <div class="card-body">
                <span class="text-heading">Expense Categories</span>
                <h4 class="my-1">{{ $categories->count() }}</h4>
                <small>Active cost centers</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Expenses Ledger</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Expense Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Reference #</th>
                    <th>Recorded By</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                    <tr>
                        <td><small>{{ $exp->expense_date->format('d M Y') }}</small></td>
                        <td>
                            <strong class="text-heading">{{ $exp->title }}</strong>
                            @if($exp->notes)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($exp->notes, 40) }}</div>@endif
                        </td>
                        <td><span class="badge bg-label-info">{{ $exp->category?->name ?? 'General' }}</span></td>
                        <td><strong class="text-danger">-${{ number_format($exp->amount, 2) }}</strong></td>
                        <td><span class="badge bg-label-secondary">{{ ucfirst($exp->payment_method) }}</span></td>
                        <td><code>{{ $exp->reference_no ?: '—' }}</code></td>
                        <td><small>{{ $exp->user?->name ?? 'Staff' }}</small></td>
                        <td class="text-end">
                            <form action="{{ route('app-expenses-destroy', $exp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bx bx-receipt fs-1 d-block mb-2 opacity-50"></i>
                            No expenses recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer border-top py-3">
        {{ $expenses->links() }}
    </div>
</div>

<!-- Modal: Record Expense -->
<div class="modal fade" id="createExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-expenses-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Record Store Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., Monthly Electricity Bill" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Category *</label>
                            <select name="expense_category_id" class="form-select" required>
                                <option value="">-- Choose --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Amount ($) *</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required min="0.01">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Expense Date *</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Method *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="cheque">Cheque</option>
                                <option value="upi">UPI / Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reference / Invoice #</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="e.g., INV-8921">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional details or voucher notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="{{ route('app-expenses-category-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Packaging, Utilities, Rent" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
