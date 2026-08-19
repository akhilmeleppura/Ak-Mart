@extends('layouts/layoutMaster')

@section('title', __('Hero Sliders & Promotional Carousel') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-carousel text-primary me-2"></i> {{ __('Homepage Hero Sliders & Promotional Banners') }}</h4>
        <p class="text-muted small mb-0">{{ __('Design dynamic marketing carousels with custom promo badges, CTA action links, and responsive backgrounds') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('storefront.home') }}" target="_blank" class="btn btn-outline-primary rounded-pill">
            <i class="bx bx-show me-1"></i> {{ __('Preview Storefront') }}
        </a>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addSliderModal">
            <i class="bx bx-plus me-1"></i> {{ __('Add New Slider') }}
        </button>
    </div>
</div>

<div class="row g-4">
    @forelse($sliders as $slider)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden d-flex flex-column justify-content-between">
                <!-- Slide Preview Card -->
                <div class="p-4 text-white position-relative" style="background: {{ $slider->bg_color ?: '#1E40AF' }}; min-height: 200px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold">{{ $slider->badge_text }}</span>
                        <span class="badge {{ $slider->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $slider->is_active ? __('Live on Store') : __('Draft Mode') }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-white mb-1">{{ $slider->title }}</h5>
                    <p class="small text-white-50 mb-3">{{ Str::limit($slider->subtitle, 85) }}</p>
                    <a href="{{ $slider->link_url }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                        {{ $slider->button_text }} <i class="bx bx-right-arrow-alt align-middle"></i>
                    </a>
                </div>

                <!-- Slide Actions Footer -->
                <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('app-sliders-toggle', $slider->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $slider->is_active ? 'btn-label-warning' : 'btn-label-success' }} rounded-pill px-2.5">
                                {{ $slider->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </form>
                        <small class="text-muted">{{ __('Order:') }} <strong>#{{ $slider->sort_order }}</strong></small>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#editSliderModal-{{ $slider->id }}">
                            <i class="bx bx-edit-alt"></i> {{ __('Edit') }}
                        </button>
                        <form action="{{ route('app-sliders-destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Delete this hero slider?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1"><i class="bx bx-trash fs-5"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Edit Slider -->
        <div class="modal fade" id="editSliderModal-{{ $slider->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('app-sliders-update', $slider->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Edit Slide') }}: {{ $slider->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label">{{ __('Slide Title') }}</label>
                                <input type="text" name="title" class="form-control" value="{{ $slider->title }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Promo Badge') }}</label>
                                <input type="text" name="badge_text" class="form-control" value="{{ $slider->badge_text }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Subtitle / Description') }}</label>
                            <textarea name="subtitle" class="form-control" rows="2">{{ $slider->subtitle }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('CTA Button Text') }}</label>
                                <input type="text" name="button_text" class="form-control" value="{{ $slider->button_text }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">{{ __('Target URL / Action Link') }}</label>
                                <input type="text" name="link_url" class="form-control" value="{{ $slider->link_url }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label">{{ __('Background CSS Gradient') }}</label>
                                <input type="text" name="bg_color" class="form-control" value="{{ $slider->bg_color }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Display Sort Order') }}</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ $slider->sort_order }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bx bx-carousel fs-1 mb-2"></i>
            <h5>{{ __('No hero sliders created yet.') }}</h5>
            <p>{{ __('Create engaging promotional banners to showcase flash deals and express deliveries.') }}</p>
        </div>
    @endforelse
</div>

<!-- Modal: Add Slider -->
<div class="modal fade" id="addSliderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('app-sliders-store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Create Hero Carousel Slide') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('Slide Title') }}</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Fresh Farm Produce & Organic Dairy" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Promo Badge Text') }}</label>
                        <input type="text" name="badge_text" class="form-control" placeholder="e.g. 30-Min Express Delivery">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Subtitle / Description') }}</label>
                    <textarea name="subtitle" class="form-control" rows="2" placeholder="Hand-picked supermarket quality delivered straight to your doorstep..."></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('CTA Button Text') }}</label>
                        <input type="text" name="button_text" class="form-control" value="Shop Now" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">{{ __('Target URL / Link') }}</label>
                        <input type="text" name="link_url" class="form-control" value="/store/shop">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('Background Gradient / Color CSS') }}</label>
                        <input type="text" name="bg_color" class="form-control" value="linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #0D9488 100%)">
                        <small class="text-muted">Presets: 
                            <a href="javascript:void(0)" onclick="setGradient('linear-gradient(135deg, #065F46 0%, #059669 50%, #10B981 100%)')">🌱 Green Organic</a> | 
                            <a href="javascript:void(0)" onclick="setGradient('linear-gradient(135deg, #1E1B4B 0%, #3730A3 50%, #4F46E5 100%)')">⚡ Indigo Express</a> | 
                            <a href="javascript:void(0)" onclick="setGradient('linear-gradient(135deg, #7C2D12 0%, #C2410C 50%, #EA580C 100%)')">🔥 Sunset Deals</a>
                        </small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Slide Display Order') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Publish Slide') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
function setGradient(val) {
    document.querySelector('input[name="bg_color"]').value = val;
}
</script>
@endsection
