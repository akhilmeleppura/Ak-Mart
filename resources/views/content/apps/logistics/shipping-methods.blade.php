@extends('layouts/layoutMaster')

@section('title', 'Shipping Methods - Logistics')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Shipping Methods</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMethodModal">
                    <i class="bx bx-plus me-1"></i> Add Method
                </button>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Carrier</th>
                                <th>Base Cost</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($methods as $method)
                                <tr>
                                    <td><strong>{{ $method->name }}</strong></td>
                                    <td><span class="badge bg-label-info">{{ strtoupper($method->carrier_code) }}</span></td>
                                    <td>${{ number_format($method->base_cost, 2) }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-status" type="checkbox" data-id="{{ $method->id }}" {{ $method->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('app-logistics-shipping-destroy', $method->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No shipping methods found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Method Modal -->
<div class="modal fade" id="addMethodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shipping Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('app-logistics-shipping-store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Method Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. FedEx Express" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Carrier Code</label>
                        <select name="carrier_code" class="form-select" required>
                            <option value="fedex">FedEx</option>
                            <option value="shiprocket">Shiprocket</option>
                            <option value="dhl">DHL</option>
                            <option value="self">Self Pickup / Local</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Base Cost ($)</label>
                        <input type="number" name="base_cost" class="form-control" step="0.01" value="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Method</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-status').forEach(el => {
        el.addEventListener('change', function() {
            const id = this.dataset.id;
            fetch(`/app/logistics/shipping/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
        });
    });
});
</script>
@endsection
