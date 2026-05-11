@extends('layouts/layoutMaster')

@section('title', 'Edit Document Template')

@section('vendor-style')
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    @vite('resources/js/HS/sweet-alerts.js')
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Edit Document Template</h5>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('general.templates.update', $template->uuid) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Type Dropdown -->
                <div class="mb-3">
                    <label for="type" class="form-label">Document Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="">Select Document Type</option>
                        <option value="invoice" {{ old('type', $template->type) == 'invoice' ? 'selected' : '' }}>Invoice
                        </option>
                        <option value="credit_note" {{ old('type', $template->type) == 'credit_note' ? 'selected' : '' }}>
                            Credit Note</option>
                        <option value="debit_note" {{ old('type', $template->type) == 'debit_note' ? 'selected' : '' }}>
                            Debit Note</option>
                    </select>
                </div>

                <!-- Template Design Dropdown -->
                <div class="mb-3">
                    <label for="template_id" class="form-label">Select Template</label>
                    <select name="template_id" id="template_id" class="form-select">
                        <option value="">Select a Template Design</option>
                        @foreach ($templateDesigns as $design)
                            <option value="{{ $design->id }}"
                                {{ old('template_id', $template->template_id) == $design->id ? 'selected' : '' }}>
                                {{ $design->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Header Image -->
                <div class="mb-3">
                    <label for="header_image" class="form-label">Header Image</label>
                    <input type="file" name="header_image" id="header_image" class="form-control">
                    @if ($template->header_image)
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Current Header Image:</small>
                            <img src="{{ $template->headerImageUrl }}" alt="Current Header" class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                    @endif
                </div>

                <!-- Footer Image -->
                <div class="mb-3">
                    <label for="footer_image" class="form-label">Footer Image</label>
                    <input type="file" name="footer_image" id="footer_image" class="form-control">
                    @if ($template->footer_image)
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Current Footer Image:</small>
                            <img src="{{ $template->footerImageUrl }}" alt="Current Footer" class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                    @endif
                </div>

                <!-- Is Active -->
                <div class="mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" name="is_active" class="form-check-input"
                            {{ $template->is_active ? 'checked' : '' }}>
                        Is Active
                    </label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Template</button>
                    <a href="{{ route('general.templates.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
