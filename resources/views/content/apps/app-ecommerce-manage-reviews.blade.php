@extends('layouts/layoutMaster')

@section('title', 'Customer Reviews & Feedback Management - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
<style>
  .star-rating-picker {
    display: flex;
    gap: 6px;
    font-size: 28px;
    cursor: pointer;
    color: #CBD5E1;
  }
  .star-rating-picker .star-item.selected,
  .star-rating-picker .star-item:hover,
  .star-rating-picker .star-item.hovered {
    color: #F59E0B;
  }
  .review-card-item {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border-radius: 16px;
  }
  .review-card-item:hover {
    box-shadow: 0 8px 24px -6px rgba(15, 23, 42, 0.08);
    border-color: #CBD5E1;
  }
  .gold-star {
    color: #F59E0B;
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Page Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1">
        <i class="icon-base bx bx-star text-warning me-2"></i>{{ __('Customer Reviews & Testimonial Management') }}
      </h4>
      <p class="text-muted mb-0">{{ __('Moderate, curate, and create authentic customer ratings and reviews across your product catalog.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal">
        <i class="icon-base bx bx-plus me-1"></i> {{ __('Add Review') }}
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
      <i class="icon-base bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Metrics Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-5">
      <div class="card h-100 border shadow-xs rounded-4">
        <div class="card-body row align-items-center g-3">
          <div class="col-sm-5 border-end pe-sm-4 text-center text-sm-start">
            <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 mb-1">
              <h2 class="fw-bold text-dark mb-0">{{ $avgRating }}</h2>
              <i class="icon-base bx bxs-star fs-2 text-warning"></i>
            </div>
            <p class="text-muted small mb-2">{{ __('Total') }} <strong>{{ number_format($totalReviews) }}</strong> {{ __('reviews') }}</p>
            <span class="badge bg-label-primary rounded-pill small">+{{ $thisWeek }} {{ __('this week') }}</span>
          </div>

          <div class="col-sm-7 ps-sm-4">
            @foreach([5, 4, 3, 2, 1] as $star)
              @php
                $count = $starCounts[$star] ?? 0;
                $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
              @endphp
              <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="small fw-semibold text-muted" style="min-width: 32px;">{{ $star }} ★</span>
                <div class="progress flex-grow-1 bg-label-secondary" style="height: 6px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="small text-muted font-monospace" style="min-width: 24px; text-align: right;">{{ $count }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-7">
      <div class="row g-4 h-100">
        <div class="col-sm-6">
          <div class="card border shadow-xs rounded-4 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-bold">{{ __('Satisfaction Rate') }}</span>
                <h3 class="fw-bold my-1 text-success">{{ $positivePercent }}%</h3>
                <span class="badge bg-label-success small">{{ __('4 & 5 Star Ratings') }}</span>
              </div>
              <div class="avatar avatar-lg bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                <i class="icon-base bx bx-smile fs-3"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="card border shadow-xs rounded-4 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-bold">{{ __('New This Month') }}</span>
                <h3 class="fw-bold my-1 text-info">{{ number_format($newThisMonth) }}</h3>
                <span class="badge bg-label-info small">{{ __('Recent Submissions') }}</span>
              </div>
              <div class="avatar avatar-lg bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                <i class="icon-base bx bx-chat fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Toolbar Card -->
  <div class="card border shadow-xs rounded-4 mb-4 overflow-hidden">
    <div class="card-header border-bottom bg-light py-3">
      <form action="{{ route('app-ecommerce-manage-reviews') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-white"><i class="icon-base bx bx-search"></i></span>
            <input type="text" name="q" class="form-control" placeholder="{{ __('Search reviewer, product, title or comment...') }}" value="{{ request('q') }}">
          </div>
        </div>

        <div class="col-6 col-md-2">
          <select name="rating" class="form-select" onchange="this.form.submit()">
            <option value="">{{ __('All Ratings') }}</option>
            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars ★★★★★</option>
            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars ★★★★☆</option>
            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars ★★★☆☆</option>
            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars ★★☆☆☆</option>
            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star ★☆☆☆☆</option>
          </select>
        </div>

        <div class="col-6 col-md-2">
          <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">{{ __('All Statuses') }}</option>
            <option value="Published" {{ request('status') === 'Published' ? 'selected' : '' }}>{{ __('Published') }}</option>
            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>{{ __('Pending Approval') }}</option>
          </select>
        </div>

        <div class="col-12 col-md-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
          @if(request()->hasAny(['q', 'rating', 'status', 'product_id']))
            <a href="{{ route('app-ecommerce-manage-reviews') }}" class="btn btn-label-secondary">{{ __('Reset') }}</a>
          @endif
        </div>
      </form>
    </div>

    <!-- Reviews Table -->
    <div class="table-responsive text-nowrap">
      <table class="table align-middle table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('Product') }}</th>
            <th>{{ __('Reviewer') }}</th>
            <th>{{ __('Rating & Comment') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reviewsList as $review)
            <tr>
              <td class="text-muted small">#{{ $review->id }}</td>
              <td>
                <div class="d-flex align-items-center gap-2.5" style="max-width: 260px;">
                  <div class="avatar avatar-sm rounded-3 bg-light d-flex align-items-center justify-content-center overflow-hidden border flex-shrink-0">
                    <i class="icon-base bx bx-package text-secondary fs-4"></i>
                  </div>
                  <div class="text-truncate">
                    <h6 class="mb-0 text-dark text-truncate fw-bold" style="font-size: 13.5px;" title="{{ $review->product->name ?? 'N/A' }}">
                      {{ $review->product->name ?? 'N/A' }}
                    </h6>
                    <small class="text-muted">{{ $review->product->category->name ?? __('Aisle Product') }}</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold text-uppercase">
                    {{ substr($review->user->name ?? 'C', 0, 1) }}
                  </div>
                  <div>
                    <span class="fw-bold text-dark d-block" style="font-size: 13.5px;">{{ $review->user->name ?? 'Customer' }}</span>
                    <small class="text-muted">{{ $review->user->email ?? 'N/A' }}</small>
                    @if($review->is_verified_purchase)
                      <span class="badge bg-label-success rounded-pill px-1.5 py-0.5 ms-1" style="font-size: 10px;">
                        <i class="icon-base bx bx-check me-0.5"></i>{{ __('Verified Buyer') }}
                      </span>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                <div style="max-width: 380px;">
                  <div class="d-flex align-items-center gap-1 mb-1">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="icon-base bx {{ $i <= $review->rating ? 'bxs-star gold-star' : 'bx-star text-muted' }}" style="font-size: 14px;"></i>
                    @endfor
                    @if($review->title)
                      <span class="fw-bold text-dark small ms-1 text-truncate">{{ $review->title }}</span>
                    @endif
                  </div>
                  <p class="text-muted small mb-0 text-truncate" title="{{ $review->comment }}">
                    {{ $review->comment ?: 'No written comment' }}
                  </p>
                </div>
              </td>
              <td>
                <span class="small text-muted">{{ $review->created_at->format('M d, Y') }}</span>
              </td>
              <td>
                @if($review->status === 'Published' || $review->status === 'approved')
                  <span class="badge bg-label-success"><i class="icon-base bx bx-check me-1"></i>{{ __('Published') }}</span>
                @else
                  <span class="badge bg-label-warning"><i class="icon-base bx bx-time me-1"></i>{{ __('Pending') }}</span>
                @endif
              </td>
              <td class="text-end">
                <div class="d-inline-flex align-items-center gap-1">
                  <!-- Quick Status Toggle -->
                  <form action="{{ route('app-ecommerce-manage-reviews.update', $review->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ $review->status === 'Published' ? 'Pending' : 'Published' }}">
                    <button type="submit" class="btn btn-sm btn-icon {{ $review->status === 'Published' ? 'btn-label-warning' : 'btn-label-success' }}" title="{{ $review->status === 'Published' ? __('Unpublish (Make Pending)') : __('Approve & Publish') }}">
                      <i class="icon-base bx {{ $review->status === 'Published' ? 'bx-hide' : 'bx-check' }}"></i>
                    </button>
                  </form>

                  <!-- Delete Review -->
                  <form action="{{ route('app-ecommerce-manage-reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="{{ __('Delete Review') }}">
                      <i class="icon-base bx bx-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <i class="icon-base bx bx-star fs-1 text-muted mb-2"></i>
                <h6 class="text-muted fw-bold">{{ __('No customer reviews found') }}</h6>
                <p class="small text-muted mb-0">{{ __('Click "Add Review" above to create an authentic customer testimonial or rating.') }}</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($reviewsList->hasPages())
      <div class="card-footer d-flex justify-content-end py-2">
        {{ $reviewsList->links() }}
      </div>
    @endif
  </div>
</div>

<!-- Modal: Add New Customer Review -->
<div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold"><i class="icon-base bx bx-star text-warning me-2"></i>{{ __('Create Customer Review') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('app-ecommerce-manage-reviews.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <!-- Product Selector -->
          <div class="mb-4">
            <label class="form-label fw-bold">{{ __('Select Product') }} <span class="text-danger">*</span></label>
            <select name="product_id" class="form-select select2-basic" required>
              <option value="">{{ __('Choose a product to review...') }}</option>
              @foreach($products as $prod)
                <option value="{{ $prod->id }}">{{ $prod->name }} (${{ number_format($prod->price, 2) }})</option>
              @endforeach
            </select>
          </div>

          <!-- Reviewer Identity Mode: Existing User vs New Customer -->
          <div class="mb-4">
            <label class="form-label fw-bold d-block">{{ __('Reviewer Identity') }} <span class="text-danger">*</span></label>
            <div class="btn-group w-100 mb-3" role="group">
              <input type="radio" class="btn-check" name="customer_mode" id="modeExisting" value="existing" checked onchange="toggleCustomerMode('existing')">
              <label class="btn btn-outline-primary fw-semibold" for="modeExisting">{{ __('Select Existing User') }}</label>

              <input type="radio" class="btn-check" name="customer_mode" id="modeNew" value="new" onchange="toggleCustomerMode('new')">
              <label class="btn btn-outline-primary fw-semibold" for="modeNew">{{ __('Create New Customer on Fly') }}</label>
            </div>

            <!-- Mode 1: Existing User Dropdown -->
            <div id="sectionExistingUser">
              <select name="user_id" id="selectExistingUser" class="form-select">
                <option value="">{{ __('Select registered customer...') }}</option>
                @foreach($users as $usr)
                  <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->email }})</option>
                @endforeach
              </select>
            </div>

            <!-- Mode 2: New Customer Form Fields -->
            <div id="sectionNewUser" class="d-none row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold">{{ __('Customer Full Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" id="inputCustomerName" class="form-control" placeholder="e.g. Sarah Jenkins">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">{{ __('Customer Email Address') }} <span class="text-danger">*</span></label>
                <input type="email" name="customer_email" id="inputCustomerEmail" class="form-control" placeholder="sarah.jenkins@example.com">
              </div>
            </div>
          </div>

          <!-- Interactive Star Rating Picker -->
          <div class="mb-4">
            <label class="form-label fw-bold d-block">{{ __('Rating Score') }} <span class="text-danger">*</span></label>
            <div class="d-flex align-items-center gap-3">
              <div class="star-rating-picker" id="starPicker">
                <span class="star-item selected" data-value="1">★</span>
                <span class="star-item selected" data-value="2">★</span>
                <span class="star-item selected" data-value="3">★</span>
                <span class="star-item selected" data-value="4">★</span>
                <span class="star-item selected" data-value="5">★</span>
              </div>
              <span class="badge bg-label-warning fw-bold fs-6" id="ratingTextBadge">5.0 / 5 Stars (Excellent)</span>
              <input type="hidden" name="rating" id="inputRatingValue" value="5">
            </div>
          </div>

          <!-- Review Headline Title -->
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Review Headline / Title (Optional)') }}</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Incredibly fresh and fast delivery!">
          </div>

          <!-- Review Body / Comment -->
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Review Body / Testimonial') }} <span class="text-danger">*</span></label>
            <textarea name="comment" class="form-control" rows="4" placeholder="Write the customer feedback or testimonial here..." required></textarea>
          </div>

          <!-- Status & Badges -->
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">{{ __('Publication Status') }}</label>
              <select name="status" class="form-select">
                <option value="Published">{{ __('Published (Visible on Storefront)') }}</option>
                <option value="Pending">{{ __('Pending (Draft / Hidden)') }}</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-center mt-md-4">
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="is_verified_purchase" id="checkVerifiedPurchase" value="1" checked>
                <label class="form-check-label fw-semibold" for="checkVerifiedPurchase">
                  {{ __('Mark as Verified Buyer Purchase') }}
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary fw-bold"><i class="icon-base bx bx-check me-1"></i>{{ __('Save & Publish Review') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
function toggleCustomerMode(mode) {
  const secExisting = document.getElementById('sectionExistingUser');
  const secNew = document.getElementById('sectionNewUser');
  const selUser = document.getElementById('selectExistingUser');
  const inpName = document.getElementById('inputCustomerName');
  const inpEmail = document.getElementById('inputCustomerEmail');

  if (mode === 'existing') {
    secExisting.classList.remove('d-none');
    secNew.classList.add('d-none');
    selUser.required = true;
    inpName.required = false;
    inpEmail.required = false;
  } else {
    secExisting.classList.add('d-none');
    secNew.classList.remove('d-none');
    selUser.required = false;
    inpName.required = true;
    inpEmail.required = true;
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // Interactive Star Rating Picker
  const stars = document.querySelectorAll('#starPicker .star-item');
  const inputRating = document.getElementById('inputRatingValue');
  const badge = document.getElementById('ratingTextBadge');

  const ratingDescriptions = {
    1: '1.0 / 5 Stars (Poor)',
    2: '2.0 / 5 Stars (Fair)',
    3: '3.0 / 5 Stars (Good)',
    4: '4.0 / 5 Stars (Very Good)',
    5: '5.0 / 5 Stars (Excellent)'
  };

  stars.forEach(star => {
    star.addEventListener('click', function () {
      const val = parseInt(this.getAttribute('data-value'));
      inputRating.value = val;
      badge.textContent = ratingDescriptions[val];

      stars.forEach(s => {
        const sVal = parseInt(s.getAttribute('data-value'));
        if (sVal <= val) {
          s.classList.add('selected');
        } else {
          s.classList.remove('selected');
        }
      });
    });

    star.addEventListener('mouseenter', function () {
      const val = parseInt(this.getAttribute('data-value'));
      stars.forEach(s => {
        const sVal = parseInt(s.getAttribute('data-value'));
        if (sVal <= val) {
          s.classList.add('hovered');
        } else {
          s.classList.remove('hovered');
        }
      });
    });

    star.addEventListener('mouseleave', function () {
      stars.forEach(s => s.classList.remove('hovered'));
    });
  });
});
</script>
@endsection