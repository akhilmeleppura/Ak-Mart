@extends('layouts/layoutMaster')

@section('title', 'Payment Settings - Project Details')

@section('content')
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
                <h5 class="mb-4">Setting For {{ ucfirst($moduleName) }}</h5>
                {{-- This sub-menu might need to be adjusted for this context --}}
                @include('billing::payment-options.sub-menu')
            </div>
        </div>

        <!-- Content -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="payment_settings" role="tabpanel">
                    <div class="container px-0">
                        {{-- *** PERSONALIZED PAYMENT OPTIONS CARD *** --}}
                        <div class="card shadow rounded mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Your Payment Options</h5>
                                <span class="badge bg-info text-dark">Personalized Settings</span>
                            </div>

                            <div class="card-body">
                                {{-- Success Message --}}
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                {{-- Error Message --}}
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                {{-- Form for updating personalized payment options --}}
                                <form action="{{ route('payment-options.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Select Your Preferred Payment Options</label>
                                        <p class="text-muted small">Choose the payment options you want to use. You can select multiple options.</p>
                                        
                                        <div class="row">
                                            @forelse ($paymentOptions as $option)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="selected_payment_options[]" 
                                                               value="{{ $option->id }}" 
                                                               id="payment_option_{{ $option->id }}"
                                                               {{ in_array($option->id, $selectedPaymentOptions) ? 'checked' : '' }}>
                                                        <label class="form-check-label d-flex align-items-center" for="payment_option_{{ $option->id }}">
                                                            <span class="me-2">{{ $option->name }}</span>
                                                            @if ($defaultOption && $defaultOption->id === $option->id)
                                                                <span class="badge bg-primary">Default</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    <div class="alert alert-warning">
                                                        No payment options found. Please contact your administrator.
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Save My Payment Options
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- *** MANAGE PAYMENT OPTIONS CARD *** --}}
                        <div class="card shadow rounded mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Manage Payment Options</h5>
                                <span class="badge bg-info text-dark">Live Settings</span>
                            </div>

                            <div class="card-body">
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

                                {{-- Form for updating statuses --}}
                                <form action="{{ route('payment-options.update') }}" method="POST">
                                    @csrf
                                    @method('PUT') {{-- Use PUT method for updates --}}

                                    <div class="list-group">
                                        @forelse ($paymentOptions as $option)
                                            <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <div class="d-flex align-items-center">
                                                    {{-- Checkbox for Active Status --}}
                                                    <div class="form-check form-switch me-3">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                               name="active_status[{{ $option->id }}]"
                                                               value="1" {{ $option->is_active ? 'checked' : '' }}>
                                                    </div>

                                                    {{-- Option Details --}}
                                                    <div>
                                                        <h6 class="mb-1 fw-semibold">{{ $option->name }}</h6>
                                                        <small class="text-muted">{{ $option->description }}</small>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center">
                                                    {{-- Default Badge / Button --}}
                                                    @if ($defaultOption && $defaultOption->id === $option->id)
                                                        <span class="badge bg-primary me-2">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Default
                                                        </span>
                                                    @else
                                                        {{-- Form to set a new default --}}
                                                        <form action="{{ route('payment-options.update') }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="set_default" value="{{ $option->id }}">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                Set as Default
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="list-group-item text-center text-muted">
                                                No payment options found. Please run the seeder.
                                            </div>
                                        @endforelse
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection