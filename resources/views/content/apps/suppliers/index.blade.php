@extends('layouts/layoutMaster')

@section('title', 'Supplier Management — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-truck text-primary me-2"></i> Supplier Management</h4>
        <p class="text-muted small mb-0">Manage vendor contact details, balances, and purchase relationships</p>
    </div>
    <button class="btn btn-ak-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
        <i class="bx bx-plus me-1"></i> Add Supplier
    </button>
</div>

<div class="card shadow-sm border">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Supplier</th>
                        <th>Company</th>
                        <th>Contact Email</th>
                        <th>Phone</th>
                        <th>Current Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td class="fw-semibold text-heading">{{ $s->name }}</td>
                        <td>{{ $s->company_name ?: 'N/A' }}</td>
                        <td>{{ $s->email ?: 'N/A' }}</td>
                        <td>{{ $s->phone ?: 'N/A' }}</td>
                        <td class="fw-bold text-primary">${{ number_format($s->balance, 2) }}</td>
                        <td>
                            <span class="badge {{ $s->is_active ? 'badge-ak-success' : 'badge-ak-danger' }}">
                                {{ $s->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-xs btn-label-danger" onclick="deleteSupplier({{ $s->id }})">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bx bx-buildings fs-2 d-block mb-2 opacity-50"></i>
                            No suppliers found. Click "Add Supplier" to register one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add Supplier -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-suppliers-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Supplier Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Global Foods Distributors" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Global Foods LLC" />
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="supplier@example.com" />
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+1 555-0199" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Full address..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-ak-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteSupplier(id) {
    Swal.fire({
        title: 'Delete Supplier?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        confirmButtonText: 'Yes, Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('app/suppliers') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
}
</script>
@endsection
