@extends('layouts.storefrontMaster')

@section('title', __('Refer & Earn $10 Store Credit') . ' — AK-Mart')

@section('content')
<div class="container">
    <!-- Hero Banner -->
    <div class="card border-0 shadow-sm rounded-4 p-5 mb-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <span class="badge bg-white text-success fw-bold px-3 py-1.5 rounded-pill mb-3 fs-6">🎁 {{ __('AK-Mart Referral Rewards') }}</span>
                <h1 class="display-5 fw-bolder mb-3 text-white">{{ __('Give $10, Get $10 in Store Credit!') }}</h1>
                <p class="lead mb-0 text-white-50 fs-5">{{ __('Invite friends and family to shop fresh supermarket groceries on AK-Mart. When they place their first order, you both get $10 automatically added to your wallet!') }}</p>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="bg-white bg-opacity-20 p-4 rounded-4 backdrop-blur border border-white border-opacity-25 text-white">
                    <div class="small fw-semibold text-uppercase letter-spacing-1 mb-1">{{ __('Your Total Referral Earnings') }}</div>
                    <div class="display-5 fw-bold text-white mb-2">${{ number_format($earnedCredits, 2) }}</div>
                    <span class="badge bg-white text-dark rounded-pill">{{ $referredUsers->count() }} {{ __('Friends Invited') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Referral Link & Social Sharing -->
        <div class="col-lg-7">
            <div class="card p-4 border shadow-sm rounded-4 bg-white mb-4">
                <h5 class="fw-bold mb-3"><i class="bx bx-link-alt text-primary me-2"></i> {{ __('Your Personal Referral Link') }}</h5>
                <p class="text-muted small mb-3">{{ __('Share this link via social media or messaging apps. Anyone who shops using your link earns you instant wallet balance.') }}</p>

                <div class="input-group input-group-lg mb-4">
                    <input type="text" id="referralLinkInput" class="form-control font-monospace bg-light" value="{{ $referralLink }}" readonly>
                    <button class="btn btn-primary px-4 fw-bold" type="button" onclick="copyReferralLink()">
                        <i class="bx bx-copy me-1"></i> <span id="copyBtnText">{{ __('Copy Link') }}</span>
                    </button>
                </div>

                <!-- 1-Click Social Sharing -->
                <h6 class="fw-bold small text-muted text-uppercase mb-3">{{ __('1-Click Social Share') }}</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @php
                        $shareText = urlencode("🛒 Hey! Get fresh groceries and supermarket essentials delivered in 30 minutes on AK-Mart. Use my link to get a special discount on your first order: {$referralLink}");
                    @endphp
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" class="btn btn-success rounded-pill px-3 d-flex align-items-center gap-2">
                        <i class="bx bxl-whatsapp fs-5"></i> WhatsApp
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode($referralLink) }}&text={{ $shareText }}" target="_blank" class="btn btn-info text-white rounded-pill px-3 d-flex align-items-center gap-2">
                        <i class="bx bxl-telegram fs-5"></i> Telegram
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ $shareText }}" target="_blank" class="btn btn-dark rounded-pill px-3 d-flex align-items-center gap-2">
                        <i class="bx bxl-twitter fs-5"></i> Twitter / X
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralLink) }}" target="_blank" class="btn btn-primary rounded-pill px-3 d-flex align-items-center gap-2">
                        <i class="bx bxl-facebook fs-5"></i> Facebook
                    </a>
                </div>
            </div>

            <!-- How It Works Steps -->
            <div class="card p-4 border shadow-sm rounded-4 bg-white">
                <h5 class="fw-bold mb-4"><i class="bx bx-bulb text-warning me-2"></i> {{ __('How Does It Work?') }}</h5>
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="avatar avatar-md bg-label-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                <i class="bx bx-share-alt fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-1">1. {{ __('Share Link') }}</h6>
                            <small class="text-muted">{{ __('Send your unique link to friends and family.') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="avatar avatar-md bg-label-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                <i class="bx bx-cart-add fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-1">2. {{ __('They Order') }}</h6>
                            <small class="text-muted">{{ __('They order fresh groceries with door-to-door delivery.') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="avatar avatar-md bg-label-warning rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                <i class="bx bx-gift fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-1">3. {{ __('You Earn $10') }}</h6>
                            <small class="text-muted">{{ __('Get $10 instant store credit on every friend purchase.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referred Friends List -->
        <div class="col-lg-5">
            <div class="card p-4 border shadow-sm rounded-4 bg-white h-100">
                <h5 class="fw-bold mb-3"><i class="bx bx-group text-primary me-2"></i> {{ __('Invited Friends') }} ({{ $referredUsers->count() }})</h5>

                @if($referredUsers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-user-plus fs-1 mb-2 text-muted"></i>
                        <h6 class="fw-bold text-muted">{{ __('No friends invited yet') }}</h6>
                        <p class="small text-muted">{{ __('Copy your referral link and send it to your friends to start earning wallet rewards!') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Friend') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referredUsers as $refUser)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small">{{ $refUser->name }}</div>
                                            <div class="text-muted extra-small">{{ Str::mask($refUser->email, '*', 3, -4) }}</div>
                                        </td>
                                        <td class="small text-muted">{{ $refUser->created_at->format('M d') }}</td>
                                        <td>
                                            <span class="badge bg-label-success">{{ __('Joined / Active') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyReferralLink() {
    const input = document.getElementById('referralLinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);

    const btnText = document.getElementById('copyBtnText');
    btnText.textContent = 'Copied!';
    showToast('Referral link copied to clipboard!', 'success');
    setTimeout(() => btnText.textContent = 'Copy Link', 2000);
}
</script>
@endsection
