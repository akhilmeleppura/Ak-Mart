@extends('layouts.storefrontMaster')

@section('title', $product->name . ' — AK-Mart')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('storefront.home') }}" class="text-decoration-none">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('storefront.shop') }}" class="text-decoration-none">{{ __('Shop') }}</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('storefront.shop', ['category' => $product->category->id]) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Overview Grid -->
    <div class="row g-5 mb-5">
        <!-- Gallery Images -->
        <div class="col-lg-5">
            <div class="card p-4 border shadow-xs rounded-4 text-center bg-white position-relative">
                <button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-xs border-0" onclick="quickToggleWishlist({{ $product->id }}, this, event)" title="{{ __('Save to Wishlist') }}">
                    <i class="bx {{ in_array($product->id, session('wishlist', [])) ? 'bxs-heart text-danger' : 'bx-heart text-muted' }} fs-4 align-middle"></i>
                </button>
                <button class="btn btn-light rounded-circle position-absolute top-0 start-0 m-3 shadow-xs border-0 p-2" onclick="quickToggleCompare({{ $product->id }}, this, event)" style="z-index: 10;" title="{{ __('Add to Compare') }}">
                    <i class="bx {{ in_array($product->id, session('compare_list', [])) ? 'bx-git-compare text-primary fw-bold' : 'bx-git-compare text-muted' }} fs-4 align-middle"></i>
                </button>
                <img src="{{ $product->image ? asset($product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" id="mainProductImg" class="img-fluid rounded-3 object-fit-contain" style="max-height: 380px;" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-7">
            <span class="badge bg-label-primary px-3 py-1.5 rounded-pill mb-2 fw-semibold">{{ $product->category?->name ?? __('Grocery') }}</span>
            <h2 class="fw-bold mb-2">{{ $product->name }}</h2>

            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="text-warning small">
                    <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star-half"></i>
                </div>
                <span class="text-muted small">({{ $product->reviews->count() }} {{ __('verified customer reviews') }})</span>
                <span class="text-muted small">| SKU: <strong>{{ $product->sku }}</strong></span>
            </div>

            <!-- Price & Stock -->
            <div class="d-flex align-items-baseline gap-3 mb-4 flex-wrap">
                <span class="display-6 fw-bold text-primary" id="dynamicTotalPrice">${{ number_format($product->price, 2) }}</span>
                <span class="text-muted small align-self-center" id="unitPriceLabel">(${{ number_format($product->price, 2) }} / each)</span>
                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="text-decoration-line-through text-muted fs-5" id="dynamicComparePrice">${{ number_format($product->compare_at_price, 2) }}</span>
                    <span class="badge bg-danger rounded-pill" id="dynamicSavingsBadge">{{ __('Save') }} ${{ number_format($product->compare_at_price - $product->price, 2) }}</span>
                @endif
            </div>

            <div class="mb-4">
                @if($availableStock > 5)
                    <span class="badge bg-label-success px-3 py-1.5 rounded-pill"><i class="bx bx-check-circle me-1"></i> {{ __('In Stock') }} ({{ $availableStock }} {{ __('available') }})</span>
                @elseif($availableStock > 0)
                    <span class="badge bg-label-warning px-3 py-1.5 rounded-pill"><i class="bx bx-error-circle me-1"></i> {{ __('Low Stock') }} ({{ __('Only') }} {{ $availableStock }} {{ __('left!') }})</span>
                @else
                    <span class="badge bg-label-danger px-3 py-1.5 rounded-pill"><i class="bx bx-x-circle me-1"></i> {{ __('Sold Out / Backorder Available') }}</span>
                @endif
            </div>

            <p class="text-muted mb-4 lead fs-6">{{ $product->description ?: __('Fresh hand-picked supermarket quality guaranteed. Certified organic and verified for daily essentials.') }}</p>

            <!-- Quantity & Actions -->
            <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                @if($availableStock > 0)
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary px-3" type="button" id="btnQtyMinus" onclick="adjustQty(-1)">-</button>
                        <input type="number" id="detailQty" class="form-control text-center fw-bold fs-5" value="1" min="1" max="{{ $availableStock }}" oninput="updateRate()">
                        <button class="btn btn-outline-secondary px-3" type="button" id="btnQtyPlus" onclick="adjustQty(1)">+</button>
                    </div>
                    <button class="btn btn-primary btn-lg rounded-pill px-4 flex-grow-1 d-flex align-items-center justify-content-center gap-2" onclick="addDetailToCart({{ $product->id }})">
                        <i class="bx bx-cart-add fs-4"></i>
                        <span id="btnCartText">{{ __('Add to Cart') }} • ${{ number_format($product->price, 2) }}</span>
                    </button>
                @else
                    <button class="btn btn-warning btn-lg rounded-pill px-4 flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#backInStockModal">
                        <i class="bx bx-bell fs-4"></i>
                        <span>{{ __('Notify Me When Back in Stock') }}</span>
                    </button>
                @endif
            </div>

            <!-- Perks -->
            <div class="border-top pt-3 text-muted small d-flex flex-column gap-2">
                <div><i class="bx bx-check text-success me-2 fs-5 align-middle"></i> {{ __('30-Minute Doorstep Delivery Available') }}</div>
                <div><i class="bx bx-check text-success me-2 fs-5 align-middle"></i> {{ __('Earn Loyalty Points with this purchase') }}</div>
                <div><i class="bx bx-check text-success me-2 fs-5 align-middle"></i> {{ __('Hassle-Free Return & Instant Store Credit Refund') }}</div>
            </div>
        </div>
    </div>

    <!-- Frequently Bought Together Bundle Card -->
    @if($frequentlyBought->isNotEmpty())
        @php
            $bundleTotal = $product->price + $frequentlyBought->sum('price');
        @endphp
        <div class="card border-primary border shadow-sm rounded-4 p-4 mb-5 bg-primary bg-opacity-10">
            <h4 class="fw-bold mb-3 text-primary"><i class="bx bxs-zap me-2"></i> {{ __('Frequently Bought Together (Recommended Bundle)') }}</h4>
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <!-- Main Product -->
                        <div class="bg-white p-2 rounded-3 border text-center" style="width: 120px;">
                            <img src="{{ $product->image ? asset($product->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="60" height="60" class="object-fit-contain">
                            <div class="fw-bold small text-truncate mt-1">{{ $product->name }}</div>
                            <span class="text-primary fw-bold small">${{ number_format($product->price, 2) }}</span>
                        </div>

                        <span class="fs-4 fw-bold text-muted">+</span>

                        <!-- Suggested Bundle Products -->
                        @foreach($frequentlyBought as $fb)
                            <div class="bg-white p-2 rounded-3 border text-center" style="width: 120px;">
                                <img src="{{ $fb->image ? asset($fb->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" width="60" height="60" class="object-fit-contain">
                                <div class="fw-bold small text-truncate mt-1">{{ $fb->name }}</div>
                                <span class="text-primary fw-bold small">${{ number_format($fb->price, 2) }}</span>
                            </div>
                            @if(!$loop->last)
                                <span class="fs-4 fw-bold text-muted">+</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="text-muted small mb-1">{{ __('Total Bundle Price:') }}</div>
                    <div class="display-6 fw-bold text-primary mb-2">${{ number_format($bundleTotal, 2) }}</div>
                    <button class="btn btn-primary rounded-pill px-4 fw-bold" onclick="addBundleToCart([{{ $product->id }}, {{ $frequentlyBought->pluck('id')->implode(',') }}])">
                        <i class="bx bx-cart-add me-1"></i> {{ __('Add Bundle to Cart') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Customer Reviews & Rating Section -->
    <div class="card p-4 border shadow-sm rounded-4 mb-5 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bxs-star text-warning me-2"></i> {{ __('Verified Customer Reviews') }} ({{ $totalReviews }})</h4>
                <p class="text-muted small mb-0">{{ __('Real verified shopper experiences and rating distribution') }}</p>
            </div>
            <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
                <i class="bx bx-edit me-1"></i> {{ __('Write a Review') }}
            </button>
        </div>

        <!-- Rating Distribution Summary & Bars -->
        <div class="row g-4 align-items-center mb-4 pb-4 border-bottom">
            <div class="col-md-4 text-center border-end">
                <div class="display-4 fw-bolder text-dark mb-1">{{ number_format($averageRating, 1) }}</div>
                <div class="text-warning mb-2 fs-5">
                    @for($s = 1; $s <= 5; $s++)
                        @if($s <= floor($averageRating))
                            <i class="bx bxs-star"></i>
                        @elseif($s - $averageRating < 1)
                            <i class="bx bxs-star-half"></i>
                        @else
                            <i class="bx bx-star text-muted"></i>
                        @endif
                    @endfor
                </div>
                <div class="text-muted small">{{ $totalReviews }} {{ __('verified customer ratings') }}</div>
            </div>

            <div class="col-md-8">
                <div class="d-flex flex-column gap-2">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php $percent = $ratingBreakdown[$star] ?? 0; @endphp
                        <div class="d-flex align-items-center gap-3">
                            <span class="small fw-semibold text-nowrap" style="width: 55px;">{{ $star }} {{ __('Star') }}</span>
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="small text-muted text-end" style="width: 40px;">{{ $percent }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row g-3">
            @forelse($product->reviews as $rev)
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bx {{ $i <= $rev->rating ? 'bxs-star' : 'bx-star text-muted' }}"></i>
                                @endfor
                            </div>
                            <span class="badge bg-label-success small"><i class="bx bx-check-shield me-1"></i> {{ __('Verified Purchase') }}</span>
                        </div>
                        <h6 class="fw-bold mb-1">{{ $rev->title ?: __('Customer Review') }}</h6>
                        <p class="small text-muted mb-2">{{ $rev->comment }}</p>
                        <small class="text-secondary fw-semibold">— {{ $rev->user?->name ?? 'Verified Shopper' }}</small>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">
                    <i class="bx bx-comment-detail fs-1 mb-2"></i>
                    <p class="mb-0">{{ __('No reviews written yet. Be the first to share your thoughts on this product!') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Customer Questions & Answers Section -->
    <div class="card p-4 border shadow-sm rounded-4 mb-5 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-help-circle text-primary me-2"></i> {{ __('Customer Questions & Answers') }} ({{ $questions->count() }})</h4>
                <p class="text-muted small mb-0">{{ __('Got a question about freshness, origin, or usage? Ask our staff or community.') }}</p>
            </div>
            <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                <i class="bx bx-message-rounded-dots me-1"></i> {{ __('Ask a Question') }}
            </button>
        </div>

        <div class="accordion accordion-flush" id="productQaAccordion">
            @forelse($questions as $index => $qa)
                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                    <h2 class="accordion-header" id="headingQa-{{ $qa->id }}">
                        <button class="accordion-button {{ $index > 1 ? 'collapsed' : '' }} fw-semibold bg-light text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQa-{{ $qa->id }}" aria-expanded="{{ $index <= 1 ? 'true' : 'false' }}">
                            <span class="badge bg-primary me-2 fw-bold">Q</span> {{ $qa->question }}
                        </button>
                    </h2>
                    <div id="collapseQa-{{ $qa->id }}" class="accordion-collapse collapse {{ $index <= 1 ? 'show' : '' }}" data-bs-parent="#productQaAccordion">
                        <div class="accordion-body bg-white pt-3">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-success fw-bold">A</span>
                                <div>
                                    <p class="mb-1 text-secondary">{{ $qa->answer ?: __('Our support team has received your question and will verify with the store manager shortly.') }}</p>
                                    <small class="text-muted">
                                        {{ __('Answered by') }} <strong>{{ $qa->answeredBy?->name ?? __('AK-Mart Supermarket Staff') }}</strong>
                                        • {{ $qa->answered_at ? $qa->answered_at->diffForHumans() : $qa->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bx bx-question-mark fs-1 mb-2"></i>
                    <p class="mb-0">{{ __('No questions asked yet for this product. Be the first to ask!') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">{{ __('Related Products You May Also Like') }}</h4>
                <a href="{{ route('storefront.shop') }}" class="text-primary text-decoration-none small">{{ __('View All') }} <i class="bx bx-chevron-right"></i></a>
            </div>
            <div class="row g-3">
                @foreach($relatedProducts as $rel)
                    <div class="col-6 col-md-3">
                        <div class="product-card p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="product-img-wrap rounded-3 mb-2">
                                    <img src="{{ $rel->image ? asset($rel->image) : asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="{{ $rel->name }}">
                                </div>
                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('storefront.product', $rel->id) }}" class="text-dark text-decoration-none text-truncate d-block">{{ $rel->name }}</a>
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-primary">${{ number_format($rel->price, 2) }}</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="quickAddToCart({{ $rel->id }})"><i class="bx bx-plus"></i></button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Modal: Write Review -->
<div class="modal fade" id="writeReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form onsubmit="submitReviewForm(event, {{ $product->id }})" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Write a Verified Product Review') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('Star Rating') }}</label>
                    <select id="reviewRating" class="form-select" required>
                        <option value="5">⭐⭐⭐⭐⭐ (5 / 5 — Excellent)</option>
                        <option value="4">⭐⭐⭐⭐ (4 / 5 — Very Good)</option>
                        <option value="3">⭐⭐⭐ (3 / 5 — Good)</option>
                        <option value="2">⭐⭐ (2 / 5 — Fair)</option>
                        <option value="1">⭐ (1 / 5 — Poor)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Review Title') }}</label>
                    <input type="text" id="reviewTitle" class="form-control" placeholder="e.g. Excellent fresh quality!" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Detailed Feedback') }}</label>
                    <textarea id="reviewComment" class="form-control" rows="4" placeholder="Share your experience with product freshness, packaging, and taste..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Submit Verified Review') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Back in Stock Alert Modal -->
<div class="modal fade" id="backInStockModal" tabindex="-1" aria-labelledby="backInStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" onsubmit="submitStockNotification(event, {{ $product->id }})">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="backInStockModalLabel"><i class="bx bx-bell text-warning me-2"></i> {{ __('Notify Me When Back in Stock') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">{{ __('Enter your email or phone number and we will send you an instant notification the moment :product is restocked.', ['product' => $product->name]) }}</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">{{ __('Email Address') }}</label>
                    <input type="email" id="stockNotifyEmail" class="form-control" placeholder="name@example.com" value="{{ Auth::user()?->email ?? '' }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">{{ __('Or Mobile Number (SMS/WhatsApp)') }}</label>
                    <input type="text" id="stockNotifyPhone" class="form-control" placeholder="+1 (555) 000-0000" value="{{ Auth::user()?->phone ?? '' }}">
                </div>
                <div id="stockNotifyFeedback" class="small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-warning text-dark fw-bold px-4">{{ __('Subscribe to Restock Alert') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Ask a Question Modal -->
<div class="modal fade" id="askQuestionModal" tabindex="-1" aria-labelledby="askQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" onsubmit="submitQuestionForm(event, {{ $product->id }})">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="askQuestionModalLabel"><i class="bx bx-message-rounded-dots text-primary me-2"></i> {{ __('Ask a Question about :product', ['product' => $product->name]) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">{{ __('Your Question') }} <span class="text-danger">*</span></label>
                    <textarea id="questionInput" class="form-control" rows="4" placeholder="{{ __('e.g. Does this package contain allergens or gluten? Where is it sourced from?') }}" required></textarea>
                </div>
                <div id="askQuestionFeedback" class="small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">{{ __('Post Question') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const unitPrice = {{ (float)$product->price }};
const comparePrice = {{ (float)($product->compare_at_price ?: 0) }};
const maxStock = {{ max(1, $availableStock) }};

function adjustQty(amount) {
    const input = document.getElementById('detailQty');
    let val = parseInt(input.value) || 1;
    val += amount;
    if (val < 1) val = 1;
    if (val > maxStock) val = maxStock;
    input.value = val;
    updateRate();
}

function updateRate() {
    const input = document.getElementById('detailQty');
    let qty = parseInt(input.value) || 1;
    if (qty < 1) qty = 1;
    if (qty > maxStock) qty = maxStock;
    input.value = qty;

    const total = (unitPrice * qty).toFixed(2);
    const priceDisplay = document.getElementById('dynamicTotalPrice');
    if (priceDisplay) {
        priceDisplay.textContent = '$' + total;
    }

    const btnCartText = document.getElementById('btnCartText');
    if (btnCartText) {
        btnCartText.textContent = `Add to Cart • $${total}`;
    }

    if (comparePrice > unitPrice) {
        const compareTotal = (comparePrice * qty).toFixed(2);
        const savingsTotal = ((comparePrice - unitPrice) * qty).toFixed(2);
        
        const compareDisplay = document.getElementById('dynamicComparePrice');
        if (compareDisplay) compareDisplay.textContent = '$' + compareTotal;

        const savingsBadge = document.getElementById('dynamicSavingsBadge');
        if (savingsBadge) savingsBadge.textContent = `Save $${savingsTotal}`;
    }
}

function addDetailToCart(productId) {
    const qty = parseInt(document.getElementById('detailQty')?.value) || 1;
    fetch('{{ route("storefront.cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, qty: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.totalItems;
            showToast(data.message, 'success');
        }
    });
}

function addBundleToCart(productIds) {
    let promises = productIds.map(id => {
        return fetch('{{ route("storefront.cart.add") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_id: id, qty: 1 })
        }).then(r => r.json());
    });

    Promise.all(promises).then(results => {
        const lastResult = results[results.length - 1];
        const badge = document.getElementById('cartBadge');
        if (badge && lastResult) badge.textContent = lastResult.totalItems;
        showToast('All bundle items added to your cart!', 'success');
    });
}

function toggleProductWishlist(productId, btn) {
    fetch('{{ route("storefront.wishlist.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'primary');
        }
    });
}

function submitReviewForm(e, productId) {
    e.preventDefault();
    const rating = document.getElementById('reviewRating').value;
    const title = document.getElementById('reviewTitle').value;
    const comment = document.getElementById('reviewComment').value;

    fetch(`/store/product/${productId}/review`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ rating: rating, title: title, comment: comment })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 600);
        }
    });
}

function submitStockNotification(e, productId) {
    e.preventDefault();
    const email = document.getElementById('stockNotifyEmail').value.trim();
    const phone = document.getElementById('stockNotifyPhone').value.trim();
    const feedback = document.getElementById('stockNotifyFeedback');

    if (!email && !phone) {
        feedback.innerHTML = '<span class="text-danger">{{ __("Please provide email or phone.") }}</span>';
        return;
    }

    fetch(`/store/product/${productId}/notify-stock`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ email: email, phone: phone })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const modalEl = document.getElementById('backInStockModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else {
            feedback.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }
    })
    .catch(err => {
        feedback.innerHTML = '<span class="text-danger">{{ __("An error occurred.") }}</span>';
    });
}

function submitQuestionForm(e, productId) {
    e.preventDefault();
    const question = document.getElementById('questionInput').value.trim();
    const feedback = document.getElementById('askQuestionFeedback');

    if (!question || question.length < 5) {
        feedback.innerHTML = '<span class="text-danger">{{ __("Please enter a question with at least 5 characters.") }}</span>';
        return;
    }

    fetch(`/store/product/${productId}/question`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ question: question })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const modalEl = document.getElementById('askQuestionModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            setTimeout(() => window.location.reload(), 600);
        } else {
            feedback.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }
    })
    .catch(err => {
        feedback.innerHTML = '<span class="text-danger">{{ __("An error occurred while posting your question.") }}</span>';
    });
}
</script>
@endsection
