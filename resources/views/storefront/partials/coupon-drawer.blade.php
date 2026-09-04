<!-- Modern Interactive Coupon Drawer / Modal -->
<div class="modal fade" id="availableCouponsModal" tabindex="-1" aria-labelledby="availableCouponsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-primary text-white p-3.5" style="background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle bg-white bg-opacity-20 text-white">
                        <i class="bx bxs-coupon fs-4"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="availableCouponsModalLabel">{{ __('Available Coupons & Offers') }}</h6>
                        <small class="text-white text-opacity-75">{{ __('Apply the best promo code to maximize your savings') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3.5 bg-light bg-opacity-50">
                <!-- Smart Auto-Apply Best Coupon Recommendation Banner -->
                <div id="smartBestCouponBanner" class="card border-0 mb-3 rounded-4 p-3 shadow-xs d-none" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); border-left: 4px solid #4F46E5 !important;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge rounded-pill bg-primary text-white mb-1 px-2.5 py-1 fw-bold">
                                <i class="bx bx-sparkles me-1"></i> {{ __('AI Recommended Best Value') }}
                            </span>
                            <h6 class="fw-bold text-dark mb-0 mt-1" id="smartBestCouponTitle">Save with code</h6>
                            <p class="small text-muted mb-0" id="smartBestCouponDesc"></p>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold text-nowrap" id="smartBestCouponBtn" onclick="autoApplyBestCouponAjax()">
                            <i class="bx bx-bolt-circle me-1"></i> {{ __('Auto-Apply') }}
                        </button>
                    </div>
                </div>

                <!-- Live Promo Input in Drawer -->
                <div class="card p-3 rounded-4 border mb-3 bg-white shadow-xs">
                    <label class="form-label small fw-bold text-dark mb-1.5">{{ __('Have a specific promo voucher?') }}</label>
                    <div class="input-group">
                        <input type="text" id="drawerCouponInput" class="form-control text-uppercase font-monospace fw-bold" placeholder="{{ __('ENTER CODE (e.g. FESTIVE20)') }}">
                        <button class="btn btn-primary fw-bold px-3" type="button" onclick="applyCouponFromDrawer()">{{ __('Apply') }}</button>
                    </div>
                    <div id="drawerCouponFeedback" class="small mt-1.5"></div>
                </div>

                <!-- Active / Available Coupons List Container -->
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <span class="small fw-bold text-uppercase text-muted letter-spacing-1">{{ __('All Store Vouchers') }}</span>
                    <span class="badge bg-light text-muted border" id="drawerCouponCount">0 {{ __('offers') }}</span>
                </div>

                <div id="drawerCouponsLoading" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <p class="small text-muted mt-2 mb-0">{{ __('Calculating live discounts for your cart...') }}</p>
                </div>

                <div id="drawerCouponsList" class="d-flex flex-column gap-2.5">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white border-top py-2.5 px-3.5 d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="bx bx-shield-check text-success me-1"></i> {{ __('Verified authentic store discounts') }}</small>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Ticket Voucher Styles */
.ticket-voucher {
    background: #FFFFFF;
    border: 1.5px dashed #CBD5E1;
    border-radius: 16px;
    padding: 14px 16px;
    position: relative;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.ticket-voucher:hover {
    border-color: #4F46E5;
    box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.12);
    transform: translateY(-2px);
}
.ticket-voucher.is-applied {
    border-color: #10B981;
    border-style: solid;
    background: #F0FDF4;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);
}
.ticket-voucher.is-locked {
    opacity: 0.85;
    background: #F8FAFC;
}
.voucher-badge-value {
    background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
    color: #FFFFFF;
    font-size: 11.5px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ticket-code-tag {
    font-family: var(--bs-font-monospace);
    font-weight: 800;
    font-size: 13.5px;
    letter-spacing: 0.05em;
    color: #1E293B;
}
.coupon-unlock-progress {
    height: 5px;
    border-radius: 10px;
    background: #E2E8F0;
    overflow: hidden;
    margin-top: 6px;
}
.coupon-unlock-bar {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, #F59E0B, #10B981);
    transition: width 0.4s ease;
}
</style>

<script>
// Load and render available coupons in drawer
function openAvailableCouponsModal() {
    const modalEl = document.getElementById('availableCouponsModal');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    loadAvailableCouponsData();
}

function loadAvailableCouponsData() {
    const loading = document.getElementById('drawerCouponsLoading');
    const container = document.getElementById('drawerCouponsList');
    const countBadge = document.getElementById('drawerCouponCount');
    const bestBanner = document.getElementById('smartBestCouponBanner');

    loading.classList.remove('d-none');
    container.innerHTML = '';
    bestBanner.classList.add('d-none');

    fetch('{{ route("storefront.coupon.available") }}')
        .then(r => r.json())
        .then(data => {
            loading.classList.add('d-none');
            if (!data.success) return;

            const coupons = data.coupons || [];
            countBadge.textContent = coupons.length + ' {{ __("offers") }}';

            // Show Smart Best Coupon Banner if available & eligible
            if (data.best_coupon && data.best_coupon.is_eligible && (!data.applied_coupon || data.applied_coupon.code !== data.best_coupon.code)) {
                bestBanner.classList.remove('d-none');
                document.getElementById('smartBestCouponTitle').textContent = `Apply code ${data.best_coupon.code} for ${data.best_coupon.value_formatted}`;
                document.getElementById('smartBestCouponDesc').textContent = `Unlocks estimated savings of ${data.best_coupon.discount_formatted} on your current total (${data.subtotal_formatted})`;
            }

            if (coupons.length === 0) {
                container.innerHTML = `<div class="text-center py-4 text-muted small"><i class="bx bx-purchase-tag fs-3 mb-1 d-block text-secondary"></i>{{ __("No active coupon campaigns at this time.") }}</div>`;
                return;
            }

            let html = '';
            coupons.forEach(c => {
                const isApplied = data.applied_coupon && data.applied_coupon.code === c.code;
                const isEligible = c.is_eligible;

                html += `
                    <div class="ticket-voucher ${isApplied ? 'is-applied' : (!isEligible ? 'is-locked' : '')}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="voucher-badge-value">${c.value_formatted}</span>
                                <span class="ticket-code-tag ms-2">${c.code}</span>
                                ${isApplied ? '<span class="badge bg-success rounded-pill ms-1.5"><i class="bx bx-check me-0.5"></i>Applied</span>' : ''}
                            </div>
                            <div>
                                ${isApplied 
                                    ? `<button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5 small" onclick="removeCouponAjax()"><i class="bx bx-x me-1"></i>Remove</button>`
                                    : (isEligible 
                                        ? `<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 small fw-bold" onclick="applyDirectCoupon('${c.code}')"><i class="bx bx-check-circle me-1"></i>Apply</button>`
                                        : `<span class="badge bg-light text-muted border small">Locked</span>`
                                      )
                                }
                            </div>
                        </div>

                        <p class="small text-muted mb-1">${c.description}</p>

                        ${!isEligible && c.reason ? `
                            <div class="mt-2 pt-1 border-top">
                                <div class="d-flex justify-content-between align-items-center small">
                                    <span class="text-warning fw-semibold"><i class="bx bx-lock-alt me-1"></i>${c.reason}</span>
                                    <span class="text-muted font-monospace">${c.progress_pct}%</span>
                                </div>
                                <div class="coupon-unlock-progress">
                                    <div class="coupon-unlock-bar" style="width: ${c.progress_pct}%;"></div>
                                </div>
                            </div>
                        ` : `
                            <div class="d-flex justify-content-between align-items-center small text-muted mt-2 pt-1 border-top">
                                <span class="text-success fw-semibold"><i class="bx bx-trending-down me-1"></i>Saves ${c.discount_formatted}</span>
                                <span><i class="bx bx-time-five me-1"></i>Exp: ${c.expires_at}</span>
                            </div>
                        `}
                    </div>
                `;
            });

            container.innerHTML = html;
        })
        .catch(err => {
            loading.classList.add('d-none');
            container.innerHTML = `<div class="alert alert-danger small py-2 mb-0">{{ __("Could not load coupons right now.") }}</div>`;
        });
}

function applyDirectCoupon(code) {
    applyCouponGeneral(code);
}

function applyCouponFromDrawer() {
    const code = document.getElementById('drawerCouponInput').value.trim();
    if (!code) {
        document.getElementById('drawerCouponFeedback').innerHTML = '<span class="text-danger small">{{ __("Please enter a voucher code.") }}</span>';
        return;
    }
    applyCouponGeneral(code, 'drawerCouponFeedback');
}

function autoApplyBestCouponAjax() {
    const btn = document.getElementById('smartBestCouponBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Applying...';

    fetch('{{ route("storefront.coupon.auto_apply_best") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-bolt-circle me-1"></i> {{ __("Auto-Apply") }}';

        if (data.success) {
            if (window.showToast) showToast(data.message, 'success');
            // Close modal & reload or re-sync UI
            const modalEl = document.getElementById('availableCouponsModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            setTimeout(() => window.location.reload(), 300);
        } else {
            alert(data.message || 'Could not auto-apply coupon.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-bolt-circle me-1"></i> {{ __("Auto-Apply") }}';
        alert('An error occurred. Please try again.');
    });
}

function removeCouponAjax() {
    fetch('{{ route("storefront.coupon.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.showToast) showToast(data.message, 'info');
            setTimeout(() => window.location.reload(), 300);
        }
    })
    .catch(() => window.location.reload());
}

function applyCouponGeneral(code, feedbackId = null) {
    fetch('{{ route("storefront.coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: code })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.showToast) showToast(data.message, 'success');
            const modalEl = document.getElementById('availableCouponsModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            setTimeout(() => window.location.reload(), 300);
        } else {
            if (feedbackId && document.getElementById(feedbackId)) {
                document.getElementById(feedbackId).innerHTML = `<span class="text-danger small">${data.message}</span>`;
            } else {
                alert(data.message || 'Invalid coupon.');
            }
        }
    })
    .catch(() => alert('Failed to apply coupon.'));
}
</script>
