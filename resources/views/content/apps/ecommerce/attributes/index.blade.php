@extends('layouts/layoutMaster')

@section('title', __('Product Attributes & EAV Management') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-slider-alt text-primary me-2"></i> {{ __('Product Attributes & EAV System') }}</h4>
        <p class="text-muted small mb-0">{{ __('Define dynamic product attributes, variant properties, and custom specifications') }}</p>
    </div>
    <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addAttributeModal">
        <i class="bx bx-plus me-1"></i> {{ __('Add New Attribute') }}
    </button>
</div>

<div class="row g-4">
    @forelse($attributes as $attr)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border shadow-sm p-4 rounded-3 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $attr->name }}</h5>
                            <code class="small text-muted">{{ $attr->code }}</code>
                        </div>
                        <span class="badge bg-label-info text-uppercase">{{ $attr->type }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">{{ __('Configured Values:') }}</small>
                        <div class="d-flex flex-wrap gap-1.5">
                            @forelse($attr->values as $val)
                                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                    @if($val->color_code)
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $val->color_code }}; display: inline-block;"></span>
                                    @endif
                                    {{ $val->value }}
                                </span>
                            @empty
                                <span class="text-muted small fst-italic">{{ __('No option values yet.') }}</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                    <form action="{{ route('app-attributes-value-store', $attr->id) }}" method="POST" class="d-flex gap-1 flex-grow-1 me-2">
                        @csrf
                        <input type="text" name="value" class="form-control form-control-sm" placeholder="{{ __('Add value...') }}" required>
                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bx bx-plus"></i></button>
                    </form>

                    <form action="{{ route('app-attributes-destroy', $attr->id) }}" method="POST" onsubmit="return confirm('Delete this attribute?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bx bx-trash fs-5"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bx bx-slider-alt fs-1 mb-2"></i>
            <h5>{{ __('No product attributes configured yet.') }}</h5>
            <p>{{ __('Create custom attributes like Weight, Color, Flavour, or Packaging Size.') }}</p>
        </div>
    @endforelse
</div>

<!-- Modal: Add Attribute -->
<div class="modal fade" id="addAttributeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('app-attributes-store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Create Product Attribute') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('Attribute Label') }}</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Flavour, Size, Net Weight" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="type" class="form-select">
                        <option value="select">{{ __('Select Dropdown') }}</option>
                        <option value="color">{{ __('Color Picker') }}</option>
                        <option value="text">{{ __('Text') }}</option>
                        <option value="number">{{ __('Number') }}</option>
                        <option value="boolean">{{ __('Yes / No (Boolean)') }}</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Attribute') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
