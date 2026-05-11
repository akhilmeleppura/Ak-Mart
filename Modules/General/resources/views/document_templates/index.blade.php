@extends('layouts.layoutMaster')

@section('title', 'Document Templates Management')

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
    @vite('resources/js/HS/sweet-alert-form.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DELETE CONFIRMATION
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const uuid = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("{{ route('general.templates.destroy', ':uuid') }}"
                                    .replace(':uuid', uuid), {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        }
                                    })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire(
                                            'Deleted!',
                                            data.message,
                                            'success'
                                        ).then(() => location.reload());
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            data.message,
                                            'error'
                                        );
                                    }
                                })
                                .catch(err => {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong!',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });

            // TOGGLE STATUS
            const statusButtons = document.querySelectorAll('.status-toggle-btn');
            statusButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const uuid = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Change Status?',
                        text: "Do you want to change the status of this template?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, change it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("{{ route('general.templates.toggle-status', ':uuid') }}"
                                    .replace(':uuid', uuid), {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        }
                                    })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire(
                                            'Updated!',
                                            data.message,
                                            'success'
                                        ).then(() => location.reload());
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            data.message,
                                            'error'
                                        );
                                    }
                                })
                                .catch(err => {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong!',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });
        });
    </script>
@endsection

@section('content')

    {{-- 🔔 Global Message Section --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> There were some problems with your input:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Create Form Card -->
    <div class="card mb-4">
        <h5 class="card-header">Upload Document Template Images</h5>
        <div class="card-body">
            <form action="{{ route('general.templates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="type" class="form-label">Document Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="">Select Document Type</option>
                        <option value="invoice">Invoice</option>
                        <option value="credit_note">Credit Note</option>
                        <option value="debit_note">Debit Note</option>
                    </select>
                </div>

                <!-- Template Design Dropdown -->
                <div class="mb-3">
                    <label for="template_id" class="form-label">Select Template</label>
                    <select name="template_id" id="template_id" class="form-select">
                        <option value="">Select a Template Design</option>
                        @foreach ($templateDesigns as $design)
                            <option value="{{ $design->id }}" {{ old('template_id') == $design->id ? 'selected' : '' }}>
                                {{ $design->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="header_image" class="form-label">Header Image</label>
                    <input type="file" name="header_image" id="header_image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="footer_image" class="form-label">Footer Image</label>
                    <input type="file" name="footer_image" id="footer_image" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" name="is_active" class="form-check-input" checked>
                        Is Active
                    </label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Upload Template</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Listing Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Document Templates List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Template Design</th>
                            <th>Header Image</th>
                            <th>Footer Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $template)
                            <tr>
                                <td>{{ $template->name }}</td>
                                <td>
                                    @if ($template->template)
                                        <span class="badge bg-info">{{ $template->template->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($template->header_image)
                                        <img src="{{ $template->headerImageUrl }}" alt="Header" class="img-thumbnail"
                                            style="max-width: 150px;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($template->footer_image)
                                        <img src="{{ $template->footerImageUrl }}" alt="Footer" class="img-thumbnail"
                                            style="max-width: 150px;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('general.templates.edit', $template->uuid) }}"
                                        class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                    <button type="button" class="btn btn-sm btn-outline-info status-toggle-btn"
                                        data-id="{{ $template->uuid }}">
                                        Toggle Status
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="{{ $template->uuid }}">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No templates found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
